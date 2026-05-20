<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\AnswerDetail;
use Illuminate\Http\Request;

class MobileAnswerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'answers'                  => 'required|array|min:1',
            'answers.*.question_id'    => 'required|exists:questions,id',
            'answers.*.value'          => 'nullable',
            'answers.*.item'           => 'nullable', // Optional: untuk matrix items individual
        ]);

        $user   = auth()->user();
        $alumni = $user->alumni;

        if (!$alumni) {
            return response()->json([
                'status'  => false,
                'message' => 'Data alumni tidak ditemukan',
            ], 404);
        }

        // Ambil periode survei yang sedang aktif
        $activePeriod = \App\Models\SurveyPeriod::getAktif();

        // Group & merge answers by question_id, with special handling for matrix
        $answersByQuestion = [];
        
        foreach ($request->answers as $item) {
            $qId = $item['question_id'];
            $value = $item['value'] ?? null;
            
            // Skip jika value kosong
            if ($value === null || $value === '' || $value === '[]') {
                continue;
            }

            // Get question untuk check type
            $question = \App\Models\Question::find($qId);
            if (!$question) {
                continue;
            }

            // Initialize if not exists
            if (!isset($answersByQuestion[$qId])) {
                $answersByQuestion[$qId] = [
                    'question_id' => $qId,
                    'value' => $question->type === 'matrix' ? [] : $value,
                    'is_matrix' => ($question->type === 'matrix'),
                ];
            }

            // Handle matrix questions - accumulate values
            if ($question->type === 'matrix') {
                // Ambil details sekali, cache di question object
                $details = $question->details()->orderBy('urutan')->get();

                if (isset($item['item']) && $item['item'] !== null && $item['item'] !== '') {
                    $itemKey = $item['item'];

                    // Jika item adalah index numerik (0, 1, 2, ...), map ke item_label
                    if (is_numeric($itemKey) && $details->count() > 0) {
                        $idx = (int) $itemKey;
                        $detailItem = $details->get($idx);
                        if ($detailItem) {
                            $itemKey = $detailItem->item_label;
                        }
                    }

                    $answersByQuestion[$qId]['value'][$itemKey] = $value;
                } else {
                    // Tidak ada 'item' field
                    // Cek apakah value adalah JSON object lengkap {"item_label": val, ...}
                    $decodedValue = json_decode($value, true);
                    if (is_array($decodedValue) && !array_is_list($decodedValue)) {
                        // Value sudah berupa JSON object matrix lengkap — merge langsung
                        foreach ($decodedValue as $k => $v) {
                            $answersByQuestion[$qId]['value'][$k] = $v;
                        }
                    } else {
                        // Mapping berdasarkan urutan pengiriman
                        if ($details->count() > 0) {
                            $currentItemIndex = count($answersByQuestion[$qId]['value']);
                            if ($currentItemIndex < $details->count()) {
                                $itemLabel = $details[$currentItemIndex]->item_label;
                                $answersByQuestion[$qId]['value'][$itemLabel] = $value;
                            }
                        }
                    }
                }
            } else {
                // Non-matrix: overwrite dengan value terbaru
                $answersByQuestion[$qId]['value'] = $value;
            }
        }

        // Process grouped answers
        foreach ($answersByQuestion as $processItem) {
            $value = $processItem['value'];
            $qId = $processItem['question_id'];
            $isMatrix = $processItem['is_matrix'] ?? false;

            // Untuk matrix dengan array accumulation, convert ke JSON
            // Urutkan sesuai urutan question_details agar konsisten
            if ($isMatrix && is_array($value)) {
                $question = \App\Models\Question::find($qId);
                if ($question) {
                    $orderedDetails = $question->details()->orderBy('urutan')->pluck('item_label');
                    $sortedValue = [];
                    foreach ($orderedDetails as $label) {
                        if (array_key_exists($label, $value)) {
                            $sortedValue[$label] = $value[$label];
                        }
                    }
                    // Tambahkan key yang tidak ada di details (fallback)
                    foreach ($value as $k => $v) {
                        if (!array_key_exists($k, $sortedValue)) {
                            $sortedValue[$k] = $v;
                        }
                    }
                    $value = $sortedValue;
                }
                $value = json_encode($value);
            }

            // Skip jika value kosong setelah accumulation
            if ($value === null || $value === '' || $value === '[]' || $value === '{}') {
                continue;
            }

            // Get question
            $question = \App\Models\Question::find($qId);
            if (!$question) {
                continue;
            }

            // Simpan / update header jawaban
            $answer = Answer::updateOrCreate(
                [
                    'alumni_id'        => $alumni->id,
                    'question_id'      => $qId,
                    'survey_period_id' => $activePeriod?->id,
                ],
                []
            );

            // Hapus detail lama
            AnswerDetail::where('answer_id', $answer->id)->delete();

            // Handle value encoding — pastikan multiple tersimpan sebagai JSON array
            $finalValue = $value;

            if (is_array($value)) {
                $finalValue = json_encode($value);
            } elseif (is_string($value)) {
                $trimmed = trim($value);

                // Cek apakah sudah JSON valid
                if (
                    (substr($trimmed, 0, 1) === '[' || substr($trimmed, 0, 1) === '{') &&
                    (substr($trimmed, -1) === ']' || substr($trimmed, -1) === '}')
                ) {
                    // Sudah JSON — decode dulu untuk cek double-encoding
                    $decoded = json_decode($trimmed, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (is_string($decoded)) {
                            // Double-encoded: decode sekali lagi
                            $decoded2 = json_decode($decoded, true);
                            $finalValue = json_last_error() === JSON_ERROR_NONE
                                ? json_encode($decoded2)
                                : json_encode($decoded);
                        } else {
                            // Normal JSON, simpan as-is
                            $finalValue = $trimmed;
                        }
                    } else {
                        $finalValue = $trimmed;
                    }
                } else {
                    // Plain string
                    $finalValue = (string) $value;
                }
            }

            // Simpan value
            AnswerDetail::create([
                'answer_id' => $answer->id,
                'option_id' => null,
                'value'     => $finalValue,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Jawaban berhasil disimpan',
        ]);
    }
}
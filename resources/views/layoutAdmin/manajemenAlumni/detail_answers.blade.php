@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Jawaban Alumni</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/admin/alumni') }}">Alumni</a></li>
        <li class="breadcrumb-item active">Detail Jawaban</li>
    </ol>

    <!-- Info Alumni Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-user me-1"></i> Informasi Alumni</span>
            <a href="{{ url('/admin/alumni-sudah-mengisi') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama:</strong> {{ $alumni->nama }}</p>
                    <p><strong>NIM:</strong> {{ $alumni->nim }}</p>
                    <p><strong>Program Studi:</strong> {{ $alumni->prodi }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Angkatan:</strong> {{ $alumni->angkatan }}</p>
                    <p><strong>Tahun Lulus:</strong> {{ $alumni->tahun_lulus }}</p>
                    <p><strong>No HP:</strong> {{ $alumni->no_hp ?? '-' }}</p>
                </div>
            </div>
            @if($alumni->email)
                <p><strong>Email:</strong> {{ $alumni->email }}</p>
            @endif
            @if($alumni->alamat)
                <p><strong>Alamat:</strong> {{ $alumni->alamat }}</p>
            @endif
        </div>
    </div>

    <!-- Answers Section -->
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-check-circle me-1"></i> Jawaban Questionnaire</span>
            <span class="badge bg-info ms-2">
                {{ count($answers) }} / {{ count($questions) }} Pertanyaan Terjawab
            </span>
        </div>
        <div class="card-body">
            @if(count($questions) == 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> Tidak ada pertanyaan yang tersedia.
                </div>
            @else
                <div class="accordion" id="accordionAnswers">
                    @foreach($questions as $index => $question)
                        @php
                            $answer = $answers->get($question->id);
                            $hasAnswered = $answer ? true : false;
                            $statusBadge = $hasAnswered ? 
                                '<span class="badge bg-success ms-2">Terjawab</span>' : 
                                '<span class="badge bg-danger ms-2">Belum Terjawab</span>';
                        @endphp
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $question->id }}">
                                <button 
                                    class="accordion-button {{ !$hasAnswered ? 'collapsed' : '' }}" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $question->id }}" 
                                    aria-expanded="{{ $hasAnswered ? 'true' : 'false' }}" 
                                    aria-controls="collapse{{ $question->id }}">
                                    
                                    <strong>{{ $index + 1 }}. </strong>
                                    <span class="ms-2">{{ $question->pertanyaan }}</span>
                                    {!! $statusBadge !!}
                                </button>
                            </h2>
                            <div 
                                id="collapse{{ $question->id }}" 
                                class="accordion-collapse collapse {{ $hasAnswered ? 'show' : '' }}" 
                                aria-labelledby="heading{{ $question->id }}" 
                                data-bs-parent="#accordionAnswers">
                                
                                <div class="accordion-body">
                                    @if($hasAnswered)
                                        @php
                                            // Get answer details with proper null handling
                                            $answerDetails = ($answer && $answer->answerDetails) ? $answer->answerDetails : collect([]);
                                        @endphp
                                        
                                        @if($question->type == 'text')
                                            {{-- Text Answer --}}
                                            <p class="mb-0">
                                                <strong>Jawaban:</strong><br>
                                                <span class="text-muted">{{ $answerDetails->first()->value ?? '-' }}</span>
                                            </p>
                                        
                                        @elseif($question->type == 'single')
                                            {{-- Single Option Answer --}}
                                            @php
                                                $option = $answerDetails->first()?->option;
                                                $scaleValue = $answerDetails->first()->value ?? null;
                                                $scaleLabel = null;
                                                
                                                // Cek apakah ini scale type dengan option
                                                if ($question->type == 'single' && $question->options->count() > 0) {
                                                    // Ambil label dari option berdasarkan value
                                                    $matchedOption = $question->options->firstWhere('value', $scaleValue);
                                                    if ($matchedOption) {
                                                        $scaleLabel = $matchedOption->label;
                                                    }
                                                }
                                            @endphp
                                            <p class="mb-0">
                                                <strong>Jawaban:</strong><br>
                                                @if($option)
                                                    <span class="badge bg-primary">{{ $option->label ?? '-' }}</span>
                                                @elseif($scaleLabel)
                                                    <span class="badge bg-primary">{{ $scaleLabel }}</span>
                                                @else
                                                    <span class="badge bg-primary">{{ $scaleValue ?? '-' }}</span>
                                                @endif
                                            </p>
                                        
                                        @elseif($question->type == 'multiple')
                                            {{-- Multiple Options Answer --}}
                                            <p class="mb-2">
                                                <strong>Jawaban (Pilih Lebih Dari Satu):</strong>
                                            </p>
                                            <div>
                                                @php
                                                    $multipleAnswers = [];
                                                    if ($answerDetails && count($answerDetails) > 0) {
                                                        $firstValue = $answerDetails->first()->value ?? null;

                                                        if ($firstValue && !empty($firstValue)) {
                                                            // Decode pertama
                                                            $decoded = json_decode($firstValue, true);

                                                            if (is_array($decoded)) {
                                                                // Format normal: ["opsi1","opsi2"]
                                                                $multipleAnswers = $decoded;
                                                            } elseif (is_string($decoded)) {
                                                                // Double-encoded: value tersimpan sebagai "\"[\\\"opsi1\\\"]\"" 
                                                                $decoded2 = json_decode($decoded, true);
                                                                if (is_array($decoded2)) {
                                                                    $multipleAnswers = $decoded2;
                                                                } else {
                                                                    // Single string value
                                                                    $multipleAnswers = [$decoded];
                                                                }
                                                            } else {
                                                                // Bukan JSON — plain string (misal: "anjay")
                                                                $multipleAnswers = [$firstValue];
                                                            }
                                                        }

                                                        // Fallback: tersimpan sebagai multiple detail records dengan option_id
                                                        if (empty($multipleAnswers)) {
                                                            foreach ($answerDetails as $detail) {
                                                                if ($detail->option) {
                                                                    $multipleAnswers[] = $detail->option->label;
                                                                } elseif (!empty($detail->value)) {
                                                                    $multipleAnswers[] = $detail->value;
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if(count($multipleAnswers) > 0)
                                                    @foreach($multipleAnswers as $ans)
                                                        <span class="badge bg-primary mb-2">{{ $ans }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        
                                        @elseif($question->type == 'scale')
                                            {{-- Scale Answer (1-5) dengan options --}}
                                            @php
                                                $scaleValue = $answerDetails->first()->value ?? '-';
                                                $scaleLabels = [
                                                    '1' => 'Sangat Rendah / Tidak Puas',
                                                    '2' => 'Rendah / Kurang Puas',
                                                    '3' => 'Sedang / Cukup',
                                                    '4' => 'Tinggi / Puas',
                                                    '5' => 'Sangat Tinggi / Sangat Puas',
                                                ];
                                            @endphp
                                            <p class="mb-0">
                                                <strong>Jawaban:</strong><br>
                                                <span class="badge bg-warning text-dark">
                                                    {{ $scaleValue }} - {{ $scaleLabels[$scaleValue] ?? 'N/A' }}
                                                </span>
                                            </p>
                                        
                                        @elseif($question->type == 'matrix')
                                            {{-- Matrix Answer (Tabel dengan Sub-Items) --}}
                                            @php
                                                // Decode JSON jawaban matrix: {"item_label": value, ...}
                                                $matrixAnswerRaw = [];
                                                $firstDetail = $answerDetails->first();
                                                if ($firstDetail && !empty($firstDetail->value)) {
                                                    $rawVal = $firstDetail->value;
                                                    $decoded = json_decode($rawVal, true);

                                                    if (is_array($decoded)) {
                                                        // Cek apakah ini nested: value dari key pertama adalah JSON object lagi
                                                        // Contoh: {"anjay":"{\"gacor\":\"4\",...}","gacor":null}
                                                        $firstVal = reset($decoded);
                                                        if (is_string($firstVal) && str_starts_with(trim($firstVal), '{')) {
                                                            $innerDecoded = json_decode($firstVal, true);
                                                            if (is_array($innerDecoded)) {
                                                                // Data nested — gunakan inner JSON sebagai jawaban sebenarnya
                                                                $matrixAnswerRaw = $innerDecoded;
                                                            } else {
                                                                $matrixAnswerRaw = $decoded;
                                                            }
                                                        } else {
                                                            $matrixAnswerRaw = $decoded;
                                                        }
                                                    }
                                                }

                                                // Ambil urutan item dari question_details (sumber kebenaran urutan)
                                                $questionDetails = $question->details
                                                    ? $question->details->sortBy('urutan')
                                                    : collect([]);
                                            @endphp
                                            <p class="mb-2">
                                                <strong>Jawaban (Matrix):</strong>
                                            </p>
                                            @if($questionDetails->isNotEmpty())
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead style="background-color:#1a73e8; color:#fff;">
                                                            <tr>
                                                                <th>Item</th>
                                                                <th>Jawaban</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($questionDetails as $detailItem)
                                                                @php
                                                                    $label = $detailItem->item_label;
                                                                    $val   = $matrixAnswerRaw[$label] ?? null;
                                                                @endphp
                                                                <tr>
                                                                    <td><strong>{{ $label }}</strong></td>
                                                                    <td>
                                                                        @if($val !== null && $val !== '')
                                                                            @if(is_array($val))
                                                                                @foreach($val as $v)
                                                                                    <span class="badge bg-info mb-1">{{ $v }}</span>
                                                                                @endforeach
                                                                            @else
                                                                                <span class="badge bg-info">{{ $val }}</span>
                                                                            @endif
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <span class="text-muted">Belum ada jawaban</span>
                                            @endif
                                        
                                        @else
                                            <p class="text-muted mb-0">Format jawaban tidak dikenali (Type: {{ $question->type }})</p>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">Alumni belum menjawab pertanyaan ini</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Summary Stats -->
                @php
                    $answeredCount = count($answers);
                    $totalCount = count($questions);
                    $percentage = $totalCount > 0 ? ($answeredCount / $totalCount) * 100 : 0;
                @endphp
                <div class="mt-4 pt-3 border-top">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="alert alert-info mb-0">
                                <strong>Total Pertanyaan:</strong> {{ $totalCount }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-success mb-0">
                                <strong>Sudah Terjawab:</strong> {{ $answeredCount }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning mb-0">
                                <strong>Persentase:</strong> {{ round($percentage, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #000;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .text-muted {
        font-size: 1rem;
    }
</style>
@endpush

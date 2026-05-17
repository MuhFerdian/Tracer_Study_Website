<?php

namespace App\Http\Controllers;

use App\Models\SurveyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SurveyPeriodController extends Controller
{
    // =============================================
    // GET semua periode (untuk dropdown & list)
    // =============================================
    public function index()
    {
        $periods = SurveyPeriod::orderByDesc('tahun')->orderByDesc('id')->get();
        return response()->json(['status' => true, 'data' => $periods]);
    }

    // =============================================
    // GET periode aktif saat ini
    // =============================================
    public function getAktif()
    {
        $period = SurveyPeriod::getAktif();
        return response()->json([
            'status' => true,
            'data'   => $period,
        ]);
    }

    // =============================================
    // STORE — buat periode baru
    // =============================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:100',
            'tahun'         => 'required|integer|min:2000|max:2100',
            'tanggal_buka'  => 'required|date',
            'tanggal_tutup' => 'required|date|after:tanggal_buka',
            'deskripsi'     => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal',
                'msgField' => $validator->errors(),
            ], 422);
        }

        $period = SurveyPeriod::create([
            'nama'          => $request->nama,
            'tahun'         => $request->tahun,
            'tanggal_buka'  => $request->tanggal_buka,
            'tanggal_tutup' => $request->tanggal_tutup,
            'status'        => 'draft',
            'deskripsi'     => $request->deskripsi,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Periode berhasil dibuat',
            'data'    => $period,
        ]);
    }

    // =============================================
    // UPDATE — edit periode
    // =============================================
    public function update(Request $request, $id)
    {
        $period = SurveyPeriod::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:100',
            'tahun'         => 'required|integer|min:2000|max:2100',
            'tanggal_buka'  => 'required|date',
            'tanggal_tutup' => 'required|date|after:tanggal_buka',
            'deskripsi'     => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal',
                'msgField' => $validator->errors(),
            ], 422);
        }

        $period->update($request->only([
            'nama', 'tahun', 'tanggal_buka', 'tanggal_tutup', 'deskripsi'
        ]));

        return response()->json([
            'status'  => true,
            'message' => 'Periode berhasil diperbarui',
            'data'    => $period,
        ]);
    }

    // =============================================
    // AKTIFKAN — hanya 1 periode aktif sekaligus
    // =============================================
    public function aktifkan($id)
    {
        DB::transaction(function () use ($id) {
            // Nonaktifkan semua periode dulu
            SurveyPeriod::where('status', 'aktif')->update(['status' => 'tutup']);
            // Aktifkan yang dipilih
            SurveyPeriod::where('id', $id)->update(['status' => 'aktif']);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Periode berhasil diaktifkan',
        ]);
    }

    // =============================================
    // TUTUP — nonaktifkan periode
    // =============================================
    public function tutup($id)
    {
        SurveyPeriod::where('id', $id)->update(['status' => 'tutup']);

        return response()->json([
            'status'  => true,
            'message' => 'Periode berhasil ditutup',
        ]);
    }

    // =============================================
    // DELETE — hapus periode (hanya jika draft/tutup)
    // =============================================
    public function destroy($id)
    {
        $period = SurveyPeriod::findOrFail($id);

        if ($period->status === 'aktif') {
            return response()->json([
                'status'  => false,
                'message' => 'Periode aktif tidak bisa dihapus. Tutup dulu sebelum menghapus.',
            ], 422);
        }

        $period->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Periode berhasil dihapus',
        ]);
    }
}

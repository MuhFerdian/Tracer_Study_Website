<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganPekerjaan;
use Yajra\DataTables\Facades\DataTables;

class LokerController extends Controller
{
    public function index()
    {
        return view('layoutAdmin.lowongankerja.index');
    }

    // =======================
    // STORE (TAMBAH)
    // =======================
    public function store(Request $request)
    {
        $request->validate([
    'posisi' => 'required',
    'nama_perusahaan' => 'required',
    'kontak' => ['required', 'regex:/^(08)[0-9]{8,12}$/'],
    'batas_lamaran' => ['required', 'date', 'after_or_equal:today']
], [
    'batas_lamaran.after_or_equal' => 'Tanggal batas lamaran tidak boleh di masa lalu'
]);

        LowonganPekerjaan::create([
            'posisi' => $request->posisi,
            'nama_perusahaan' => $request->nama_perusahaan,
            'lokasi' => $request->lokasi,
            'gaji' => $request->gaji,
            'deskripsi' => $request->deskripsi,
            'batas_lamaran' => $request->batas_lamaran,
            'kontak' => $request->kontak,
            'link_lamaran' => $request->link_lamaran,
            'dibuat_oleh' => auth()->id(),
            'role' => auth()->user()->role->role_nama,
            'aktif' => true,
        ]);

        return response()->json([
            'message' => 'Lowongan berhasil ditambahkan'
        ]);
    }

    // =======================
    // LIST DATATABLE
    // =======================
    public function list()
    {
        $data = LowonganPekerjaan::latest()->get();

        return DataTables::of($data)
            ->addIndexColumn()

            ->addColumn('link_lamaran', function ($row) {
                return '<a href="'.$row->link_lamaran.'" target="_blank">Lamar</a>';
            })

            ->addColumn('aktif', function ($row) {
                return $row->aktif ? 'Aktif' : 'Nonaktif';
            })

            ->addColumn('aksi', function ($row) {
                return '
                    <button onclick="edit('.$row->id.')" class="btn btn-warning btn-sm">Edit</button>
                    <button onclick="hapus('.$row->id.')" class="btn btn-danger btn-sm">Hapus</button>
                ';
            })

            ->rawColumns(['aksi', 'link_lamaran'])
            ->make(true);
    }

    // =======================
    // CREATE MODAL
    // =======================
    public function create_ajax()
    {
        return view('layoutAdmin.lowongankerja.create');
    }

    // =======================
    // EDIT MODAL
    // =======================
    public function edit($id)
    {
        $loker = LowonganPekerjaan::findOrFail($id);
        return view('layoutAdmin.lowongankerja.edit', compact('loker'));
    }

    // =======================
    // UPDATE
    // =======================
    public function update(Request $request, $id)
    {
        $request->validate([
    'posisi' => 'required',
    'nama_perusahaan' => 'required',
    'kontak' => ['required', 'regex:/^(08)[0-9]{8,12}$/'],
    'batas_lamaran' => ['required', 'date', 'after_or_equal:today']
], [
    'batas_lamaran.after_or_equal' => 'Tanggal batas lamaran tidak boleh di masa lalu'
]);

        $loker = LowonganPekerjaan::findOrFail($id);
        $loker->update($request->all());

        return response()->json([
            'message' => 'Berhasil update'
        ]);
    }

    // =======================
    // CONFIRM DELETE
    // =======================
    public function confirm($id)
    {
        $loker = LowonganPekerjaan::findOrFail($id);
        return view('layoutAdmin.lowongankerja.confirm', compact('loker'));
    }

    // =======================
    // DELETE
    // =======================
    public function destroy($id)
    {
        LowonganPekerjaan::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Berhasil hapus'
        ]);
    }
}
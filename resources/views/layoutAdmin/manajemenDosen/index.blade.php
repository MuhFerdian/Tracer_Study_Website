@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Manajemen Dosen</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Manajemen / Dosen</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fs-5"><i class="fas fa-chalkboard-user me-2"></i> Daftar Dosen</span>
            <a href="{{ url('/admin/manajemen-dosen/create') }}" class="btn btn-primary">Tambah Dosen</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dosens as $i => $d)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $d->username }}</td>
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->email }}</td>
                            <td>{{ $d->status }}</td>
                            <td>
                                <a href="{{ url('/admin/manajemen-dosen/'.$d->id.'/edit') }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ url('/admin/manajemen-dosen/'.$d->id.'/delete') }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus dosen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Dosen</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ url('/admin/manajemen-dosen/store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="pending">pending</option>
                        <option value="active">active</option>
                    </select>
                </div>
                <button class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

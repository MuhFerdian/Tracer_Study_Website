@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Dosen</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ url('/admin/manajemen-dosen/'.$user->id.'/update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password (kosongkan untuk tidak mengubah)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>pending</option>
                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>active</option>
                    </select>
                </div>
                <button class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

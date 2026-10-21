@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="container-fluid" style="max-width: 900px">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Edit Kategori</h2>
            <p class="text-muted mb-0">Perbarui informasi kelompok kategori <strong>{{ $category->nama }}</strong></p>
        </div>

        <div>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-primary"></i> Formulir Perubahan Kategori
            </strong>
        </div>

        <div class="card-body">
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">
                        Nama Kategori <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        value="{{ old('nama', $category->nama) }}"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label">
                        Deskripsi
                    </label>
                    <textarea
                        name="deskripsi"
                        class="form-control"
                        rows="4"
                        placeholder="Deskripsi kategori..."
                    >{{ old('deskripsi', $category->deskripsi) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
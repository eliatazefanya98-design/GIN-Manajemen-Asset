@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Kategori Asset</h3>
            <p class="text-muted mb-0">
                Kelola kategori asset
            </p>
        </div>

        <a href="{{ route('categories.create') }}"
           class="btn btn-primary">
            + Tambah Kategori
        </a>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Validation Error --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Search --}}
    <form method="GET"
          action="{{ route('categories.index') }}"
          class="mb-3">

        <div class="input-group">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Cari kategori..."
                value="{{ $search }}"
            >

            <button class="btn btn-outline-primary" type="submit">
                Cari
            </button>

            @if($search)
                <a href="{{ route('categories.index') }}"
                   class="btn btn-outline-secondary">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">
                        <tr>
                            <th width="70">No</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th width="130">Jumlah Asset</th>
                            <th width="220">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($categories as $category)

                            <tr>

                                <td>
                                    {{ $categories->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $category->nama }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $category->deskripsi ?: '-' }}
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $category->assets_count }}
                                    </span>
                                </td>

                                <td>

                                    <a href="{{ route('categories.show', $category) }}"
                                       class="btn btn-sm btn-info text-white">
                                        Detail
                                    </a>

                                    <a href="{{ route('categories.edit', $category) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('categories.destroy', $category) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                        >
                                            Hapus
                                        </button>
                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    class="text-center py-4 text-muted">
                                    Belum ada kategori.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $categories->links() }}
    </div>

</div>
@endsection
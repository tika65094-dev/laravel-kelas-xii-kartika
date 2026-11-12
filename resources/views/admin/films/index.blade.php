@extends('templates.index')

@section('content')
<div class="general_agileits_genres">
    <div class="container" style="padding-top: 2em; padding-bottom: 3em;">
        @include('admin.nav')

        <div class="row">
            <div class="col-md-6 col-sm-6">
                <h2 style="color: #FF8D1B; font-weight: bold; margin-bottom: 5px;">
                    <i class="fa fa-film"></i> Kelola Data Film
                </h2>
                <p style="color: #666; margin-bottom: 20px;">
                    Total {{ $films->total() }} film terdaftar di sistem.
                </p>
            </div>
            <div class="col-md-6 col-sm-6 text-right" style="margin-top: 15px;">
                <a href="{{ route('admin.films.create') }}" class="btn" style="background: #FF8D1B; color: #fff; font-weight: bold; padding: 8px 20px;">
                    <i class="fa fa-plus"></i> Tambah Film Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert" style="border-radius: 4px;">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <i class="fa fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert" style="border-radius: 4px;">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Filter & Search Bar -->
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-6">
                <form method="GET" action="{{ route('admin.films.index') }}" class="form-inline">
                    <div class="input-group" style="width: 100%;">
                        <input type="text" name="search" class="form-control" placeholder="Cari judul film..." value="{{ request('search') }}">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit" style="background: #212121; color: #fff;">
                                <i class="fa fa-search"></i> Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.films.index') }}" class="btn btn-default" title="Reset">
                                    <i class="fa fa-times"></i>
                                </a>
                            @endif
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Films Table -->
        <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
            <div class="panel-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="width: 60px;">ID</th>
                                <th style="width: 80px;">Poster</th>
                                <th>Judul</th>
                                <th>Genre</th>
                                <th>Tahun</th>
                                <th style="width: 120px;">Jumlah Cast</th>
                                <th style="width: 160px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($films as $film)
                                <tr>
                                    <td>{{ $film->id }}</td>
                                    <td>
                                        @if($film->poster)
                                            <img src="/{{ $film->poster }}" alt="{{ $film->title }}" style="width: 45px; height: 60px; object-fit: cover; border-radius: 3px;" onerror="this.src='{{ asset('template/images/m1.jpg') }}';">
                                        @else
                                            <span class="text-muted"><i class="fa fa-image fa-2x"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('movies.show', $film->id) }}" target="_blank" style="font-weight: bold; color: #212121;">
                                            {{ $film->title }} <i class="fa fa-external-link" style="font-size: 0.8em; color: #999;"></i>
                                        </a>
                                    </td>
                                    <td>{{ $film->genre->name ?? '-' }}</td>
                                    <td>{{ $film->year }}</td>
                                    <td>
                                        <a href="{{ route('admin.film-cast', ['film_id' => $film->id]) }}" class="badge" style="background: #FF8D1B;" title="Kelola Cast">
                                            {{ $film->perans_count ?? $film->perans->count() }} Cast
                                        </a>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('admin.films.edit', $film->id) }}" class="btn btn-xs btn-warning" style="margin-right: 5px;" title="Ubah Film">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.films.destroy', $film->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus film &quot;{{ addslashes($film->title) }}&quot;? Semua peran dan komentar terkait juga akan dihapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="Hapus Film">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                                        @if(request('search'))
                                            Tidak ada film yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>".
                                        @else
                                            Belum ada film yang terdaftar.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-md-12 text-center">
                {{ $films->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


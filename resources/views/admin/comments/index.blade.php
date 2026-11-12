@extends('templates.index')

@section('content')
<div class="general_agileits_genres">
    <div class="container" style="padding-top: 2em; padding-bottom: 3em;">
        @include('admin.nav')

        <div class="row">
            <div class="col-md-6 col-sm-6">
                <h2 style="color: #FF8D1B; font-weight: bold; margin-bottom: 5px;">
                    <i class="fa fa-comments"></i> Kelola Data Komentar
                </h2>
                <p style="color: #666; margin-bottom: 20px;">
                    Total {{ $comments->total() }} komentar terdaftar di sistem.
                </p>
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
                <form method="GET" action="{{ route('admin.comments.index') }}" class="form-inline">
                    <div class="input-group" style="width: 100%;">
                        <input type="text" name="search" class="form-control" placeholder="Cari isi komentar atau nama pengguna..." value="{{ request('search') }}">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit" style="background: #212121; color: #fff;">
                                <i class="fa fa-search"></i> Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.comments.index') }}" class="btn btn-default" title="Reset">
                                    <i class="fa fa-times"></i>
                                </a>
                            @endif
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Comments Table -->
        <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
            <div class="panel-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="width: 60px;">ID</th>
                                <th style="width: 180px;">Pengguna</th>
                                <th style="width: 200px;">Film</th>
                                <th>Komentar</th>
                                <th style="width: 150px;">Waktu</th>
                                <th style="width: 100px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comments as $comment)
                                <tr>
                                    <td>{{ $comment->id }}</td>
                                    <td>
                                        <i class="fa fa-user" style="color: #FF8D1B;"></i>
                                        <strong>{{ $comment->user->name ?? 'Pengguna tidak dikenal' }}</strong>
                                    </td>
                                    <td>
                                        @if($comment->film)
                                            <a href="{{ route('movies.show', $comment->film->id) }}" target="_blank" style="color: #212121; font-weight: bold;">
                                                {{ $comment->film->title }} <i class="fa fa-external-link" style="font-size: 0.8em; color: #999;"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $comment->comment }}</td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fa fa-clock-o"></i> {{ $comment->created_at ? $comment->created_at->diffForHumans() : '-' }}
                                        </small>
                                    </td>
                                    <td style="text-align: center;">
                                        <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="Hapus Komentar">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                                        @if(request('search'))
                                            Tidak ada komentar yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>".
                                        @else
                                            Belum ada komentar.
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
                {{ $comments->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


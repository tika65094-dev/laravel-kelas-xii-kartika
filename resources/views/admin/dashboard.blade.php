@extends('templates.index')

@section('content')
<div class="general_agileits_genres">
    <div class="container" style="padding-top: 2em; padding-bottom: 3em;">
        @include('admin.nav')

        <div class="row">
            <div class="col-md-12">
                <h2 style="color: #FF8D1B; font-weight: bold; margin-bottom: 10px;">
                    <i class="fa fa-dashboard"></i> Admin Dashboard
                </h2>
                <p style="color: #666; margin-bottom: 25px;">
                    Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>! Anda masuk sebagai 
                    <span class="label label-warning" style="background-color: #FF8D1B;">{{ strtoupper(Auth::user()->role) }}</span>
                </p>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row" style="margin-bottom: 30px;">
            <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
                <a href="{{ route('admin.films.index') }}" style="text-decoration: none;">
                    <div style="background: #212121; color: #fff; padding: 20px; border-radius: 4px; border-left: 5px solid #FF8D1B;">
                        <div style="float: right; font-size: 2.5em; color: #FF8D1B;"><i class="fa fa-film"></i></div>
                        <h3 style="margin: 0; font-size: 2em; color: #fff;">{{ $totalFilms }}</h3>
                        <p style="margin: 5px 0 0; color: #aaa;">Total Film <i class="fa fa-external-link" style="font-size: 0.8em;"></i></p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
                <div style="background: #212121; color: #fff; padding: 20px; border-radius: 4px; border-left: 5px solid #FF8D1B;">
                    <div style="float: right; font-size: 2.5em; color: #FF8D1B;"><i class="fa fa-tags"></i></div>
                    <h3 style="margin: 0; font-size: 2em; color: #fff;">{{ $totalGenres }}</h3>
                    <p style="margin: 5px 0 0; color: #aaa;">Total Genre</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
                <a href="{{ route('admin.users.index') }}" style="text-decoration: none;">
                    <div style="background: #212121; color: #fff; padding: 20px; border-radius: 4px; border-left: 5px solid #FF8D1B;">
                        <div style="float: right; font-size: 2.5em; color: #FF8D1B;"><i class="fa fa-users"></i></div>
                        <h3 style="margin: 0; font-size: 2em; color: #fff;">{{ $totalUsers }}</h3>
                        <p style="margin: 5px 0 0; color: #aaa;">Total Pengguna <i class="fa fa-external-link" style="font-size: 0.8em;"></i></p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
                <a href="{{ route('admin.film-cast') }}" style="text-decoration: none;">
                    <div style="background: #212121; color: #fff; padding: 20px; border-radius: 4px; border-left: 5px solid #FF8D1B;">
                        <div style="float: right; font-size: 2.5em; color: #FF8D1B;"><i class="fa fa-star"></i></div>
                        <h3 style="margin: 0; font-size: 2em; color: #fff;">{{ $totalCasts }}</h3>
                        <p style="margin: 5px 0 0; color: #aaa;">Total Pemeran <i class="fa fa-external-link" style="font-size: 0.8em;"></i></p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Users & Films Tables -->
        <div class="row">
            <div class="col-md-6" style="margin-bottom: 20px;">
                <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
                    <div class="panel-heading" style="background: #f5f5f5; font-weight: bold;">
                        <i class="fa fa-users"></i> Pengguna Terbaru
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <table class="table table-striped table-hover" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $usr)
                                    <tr>
                                        <td>{{ $usr->id }}</td>
                                        <td>{{ $usr->name }}</td>
                                        <td>{{ $usr->email }}</td>
                                        <td>
                                            <span class="label {{ $usr->role === 'admin' ? 'label-warning' : 'label-default' }}">
                                                {{ $usr->role }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada user</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6" style="margin-bottom: 20px;">
                <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
                    <div class="panel-heading" style="background: #f5f5f5; font-weight: bold;">
                        <i class="fa fa-film"></i> Film Terbaru
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <table class="table table-striped table-hover" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Genre</th>
                                    <th>Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentFilms as $flm)
                                    <tr>
                                        <td><a href="{{ route('movies.show', $flm->id) }}">{{ $flm->title }}</a></td>
                                        <td>{{ $flm->genre->name ?? '-' }}</td>
                                        <td>{{ $flm->year }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada film</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Comments Table -->
        <div class="row">
            <div class="col-md-12" style="margin-bottom: 20px;">
                <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
                    <div class="panel-heading" style="background: #f5f5f5; font-weight: bold;">
                        <i class="fa fa-comments"></i> Komentar Terbaru
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <table class="table table-striped table-hover" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Pengguna</th>
                                    <th style="width: 25%;">Film</th>
                                    <th>Komentar</th>
                                    <th style="width: 15%;">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentComments as $comment)
                                    <tr>
                                        <td>
                                            <i class="fa fa-user" style="color: #FF8D1B;"></i> 
                                            <strong>{{ $comment->user->name ?? 'Pengguna tidak dikenal' }}</strong>
                                        </td>
                                        <td>
                                            @if($comment->film)
                                                <a href="{{ route('movies.show', $comment->film->id) }}" style="color: #212121; font-weight: bold;">
                                                    {{ $comment->film->title }}
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada komentar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


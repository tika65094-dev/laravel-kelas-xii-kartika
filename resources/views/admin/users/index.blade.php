@extends('templates.index')

@section('content')
<div class="general_agileits_genres">
    <div class="container" style="padding-top: 2em; padding-bottom: 3em;">
        @include('admin.nav')

        <div class="row">
            <div class="col-md-6 col-sm-6">
                <h2 style="color: #FF8D1B; font-weight: bold; margin-bottom: 5px;">
                    <i class="fa fa-users"></i> Kelola Data Pengguna
                </h2>
                <p style="color: #666; margin-bottom: 20px;">
                    Total {{ $users->total() }} pengguna terdaftar di sistem.
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
                <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline">
                    <div class="input-group" style="width: 100%;">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama atau email pengguna..." value="{{ request('search') }}">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit" style="background: #212121; color: #fff;">
                                <i class="fa fa-search"></i> Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-default" title="Reset">
                                    <i class="fa fa-times"></i>
                                </a>
                            @endif
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
            <div class="panel-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="width: 60px;">ID</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Terdaftar Pada</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === Auth::id())
                                            <span class="label label-info" style="margin-left: 5px;">Anda</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="label {{ $user->role === 'admin' ? 'label-warning' : 'label-default' }}">
                                            {{ strtoupper($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</td>
                                    <td style="text-align: center;">
                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna &quot;{{ addslashes($user->name) }}&quot;?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger" title="Hapus Pengguna">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted" style="font-size: 11px;">(Akun Anda)</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                                        @if(request('search'))
                                            Tidak ada pengguna yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>".
                                        @else
                                            Belum ada pengguna.
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
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


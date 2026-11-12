@extends('templates.index')

@section('content')
<div class="general_agileits_genres">
    <div class="container" style="padding-top: 2em; padding-bottom: 3em;">
        @include('admin.nav')

        <div class="row">
            <div class="col-md-12">
                <h2 style="color: #FF8D1B; font-weight: bold; margin-bottom: 5px;">
                    <i class="fa fa-users"></i> Kelola Cast Film
                </h2>
                <p style="color: #666; margin-bottom: 20px;">
                    Pilih film untuk melihat, menambahkan, atau menghapus daftar cast dan peran.
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

        <div class="row">
            <!-- Film selector + current cast list -->
            <div class="col-md-7" style="margin-bottom: 20px;">
                <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
                    <div class="panel-heading" style="background: #f5f5f5; font-weight: bold;">
                        <i class="fa fa-film"></i> Pilih Film
                    </div>
                    <div class="panel-body">
                        <form method="GET" action="{{ route('admin.film-cast') }}">
                            <div class="form-group">
                                <select name="film_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Pilih Film --</option>
                                    @foreach($films as $f)
                                        <option value="{{ $f->id }}" {{ ($selectedFilm && $selectedFilm->id == $f->id) ? 'selected' : '' }}>
                                            {{ $f->title }} ({{ $f->year }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                @if($selectedFilm)
                <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
                    <div class="panel-heading" style="background: #f5f5f5; font-weight: bold;">
                        <i class="fa fa-star"></i> Cast untuk: {{ $selectedFilm->title }}
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <table class="table table-striped table-hover" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Nama Cast</th>
                                    <th>Peran / Karakter</th>
                                    <th style="width: 80px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filmPerans as $peran)
                                    <tr>
                                        <td>{{ $peran->cast->name ?? 'Unknown' }}</td>
                                        <td>{{ $peran->actor }}</td>
                                        <td style="text-align: center;">
                                            <form action="{{ route('admin.film-cast.destroy', $peran->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus cast ini dari film?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            Belum ada cast untuk film ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Add cast to film form -->
            <div class="col-md-5" style="margin-bottom: 20px;">
                @if($selectedFilm)
                <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
                    <div class="panel-heading" style="background: #FF8D1B; color: #fff; font-weight: bold;">
                        <i class="fa fa-plus"></i> Tambah Cast ke "{{ $selectedFilm->title }}"
                    </div>
                    <div class="panel-body">
                        @if($errors->any())
                            <div class="alert alert-danger" style="font-size: 13px;">
                                @foreach($errors->all() as $error)
                                    <div><i class="fa fa-exclamation-circle"></i> {{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('admin.film-cast.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="film_id" value="{{ $selectedFilm->id }}">

                            <div class="form-group">
                                <label>Pilih Cast</label>
                                <select name="cast_id" class="form-control" required>
                                    <option value="">-- Pilih Cast --</option>
                                    @foreach($availableCasts as $cast)
                                        <option value="{{ $cast->id }}" {{ old('cast_id') == $cast->id ? 'selected' : '' }}>
                                            {{ $cast->name }} (Umur: {{ $cast->age }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Nama Peran / Karakter</label>
                                <input type="text" name="actor" class="form-control" placeholder="Contoh: Detektif, Guru, dll." value="{{ old('actor') }}" required>
                            </div>

                            <button type="submit" class="btn btn-block" style="background: #FF8D1B; color: #fff; font-weight: bold;">
                                <i class="fa fa-plus"></i> Tambahkan Cast
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="panel panel-default" style="border: 1px solid #e0e0e0;">
                    <div class="panel-body text-center text-muted" style="padding: 40px;">
                        <i class="fa fa-info-circle" style="font-size: 2em; color: #FF8D1B;"></i>
                        <p style="margin-top: 10px;">Pilih film terlebih dahulu untuk mengelola cast.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


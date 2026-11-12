@extends('templates.index')

@section('content')
<div class="general_agileits_genres">
    <div class="container" style="padding-top: 2em; padding-bottom: 3em;">
        @include('admin.nav')

        <div class="row">
            <div class="col-md-12">
                <h2 style="color: #FF8D1B; font-weight: bold; margin-bottom: 5px;">
                    <i class="fa fa-plus-circle"></i> Tambah Film Baru
                </h2>
                <p style="color: #666; margin-bottom: 20px;">
                    <a href="{{ route('admin.films.index') }}" style="color: #FF8D1B;">&laquo; Kembali ke Daftar Film</a>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default" style="border: 1px solid #e0e0e0; border-top: 4px solid #FF8D1B;">
                    <div class="panel-heading" style="background: #f9f9f9; font-weight: bold;">
                        Form Tambah Film
                    </div>
                    <div class="panel-body" style="padding: 25px;">
                        @if($errors->any())
                            <div class="alert alert-danger" style="margin-bottom: 20px;">
                                <ul style="margin-bottom: 0; padding-left: 20px;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.films.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                <label for="title">Judul Film <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Interstellar" required>
                                @if($errors->has('title'))
                                    <span class="help-block">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('genre_id') ? 'has-error' : '' }}">
                                        <label for="genre_id">Genre <span class="text-danger">*</span></label>
                                        <select name="genre_id" id="genre_id" class="form-control" required>
                                            <option value="">-- Pilih Genre --</option>
                                            @foreach($genres as $genre)
                                                <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
                                                    {{ $genre->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('genre_id'))
                                            <span class="help-block">{{ $errors->first('genre_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('year') ? 'has-error' : '' }}">
                                        <label for="year">Tahun Rilis <span class="text-danger">*</span></label>
                                        <input type="number" name="year" id="year" class="form-control" value="{{ old('year', date('Y')) }}" min="1900" max="{{ date('Y') + 5 }}" placeholder="Contoh: {{ date('Y') }}" required>
                                        @if($errors->has('year'))
                                            <span class="help-block">{{ $errors->first('year') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('poster') ? 'has-error' : '' }}">
                                <label for="poster">URL atau Lokasi Poster <span class="text-danger">*</span></label>
                                <input type="text" name="poster" id="poster" class="form-control" value="{{ old('poster', 'images/1.jpg') }}" placeholder="Contoh: images/1.jpg atau URL gambar" required>
                                <small class="text-muted">Masukkan path gambar di folder public (misal: <code>images/1.jpg</code>) atau URL lengkap.</small>
                                @if($errors->has('poster'))
                                    <span class="help-block">{{ $errors->first('poster') }}</span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('sinopsis') ? 'has-error' : '' }}">
                                <label for="sinopsis">Sinopsis <span class="text-danger">*</span></label>
                                <textarea name="sinopsis" id="sinopsis" rows="5" class="form-control" placeholder="Tuliskan ringkasan jalan cerita film..." required>{{ old('sinopsis') }}</textarea>
                                @if($errors->has('sinopsis'))
                                    <span class="help-block">{{ $errors->first('sinopsis') }}</span>
                                @endif
                            </div>

                            <div class="form-group" style="margin-top: 25px;">
                                <button type="submit" class="btn btn-primary" style="background: #FF8D1B; border-color: #FF8D1B; font-weight: bold; padding: 10px 30px;">
                                    <i class="fa fa-save"></i> Simpan Film
                                </button>
                                <a href="{{ route('admin.films.index') }}" class="btn btn-default" style="margin-left: 10px; padding: 10px 20px;">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Edit Motor</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        {{-- form edit data --}}
        <form action="{{ route('motors.update', $motor->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-7">
                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select select2-single @error('category') is-invalid @enderror" autocomplete="off">
                            <option disabled value="">- Select category -</option>
                            @foreach ($categories as $category)
                                <option {{ old('category', $motor->category_id) == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->type_motor }}</option>
                            @endforeach
                        </select>

                        {{-- pesan error untuk category --}}
                        @error('category')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Nama Motor <span class="text-danger">*</span></label>
                        <input type="text" name="nama_motor" class="form-control @error('nama_motor') is-invalid @enderror" value="{{ old('nama_motor', $motor->nama_motor) }}" autocomplete="off">
                        
                        {{-- pesan error untuk nama motor --}}
                        @error('name')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Harga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="harga" class="form-control mask-number @error('harga') is-invalid @enderror" value="{{ old('harga', number_format($motor->harga, 0, '', '.')) }}" autocomplete="off">
                        </div>
                        
                        {{-- pesan error untuk harga --}}
                        @error('harga')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="mb-3 ps-xl-3">
                        <label class="form-label">Image</label>
                        <input type="file" accept=".jpg, .jpeg, .png" name="image" id="image" class="form-control @error('image') is-invalid @enderror" autocomplete="off">
            
                        {{-- pesan error untuk image --}}
                        @error('image')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- view image --}}
                        <div class="mt-4">
                            <img id="imagePreview" src="{{ $motor->image }}" class="img-thumbnail rounded-4 shadow-sm" width="50%" alt="Image">
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="pt-4 pb-2 mt-5 border-top">
                <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                    {{-- button update data --}}
                    <button type="submit" class="btn btn-primary py-2 px-3">Update</button>
                    {{-- button kembali ke halaman index --}}
                    <a href="{{ route('motors.index') }}" class="btn btn-secondary py-2 px-3">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
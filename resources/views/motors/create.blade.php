<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Tambah Motor</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        {{-- form add data --}}
        <form action="{{ route('motors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-7">
                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select select2-single @error('category') is-invalid @enderror" autocomplete="off">
                            <option selected disabled value="">- Select category -</option>
                            @foreach ($categories as $category)
                                <option {{ old('category') == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->type_motor }}</option>
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
                        <label class="form-label">Nama Motor<span class="text-danger">*</span></label>
                        <input type="text" name="nama_motor" class="form-control @error('nama_motor') is-invalid @enderror" value="{{ old('nama_motor') }}" autocomplete="off">
                        
                        {{-- pesan error untuk name --}}
                        @error('nama_motor')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Harga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="harga" class="form-control mask-number @error('harga') is-invalid @enderror" value="{{ old('harga') }}" autocomplete="off">
                        </div>
                        
                        {{-- pesan error untuk price --}}
                        @error('harga')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-5">
                <div id="dropArea" class="mb-3 ps-xl-3">
                    <label class="form-label">Image <span class="text-danger">*</span></label>
                        <input type="file" accept=".jpg, .jpeg, .png" name="image" id="image" class="form-control @error('image') is-invalid @enderror" autocomplete="off">
                        {{-- <input type="file" accept=".jpg, .jpeg, .png" name="image" id="image" class="d-none"> --}}
                
                    {{-- pesan error untuk image --}}
                @error('image')
                    <div class="alert alert-danger mt-2">
                        {{ $message }}
                    </div>
                @enderror

                {{-- preview image --}}
                <div class="mt-4">
                    <img id="imagePreview" src="{{ asset('images/no-image.svg') }}" class="img-thumbnail rounded-4 shadow-sm" width="50%" alt="Image">
                </div>
            </div>
        </div>
    </div>
    
            <div class="pt-4 pb-2 mt-5 border-top">
                <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                    {{-- button simpan data --}}
                    <button type="submit" class="btn btn-primary py-2 px-4">Save</button>
                    {{-- button kembali ke halaman index --}}
                    <a href="{{ route('motors.index') }}" class="btn btn-secondary py-2 px-3">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let dropArea = document.getElementById("dropArea");
        let inputFile = document.getElementById("image");
        let imagePreview = document.getElementById("imagePreview");
    
        // Prevent default behaviors (drag & drop)
        ["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => e.preventDefault(), false);
        });
    
        // Highlight drop area when dragging over it
        ["dragenter", "dragover"].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.add("border-primary", "shadow-lg");
            }, false);
        });
    
        ["dragleave", "drop"].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.remove("border-primary", "shadow-lg");
            }, false);
        });
    
        // Handle dropped files
        dropArea.addEventListener("drop", (event) => {
            let files = event.dataTransfer.files;
            if (files.length > 0) {
                let file = files[0];
    
                // Validate file type
                if (!["image/jpeg", "image/png", "image/jpg"].includes(file.type)) {
                    alert("Only JPG, JPEG, and PNG files are allowed.");
                    return;
                }
    
                // Display image preview
                let reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
    
                // Assign file to input
                inputFile.files = files;
            }
        });
    
        // Click event to open file selector
        dropArea.addEventListener("click", () => inputFile.click());
    
        // Handle file selection manually
        inputFile.addEventListener("change", function () {
            if (this.files.length > 0) {
                let file = this.files[0];
                let reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
</x-app-layout>
<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Tambah Product</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        {{-- form add data --}}
        <form action="{{ route('food.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-7">
                    {{-- Nama Produk --}}
                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk') }}" autocomplete="off">

                        @error('nama_produk')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror">
                            <option disabled selected value="">- Select category -</option>
                            <option value="makanan" {{ old('category') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                            <option value="minuman" {{ old('category') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                        </select>

                        @error('category')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>  

                    {{-- Harga --}}
                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Harga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="harga" class="form-control mask-number @error('harga') is-invalid @enderror" value="{{ old('harga') }}" autocomplete="off">
                        </div>

                        @error('harga')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Qty --}}
                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Stok (Qty) <span class="text-danger">*</span></label>
                        <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty') }}" min="0" autocomplete="off">

                        @error('qty')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Image Upload --}}
                <div class="col-lg-5">
                    <div id="dropArea" class="mb-3 ps-xl-3">
                        <label class="form-label">Image</label>
                        <input type="file" accept=".jpg, .jpeg, .png" name="image" id="image" class="form-control @error('image') is-invalid @enderror" autocomplete="off">

                        @error('image')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror

                        {{-- Preview image --}}
                        <div class="mt-4">
                            <img id="imagePreview" src="{{ asset('images/no-image.svg') }}" class="img-thumbnail rounded-4 shadow-sm" width="50%" alt="Image">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="pt-4 pb-2 mt-5 border-top">
                <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                    <button type="submit" class="btn btn-primary py-2 px-4">Save</button>
                    <a href="{{ route('food.index') }}" class="btn btn-secondary py-2 px-3">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Script Preview --}}
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

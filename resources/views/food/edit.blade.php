<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Edit Product</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        {{-- form edit data --}}
        <form action="{{ route('food.update', $food->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-7">
                    {{-- Nama Produk --}}
                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nama_produk" 
                               class="form-control @error('nama_produk') is-invalid @enderror" 
                               value="{{ old('nama_produk', $food->nama_produk) }}" 
                               autocomplete="off">

                        @error('nama_produk')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror">
                            <option disabled value="">- Select category -</option>
                            <option value="makanan" {{ old('category', $food->category) == 'makanan' ? 'selected' : '' }}>Makanan</option>
                            <option value="minuman" {{ old('category', $food->category) == 'minuman' ? 'selected' : '' }}>Minuman</option>
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
                            <input type="text" 
                                   name="harga" 
                                   class="form-control mask-number @error('harga') is-invalid @enderror" 
                                   value="{{ old('harga', number_format($food->harga, 0, ',', '.')) }}" 
                                   autocomplete="off">
                        </div>

                        @error('harga')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Qty --}}
                    <div class="mb-3 pe-xl-3">
                        <label class="form-label">Stok (Qty) <span class="text-danger">*</span></label>
                        <input type="number" 
                               name="qty" 
                               class="form-control @error('qty') is-invalid @enderror" 
                               value="{{ old('qty', $food->qty) }}" 
                               min="0" autocomplete="off">

                        @error('qty')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Image Upload --}}
                <div class="col-lg-5">
                    <div id="dropArea" class="mb-3 ps-xl-3">
                        <label class="form-label">Image</label>
                        <input type="file" 
                               accept=".jpg, .jpeg, .png" 
                               name="image" 
                               id="image" 
                               class="form-control @error('image') is-invalid @enderror" 
                               autocomplete="off">

                        @error('image')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror

                        {{-- Preview image --}}
                        <div class="mt-4">
                            <img id="imagePreview" 
                                 src="{{ $food->image ? asset($food->image) : asset('images/no-image.svg') }}" 
                                 class="img-thumbnail rounded-4 shadow-sm" 
                                 width="50%" 
                                 alt="Image">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="pt-4 pb-2 mt-5 border-top">
                <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                    <button type="submit" class="btn btn-primary py-2 px-4">Update</button>
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
        
            ["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
                dropArea.addEventListener(eventName, (e) => e.preventDefault(), false);
            });
        
            ["dragenter", "dragover"].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.add("border-primary", "shadow-lg");
                }, false);
            });
        
            ["dragleave", "drop"].forEach(eventName => {
                dropArea.classList.remove("border-primary", "shadow-lg");
            }, false);
        
            dropArea.addEventListener("drop", (event) => {
                let files = event.dataTransfer.files;
                if (files.length > 0) {
                    let file = files[0];
                    if (!["image/jpeg", "image/png", "image/jpg"].includes(file.type)) {
                        alert("Only JPG, JPEG, and PNG files are allowed.");
                        return;
                    }
                    let reader = new FileReader();
                    reader.onload = (e) => imagePreview.src = e.target.result;
                    reader.readAsDataURL(file);
                    inputFile.files = files;
                }
            });
        
            dropArea.addEventListener("click", () => inputFile.click());
        
            inputFile.addEventListener("change", function () {
                if (this.files.length > 0) {
                    let reader = new FileReader();
                    reader.onload = (e) => imagePreview.src = e.target.result;
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
</x-app-layout>

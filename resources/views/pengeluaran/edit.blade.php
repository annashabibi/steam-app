<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Edit Pengeluaran</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        {{-- form edit data --}}
        <form action="{{ route('pengeluaran.update', $pengeluaran->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="text" name="date" class="form-control datepicker @error('date') is-invalid @enderror"
                               value="{{ old('date', $pengeluaran->date) }}" autocomplete="off">

                        @error('date')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <hr class="mt-4">

                    <div class="mb-3">
                        <label class="form-label">Jenis Pengeluaran <span class="text-danger">*</span></label>
                        <select id="jenis_pengeluaran" name="jenis_pengeluaran" class="form-select select2-single @error('jenis_pengeluaran') is-invalid @enderror" onchange="toggleKaryawan()">
                            <option disabled value="">- Select Pengeluaran -</option>
                            <option value="Kasbon" {{ old('jenis_pengeluaran', $pengeluaran->jenis_pengeluaran) === 'Kasbon' ? 'selected' : '' }}>Kasbon</option>
                            <option value="Uang Makan" {{ old('jenis_pengeluaran', $pengeluaran->jenis_pengeluaran) === 'Uang Makan' ? 'selected' : '' }}>Uang Makan</option>
                            <option value="Token" {{ old('jenis_pengeluaran', $pengeluaran->jenis_pengeluaran) === 'Token' ? 'selected' : '' }}>Token</option>
                            <option value="Air" {{ old('jenis_pengeluaran', $pengeluaran->jenis_pengeluaran) === 'Air' ? 'selected' : '' }}>Galon</option>
                            <option value="Sabun" {{ old('jenis_pengeluaran', $pengeluaran->jenis_pengeluaran) === 'Sabun' ? 'selected' : '' }}>Sabun</option>
                            <option value="Uang Sampah" {{ old('jenis_pengeluaran', $pengeluaran->jenis_pengeluaran) === 'Uang Sampah' ? 'selected' : '' }}>Uang Sampah</option>
                        </select>

                        @error('jenis_pengeluaran')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3" id="karyawan_field">
                        <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                        <select name="karyawan_id" class="form-select select2-single @error('karyawan_id') is-invalid @enderror">
                            <option disabled value="">- Select Karyawan -</option>
                            @foreach ($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $pengeluaran->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>

                        @error('karyawan_id')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="jumlah" name="jumlah" class="form-control mask-number @error('jumlah') is-invalid @enderror"
                                   value="{{ old('jumlah', number_format($pengeluaran->jumlah, 0, '', '.')) }}" autocomplete="off">
                        </div>

                        @error('jumlah')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 pb-2 mt-5 border-top">
                    <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                        <button type="submit" class="btn btn-primary py-2 px-4">Update</button>
                        <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary py-2 px-3">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleKaryawan() {
            var jenis = document.getElementById("jenis_pengeluaran").value;
            var karyawanField = document.getElementById("karyawan_field");

            if (jenis === "Kasbon") {
                karyawanField.style.display = "block";
            } else {
                karyawanField.style.display = "none";
                document.querySelector("select[name='karyawan_id']").value = "";
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            toggleKaryawan();
        });

        document.addEventListener("DOMContentLoaded", function () {
            var jumlahInput = document.getElementById("jumlah");

            jumlahInput.addEventListener("input", function () {
                var value = this.value.replace(/\D/g, "");
                var formattedValue = new Intl.NumberFormat("id-ID").format(value);

                this.value = formattedValue;
            });
        });
    </script>
</x-app-layout>

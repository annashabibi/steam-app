<x-app-layout>
    <x-page-title>Add Helm Transaction</x-page-title>
    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        <form action="{{ route('helms.store') }}" method="POST" id="helmForm">
            @csrf
            <div class="row">
                <div class="col-lg-6">

                    {{-- Nama Customer --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Customer</label>
                        <input type="text" name="nama_customer" class="form-control @error('nama_customer') is-invalid @enderror" value="{{ old('nama_customer') }}" autocomplete="off">
                        @error('nama_customer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Cuci --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal Cuci <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_cuci" class="form-control datepicker @error('tanggal_cuci') is-invalid @enderror" value="{{ old('tanggal_cuci', date('Y-m-d')) }}" autocomplete="off">
                        @error('tanggal_cuci')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control datepicker @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" autocomplete="off">
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    {{-- Helm Items --}}
                    <div id="helm-items">
                        <div class="helm-item border p-3 mb-3 rounded shadow-sm">
                            {{-- Nama Helm --}}
                            <div class="mb-3">
                                <label class="form-label">Nama Helm <span class="text-danger">*</span></label>
                                <input type="text" name="nama_helm[]" class="form-control @error('nama_helm.*') is-invalid @enderror" value="{{ old('nama_helm.0') }}" required>
                                @error('nama_helm.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Type Helm --}}
                            <div class="mb-3">
                                <label class="form-label">Tipe Helm <span class="text-danger">*</span></label>
                                <select name="type_helm[]" class="form-select @error('type_helm.*') is-invalid @enderror" required>
                                    <option value="">- Pilih Tipe Helm -</option>
                                    <option value="half_face" {{ old('type_helm.0') === 'half_face' ? 'selected' : '' }}>Half Face - Rp18.000</option>
                                    <option value="full_face" {{ old('type_helm.0') === 'full_face' ? 'selected' : '' }}>Full Face - Rp20.000</option>
                                </select>
                                @error('type_helm.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Karyawan --}}
                            <div class="mb-3">
                                <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                                <select name="karyawan_id[]" class="form-select select2-single @error('karyawan_id.*') is-invalid @enderror" required>
                                    <option value="">- Pilih Karyawan -</option>
                                    @foreach ($karyawans as $karyawan)
                                        <option value="{{ $karyawan->id }}" {{ old('karyawan_id.0') == $karyawan->id ? 'selected' : '' }}>
                                            {{ $karyawan->nama_karyawan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('karyawan_id.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-danger btn-sm remove-item py-2 px-4">Hapus</button>
                        </div>
                    </div>

                    <button type="button" id="add-helm-item" class="btn btn-outline-primary mb-4">+ Tambah Helm</button>

                    {{-- Total Harga --}}
                    <div class="mb-3">
                        <label class="form-label">Total Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="total-harga" class="form-control" readonly>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <input type="hidden" name="payment_method" id="payment_method" value="">
                    <div class="pt-4 pb-2 mt-5 border-top">
                        <div class="d-grid gap-3 d-sm-flex pt-1">
                            <button type="button" id="btn-cash" class="btn btn-primary py-2 px-4">Cash</button>
                            <button type="button" id="btn-online" class="btn btn-warning py-2 px-4">Online</button>
                            <a href="{{ route('helms.index') }}" class="btn btn-secondary py-2 px-4">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <script>
    $(document).ready(function () {
        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function updateTotal() {
            let total = 0;
            $('select[name="type_helm[]"]').each(function () {
                const val = $(this).val();
                if (val === 'half_face') total += 18000;
                else if (val === 'full_face') total += 20000;
            });
            $('#total-harga').val(formatRupiah(total));
        }

        function reinitializeSelect2() {
            $('.select2-single').select2({
                dropdownParent: $('#helm-items')
            });
        }

        $('#add-helm-item').on('click', function () {
            const clone = $('.helm-item:first').clone();

            // Hapus error dan isi input
            clone.find('input').val('').removeClass('is-invalid');

            // Reset semua select
            clone.find('select').each(function () {
                $(this).val('').removeClass('is-invalid');
            });

            // Hapus select2 sebelumnya agar tidak bentrok
            clone.find('.select2-container').remove();
            clone.find('.select2-single').removeAttr('data-select2-id').removeClass('select2-hidden-accessible').removeAttr('aria-hidden');

            $('#helm-items').append(clone);

            // Re-init Select2
            reinitializeSelect2();
            updateTotal();
        });

        $(document).on('click', '.remove-item', function () {
            if ($('.helm-item').length > 1) {
                $(this).closest('.helm-item').remove();
                updateTotal();
            } else {
                alert('Minimal harus ada 1 helm');
            }
        });

        $(document).on('change', 'select[name="type_helm[]"]', updateTotal);

        $('#btn-cash').on('click', function () {
            $('#payment_method').val('offline');
            $(this).prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Processing...');
            $('#helmForm').submit();
        });

        $('#btn-online').on('click', function () {
            $('#payment_method').val('online');
            $(this).prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Processing...');
            $('#btn-cash').prop('disabled', true);
            $('#helmForm').submit();
        });

        reinitializeSelect2();
        updateTotal();
    });
</script>
</x-app-layout>
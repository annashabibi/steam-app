<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Edit Transaction</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        {{-- form edit data --}}
        <form id="transactionForm" action="{{ route('transactions.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="text" name="date" class="form-control datepicker @error('date') is-invalid @enderror" value="{{ old('date', $transaction->date) }}" autocomplete="off">
                        
                        {{-- pesan error untuk date --}}
                        @error('date')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <hr class="mt-4">

                    <div class="mb-3">
                        <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                        <select name="karyawan" class="form-select select2-single @error('karyawan') is-invalid @enderror" autocomplete="off">
                            <option disabled value="">- Select Karyawan -</option>
                            @foreach ($karyawans as $karyawan)
                                <option {{ old('karyawan', $transaction->karyawan_id) == $karyawan->id ? 'selected' : '' }} value="{{ $karyawan->id }}">{{ $karyawan->nama_karyawan }}</option>
                            @endforeach
                        </select>

                        {{-- pesan error untuk karyawan --}}
                        @error('karyawan')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Motor <span class="text-danger">*</span></label>
                        <select id="motor" name="motor" class="form-select select2-single @error('motor') is-invalid @enderror" autocomplete="off">
                            <option disabled value="">- Select Motor -</option>
                            @foreach ($motors as $motor)
                                <option {{ old('motor', $transaction->motor_id) == $motor->id ? 'selected' : '' }} value="{{ $motor->id }}" data-harga="{{ number_format($motor->harga, 0, '', '.') }}">{{ $motor->nama_motor }}</option>
                                {{-- <option {{ old('motor', $transaction->motor_id) == $motor->id ? 'selected' : '' }} value="{{ $motor->id }}" data-harga="{{ $motor->harga }}">{{ $motor->nama_motor }} --}}
                            </option>
                                @endforeach
                        </select>

                        {{-- pesan error untuk motor --}}
                        @error('motor')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="harga" name="harga" class="form-control mask-number @error('harga') is-invalid @enderror" value="{{ old('harga', number_format($transaction->motor->harga, 0, '', '.')) }}" readonly>
                        </div>
                        
                        {{-- pesan error untuk harga --}}
                        @error('harga')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tip <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="tip" name="tip" class="form-control mask-number @error('tip') is-invalid @enderror" value="{{ old('tip', number_format($transaction->tip, 0, '', '.')) }}" autocomplete="off">
                        </div>
                        
                        {{-- pesan error untuk tip --}}
                        @error('tip')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="total" name="total" class="form-control mask-number @error('total') is-invalid @enderror" value="{{ old('total', number_format($transaction->total, 0, '', '.')) }}" readonly>
                        </div>
                        
                        {{-- pesan error untuk total --}}
                        @error('total')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
    
            <div class="pt-4 pb-2 mt-5 border-top">
                <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                    {{-- button update data --}}
                    <button type="submit" class="btn btn-primary py-2 px-3">Update</button>
                    {{-- button kembali ke halaman index --}}
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary py-2 px-3">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        document.getElementById('transactionForm').addEventListener('submit', function (e) {
            const tipInput = document.getElementById('tip');
            if (tipInput.value === '') {
                tipInput.value = '0';
            }
        });

        document.getElementById('motor').addEventListener('change', function () {
            var harga = this.options[this.selectedIndex].getAttribute('data-harga');
            harga = harga ? harga.replace(/\./g, '') : '0';
            document.getElementById('harga').value = formatNumber(harga);

            var tip = document.getElementById('tip').value.replace(/\./g, '') || '0';
            var total = parseInt(harga) + parseInt(tip);
            document.getElementById('total').value = formatNumber(total);
        });

        document.getElementById('tip').addEventListener('input', function () {
            var harga = document.getElementById('harga').value.replace(/\./g, '');
            var tip = this.value.replace(/\./g, '') || '0';

            if (harga !== "") {
                var total = parseInt(harga) + parseInt(tip);
                document.getElementById('total').value = formatNumber(total);
            }
        });

        document.getElementById('total').addEventListener('input', function () {
            var total = this.value.replace(/\./g, '');
            this.value = formatNumber(total);
        });

        // Trigger pertama saat halaman load
        document.getElementById('motor').dispatchEvent(new Event('change'));

        // ✅ Tambahan ini untuk Select2 agar trigger tetap jalan
        $('#motor').on('select2:select', function (e) {
            this.dispatchEvent(new Event('change'));
        });
    });
</script>
</x-app-layout>

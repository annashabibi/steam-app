<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Add Transaction</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        {{-- form add data --}}
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control datepicker @error('date') is-invalid @enderror" value="{{ today()->format('Y-m-d') }}" readonly autocomplete="off">
                        {{-- Test Tanggal Manual --}}
                        {{-- <input type="date" name="date" class="form-control datepicker @error('date') is-invalid @enderror" value="2025-07-30" autocomplete="off"> --}}


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
                            <option selected disabled value="">- Select Karyawan -</option>
                            @foreach ($karyawans as $karyawan)
                                <option {{ old('karyawan') == $karyawan->id ? 'selected' : '' }} value="{{ $karyawan->id }}">{{ $karyawan->nama_karyawan }}</option>
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
                            <option selected disabled value="">- Select motor -</option>
                            @foreach ($motors as $motor)
                                <option {{ old('motor') == $motor->id ? 'selected' : '' }} value="{{ $motor->id }}" data-harga="{{ number_format($motor->harga, 0, '', '.') }}">{{ $motor->nama_motor }}</option>
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
                            <input type="text" id="harga" name="harga" class="form-control mask-number @error('harga') is-invalid @enderror" value="{{ old('harga') }}" readonly>
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
                                <input type="text" id="tip" name="tip" class="form-control mask-number @error('tip') is-invalid @enderror" value="{{ old('tip') }}" autocomplete="off">
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
                            <input type="text" id="total" name="total" class="form-control mask-number @error('total') is-invalid @enderror" value="{{ old('total') }}" readonly>
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
    
            {{-- Hidden input for payment_method --}}
            <input type="hidden" name="payment_method" id="payment_method" value="">

            <div class="pt-4 pb-2 mt-5 border-top">
                <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                    {{-- button simpan data --}}
                    <button type="button" id="btn-cash" class="btn btn-primary py-2 px-4">Cash</button>
                    <button type="button" id="btn-online" class="btn btn-warning py-2 px-4">Online</button>
                    {{-- button kembali ke halaman index --}}
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary py-2 px-3">Cancel</a>
                </div>
            </div>
        </form>
    </div>
    
    <script type="text/javascript">
        $(document).ready(function() {
            // Fungsi untuk memformat angka dengan pemisah ribuan
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

                $('#motor').change(function() {
                    var harga = $(this).children('option:selected').data('harga').replace(/\./g, '');
                    $('#harga').val(formatNumber(harga));
                    $('#tip').val('');
                    $('#total').val(formatNumber(harga));
                });

                $('#tip').keyup(function() {
                    var harga = $('#harga').val().replace(/\./g, '');
                    var tip = $(this).val().replace(/\./g, '');

                    if (harga === "") {
                        $.notify({
                            title: '<h6 class="fw-bold mb-1"><i class="ti ti-circle-x-filled fs-5 align-text-top me-2"></i>Error!</h6>',
                            message: 'The harga field is required.',
                        }, {
                            type: 'danger',
                            delay: 500,
                        });
                        $('#tip').val('');
                        var total = "";
                    } else if (tip === "") {
                        var total = parseInt(harga);
                    } else if (tip < 0) {
                        $.notify({
                            title: '<h6 class="fw-bold mb-1"><i class="ti ti-circle-x-filled fs-5 align-text-top me-2"></i>Error!</h6>',
                            message: 'The tip field must be filled with positive integers.'
                        }, {
                            type: 'danger',
                            delay: 500
                        });
                        $('#tip').val('');
                        var total = parseInt(harga);
                    } else {
                        var total = parseInt(harga) + parseInt(tip);
                    }

                    var formattedTotal = formatNumber(total);
                    $('#total').val(formattedTotal);
                });

            $('#total').keyup(function() {
                var total = $(this).val().replace(/\./g, '');
                $(this).val(formatNumber(total));
            });

                // Set value payment_method based on button click
                $('#btn-cash').click(function() {
                $(this).html('<i class="spinner-border spinner-border-sm"></i> Processing...').prop('disabled', true);
                $('#payment_method').val('cash');
                $('#paymentForm').submit();
            });

            $('#btn-online').click(function() {
                $(this).html('<i class="spinner-border spinner-border-sm"></i> Processing...').prop('disabled', true);
                $('#btn-cash').prop('disabled', true);
                $('#payment_method').val('midtrans');
                $('#paymentForm').submit();
            });
        });
    </script>
</x-app-layout>

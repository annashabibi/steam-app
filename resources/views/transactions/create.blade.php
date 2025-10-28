<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Tambah Transaksi</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        {{-- form add data --}}
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control datepicker @error('date') is-invalid @enderror" value="{{ today()->format('Y-m-d') }}" readonly autocomplete="off">

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

                    <div class="mb-3 d-none d-lg-block">
                        <label class="form-label">Kembalian <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="kembalian-pc" class="form-control mask-number" value="0" readonly>
                        </div>
                    </div>

                </div>

            <div class="col-lg-6">
                <div class="mb-3">
                <label class="form-label">Makanan & Minuman <span class="text-danger">*</span></label>

                <div class="food-scroll-container border rounded-3">
                    <div class="food-grid">
                        @foreach ($foods as $food)
                        @php
                        $qty = $food->qty ?? 0;
                        // Tentukan class badge berdasarkan qty
                        $badgeClass = match(true) {
                            $qty > 10 => 'bg-success',
                            $qty >= 5 => 'bg-warning',
                            $qty > 0  => 'bg-danger',
                            default   => 'bg-secondary',
                        };
                        $badgeText = $qty > 0 ? "Tersedia: $qty" : "Habis";
                        @endphp
                        <div class="food-card" data-id="{{ $food->id }}" data-name="{{ $food->nama_produk }}" data-price="{{ $food->harga }}" data-qty="{{ $qty }}">
                            <div class="food-image rounded-3 overflow-hidden shadow-sm">
                                <img src="{{ $food->image }}" alt="{{ $food->nama_produk }}">
                            </div>
                            <div class="food-details">
                                <p class="product-name fw-semibold text-dark mb-0">{{ $food->nama_produk }}</p>
                                <p class="mb-0">
                                    <span class="badge {{ $badgeClass }} qty-badge">
                                        <span class="qty-text">{{ $badgeText }}</span>
                                    </span>
                                </p>
                                <p class="product-price text-muted mb-0">Rp{{ number_format($food->harga, 0, ',', '.') }}</p>
                                    <div class="quantity-controls">
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle btn-minus">-</button>
                                        <span class="quantity">0</span>
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-circle btn-plus">+</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
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

                    <div class="mb-3">
                    <label class="form-label">Uang <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="uang" name="uang" class="form-control mask-number @error('uang') is-invalid @enderror" value="{{ old('uang') }}" autocomplete="off">
                    </div>
                    
                    {{-- pesan error untuk uang --}}
                    @error('uang')
                        <div class="alert alert-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Kembalian untuk Mobile --}}
                <div class="mb-3 d-lg-none">
                    <label class="form-label">Kembalian <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="kembalian-mobile" class="form-control mask-number" value="0" readonly>
                    </div>
                </div>
                    
                </div>
            </div>
    
            {{-- Hidden input for food --}}
            <input type="hidden" name="food_items" id="food_items_input">

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

        let totalFood = 0;

        $('#motor').change(function() {
            var harga = $(this).children('option:selected').data('harga').replace(/\./g, '');
            $('#harga').val(formatNumber(harga));
            $('#tip').val('');
            $('#total').val(formatNumber(parseInt(harga) + totalFood));
        });

        $('#tip').keyup(function() {
            var harga = $('#harga').val().replace(/\./g, '') || 0;
            var tip = $(this).val().replace(/\./g, '') || 0;

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
                var total = parseInt(harga) + totalFood;
            } else if (tip < 0) {
                $.notify({
                    title: '<h6 class="fw-bold mb-1"><i class="ti ti-circle-x-filled fs-5 align-text-top me-2"></i>Error!</h6>',
                    message: 'The tip field must be filled with positive integers.'
                }, {
                    type: 'danger',
                    delay: 500
                });
                $('#tip').val('');
                var total = parseInt(harga) + totalFood;
            } else {
                var total = parseInt(harga) + parseInt(tip) + totalFood;
            }

            var formattedTotal = formatNumber(total);
            $('#total').val(formattedTotal);
        });

        $('#total').keyup(function() {
            var total = $(this).val().replace(/\./g, '');
            $(this).val(formatNumber(total));
        });

        const foodSelections = {};

        $('.btn-plus').click(function() {
    const card = $(this).closest('.food-card');
    const id = card.data('id');
    const price = parseInt(card.data('price'));
    const maxQty = parseInt(card.data('qty'));
    const qtyEl = card.find('.quantity');
    const currentQty = parseInt(qtyEl.text());
    const stockWarning = card.find('.stock-warning');
    const qtyBadge = card.find('.qty-badge');
    const qtyText = card.find('.qty-text');

    // Cek apakah qty masih tersedia
    if (currentQty >= maxQty) {
        stockWarning.removeClass('d-none');
        
        $.notify({
            title: '<h6 class="fw-bold mb-1"><i class="ti ti-alert-circle fs-5 align-text-top me-2"></i>Stock Habis!</h6>',
            message: 'Stock ' + card.data('name') + ' tidak mencukupi.',
        }, {
            type: 'danger',
            delay: 1000,
        });

        setTimeout(function() {
            stockWarning.addClass('d-none');
        }, 3000);

        return;
    }

    let qty = currentQty + 1;
    qtyEl.text(qty);

    // Update badge
    const remaining = maxQty - qty;
    qtyText.text('Tersedia: ' + remaining);
    
    // Update warna badge
    qtyBadge.removeClass('bg-success bg-warning bg-danger bg-secondary');
    if (remaining > 10) {
        qtyBadge.addClass('bg-success');
    } else if (remaining >= 5) {
        qtyBadge.addClass('bg-warning');
    } else if (remaining > 0) {
        qtyBadge.addClass('bg-danger');
    } else {
        qtyBadge.addClass('bg-secondary');
        qtyText.text('Habis');
    }

    // Disable button jika sudah max
    if (qty >= maxQty) {
        $(this).prop('disabled', true);
    }

    foodSelections[id] = qty;
    hitungTotalFood();
});

// ====================================
// BUTTON MINUS - Dengan Update Badge
// ====================================
$('.btn-minus').click(function() {
    const card = $(this).closest('.food-card');
    const id = card.data('id');
    const maxQty = parseInt(card.data('qty'));
    const qtyEl = card.find('.quantity');
    const qtyBadge = card.find('.qty-badge');
    const qtyText = card.find('.qty-text');
    const btnPlus = card.find('.btn-plus');
    
    let qty = Math.max(0, parseInt(qtyEl.text()) - 1);
    qtyEl.text(qty);

    // Update badge
    const remaining = maxQty - qty;
    qtyText.text('Tersedia: ' + remaining);
    
    // Update warna badge
    qtyBadge.removeClass('bg-success bg-warning bg-danger bg-secondary');
    if (remaining > 10) {
        qtyBadge.addClass('bg-success');
    } else if (remaining >= 5) {
        qtyBadge.addClass('bg-warning');
    } else if (remaining > 0) {
        qtyBadge.addClass('bg-danger');
    }

    // Enable button plus
    if (qty < maxQty) {
        btnPlus.prop('disabled', false);
    }

    if (qty === 0) {
        delete foodSelections[id];
    } else {
        foodSelections[id] = qty;
    }
    hitungTotalFood();
});

        function hitungTotalFood() {
            totalFood = 0;
            const selectedItems = [];

            $('.food-card').each(function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = parseInt($(this).data('price'));
                const qty = parseInt($(this).find('.quantity').text());

                if (qty > 0) {
                    totalFood += price * qty;
                    selectedItems.push({ id, name, qty, subtotal: price * qty });
                }
            });

            // simpan ke input hidden agar terkirim ke backend
            $('#food_items_input').val(JSON.stringify(selectedItems));

            // update total keseluruhan
            var harga = parseInt($('#harga').val().replace(/\./g, '') || 0);
            var tip = parseInt($('#tip').val().replace(/\./g, '') || 0);
            var total = harga + tip + totalFood;
            $('#total').val(formatNumber(total));
        }

        document.getElementById('uang').addEventListener('input', function() {
        calculateKembalian();
        });

        document.getElementById('total').addEventListener('change', function() {
            calculateKembalian();
        });

        function calculateKembalian() {
            const totalStr = document.getElementById('total').value.replace(/\./g, '');
            const uangStr = document.getElementById('uang').value.replace(/\./g, '');
            
            const total = parseInt(totalStr) || 0;
            const uang = parseInt(uangStr) || 0;
            
            const kembalian = Math.max(0, uang - total);
            const kembalianFormatted = kembalian.toLocaleString('id-ID');
            
            // Update kedua field kembalian
            document.getElementById('kembalian-pc').value = kembalianFormatted;
            document.getElementById('kembalian-mobile').value = kembalianFormatted;
        }

        // Spinner tombol pembayaran
        $('#btn-cash').click(function() {
            $(this).html('<i class="spinner-border spinner-border-sm"></i> Processing...')
                .prop('disabled', true);
            $('#payment_method').val('cash');
            $('#paymentForm').submit();
        });

        $('#btn-online').click(function() {
            $(this).html('<i class="spinner-border spinner-border-sm"></i> Processing...')
                .prop('disabled', true);
            $('#btn-cash').prop('disabled', true);
            $('#payment_method').val('midtrans');
            $('#paymentForm').submit();
        });
    });
</script>
</x-app-layout>
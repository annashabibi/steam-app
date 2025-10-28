<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Transaksi {{ $today->translatedFormat('l, j F Y') }}</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
        <div class="row">
            <div class="d-grid d-lg-block col-lg-5 col-xl-6 mb-4 mb-lg-0">
                {{-- Add Transaction --}}
                <a href="{{ route('transactions.create') }}" class="btn btn-primary py-2 px-3">
                    <i class="ti ti-plus me-2"></i> Tambah Transaksi
                </a>

                {{-- Modal Search --}}
                </div>
                <div class="col-lg-7 col-xl-6">
                    {{-- Search --}}
                    <form action="{{ route('transactions.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control py-2" value="{{ request('search') }}" placeholder="Search transaction ..." autocomplete="off">
                            <button class="btn btn-primary py-2" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <div class="bg-white rounded-2 shadow-sm pt-4 px-4 pb-3 mb-5">
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Karyawan</th>
                        <th class="text-center">Motor</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Tip</th>
                        <th class="text-center">F&B</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Pembayaran</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="text-center">{{ ++$i }}</td>
                            <td>{{ date('F j, Y', strtotime($transaction->date)) }}</td>
                            <td>{{ $transaction->karyawan->nama_karyawan }}</td>
                            <td>{{ $transaction->motor->nama_motor }}</td>
                            <td class="text-end">{{ 'Rp' . number_format($transaction->motor->harga, 0, '', '.') }}</td>
                            <td class="text-center">{{ number_format($transaction->tip, 0, '', '.') }}</td>
                            <td class="text-start">
                                @if (!empty($transaction->food_items))
                                    @php
                                        $foodItems = json_decode($transaction->food_items, true);
                                    @endphp

                                    @if (!empty($foodItems))
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($foodItems as $item)
                                                <li>
                                                    {{ $item['nama_produk'] }} (x{{ $item['qty'] }})<br>
                                                    Rp{{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            {{-- <td class="text-end">{{ 'Rp ' . number_format($transaction->motor->harga + $transaction->tip, 0, '', '.') }}</td> --}}
                            <td class="text-end">
                                {{ 'Rp' . number_format(
                                    ($transaction->motor->harga ?? 0) 
                                    + ($transaction->tip ?? 0) 
                                    + array_sum(array_map(fn($f) => ($f['harga'] ?? 0) * ($f['qty'] ?? 1), json_decode($transaction->food_items ?? '[]', true))),
                                    0, '', '.'
                                ) }}
                            </td>
                            @php
                            $status = $transaction->payment_status ?? 'paid';
                            $class = match($status) {
                                    'paid'    => 'bg-success',
                                    'pending' => 'bg-warning',
                                    'expired' => 'bg-danger',
                                     default  => 'bg-secondary',
                                };
                            @endphp
                            <td class="text-center"> 
                                <span class="badge {{ $class }}">{{ ucfirst($status) }}</span>
                            </td>
                            <td class="text-center">
                                @if ($transaction->payment_method === 'midtrans')
                                    {{ ucfirst($transaction->midtrans_payment_type ?? 'Midtrans') }}
                                @elseif ($transaction->payment_method === 'cash')
                                    Cash
                                @else
                                    {{ ucfirst($transaction->payment_method ?? '-') }}
                                @endif
                            </td>
                            {{-- <td class="text-center">{{ ucfirst($transaction->payment_status ?? 'Paid') }}</td> --}}
                            <td class="text-center">
                                {{-- Tombol Detail di tabel --}}
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $transaction->id }}" title="Lihat Detail">
                                    <i class="ti ti-list"></i>
                                </button>
                                {{-- Tombol Delete --}}
                                <button type="button" class="btn btn-danger btn-sm m-1" data-bs-toggle="modal" title="Hapus" data-bs-target="#modalDelete{{ $transaction->id }}">
                                    <i class="ti ti-trash"></i>
                                </button>

                                {{-- Modal Detail --}}
                                <div class="modal fade" id="modalDetail{{ $transaction->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $transaction->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            
                                <div class="modal-body p-0">
                                    <div class="card border-0 rounded-0" id="transaksiContent{{ $transaction->id }}">
                                        <div class="card-body text-center p-4">
                                            {{-- Logo --}}
                                            <h3 class="brand">Gue Gbyuur</h3>
                                            {{-- <img src="{{ asset('images/logo2.png') }}" class="img-fluid mb-3" alt="Logo" style="max-width: 120px;"> --}}
                                                    
                                            {{-- Header Info --}}
                                            <h6 class="text-muted mb-1">Steam Motor & Cuci Helm</h6>
                                            <p class="text-muted small mb-3">Jl. Abdul Ghani 2 Perumahan Palkostrad, Kalibaru, Kec. Cilodong, Kota Depok</p>
                                                    
                            {{-- Payment Status --}}
                            @php
                                $status = $transaction->payment_status ?? 'paid';
                                $statusClass = match($status) {
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    'expired' => 'danger',
                                    default => 'secondary',
                                };
                                $statusText = match($status) {
                                    'paid' => 'Pembayaran Lunas',
                                    'pending' => 'Menunggu Pembayaran',
                                    'expired' => 'Pembayaran Kadaluarsa',
                                    default => 'Status Tidak Diketahui',
                                };
                            @endphp
                            
                            <div class="alert alert-{{ $statusClass }} py-2 mb-4">
                                <i class="ti ti-check-circle me-1"></i>
                                <strong>{{ $statusText }}</strong>
                            </div>

                            {{-- QR Code --}}
                            @if ($transaction->payment_method === 'midtrans')
                                @if ($transaction->payment_status === 'pending' 
                                    && $transaction->expiry_time 
                                    && now()->lt(\Carbon\Carbon::parse($transaction->expiry_time)))
                                    <div class="text-center my-3">
                                        {!! QrCode::size(250)->generate($transaction->qr_string) !!}
                                        <p class="small text-muted mt-2">
                                            <div class="mt-2">
                                                <div class="countdown" data-expired="{{ $transaction->expiry_time }}" id="countdown{{ $transaction->id }}"></div>
                                            </div>
                                        </p>
                                    </div>
                                @endif
                            @endif

                        {{-- Transaction Details --}}
                        <div class="row g-0">
                            <div class="col-12">
                                <div class="border rounded-3 p-3 bg-light">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted">
                                                    <i class="ti ti-calendar me-2"></i>Tanggal
                                                </td>
                                                <td class="text-end">
                                                    {{ $transaction->date->translatedFormat('l, d F Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted">
                                                    <i class="fas fa-motorcycle me-2"></i>Motor
                                                </td>
                                                <td class="text-end">
                                                    {{ $transaction->motor->nama_motor ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted">
                                                    <i class="ti ti-coin me-2"></i>Harga
                                                </td>
                                                <td class="text-end">
                                                    Rp {{ number_format($transaction->motor->harga ?? 0, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted">
                                                    <i class="ti ti-cup me-2"></i>F&B
                                                </td>
                                                <td class="text-end">
                                                    @if (!empty($transaction->food_items))
                                                        @php
                                                            $foodItems = json_decode($transaction->food_items, true);
                                                        @endphp

                                                        @if (!empty($foodItems))
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach ($foodItems as $item)
                                                                    <li>
                                                                        {{ $item['nama_produk'] }} (x{{ $item['qty'] }})<br>
                                                                        Rp{{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-start fw-semibold text-muted">
                                                    <i class="ti ti-credit-card me-2"></i>Metode Pembayaran
                                                </td>
                                                <td class="text-end">
                                                    @if ($transaction->payment_method === 'midtrans')
                                                        {{ ucfirst($transaction->midtrans_payment_type ?? 'Midtrans') }}
                                                    @elseif ($transaction->payment_method === 'cash')
                                                        Cash
                                                    @else
                                                        {{ ucfirst($transaction->payment_method ?? '-') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr class="text-end">
                                                <td class="text-start fw-bold text-primary">
                                                    <i class="ti ti-calculator me-2"></i>Total
                                                </td>
                                                @php
                                                    $motorHarga = $transaction->motor->harga ?? 0;
                                                    $tip = $transaction->tip ?? 0;
                                                    $foodTotal = 0;

                                                    $foodItems = !empty($transaction->food_items) ? json_decode($transaction->food_items, true) : [];

                                                    if (!empty($foodItems) && is_array($foodItems)) {
                                                        foreach ($foodItems as $item) {
                                                            $foodTotal += ($item['harga'] ?? 0) * ($item['qty'] ?? 1);
                                                        }
                                                    }

                                                    $totalKeseluruhan = $motorHarga + $tip + $foodTotal;
                                                @endphp

                                                <td class="text-end fw-bold text-primary fs-5">
                                                    Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="mt-1 pt-2">
                            <p class="text-muted small mb-2">
                                <i class="ti ti-clock me-1">
                                    Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
                                </i>
                            </p>
                            <p class="text-muted small mb-0">Terima kasih atas kepercayaan Anda!</p>
                        </div>

                        </div>
                    </div>
                </div>

                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Tutup
                        </button>

                        <button type="button" class="btn btn-warning" onclick="printTransaksi({{ $transaction->id }})" @if(!in_array($transaction->payment_status, ['paid','settlement','capture'])) disabled @endif>
                            <i class="ti ti-printer me-1"></i> Cetak
                        </button>

                        @if($transaction->payment_method === 'midtrans' && !in_array($transaction->payment_status, ['paid','settlement','capture', 'expired']))
                            <button class="refresh-button checkStatusBtn" id="checkStatusBtn{{ $transaction->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" class="svg-icon">
                                    <path stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                        d="M3 12a9 9 0 1 0 3-6.708M3 3v4h4"/>
                                </svg>
                                <span class="label">Cek Status</span>
                            </button>
                        @endif
                    </div>

                    </div>
                </div>
            </div>
        </td>
    </tr>
                        {{-- Modal Delete One --}}
                        <div class="modal fade" id="modalDelete{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5"><i class="ti ti-trash me-2"></i> Delete Transaction</h1>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-2">Are you sure to delete this transaction?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary py-2 px-3" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger py-2 px-3">Yes, delete it!</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="d-flex justify-content-center align-items-center">
                                    <i class="ti ti-info-circle fs-5 me-2"></i>
                                    <span>No data available.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" class="text-end"><strong>Total Keseluruhan</strong></td>
                        <td class="text-end"><strong>{{ 'Rp' . number_format($totalTransaksi, 0, '', '.') }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="pagination-links">{{ $transactions->links() }}</div>
    </div>

    {{-- Script Print --}}
    <script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".countdown").forEach(function (el) {
        let expiredTime = new Date(el.dataset.expired).getTime();

        let timer = setInterval(function () {
            let now = new Date().getTime();
            let distance = expiredTime - now;

            if (distance <= 0) {
                clearInterval(timer);
                el.innerHTML = "⏰ Waktu pembayaran sudah habis";
                el.classList.add("countdown-expired");
                return;
            }

            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);
            el.innerHTML = `Sisa waktu: ${minutes}m ${seconds}s`;
            el.classList.add("countdown-red");
        }, 1000);
    });
});

// Check Status
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".checkStatusBtn").forEach(function(btn) {
        const transactionId = btn.id.replace('checkStatusBtn','');

        // manual click
        btn.addEventListener('click', function() {
            checkPaymentStatus(transactionId, true);
        });
    });
});

function checkPaymentStatus(transactionId, isManualCheck = false) {
    const checkBtn = document.getElementById("checkStatusBtn" + transactionId);
    const label = checkBtn.querySelector(".label");

    checkBtn.disabled = true;
    label.textContent = "Memeriksa...";

    fetch(`/payment/${transactionId}/status`)
        .then(response => response.json())
        .then(data => {
            if(['paid','settlement','capture'].includes(data.status)) {
                checkBtn.remove();
                const badge = document.querySelector(`#modalDetail${transactionId} .badge`);
                if(badge) {
                    badge.textContent = 'Paid';
                    badge.className = 'badge bg-success';
                }

                if(isManualCheck) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Lunas',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else if(data.status === 'pending') {
                if(isManualCheck) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Pembayaran Pending',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                checkBtn.disabled = false;
                label.textContent = "Cek Status";
            } else {
                if(isManualCheck) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Gagal',
                        text: `Status: ${data.status}`,
                        showConfirmButton: true
                    });
                }
                checkBtn.disabled = false;
                label.textContent = "Cek Status";
            }
        })
        .catch(err => {
            if(isManualCheck) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal Cek Status',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    showConfirmButton: true
                });
            }
            checkBtn.disabled = false;
            label.textContent = "Cek Status";
            console.error(err);
        });
}

// Global function print
function printTransaksi(id) {
    // Ambil konten yang akan dicetak
    let transaksiContent = document.getElementById('transaksiContent' + id);
    let printWindow = window.open('', '_blank');
    
    // Buat HTML untuk print dengan styling yang sesuai
    let printHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Struk</title>
            <style>
                @font-face {
                font-family: 'soopafre';
                src: url("data:font/truetype;charset=utf-8;base64,{{ base64_encode(file_get_contents(public_path('fonts/soopafre.ttf'))) }}") format('truetype');
                }

                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px;
                    line-height: 1.4;
                    color: #333;
                }

                @page {
                    margin: 5mm;
                }
                
                .card-body { 
                    text-align: center; 
                    max-width: 400px; 
                    margin: 0 auto;
                    border: 1px solid #ddd;
                    padding: 20px;
                    border-radius: 8px;
                }

                .brand {
                    font-family: 'Soopafre', sans-serif;
                    font-size: 28px;
                    font-weight: bold;
                    margin-bottom: 4px;
                }

                .alert { 
                    padding: 10px; 
                    border-radius: 4px; 
                    margin: 15px 0;
                    font-weight: bold;
                }
                .alert-success { 
                    background-color: #d4edda; 
                    color: #155724; 
                    border: 1px solid #c3e6cb;
                }
                .alert-warning { 
                    background-color: #fff3cd; 
                    color: #856404; 
                    border: 1px solid #ffeaa7;
                }
                .alert-danger { 
                    background-color: #f8d7da; 
                    color: #721c24; 
                    border: 1px solid #f5c6cb;
                }

                table { 
                    width: 100%; 
                    border-collapse: collapse;
                }
                td { 
                    padding: 8px 0; 
                    border-bottom: 1px solid #eee;
                }
                .border-top td {
                    border-top: 2px solid #333;
                    font-weight: bold;
                }
                .text-primary { color: #0d6efd; }
                .fw-bold { font-weight: bold; }
                .fs-5 { font-size: 1.25rem; }
                .text-muted { color: #6c757d; }
                .small { font-size: 0.875rem; }
            </style>
        </head>
        <body>
            ${transaksiContent.innerHTML}
        </body>
        </html>
    `;
    
    printWindow.document.write(printHTML);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 300);
}
</script>
</x-app-layout>
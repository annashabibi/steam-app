<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Transaksi ({{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }})</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
        <div class="row">
            <div class="d-grid d-lg-block col-lg-5 col-xl-6 mb-4 mb-lg-0">
                {{-- Add Transaction --}}
                <a href="{{ route('transactions.create') }}" class="btn btn-primary py-2 px-3">
                    <i class="ti ti-plus me-2"></i> Add Transaction
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
                        <th class="text-center">Date</th>
                        <th class="text-center">Karyawan</th>
                        <th class="text-center">Motor</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Tip</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Pembayaran</th>
                        <th class="text-center">Status</th>
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
                            <td class="text-end">{{ 'Rp ' . number_format($transaction->motor->harga, 0, '', '.') }}</td>
                            <td class="text-center">{{ number_format($transaction->tip, 0, '', '.') }}</td>
                            <td class="text-end">{{ 'Rp ' . number_format($transaction->motor->harga + $transaction->tip, 0, '', '.') }}</td>
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
                                <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-primary btn-sm m-1" title="Edit Transaksi">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm m-1" data-bs-toggle="modal" title="Hapus" data-bs-target="#modalDelete{{ $transaction->id }}">
                                    <i class="ti ti-trash"></i>
                                </button>
                                {{-- Tombol Detail di tabel --}}
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $transaction->id }}" title="Lihat Detail">
                                    <i class="ti ti-list"></i>
                                </button>

                                {{-- Modal Detail --}}
                                <div class="modal fade" id="modalDetail{{ $transaction->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $transaction->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            {{-- <div class="modal-header bg-primary text-white border-0">
                                                <h5 class="modal-title fw-bold" id="modalDetailLabel{{ $transaction->id }}">
                                                    <i class="ti ti-receipt me-2"></i>Detail Transaksi
                                                </h5> --}}
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            {{-- </div> --}}
            
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
                                        {!! QrCode::size(250)->generate($transaction->qr_url) !!}
                                        <p class="small text-muted mt-2">
                                            <div>
                                                <div id="countdown{{ $transaction->id }}"></div>
                                            </div>
                                        </p>
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            let countdownEl = document.getElementById("countdown{{ $transaction->id }}");
                                            let expiredTime = new Date("{{ $transaction->expiry_time }}").getTime();
                                            if (countdownEl) {
                                                let timer = setInterval(function () {
                                                    let now = new Date().getTime();
                                                    let distance = expiredTime - now;
                                                    if (distance <= 0) {
                                                        clearInterval(timer);
                                                        countdownEl.innerHTML = "⏰ Waktu pembayaran sudah habis";
                                                        countdownEl.classList.remove("countdown-red");
                                                        countdownEl.classList.add("countdown-expired");
                                                        let checkBtn = document.getElementById("checkStatusBtn{{ $transaction->id }}");
                                                        if (checkBtn) {
                                                            checkBtn.disabled = true;
                                                            checkBtn.innerHTML = "Waktu Habis";
                                                        }
                                                        return;
                                                    }
                                                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                    countdownEl.innerHTML = `⏱️ Sisa waktu: ${minutes}m ${seconds}s`;
                                                    countdownEl.classList.add("countdown-red");
                                                }, 1000);
                                            }
                                        });
                                    </script>
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
                                                <td class="text-end fw-bold text-primary fs-5">
                                                    Rp {{ number_format(($transaction->motor->harga ?? 0) + ($transaction->tip ?? 0), 0, ',', '.') }}
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
                            <button type="button" class="btn btn-warning" onclick="printTransaksi({{ $transaction->id }})"
                                @if(!in_array($transaction->payment_status, ['paid','settlement','capture'])) disabled @endif>
                                <i class="ti ti-printer me-1"></i> Cetak
                            </button>
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
                            <td colspan="10">
                                <div class="d-flex justify-content-center align-items-center">
                                    <i class="ti ti-info-circle fs-5 me-2"></i>
                                    <span>No data available.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    @if ($transactions->currentPage() == $transactions->lastPage())
                        <tr>
                            <td colspan="8" class="text-end"><strong>Total Keseluruhan</strong></td>
                            <td class="text-end"><strong>{{ 'Rp ' . number_format($totalKeseluruhan, 0, '', '.') }}</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>
        <div class="pagination-links">{{ $transactions->links() }}</div>
    </div>

    {{-- Script Print --}}
    <script>
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
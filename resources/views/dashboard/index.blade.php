<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Dashboard</x-page-title>

    {{-- Heroes --}}
    <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-3">
                <img src="{{ asset('images/logo.png') }}" class="img-fluid opacity-85" alt="images" loading="lazy">
            </div>
            <div class="col-lg-9">
                <h4 class="text-primary mb-2">
                    Welcome, <span class="fw-semibold">{{ Auth::user()->username }}</span>!
                </h4>
                <p class="lead-dashboard mb-4">Selamat bekerja, jangan lupa tetap teliti dan semangat ya!</p>
                <div class="d-grid gap-3 d-md-flex justify-content-md-start">
                    <a href="{{ route('transactions.index') }}" class="btn btn-primary py-2 px-4">
                        Lihat Transaksi <i class="ti ti-chevron-right align-middle ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

   {{-- Chart Pendapatan --}}
    <div class="row">
        {{-- Chart Pendapatan Pemilik --}}
        <div class="col-12">
            <div class="bg-white rounded-2 shadow-sm p-4 mb-5" id="income-chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    
                    {{-- Judul chart --}}
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="ti ti-chart-line fs-5 align-text-top me-1"></i>
                        Pendapatan Pemilik
                        @if($range == '7') (7 Hari Terakhir) 
                        @elseif($range == '30') (30 Hari Terakhir) 
                        @else (Bulanan) 
                        @endif
                    </h6>

                    {{-- Filter --}}
                    <div class="d-flex align-items-center">
                        {{-- Dropdown filter --}}
                        <form method="GET" action="{{ route('dashboard') }}">
                            <select name="range" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="7" {{ $range == '7' ? 'selected' : '' }}>7 Hari</option>
                                <option value="30" {{ $range == '30' ? 'selected' : '' }}>30 Hari</option>
                                <option value="monthly" {{ $range == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            </select>
                        </form>

                        {{-- Total pendapatan --}}
                        <div class="text-end ms-3">
                            <small class="text-muted">Total: </small>
                            <span class="fw-bold text-primary" id="totalDisplay">
                                Rp{{ number_format(array_sum($incomes->toArray()), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Canvas chart --}}
                <div style="height: 400px; position: relative;">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>
        </div>
    </div>



    <div class="row mb-3">
        {{-- menampilkan informasi jumlah data Helm --}}
        <div class="col-lg-6 col-xl-3">
            <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
                <div class="d-flex align-items-center justify-content-start">
                    <div class="me-4">
                        <i class="ti ti-helmet fs-1 bg-primary-2 text-white rounded-2 p-2"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Helm</p>
                        {{-- tampilkan data --}}
                        <h5 class="fw-bold mb-0">{{ $totalHelm }}</h5>
                    </div>
                </div>
            </div>
        </div>
        {{-- menampilkan informasi jumlah data Motor --}}
        <div class="col-lg-6 col-xl-3">
            <div class="bg-white rounded-2 shadow-sm p-4 p-lg-4-2 mb-4">
                <div class="d-flex align-items-center justify-content-start">
                    <div class="text-muted me-4">
                        <i class="fas fa-motorcycle fs-1 bg-success text-white rounded-2 p-1"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Motor</p>
                        {{-- tampilkan data --}}
                        <h5 class="fw-bold mb-0">{{ $totalMotor }}</h5>
                    </div>
                </div>
            </div>
        </div>
        {{-- menampilkan informasi jumlah data karyawan --}}
        <div class="col-lg-6 col-xl-3">
            <div class="bg-white rounded-2 shadow-sm p-4 p-lg-4-2 mb-4">
                <div class="d-flex align-items-center justify-content-start">
                    <div class="text-muted me-4">
                        <i class="ti ti-users fs-1 bg-warning text-white rounded-2 p-2"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Karyawan</p>
                        {{-- tampilkan data --}}
                        <h5 class="fw-bold mb-0">{{ $totalKaryawan }}</h5>
                    </div>
                </div>
            </div>
        </div>
        {{-- menampilkan informasi jumlah data Transaction --}}
        <div class="col-lg-6 col-xl-3">
            <div class="bg-white rounded-2 shadow-sm p-4 p-lg-4-2 mb-4">
                <div class="d-flex align-items-center justify-content-start">
                    <div class="text-muted me-4">
                        <i class="ti ti-credit-card fs-1 bg-info text-white rounded-2 p-2"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Transactions</p>
                        {{-- tampilkan data --}}
                        <h5 class="fw-bold mb-0">{{ $totalTransaction }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2 shadow-sm pt-4 px-4 pb-3 mb-5">
        {{-- judul --}}
        <h6 class="mb-0">
            <i class="ti ti-folder-star fs-5 align-text-top me-1"></i> 
            5 Best Washing Motors Today.
        </h6>

        <hr class="mb-4">

        {{-- tabel tampil data --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                    <th class="text-center">Image</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Total Washes</th>
                </thead>
                <tbody>
                @forelse ($motorTransactions as $transaction)
                    {{-- jika data ada, tampilkan data --}}
                    <tr class="text-center">
                        <td width="50" class="text-center">
                            <img src="{{ asset('/storage/motors/'.$transaction->motor->image) }}" class="img-thumbnail rounded-4" width="80" alt="Images">
                        </td>
                        <td width="200">{{ $transaction->motor->nama_motor }}</td>
                        <td width="100" class="text-end-center">{{ 'Rp ' . number_format($transaction->motor->harga, 0, '', '.') }}</td> 
                        {{-- <td width="80" class="text-center">{{ $transaction->total_washes }}</td> --}}
                        <td width="80" class="text-center">{{ $transaction->total_washes ?? 0 }}</td>
                    </tr>
                @empty
                    {{-- jika data tidak ada, tampilkan pesan data tidak tersedia --}}
                    <tr>
                        <td colspan="4">
                            <div class="d-flex justify-content-center align-items-center">
                                <i class="ti ti-info-circle fs-5 me-2"></i>
                                <div>No data available.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

{{-- Script ChartJS --}}
<script>
    const dates = {!! json_encode($dates) !!};
    const incomes = {!! json_encode($incomes) !!};
    const totals = {!! json_encode($totals) !!};

    const ctx = document.getElementById('incomeChart').getContext('2d');
    const incomeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Pendapatan Bersih Pemilik',
                data: incomes,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 4,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#5a67d8',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#667eea',
                    borderWidth: 1,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            const index = context.dataIndex;
                            const pendapatan = context.parsed.y.toLocaleString('id-ID');
                            const totalTrx = totals[index] ?? 0;
                            return [
                                'Pendapatan Bersih: Rp ' + pendapatan,
                                'Total Transaksi: ' + totalTrx + 'x'
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#718096',
                        font: { size: 12, weight: '500' }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#718096',
                        font: { size: 12, weight: '500' },
                        stepSize: 10000,
                        callback: function(value) {
                            return 'Rp' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Animasi saat load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            incomeChart.update();
        }, 500);
    });
</script>
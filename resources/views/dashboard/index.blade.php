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
                    <a href="#" class="btn btn-primary py-2 px-4">
                        Show Income <i class="ti ti-chevron-right align-middle ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

   {{-- Chart Pendapatan --}}
    <div class="row">
        {{-- Chart Pendapatan Pemilik (Motor) --}}
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

        {{-- Chart F&B --}}
        <div class="col-12 col-lg-8">
        <div class="bg-white rounded-2 shadow-sm p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0 d-flex align-items-center">
                    <i class="ti ti-chart-bar fs-5 align-text-top me-1"></i>
                    Pendapatan F&B
                    @if($range == '7') (7 Hari Terakhir) 
                    @elseif($range == '30') (30 Hari Terakhir) 
                    @else (Bulanan) 
                    @endif
                </h6>

                <div class="text-end">
                    <small class="text-muted">Total: </small>
                    <span class="fw-bold" style="color: #FF8C00;">
                        Rp{{ number_format($totalFnbRevenue, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div id="fnbChart" style="height: 350px; width: 100%;"></div>

            {{-- Summary Info --}}
            <div class="row mt-3 pt-3 border-top">
                <div class="col-6 text-center">
                    <small class="text-muted d-block">Total Transaksi: {{ $totalFnbTransactions }}</small>
                    {{-- <h5 class="fw-bold mb-0">{{ $totalFnbTransactions }}</h5> --}}
                </div>
                <div class="col-6 text-center">
                    <small class="text-muted d-block">Total Product Terjual: {{ $totalFnbItems }}</small>
                    {{-- <h5 class="fw-bold mb-0">{{ $totalFnbItems }}</h5> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Top 5 Products F&B --}}
    <div class="col-12 col-lg-4">
        <div class="menu-card">
            <div class="menu-bg-accent"></div>
            
            <div class="menu-header">
                <h6 class="menu-title">
                    Top 5 Product Teratas Hari Ini
                </h6>
            </div>

            @forelse($topProducts as $index => $product)
            
            <div class="menu-wrapper" style="animation-delay: {{ $index * 0.08 }}s;">
                <div class="menu-item">
                    
                    <div class="menu-rank {{ $index < 3 ? 'menu-rank-top' : 'menu-rank-default' }}">
                        {{ $index + 1 }}
                    </div>

                    <div class="row g-3 align-items-center">
                        
                        <div class="col-auto ms-4">
                            <div class="position-relative">
                                <div class="menu-image-wrapper">
                                    <img src="{{ $product['image'] }}" alt="{{ $product['nama_produk'] }}" class="menu-image">
                                </div>
                                
                                @if($index === 0)
                                <div class="menu-badge-position">
                                    <div class="menu-badge-star">
                                        <i class="ti ti-star-filled text-white menu-icon-star"></i>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-2">
                                <h6 class="menu-product-name">
                                    {{ $product['nama_produk'] }}
                                </h6>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="menu-badge-sold">
                                        <i class="ti ti-package menu-icon-cart"></i> {{ $product['qty'] }} terjual
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <div class="menu-revenue-row">
                                    <span class="menu-revenue-label">Pendapatan</span>
                                    <strong class="menu-revenue-value">
                                        Rp{{ number_format($product['revenue'], 0, ',', '.') }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="menu-empty">
                <div class="mb-3">
                    <div class="menu-empty-icon">
                        <i class="ti ti-package-off menu-icon-empty"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <span>No data available.</span>
                </div>
            </div>
            @endforelse
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
        {{-- menampilkan informasi jumlah data Transaksi --}}
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
                            <img src="{{ $motor->image }}" class="img-thumbnail rounded-4" width="80" alt="Images">
                        </td>
                        <td width="200">{{ $transaction->motor->nama_motor }}</td>
                        <td width="100" class="text-end-center">{{ 'Rp ' . number_format($transaction->motor->harga, 0, '', '.') }}</td> 
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
    // Data Motor Chart
    const dates = {!! json_encode($dates) !!};
    const incomes = {!! json_encode($incomes) !!};
    const totals = {!! json_encode($totals) !!};

    // Data F&B Chart
    const fnbIncomes = {!! json_encode($fnbIncomes) !!};
    const fnbTotals = {!! json_encode($fnbTotals) !!};

    // Motor Chart
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

    //  F&B Chart 
    anychart.onDocumentReady(function () {
        var data = [];
        for (var i = 0; i < dates.length; i++) {
            data.push({
                x: dates[i],
                value: fnbIncomes[i],
                items: fnbTotals[i]
            });
        }
        
        var chart = anychart.stick();
        chart.interactivity("by-x");
        var series = chart.stick(data);
        
        series.stroke('#FF8C00', 2);
        
        chart.title(false);
        
        var xAxis = chart.xAxis();
        xAxis.title("");
        xAxis.labels().fontColor('#718096');
        xAxis.labels().fontSize(11);
        
        var yAxis = chart.yAxis();
        yAxis.title("");
        yAxis.labels().fontColor('#718096');
        yAxis.labels().fontSize(11);
        yAxis.labels().format(function() {
            return 'Rp' + Math.round(this.value).toLocaleString('id-ID');
        });
        
        var yScale = chart.yScale();
        yScale.minimum(0);
        yScale.ticks().interval(5000);
        
        chart.tooltip().format(function() {
            var pendapatan = this.value.toLocaleString('id-ID');
            var items = this.getData('items') || 0;
            return 'Pendapatan: Rp ' + pendapatan + '\nProduct Terjual: ' + items + 'pcs';
        });
        
        chart.tooltip().titleFormat('{%x}');
        chart.tooltip().fontColor('#ffffff');
        chart.tooltip().background().fill('rgba(0, 0, 0, 0.8)');
        chart.tooltip().background().stroke('#FF8C00');
        
        chart.background().fill('#ffffff');
        chart.padding(10, 20, 10, 10);
    
        chart.container("fnbChart");
        chart.draw();
    });

    // Animasi saat load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            incomeChart.update();
        }, 500);
    });
</script>
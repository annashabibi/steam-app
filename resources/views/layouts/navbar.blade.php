    {{-- Navbar Top --}}
    <nav class="navbar navbar-top fixed-top bg-primary text-white">
    <div class="container d-flex align-items-center justify-content-between">
        {{-- Brand --}}
        <a class="navbar-brand text-white" href="{{ route('dashboard') }}">
            <span class=" brand-logo-text fs-4">Steam Gue Gbyuur</span>
        </a>

        {{-- Tombol Hamburger --}}
        <label class="burger d-lg-none" for="burger">
        <input type="checkbox" id="burger">
        <span></span>
        <span></span>
        <span></span>
        </label>
    </div>
</nav>


    {{-- Navbar Menu --}}
    <nav class="navbar navbar-menu fixed-top navbar-expand-lg bg-light shadow-lg-sm">
        <div class="container">
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <x-navbar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            <i class="ti ti-home align-text-top me-1"></i> Dashboard
                        </x-navbar-link>
                    </li>
                    <li class="nav-item">
                        <x-navbar-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.*')">
                            <i class="ti ti-category align-text-top me-1"></i> Categories
                        </x-navbar-link>
                    </li>
                    <li class="nav-item">
                        <x-navbar-link href="{{ route('motors.index') }}" :active="request()->routeIs('motors.*')">
                            <i class="fas fa-motorcycle align-text-top me-1"></i> Motor
                        </x-navbar-link>
                    </li>
                    <li class="nav-item">
                        <x-navbar-link href="{{ route('helms.index') }}" :active="request()->routeIs('helms.*')">
                            <i class="ti ti-helmet align-text-top me-1"></i> Helm
                        </x-navbar-link>
                    </li>
                    <li class="nav-item">
                        <x-navbar-link href="{{ route('karyawans.index') }}" :active="request()->routeIs('karyawans.*')">
                            <i class="ti ti-users align-text-top me-1"></i> Karyawan
                        </x-navbar-link>
                    </li>
                    <li class="nav-item">
                        <x-navbar-link href="{{ route('food.index') }}" :active="request()->routeIs('food.*')">
                            <i class="ti ti-cup align-text-top me-1"></i> F&B
                        </x-navbar-link>
                    </li>
                    <li class="nav-item">
                        <x-navbar-link href="{{ route('transactions.index') }}" :active="request()->routeIs('transactions.*')">
                            <i class="ti ti-credit-card align-text-top me-1"></i> Transaksi
                        </x-navbar-link>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('gaji.*') || request()->routeIs('pengeluaran.*') ? 'active' : '' }}" href="#" id="keuanganDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-wallet align-text-top me-1"></i> Keuangan
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="keuanganDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('gaji.*') ? 'active' : '' }}" 
                                href="{{ route('gaji.index') }}">
                                    <i class="ti ti-coin align-text-top me-1"></i> Gaji
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}" 
                                href="{{ route('pengeluaran.index') }}">
                                    <i class="ti ti-receipt align-text-top me-1"></i> Pengeluaran
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <x-navbar-link href="{{ route('report.index')}}" :active="request()->routeIs('report.*')">
                            <i class="ti ti-file-text align-text-top me-1"></i> Laporan
                        </x-navbar-link>
                    </li>
                </ul>

                    {{-- Button Logout --}}
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-user align-text-top me-1"></i> {{ Auth::user()->username }}
                            </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="ti ti-logout me-1"></i> Logout
                                            </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </nav>
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    {{-- Required meta tags --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Guegbyuur"/>
    <meta property="og:description" content="Application for Cashier"/>
    <meta property="og:url" content="https://guegbyuur.up.railway.app"/>
    <meta property="og:image" content="https://guegbyuur.up.railway.app/images/preview.png"/>
    <meta property="og:type" content="website"/>
    <meta name="author" content="Annas Habibi">
    <meta name="description" content="Aplikasi POS Steam Gue Gbyuur">
    <meta name="author" content="Annas Habibi">

    {{-- Title --}}
    <title>Aplikasi POS Steam Gue Gbyuur Laravel 11</title>

    {{-- Favicon icon --}}
    {{-- <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon"> --}}
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{-- Tabler Icons CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    {{-- Flatpickr CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- logo --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    {{-- Template CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- <link rel="stylesheet" href="{{ secure_asset('css/app.css') }}"> --}}


    {{-- testing --}}
    {{-- <link rel="stylesheet" href="//802b-103-121-17-123.ngrok-free.app/css/app.css"> --}}

    {{-- jQuery Core --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body class="d-flex flex-column h-100">
    {{-- Header --}}
    <header>
        {{-- Navbar --}}
        @include('layouts.navbar')
    </header>

    {{-- Main Content --}}
    <main class="flex-shrink-0">
        <div class="container">
            <div class="page-content">
                {{-- menampilkan konten sesuai halaman yang dipilih --}}
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Footer --}}
    {{-- <footer class="footer bg-white shadow mt-auto py-3">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-left">
                <div class="copyright text-center mb-2 mb-md-0">
                    &copy; 2024 - <a href="https://www.instagram.com/annashabibi_" target="_blank" class="fw-semibold">Gue Gbyuur</a>. All rights reserved.
                </div>
            </div>
        </div>
    </footer> --}}

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    {{-- Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Flatpickr JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    {{-- Flatpickr Indonesia --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <!-- Select2 JS -->
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- jQuery Mask Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js" integrity="sha256-Kg2zTcFO9LXOc7IwcBx1YeUBJmekycsnTsq2RuFHSZU=" crossorigin="anonymous"></script>
    {{-- Sweetalert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Bootstrap Notify --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-notify@3.1.3/bootstrap-notify.min.js"></script>

    {{-- Custom Scripts --}}
    <script src="{{ asset('js/plugins.js') }}"></script>
    {{-- <script src="https://802b-103-121-17-123.ngrok-free.app/js/plugins.js"></script> --}}
    <script src="{{ asset('js/image-preview.js') }}"></script>
    

    <script>
        document.getElementById('burger').addEventListener('change', function() {
    const navbar = document.getElementById('navbarMenu');
    if(this.checked){
        new bootstrap.Collapse(navbar, { toggle: true });
    } else {
        bootstrap.Collapse.getInstance(navbar)?.hide();
    }
});
        // menampilkan pesan dengan sweetalert
        @if (session('success'))
            Swal.fire({
                icon: "success",
                title: "Success!",
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @elseif (session('error'))
            Swal.fire({
                icon: "error",
                title: "Failed!",
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 2000
            });
            @elseif (session('login'))
            Swal.fire({
                title: 'Selamat Datang!',
                text: '{{ session('login') }}',
                imageUrl: '/images/login.png',
                imageWidth: 200,
                imageHeight: 200,
                imageAlt: 'Login',
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    </script>
</body>
</html>
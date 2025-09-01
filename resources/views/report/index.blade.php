<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Laporan</x-page-title>

    <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
        {{-- info form --}}
        <div class="alert alert-primary mb-5" role="alert">
            <i class="ti ti-file-search fs-5 me-2"></i> Pilih type laporan.
        </div>
        {{-- form filter data --}}
        <form action="{{ route('report.filter') }}" method="GET" class="needs-validation" novalidate>
        <div class="row">
        {{-- Select Type --}}
        <div class="col-lg-4 col-xl-3 mb-4 mb-lg-0">
            <label class="form-label">Laporan Type <span class="text-danger">*</span></label>
            <select name="report_type" class="form-control @error('report_type') is-invalid @enderror">
                <option selected disabled value="">- Select Type Laporan -</option>
                <option value="transaction" {{ old('report_type', request('report_type')) == 'transaction' ? 'selected' : '' }}>Transaksi</option>
                <option value="pendapatan" {{ old('report_type', request('report_type')) == 'pendapatan' ? 'selected' : '' }}>Pendapatan</option>
            </select>
            @error('report_type')
                <div class="alert alert-danger mt-2">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Input Tanggal --}}
        <div class="col-lg-4 col-xl-3 mb-4 mb-lg-0">
            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control datepicker @error('date') is-invalid @enderror" value="{{ request('date') ?? now()->toDateString() }}">
            @error('date')
                <div class="alert alert-danger mt-2">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="pt-4 pb-2 mt-5 border-top">
        <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
            <button type="submit" class="btn btn-primary py-2 px-4">
                Show <i class="ti ti-chevron-right align-middle ms-2"></i>
            </button>
        </div>
    </div>
</form>

    </div>
    @if (request('report_type'))
        @if (request('report_type') == 'transaction')
            @include('report.transaction')
        @elseif (request('report_type') == 'pendapatan')
            @include('report.pendapatan')
        @endif
    @endif
</x-app-layout>

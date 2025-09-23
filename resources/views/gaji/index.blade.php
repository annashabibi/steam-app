<x-app-layout>
    {{-- Page Title --}}
    <x-page-title>Gaji</x-page-title>

    {{-- Form Pilih Karyawan --}}
    <div class="bg-white rounded-2 shadow-sm p-4 mb-4">
        <div class="alert alert-primary mb-4" role="alert">
            <i class="ti ti-user fs-5 me-2"></i> Pilih Karyawan.
        </div>

        <form method="GET" action="{{ route('gaji.filter') }}">
            <div class="row">
                <div class="col-lg-4 col-xl-3 mb-3">
                    <label for="karyawan" class="form-label">Nama Karyawan <span class="text-danger">*</span></label>
                    <select name="karyawan" id="karyawan" class="form-select select2-single @error('karyawan') is-invalid @enderror">
                        <option selected disabled value="">- Pilih Karyawan -</option>
                        @foreach ($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" {{ isset($selected) && $selected->id == $karyawan->id ? 'selected' : '' }}>
                                {{ $karyawan->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                    @error('karyawan')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="pt-4 pb-2 mt-5 border-top">
                    <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
                        <button type="submit" class="btn btn-primary py-2 px-4">
                            Show <i class="ti ti-chevron-right align-middle ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Gaji (Tampilan Terpisah) --}}
    @if(isset($dataPerHari))
    <div class="bg-light rounded-2 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="alert alert-success mb-0 flex-grow-1 me-3" role="alert">
            <i class="ti ti-user fs-5 me-2"></i> Menampilkan gaji untuk <strong>{{ $selected->nama_karyawan }}</strong>
        </div>

        <a href="{{ route('gaji.print', $selected->id) }}" target="_blank" class="btn btn-warning py-2 px-3">
            <i class="ti ti-printer me-2"></i> Print
        </a>
    </div>
</div>


        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                    <tr class="text-center">
                        <th>Tanggal</th>
                        <th>Pendapatan</th>
                        <th>Pengeluaran</th>
                        <th>Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataPerHari as $data)
                        <tr class="text-center">
                            <td>{{ \Carbon\Carbon::parse($data['date'])->translatedFormat('j F Y') }}</td>
                            <td>Rp{{ number_format($data['pendapatan'], 0, ',', '.') }}</td>
                            {{-- <td class="text-danger fw-bold">Rp-{{ number_format($data['pengeluaran'], 0, ',', '.') }}</td> --}}
                            <td class="text-danger fw-bold">
                            @if($data['pengeluaran'] > 0)
                                Rp-{{ number_format($data['pengeluaran'],  0, ',', '.') }}
                            @else
                                Rp0
                            @endif
                            </td>
                            <td class="text-success fw-bold">Rp{{ number_format($data['pendapatan_bersih'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $totalKeseluruhan = collect($dataPerHari)->sum('pendapatan_bersih');
                        $class = $totalKeseluruhan < 0 ? 'text-danger' : 'text-success';
                    @endphp
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">Total Keseluruhan:</td>
                        <td class="text-center {{ $class }}">
                            Rp{{ $totalKeseluruhan < 0 ? '-' : '' }}{{ number_format(abs($totalKeseluruhan), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
</x-app-layout>

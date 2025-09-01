<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // jumlah data yang ditampilkan per paginasi halaman
        $pagination = 10;

        if ($request->search) {
            // menampilkan pencarian data
            $karyawans = Karyawan::select('id', 'nama_karyawan', 'no_telepon', 'aktif')
                ->whereAny(['nama_karyawan', 'no_telepon', 'aktif'], 'LIKE', '%' . $request->search . '%')
                ->paginate($pagination)
                ->withQueryString();
        } else {
            // menampilkan semua data
            $karyawans = Karyawan::select('id', 'nama_karyawan', 'no_telepon', 'aktif')
                ->latest()
                ->paginate($pagination);
        }

        // tampilkan data ke view
        return view('karyawans.index', compact('karyawans'))->with('i', ($request->input('page', 1) - 1) * $pagination);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // tampilkan form add data
        return view('karyawans.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama_karyawan'    => 'required',
            'no_telepon'   => 'required|max:13|unique:karyawans'
        ]);

        // create data
        Karyawan::create([
            'nama_karyawan' => $request->nama_karyawan,
            'no_telepon'    => $request->no_telepon
        ]);

        // redirect ke halaman index dan tampilkan pesan berhasil simpan data
        return redirect()->route('karyawans.index')->with(['success' => 'The new Karyawan has been saved.']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // get data by ID
        $karyawan = Karyawan::findOrFail($id);

        // tampilkan form edit data
        return view('karyawans.edit', compact('karyawan'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama_karyawan' => 'required',
            'no_telepon'    => 'required|max:13|unique:karyawans,no_telepon,' . $id
        ]);

        // get data by ID
        $karyawan = Karyawan::findOrFail($id);

        // update data
        $karyawan->update([
            'nama_karyawan' => $request->nama_karyawan,
            'no_telepon'    => $request->no_telepon
        ]);

        // redirect ke halaman index dan tampilkan pesan berhasil ubah data
        return redirect()->route('karyawans.index')->with(['success' => 'The Karyawan has been updated.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function status($id): RedirectResponse
    {
        $karyawan = Karyawan::findOrFail($id);

        $karyawan->update([
            'aktif' => !$karyawan->aktif
        ]);

        $pesan = $karyawan->aktif ? 'Karyawan berhasil diaktifkan kembali.' : 'Karyawan berhasil dinonaktifkan.';

        return redirect()->route('karyawans.index')->with(['success' => $pesan]);
    }

}

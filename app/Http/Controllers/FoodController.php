<?php

namespace App\Http\Controllers;
use App\Models\Food;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\View\View;

class FoodController extends Controller
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
            $foods = Food::select('id', 'nama_produk', 'category', 'image', 'qty', 'harga')
                ->whereAny(['nama_produk', 'category', 'harga'], 'LIKE', '%' . $request->search . '%')
                ->paginate($pagination)
                ->withQueryString();
        } else {
            // menampilkan semua data
            $foods = Food::select('id', 'nama_produk', 'category', 'image', 'qty', 'harga')
                ->latest()
                ->paginate($pagination);
        }

        // tampilkan data ke view
        return view('food.index', compact('foods'))->with('i', ($request->input('page', 1) - 1) * $pagination);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // tampilkan form add data
        return view('food.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama_produk' => 'required|unique:food,nama_produk',
            'category'    => 'required|in:makanan,minuman',
            'harga'       => 'required|numeric|min:0',
            'qty'         => 'nullable|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,jpg,png|max:1024'
        ]);

        // upload gambar ke Cloudinary jika ada
        $uploadedFileUrl = null;
        if ($request->hasFile('image')) {
            $uploadedFileUrl = Cloudinary::upload(
                $request->file('image')->getRealPath(),
                [
                    'folder' => 'food',
                    'overwrite' => true,
                    'resource_type' => 'image'
                ]
            )->getSecurePath();
        }

        // simpan data ke database
        Food::create([
            'nama_produk' => $request->nama_produk,
            'category'    => $request->category,
            'harga'       => str_replace('.', '', $request->harga), // hapus pemisah ribuan
            'qty'         => $request->qty ?? 0,
            'image'       => $uploadedFileUrl,
        ]);

        // redirect ke halaman index dengan pesan sukses
        return redirect()->route('food.index')->with(['success' => 'Produk baru berhasil ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         // get data by ID
        $food = Food::findOrFail($id);

        // tampilkan form detail data
        return view('food.show', compact('food'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // get data by ID
        $food = Food::findOrFail($id);

        // tampilkan form edit data
        return view('food.edit', compact('food'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // validasi form
        $request->validate([
            'nama_produk' => 'required|unique:food,nama_produk,' . $id,
            'category'    => 'required|in:makanan,minuman',
            'harga'       => 'required|numeric|min:0',
            'qty'         => 'nullable|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,jpg,png|max:1024'
        ]);

        // cari data yang akan diupdate
        $food = Food::findOrFail($id);

        // upload gambar ke Cloudinary jika ada
        $uploadedFileUrl = $food->image; // gunakan gambar lama jika tidak ada upload baru
        if ($request->hasFile('image')) {
            $uploadedFileUrl = Cloudinary::upload(
                $request->file('image')->getRealPath(),
                [
                    'folder' => 'food',
                    'overwrite' => true,
                    'resource_type' => 'image'
                ]
            )->getSecurePath();
        }

        // update data ke database
        $food->update([
            'nama_produk' => $request->nama_produk,
            'category'    => $request->category,
            'harga'       => str_replace('.', '', $request->harga), // hapus pemisah ribuan
            'qty'         => $request->qty ?? 0,
            'image'       => $uploadedFileUrl,
        ]);

        // redirect ke halaman index dengan pesan sukses
        return redirect()->route('food.index')->with(['success' => 'Produk berhasil diupdate.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

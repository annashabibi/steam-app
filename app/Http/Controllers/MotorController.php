<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Motor;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
class MotorController extends Controller
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
            $motors = Motor::select('id', 'category_id', 'nama_motor', 'harga', 'image')->with('category:id,type_motor')
                ->whereAny(['nama_motor', 'harga'], 'LIKE', '%' . $request->search . '%')
                ->paginate($pagination)
                ->withQueryString();
        } else {
            // menampilkan semua data
            $motors = Motor::select('id', 'category_id', 'nama_motor', 'harga', 'image')->with('category:id,type_motor')
                ->latest()
                ->paginate($pagination);
        }

        // tampilkan data ke view
        return view('motors.index', compact('motors'))->with('i', ($request->input('page', 1) - 1) * $pagination);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // get data kategori
        $categories = Category::get(['id', 'type_motor']);

        // tampilkan form add data
        return view('motors.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'category'   => 'required|exists:categories,id',
        'nama_motor' => 'required',
        'harga'      => 'required',
        'image'      => 'required|image|mimes:jpeg,jpg,png|max:1024'
    ]);

    try {
        $file = $request->file('image');
        if (!$file) {
            throw new \Exception('File image tidak diterima.');
        }

        // Upload ke Cloudinary
        $uploadedFileUrl = Cloudinary::upload($file->getRealPath())->getSecurePath();

        // Simpan ke database
        Motor::create([
            'category_id' => $request->category,
            'nama_motor'  => $request->nama_motor,
            'harga'       => str_replace('.', '', $request->harga),
            'image'       => $uploadedFileUrl
        ]);

        return redirect()->route('motors.index')
                         ->with(['success' => 'Motor berhasil ditambahkan.']);

    } catch (\Exception $e) {
        // Menangkap semua error dan menampilkannya di form
        return back()->withErrors(['image' => 'Terjadi error: '.$e->getMessage()]);
    }
}



    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        // get data by ID
        $motor = Motor::findOrFail($id);

        // tampilkan form detail data
        return view('motors.show', compact('motor'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // get data product by ID
        $motor = Motor::findOrFail($id);
        // get data kategori
        $categories = Category::get(['id', 'type_motor']);

        // tampilkan form edit data
        return view('motors.edit', compact('motor', 'categories'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // validasi form
        $request->validate([
            'category'    => 'required|exists:categories,id',
            'nama_motor'  => 'required',
            'harga'       => 'required',
            'image'       => 'image|mimes: jpeg,jpg,png|max: 1024'
        ]);

        // get data by ID
        $motor = Motor::findOrFail($id);

        // cek jika image diubah
        if ($request->hasFile('image')) {
            // upload image baru
            $image = $request->file('image');
            $image->storeAs('public/motors', $image->hashName());

            // delete image lama
            Storage::delete('public/motors/' . $motor->image);

            // update data
            $motor->update([
                'category_id' => $request->category,
                'nama_motor'  => $request->nama_motor,
                'harga'       => str_replace('.', '', $request->price),
                'image'       => $image->hashName()
            ]);
        }
        // jika image tidak diubah
        else {
            // update data
            $motor->update([
                'category_id' => $request->category,
                'nama_motor'  => $request->nama_motor,
                'harga'       => str_replace('.', '', $request->harga)
            ]);
        }

        // redirect ke halaman index dan tampilkan pesan berhasil ubah data
        return redirect()->route('motors.index')->with(['success' => 'The product has been updated.']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        // get data by ID
        $motor = Motor::findOrFail($id);

        // delete image
        Storage::delete('public/motors/' . $motor->image);

        // delete data
        $motor->delete();

        // redirect ke halaman index dan tampilkan pesan berhasil hapus data
        return redirect()->route('motors.index')->with(['success' => 'The motor has been deleted!']);
    }
}

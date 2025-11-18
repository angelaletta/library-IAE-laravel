<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{

    public function index()
    {
        $books = Book::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar data buku',
            'data'    => $books
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul'     => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit'  => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900',
            'jumlah_halaman' => 'required|integer',
            'kategori'  => 'required|string',
            'isbn'      => 'required|string|unique:books',
            'status'    => 'required|in:Tersedia,Dipinjam',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal!',
                'data'    => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        $book = Book::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan!',
            'data'    => $book
        ], 201); // 201 Created
    }

    public function show(string $id)
    {
        $book = Book::find($id);

        if ($book) {
            return response()->json([
                'success' => true,
                'message' => 'Detail data buku',
                'data'    => $book
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Buku tidak ditemukan!',
        ], 404); // 404 Not Found
    }

    public function update(Request $request, string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan!',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul'     => 'string|max:255',
            'pengarang' => 'string|max:255',
            'penerbit'  => 'string|max:255',
            'tahun_terbit' => 'integer|min:1900',
            'jumlah_halaman' => 'integer',
            'kategori'  => 'string',
            'isbn'      => 'string|unique:books,isbn,' . $id,
            'status'    => 'in:Tersedia,Dipinjam',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal!',
                'data'    => $validator->errors()
            ], 422);
        }

        $book->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diupdate!',
            'data'    => $book
        ], 200);
    }

    public function destroy(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan!',
            ], 404);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus!',
        ], 200); // Atau 204 No Content
    }
}

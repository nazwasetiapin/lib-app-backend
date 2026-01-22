<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // GET /api/books
    public function index()
    {
        return response()->json([
            'data' => Book::latest()->paginate(10)
        ]);
    }

    // GET /api/books/{id}
    public function show(Book $book)
    {
        return response()->json([
            'data' => $book
        ]);
    }

    // POST /api/books (ADMIN)
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'title'  => 'required|string',
            'author' => 'required|string',
            'year'   => 'required|integer',
        ]);

        $book = Book::create($data);

        return response()->json([
            'message' => 'Book created',
            'data' => $book
        ], 201);
    }

    // PUT /api/books/{id} (ADMIN)
    public function update(Request $request, Book $book)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'title'  => 'required|string',
            'author' => 'required|string',
            'year'   => 'required|integer',
        ]);

        $book->update($data);

        return response()->json([
            'message' => 'Book updated',
            'data' => $book
        ]);
    }

    // DELETE /api/books/{id} (ADMIN)
    public function destroy(Book $book)
    {
        $this->ensureAdmin();

        $book->delete();

        return response()->json([
            'message' => 'Book deleted'
        ]);
    }

    // 🔐 Admin check
    private function ensureAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Forbidden');
        }
    }
}
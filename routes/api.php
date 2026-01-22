<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Book;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Email atau password salah'
        ], 401);
    }

    return response()->json([
    'token' => $user->createToken('auth-token')->plainTextToken,
    'user'  => [
        'id'    => $user->id,
        'name'  => $user->name,
        'email' => $user->email,
        'role'  => $user->role, 
    ]
]);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});


/*
|--------------------------------------------------------------------------
| BOOKS (ADMIN)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // GET books (all users)
    Route::get('/books', function () {
        return Book::paginate(10);
    });

    // CREATE book (admin only)
    Route::post('/books', function (Request $request) {

        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
         $request->validate([
            'title'  => 'required',
            'author' => 'required',
            'year'   => 'nullable|integer',
            'stock'  => 'required|integer'
        ]);

        return Book::create($request->all());
    });

     // UPDATE book
    Route::put('/books/{id}', function (Request $request, $id) {

        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $book = Book::findOrFail($id);
        $book->update($request->all());

        return $book;
    });

    
    // DELETE book
    Route::delete('/books/{id}', function ($id) {

        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        Book::findOrFail($id)->delete();

        return response()->json(['message' => 'Book deleted']);
    });


});
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::orderBy('created_at', 'DESC');

        if (!empty($request->search)) {
            $books->where('title', 'like', '%' . $request->search . '%');
        }

        $books = $books->paginate(8);

        return view('home', [
            'books' => $books
        ]);
    }
}

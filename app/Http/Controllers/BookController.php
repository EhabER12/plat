<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of published books.
     */
    public function index()
    {
        $books = Book::where('is_published', true)
            ->latest()
            ->paginate(12);
        
        return view('books.index', compact('books'));
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        // Check if the book is published
        if (!$book->is_published) {
            abort(404);
        }

        // Get related books (same author or language)
        $relatedBooks = Book::where('is_published', true)
            ->where('id', '!=', $book->id)
            ->where(function ($query) use ($book) {
                $query->where('author', $book->author)
                    ->orWhere('language', $book->language);
            })
            ->limit(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    /**
     * Display the book cover image directly.
     */
    public function showCover(Book $book)
    {
        // Check if the book has a cover image
        if (!$book->cover_image) {
            abort(404);
        }
        
        // Check if the path is in the newest format with instructor ID
        if (str_contains($book->cover_image, "instructors/")) {
            $path = storage_path('app/public/' . $book->cover_image);
            
            if (!file_exists($path)) {
                abort(404);
            }
        }
        // Check if the path is in the older format with only book ID
        else if (str_contains($book->cover_image, "/{$book->id}/cover/")) {
            $path = storage_path('app/public/books/' . $book->cover_image);
            
            if (!file_exists($path)) {
                abort(404);
            }
        } 
        // Very old format
        else {
            $path = storage_path('app/public/books/covers/' . $book->cover_image);
            
            if (!file_exists($path)) {
                // Try public path as fallback
                $path = public_path('storage/books/covers/' . $book->cover_image);
                
                if (!file_exists($path)) {
                    abort(404);
                }
            }
        }

        // Use file response with appropriate headers
        return response()->file($path, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Display the book PDF file directly.
     */
    public function showPdf(Book $book)
    {
        // Check if the book is published or if the user has permission
        if (!$book->is_published && (!Auth::check() || Auth::id() != $book->user_id)) {
            abort(404);
        }

        // Check if user is authenticated (required for viewing PDFs)
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view PDF files.');
        }

        // Check if the book has a PDF file
        if (!$book->pdf_file) {
            abort(404);
        }

        // Check if the path is in the newest format with instructor ID
        if (str_contains($book->pdf_file, "instructors/")) {
            $path = storage_path('app/public/' . $book->pdf_file);
            
            if (!file_exists($path)) {
                abort(404);
            }
        }
        // Check if the path is in the older format with only book ID
        else if (str_contains($book->pdf_file, "/{$book->id}/pdf/")) {
            $path = storage_path('app/public/books/' . $book->pdf_file);
            
            if (!file_exists($path)) {
                abort(404);
            }
        }
        // Very old format
        else {
            $path = storage_path('app/public/books/pdf/' . $book->pdf_file);
            
            if (!file_exists($path)) {
                // Try public path as fallback
                $path = public_path('storage/books/pdf/' . $book->pdf_file);
                
                if (!file_exists($path)) {
                    abort(404);
                }
            }
        }

        // Use file response with appropriate headers instead of reading the file content
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $book->title . '.pdf"',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Download the book PDF file.
     */
    public function download(Book $book)
    {
        // Check if the book is published
        if (!$book->is_published) {
            abort(404);
        }

        // Check if the book has a PDF file
        if (!$book->pdf_file) {
            return back()->with('error', 'No PDF file available for this book.');
        }

        // Check if the path is in the newest format with instructor ID
        if (str_contains($book->pdf_file, "instructors/")) {
            $path = storage_path('app/public/' . $book->pdf_file);
            
            if (!file_exists($path)) {
                return back()->with('error', 'PDF file not found.');
            }
        }
        // Check if the path is in the older format with only book ID
        else if (str_contains($book->pdf_file, "/{$book->id}/pdf/")) {
            $path = storage_path('app/public/books/' . $book->pdf_file);
            
            if (!file_exists($path)) {
                return back()->with('error', 'PDF file not found.');
            }
        }
        // Very old format
        else {
            // Try storage path first (old format)
            $path = storage_path('app/public/books/pdf/' . $book->pdf_file);
            
            if (!file_exists($path)) {
                // Try public path as fallback
                $path = public_path('storage/books/pdf/' . $book->pdf_file);
                
                if (!file_exists($path)) {
                    return back()->with('error', 'PDF file not found.');
                }
            }
        }

        $filename = $book->title . '.pdf';
        
        // Return as a downloadable file
        return response()->download($path, $filename);
    }
}

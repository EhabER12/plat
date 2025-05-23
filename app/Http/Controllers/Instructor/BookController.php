<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * Display a listing of the instructor's books.
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        // Get filter and sort parameters
        $status = $request->get('status');
        $search = $request->get('search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        // Base query
        $query = $user->books();

        // Apply filters
        if ($status === 'published') {
            $query->where('is_published', true);
        } elseif ($status === 'draft') {
            $query->where('is_published', false);
        }

        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $allowedSortFields = ['title', 'price', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSortFields)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest(); // Default sort
        }

        // Get paginated results
        $books = $query->paginate(10);

        // Get counts for filters
        $totalBooks = $user->books()->count();
        $publishedBooks = $user->books()->where('is_published', true)->count();
        $draftBooks = $user->books()->where('is_published', false)->count();

        return view('instructor.books.index', compact(
            'books',
            'totalBooks',
            'publishedBooks',
            'draftBooks',
            'status',
            'search',
            'sort',
            'direction'
        ));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        return view('instructor.books.create');
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'author' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_file' => 'required|mimes:pdf|max:10240', // 10MB max
        ]);

        // Create book first to get ID
        $user = User::find(Auth::id());
        $book = $user->books()->create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'author' => $request->author,
            'pages' => $request->pages,
            'language' => $request->language,
            'is_published' => $request->has('is_published'),
        ]);

        // Create a unique folder structure that includes both instructor_id and book_id
        $instructorId = Auth::id();
        $bookFolderPath = "public/instructors/{$instructorId}/books/{$book->id}";
        Storage::makeDirectory($bookFolderPath);
        Storage::makeDirectory($bookFolderPath . '/pdf');
        Storage::makeDirectory($bookFolderPath . '/cover');

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            try {
                $image = $request->file('cover_image');

                // Verify that the file is valid
                if (!$image->isValid()) {
                    Log::error('Cover Image Upload - Invalid file', [
                        'instructor_id' => $instructorId,
                        'book_id' => $book->id
                    ]);
                    return redirect()->back()->withErrors(['cover_image' => 'Invalid image file'])->withInput();
                }

                // Create a debug log to track the upload
                Log::info('Cover Image Upload - Starting upload process', [
                    'instructor_id' => $instructorId,
                    'book_id' => $book->id,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'extension' => $image->getClientOriginalExtension()
                ]);

                $coverImageName = 'cover.' . $image->getClientOriginalExtension();

                // Make sure the directory exists
                $coverDirectory = storage_path('app/' . $bookFolderPath . '/cover');
                if (!file_exists($coverDirectory)) {
                    if (!mkdir($coverDirectory, 0755, true)) {
                        Log::error('Cover Image Upload - Failed to create directory', [
                            'directory' => $coverDirectory
                        ]);
                        return redirect()->back()->withErrors(['cover_image' => 'Server error: Could not create directory'])->withInput();
                    }
                }

                // Store the file directly using move
                if ($image->move($coverDirectory, $coverImageName)) {
                    Log::info('Cover Image Upload - File moved successfully', [
                        'destination' => $coverDirectory . '/' . $coverImageName
                    ]);
                    $book->cover_image = "instructors/{$instructorId}/books/{$book->id}/cover/{$coverImageName}";
                } else {
                    Log::error('Cover Image Upload - Failed to move file', [
                        'destination' => $coverDirectory . '/' . $coverImageName
                    ]);
                    return redirect()->back()->withErrors(['cover_image' => 'Failed to upload cover image'])->withInput();
                }
            } catch (\Exception $e) {
                Log::error('Cover Image Upload - Exception', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->withErrors(['cover_image' => 'Error uploading cover image: ' . $e->getMessage()])->withInput();
            }
        }

        // Handle PDF file upload
        if ($request->hasFile('pdf_file')) {
            try {
                $pdf = $request->file('pdf_file');

                // Verify that the file is valid
                if (!$pdf->isValid()) {
                    return redirect()->back()->withErrors(['pdf_file' => 'Invalid PDF file'])->withInput();
                }

                // Create a debug log to track the upload
                Log::info('PDF Upload - Starting upload process', [
                    'instructor_id' => $instructorId,
                    'book_id' => $book->id,
                    'original_name' => $pdf->getClientOriginalName(),
                    'mime_type' => $pdf->getMimeType(),
                    'size' => $pdf->getSize(),
                    'extension' => $pdf->getClientOriginalExtension()
                ]);

                $pdfFileName = 'book.' . $pdf->getClientOriginalExtension();

                // Make sure the directory exists
                $pdfDirectory = storage_path('app/' . $bookFolderPath . '/pdf');
                if (!file_exists($pdfDirectory)) {
                    if (!mkdir($pdfDirectory, 0755, true)) {
                        Log::error('PDF Upload - Failed to create directory', [
                            'directory' => $pdfDirectory
                        ]);
                        return redirect()->back()->withErrors(['pdf_file' => 'Server error: Could not create directory'])->withInput();
                    }
                }

                // Store the file directly using move
                if ($pdf->move($pdfDirectory, $pdfFileName)) {
                    Log::info('PDF Upload - File moved successfully', [
                        'destination' => $pdfDirectory . '/' . $pdfFileName
                    ]);
                    $book->pdf_file = "instructors/{$instructorId}/books/{$book->id}/pdf/{$pdfFileName}";
                } else {
                    Log::error('PDF Upload - Failed to move file', [
                        'destination' => $pdfDirectory . '/' . $pdfFileName
                    ]);
                    return redirect()->back()->withErrors(['pdf_file' => 'Failed to upload PDF file'])->withInput();
                }
            } catch (\Exception $e) {
                Log::error('PDF Upload - Exception', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->withErrors(['pdf_file' => 'Error uploading PDF: ' . $e->getMessage()])->withInput();
            }
        }

        // Save the book with file paths
        $book->save();

        return redirect()->route('instructor.books.index')
            ->with('success', 'Book created successfully!');
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        // Check if the authenticated user owns this book
        if ($book->user_id !== Auth::id()) {
            abort(403);
        }

        return view('instructor.books.edit', compact('book'));
    }

    /**
     * Update the specified book in storage.
     */
    public function update(Request $request, Book $book)
    {
        // Check if the authenticated user owns this book
        if ($book->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'author' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:10240', // 10MB max
        ]);

        // Create book directory structure that includes instructor_id if it doesn't exist
        $instructorId = Auth::id();
        $bookFolderPath = "public/instructors/{$instructorId}/books/{$book->id}";
        Storage::makeDirectory($bookFolderPath);
        Storage::makeDirectory($bookFolderPath . '/pdf');
        Storage::makeDirectory($bookFolderPath . '/cover');

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            try {
                // Delete old image if exists
                if ($book->cover_image) {
                    $oldImagePath = 'public/' . $book->cover_image;
                    if (Storage::exists($oldImagePath)) {
                        Log::info('Cover Image Update - Deleting old image', [
                            'path' => $oldImagePath
                        ]);
                        Storage::delete($oldImagePath);
                    } else {
                        Log::warning('Cover Image Update - Old image not found', [
                            'path' => $oldImagePath
                        ]);
                    }
                }

                $image = $request->file('cover_image');

                // Verify that the file is valid
                if (!$image->isValid()) {
                    Log::error('Cover Image Update - Invalid file', [
                        'instructor_id' => $instructorId,
                        'book_id' => $book->id
                    ]);
                    return redirect()->back()->withErrors(['cover_image' => 'Invalid image file'])->withInput();
                }

                // Create a debug log to track the upload
                Log::info('Cover Image Update - Starting upload process', [
                    'instructor_id' => $instructorId,
                    'book_id' => $book->id,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'extension' => $image->getClientOriginalExtension()
                ]);

                $coverImageName = 'cover.' . $image->getClientOriginalExtension();

                // Make sure the directory exists
                $coverDirectory = storage_path('app/' . $bookFolderPath . '/cover');
                if (!file_exists($coverDirectory)) {
                    if (!mkdir($coverDirectory, 0755, true)) {
                        Log::error('Cover Image Update - Failed to create directory', [
                            'directory' => $coverDirectory
                        ]);
                        return redirect()->back()->withErrors(['cover_image' => 'Server error: Could not create directory'])->withInput();
                    }
                }

                // Store the file directly using move
                if ($image->move($coverDirectory, $coverImageName)) {
                    Log::info('Cover Image Update - File moved successfully', [
                        'destination' => $coverDirectory . '/' . $coverImageName
                    ]);
                    $book->cover_image = "instructors/{$instructorId}/books/{$book->id}/cover/{$coverImageName}";
                } else {
                    Log::error('Cover Image Update - Failed to move file', [
                        'destination' => $coverDirectory . '/' . $coverImageName
                    ]);
                    return redirect()->back()->withErrors(['cover_image' => 'Failed to upload cover image'])->withInput();
                }
            } catch (\Exception $e) {
                Log::error('Cover Image Update - Exception', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->withErrors(['cover_image' => 'Error uploading cover image: ' . $e->getMessage()])->withInput();
            }
        }

        // Handle PDF file upload
        if ($request->hasFile('pdf_file')) {
            try {
                // Delete old file if exists
                if ($book->pdf_file) {
                    Storage::delete('public/' . $book->pdf_file);
                }

                $pdf = $request->file('pdf_file');

                // Verify that the file is valid
                if (!$pdf->isValid()) {
                    return redirect()->back()->withErrors(['pdf_file' => 'Invalid PDF file'])->withInput();
                }

                // Create a debug log to track the upload
                Log::info('PDF Update - Starting upload process', [
                    'instructor_id' => $instructorId,
                    'book_id' => $book->id,
                    'original_name' => $pdf->getClientOriginalName(),
                    'mime_type' => $pdf->getMimeType(),
                    'size' => $pdf->getSize(),
                    'extension' => $pdf->getClientOriginalExtension()
                ]);

                $pdfFileName = 'book.' . $pdf->getClientOriginalExtension();

                // Make sure the directory exists
                $pdfDirectory = storage_path('app/' . $bookFolderPath . '/pdf');
                if (!file_exists($pdfDirectory)) {
                    if (!mkdir($pdfDirectory, 0755, true)) {
                        Log::error('PDF Update - Failed to create directory', [
                            'directory' => $pdfDirectory
                        ]);
                        return redirect()->back()->withErrors(['pdf_file' => 'Server error: Could not create directory'])->withInput();
                    }
                }

                // Store the file directly using move
                if ($pdf->move($pdfDirectory, $pdfFileName)) {
                    Log::info('PDF Update - File moved successfully', [
                        'destination' => $pdfDirectory . '/' . $pdfFileName
                    ]);
                    $book->pdf_file = "instructors/{$instructorId}/books/{$book->id}/pdf/{$pdfFileName}";
                } else {
                    Log::error('PDF Update - Failed to move file', [
                        'destination' => $pdfDirectory . '/' . $pdfFileName
                    ]);
                    return redirect()->back()->withErrors(['pdf_file' => 'Failed to upload PDF file'])->withInput();
                }
            } catch (\Exception $e) {
                Log::error('PDF Update - Exception', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->withErrors(['pdf_file' => 'Error uploading PDF: ' . $e->getMessage()])->withInput();
            }
        }

        // Update book details
        $book->title = $request->title;
        $book->description = $request->description;
        $book->price = $request->price;
        $book->author = $request->author;
        $book->pages = $request->pages;
        $book->language = $request->language;
        $book->is_published = $request->has('is_published');
        $book->save();

        return redirect()->route('instructor.books.index')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(Book $book)
    {
        // Check if the authenticated user owns this book
        if ($book->user_id !== Auth::id()) {
            abort(403);
        }

        $instructorId = $book->user_id;

        // Delete book directory and all files
        Storage::deleteDirectory("public/instructors/{$instructorId}/books/{$book->id}");

        // Also delete files in the old path format if they exist
        if ($book->cover_image && !str_contains($book->cover_image, "instructors/")) {
            if (str_contains($book->cover_image, "/{$book->id}/cover/")) {
                // Old format from previous update
                Storage::delete('public/books/' . $book->cover_image);
            } else {
                // Very old format
                Storage::delete('public/books/covers/' . $book->cover_image);
            }
        }

        if ($book->pdf_file && !str_contains($book->pdf_file, "instructors/")) {
            if (str_contains($book->pdf_file, "/{$book->id}/pdf/")) {
                // Old format from previous update
                Storage::delete('public/books/' . $book->pdf_file);
            } else {
                // Very old format
                Storage::delete('public/books/pdf/' . $book->pdf_file);
            }
        }

        // Delete the book
        $book->delete();

        return redirect()->route('instructor.books.index')
            ->with('success', 'Book deleted successfully!');
    }
}

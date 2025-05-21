<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
        'cover_image',
        'pdf_file',
        'author',
        'pages',
        'language',
        'is_published'
    ];

    /**
     * Get the instructor that owns the book.
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the PDF file URL attribute.
     */
    public function getPdfUrlAttribute()
    {
        if (!$this->pdf_file) {
            return null;
        }
        
        // Check if the path is in the newest format with instructor ID
        if (str_contains($this->pdf_file, "instructors/")) {
            return url("/storage/{$this->pdf_file}");
        }
        
        // Check if the path is in the older format (contains only book ID in path)
        if (str_contains($this->pdf_file, "/{$this->id}/pdf/")) {
            // Extract just the filename part
            $parts = explode('/', $this->pdf_file);
            $filename = end($parts);
            // Use direct route format
            return route('books.direct.file', ['bookId' => $this->id, 'type' => 'pdf', 'filename' => $filename]);
        }
        
        // Very old format - use direct route
        return url("/direct-books/pdf/{$this->pdf_file}");
    }

    /**
     * Get the cover image URL attribute.
     */
    public function getCoverImageUrlAttribute()
    {
        if (!$this->cover_image) {
            return asset('images/default-book-cover.jpg');
        }
        
        // Check if the path is in the newest format with instructor ID
        if (str_contains($this->cover_image, "instructors/")) {
            return url("/storage/{$this->cover_image}");
        }
        
        // Check if the path is in the older format (contains only book ID in path)
        if (str_contains($this->cover_image, "/{$this->id}/cover/")) {
            // Extract just the filename part
            $parts = explode('/', $this->cover_image);
            $filename = end($parts);
            // Use direct route format
            return route('books.direct.file', ['bookId' => $this->id, 'type' => 'cover', 'filename' => $filename]);
        }
        
        // Very old format - use direct route
        return url("/direct-books/covers/{$this->cover_image}");
    }

    /**
     * Check if the PDF file exists in storage.
     */
    public function pdfFileExists()
    {
        if (!$this->pdf_file) {
            return false;
        }
        
        // Check if the path is in the newest format with instructor ID
        if (str_contains($this->pdf_file, "instructors/")) {
            $storagePath = storage_path('app/public/' . $this->pdf_file);
            return file_exists($storagePath);
        }
        
        // Check if the path is in the older format
        if (str_contains($this->pdf_file, "/{$this->id}/pdf/")) {
            $storagePath = storage_path('app/public/books/' . $this->pdf_file);
            return file_exists($storagePath);
        }
        
        // Very old format checks
        $storagePath = storage_path('app/public/books/pdf/' . $this->pdf_file);
        if (file_exists($storagePath)) {
            return true;
        }
        
        $publicPath = public_path('storage/books/pdf/' . $this->pdf_file);
        return file_exists($publicPath);
    }

    /**
     * Check if the cover image exists in storage.
     */
    public function coverImageExists()
    {
        if (!$this->cover_image) {
            return false;
        }
        
        // Check if the path is in the newest format with instructor ID
        if (str_contains($this->cover_image, "instructors/")) {
            $storagePath = storage_path('app/public/' . $this->cover_image);
            return file_exists($storagePath);
        }
        
        // Check if the path is in the older format
        if (str_contains($this->cover_image, "/{$this->id}/cover/")) {
            $storagePath = storage_path('app/public/books/' . $this->cover_image);
            return file_exists($storagePath);
        }
        
        // Very old format checks
        $storagePath = storage_path('app/public/books/covers/' . $this->cover_image);
        if (file_exists($storagePath)) {
            return true;
        }
        
        $publicPath = public_path('storage/books/covers/' . $this->cover_image);
        return file_exists($publicPath);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannedWord;
use App\Services\ContentFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BannedWordsController extends Controller
{
    /**
     * @var ContentFilterService
     */
    protected $contentFilterService;

    /**
     * Create a new controller instance.
     *
     * @param ContentFilterService $contentFilterService
     */
    public function __construct(ContentFilterService $contentFilterService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->contentFilterService = $contentFilterService;
    }

    /**
     * Display a listing of banned words.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = BannedWord::query();
        
        // Filter by type if provided
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Filter by status if provided
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('active', false);
            }
        }
        
        // Filter by severity if provided
        if ($request->has('severity') && is_numeric($request->severity)) {
            $query->where('severity', $request->severity);
        }
        
        // Search query if provided
        if ($request->has('search') && !empty($request->search)) {
            $query->where('word', 'like', '%' . $request->search . '%')
                  ->orWhere('replacement', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%');
        }
        
        // Sort by specified column or default to ID descending
        $sortColumn = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedColumns = ['id', 'word', 'type', 'severity', 'active', 'created_at'];
        
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        $query->orderBy($sortColumn, $sortDirection);
        
        $bannedWords = $query->paginate(20)->withQueryString();
        
        // Get distinct types for filter dropdown
        $types = BannedWord::select('type')->distinct()->pluck('type');
        
        return view('admin.banned-words.index', compact('bannedWords', 'types'));
    }

    /**
     * Show the form for creating a new banned word.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.banned-words.create');
    }

    /**
     * Store a newly created banned word in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100|unique:banned_words,word',
            'type' => 'required|string|max:50',
            'replacement' => 'nullable|string',
            'severity' => 'required|integer|min:1|max:5',
            'active' => 'boolean',
            'notes' => 'nullable|string'
        ]);
        
        try {
            $bannedWord = BannedWord::create([
                'word' => $request->word,
                'type' => $request->type,
                'replacement' => $request->replacement,
                'severity' => $request->severity,
                'active' => $request->has('active'),
                'notes' => $request->notes
            ]);
            
            // Clear cache to apply changes immediately
            $this->contentFilterService->clearBannedWordsCache();
            
            return redirect()->route('admin.banned-words.index')
                            ->with('success', 'Banned word added successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating banned word: ' . $e->getMessage());
            
            return redirect()->back()
                            ->with('error', 'Error creating banned word. Please try again.')
                            ->withInput();
        }
    }

    /**
     * Show the form for editing the specified banned word.
     *
     * @param BannedWord $bannedWord
     * @return \Illuminate\View\View
     */
    public function edit(BannedWord $bannedWord)
    {
        return view('admin.banned-words.edit', compact('bannedWord'));
    }

    /**
     * Update the specified banned word in storage.
     *
     * @param Request $request
     * @param BannedWord $bannedWord
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, BannedWord $bannedWord)
    {
        $request->validate([
            'word' => 'required|string|max:100|unique:banned_words,word,' . $bannedWord->id,
            'type' => 'required|string|max:50',
            'replacement' => 'nullable|string',
            'severity' => 'required|integer|min:1|max:5',
            'active' => 'boolean',
            'notes' => 'nullable|string'
        ]);
        
        try {
            $bannedWord->update([
                'word' => $request->word,
                'type' => $request->type,
                'replacement' => $request->replacement,
                'severity' => $request->severity,
                'active' => $request->has('active'),
                'notes' => $request->notes
            ]);
            
            // Clear cache to apply changes immediately
            $this->contentFilterService->clearBannedWordsCache();
            
            return redirect()->route('admin.banned-words.index')
                            ->with('success', 'Banned word updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating banned word: ' . $e->getMessage());
            
            return redirect()->back()
                            ->with('error', 'Error updating banned word. Please try again.')
                            ->withInput();
        }
    }

    /**
     * Remove the specified banned word from storage.
     *
     * @param BannedWord $bannedWord
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(BannedWord $bannedWord)
    {
        try {
            $bannedWord->delete();
            
            // Clear cache to apply changes immediately
            $this->contentFilterService->clearBannedWordsCache();
            
            return redirect()->route('admin.banned-words.index')
                            ->with('success', 'Banned word deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting banned word: ' . $e->getMessage());
            
            return redirect()->back()
                            ->with('error', 'Error deleting banned word. Please try again.');
        }
    }

    /**
     * Toggle the active status of the specified banned word.
     *
     * @param BannedWord $bannedWord
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(BannedWord $bannedWord)
    {
        try {
            $bannedWord->update([
                'active' => !$bannedWord->active
            ]);
            
            // Clear cache to apply changes immediately
            $this->contentFilterService->clearBannedWordsCache();
            
            return redirect()->route('admin.banned-words.index')
                            ->with('success', 'Banned word status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error toggling banned word status: ' . $e->getMessage());
            
            return redirect()->back()
                            ->with('error', 'Error updating banned word status. Please try again.');
        }
    }

    /**
     * Search for flagged messages.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function flaggedMessages(Request $request)
    {
        $query = \App\Models\DirectMessage::flagged();
        
        // Filter by minimum severity if provided
        if ($request->has('min_severity') && is_numeric($request->min_severity)) {
            $query->where('flagged_severity', '>=', $request->min_severity);
        }
        
        // Filter by course if provided
        if ($request->has('course_id') && is_numeric($request->course_id)) {
            $query->where('course_id', $request->course_id);
        }
        
        // Filter by user if provided
        if ($request->has('user_id') && is_numeric($request->user_id)) {
            $query->where(function($q) use ($request) {
                $q->where('sender_id', $request->user_id)
                  ->orWhere('receiver_id', $request->user_id);
            });
        }
        
        // Include relationships
        $query->with(['sender', 'receiver', 'course']);
        
        // Sort by specified column or default to creation date descending
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedColumns = ['message_id', 'created_at', 'flagged_severity'];
        
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'created_at';
        }
        
        $query->orderBy($sortColumn, $sortDirection);
        
        $flaggedMessages = $query->paginate(15)->withQueryString();
        
        // Get courses for filter dropdown
        $courses = \App\Models\Course::select('course_id', 'title')->orderBy('title')->get();
        
        return view('admin.banned-words.flagged-messages', compact('flaggedMessages', 'courses'));
    }

    /**
     * Test content filtering on sample text.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testFilter(Request $request)
    {
        $request->validate([
            'content' => 'required|string'
        ]);
        
        $result = $this->contentFilterService->filterContent($request->content);
        
        return response()->json($result);
    }

    /**
     * Bulk import banned words.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'words' => 'required|string',
            'type' => 'required|string|max:50',
            'severity' => 'required|integer|min:1|max:5',
            'active' => 'boolean',
        ]);
        
        $words = preg_split('/\r\n|\r|\n/', $request->words);
        $words = array_map('trim', $words);
        $words = array_filter($words);
        
        $count = 0;
        $duplicates = 0;
        
        foreach ($words as $word) {
            // Skip empty lines
            if (empty($word)) {
                continue;
            }
            
            // Check if word already exists
            if (BannedWord::where('word', $word)->exists()) {
                $duplicates++;
                continue;
            }
            
            BannedWord::create([
                'word' => $word,
                'type' => $request->type,
                'severity' => $request->severity,
                'active' => $request->has('active'),
            ]);
            
            $count++;
        }
        
        // Clear cache to apply changes immediately
        $this->contentFilterService->clearBannedWordsCache();
        
        return redirect()->route('admin.banned-words.index')
                        ->with('success', "Added $count banned words. Skipped $duplicates duplicates.");
    }
} 
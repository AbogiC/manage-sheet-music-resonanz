<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SheetMusicResource;
use App\Models\SheetMusic;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SheetMusicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SheetMusic::with('user')->public();

        // Apply filters
        if ($request->has('instrument') && $request->instrument) {
            $query->byInstrument($request->instrument);
        }

        if ($request->has('genre') && $request->genre) {
            $query->byGenre($request->genre);
        }

        if ($request->has('difficulty') && $request->difficulty) {
            $query->byDifficulty($request->difficulty);
        }

        if ($request->has('event') && $request->event) {
            $query->byEvent($request->event);
        }

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 12);
        $sheetMusic = $query->paginate($perPage);

        return SheetMusicResource::collection($sheetMusic);
    }

    /**
     * Get all filter options.
     */
    public function getFilters()
    {
        return response()->json([
            'instruments' => Category::getInstruments(),
            'genres' => Category::getGenres(),
            'difficulties' => Category::getDifficulties(),
            'events' => \App\Models\Event::select('id', 'name')->withoutTrashed()->get()
        ]);
    }

    /**
     * Get statistics.
     */
    public function getStats()
    {
        $stats = [
            'total_sheets' => SheetMusic::public()->count(),
            'total_instruments' => count(Category::getInstruments()),
            'total_genres' => count(Category::getGenres()),
            'total_downloads' => SheetMusic::public()->sum('download_count'),
            'total_views' => SheetMusic::public()->sum('view_count')
        ];

        return response()->json($stats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Authentication check
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Debug: Log all request data
            error_log('Upload request data: ' . json_encode($request->all()));
            error_log('Files in request: ' . json_encode($request->allFiles()));
            error_log('Has file: ' . ($request->hasFile('file') ? 'true' : 'false'));
            if ($request->hasFile('file')) {
                error_log('File details: ' . json_encode($request->file('file')));
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'composer' => 'required|string|max:255',
                'arranger' => 'nullable|string|max:255',
                'instrument' => 'required|string|max:100',
                'genre' => 'required|string|max:100',
                'difficulty' => 'required|in:Beginner,Intermediate,Advanced,Professional',
                'pages' => 'required|integer|min:1',
                'key' => 'nullable|string|max:50',
                'time_signature' => 'nullable|string|max:20',
                'tempo' => 'nullable|integer|min:1',
                'description' => 'nullable|string',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'is_public' => 'boolean',
                'file' => 'required|file|mimes:pdf|max:51200' // 50MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('sheet-music', $fileName, 'public');

                $sheetMusic = SheetMusic::create([
                    'user_id' => $request->user()->id,
                    'title' => $request->title,
                    'composer' => $request->composer,
                    'arranger' => $request->arranger,
                    'instrument' => $request->instrument,
                    'genre' => $request->genre,
                    'difficulty' => $request->difficulty,
                    'pages' => $request->pages,
                    'key' => $request->key,
                    'time_signature' => $request->time_signature,
                    'tempo' => $request->tempo,
                    'description' => $request->description,
                    'tags' => $request->tags,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                    'is_public' => $request->boolean('is_public', true)
                ]);

                // Generate thumbnail from first page of PDF (requires additional package)
                // $this->generateThumbnail($sheetMusic);

                return new SheetMusicResource($sheetMusic);
            }

            return response()->json(['message' => 'File upload failed'], 500);
        } catch (\Exception $e) {
            error_log('Error in upload processing: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sheetMusic = SheetMusic::with('user')->public()->findOrFail($id);

        // Increment view count
        $sheetMusic->incrementViewCount();

        return new SheetMusicResource($sheetMusic);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sheetMusic = SheetMusic::findOrFail($id);

        // Authorization check
        if ($request->user()->id !== $sheetMusic->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'composer' => 'sometimes|required|string|max:255',
            'arranger' => 'nullable|string|max:255',
            'instrument' => 'sometimes|required|string|max:100',
            'genre' => 'sometimes|required|string|max:100',
            'difficulty' => 'sometimes|required|in:Beginner,Intermediate,Advanced,Professional',
            'pages' => 'sometimes|required|integer|min:1',
            'key' => 'nullable|string|max:50',
            'time_signature' => 'nullable|string|max:20',
            'tempo' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $sheetMusic->update($request->only([
            'title', 'composer', 'arranger', 'instrument', 'genre',
            'difficulty', 'pages', 'key', 'time_signature', 'tempo',
            'description', 'tags', 'is_public'
        ]));

        return new SheetMusicResource($sheetMusic);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $sheetMusic = SheetMusic::findOrFail($id);

        // Authorization check
        if ($request->user()->id !== $sheetMusic->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($sheetMusic->file_path)) {
            Storage::disk('public')->delete($sheetMusic->file_path);
        }

        // Delete thumbnail if exists
        if ($sheetMusic->thumbnail_path && Storage::disk('public')->exists($sheetMusic->thumbnail_path)) {
            Storage::disk('public')->delete($sheetMusic->thumbnail_path);
        }

        $sheetMusic->delete();

        return response()->json(['message' => 'Sheet music deleted successfully']);
    }

    /**
     * Download sheet music file.
     */
    public function download($id)
    {
        $sheetMusic = SheetMusic::public()->findOrFail($id);

        // Increment download count
        $sheetMusic->incrementDownloadCount();

        $filePath = storage_path('app/public/' . $sheetMusic->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download($filePath, $sheetMusic->file_name);
    }

    /**
     * Get user's sheet music.
     */
    public function mySheets(Request $request)
    {
        $query = $request->user()->sheetMusic();

        // Apply filters
        if ($request->has('instrument') && $request->instrument) {
            $query->byInstrument($request->instrument);
        }

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        $perPage = $request->get('per_page', 12);
        $sheetMusic = $query->paginate($perPage);

        return SheetMusicResource::collection($sheetMusic);
    }

    /**
     * Get events for a specific sheet music.
     */
    public function getEvents(Request $request, $id)
    {
        $sheetMusic = SheetMusic::findOrFail($id);

        // Authorization check - user must own the sheet music
        if ($request->user()->id !== $sheetMusic->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $events = $sheetMusic->events()->with('user')->get();

        return response()->json($events);
    }
}

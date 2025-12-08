<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::with('user', 'sheetMusic')->where('user_id', $request->user()->id);

        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'event_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 12);
        $events = $query->paginate($perPage);

        return response()->json($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Authentication check
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $event = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'user_id' => $request->user()->id
        ]);

        return response()->json($event->load('sheetMusic'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $event = Event::with('user', 'sheetMusic')->findOrFail($id);

        // Authorization check
        if ($request->user()->id !== $event->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Authorization check
        if ($request->user()->id !== $event->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'sometimes|required|date',
            'location' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $event->update($request->only([
            'name', 'description', 'event_date', 'location'
        ]));

        return response()->json($event->load('sheetMusic'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Authorization check
        if ($request->user()->id !== $event->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

    /**
     * Add sheet music to an event.
     */
    public function addSheetMusic(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        // Authorization check
        if ($request->user()->id !== $event->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'sheet_music_id' => 'required|exists:sheet_music,id',
            'order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if sheet music exists (frontend already filters to public sheet music)
        $sheetMusic = \App\Models\SheetMusic::findOrFail($request->sheet_music_id);

        $event->sheetMusic()->attach($request->sheet_music_id, [
            'order' => $request->order ?? 0,
            'notes' => $request->notes
        ]);

        return response()->json($event->load('sheetMusic'), 201);
    }

    /**
     * Remove sheet music from an event.
     */
    public function removeSheetMusic(Request $request, $eventId, $sheetMusicId)
    {
        $event = Event::findOrFail($eventId);

        // Authorization check
        if ($request->user()->id !== $event->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->sheetMusic()->detach($sheetMusicId);

        return response()->json($event->load('sheetMusic'));
    }

    /**
     * Update sheet music order/notes in an event.
     */
    public function updateSheetMusic(Request $request, $eventId, $sheetMusicId)
    {
        $event = Event::findOrFail($eventId);

        // Authorization check
        if ($request->user()->id !== $event->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $event->sheetMusic()->updateExistingPivot($sheetMusicId, [
            'order' => $request->order ?? 0,
            'notes' => $request->notes
        ]);

        return response()->json($event->load('sheetMusic'));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->has('start_date')) {
            $query->where('start_time', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('end_time', '<=', $request->end_date);
        }

        $query->orderBy('start_time', 'desc');
        $events = $query->cursorPaginate(15);

        return EventResource::collection($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date|after_or_equal:today',
            'end_time' => 'required|after:start_time'
        ]);

        $event = Event::create([
            'user_id' => $request->user()->id,
            ...$validated
        ]);

        return new EventResource($event);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Event $event)
    {
        $allowedIncludes = ['user', 'attendees', 'attendees.user', 'reviews', 'reviews.user'];

        if ($request->has('include')) {
            $includes = collect(explode(',', $request->include))
                ->map(fn($include) => trim($include))
                ->intersect($allowedIncludes);

            if ($includes->isNotEmpty()) {
                $event->load($includes->toArray());
            }
        }

        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'start_time' => 'sometimes|date|after_or_equal:today',
            'end_time' => 'sometimes|after:start_time'
        ]);

        $event->update($validated);
        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->noContent();
    }
}

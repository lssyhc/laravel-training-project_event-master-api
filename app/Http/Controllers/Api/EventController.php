<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Event::class);
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
        Gate::authorize('create', Event::class);
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
        Gate::authorize('view', $event);
        $allowedIncludes = ['user', 'attendees', 'attendees.user', 'reviews', 'reviews.user'];
        $allowedCounts = ['attendees', 'reviews'];

        if ($request->has('include')) {
            $includes = collect(explode(',', $request->include))
                ->map(fn($include) => trim($include))
                ->intersect($allowedIncludes);

            if ($includes->isNotEmpty()) {
                $event->load($includes->toArray());
            }
        }

        if ($request->has('count')) {
            $counts = collect(explode(',', $request->count))
                ->map(fn($count) => trim($count))
                ->intersect($allowedCounts);

            if ($counts->isNotEmpty()) {
                $event->loadCount($counts->toArray());
            }
        }

        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        Gate::authorize('update', $event);
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
        Gate::authorize('delete', $event);
        $event->delete();
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class AttendeeController extends Controller
{
    public function index(Event $event)
    {
        Gate::authorize('viewAny', Attendee::class);
        $attendees = $event->attendees()->with(['user', 'event'])->paginate(15);
        return AttendeeResource::collection($attendees);
    }

    public function show(Attendee $attendee)
    {
        Gate::authorize('view', $attendee);
        return new AttendeeResource($attendee->load(['user', 'event']));
    }

    public function store(Request $request, Event $event)
    {
        Gate::authorize('create', Attendee::class);
        $existingAttendee = Attendee::where('user_id', $request->user()->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existingAttendee) {
            throw ValidationException::withMessages([
                'user_id' => 'You are already attending this event.'
            ]);
        }

        $attendee = $event->attendees()->create(['user_id' => $request->user()->id]);
        return new AttendeeResource($attendee);
    }

    public function destroy(Attendee $attendee)
    {
        Gate::authorize('delete', $attendee);
        $attendee->delete();
        return response()->noContent();
    }
}

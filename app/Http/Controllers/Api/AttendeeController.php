<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyEventAttendanceRequest;
use App\Http\Resources\AttendeeResource;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;

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

    public function store(VerifyEventAttendanceRequest $request, Event $event)
    {
        Gate::authorize('create', Attendee::class);
        $request->validateAttendance(true);
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

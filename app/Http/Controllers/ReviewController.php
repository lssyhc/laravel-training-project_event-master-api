<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyEventAttendanceRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Event;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::query();

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->with(['user', 'event'])->orderBy('updated_at', 'desc')->paginate(15);
        return ReviewResource::collection($reviews);
    }

    public function store(VerifyEventAttendanceRequest $request, Event $event)
    {
        $request->validateAttendance(false);
        $review = $event->reviews()->create([
            ...$request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|max:255'
            ]),
            'user_id' => $request->user()->id,
            'event_id' => $event->id
        ]);
        return new ReviewResource($review);
    }

    public function show(Review $review)
    {
        return new ReviewResource($review->load(['user', 'event']));
    }

    public function update(Request $request, Review $review)
    {
        $review->update(
            $request->validate([
                'rating' => 'sometimes|integer|min:1|max:5',
                'comment' => 'nullable|max:255'
            ])
        );
        return new ReviewResource($review);
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return response()->noContent();
    }
}

<?php

namespace Database\Factories;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

use function PHPUnit\Framework\isNull;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendee>
 */
class AttendeeFactory extends Factory
{
    protected static $uniquePairs;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (is_null(self::$uniquePairs)) {
            $user_ids = User::pluck('id');
            $event_ids = Event::pluck('id');

            $allPairs = collect();
            foreach ($user_ids as $user_id) {
                foreach ($event_ids as $event_id) {
                    $allPairs->push(['user_id' => $user_id, 'event_id' => $event_id]);
                }
            }

            self::$uniquePairs = $allPairs->shuffle();
        }

        $pair = self::$uniquePairs->pop();
        if (is_null($pair)) {
            throw new Exception("No more unique combinations are available for Attendee.");
        }

        return [
            'user_id' => $pair['user_id'],
            'event_id' => $pair['event_id']
        ];
    }
}

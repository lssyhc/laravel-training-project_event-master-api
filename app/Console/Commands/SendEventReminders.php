<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotifications;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications to attendees of events happening tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow =  Carbon::tomorrow();
        $events = Event::with('attendees.user')
            ->whereBetween('start_time', [
                $tomorrow->copy()->startOfDay(),
                $tomorrow->copy()->endOfDay()
            ])->get();

        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);
        $this->info("Found $eventCount $eventLabel scheduled for tomorrow.");

        $notifiedCount = 0;
        $events->each(function ($event) use (&$notifiedCount) {
            $this->info("Sending {$event->attendees->count()} notifications for event: {$event->name}");

            $event->attendees->each(function ($attendee) use ($event, &$notifiedCount) {
                $attendee->user->notify(new EventReminderNotifications($event));
                $notifiedCount++;
            });
        });

        return Command::SUCCESS;
    }
}

<?php

namespace App\Http\Requests;

use App\Models\Attendee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class VerifyEventAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        if ($this->route('event')) {
            $this->merge([
                'event' => $this->route('event'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event' => 'required'
        ];
    }

    public function validateAttendance(bool $attend): void
    {
        $existingAttendee = Attendee::where('user_id', $this->user()->id)
            ->where('event_id', $this->event->id)
            ->first();

        if ($attend) {
            if ($existingAttendee) {
                throw ValidationException::withMessages([
                    'user_id' => 'You are already attending this event.'
                ]);
            }
        } elseif (!$attend) {
            if (!$existingAttendee) {
                throw ValidationException::withMessages([
                    'user_id' => 'You have never attended this event.'
                ]);
            }
        }
    }
}

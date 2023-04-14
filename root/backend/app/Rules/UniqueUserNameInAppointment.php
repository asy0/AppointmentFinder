<?php

namespace App\Rules;

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueUserNameInAppointment implements Rule
{
    protected string $appointmentDateId;

    public function __construct($appointmentDateId)
    {
        $this->appointmentDateId = $appointmentDateId;
    }

    public function passes($attribute, $value)
    {
        $query = DB::table('appointments')
            ->join('appointment_dates', 'appointments.id', '=', 'appointment_dates.appointment_id')
            ->join('votes', 'votes.appointment_date_id', '=', 'appointment_dates.id')
            ->where('appointment_dates.id', $this->appointmentDateId)
            ->where('votes.user_name', $value);

        return $query->doesntExist();
    }

    public function message()
    {
        return ':input has already voted in the appointment.';
    }
}

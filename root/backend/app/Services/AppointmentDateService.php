<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentDate;

class AppointmentDateService {

	public function getDatesOfAppointment(int $id): array {
		$appointment = Appointment::find($id);
		return $appointment->appointmentDates()->get()->toArray();
	}

	public function getAppointmentDate(int $id): AppointmentDate {
		return AppointmentDate::find($id);
	}

}
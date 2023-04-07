<?php

namespace App\Services;

use App\Exceptions\AppointmentDateNotFoundException;
use App\Exceptions\AppointmentNotFoundException;
use App\Models\Appointment;
use App\Models\AppointmentDate;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AppointmentDateService {

	public function getDatesOfAppointment(int $id): array {
		// Try to find the appointment, if model not found, throw exception
		try {
			$appointment = Appointment::findOrFail($id);
			return $appointment->appointmentDates()->get()->toArray();
		} catch (ModelNotFoundException $e) {
			throw new AppointmentNotFoundException("Appointment with id $id not found", 404, $e);
		}
	}

	public function getAppointmentDate(int $id): AppointmentDate {
		try {
			return AppointmentDate::findOrFail($id);
		} catch (ModelNotFoundException $e) {
			throw new AppointmentDateNotFoundException("AppointmentDate with id $id not found", 404, $e);
		}
	}

}
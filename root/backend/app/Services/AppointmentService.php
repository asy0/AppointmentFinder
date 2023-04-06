<?php

namespace App\Services;

use App\Exceptions\AppointmentNotSavedException;
use App\Models\Appointment;
use App\Models\AppointmentDate;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AppointmentService {

	public function getAllAppointments(): Collection {
		return Appointment::all();
	}

	public function getAppointmentById(int $id): ?Appointment {
		return Appointment::find($id);
	}

	public function deleteAppointment(int $id): bool {
		$appointment = $this->getAppointmentById($id);

		if (!$appointment) {
			return false;
		}

		return $appointment->delete();
	}

	/**
	 * @throws Exception
	 */
	public function createAppointment(array $data): Appointment {
		$appointment = new Appointment([
			'title' => $data['title'],
			'location' => $data['location'],
			'description' => $data['description'],
			'expiration_date' => Carbon::parse($data['expiration_date']),
		]);
		if (!$appointment->save()) {
			throw new AppointmentNotSavedException('Failed to save the appointment.');
		}
		try {
			$this->addAppointmentDates($appointment, $data);
		} catch (Exception) {
			$appointment->delete();
			throw new AppointmentNotSavedException('Failed to save the appointment dates.');
		}
		return $appointment;
	}

	private function addAppointmentDates(Appointment $appointment, array $data): void {
		$appointmentDates = [];

		foreach ($data['appointment_dates'] as $appointmentDate) {
			$appointmentDates[] = new AppointmentDate([
				'date' => Carbon::parse($appointmentDate['date']),
				'start_time' => $appointmentDate['start_time'],
				'end_time' => $appointmentDate['end_time'],
			]);
		}

		$appointment->appointmentDates()->saveMany($appointmentDates);
	}

}
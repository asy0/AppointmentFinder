<?php

namespace App\Http\Controllers;

use App\Exceptions\AppointmentDateNotFoundException;
use App\Exceptions\AppointmentNotFoundException;
use App\Services\AppointmentDateService;
use Illuminate\Http\JsonResponse;

class AppointmentDateController extends Controller {

	protected AppointmentDateService $appointmentDatesService;

	public function __construct(AppointmentDateService $appointmentDatesService) {
		$this->appointmentDatesService = $appointmentDatesService;
	}

	public function index(int $appointmentId): JsonResponse {
		try {
			$appointmentDates = $this->appointmentDatesService->getDatesOfAppointment($appointmentId);
			return response()->json($appointmentDates);
		} catch (AppointmentNotFoundException $e) {
			return response()->json(['error' => $e->getMessage()], 404);
		}
	}

	public function get(int $id): JsonResponse {
		try {
			$appointmentDate = $this->appointmentDatesService->getAppointmentDate($id);
			return response()->json($appointmentDate);
		} catch (AppointmentDateNotFoundException $e) {
			return response()->json(['error' => $e->getMessage()], 404);
		}
	}
}
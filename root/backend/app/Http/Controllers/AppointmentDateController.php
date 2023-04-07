<?php

namespace App\Http\Controllers;

use App\Services\AppointmentDateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentDateController extends Controller {

	protected AppointmentDateService $appointmentDatesService;

	public function __construct(AppointmentDateService $appointmentDatesService) {
		$this->appointmentDatesService = $appointmentDatesService;
	}

	public function index(int $appointmentId): JsonResponse {
		$appointmentDates = $this->appointmentDatesService->getDatesOfAppointment($appointmentId);
		return response()->json($appointmentDates);
	}

	public function get(int $id): JsonResponse {
		$appointmentDate = $this->appointmentDatesService->getAppointmentDate($id);
		return response()->json($appointmentDate);
	}
}
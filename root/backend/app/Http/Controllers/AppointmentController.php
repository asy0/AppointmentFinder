<?php

namespace App\Http\Controllers;

use App\Exceptions\AppointmentDatesNotSavedException;
use App\Exceptions\AppointmentNotDeletedException;
use App\Exceptions\AppointmentNotFoundException;
use App\Exceptions\AppointmentNotSavedException;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller {
	protected AppointmentService $appointmentService;

	public function __construct(AppointmentService $appointmentService) {
		$this->appointmentService = $appointmentService;
	}

	public function index(): JsonResponse {
		$appointments = $this->appointmentService->getAllAppointments();
		return response()->json($appointments);
	}

	public function create(Request $request): JsonResponse {
		$validator = Validator::make($request->all(),
			[
			'title' => 'required|string',
			'location' => 'required|string',
			'description' => 'required|string',
			'expiration_date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
			'appointment_dates' => 'required|array|min:1',
			'appointment_dates.*.date' => 'required|date|date_format:Y-m-d|after_or_equal:today|before:expiration_date',
			'appointment_dates.*.start_time' => 'required|date_format:H:i',
			'appointment_dates.*.end_time' => 'required|date_format:H:i',],
			['title.required' => 'Title is required',
			'location.required' => 'Location is required',
			'description.required' => 'Description is required',
			'expiration_date.required' => 'Expiration date is required',
			'appointment_dates.required' => 'Appointment dates are required',
			'appointment_dates.min' => 'There must be at least one appointment date',
			'appointment_dates.*.date.required' => 'Date is required',
			'appointment_dates.*.date.after_or_equal' => 'Date must be equal or after today',
			'appointment_dates.*.date.before' => 'Date must be before the expiration date',
			'appointment_dates.*.start_time.required' => 'Start time is required',
			'appointment_dates.*.end_time.required' => 'End time is required',]);
		if ($validator->fails()) {
			return response()->json(['message' => $validator->errors()], 400);
		}
		try {
			$appointment = $this->appointmentService->createAppointment($request->all());
			return response()->json($appointment, 201);
		} catch (AppointmentNotSavedException | AppointmentDatesNotSavedException $e) {
			return response()->json(['message' => $e->getMessage()], 500);
		}
	}

	public function get(int $appointmentId): JsonResponse {
		try {
			$appointment = $this->appointmentService->getAppointmentById($appointmentId);
			return response()->json($appointment);
		} catch (AppointmentNotFoundException $e) {
			return response()->json(['message' => $e->getMessage()], 404);
		}
	}

	public function delete(int $id): JsonResponse {
		try {
			$this->appointmentService->deleteAppointment($id);
			return response()->json(['message' => 'Appointment deleted.'], 200);
		} catch (AppointmentNotFoundException $e) {
			return response()->json(['message' => $e->getMessage()], 404);
		} catch (AppointmentNotDeletedException $e) {
			return response()->json(['message' => $e->getMessage()], 500);
		}
	}

}

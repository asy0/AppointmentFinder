<?php

namespace App\Http\Controllers;

use App\Exceptions\VoteNotSavedException;
use App\Services\VoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoteController extends Controller {

	protected VoteService $voteService;

	public function __construct(VoteService $voteService) {
		$this->voteService = $voteService;
	}

	public function index(Request $request): JsonResponse {
		$appointmentDateId = $request->query('appointmentDateId');

		$votes = $this->voteService->getVotesOfAppointmentDate($appointmentDateId);

		return response()->json($votes);
	}

	public function create(Request $request): JsonResponse {
		$validator = Validator::make($request->all(), [
			'user_name' => 'required|string|max:255',
			'appointmentDateIds' => 'required|array',
			'appointmentDateIds.*' => 'required|integer|distinct|exists:appointment_dates,id',
			'comment' => 'nullable|string',
		], [
			'user_name.required' => 'User name is required',
			'appointmentDateIds.required' => 'There must be at least one time slot selected',
			'appointmentDateIds.*.integer' => 'Each appointment date ID must be an integer',
			'appointmentDateIds.*.distinct' => 'Each appointment date ID must be unique',
			'appointmentDateIds.*.exists' => 'Appointment date ID not found',
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}
		try {
			$vote = $this->voteService->save($request->all());
			return response()->json($vote);
		} catch (VoteNotSavedException $e) {
			return response()->json(['errors' => $e->getMessage()], 422);
		}

	}

	public function get(int $id): JsonResponse {
		$vote = $this->voteService->getVoteById($id);
		return response()->json($vote);
	}
}
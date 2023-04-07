<?php

namespace App\Http\Controllers;

use App\Exceptions\AppointmentNotFoundException;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller {

	protected CommentService $commentService;

	public function __construct(CommentService $commentService) {
		$this->commentService = $commentService;
	}

	public function index(int $appointmentId): JsonResponse {
		try {
			$comments = $this->commentService->getCommentsOfAppointment($appointmentId);
			return response()->json($comments);
		} catch (AppointmentNotFoundException $e) {
			return response()->json(['message' => $e->getMessage()], 404);
		}
	}

	public function delete(int $id): JsonResponse {
		try {
			$this->commentService->deleteComment($id);
			return response()->json(['message' => 'Comment deleted successfully.']);
		} catch (\Exception $e) {
			return response()->json(['message' => $e->getMessage()], 500);
		}
	}

}
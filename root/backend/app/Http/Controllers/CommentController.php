<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller {

	protected CommentService $commentService;

	public function __construct(CommentService $commentService) {
		$this->commentService = $commentService;
	}

	public function index(int $appointmentId): JsonResponse {
		$comments = $this->commentService->getCommentsOfAppointment($appointmentId);
		return response()->json($comments);
	}

	public function delete(int $id): JsonResponse {
		$this->commentService->deleteComment($id);
		return response()->json(['message' => 'Comment deleted successfully.']);
	}

}
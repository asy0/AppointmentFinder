<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller {

	protected CommentService $commentService;

	public function __construct(CommentService $commentService) {
		$this->commentService = $commentService;
	}

	public function index(Request $request): JsonResponse {
		$appointmentDateId = $request->query('appointmentId');

		$comments = $this->commentService->getCommentsOfAppointment($appointmentDateId);

		return response()->json($comments);
	}

}
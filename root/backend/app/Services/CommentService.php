<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Comment;

class CommentService {

	// Get all comments with user name for a single appointment with its id
	public function getCommentsOfAppointment(int $appointmentId): array {
		$appointment = Appointment::with('appointmentDates.votes.comment')->findOrFail($appointmentId);
		$commentsWithUsernames = [];
		foreach ($appointment->appointmentDates as $appointmentDate) {
			foreach ($appointmentDate->votes as $vote) {
				if ($vote->comment) {
					$commentsWithUsernames[] = [
						'username' => $vote->user_name,
						'comment' => $vote->comment->content,
						'created_at' => $vote->comment->created_at->format('d.m.Y H:i:s'),
					];
				}
			}
		}
		return $commentsWithUsernames;
	}


	public function delete(int $id) {
		$comment = Comment::findOrFail($id);
		$comment->delete();
	}

}
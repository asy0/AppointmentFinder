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
						'comment' => $vote->comment->content, // Replace 'content' with the actual field name in your Comment model that stores the comment text.
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
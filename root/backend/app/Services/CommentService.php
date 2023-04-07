<?php

namespace App\Services;

use App\Exceptions\AppointmentNotFoundException;
use App\Exceptions\CommentNotDeletedException;
use App\Exceptions\CommentNotFoundException;
use App\Models\Appointment;
use App\Models\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentService {

	/**
	 * @throws AppointmentNotFoundException
	 */
	public function getCommentsOfAppointment(int $appointmentId): array {
		try {
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
		} catch (ModelNotFoundException $e) {
			throw new AppointmentNotFoundException('Appointment with id ' . $appointmentId . ' not found.');
		}
	}


	/**
	 * @throws CommentNotDeletedException
	 * @throws CommentNotFoundException
	 */
	public function deleteComment(int $id): void {
		try {
			$comment = Comment::findOrFail($id);
			if(!$comment->delete()) {
				throw new CommentNotDeletedException('Comment with id ' . $id . ' could not be deleted.');
			}
		} catch (ModelNotFoundException $e) {
			throw new CommentNotFoundException('Comment with id ' . $id . ' not found.');
		}
	}

}
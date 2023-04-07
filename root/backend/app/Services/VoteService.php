<?php

namespace App\Services;

use App\Exceptions\VoteNotSavedException;
use App\Models\Comment;
use App\Models\Vote;
use Exception;

class VoteService {

	// Get all votes of an appointment and group them by user
	public function getVotesOfAppointment(int $appointmentId) {
		// Get all votes of the appointment using the appointment id and group them by user
		return Vote::join('appointment_dates', 'votes.appointment_date_id', '=', 'appointment_dates.id')
			->where('appointment_dates.appointment_id', $appointmentId)
			->select('votes.*')
			->get()
			->groupBy('user_name')
			->toArray();
	}

	public function getVoteById(int $id) {
		return Vote::findOrFail($id);
	}

	/**
	 * @throws Exception
	 */
	public function save(array $data): array {

		// For each appointment date id, create a vote and save it
		$votes = [];
		$comment = null;
		// Iterate over all appointment date ids with idx
		foreach ($data['appointmentDateIds'] as $idx => $appointmentDateId) {
			$vote = new Vote([
				'appointment_date_id' => (int)$appointmentDateId,
				'user_name' => $data['user_name'],
			]);
			if (!$vote->save()) {
				foreach ($votes as $vote) {
					$vote->delete();
				}
				throw new VoteNotSavedException('Vote could not be saved');
			}
			if ($idx === 0 && isset($data['comment'])) {
				// Create a comment model and save it
				$comment = new Comment([
					'vote_id' => $vote->id,
					'content' => $data['comment'],
				]);
				if (!$comment->save()) {
					foreach ($votes as $vote) {
						$vote->delete();
					}
					throw new VoteNotSavedException('Comment could not be saved');
				}
			}
			$votes[] = $vote;
		}

		return [
			'votes' => $votes,
			'comment' => $comment
		];
	}

}
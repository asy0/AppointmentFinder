<?php

namespace App\Services;

use App\Exceptions\VoteNotSavedException;
use App\Models\AppointmentDate;
use App\Models\Vote;
use Exception;

class VoteService {

	// Get all votes of an appointment date and group them by user
	public function getVotesOfAppointmentDate(int $appointmentDateId) {
		// Get all votes of the appointment date using the appointment date id and group them by user
		return Vote::where('appointment_date_id', $appointmentDateId)
			->get()
			->groupBy('user_name');
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
		foreach ($data['appointmentDateIds'] as $appointmentDateId) {
			$vote = new Vote([
				'appointment_date_id' => (int) $appointmentDateId,
				'user_name' => $data['user_name'],
				'comment' => $data['comment'] ?? null
			]);
			if (!$vote->save()) {
				foreach ($votes as $vote) {
					$vote->delete();
				}
				throw new VoteNotSavedException('Vote could not be saved');
			}
			$votes[] = $vote;
		}

		return $votes;
	}

}
<?php

namespace App\Services;

use App\Exceptions\AppointmentNotFoundException;
use App\Exceptions\VoteNotFoundException;
use App\Exceptions\VoteNotSavedException;
use App\Models\Comment;
use App\Models\Vote;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

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

	/**
	 * @throws VoteNotFoundException
	 */
	public function getVoteById(int $id) {
		try {
			// Get the vote by id
			return Vote::findOrFail($id);
		} catch (ModelNotFoundException) {
			throw new VoteNotFoundException('Vote with id ' . $id . ' not found');
		}
	}

	/**
	 * @throws VoteNotSavedException
	 */
	public function save(array $data): array {
		DB::beginTransaction();

		try {
			// For each appointment date id, create a vote and save it
			$votes = [];
			$comment = null;
			// Iterate over all appointment date ids with idx
			foreach ($data['appointmentDateIds'] as $appointmentDateId) {
				$this->saveVote($appointmentDateId, $data['user_name'], $votes);
			}
			$this->saveCommentIfPresent($data, $votes[0]);

			DB::commit();

			return [
				'votes' => $votes,
				'comment' => $comment
			];
		} catch (VoteNotSavedException $e) {
			DB::rollback();
			throw $e;
		}
	}

	/**
	 * @param mixed $appointmentDateId
	 * @param string $user_name
	 * @param array $votes
	 * @return array
	 * @throws VoteNotSavedException
	 */
	public function saveVote(mixed $appointmentDateId, string $user_name, array &$votes): array {
		$vote = new Vote([
			'appointment_date_id' => (int)$appointmentDateId,
			'user_name' => $user_name,
		]);
		if (!$vote->save()) {
			throw new VoteNotSavedException('Vote could not be saved');
		}
		$votes[] = $vote;
		return $votes;
	}

	/**
	 * @param array $data
	 * @param Vote $vote
	 * @throws VoteNotSavedException
	 */
	public function saveCommentIfPresent(array $data, Vote $vote): void {
		if (isset($data['comment'])) {
			// Create a comment model and save it
			$comment = new Comment([
				'vote_id' => $vote->id,
				'content' => $data['comment'],
			]);

			if (!$comment->save()) {
				throw new VoteNotSavedException('Comment could not be saved => all votes have been deleted');
			}
		}
	}

}
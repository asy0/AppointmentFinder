<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentDate extends Model {
	use HasFactory;

	protected $table = 'appointment_dates';

	protected $fillable = ['date', 'start_time', 'end_time'];

	public function votes(): HasMany {
		return $this->hasMany(Vote::class);
	}
}

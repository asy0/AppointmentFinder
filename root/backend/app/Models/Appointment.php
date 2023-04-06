<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Appointment extends Model {
	use HasFactory;

	protected $table = 'appointments';

	protected $fillable = ['title', 'location', 'description', 'expiration_date'];

	public function appointmentDates(): HasMany {
		return $this->hasMany(AppointmentDate::class);
	}

	public function votes(): HasMany {
		return $this->hasMany(Vote::class);
	}
}

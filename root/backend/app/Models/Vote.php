<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Vote extends Model {
	use HasFactory;

	protected $table = 'votes';

	protected $fillable = ['user_name', 'appointment_date_id'];


	public function appointmentDate(): BelongsTo {
		return $this->belongsTo(AppointmentDate::class);
	}

	public function comment(): HasOne {
		return $this->hasOne(Comment::class);
	}
}

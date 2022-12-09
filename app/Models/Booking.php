<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bookings';
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d';
    protected $guarded = [];

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('dateStart', '<=', now()->format($this->dateFormat))
            ->where('dateEnd', '>', now()->format($this->dateFormat));
    }

//    public function user(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }
}

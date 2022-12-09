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
    const CURRENCY_MULTIPLIER = 100;

    private int|float $price;

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('dateStart', '<=', now()->format($this->dateFormat))
            ->where('dateEnd', '>', now()->format($this->dateFormat));
    }

    /**
     * Mutator to normalize price as integer in pennies.
     * @param float|int $value
     * @return void
     */
    public function setPriceAttribute($value): void
    {
        $this->attributes['price'] = $value * self::CURRENCY_MULTIPLIER;
    }

    /**
     * Accessor to return price as float.
     * @return void
     */
    public function getPriceAttribute()
    {
        $this->attributes['price'] / self::CURRENCY_MULTIPLIER;
    }

//    public function user(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }
}

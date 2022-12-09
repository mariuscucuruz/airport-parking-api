<?php

namespace App\Services;

use App\Models\Booking;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AvailabilityService
{
    public const MAX_DAILY_SPACES = 10;

    public function isAvailable(mixed $payload): bool
    {
        return (self::MAX_DAILY_SPACES >= $this->findBookings($payload)->count());
    }

    /**
     * @param array $payload
     * @return \App\Models\Booking
     * @throws \Exception
     */
    public function reserveDates(array $payload): Booking
    {
        if (!$this->isAvailable($payload)) {
            throw new Exception('Slot not available.');
        }

        return Booking::create($payload);
    }

    public function changeReservation(Booking $booking, array $payload): Booking
    {
        if ($this->findBookings($booking->toArray())->count() === 1) {
            $booking->update($payload);

            return $booking->save();
        }

        throw new ModelNotFoundException();
    }

    /**
     * @param array $criteria
     * @return \App\Models\Booking[]|null
     */
    private function findBookings(array $criteria): ?Booking
    {
        return Booking::where($criteria)->get();
    }
}
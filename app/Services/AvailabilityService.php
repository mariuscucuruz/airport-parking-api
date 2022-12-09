<?php

namespace App\Services;

use App\Models\Booking;
use http\Exception\InvalidArgumentException;

class AvailabilityService
{
    public function isAvailable(mixed $payload): bool
    {
        return (bool) $this->findBooking($payload);
    }

    public function reserveDates(array $payload): Booking
    {
        return Booking::create($payload);
    }

    public function changeReservation(Booking $booking, array $payload): Booking
    {
        if ($this->findBooking($booking->toArray())) {
            $booking->update($payload);

            return $booking->save();
        }

        throw new InvalidArgumentException();
    }

    private function findBooking(array $criteria): ?Booking
    {
        return Booking::where($criteria)->first();
    }
}
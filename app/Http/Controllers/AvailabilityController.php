<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Http\Requests\BookRequest;
use App\Models\Booking;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\AvailabilityRequest $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function check(AvailabilityRequest $request): JsonResponse
    {
        return response()->json(['success' => $request->validated(), 'req' => $request->all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\BookRequest $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function book(BookRequest $request): JsonResponse
    {
        // just assume the details have been saved for now
        return response()->json($request->validated());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BookRequest $request
     * @param \App\Models\Booking            $booking
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update(BookRequest $request, Booking $booking): JsonResponse
    {
        return response()->json([
            $request->validated(),
            $booking->toArray()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\BookRequest $booking
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete(BookRequest $booking): JsonResponse
    {
        return response()->json(['success' => $booking->exists()]);
    }
}

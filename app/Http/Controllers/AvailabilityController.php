<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Http\Requests\BookRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(['ok']);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\AvailabilityRequest $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function check(AvailabilityRequest $request): JsonResponse
    {
        return response()->json(['success' => true, 'req' => $request->all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\BookRequest $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function book(BookRequest $request): JsonResponse
    {
        return response()->json(['success' => true]);
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
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function delete(BookRequest $booking): JsonResponse
    {
        return response()->json(['success' => true]);
    }
}

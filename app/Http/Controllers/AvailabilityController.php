<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Http\Requests\BookRequest;
use App\Models\Booking;
use App\Services\AvailabilityService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityController extends Controller
{
    /**
     * @var \App\Services\AvailabilityService
     */
    private AvailabilityService $service;

    public function __construct(AvailabilityService $service)
    {
        $this->service = $service;
    }

    public function check(AvailabilityRequest $request): JsonResponse
    {
        $response = [
            'status' => $this->service->isAvailable($request->validated())
        ];

        return $this->toJsonResponse($response);
    }

    public function book(BookRequest $request): JsonResponse
    {
        $booking = $this->service->reserveDates($request->validated());

        return $this->toJsonResponse($booking->toArray());
    }

    public function update(BookRequest $request, Booking $booking): JsonResponse
    {
        $result = $this->service->changeReservation($booking, $request->validated());

        return $this->toJsonResponse($result->toArray());
    }

    public function delete(BookRequest $booking): JsonResponse
    {
        try {
            return $this->service->changeReservation($booking);
        } catch (\Exception $exception) {
            return $this->toJsonResponse([
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Little transformer helper to enforce same response structure.
     * In a real world scenario one would use something like thephpleague/fractal.
     *
     * @param array $payload
     * @param int   $code
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function toJsonResponse(array $payload = [], int $code = Response::HTTP_OK): JsonResponse
    {
        if (!$payload) {
            $code = Response::HTTP_NO_CONTENT;
            $payload = null;
        }

        return response()->json($payload, $code);
    }
}

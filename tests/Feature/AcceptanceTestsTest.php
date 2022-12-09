<?php

namespace Tests\Feature;

/**
 * User stories:
 * - Customer should be able to check if there's an available car parking space for given dates
 * - Customer should be able to check parking price for given dates (for example summer prices might be different from winter prices)
 *   - GET /check-availability?dateStart={yyyy-mm-dd}&dateEnd={yyyy-mm-dd}
 *   - return: json(['available' => bool, 'price' => float])
 * - Customers should be able to create a booking for given dates (from - to)
 *   - POST /check-availability?dateStart={yyyy-mm-dd}&dateEnd={yyyy-mm-dd}
 *   - return: json(['available' => bool, 'price' => float])
 * - Customer should be able to cancel given booking
 *   - DELETE /check-availability?dateStart={yyyy-mm-dd}&dateEnd={yyyy-mm-dd}
 *   - return: json(['available' => bool, 'price' => float])
 * - Customer should be able to amend given booking
 *   - PUT /check-availability?dateStart={yyyy-mm-dd}&dateEnd={yyyy-mm-dd}
 *   - return: json(['available' => bool, 'price' => float])
 */

use App\Models\Booking;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AcceptanceTestsTest extends TestCase
{
    use WithFaker;

    public const API_ENDPOINT = '/check-availability';
    public const DATE_FORMAT = 'Y-m-d';

    public function test_api_accepts_get_requests()
    {
        // given

        // when
        $request = $this->get(self::API_ENDPOINT);

        // then
        $request->assertStatus(Response::HTTP_FOUND);
        static::assertNotEquals(Response::HTTP_NOT_FOUND, $request->getStatusCode());
        //$request->assertSee('ok');
    }

    public function test_api_rejects_patch_requests()
    {
        // given

        // when
        $request = $this->patch(self::API_ENDPOINT);

        // then
        $request->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_reject_requests_without_start_date()
    {
        // given
        $dateEnd = $this->faker()->dateTimeBetween('+7 days', '+1 week');
        $url = self::API_ENDPOINT . "/?dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        // when
        $request = $this->getJson($url);

        // then
        $request->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $request->assertSee('date start field is required');
    }

    public function test_reject_requests_without_end_date()
    {
        // given
        $dateStart  = $this->faker()->dateTime('+2 days');
        $url        = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}";

        // when
        $request = $this->getJson($url);

        // then
        $request->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $request->assertSee('date end field is required');
    }

    public function test_reject_dates_in_the_past()
    {
        // given
        $dateStart  = $this->faker()->dateTimeBetween('-20days', '-10 days');
        $dateEnd    = $this->faker()->dateTimeBetween('-5days', '+0 days');
        $url        = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}&dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        // when
        $request = $this->getJson($url);

        // then
        $request->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $request->assertSee('Dates must be in the future');
    }

    public function test_handles_correct_requests()
    {
        // given
        $dateStart = $this->faker()->dateTimeBetween('+5 days', '+10 days');
        $dateEnd   = $this->faker()->dateTimeBetween('+10 days', '+20 days');
        $email     = $this->faker()->safeEmail();

        $requestUrl     = self::API_ENDPOINT;
        $requestPayload = [
            'dateStart'   => $dateStart->format(self::DATE_FORMAT),
            'dateEnd'     => $dateEnd->format(self::DATE_FORMAT),
            'email'       => $email
        ];

        // when
        $request  = $this->postJson($requestUrl, $requestPayload);
        $response = json_decode($request->getContent(), true);

        // then
        $request->assertStatus(Response::HTTP_OK);
        static::assertEquals($response['dateStart'], $dateStart->format(self::DATE_FORMAT));
        static::assertEquals($response['dateEnd'], $dateEnd->format(self::DATE_FORMAT));
        static::assertEquals($response['email'], $email);
    }

    public function test_correctly_identify_booked_slots()
    {
        // given
        $dateStart = $this->faker()->dateTimeBetween('+5 days', '+10 days');
        $dateEnd = $this->faker()->dateTimeBetween('+10 days', '+20 days');

        /** @var \Illuminate\Database\Eloquent\Model $booking */
        $booking = Booking::factory([
            'email'     => $this->faker->safeEmail(),
            'dateStart' => $dateStart->format(self::DATE_FORMAT),
            'dateEnd'   => $dateEnd->format(self::DATE_FORMAT),
        ]);

        $url = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}&dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        // when
        $request = $this->getJson($url);
        $response = json_decode($request->getContent(), true);

        // then
        $request->assertStatus(Response::HTTP_FORBIDDEN);
        static::assertEquals($response['dateStart'], $dateStart->format(self::DATE_FORMAT));
        static::assertEquals($response['dateEnd'], $dateEnd->format(self::DATE_FORMAT));
    }
}

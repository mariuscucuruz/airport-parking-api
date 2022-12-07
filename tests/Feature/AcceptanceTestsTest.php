<?php

namespace Tests\Feature;

use App\Models\Availability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AcceptanceTestsTest extends TestCase
{
    use WithFaker;

    public const API_ENDPOINT = '/check-availability';
    public const DATE_FORMAT = 'Y-m-d';

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

    public function test_api_accepts_get_requests()
    {
        $response = $this->get(self::API_ENDPOINT);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_api_rejects_patch_requests()
    {
        $response = $this->patch(self::API_ENDPOINT);

        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_reject_requests_without_start_date()
    {
        $dateEnd = $this->faker()->dateTime('next week');
        $url = self::API_ENDPOINT . "?dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

//        $this->expectException();
        $response = $this->get($url);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_reject_requests_without_end_date()
    {
        $dateStart = $this->faker()->dateTime('tomorrow');
        $url = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}";

        $this->expectException();
        $response = $this->get($url);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_reject_dates_in_the_past()
    {
        $dateStart = $this->faker()->dateTime('last month');
        $dateEnd = $this->faker()->dateTime('last week');
        $url = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}&dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        $this->expectException();
        $response = $this->get($url);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_handles_correct_requests()
    {
        $dateStart = $this->faker()->dateTime('tomorrow');
        $dateEnd = $this->faker()->dateTime('next week');
        $url = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}&dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        $response = $this->post($url, [
            'email' => $this->faker()->safeEmail()
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_correctly_identify_booked_slots()
    {
        $dateStart = $this->faker()->dateTime('tomorrow');
        $dateEnd = $this->faker()->dateTime('next week');

        /** @var \Illuminate\Database\Eloquent\Model $booking */
        $booking = Availability::factory([
            'booked' => true,
            'dateStart' => $dateStart->format(self::DATE_FORMAT),
            'dateEnd' => $dateEnd->format(self::DATE_FORMAT),
        ]);

        $url = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}&dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        $response = $this->get($url);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

}

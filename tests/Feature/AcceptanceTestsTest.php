<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
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
        // given

        // when
        $response = $this->get(self::API_ENDPOINT);

        // then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee('ok');
    }

    public function test_api_rejects_patch_requests()
    {
        // given

        // when
        $response = $this->patch(self::API_ENDPOINT);

        // then
        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_reject_requests_without_start_date()
    {
        // given
        $dateEnd = $this->faker()->dateTimeBetween('+7 days', '+1 week');
        $url = self::API_ENDPOINT . "/?dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        // when
        $this->expectException(InvalidArgumentException::class);
        $response = $this->get($url);
        dd($url, $response->dd());

        // then
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_reject_requests_without_end_date()
    {
        // given
        $dateStart = $this->faker()->dateTime('+2 days');
        $url = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}";

        // when
        $this->expectException(InvalidArgumentException::class);
        $response = $this->get($url);

        // then
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_reject_dates_in_the_past()
    {
        // given
        $dateStart = $this->faker()->dateTimeBetween('-20days', '-10 days');
        $dateEnd = $this->faker()->dateTimeBetween('-5days', '+0 days');
        $url = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}&dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        // when
        $this->expectException(InvalidArgumentException::class);
        $response = $this->get($url);

        // then
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_handles_correct_requests()
    {
        // given
        $dateStart = $this->faker()->dateTimeBetween('+5 days', '+10 days');
        $dateEnd = $this->faker()->dateTimeBetween('+10 days', '+20 days');
        $url = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}&dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        // when
        $response = $this->post($url, [
            'email' => $this->faker()->safeEmail()
        ]);

        // then
        $response->assertStatus(Response::HTTP_OK);
        static::assertEquals($response['dateStart'], $dateStart->format(self::DATE_FORMAT));
        static::assertEquals($response['dateEnd'], $dateEnd->format(self::DATE_FORMAT));
    }

    public function test_correctly_identify_booked_slots()
    {
        // given
        $dateStart = $this->faker()->dateTimeBetween('+5 days', '+10 days');
        $dateEnd = $this->faker()->dateTimeBetween('+10 days', '+20 days');

        /** @var \Illuminate\Database\Eloquent\Model $booking */
        $booking = Booking::factory([
            'booked' => true,
            'dateStart' => $dateStart->format(self::DATE_FORMAT),
            'dateEnd' => $dateEnd->format(self::DATE_FORMAT),
        ]);

        $url = self::API_ENDPOINT . "?dateStart={$dateStart->format(self::DATE_FORMAT)}&dateEnd={$dateEnd->format(self::DATE_FORMAT)}";

        // when
        $response = $this->get($url);

        // then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        static::assertEquals($response->getContent(), json_encode($booking->toArray()));
    }

}

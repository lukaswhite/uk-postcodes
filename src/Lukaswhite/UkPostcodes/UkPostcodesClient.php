<?php namespace Lukaswhite\UkPostcodes;

use Guzzle\Http\Client;

/**
 * Web Service client for the uk-postcodes.com web service.
 */
class UkPostcodesClient {

	/**
	 * @var string
	 * 	The base URI for the web service.
	 */
	const BASE_URI = 'http://uk-postcodes.com/';

	/**
	 * @var Guzzle\Http\Client
	 */
	private $client;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Instantiate the Guzzle client
		$this->client = new Client(self::BASE_URI);
	}


	/**
	 * Geocode a postcode
	 *
	 * @param string $postcode
	 * @return UkPostcode
	 */
	public function postcode($postcode)
	{
		// Remove spaces, convert to uppercase
		$postcode = strtoupper(str_replace(' ', '', $postcode));

		// Construct the web service URL
		$url = sprintf('/postcode/%s.%s', $postcode, 'json');

		// Make the request
		$request = $this->client->get($url);
		
		try {
			$response = $request->send();

			// Create a new UkPostcode
			$postcode = UkPostcode::fromJson($response->getBody());
			
			return $postcode;

		} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
			return null;
		}
	}

	/**
	 * Get the nearest postcode to a given point.
	 *
	 * @param array|League\Geotools\Coordinate\Coordinate|string $to
	 * @return UkPostcode
	 * @throws League\Geotools\Exception\InvalidArgumentException
	 */
	public function getNearest($to)
	{
		if (!$to instanceof \League\Geotools\Coordinate\Coordinate) {
			// If this fails, it'll throw a League\Geotools\Exception\InvalidArgumentException
			$to = new \League\Geotools\Coordinate\Coordinate($to);
		}

		$url = sprintf('latlng/%f,%f.%s', $to->getLatitude(), $to->getLongitude(), 'json');

		// Make the request
		$request = $this->client->get($url);
		$response = $request->send();
		
		$postcode = UkPostcode::fromJson($response->getBody());
		
		return $postcode;
	}

	/**
	 * Get postcode data wthin x miles of a given postcode or lat/lng
	 *
	 * @param UkPostcode|array|League\Geotools\Coordinate\Coordinate|string $to
	 * @return array
	 * @throws League\Geotools\Exception\InvalidArgumentException
	 */
	public function getWithin($point, $distance = 5)
	{
		if ($point instanceof UkPostcode) {
			$point = $point->getCoordinate();
		} elseif (!$point instanceof \League\Geotools\Coordinate\Coordinate) {
			// If this fails, it'll throw a League\Geotools\Exception\InvalidArgumentException
			$point = new \League\Geotools\Coordinate\Coordinate($point);
		}

		// Create the request
		$request = $this->client->get('postcode/nearest');

		// This one's slightly different, in that the parameters are all GET params
		$request->getQuery()
				->set('lat', $point->getLatitude())
        ->set('lng', $point->getLatitude())
        ->set('distance', $distance)
        ->set('format', 'json');

    // ...and send
		$response = $request->send();

		return $response->json();
		
	}

}

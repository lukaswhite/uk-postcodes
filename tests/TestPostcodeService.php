<?php

class TestPostcodeService extends \PHPUnit_Framework_TestCase {

	/**
	 * Test geocoding a valid postcode
	 */
	public function testValidPostcode()
	{
		$str = 'GL9 1AH';

		$client = new Lukaswhite\UkPostcodes\UkPostcodesClient();

		$postcode = $client->postcode($str);

		$this->assertInstanceOf('Lukaswhite\UkPostcodes\UkPostcode', $postcode);
		$this->assertEquals('GL9 1AH', $postcode->postcode);

		$coordinate = $postcode->getCoordinate();

		$this->assertInstanceOf('League\Geotools\Coordinate\Coordinate', $coordinate);
		// Check the coordinates are the same, albeit we'll round them
		$this->assertEquals(52, round($coordinate->getLatitude()));  // rounded up
		$this->assertEquals(-2, round($coordinate->getLongitude()));

		$this->assertEquals('http://geohash.org/gcnm7vz09678', $postcode->geohash);
		$this->assertEquals('South Gloucestershire', $postcode->council->title);
	}

	/**
	 * Test the behaviour of the client when an invalid postcode is used.
	 */
	public function testInvalidPostcode()
	{
		// Not a valid postcode
		$str = 'Postcode';

		// so we can try geocoding it...
		$client = new Lukaswhite\UkPostcodes\UkPostcodesClient();

		// ...but it will only return a NULL
		$this->assertNull($client->postcode($str));
	}


}
<?php

class TestUkPostcodeClass extends \PHPUnit_Framework_TestCase {

	/**
	 * Test the class instantiation
	 */
	public function testInstantiate()
	{
		$postcode = new Lukaswhite\UkPostcodes\UkPostcode('GL9 1AH');
		$this->assertInstanceOf('Lukaswhite\UkPostcodes\UkPostcode', $postcode);
		$this->assertEquals('GL9 1AH', $postcode->postcode);
	}

	/**
	 * Test the getOutcode() function
	 */
	public function testOutcode()
	{
		$postcode = new Lukaswhite\UkPostcodes\UkPostcode('GL9 1AH');		
		$this->assertEquals('GL9', $postcode->getOutcode());	
	}

	/**
	 * Test the getCoordiate() function
	 */
	public function testCoordinate()
	{
		$postcode = new Lukaswhite\UkPostcodes\UkPostcode('GL9 1AH');		
		$this->assertInstanceOf('League\Geotools\Coordinate\Coordinate', $postcode->getCoordinate());
	}

	/**
	 * Test the fromJson factory method
	 */
	public function testFromJson()
	{
		$json = '{"postcode":"GL9 1AH","geo":{"lat":51.57953522690476,"lng":-2.286471165544967,"easting":380245.0,"northing":186780.0,"geohash":"http://geohash.org/gcnm7vz09678"},"administrative":{"council":{"title":"South Gloucestershire","uri":"http://statistics.data.gov.uk/id/statistical-geography/E06000025","code":"E06000025"},"ward":{"title":"Cotswold Edge","uri":"http://statistics.data.gov.uk/id/statistical-geography/E05002051","code":"E05002051"},"constituency":{"title":"Thornbury and Yate","uri":"http://statistics.data.gov.uk/id/statistical-geography/E14000994","code":"E14000994"}}}';
		$postcode = Lukaswhite\UkPostcodes\UkPostcode::fromJson($json);
		$this->assertInstanceOf('Lukaswhite\UkPostcodes\UkPostcode', $postcode);
		$this->assertEquals('GL9 1AH', $postcode->postcode);
		$coordinate = $postcode->getCoordinate();

		// Check the coordinates are the same, albeit we'll round them
		$this->assertEquals(52, round($coordinate->getLatitude()));  // rounded up
		$this->assertEquals(-2, round($coordinate->getLongitude()));

		$this->assertEquals('http://geohash.org/gcnm7vz09678', $postcode->geohash);
		$this->assertEquals('South Gloucestershire', $postcode->council->title);
	}

}
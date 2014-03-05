<?php namespace Lukaswhite\UkPostcodes;

/**
 * UkPostcode class
 *
 * Represents a UK Postcode
 */
class UkPostcode {

	public $postcode;
	private $lat;
	private $lng;
	private $easting;
	private $nothing;
	public $geohash;
	public $council;
	public $ward;
	public $constituency;

	public function __construct($postcode)
	{
		$this->postcode = $postcode;
	}

	/**
	 * Utility function; validate the supplied postcode.
	 * 
	 * @param string $postcode
	 * @return bool
	 */
	public static function validate($postcode)
	{
		if (preg_match('/^\s*(([A-Z]{1,2})[0-9][0-9A-Z]?)\s*(([0-9])[A-Z]{2})\s*$/', str_replace(' ', '', strtoupper($postcode)))) {
			return true;
		}
		return false;
	}


	/**
	 * Factory method; create an instance from JSON
	 */
	public static function fromJson($json)
	{
		// Decode the JSON
		$object = json_decode($json);

		// Create it
		$postcode = new self($object->postcode);
		
		$postcode->lat = $object->geo->lat;
		$postcode->lng = $object->geo->lng;
		$postcode->geohash = $object->geo->geohash;
		$postcode->council = $object->administrative->council;
		$postcode->ward = $object->administrative->ward;
		$postcode->constituency = $object->administrative->constituency;

		return $postcode;
	}

	/**
	 * Get the outcode; that is, the first part of the postcode.
	 *
	 * @return string
	 */
	public function getOutcode()
	{
		// Format the postcode first		
    $formatted_postcode = strtoupper(str_replace(' ', '', $this->postcode));

    // The outcode is the postcode less the last three characters
    return substr($formatted_postcode, 0, (strlen($formatted_postcode) - 3));       
    
	}

	/**
	 * Return a coordinate object for this postcode.
	 *
	 * @return League\Geotools\Coordinate\Coordinate
	 */
	public function getCoordinate()
	{
		return new \League\Geotools\Coordinate\Coordinate(array($this->lat, $this->lng));
	}

}

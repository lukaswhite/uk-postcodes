<?php namespace Lukaswhite\UkPostcodes;

/**
 * UkPostcode class
 *
 * Represents a UK Postcode
 */
class UkPostcode {

	/**
	 * String representation of the postcode
	 * 
	 * @var string
	 */	
	public $postcode;

	/**
	 * The latitude
	 * 
	 * @var float
	 */
	private $lat;

	/**
	 * The longitude
	 * 
	 * @var float
	 */
	private $lng;

	/**
	 * The Easting
	 * 
	 * @var integer
	 */
	private $easting;

	/**
	 * The Northing
	 * 
	 * @var string
	 */
	private $nothing;

	/**
	 * The Geohash (ref: https://en.wikipedia.org/wiki/Geohash)
	 * 
	 * @var string
	 */
	public $geohash;

	/**
	 * The name of the council
	 * 
	 * @var string
	 */
	public $council;

	/**
	 * The name of the ward
	 * 
	 * @var string
	 */
	public $ward;

	/**
	 * The name of the parliamentary consistency
	 * 
	 * @var string
	 */
	public $constituency;

	/**
	 * The path to the postcode data file
	 * 
	 * @var string
	 */
	private static $databasePath;
	
	/**
	 * Field lengths, used to interpret the data file
	 */
	const DISTRICT_LENGTH  = 4;
	const POST_TOWN_LENGTH = 22;
	const RECORD_LENGTH    = 26;

	/**
	 * Constructor
	 *
	 * Create an instance by providing a string representation of the postcode; it's fairly lax about the format.
	 * So in other words, it'll accept any of the following:
	 *
	 *   GL9 1AH
	 *   gl91ah
	 *   Gl91Ah
	 *   gl  9 1 a    h
	 *   
	 * @param string $postcode
	 */
	public function __construct($postcode)
	{
		$this->postcode = $postcode;
	}

	/**
	 * Utility (static) function; validate the supplied postcode.
	 * 
	 * @param string $postcode
	 * @return bool
	 */
	public static function validate($postcode)
	{
		// Need to do an assignment, because we're going to pass by reference
		$toCheck = $postcode;

		// Thr format function validates the postcode.
		return self::format($toCheck);
	}
	

	/**
	 * Utility (static) function; format the supplied postcode.
	 * 
	 * @param string $toCheck
	 * @return bool
	 */
	public static function format(&$toCheck)
	{		

	  // Permitted letters depend upon their position in the postcode.
	  $alpha1 = "[abcdefghijklmnoprstuwyz]";                          // Character 1
	  $alpha2 = "[abcdefghklmnopqrstuvwxy]";                          // Character 2
	  $alpha3 = "[abcdefghjkpmnrstuvwxy]";                            // Character 3
	  $alpha4 = "[abehmnprvwxy]";                                     // Character 4
	  $alpha5 = "[abdefghjlnpqrstuwxyz]";                             // Character 5
	  $BFPOa5 = "[abdefghjlnpqrst]{1}";                               // BFPO character 5
	  $BFPOa6 = "[abdefghjlnpqrstuwzyz]{1}";                          // BFPO character 6
	  
	  // Expression for BF1 type postcodes 
	  $pcexp[0] =  '/^(bf1)([[:space:]]{0,})([0-9]{1}' . $BFPOa5 . $BFPOa6 .')$/';
	  
	  // Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA with a space
	  $pcexp[1] = '/^('.$alpha1.'{1}'.$alpha2.'{0,1}[0-9]{1,2})([[:space:]]{0,})([0-9]{1}'.$alpha5.'{2})$/';

	  // Expression for postcodes: ANA NAA
	  $pcexp[2] =  '/^('.$alpha1.'{1}[0-9]{1}'.$alpha3.'{1})([[:space:]]{0,})([0-9]{1}'.$alpha5.'{2})$/';

	  // Expression for postcodes: AANA NAA
	  $pcexp[3] =  '/^('.$alpha1.'{1}'.$alpha2.'{1}[0-9]{1}'.$alpha4.')([[:space:]]{0,})([0-9]{1}'.$alpha5.'{2})$/';
	  
	  // Exception for the special postcode GIR 0AA
	  $pcexp[4] =  '/^(gir)([[:space:]]{0,})(0aa)$/';
	  
	  // Standard BFPO numbers
	  $pcexp[5] = '/^(bfpo)([[:space:]]{0,})([0-9]{1,4})$/';
	  
	  // c/o BFPO numbers
	  $pcexp[6] = '/^(bfpo)([[:space:]]{0,})(c\/o([[:space:]]{0,})[0-9]{1,3})$/';
	  
	  // Overseas Territories
	  $pcexp[7] = '/^([a-z]{4})([[:space:]]{0,})(1zz)$/';
	  
	  // Anquilla
	  $pcexp[8] = '/^ai-2640$/';

	  // Load up the string to check, converting into lowercase
	  $postcode = str_replace(' ', '', strtolower($toCheck));

	  // Assume we are not going to find a valid postcode
	  $valid = false;
	  
	  // Check the string against the six types of postcodes
	  foreach ($pcexp as $regexp) {
	  
	    if (preg_match($regexp,$postcode, $matches)) {
	    
	      // Load new postcode back into the form element  
			  $postcode = strtoupper ($matches[1] . ' ' . $matches [3]);
				
	      // Take account of the special BFPO c/o format
	      $postcode = preg_replace ('/C\/O([[:space:]]{0,})/', 'c/o ', $postcode);
	      
	      // Take acount of special Anquilla postcode format (a pain, but that's the way it is)
	      if (preg_match($pcexp[7],strtolower($toCheck), $matches)) $postcode = 'AI-2640';      
	      
	      // Remember that we have found that the code is valid and break from loop
	      $valid = true;
	      break;
	    }
	  }
	    
	  // Return with the reformatted valid postcode in uppercase if the postcode was 
	  // valid
	  if ($valid){
		  $toCheck = $postcode; 
			return true;
		} 
		else return false;
	
	}

	/**
	 * Validates this postcode.
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return self::validate($this->postcode);
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
		
		// Populate it
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
	 * Returns the inward code
	 * 
	 * @return string
	 */
	public function getInwardCode()
	{
		// Format the postcode first		
    $formatted_postcode = strtoupper(str_replace(' ', '', $this->postcode));

    // Get the last three characters
    return substr($formatted_postcode, (strlen($formatted_postcode) - 3));       
	}

	/**
	 * Get the postcode sector
	 *
	 * e.g. GL9 1 	 
	 * 
	 * @return string
	 */
	public function getSector()
	{
		// The sector is simply outcode + space + first digit of the inward code
		return sprintf( '%s %s', $this->getOutcode(), substr( $this->getInwardCode(), 0, 1 ) );
	}

	/**
	 * Formats this postcode.
	 *
	 * @return string
	 */
	public function formatted()
	{
		$postcode = $this->postcode;
		self::format($postcode);
		return $postcode;
	}

	/**
	 * Magic method; returns a string representation of this postcode
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->formatted( );
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

	/* 
	 * Returns the post town for a postcode, or the empty string if the post town
   * could not be determined.
   *
   * This function is based on http://code.stephenmorley.org/php/handling-uk-postcodes/ -- so all credit for most of
   * this particular function, and the accompanying data file, is due to Stephen.
   *
   * $postcode - the postcode whose post town should be returned
   */
	public function getPostTown()
	{
		$district = $this->getOutcode();

    // check whether the path to the database has been initialised
    if (!self::$databasePath){

      // initialise the path to the database
      self::$databasePath =
          dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'Postcode.data';

      // throw an error if the database is missing
      if (!is_file(self::$databasePath)){      	
        trigger_error(
            'Postcode.data not found - ensure you have downloaded the database as well as the PHP class',
            E_USER_ERROR);
      }

    }

    // open the database
    $database = fopen(self::$databasePath, 'r');

    // initialise the search bounds
    $start = 0;
    $end   = filesize(self::$databasePath) / self::RECORD_LENGTH - 1;

    // loop until the bounds have crossed over
    do{

      // determine the index of the midpoint
      $midpoint = ($start + $end) >> 1;

      // determine the district for the midpoint
      fseek($database, $midpoint * self::RECORD_LENGTH);
      $midpointDistrict = rtrim(fread($database, self::DISTRICT_LENGTH));

      // update the bounds or store the post town depending on the district
      if ($district < $midpointDistrict){
        $end = $midpoint - 1;
      }elseif ($district > $midpointDistrict){
        $start = $midpoint + 1;
      }else{
        $postTown = rtrim(fread($database, self::POST_TOWN_LENGTH));
        break;
      }

    }while ($start <= $end);

    // close the database
    fclose($database);

    // return the post town
    return (isset($postTown) ? $postTown : '');

  
	}

}

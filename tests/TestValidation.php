<?php

class TestValidation extends \PHPUnit_Framework_TestCase {

	/**
	 * Try some valid postcodes.
	 */
	public function testValid()
	{
		$this->assertTrue(Lukaswhite\UkPostcodes\UkPostcode::validate('GL9 1AH'));
		$this->assertTrue(Lukaswhite\UkPostcodes\UkPostcode::validate('gl91ah'));
		$this->assertTrue(Lukaswhite\UkPostcodes\UkPostcode::validate('Gl91Ah'));
		$this->assertTrue(Lukaswhite\UkPostcodes\UkPostcode::validate('gl  9 1 a    h'));
		$this->assertTrue(Lukaswhite\UkPostcodes\UkPostcode::validate('SW95 9DH'));
		
		// @todo This postcode doesn't actually exist, but the forma is perfectly valid. Should it pass or fail?
		$this->assertTrue(Lukaswhite\UkPostcodes\UkPostcode::validate('SW50 9DH'));
	}

	/**
	 * Try some invalid postcodes.
	 */
	public function testInvalid()
	{
		$this->assertFalse(Lukaswhite\UkPostcodes\UkPostcode::validate('postcode'));
		$this->assertFalse(Lukaswhite\UkPostcodes\UkPostcode::validate('P05T C0DE'));		
	}

}
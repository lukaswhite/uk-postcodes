# UK Postcodes

[![CircleCI](https://circleci.com/gh/lukaswhite/uk-postcodes.svg?style=svg)](https://circleci.com/gh/lukaswhite/uk-postcodes)

This package serves two purposes:

1. A simple class which represents a UK Postcode and contains functionality such as formatting and validation.
2. A wrapper for [this web service](http://www.uk-postcodes.com/api) for geocoding postcodes.

## Installation

Via Composer:

    "lukaswhite/uk-postcodes" : "dev-master"

## UkPostcode Class

A simple class to encapsulate a UK postcode. While it's primarily used as the return format for the web service, it does have the odd use in itself.

### Constructor

    $postcode = new Lukaswhite\UkPostcodes\UkPostcode('sw1a2aa');

### Validation
        
    if ($postcode->isValid()) {
        // do something...
    }

Alternatively, use the static method:

    if (Lukaswhite\UkPostcodes\UkPostcode::validate('sw1a2aa')) {
        // do something...
    }

### Formatting

    $postcode = new Lukaswhite\UkPostcodes\UkPostcode('sw1a2aa');

    print $postcode->formatted();

    // > "SW1A 2AA"

### Outcodes

The outcode is the first part of a UK postcode. To illustrate:

    $postcode = new Lukaswhite\UkPostcodes\UkPostcode('sw1a2aa');
    print $postcode->getOutcode();
    // SW1A

    $postcode = new Lukaswhite\UkPostcodes\UkPostcode('GL9 1AH');
    print $postcode->getOutcode();
    // GL9

    $postcode = new Lukaswhite\UkPostcodes\UkPostcode('gl91ah');
    print $postcode->getOutcode();
    // GL9

## The Web Service Client

Provides wrapper for [this web service](http://www.uk-postcodes.com/api) for geocoding postcodes.

### Geocoding a Postcode

It's probably easiest to demonstrate the usage through an example:

    // Create the client
    $client = new Lukaswhite\UkPostcodes\UkPostcodesClient();       

    // Call the web service
    $postcode = $client->postcode('sw1a2aa');

    print get_class($postcode);
    // Lukaswhite\UkPostcodes\UkPostcode

    print $postcode->formatted();
    // SW1A 2AA

    print get_class($postcode->getCoordinate());
    // League\Geotools\Coordinate\Coordinate

    print $postcode->getCoordinate()->getLatitude();
    // 51.503539898876

    print $postcode->getCoordinate()->getLongitude();
    // -0.12768084037293

    print get_class($postcode->getCoordinate()->getEllipsoid());
    // League\Geotools\Coordinate\Ellipsoid 

    print $postcode->council->title;
    // City of Westminster

    print $postcode->council->code;
    // E09000033

    print $postcode->council->uri;
    // http://statistics.data.gov.uk/id/statistical-geography/E09000033

    print $postcode->ward->title;
    // St. James's

    print $postcode->constituency->title;
    // Cities of London and Westminster

If the postcode cannot be found for whatever reason, it'll simply return NULL.

### Nearest Postcode

You can get the nearest postcode to a given point using `getNearest()`.

Specify the point using either an instance of `League\Geotools\Coordinate\Coordinate` or an array of lat / lng, e.g. `[51.503539898876, -0.12768084037293]`.

    // Create the client
    $client = new Lukaswhite\UkPostcodes\UkPostcodesClient();       

    $to = new League\Geotools\Coordinate\Coordinate([51.503539898876, -0.12768084037293]);

    $postcode = $client->getNearest($to);

    print $postcode->formatted();

    // SW1A 2AA

### Postcodes within x miles

Get the postcodes within x miles of a given point using `getWithin()`.  First parameter is a point, second is the number of miles (5 miles maxiumum).

    // Create the client
    $client = new Lukaswhite\UkPostcodes\UkPostcodesClient();       

    $location = new League\Geotools\Coordinate\Coordinate([51.503539898876, -0.12768084037293]);

    $postcodes = $client->getWithin($location, 3);

    // returns an array of all postcodes within 3 miles of Lat 51.503539898876, Long -0.12768084037293

## Testing

There are a bunch of unit tests (PHPUnit) in `/tests`.

## Todo

Better error handling.

Caching.

Unit tests for `getNearest()` and `getWithin()`.

There is a function currently called `getPostTown()` adapted from [this site](http://code.stephenmorley.org/php/handling-uk-postcodes/), but it doesn't appear to be 100% reliable.  When I get time, I'll attempt to fix this.  
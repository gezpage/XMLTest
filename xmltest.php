<?php

/**
 * xmltest.php
 *
 * An exercise in XML creation / parsing, date manipulation, and arithmetic in PHP
 *
 * Create XML document containing every 30th June since the unix epoch at 1pm GMT.
 *
 * Parse generated XML document and create another XML document with timestamps
 * in descending order, in Pacific Standard Time (PST), excluding timestamps with
 * prime number years.
 *
 * Intended to be executed from the command line ONLY
 *
 * Command line usage:
 *      php xmltest.php create <filename> <timezone>
 *      php xmltest.php convert <filename> <timezone>
 *
 * (timezone is optional - create uses GMT, convert uses PST as a default)
 *
 */

// Check we are running from the command line
if ('cli' != php_sapi_name()) {
        die('This script is intended to be run from the command line only.');
}

// Check required parameters
if (!(isset($argv[1]) && isset($argv[2]))) {
        // Need at least 2 params, show usage guidelines only
        XMLTest::usage();
        exit;
}

// TimestampXMLwriter class
require_once 'TimestampXMLwriter.php';

// Route to desired action
switch($argv[1]) {
        case 'create':
                // Use timezone if provided, or default to GMT
                $timezone = @XMLTest::validate_timezone($argv[3] ?: 'GMT');
                XMLTest::create($argv[2], $timezone);
        break;
        case 'convert':
                // Use timezone if provided, or default to PST
                $timezone = @XMLTest::validate_timezone($argv[3] ?: 'PST');
                XMLTest::convert($argv[2], $timezone);
        break;
        default:
                // Invalid action
                XMLTest::usage();
        break;
}

class XMLTest
{

        // Create timestamp XML file
        static function create($file, $timezone) {
                // Prepare to build XML document
                $xml = new TimestampXMLwriter();
                $xml->setTimezone($timezone);

                // Start date is the first 30th June after the unix epoch
                $start_date = '1970-06-30 13:00:00';
                $timestamp = strtotime($start_date);

                // Loop through adding timestamps to the XML instance
                // strtotime can be expensive if used extensively, consider
                // refactoring using string manipulation to increment year in
                // the date, for now it's quick and simple to use here.
                while ($timestamp < strtotime('now')) {
                        $xml->addTimestamp($timestamp);
                        $timestamp = strtotime('+1 year', $timestamp);
                }

                // Ensure file is saved using same location as current script
                $save_file = dirname(__FILE__) . '/' . $file;
                $xml->saveDocument($save_file);
                echo "XML file saved to $save_file\n";
        }

        // Convert XML file to new format
        static function convert($file, $timezone) {
                // Use simplexml to parse the XML file
                $xml = simplexml_load_file((dirname(__FILE__)) . '/' . $file);

                // Convert into numeric array of timestamp strings
                $timestamps = array();
                foreach ($xml as $x) $timestamps[] = (string) $x['time'];

                // Prepare to build XML document
                $xml = new TimestampXMLwriter();
                $xml->setTimezone($timezone);
                $xml->ignore_prime_years = true;

                // Reverse order and loop through timestamp array
                foreach (array_reverse($timestamps) as $timestamp) {
                        $xml->addTimestamp($timestamp);
                }

                // Ensure file is saved using same location as current script
                $save_file = dirname(__FILE__) . '/converted_' . $file;
                $xml->saveDocument($save_file);
                echo "XML file saved to $save_file\n";
        }

        // Display usage guidelines
        static function usage() {
                echo "\n xmltest.php - Create and parse XML timestamp documents.\n\n"
                        . " Command line usage:\n\n"
                        . " php xmltest.php create <filename> <timezone>\n"
                        . " php xmltest.php convert <filename> <timezone>\n\n"
                        . " (timezone is optional - create uses GMT, convert uses PST as a default)\n\n";

        }

        // Resolve allowed timezone code to PHP supported timezone string
        static function validate_timezone($timezone) {
                // Only allow GMT or PST - more can be added here
                $allowed = array(
                        'PST' => 'America/Los_Angeles',
                        'GMT' => 'GMT',
                );
                // Default to GMT
                return $allowed[$timezone] ?: 'GMT';
        }
}

?>

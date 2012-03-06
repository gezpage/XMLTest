<?php

require_once 'TimestampXMLwriter.php';

if (!(isset($argv[1]) && isset($argv[2]))) {
        // Need at least 2 params, show usage guidelines only
        XMLTest::usage();
        exit;
}

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
                XMLTest::usage();
        break;
}

class XMLTest {

        static function create($file, $timezone) {
                $xml = new TimestampXMLwriter();
                $xml->setTimezone($timezone);

                // Start date is the first 30th June after the unix epoch
                $start_date = '1970-06-30 13:00:00';
                $timestamp = strtotime($start_date);

                while ($timestamp < strtotime('now')) {
                        $xml->addTimestamp($timestamp);
                        // Increment year for next iteration
                        $timestamp = strtotime('+1 year', $timestamp);
                }

                $save_file = dirname(__FILE__) . '/' . $file;

                $xml->saveDocument($save_file);
                echo "XML file saved to $save_file\n";
        }

        static function convert($file, $timezone) {
                // Use simplexml to parse the XML file
                $xml = simplexml_load_file((dirname(__FILE__)) . '/' . $file);

                $timestamps = array();
                // Convert into numeric array
                foreach ($xml as $x) $timestamps[] = (string) $x['time'];

                $xml = new TimestampXMLwriter();
                $xml->setTimezone($timezone);
                $xml->ignore_prime_years = true;

                foreach (array_reverse($timestamps) as $timestamp) {
                        $xml->addTimestamp($timestamp);
                }

                $save_file = dirname(__FILE__) . '/converted_' . $file;

                $xml->saveDocument($save_file);
                echo "XML file saved to $save_file\n";
        }

        static function usage() {
                echo "Command line usage:\n"
                        . " php xmltest.php create <filename>\n"
                        . " php xmltest.php convert <filename>\n";
        }

        static function validate_timezone($timezone) {
                // Only allow GMT or PST
                $allowed = array(
                        'PST' => 'America/Los_Angeles',
                        'GMT' => 'GMT',
                );
                // Default to GMT
                return $allowed[$timezone] ?: 'GMT';
        }
}


?>

<?php

include 'TimestampXMLwriter.php';
include 'math_functions.php';

if (!(isset($argv[1]) && isset($argv[2]))) {
        XMLTest::usage();
        exit;
}

$timezone = @XMLTest::validate_timezone($argv[3]);

switch($argv[1]) {
        case 'create':
                XMLTest::create($argv[2], $timezone);
        break;
        case 'convert':
                XMLTest::convert($argv[2]);
        break;
        default:
                XMLTest::usage();
        break;
}

class XMLTest {

        static function gnocreate($file, $timezone) {
                $xml = new TimestampXMLwriter();
                $xml->setTimezone($timezone);

                // Start date is the first 30th June after the unix epoch
                $start_date = '1970-06-30 13:00:00';
                $timestamp = strtotime($start_date);

                while ($timestamp < strtotime('now')) {
                        $xml->addTimestamp($timestamp);
                        // [FIXME] strtotime is heavy going - use simple string
                        // manipulation instead
                        $timestamp = strtotime('+1 year', $timestamp);
                }

                $save_file = dirname(__FILE__) . '/' . $file;

                if (file_put_contents($save_file, $xml->getDocument())) {
                        echo "XML file saved to $save_file\n";
                }
        }

        static function convert($file, $timezone) {
                // Use simplexml to parse the XML file
                $xml = simplexml_load_file((dirname(__FILE__)) . '/' . $file);

                $timestamps = array();
                // Convert into numeric array
                foreach ($xml as $x) $timestamps[] = (string) $x['time'];

                $xml = new TimestampXMLwriter();
                $xml->setTimezone($timezone);

                foreach (array_reverse($timestamps) as $timestamp) {
                        $xml->addTimestamp($timestamp);
                }

                if (file_put_contents('converted_' . $save_file, $xml->getDocument())) {
                        echo "XML file saved to $save_file\n";
                }
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

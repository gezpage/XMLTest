<?php

include 'XMLTimestamps.php';

if (!(isset($argv[1]) && isset($argv[2]))) {
        XMLTest::usage();
        exit;
}

switch($argv[1]) {
        case 'create':
                XMLTest::create($argv[2]);
        break;
        case 'import':
                XMLTest::import($argv[2]);
        break;
        default:
                XMLTest::usage();
        break;
}

class XMLTest {

        static function create($file) {
                $xml = new XMLTimestamps();

                $start_date = date('Y') . '-06-30 13:00:00';
                $unixdate = strtotime($start_date);

                while ($unixdate > 0)
                {
                        if ($unixdate < strtotime('now'))
                        {
                                $xml->addTimestamp($unixdate);
                        }
                        $unixdate = strtotime('-1 month', $unixdate);
                }

                $save_file = dirname(__FILE__) . '/' . $file;

                if (file_put_contents($save_file, $xml->getDocument())) {
                        echo "XML file saved to $save_file\n";
                }
        }

        static function import($file) {
                //
                echo 'not yet implemented';
        }

        static function usage() {
                echo "Command line usage:\n"
                        . " php xmltest.php create <filename>\n"
                        . " php xmltest.php import <filename>\n";
        }
}


?>

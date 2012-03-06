<?php

require_once 'math_functions.php';

class TimestampXMLWriter extends XMLWriter
{

        /**
         * Constructor.
         * @access public
         * @param null
         */

        protected $date;
        public $ignore_prime_years = false;

        public function __construct() {
                $this->date = new DateTime;
                $this->openMemory();
                $this->setIndent(true);
                $this->setIndentString(' ');
                $this->startDocument('1.0', 'UTF-8');
                $this->startElement('timestamps');
        }
        public function setTimezone($timezone) {
                $this->date->setTimezone(new DateTimeZone($timezone));
        }
        public function addTimestamp($timestamp) {
                if ($this->ignore_prime_years) {
                        // Skip if year is prime
                        if ($this->isYearPrime($timestamp)) return;
                }
                $this->startElement('timestamp');
                $this->writeAttribute('time', $timestamp);
                $this->writeAttribute('text', $this->makeDate($timestamp));
                $this->endElement();
        }
        public function getDocument() {
                $this->endElement();
                $this->endDocument();
                return $this->outputMemory();
        }
        public function outputDocument() {
                header('Content-type: text/xml');
                echo $this->getDocument();
        }
        public function saveDocument($file) {
                return file_put_contents($file, $this->getdocument());
        }
        protected function makeDate($timestamp) {
                $this->date->setTimestamp($timestamp);
                return $this->date->format('Y-m-d H:i:s');
        }
        protected function isYearPrime($timestamp) {
                $this->date->setTimestamp($timestamp);
                return is_prime($this->date->format('Y'));
        }
}

?>

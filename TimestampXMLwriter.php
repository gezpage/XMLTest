<?php

/**
 * TimestampXMLwriter.php
 *
 * Create XML documents containing timestamp elements
 * Extends the XMLWriter PHP extension
 *
 * Allows returning XML document as string, saving to a file, or can output
 * document with correct XML headers
 *
 */

require_once 'math_functions.php';

class TimestampXMLWriter extends XMLWriter
{

        // DateTime object created in Constructor
        private $date;

        // Switch for ignoring prime years - disabled by default
        public $ignore_prime_years = false;

        // Configure XMLWriter on instantiation
        public function __construct() {
                $this->date = new DateTime;
                $this->openMemory();
                $this->setIndent(true);
                $this->setIndentString(' ');
                $this->startDocument('1.0', 'UTF-8');
                $this->startElement('timestamps');
        }
        // Set timezone to be used in calls to addTimestamp
        public function setTimezone($timezone) {
                $this->date->setTimezone(new DateTimeZone($timezone));
        }
        // Add <stimestamp> element to XML document
        public function addTimestamp($timestamp) {
                // Set DateTime object to current timestamp
                $this->date->setTimestamp($timestamp);
                if ($this->ignore_prime_years) {
                        // Skip if year is prime
                        if ($this->isYearPrime()) return;
                }
                // Create timestamp XML element with time & text attributes
                $this->startElement('timestamp');
                $this->writeAttribute('time', $timestamp);
                $this->writeAttribute('text', $this->makeDate($timestamp));
                $this->endElement();
        }
        // Return the XML document as a string
        public function getDocument() {
                $this->endElement();
                $this->endDocument();
                return $this->outputMemory();
        }
        // Output XML document with headers
        // Do not call if output already sent
        public function outputDocument() {
                header('Content-type: text/xml');
                echo $this->getDocument();
        }
        // Write XML document to specified filename
        public function saveDocument($file) {
                return file_put_contents($file, $this->getdocument());
        }
        // Return current DateTime as a string
        protected function makeDate($timestamp) {
                return $this->date->format('Y-m-d H:i:s');
        }
        // Return true if year is a prime number, else false
        protected function isYearPrime() {
                return is_prime($this->date->format('Y'));
        }
}

?>

<?php

class XMLTimestamps extends XMLWriter
{

        /**
         * Constructor.
         * @access public
         * @param null
         */

        //protected $timezone = 'GMT';
        protected $date;

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
        public function output() {
                header('Content-type: text/xml');
                echo $this->getDocument();
        }
        protected function makeDate($timestamp) {
                $this->date->setTimestamp($timestamp);
                return $this->date->format('Y-m-d H:i:s');
        }
}

?>

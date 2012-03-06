<?php

class XMLTimestamps extends XMLWriter
{

        /**
         * Constructor.
         * @access public
         * @param null
         */

        public function __construct() {
                $this->openMemory();
                $this->setIndent(true);
                $this->setIndentString(' ');
                $this->startDocument('1.0', 'UTF-8');
                $this->startElement('timestamps');
        }
        public function addTimestamp($timestamp) {
                $this->startElement('timestamp');
                $this->writeAttribute('time', $timestamp);
                $this->writeAttribute('text', date('Y-m-d h:i:s', $timestamp));
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
}

?>

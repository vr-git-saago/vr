<?php
    // kui fail koosneb ainult php'st siis pole php lõpu märgendit vaja
    class Test {
        // muutujad ehk properties
        private $secret = 7;
        public $non_secret = 3;
        private $received_secret;
        
        // funktsioonid ehk methods
        // kohe kui klass kasutusele võtta läheb käima konstruktor
        function __construct($received) {
            echo "Klass on laetud! Konstruktor töötab!";
            // muutujatele tuleb ka viidata, et mis klass seda teeme siis selle $this muutujaga
            $this->received_secret = $received;
            // klass oma private muujatele saab ligi, kui näitaks väljas pool klassi ei saa
            echo "Saabunud salajane number on " .$this->received_secret .". ";
            $this->multiplay();
        }
        // destruct on vastupidine constructile. Tema käivitub kui klassi töö lõpeb ehk object eemaldatakse
        function __destruct () {
            echo "Klass lõpetas!";
        }

        public function reveal () {
            echo "Täiesti salajane number on " . $this->secret .". ";
        }

        private function multiplay () {
            echo "Korrutis on: " .$this->secret * $this->non_secret * $this->received_secret;
        }
    } // class lõppeb
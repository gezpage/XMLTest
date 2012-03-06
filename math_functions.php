<?php

function is_prime($number) {
        if ($number == 1) {
                // 1 Can never be prime
                return false;
        }
        if ($number == 2) {
                // 2 is prime
                return true;
        }
        // square root algorithm speeds up testing of bigger prime numbers
        $x = floor(sqrt($number));
        for ($i = 2 ; $i <= $x ; ++$i) {
                if ($number % $i == 0) {
                        break;
                }
        }
        return ($x == $i-1) ? true : false;
}

?>

<?php
trait Sanitizer {
    function cleanOutInt($num){
        return filter_var($num, FILTER_SANITIZE_NUMBER_INT);
    }
}
?>
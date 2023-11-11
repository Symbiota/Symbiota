<?php
$x = XMLReader::xml('<?xml version="1.0"?><doc></doc>');
$x->read();
$y = $x->expand();
var_dump($y);
?>
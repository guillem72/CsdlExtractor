<?php

/**
 * User: Guillem LLuch
 * Date: 13/12/16
 * Time: 21:46
 */
namespace glluchcom\csdlScraping;
require '../Validate.class.php';

$filename = "validate/information services.html";
Validate::file2Xml($filename);
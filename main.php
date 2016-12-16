<?php
/**
 * User: Guillem LLuch
 * Date: 11/12/16
 * Time: 02:18
 */
include('CsdlExtractor.class.php');
$csdl = new \glluchcom\csdlScraping\Extractor("information services");

$titles = $csdl->getTitles(); //extract
echo "Titles: " . PHP_EOL;
foreach ($titles as $title) {
    echo $title . PHP_EOL;

}
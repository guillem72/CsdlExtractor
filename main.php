<?php
/**
 * User: Guillem LLuch
 * Date: 11/12/16
 * Time: 02:18
 */
include('Scraper.class.php');
$csdl = new \glluchcom\csdlScraping\Scraper("array");
//$titles=$csdl->getTitles();
$titles = $csdl->getTitlesOffline(); //extract
echo "Titles: " . PHP_EOL;
foreach ($titles as $title) {
    echo $title . PHP_EOL;

}
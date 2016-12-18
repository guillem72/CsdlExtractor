<?php
/**
 * User: Guillem LLuch
 * Date: 11/12/16
 * Time: 02:18
 */
// All files in a dir ls -l | grep .html | awk '{print $9, $10, $11 }'

include_once('CsdlExtractor.class.php');
include_once('IO.class.php');
include_once('IEEEGateway.class.php');

$results = extractFromDir("files.txt");
//var_dump($results);

$title = "Machine learning nuclear detonation features";
$ieee = new \glluchcom\csdlExtractor\IEEEGateway();
//$ieee->retrieve($title,"xml/");
$ieee->foreachTerm($results, "files.txt");
//\glluchcom\csdlExtractor\IO::xmlFile2Object("xml/".$title.".xml");


/**
 * @param $filename . The name of the file which contains the list of terms.
 * Each of these terms +.html will be the name of a file in the source directory
 * (typically "html")
 */
function extractFromDir($filename)
{
    $files0 = file_get_contents($filename);
    $files = explode("\n", $files0);
    $titles = array();
    foreach ($files as $file) {
        if ($file != "") {
            $csdl = new \glluchcom\csdlExtractor\Extractor(\trim($file));
            $t = $csdl->getTitles();
            $titles[trim($file)] = $t;
        }
    }
    \glluchcom\csdlExtractor\IO::phpFormat($titles, "titles.phpseriaziled.txt");


    return $titles;
}

/**
 * Extracts titles only from single file.
 */
function extractOne()
{
    $csdl = new \glluchcom\csdlExtractor\Extractor("information services");

    $titles = $csdl->getTitles(); //extract
    echo "Titles: " . PHP_EOL;
    foreach ($titles as $title) {
        echo $title . PHP_EOL;

    }

}

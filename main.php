<?php
/**
 * User: Guillem LLuch
 * Date: 11/12/16
 * Time: 02:18
 */
// All files in a dir ls -l | grep .html | awk '{print $9, $10, $11 }'

include('CsdlExtractor.class.php');
$results = extractFromDir("/root/public_html/CsdlScraping/files.txt");
var_dump($results);

/**
 * @param $filename The name of the file which contains the list of terms.
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
            $csdl = new \glluchcom\csdlScraping\Extractor(\trim($file));
            $t = $csdl->getTitles();
            $titles[$file] = $t;
        }
    }


return $titles;
}

/**
 * Extracts titles only from single file.
 */
function extractOne()
{
    $csdl = new \glluchcom\csdlScraping\Extractor("information services");

    $titles = $csdl->getTitles(); //extract
    echo "Titles: " . PHP_EOL;
    foreach ($titles as $title) {
        echo $title . PHP_EOL;

    }

}

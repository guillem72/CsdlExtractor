<?php
namespace glluchcom\csdlExtractor;
/**
 * User: Guillem LLuch
 * Date: 17/12/16
 * Time: 21:23
 */


/**
 * Class IEEEGateway . Search an article in <http://ieeexplore.ieee.org/gateway/> and retrieve its metadata.
 * Can search for a group of articles too.
 * @package glluchcom\csdlExtractor
 */
class IEEEGateway
{
    /**
     * Given an asociative array which every element has the form
     * term=>[relatedArticle1,relatedArticle2,...] and a file
     * with the list of terms, build a dir for each term and populated it
     * with one file per related article.
     * @param $termsAndRelated . The array
     * @param $filename . The file name with the index of the array. Each line
     * contain a term.
     */
    public function foreachTerm($termsAndRelated, $filename)
    {
        $files0 = file_get_contents($filename);
        $files = explode("\n", $files0);

        foreach ($files as $file) {
            if ($file != "") {
                $term = trim($file);
                $related = $termsAndRelated[$term];
                $this->foreachTitle($term, $related);

            }
        }
    }


    /**
     * Save an xml file from IEEE gateway for each title. The name of
     * the file shows the title rank in the return list.
     * @param $term . The term.
     * @param $titles . An array of the titles related to the term.
     */
    public function foreachTitle($term, $titles)
    {
        $dir = "xml/" . $term . "/";
        if (!is_dir($dir)) {
            mkdir($dir);
            echo "Making dir " . $dir;
        }
        foreach ($titles as $pos => $title) {
            $this->retrieve($title, $dir, $pos);
        }
    }


    /**
     * Retrieve and save as xml the metainformation of an article
     * @param $title0 . The exact title of an article.
     */
    public function retrieve($title0, $dir, $pos)
    {
        $title = urlencode($title0);
        $url = 'http://ieeexplore.ieee.org/gateway/ipsSearch.jsp?ti=' . $title;


        $ch = curl_init($url);
        $fp = fopen($dir . $pos . "_" . $title0 . ".xml", "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        if (curl_error($ch)) {
            echo "ERROR curl searching for " . $title0 . ": " . PHP_EOL . curl_error($ch) . PHP_EOL;

        } else echo $url . " retrieved" . PHP_EOL;
        curl_close($ch);
        fclose($fp);

    }


}
<?php
/**
 * User: Guillem LLuch
 * Date: 18/12/16
 * Time: 13:42
 */

namespace glluchcom\csdlExtractor;


class IEEEGatewayExtractor
{
    /**
     * @var string Xpath expression to get the information need. In this case
     * is the titles of the articles.
     */
    protected $controlledPattern = "/root/document/controlledterms/term";
    protected $thesaurusPattern = "/root/document/thesaurusterms/term";
    protected $controlledTerms = array();
    protected $thesaurusTerms = array();
    protected $termsRelated = array();

    /**
     * @return array
     */
    public function getTermsRelated($filename)
    {
        if (count($this->termsRelated) == 0) $this->extractAll($filename);
        return $this->termsRelated;
    }


    public function extractAll($filename)
    {
        $files0 = file_get_contents($filename);
        $files = explode("\n", $files0);

        foreach ($files as $file) {
            if ($file != "") {
                $term = trim($file);
                $dir = "xml/" . $term . "/";
                $terms = $this->extractTerms($dir);
                $this->termsRelated[$term] = $terms;
            }
        }

    }


    /**
     * For a term search all related term in the term dir.
     * @param $path . The name of the dir where the xml file reside.
     */
    protected function extractTerms($path)
    {
        $dir = opendir($path);
        $related = array();
        while ($file = readdir($dir)) {
            if (!is_dir($file) && $file != "." && $file != "..") {
                $fileInfo = pathinfo($file);
                if (@$fileInfo["extension"] === "xml") {

                    $name0 = $fileInfo["filename"];
                    $names = explode("_", $name0);
                    $location = $fileInfo["dirname"];
                    $pos = $names[0];
                    //$name = $names[1];

                    $text = file_get_contents($path . $file);

                    $xmldoc = new \DOMDocument();

                    if (@$xmldoc->loadXML($text)) {
                        $terms = $this->extractOne($xmldoc);
                        $terms["pos"] = $pos;
                        //$terms["name"] = $name;
                        $related = termsMerge($related, $terms, $pos);
                    }

                }
            }
        }
        return $related;
    }

    public function extractOne($domXml)
    {
        $xpath = new \DOMXPath($domXml);
        //var_dump($xpath);
        $items = $xpath->query($this->controlledPattern);
        if ($items) {
            //var_dump($items);
            foreach ($items as $item) {
                $this->controlledTerms[] = trim($item->nodeValue);
            }
        }
        $items = $xpath->query($this->thesaurusPattern);
        if ($items) {
            //var_dump($items);
            foreach ($items as $item) {
                $this->thesaurusTerms[] = trim($item->nodeValue);
            }
        }
        return array("controlled" => $this->controlledTerms, "thesaurus" => $this->thesaurusTerms);
    }

    /**
     * Add terms in a matrix of terms. The terms act as column title and its value
     * is its aggregate counts.
     * @param $related . The matrix where the new counts will be added.
     * @param $terms . A matrix containing new counts to be added to related.
     * @param $pos . A factor which represents the rank of the actual $terms.
     * So, a smaller number is more important than a greater one.
     * @return mixed . A new matrix with the new terms added and the old one
     * updated.
     */
    protected function termsMerge($related, $terms, $pos)
    {
        foreach ($terms as $term) {
            $weight = 0;
            if (array_key_exists($term, $related)) { //$term is in $related
                $weight = $related[$term];
            }
            $weight += $this->calcWeight($pos); //for 0 => 1. for 20 => 0'6;
            $related[$term] = $weight;
        }
        return $related;
    }

    protected function calcWeight($rank)
    {
        $method = "expDecay";
        call_user_func($method, $rank);
    }

    /**
     *
     * @param $rank int (between 0 and 50)
     */
    protected function expDecay($rank)
    {
        return exp(-0.07824 * $rank);
    }

    protected function linealDecay($rank)
    {
        return (50 - $rank / 50);
    }
}
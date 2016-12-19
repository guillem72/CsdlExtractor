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
                $terms = $this->extractTerms($dir, true);
                $this->termsRelated[$term] = $terms;
            }
        }

    }


    /**
     * For a term search all related term in the term dir.
     * @param $path . The name of the dir where the xml file reside.
     * @param $orderByValues . Boolean which indicates if the order will base
     * on values (true) or on key, by alphabetic order (false)
     * @return array . A complex array made by terms controlled and thesaurus
     * with its weight
     *
     **/
    protected function extractTerms($path, $orderByValues)
    {
        $dir = opendir($path);
        /** @var array $related sourceTerm =&lt; array ( type of term =&lt;
         * array(term =&lt; its weight) )*/
        $related = array();
        $related["controlled"] = array();
        $related["thesaurus"] = array();
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
                        $related = $this->allTermsMerge($related, $terms, $pos);
                    }

                }
            }
        }
        if ($orderByValues) {
            arsort($related["controlled"], SORT_NUMERIC);
            arsort($related["thesaurus"], SORT_NUMERIC);
        } else {
            ksort($related["controlled"]);
            ksort($related["thesaurus"]);
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
        return array("controlled" => $this->controlledTerms,
            "thesaurus" => $this->thesaurusTerms);
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
    protected function allTermsMerge($related, $terms, $pos)
    {
        if ($related === null || !is_array($related)) {
            echo "ERROR in the type of object " . PHP_EOL;
            echo " pos=" . $pos . PHP_EOL;
            var_dump($related);
            return false;
        }

        $related["controlled"] = $this->termsMerge($related["controlled"],
            $terms["controlled"], $pos);

        $related["thesaurus"] = $this->termsMerge($related["thesaurus"],
            $terms["thesaurus"], $pos);
        return $related;
    }

    protected function termsMerge($related, $terms, $pos)
    {
        if ($related === null || !is_array($related)) {
            echo "ERROR in the type of object " . PHP_EOL;
            echo " pos=" . $pos . PHP_EOL;
            var_dump($related);
            return false;
        }
        foreach ($terms as $term) {
            if ($term !== null && $term != "") {

                $weight = 0;
                if (array_key_exists($term, $related)) { //$term is in $related
                    $weight = $related[$term];
                }
                $weight += $this->calcWeight($pos); //for 0 => 1. for 20 => 0'6;
                //echo "term is ";
                //var_dump($term);
                $related[$term] = $weight;
            }
        }
        return $related;
    }

    protected function calcWeight($rank)
    {
        return $this->expDecay($rank);
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
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
        if ($this->termsRelated . size == 0) $this->extractAll($filename);
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
                    $pos = $names[0];
                    $name = $names[1];

                    $text = file_get_contents($file);

                    $xmldoc = new DOMDocument();
                    if (@$xmldoc->load($text)) {
                        $terms = $this->extractOne($xmldoc);
                        $terms["pos"] = $pos;
                        $terms["name"] = $name;
                        $related = array_merge($related, $terms);
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
}
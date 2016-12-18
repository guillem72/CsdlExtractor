<?php
/**
 * User: Guillem LLuch
 */

namespace glluchcom\csdlExtractor;

require 'Validate.class.php';

/**
 * Class Extractor Extracts information from a CSDL searches.
 * To download the webpages, use another program as
 * [jsCSDLscraping](https://github.com/guillem72/jsCSDLscraping), for example
 * @package glluchcom\csdlScraping
 */
class Extractor extends \DOMDocument
{
    /**
     * @var string Directory where the html files are saved.
     */
    protected $sourceDir = "./html/";

    /**
     * @var string Xpath expression to get the information need. In this case
     * is the titles of the articles.
     */
    protected $pattern = "//div[@class]/h5/../../span/text()";
    /**
     * @var string saves the term currently searched. The term is the basename
     *  of the file. For "array.html" it is only array.
     */
    protected $query;
    /**
     * @var \Logger The logger for debug, info , warm, etc messages
     */

    protected $titles = array();

    /**
     * @var string The name of the file where the extracting will act.
     */
    protected $filename;

    /**
     * Scraper constructor.
     * @param $q The basename of the file, which it is the term searched.
     */
    public function __construct($q)
    {

        $this->query = $q;
        $this->filename = $this->query . ".html";


    }




    //https://www.computer.org/web/search?cs_search_action=advancedsearch&search-options=dl&searchOperation=exact&searchText=swot

    /**
     * Convert the html to a well-formed xml and extract the titles.
     * @return array The titles of the articles found in the html.
     */
    public function getTitles()
    {
        Validate::file2Xml($this->sourceDir . $this->filename);
        //echo $this->sourceDir . $this->filename.PHP_EOL;
        $this->load($this->sourceDir . $this->filename);

        $this->extract();

        return $this->titles;
    }


    /**
     * Extract the titles using xpath.
     *
     */
    protected function extract()
    {

        $xpath = new \DOMXPath($this);
        //var_dump($xpath);
        $items = $xpath->query($this->pattern);
        if (!$items) {
            echo("The search for '" . $this->query . "' doesn't 
            produce any result");
            return false;
        } else {
            //var_dump($items);
            foreach ($items as $item) {
                $this->titles[] = trim($item->nodeValue);
            }
        }

    }





    //Getter and setters

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }


    /**
     * @return mixed
     */
    public function getUrlBase()
    {
        return $this->urlBase;
    }

    /**
     * @param mixed $urlBase
     */
    public function setUrlBase($urlBase)
    {
        $this->urlBase = $urlBase;
    }

    /**
     * @return mixed
     */
    public function getSourceDir()
    {
        return $this->sourceDir;
    }

    /**
     * @param mixed $sourceDir
     */
    public function setSourceDir($sourceDir)
    {
        $this->sourceDir = $sourceDir;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }


}
<?php
/**
 * User: Guillem LLuch
 * Date: 11/12/16
 * Time: 00:52
 */

namespace glluchcom\csdlScraping;

require 'vendor/autoload.php';
require 'Validate.class.php';

class Scraper extends \DOMDocument
{
    protected $urlBase = "https://www.computer.org/web/search?cs_search_action=advancedsearch&search-options=dl&searchOperation=exact&searchText=";
    protected $targetDir = "./html/";
    protected $pattern = "//div[@class]/h5/../../span/text()";
    protected $query;
    protected $log;
    protected $titles = array();
    protected $build = false;
    protected $filename;

    /**
     * Scraper constructor.
     * @param $q
     */
    public function __construct($q)
    {
        \Logger::configure('config.xml');
        $this->log = \Logger::getLogger('Logger');
        $this->query = $q;
        $this->filename = $this->query . ".html";


    }

    //https://www.computer.org/web/search?cs_search_action=advancedsearch&search-options=dl&searchOperation=exact&searchText=swot

    /**
     * @return array
     */
    public function getTitles()
    {
        if (sizeof($this->titles) <= 0) {
            $this->csdlSearch();

        }
        $this->extract();

        return $this->titles;
    }

    public function csdlSearch()
    {
        if (!$this->build) {

            $this->obtain($this->urlBase . urlencode($this->query));
            $this->save($this->targetDir . $this->filename);
            $this->load($this->targetDir . $this->filename);
            $this->build = true;
        }
    }

    /**
     * @param $url
     */
    protected function obtain($url)
    {
        $config = array(
            'indent' => true,
            'output-xhtml' => true,
            'wrap' => 200);
        $tidy = new \tidy();

        if (!$this->log) echo("NO log: " . $url);
        else $this->log->trace("Retrieving " . $url);
        echo "Obtaining " . $url . PHP_EOL;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U;
         Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 
         Firefox/2.0.0.13');
        $info = curl_exec($ch);
        curl_close($ch);

        $tidy->parseString($info, $config, 'utf8');
        $tidy->cleanRepair();
        //TODO save $tidy (echo $tidy);
        @$this->loadHTML($tidy);
        //$this->save();
        $this->build = true;
    }

    public function extract()
    {

        $xpath = new \DOMXPath($this);
        var_dump($xpath);
        $items = $xpath->query($this->pattern);
        if (!$items) {
            echo("The search for '" . $this->query . "' doesn't 
            produce any result");
            return false;
        } else {
            var_dump($items);
            foreach ($items as $item) {
                $this->titles[] = trim($item->nodeValue);
            }
        }

    }

    public function getTitlesOffline()
    {
        Validate::file2Xml($this->targetDir . $this->filename);

        @$this->load($this->targetDir . $this->filename);
        $this->build = true;
        $this->extract();

        return $this->titles;
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
    public function getTargetDir()
    {
        return $this->targetDir;
    }

    /**
     * @param mixed $targetDir
     */
    public function setTargetDir($targetDir)
    {
        $this->targetDir = $targetDir;
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
<?php
/**
 * User: Guillem LLuch
 * Date: 13/12/16
 * Time: 00:21
 */

namespace glluchcom\csdlScraping;


class Validate
{
    public static function file2Xml($filen)
    {
        $doc = new \DOMDocument();
        $fileInfo = \pathinfo($filen);
        $path = $fileInfo['dirname'];
        $filename = $fileInfo['filename'];
        $basename = $fileInfo['basename'];
        $newfilename = $path . "/" . $filename . ".xhtml";
        $newfilename = $path . "/" . $basename;

        @$doc->loadHTMLFile($filen);
        $doc->save($newfilename);
    }
}
<?php
/**
 * User: Guillem LLuch
 * Date: 13/12/16
 * Time: 00:21
 */

namespace glluchcom\csdlExtractor;


class Validate
{
    public static function file2Xml($filen)
    {
        $doc = new \DOMDocument();
        $fileInfo = \pathinfo($filen);
        $path = $fileInfo['dirname'];
        $filename = $fileInfo['filename'];
        $basename = $fileInfo['basename'];

        $newfilename = $path . "/" . $basename;
        self::checkXMLDeclaration($newfilename);
        @$doc->loadHTMLFile($filen);
        $doc->save($newfilename,LIBXML_NOXMLDECL);
    }

    //LIBXML_NOXMLDECL doesn't work
    protected static function checkXMLDeclaration($filen){
        $newfile="";
        $html0=file_get_contents($filen);
        $html=explode("\n",$html0);//array line by line
        foreach($html as $line ) {
                 $line1 = explode(" ", $line);
            if (!in_array("<?xml",$line1)){
                $newfile.=$line;
            }
        }
        file_put_contents($filen,$newfile);


    }
}
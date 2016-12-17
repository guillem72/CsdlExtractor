<?php
/**
 * User: Guillem LLuch
 * Date: 17/12/16
 * Time: 20:19
 */

namespace glluchcom\csdlExtractor;


/**
 * Class SaveResults Save generic objects in a file.
 * @package glluchcom\csdlScraping
 */
class SaveResults
{
    /**
     * Save an object with serialize function from php.
     * @param $thing The object to be serizalized
     * @param $filename The name of file where the object will be saved.
     * @return int The value returned by the php function file_put_contents
     */
    public static function phpFormat($thing, $filename)
    {
        $phpThing = serialize($thing);
        return file_put_contents($filename, $phpThing);

    }
}
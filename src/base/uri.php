<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2014/12/30
 * Time: 11:23
 */
namespace Xiaoju\Beatles\Framework\Base;

class Uri
{
    private $uriString;

    private $uriPath;

    private $segments = array();

    public function __construct($uri)
    {
        if (!is_string($uri)) {
            throw new \InvalidArgumentException();
        }
        $this->uriString = $uri;
        $this->uriPath = parse_url($this->uriString, PHP_URL_PATH);
    }

    public function filterUri($str)
    {
        // Convert programatic characters to entities
        if (!is_string($str)) {
            throw new \InvalidArgumentException();
        }
        $bad = array('$', '(', ')', '%28', '%29');
        $good = array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;');
        return str_replace($bad, $good, $str);
    }

    public function explodeSegments()
    {
        foreach (explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->uriPath)) as $val) {
            // Filter segments for security
            $val = trim($this->filterUri($val));
            if ($val != '') {
                $this->segments[] = $val;
            }
        }
        return $this->segments;
    }
}

<?php
class ScssUtils {
    static function isValidPath($file, $basedir)
    {
        $path   = realpath($file);
        $length = strlen($basedir);
        return
            (substr($path, 0, $length) === $basedir) ?
            true : false;
    }

}

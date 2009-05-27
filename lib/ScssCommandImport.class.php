<?php
class ScssCommandImport {
    var $maxDepth = 10;

    function exec($params, $caller)
    {
        if (!count($params)) {
            return;
        }

        $depth = $caller->importDepth + 1;
        if ($depth === $this->maxDepth) {
            return;
        }

        $scss = new SmartCSS();
        $scss->importDepth = $depth;
        $basedir = dirname(dirname(__FILE__));
        foreach ($params as $file) {
            if (file_exists($file) && ScssUtils::isValidPath($file, $basedir)) {
                $scss->parse(file_get_contents($file));
            }
        }
        return str_replace("\n", '', $scss->publish());
    }

}

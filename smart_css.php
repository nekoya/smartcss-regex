<?php
require 'lib/SmartCSS.class.php';

/**
 * SmartCSS
 *
 * @copyright 2007-2009 Lism.in
 * @author    Ryo Miyake <ryo.studiom@gmail.com>
 */

/* .htaccess sample
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)$ smart_css.php?file=$1 [L]
*/

class Controller {
    var $filename;

    function exec()
    {
        if (!$this->getTargetFile()) {
            $this->notFoundError();
        }
        header('Content-type: text/css');
        $scss = new SmartCSS();
        $scss->parse(file_get_contents($this->filename));
        echo $scss->publish();
    }

    function getTargetFile()
    {
        if (!isset($_GET['file'])) {
            return false;
        }

        $filename = str_replace(array("\0","\n","\r"), '', $_GET['file']);
        if (!preg_match('/\.css$/', $filename)) {
            return false;
        }

        $filename = preg_replace('/\.css$/', '.scss', $filename);
        if (!file_exists($filename) ||
            !ScssUtils::isValidPath($filename, dirname(__FILE__))) {
            return false;
        }

        $this->filename = $filename;
        return true;
    }

    function notFoundError()
    {
        header('HTTP/1.0 404 Not Found');
        header('Content-type: text/plain');
        die('File not found.');
    }
}

$c = new Controller();
$c->exec();

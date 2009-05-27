<?php
require 'ScssNode.class.php';
require 'ScssRule.class.php';
require 'ScssCommand.class.php';
require 'ScssUtils.class.php';

class SmartCSS {
    var $buffer      = '';
    var $content     = '';
    var $nodes       = array();
    var $leftDelim   = '\[\%';
    var $rightDelim  = '\%\]';
    var $importDepth = 0;

    function parse($buffer)
    {
        if ((string)$buffer === '') {
            return;
        }
        $this->buffer = $this->beforeFiltering($buffer);
        $this->parseCommands();
        $this->findAllNodes();
    }

    function beforeFiltering($context)
    {
        $context = preg_replace('/[ \t]*\n[ \t]*/', '', $context);
        $context = preg_replace('|/\*.*?\*/|', '', $context);
        return $context;
    }

    function parseCommands()
    {
        $buffer = $this->buffer;
        $buffers = array();
        $pattern = '/'.$this->leftDelim.'.*?'.$this->rightDelim.'/';
        preg_match_all($pattern, $buffer, $matches, PREG_OFFSET_CAPTURE);
        $lastpos = 0;
        foreach ($matches[0] as $match) {
            $buffers[] = substr($buffer, $lastpos, $match[1]-$lastpos);

            $length = strlen($match[0]);
            $command = trim(
                substr($buffer, $match[1], $length),
                ' '.$this->leftDelim.$this->rightDelim
            );
            $buffers[] = $this->execCommand($command);

            $lastpos = $length+$match[1];
        }
        $buffers[] = substr($buffer, $lastpos);
        $this->buffer = join('', $buffers);
    }

    function findAllNodes()
    {
        preg_match_all(
            '/( ([^;{]+) { ((?>[^{}]+)|(?R))* } | (@.+;))/xU',
            $this->buffer,
            $matches
        );

        foreach ($matches[0] as $nodeTxt) {
            if (preg_match('/^@/', $nodeTxt)) {
                $this->nodes[] = new ScssRule($nodeTxt);
            } else {
                $this->nodes[] = new ScssNode($nodeTxt);
            }
        }
    }

    function publish()
    {
        foreach ($this->nodes as $node) {
            $this->content .= $node->publish();
        }
        return $this->content;
    }

    function execCommand($statement)
    {
        $cmd = new ScssCommand($statement, $this);
        return $cmd->exec();
    }
}

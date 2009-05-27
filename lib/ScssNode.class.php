<?php
class ScssNode {
    var $parent     = null;
    var $children   = array();
    var $selectors  = array();
    var $properties = array();

    function ScssNode($content, $parent=null)
    {
        if (is_object($parent)) {
            $this->parent = $parent;
        }

        preg_match('/(.+?){(.*)}/', $content, $matches);
        $this->analyzeSelectors($matches[1]);
        $this->analyzeProperties($matches[2]);
    }

    function publish()
    {
        $content = '';
        if ($this->properties) {
            $content .=
                join(',', $this->getSelector()).'{'.
                join(";", $this->properties).
                '}'.PHP_EOL;
        }

        if ($this->children) {
            foreach ($this->children as $child) {
                $content .= $child->publish();
            }
        }

        return $content;
    }

    function analyzeSelectors($context)
    {
        $context = trim(preg_replace('/[\t ]+/', ' ', $context));
        $this->selectors = array();
        foreach (explode(',', $context) as $selector) {
            $this->selectors[] = trim($selector);
        }
    }

    function analyzeProperties($context)
    {
        $context = trim(preg_replace('/[\t ]+/', ' ', $context));
        $this->properties = array();
        preg_match_all('/([^;{]+){((?>[^{}]+)|(?R))*}/U', $context, $matches);
        if ($matches[0]) {
            foreach ($matches[0] as $match) {
                $context = str_replace($match, '', $context);
                $this->children[] = new ScssNode($match, $this);
            }
        }
        foreach (explode(';', $context) as $property) {
            $property = trim($property);
            if ($property) {
                $this->properties[] = preg_replace(
                    '/^(.+?)\s*:\s*(.+?)$/',
                    "$1:$2",
                    $property
                );
            }
        }
    }

    function getSelector()
    {
        if (!is_object($this->parent)) {
            return $this->selectors;
        }

        $selectors = array();
        foreach ($this->parent->getSelector() as $parent) {
            foreach ($this->selectors as $selector) {
                $selectors[] = trim($parent).' '.($selector);
            }
        }
        return $selectors;
    }
}

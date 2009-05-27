<?php
class ScssCommandVariable {
    static $vars;

    function exec($params, $caller)
    {
        if (is_array($params)) {
            $this->setVar($params);
        } else {
            return $this->getVar($params);
        }
    }

    function setVar($params)
    {
        list($var, $value) = $params;
        ScssCommandVariable::$vars[$var] = $value;
    }

    function getVar($params)
    {
        return ScssCommandVariable::$vars[$params];
    }

}

<?php
class ScssRule {
    var $statement;

    function ScssRule($statement)
    {
        $this->statement = $statement;
    }

    function publish()
    {
        return $this->statement.PHP_EOL;
    }
}

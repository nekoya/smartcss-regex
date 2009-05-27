<?php
class ScssCommand {
    var $caller;
    var $command;
    var $cmdObj;

    function ScssCommand($statement, $caller)
    {
        $this->caller = $caller;
        if (preg_match('/^\$(\w+) \s*\=\s* (\"|\') (.*) (\"|\')/xU', $statement, $matches)) {
            $this->command = 'variable';
            $this->params  = array($matches[1], $matches[3]);
        } else if (preg_match('/^\$(\w+)$/', $statement, $matches)) {
            $this->command = 'variable';
            $this->params  = $matches[1];
        } else {
            $params = explode(' ', $statement);
            if (count($params)) {
                $this->command = array_shift($params);
                $this->params  = $params;
            }
        }
    }

    function exec()
    {
        if (!$this->command) {
            return;
        }

        $class = 'ScssCommand'.ucfirst(strtolower($this->command));
        $classfile = dirname(__FILE__).DIRECTORY_SEPARATOR.$class.'.class.php';
        if (file_exists($classfile)) {
            include_once $classfile;
            if (class_exists($class)) {
                $this->cmdObj = new $class;
                return $this->cmdObj->exec($this->params, $this->caller);
            }
        }
    }
}

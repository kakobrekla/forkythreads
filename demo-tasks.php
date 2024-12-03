<?php

class DemoTaskA extends Task
{
    // Default empty task that just sleeps
}

class DemoTaskB extends Task
{
    private $foo;

    public function __construct($param)
    {
        $this->foo = $param;
    }

    public function _onInit()
    {
        // Set process title for the task
        cli_set_process_title("php-cli DemoTaskB name");
        $this->printCLI("Starting.");
    }

    public function _onExit()
    {
        $this->printCLI("Exit.");
    }

    public function _loop()
    {
        // Randomly select a duration between 1 and 5 seconds
        $duration = array_rand(array_fill(1, 5, 0));
        $this->printCLI("foo is $this->foo, I'm going AFK for $duration seconds.");
        $this->_nap($duration);
    }

    private function printCLI(string $line)
    {
        // Print formatted log message with timestamp and process ID
        print("[" . date("Y-m-d H:i:s") . "] (" . getmypid() . ") " . __CLASS__ . ": $line\n");
    }
}

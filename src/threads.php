<?php

class Task
{
    public function __construct()
    {
        // Constructor logic (if any)
    }

    public function _onInit()
    {
        // Initialization logic
    }

    public function _loop()
    {
        print("Running " . getmypid() . ".\n");
        $this->_nap(1);
    }

    public function _onExit()
    {
        // Exit logic
    }

    public function _nap($seconds)
    {
        while ($seconds-- > 0) {
            pcntl_signal_dispatch();
            sleep(1);
        }
    }
}

class ThreadServer
{
    public function __construct(array $tasks, string $processTitle = '')
    {
        // Set process title if provided
        if (!empty($processTitle)) {
            cli_set_process_title("php-cli " . $processTitle);
        }

        // Register signal handlers
        pcntl_signal(SIGTERM, [$this, "exitHandler"]);
        pcntl_signal(SIGINT, [$this, "exitHandler"]);
        pcntl_signal(SIGHUP, [$this, "exitHandler"]);

        // Fork task threads
        $this->forkTaskThreads($tasks);

        // Run the main loop
        $this->runMainLoop();
    }

    private function forkTaskThreads(array $tasks)
    {
        foreach ($tasks as $task) {
            if (pcntl_fork() === 0) {
                include_once basename(__FILE__); // Include current file in child process
                new Thread($task); // Start a new thread
                exit();
            }
        }
    }

    private function runMainLoop()
    {
        while (true) {
            pcntl_signal_dispatch();
            sleep(1);
        }
    }

    public function exitHandler()
    {
        exit(get_class($this) . " stopped.\n");
    }
}

class Thread
{
    private $task;

    public function __construct($task)
    {
        // Register signal handlers
        pcntl_signal(SIGTERM, [$this, "exitHandler"]); // Kill signal
        pcntl_signal(SIGINT, [$this, "exitHandler"]);  // Ctrl + C
        pcntl_signal(SIGHUP, [$this, "exitHandler"]);  // Hangup signal

        $this->task = $task;

        // Initialize the task if method exists
        if (method_exists($this->task, "_onInit")) {
            $this->task->_onInit();
        }

        // Run the task loop if method exists
        if (method_exists($this->task, "_loop")) {
            $this->run();
        } else {
            print("Error: Missing \"_loop\" method in " . get_class($this) . "\n");
            $this->exitHandler();
        }
    }

    private function run()
    {
        while (true) {
            pcntl_signal_dispatch();
            $this->task->_loop();
        }
    }

    public function exitHandler()
    {
        if (method_exists($this->task, "_onExit")) {
            $this->task->_onExit();
        } else {
            print(get_class($this) . " stopped.\n");
        }

        exit();
    }
}

<?php


class Task{

    function __construct(){

    }

    function _onInit(){

    }

    function _loop(){
        print("Running ".getmypid().".\n");
        $this->_nap(1);
    }

    function _onExit(){

    }

    function _nap($sec){
        for($sec; $sec--;){
            pcntl_signal_dispatch();
            sleep(1);
        }
    }

}


class ThreadServer{

    function __construct($tasks, $process_title = false){

        if($process_title){
          cli_set_process_title("php-cli " . $process_title);
        }

        pcntl_signal(SIGTERM, array($this, "exitHandler"));
        pcntl_signal(SIGINT, array($this, "exitHandler"));
        pcntl_signal(SIGHUP, array($this, "exitHandler"));
        $this->forkTaskThreads($tasks);
        $this->runMainLoop();
    }

    function forkTaskThreads($tasks){
        foreach($tasks as $task){
            if (pcntl_fork() == 0) {
                include_once(basename(__FILE__));
                new Thread($task);
                exit();
            }
        }
    }

    function runMainLoop(){
        while(true){
            pcntl_signal_dispatch();
            sleep(1);
        }
    }

    function exitHandler(){
        exit(get_class($this)." stopped.\n");
    }

}


class Thread{

    function __construct($task){
        pcntl_signal(SIGTERM, array($this, "exitHandler")); //kill
        pcntl_signal(SIGINT, array($this, "exitHandler")); //ctrl c
        pcntl_signal(SIGHUP, array($this, "exitHandler"));

        $this->task = $task;
        if(method_exists($this->task, "_onInit")){
            $this->task->_onInit();
        }
        if(method_exists($this->task, "_loop")){
            $this->run();
        }else{
            print("Error: Missing \"_loop\" method in " .get_class($this). "\n");
            $this->exitHandler();
        }
    }

    function run(){
        while(true){
            pcntl_signal_dispatch();
            $this->task->_loop();
        }
    }

    function exitHandler(){
        if(method_exists($this->task, "_onExit")){
            $this->task->_onExit();
        }else{
            print(get_class($this)." stopped.\n");
        }

        exit();
    }

}


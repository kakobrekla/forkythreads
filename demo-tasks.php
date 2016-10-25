<?php

class DemoTaskA extends Task{
 //default empty task just sleeps
}


class DemoTaskB extends Task {

	var $foo;
	
	function __construct($param){
		$this->foo = $param;
	}

	function _onInit(){
		$this->printCLI("Starting.");
	}
	
	function _onExit(){
		$this->printCLI("Exit.");
	}
	
	function _loop(){
		$duration = array_rand(array_fill(1, 5, 0));
		$this->printCLI("foo is $this->foo, im going afk for $duration seconds");
		$this->_nap($duration);
	}
	
	function printCLI($line){
		print("[".date("Y-m-d H:i:s")."] (".getmypid().") ". __CLASS__ .": $line \n");
	}
	
}
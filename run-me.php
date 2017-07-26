<?php

include_once("src/threads.php");
include_once("demo-tasks.php");

$tasks[] = new DemoTaskA();
$tasks[] = new DemoTaskB("B1");
$tasks[] = new DemoTaskB("B2");

$server = new ThreadServer($tasks, "Thread Server process name");

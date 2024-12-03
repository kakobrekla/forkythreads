<?php

// Include necessary files
include_once "src/threads.php";
include_once "demo-tasks.php";

// Create tasks
$tasks = [];
$tasks[] = new DemoTaskA();
$tasks[] = new DemoTaskB("B1");
$tasks[] = new DemoTaskB("B2");

// Initialize the thread server with the tasks and process title
$server = new ThreadServer($tasks, "Thread Server process name");

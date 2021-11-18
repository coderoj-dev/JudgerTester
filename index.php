<?php

require_once realpath("vendor/autoload.php");


$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->load();
        
echo getenv('JUDGER_URL');
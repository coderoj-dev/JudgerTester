<?php

require_once realpath("vendor/autoload.php");


$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->load();
    
$s3_bucket = $_ENV['JUDGER_URL'];


print_r($s3_bucket);

echo getenv('JUDGER_URL');
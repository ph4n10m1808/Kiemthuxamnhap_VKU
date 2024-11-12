<?php
    $servername = getenv('MYSQL_HOST');
    $username = getenv('MYSQL_USER');
    $password = getenv('MYSQL_PASSWORD');
    $db = getenv('MYSQL_DATABASE');
    $conn = new mysqli($servername,$username,$password,$db);
    if ($conn -> connect_error){
        die('Connection Failed : ' .$conn -> connect_error);
    }

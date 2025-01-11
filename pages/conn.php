<?php

$conn = new mysqli("localhost","root","","final_project_php");


if($conn->connect_error){
    echo $conn->error;
}
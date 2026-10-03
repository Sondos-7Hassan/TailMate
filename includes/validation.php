<?php

function validate_input($input){
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    $input = strip_tags($input);
    return $input;
}   

function validate_email($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone){
    $phone_pattern = '/^(010|011|012|015)[0-9]{8}$/';
    return preg_match($phone_pattern, $phone);
}

function check_unique($col,$table,$value)
   {
        global $connect;
        $result=$connect->prepare("SELECT $col FROM $table WHERE $col=:val");
        $result->bindParam(":val",$value);
        $result->execute();
        return $result->rowCount();
   }
?>

<?php 

$name = $email = $phone = $password = $confirm = $data = "";

function inputvalidate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
function checkuser($col, $tab, $value)
{
    global $connect;
    $result = $connect->query("SELECT $col FROM $tab WHERE $col = '$value'");
    $count = $result->rowCount();
    return $count;
}
function checkemp($col, $tab, $value)
{
    global $empconnect;
    $result = $empconnect->query("SELECT $col FROM $tab WHERE $col = '$value'");
    $count = $result->rowCount();
    return $count;
}
function emailvalidate($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}   
function phonevalidate($phone) {
    if ( !isset($phone) || empty($phone) ) {
        return false;

    }
    return preg_match('/^(010|011|012|015)[0-9]{8}$/', $phone);
}
function passwordvalidate($password) {
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,20}$/', $password);
}
?>


<?php

if(isset($_POST["submit"]))
{

$uid = $_POST["uid"];
$pwd = $_POST["pwd"];
$pwdnew = $_POST["pwdnew"];


include "..\classes\dbh.classes.php";
include "..\classes\changepwd.classes.php";
include "..\classes\changepwd-contr.classes.php";
$changepwd = new PasswordChangeContr($uid, $pwd, $pwdnew);


$changepwd->changePassword();


header("location: ../index.php?error=none");
}
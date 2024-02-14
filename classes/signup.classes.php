<?php

class Signup extends Dbh{

    protected function setUser($uid, $pwd, $email){
        $stmt = $this->connect()->prepare('INSERT INTO uzytkownik (users_uid, users_pwd, users_email) VALUES (?,?,?);');

        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        if(!$stmt->execute(array($uid, $hashedPwd, $email))){
            $stmt = null;
            header("location:  ../index.php?error=stmtfailed");
            exit();
        }
    }

    protected function checkUser($uid, $email){
        $stmt = $this->connect()->prepare('SELECT users_uid FROM uzytkownik WHERE users_uid = ? OR users_email = ?;');
        if(!$stmt->execute(array($uid, $email))){
            $stmt = null;
            header("location:  ../index.php?error=stmtcheckUser");
            exit();
        }
        
        if($stmt -> rowCount() > 0){
            $resultCheck = false;
        }
        else{
            $resultCheck = true;
        }
        return $resultCheck;
    }
}


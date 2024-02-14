<?php

class PasswordChange extends Dbh {

    // Metoda do sprawdzenia, czy podane hasło jest aktualnym hasłem użytkownika
    public function checkUserPassword($uid, $pwd) {
        $stmt = $this->connect()->prepare('SELECT users_pwd FROM uzytkownik WHERE users_uid = ? OR users_email = ?;');
        if(!$stmt->execute(array($uid, $uid))) {
            $stmt = null;
            return false;
        }
        
        if($stmt->rowCount() == 0){
            $stmt = null;
            return false;
        }

        $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $checkPwd = password_verify($pwd, $pwdHashed[0]["users_pwd"]);

        return $checkPwd;
    }

    // Metoda do aktualizacji hasła użytkownika
    public function updatePassword($uid, $newPwd) {
        $newPwdHashed = password_hash($newPwd, PASSWORD_DEFAULT);

        $stmt = $this->connect()->prepare('UPDATE uzytkownik SET users_pwd = ? WHERE users_uid = ? OR users_email = ?;');
        if(!$stmt->execute(array($newPwdHashed, $uid, $uid))) {
            $stmt = null;
            return false;
        }

        return true;
    }
}

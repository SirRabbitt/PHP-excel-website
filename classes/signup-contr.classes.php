<?php

class SignupContr extends Signup {

    private $uid;
    private $pwd;
    private $pwdRepeat;
    private $email;

    public function __construct($uid, $pwd, $pwdRepeat, $email){
        $this->uid = $uid;
        $this->pwd = $pwd;
        $this->pwdRepeat = $pwdRepeat;
        $this->email = $email;
    }

    public function signupUser(){
        if(!$this->emptyInput()){
            header("location: ../index.php?error=input");
            exit();
        }
        if(!$this->invalidUid()){
            header("location: ../index.php?error=username");
            exit();
        }
        if(!$this->pwdMatch()){
            header("location: ../index.php?error=passwordmatch");
            exit();
        }
        if(!$this->invalidEmail()){
            header("location: ../index.php?error=email");
            exit();
        }
         if(!$this->uidTakenCheck()){
             header("location: ../index.php?error=uidTakenCheck");
             exit();
         }
        $this->setUser($this->uid, $this->pwd, $this->email);
    }

    private function emptyInput() {
        return !empty($this->uid) && !empty($this->pwd) && !empty($this->pwdRepeat) && !empty($this->email);
    }

    private function invalidUid(){
        return preg_match("/^[a-zA-Z0-9]*$/", $this->uid);
    }

    private function invalidEmail(){
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    private function pwdMatch(){
        return $this->pwd === $this->pwdRepeat;
    }

    private function uidTakenCheck(){
        return $this->checkUser($this->uid, $this->email);
    }

}

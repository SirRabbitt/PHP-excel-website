<?php
class PasswordChangeContr extends PasswordChange {

    private $uid;
    private $pwd;
    private $pwdnew;
    
    public function __construct($uid, $pwd, $pwdnew){
        $this->uid = $uid;
        $this->pwd = $pwd;
        $this->pwdnew = $pwdnew;
    }

    public function changePassword(){
        if($this->emptyInput()){
            header("location: ../index.php?error=emptyinput");
            exit();
        }
    
        if(!$this->checkUserPassword($this->uid, $this->pwd)){
            header("location: ../index.php?error=wrongpassword");
            exit();
        }
    
        $this->updatePassword($this->uid, $this->pwdnew);
    }
    
    private function emptyInput() {
        return empty($this->uid) || empty($this->pwd) || empty($this->pwdnew);
    }

  
}

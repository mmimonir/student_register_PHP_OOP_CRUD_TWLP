<?php

include_once 'Session.php';
include 'Database.php';

class User
{
    private $db;
    public function __construct()
    {
        $this->db = new Database();
    }

    public function userRegistration($data)
    {
        $name      = $data['name'];
        $username  = $data['username'];
        $email     = $data['email'];
        $password  = $data['password'];        
        $chk_email = $this->emailCheck($email);

        if ($name == "" or $username == "" or $email == "" or $password == "") {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Field Must Not Be Empty.</div>";
            return $msg;
        }

        if (strlen($username) < 3) {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Username Is too short.</div>";
            return $msg;
        } elseif (preg_match('/[^a-z0-9_-]+/i', $username)) {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Username must only contain alphanumerical, dashed, and underscores!</div>";
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>The Email Address is Not Valid!</div>";
        }

        if ($chk_email == true) {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>This Email is already Exist!</div>";
        }

        $password  = md5($data['password']);

        $sql   = "INSERT INTO tbl_user (name, username, email, password) VALUES(:name, :username, :email, :password)";
        $query = $this->db->pdo->prepare($sql);
        $query->bindValue(':name', $name);
        $query->bindValue(':username', $username);
        $query->bindValue(':email', $email);
        $query->bindValue(':password', $password);
        $result = $query->execute();
        if ($result) {
            $msg = "<div class = 'alert alert-success'><strong>Success ! </strong>Thank You, You Have Been Registered!</div>";
            return $msg;
        } else {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Sorry! There has been problem inserting youyr details.</div>";
            return $msg;
        }
    }

    public function emailCheck($email)
    {
        $sql   = "SELECT email FROM tbl_user WHERE email = :email";
        $query = $this->db->pdo->prepare($sql);
        $query->bindValue(':email', $email);
        $query->execute();
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getLoginUser($email, $password)
    {
        $sql   = "SELECT * FROM tbl_user WHERE email = :email AND password = :password LIMIT 1";
        $query = $this->db->pdo->prepare($sql);
        $query->bindValue(':email', $email);
        $query->bindValue(':password', $password);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function userLogin($data)
    {
        $email     = $data['email'];
        $password  = md5($data['password']);
        $chk_email = $this->emailCheck($email);

        if ($email == "" or $password == "") {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Field Must Not Be Empty.</div>";
            return $msg;			

            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>The Email Address is Not Valid!</div>";
            }
        }

        if ($chk_email == false) {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>This Email Not Exist!</div>";
            return $msg;
        }

        $result = $this->getLoginUser($email, $password);
        if ($result) {
            Session::init();
            Session::set("login", true);
            Session::set("id", $result->id);
            Session::set("name", $result->name);
            Session::set("username", $result->username);
            Session::set("loginmsg", "<div class = 'alert alert-success'><strong>Success ! </strong>You are LoggedIn.</div>");
            header("Location: index.php");
        } else {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Data Not Found!</div>";
            return $msg;
        }
    }

    public function getUserData(){
        $sql   = "SELECT * FROM tbl_user ORDER BY id DESC";
        $query = $this->db->pdo->prepare($sql);        
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    public function getUserById($userid){
        $sql   = "SELECT * FROM tbl_user WHERE id=:id LIMIT 1";
        $query = $this->db->pdo->prepare($sql);
        $query->bindValue(':id', $userid);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function updateUserData($userid, $data){
        $name      = $data['name'];
        $username  = $data['username'];
        $email     = $data['email'];
        
        

        if ($name == "" or $username == "" or $email == "") {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Field Must Not Be Empty.</div>";
            return $msg;
        }

        

        $sql   = "UPDATE tbl_user SET
                name        = :name,
                username    = :username,
                email       = :email
                WHERE id    = :userid";

        $query = $this->db->pdo->prepare($sql);

        $query->bindValue(':name', $name);
        $query->bindValue(':username', $username);
        $query->bindValue(':email', $email);        
        $query->bindValue(':userid', $userid);        
        $result = $query->execute();
        if ($result) {
            $msg = "<div class = 'alert alert-success'><strong>Success ! </strong>User Data Updated Successfully.</div>";
            return $msg;
        } else {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Sorry! Something is wrong. User Data not updated.</div>";
            return $msg;
        }
    }

    public function checkPassword($userid, $old_pass){
        $password = md5($old_pass);
        $sql   = "SELECT password FROM tbl_user WHERE id = :userid AND password = :password ";
        $query = $this->db->pdo->prepare($sql);
        $query->bindValue(':userid', $userid);
        $query->bindValue(':password', $password);
        $query->execute();
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function updateUPassword($userid, $data){
        $old_pass = $data['old_pass'];
        $new_pass = $data['password'];
        $chk_pass = $this->checkPassword($userid, $old_pass);

        if ($old_pass == "" OR $new_pass == "") {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Sorry! Field Must Not Be Empty.</div>";
            return $msg;
        }

        
        if ($chk_pass == false) {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Sorry! Old Password Not Exist</div>";
            return $msg;
        }

        if (strlen($new_pass)<6) {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Sorry! Password is too short.</div>";
            return $msg;
        }

        $password = md5($new_pass);

        $sql   = "UPDATE tbl_user SET
                password    = :password                
                WHERE id    = :userid";

        $query = $this->db->pdo->prepare($sql);
        $query->bindValue(':password', $password);        
        $query->bindValue(':userid', $userid);        
        $result = $query->execute();
        if ($result) {
            $msg = "<div class = 'alert alert-success'><strong>Success ! </strong>Password Updated Successfully.</div>";
            return $msg;
        } else {
            $msg = "<div class = 'alert alert-danger'><strong>Error ! </strong>Sorry! Password Not Updated.</div>";
            return $msg;
        }
    }
}

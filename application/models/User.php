<?php

namespace application\models;

use application\components\Auth;
use application\components\Db;
use application\components\Validator;

class User
{

    public $first_name;
    public $last_name;
    public $email;
    public $password;

    public function __construct($post)
    {
        $this->first_name = $post['first_name'];
        $this->last_name = $post['last_name'];
        $this->email = $post['email'];
        $this->password = $post['password'];
    }

    public function rules()
    {
        return [
            'required' => [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => $this->password,
            ],
            'name' => [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ],
            'email' => [
                'email' => $this->email,
            ],
            'password' => [
                'password' => $this->password,
            ]
        ];
    }

    public function validate()
    {
        $validator = new Validator($this->rules());
        if (!empty($validator->validate())) {
            return $validator->validate();
        }
        return [];
    }


    public function editUserData($id){

        if ($this->validate() == []){
            $update = Db::getConnection()->prepare("UPDATE `users` SET `first_name` = '$this->first_name',`last_name` = '$this->last_name', `email` = '$this->email', `password` = '$this->password' WHERE `users`.`id` = '$id';");
            $update->execute();
            return true;
        }
        return false;
    }


    public static function checkName ($login) {
        if (strlen($login) >= 2){
            return true;
        }
        return false;
    }

    public static function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public static function checkPassword($password)
    {
        if (strlen($password) >= 4 && !empty($password)) {
            return true;
        }
        return false;
    }

    public static function checkPasswordExists($password){
        $db = Db::getConnection();

        $sql = 'SELECT COUNT(*) FROM users WHERE password = :password';

        $result = $db->prepare($sql);
        $result->bindParam(':password', $password, \PDO::PARAM_STR);
        $result->execute();

        if ($result->fetchColumn())
            return true;
        return false;
    }

    public static function checkUserData($email, $password)
    {
        $db = Db::getConnection();

        $sql = 'SELECT * FROM users WHERE email = :email AND password = :password';

        $result = $db->prepare($sql);
        $result->bindParam(':email', $email, \PDO::PARAM_STR);
        $result->bindParam(':password', $password, \PDO::PARAM_INT);
        $result->execute();

        $user = $result->fetch();


        if ($user) {

            $user_id = $user['id'];

            if (isset($_POST['remember'])) {
                Auth::setCookie($user_id, $email);
            }

            return $user_id;
        }

        return false;
    }

    public static function checkLogged()
    {

        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        header("Location: /account/login");
    }

    public static function checkEmailExists($email)
    {
        $db = Db::getConnection();

        $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';

        $result = $db->prepare($sql);
        $result->bindParam(':email', $email, \PDO::PARAM_STR);
        $result->execute();

        if ($result->fetchColumn())
            return true;
        return false;
    }


    public static function register ($first_name,$last_name, $email, $password) {
        $db = Db::getConnection();

        $sql = 'INSERT INTO users (first_name, last_name,email, password)' .
            'VALUES (:first_name, :last_name, :email, :password)';

        $result = $db->prepare($sql);

        $result->bindParam(':first_name', $first_name, \PDO::PARAM_STR);
        $result->bindParam(':last_name', $last_name, \PDO::PARAM_STR);
        $result->bindParam(':email', $email, \PDO::PARAM_STR);
        $result->bindParam(':password', $password, \PDO::PARAM_STR);

        return $result->execute();
    }

    public static function auth($userId)
    {
        $_SESSION['user'] = $userId;
    }

    public static function randomPassword(){
        $pass = '';
        for ($i = 0; $i < 15; $i++) {
            if ($i%2==0){
                $pass.= chr(mt_rand(97,122));
            }else{
                $pass.= mt_rand(0,99);
            }
        }
        return $pass;
    }

    public static function hashPassword($password)
    {
        return $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    public static function getUserById($id)
    {
        $db = Db::getConnection();

        $sql = 'SELECT * FROM users WHERE id = :id';

        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, \PDO::PARAM_INT);

        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $result->execute();

        return $result->fetch();
    }

    public static function getUserId()
    {
        $db = Db::getConnection();

        $sql = 'SELECT * FROM users';

        $result = $db->prepare($sql);

        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $result->execute();

        return $result->fetch();
    }







}
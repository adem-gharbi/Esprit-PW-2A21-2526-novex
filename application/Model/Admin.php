<?php

class Admin
{
    private $id;
    private $fullname;
    private $email;
    private $password;
    private $role;

    public function __construct($id = null, $fullname = null, $email = null, $password = null, $role = 'admin')
    {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getFullname()
    {
        return $this->fullname;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getRole()
    {
        return $this->role;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function setRole($role)
    {
        $this->role = $role;
    }
}

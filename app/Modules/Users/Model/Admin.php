<?php

class Admin
{
    private $id;
    private $fullname;
    private $email;
    private $password;
    private $role;
    private $profile_photo;

    public function __construct($id = null, $fullname = null, $email = null, $password = null, $role = 'admin', $profile_photo = null)
    {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->profile_photo = $profile_photo;
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
    public function getProfilePhoto()
    {
        return $this->profile_photo;
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
    public function setProfilePhoto($profile_photo)
    {
        $this->profile_photo = $profile_photo;
    }
}

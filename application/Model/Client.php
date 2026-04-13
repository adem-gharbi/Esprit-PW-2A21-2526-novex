<?php

class Client {
    private $id;
    private $fullname;
    private $email;
    private $birthdate;
    private $tel;
    private $sexe;
    private $password;

    public function __construct($id = null, $fullname = null, $email = null, $birthdate = null, $tel = null, $sexe = null, $password = null) {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->birthdate = $birthdate;
        $this->tel = $tel;
        $this->sexe = $sexe;
        $this->password = $password;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getFullname() { return $this->fullname; }
    public function getEmail() { return $this->email; }
    public function getBirthdate() { return $this->birthdate; }
    public function getTel() { return $this->tel; }
    public function getSexe() { return $this->sexe; }
    public function getPassword() { return $this->password; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setFullname($fullname) { $this->fullname = $fullname; }
    public function setEmail($email) { $this->email = $email; }
    public function setBirthdate($birthdate) { $this->birthdate = $birthdate; }
    public function setTel($tel) { $this->tel = $tel; }
    public function setSexe($sexe) { $this->sexe = $sexe; }
    public function setPassword($password) { $this->password = $password; }
}

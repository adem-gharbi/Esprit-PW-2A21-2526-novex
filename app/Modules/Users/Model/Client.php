<?php

class Client {
    private ?int $id;
    private ?string $fullname;
    private ?string $email;
    private ?string $birthdate;
    private ?string $tel;
    private ?string $sexe;
    private ?string $password;
    private ?string $recovery_key;
    private ?string $profile_photo;

    public function __construct(?int $id = null, ?string $fullname = null, ?string $email = null, ?string $birthdate = null, ?string $tel = null, ?string $sexe = null, ?string $password = null, ?string $recovery_key = null, ?string $profile_photo = null) {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->birthdate = $birthdate;
        $this->tel = $tel;
        $this->sexe = $sexe;
        $this->password = $password;
        $this->recovery_key = $recovery_key;
        $this->profile_photo = $profile_photo;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getFullname() { return $this->fullname; }
    public function getEmail() { return $this->email; }
    public function getBirthdate() { return $this->birthdate; }
    public function getTel() { return $this->tel; }
    public function getSexe() { return $this->sexe; }
    public function getPassword() { return $this->password; }
    public function getRecoveryKey(): ?string { return $this->recovery_key; }
    public function getProfilePhoto(): ?string { return $this->profile_photo; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setFullname($fullname) { $this->fullname = $fullname; }
    public function setEmail($email) { $this->email = $email; }
    public function setBirthdate($birthdate) { $this->birthdate = $birthdate; }
    public function setTel($tel) { $this->tel = $tel; }
    public function setSexe($sexe) { $this->sexe = $sexe; }
    public function setPassword(?string $password): void { $this->password = $password; }
    public function setRecoveryKey(?string $recovery_key): void { $this->recovery_key = $recovery_key; }
    public function setProfilePhoto(?string $profile_photo): void { $this->profile_photo = $profile_photo; }
}

<?php
require_once __DIR__ . "/../models/Reservation.php";

class ReservationController {

    private $reservation;

    public function __construct($db){
        $this->reservation = new Reservation($db);
    }

    public function list(){
        return $this->reservation->getAll();
    }

    public function add($data){
        return $this->reservation->add($data);
    }

    public function delete($id){
        return $this->reservation->delete($id);
    }

    /* 👇 ADD THIS */
    public function getById($id){
        return $this->reservation->getById($id);
    }

    public function update($data, $id){
        return $this->reservation->update($data, $id);
    }
}
?>
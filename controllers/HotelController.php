<?php
require_once __DIR__ . "/../models/Hotel.php";

class HotelController {

    private $hotel;

    public function __construct($db){
        $this->hotel = new Hotel($db);
    }

    public function list(){
        return $this->hotel->getAll();
    }

    public function add($data){
        return $this->hotel->add($data);
    }

    public function delete($id){
        return $this->hotel->delete($id);
    }

    public function getById($id){
        return $this->hotel->getById($id);
    }

    public function update($data, $id){
        return $this->hotel->update($data, $id);
    }
}
?>
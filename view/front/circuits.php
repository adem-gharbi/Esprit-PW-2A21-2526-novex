<?php
class Circuit {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ALL circuits
    public function getAll() {
        $sql = "SELECT * FROM circuit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BY destination (IMPORTANT)
    public function getByDestination($id) {
        $sql = "SELECT * FROM circuit WHERE id_destination = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ADD
    public function add($titre,$duree,$prix,$places,$date,$id_dest,$hotel) {
        $sql = "INSERT INTO circuit
        (titre,duree,prix,nb_places,date_depart,id_destination,id_hotel)
        VALUES (?,?,?,?,?,?,?)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titre,$duree,$prix,$places,$date,$id_dest,$hotel]);
    }

    // DELETE
    public function delete($id) {
        $sql = "DELETE FROM circuit WHERE id_circuit=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
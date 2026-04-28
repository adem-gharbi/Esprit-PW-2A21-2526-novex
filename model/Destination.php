<?php
class Destination {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    //  READ ALL
    public function getAll() {
        try {
            $sql = "SELECT * FROM destination";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur SELECT: " . $e->getMessage());
        }
    }

    //  CREATE
    public function add($ville, $pays, $description, $image, $categorie) {
        try {
            $sql = "INSERT INTO destination (ville, pays, description, image, categorie)
                    VALUES (:ville, :pays, :description, :image, :categorie)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':ville', $ville);
            $stmt->bindParam(':pays', $pays);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':categorie', $categorie);

            return $stmt->execute();

        } catch (Exception $e) {
            die("Erreur INSERT: " . $e->getMessage());
        }
    }

    //  DELETE (bonus utile)
    public function delete($id) {
        try {
            $sql = "DELETE FROM destination WHERE id_destination = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            die("Erreur DELETE: " . $e->getMessage());
        }
    }
}
?>
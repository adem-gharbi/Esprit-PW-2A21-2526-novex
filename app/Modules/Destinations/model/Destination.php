<?php
class Destination {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // READ ALL
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

    // CREATE (avec latitude / longitude)
    public function add($ville, $pays, $description, $image, $categorie, $latitude = null, $longitude = null) {
        try {
            $sql = "INSERT INTO destination (ville, pays, description, image, categorie, latitude, longitude)
                    VALUES (:ville, :pays, :description, :image, :categorie, :latitude, :longitude)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':ville',       $ville);
            $stmt->bindParam(':pays',        $pays);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':image',       $image);
            $stmt->bindParam(':categorie',   $categorie);
            $stmt->bindParam(':latitude',    $latitude);
            $stmt->bindParam(':longitude',   $longitude);
            return $stmt->execute();
        } catch (Exception $e) {
            die("Erreur INSERT: " . $e->getMessage());
        }
    }

    // UPDATE (avec latitude / longitude)
    public function update($id, $ville, $pays, $description, $image, $categorie, $latitude = null, $longitude = null) {
        try {
            $sql = "UPDATE destination SET
                        ville       = :ville,
                        pays        = :pays,
                        description = :description,
                        image       = :image,
                        categorie   = :categorie,
                        latitude    = :latitude,
                        longitude   = :longitude
                    WHERE id_destination = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':ville',       $ville);
            $stmt->bindParam(':pays',        $pays);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':image',       $image);
            $stmt->bindParam(':categorie',   $categorie);
            $stmt->bindParam(':latitude',    $latitude);
            $stmt->bindParam(':longitude',   $longitude);
            $stmt->bindParam(':id',          $id);
            return $stmt->execute();
        } catch (Exception $e) {
            die("Erreur UPDATE: " . $e->getMessage());
        }
    }

    // DELETE
    public function delete($id) {
        try {
            $sql = "DELETE FROM destination WHERE id_destination = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            die("Erreur DELETE: " . $e->getMessage());
        }
    }

    // GET BY ID
    public function getById($id) {
        try {
            $sql = "SELECT * FROM destination WHERE id_destination = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur SELECT BY ID: " . $e->getMessage());
        }
    }

    // FRONT — destinations avec circuits valides (date >= today ET places > 0)
    public function getWithValidCircuits($params = []) {
        try {
            $search    = trim($params['search']    ?? '');
            $sort      = $params['sort']           ?? 'ville_asc';
            $pays      = trim($params['pays']      ?? '');
            $categorie = trim($params['categorie'] ?? '');
            $prix_min  = $params['prix_min']       ?? '';
            $prix_max  = $params['prix_max']       ?? '';
            $duree_min = $params['duree_min']      ?? '';
            $duree_max = $params['duree_max']      ?? '';
            $date_from = $params['date_from']      ?? '';
            $date_to   = $params['date_to']        ?? '';

            $bindings  = [];

            $sql = "SELECT d.*,
                           COUNT(c.id_circuit) AS nb_circuits,
                           MIN(c.prix)         AS prix_min_circuit,
                           MAX(c.prix)         AS prix_max_circuit
                    FROM destination d
                    INNER JOIN circuit c ON c.id_destination = d.id_destination
                        AND c.date_depart >= CURDATE()
                        AND c.nb_places   > 0";

            if ($prix_min !== '' && is_numeric($prix_min)) {
                $sql .= " AND c.prix >= :prix_min";
                $bindings[':prix_min'] = (float)$prix_min;
            }
            if ($prix_max !== '' && is_numeric($prix_max)) {
                $sql .= " AND c.prix <= :prix_max";
                $bindings[':prix_max'] = (float)$prix_max;
            }
            if ($duree_min !== '' && is_numeric($duree_min)) {
                $sql .= " AND c.duree >= :duree_min";
                $bindings[':duree_min'] = (int)$duree_min;
            }
            if ($duree_max !== '' && is_numeric($duree_max)) {
                $sql .= " AND c.duree <= :duree_max";
                $bindings[':duree_max'] = (int)$duree_max;
            }
            if ($date_from !== '') {
                $sql .= " AND c.date_depart >= :date_from";
                $bindings[':date_from'] = $date_from;
            }
            if ($date_to !== '') {
                $sql .= " AND c.date_depart <= :date_to";
                $bindings[':date_to'] = $date_to;
            }

            $where = [];
            if ($search !== '') {
                $where[] = "(d.ville LIKE :search OR d.pays LIKE :search OR d.categorie LIKE :search)";
                $bindings[':search'] = '%' . $search . '%';
            }
            if ($pays !== '') {
                $where[] = "d.pays LIKE :pays";
                $bindings[':pays'] = '%' . $pays . '%';
            }
            if ($categorie !== '') {
                $where[] = "d.categorie LIKE :categorie";
                $bindings[':categorie'] = '%' . $categorie . '%';
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            $sql .= " GROUP BY d.id_destination";

            switch ($sort) {
                case 'ville_desc':       $sql .= " ORDER BY d.ville DESC"; break;
                case 'nb_circuits_desc': $sql .= " ORDER BY nb_circuits DESC"; break;
                case 'nb_circuits_asc':  $sql .= " ORDER BY nb_circuits ASC"; break;
                default:                 $sql .= " ORDER BY d.ville ASC";
            }

            $stmt = $this->pdo->prepare($sql);
            foreach ($bindings as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur getWithValidCircuits: " . $e->getMessage());
        }
    }

    public function getDistinctPays() {
        $stmt = $this->pdo->prepare("SELECT DISTINCT pays FROM destination ORDER BY pays ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getDistinctCategories() {
        $stmt = $this->pdo->prepare("SELECT DISTINCT categorie FROM destination ORDER BY categorie ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
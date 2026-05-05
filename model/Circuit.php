<?php
class Circuit {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ALL circuits
    public function getAll() {
        $sql  = "SELECT * FROM circuit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BY destination
    public function getByDestination($id) {
        $sql  = "SELECT * FROM circuit WHERE id_destination = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ADD
    public function add($titre, $duree, $prix, $places, $date, $id_dest, $hotel) {
        $sql = "INSERT INTO circuit (titre, duree, prix, nb_places, date_depart, id_destination, id_hotel)
                VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titre, $duree, $prix, $places, $date, $id_dest, $hotel]);
    }

    // DELETE
    public function delete($id) {
        $sql  = "DELETE FROM circuit WHERE id_circuit = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // UPDATE
    public function update($id, $titre, $duree, $prix, $places, $date, $dest, $hotel) {
        $sql = "UPDATE circuit SET
                    titre          = ?,
                    duree          = ?,
                    prix           = ?,
                    nb_places      = ?,
                    date_depart    = ?,
                    id_destination = ?,
                    id_hotel       = ?
                WHERE id_circuit = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titre, $duree, $prix, $places, $date, $dest, $hotel, $id]);
    }

    // GET BY ID
    public function getById($id) {
        $sql  = "SELECT * FROM circuit WHERE id_circuit = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // COUNT circuits par destination [id_dest => nb]
    public function countByDestination() {
        $sql  = "SELECT id_destination, COUNT(*) as nb FROM circuit GROUP BY id_destination";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[$row['id_destination']] = $row['nb'];
        }
        return $result;
    }

    // =====================================================
    // FRONT — search / filter / sort circuits
    // =====================================================
    public function search($params = []) {
        $search    = trim($params['search']    ?? '');
        $sort      = $params['sort']           ?? 'date_asc';
        $prix_min  = $params['prix_min']       ?? '';
        $prix_max  = $params['prix_max']       ?? '';
        $duree_min = $params['duree_min']      ?? '';
        $duree_max = $params['duree_max']      ?? '';
        $date_from = $params['date_from']      ?? '';
        $date_to   = $params['date_to']        ?? '';
        $dispo     = $params['dispo']          ?? '';
        $id_dest   = $params['id_destination'] ?? '';

        $where    = [];
        $bindings = [];

        if ($id_dest !== '' && is_numeric($id_dest)) {
            $where[] = "c.id_destination = :id_dest";
            $bindings[':id_dest'] = (int)$id_dest;
        }
        if ($search !== '') {
            $where[] = "c.titre LIKE :search";
            $bindings[':search'] = '%' . $search . '%';
        }
        if ($prix_min !== '' && is_numeric($prix_min)) {
            $where[] = "c.prix >= :prix_min";
            $bindings[':prix_min'] = (float)$prix_min;
        }
        if ($prix_max !== '' && is_numeric($prix_max)) {
            $where[] = "c.prix <= :prix_max";
            $bindings[':prix_max'] = (float)$prix_max;
        }
        if ($duree_min !== '' && is_numeric($duree_min)) {
            $where[] = "c.duree >= :duree_min";
            $bindings[':duree_min'] = (int)$duree_min;
        }
        if ($duree_max !== '' && is_numeric($duree_max)) {
            $where[] = "c.duree <= :duree_max";
            $bindings[':duree_max'] = (int)$duree_max;
        }
        if ($date_from !== '') {
            $where[] = "c.date_depart >= :date_from";
            $bindings[':date_from'] = $date_from;
        }
        if ($date_to !== '') {
            $where[] = "c.date_depart <= :date_to";
            $bindings[':date_to'] = $date_to;
        }
        if ($dispo === '1') {
            $where[] = "c.nb_places > 0";
        }

        $sql = "SELECT c.*, d.ville, d.pays FROM circuit c
                LEFT JOIN destination d ON d.id_destination = c.id_destination";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        switch ($sort) {
            case 'prix_asc':   $sql .= " ORDER BY c.prix ASC";        break;
            case 'prix_desc':  $sql .= " ORDER BY c.prix DESC";       break;
            case 'duree_asc':  $sql .= " ORDER BY c.duree ASC";       break;
            case 'duree_desc': $sql .= " ORDER BY c.duree DESC";      break;
            case 'date_desc':  $sql .= " ORDER BY c.date_depart DESC"; break;
            default:           $sql .= " ORDER BY c.date_depart ASC";
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($bindings as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // BACK OFFICE — Statistiques
    // =====================================================
    public function getStats() {
        $today = date('Y-m-d');

        // Total circuits
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM circuit");
        $stmt->execute();
        $total = $stmt->fetchColumn();

        // Circuits actifs (date >= today ET places > 0)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM circuit WHERE date_depart >= ? AND nb_places > 0");
        $stmt->execute([$today]);
        $actifs = $stmt->fetchColumn();

        // Circuits expirés (date < today)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM circuit WHERE date_depart < ?");
        $stmt->execute([$today]);
        $expires = $stmt->fetchColumn();

        // Prix moyen
        $stmt = $this->pdo->prepare("SELECT AVG(prix) FROM circuit");
        $stmt->execute();
        $prix_moyen = round($stmt->fetchColumn(), 2);

        // Nb circuits par destination
        $stmt = $this->pdo->prepare("
            SELECT d.ville, d.pays, COUNT(c.id_circuit) as nb
            FROM destination d
            LEFT JOIN circuit c ON c.id_destination = d.id_destination
            GROUP BY d.id_destination
            ORDER BY nb DESC
        ");
        $stmt->execute();
        $par_destination = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total'           => $total,
            'actifs'          => $actifs,
            'expires'         => $expires,
            'prix_moyen'      => $prix_moyen,
            'par_destination' => $par_destination,
        ];
    }

    // =====================================================
    // BACK OFFICE — Notifications intelligentes
    // =====================================================
    public function getNotifications() {
        $today    = date('Y-m-d');
        $in2days  = date('Y-m-d', strtotime('+2 days'));
        $notifs   = [];

        // 1. Circuits date expirée
        $stmt = $this->pdo->prepare("
            SELECT c.titre, c.date_depart
            FROM circuit c
            WHERE c.date_depart < ?
            ORDER BY c.date_depart DESC
            LIMIT 10
        ");
        $stmt->execute([$today]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $notifs[] = ['type' => 'expired', 'msg' => 'Circuit expiré : "' . $row['titre'] . '" — ' . $row['date_depart']];
        }

        // 2. Circuits bientôt expirés (départ dans <= 2 jours)
        $stmt = $this->pdo->prepare("
            SELECT c.titre, c.date_depart
            FROM circuit c
            WHERE c.date_depart >= ? AND c.date_depart <= ?
            ORDER BY c.date_depart ASC
        ");
        $stmt->execute([$today, $in2days]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $notifs[] = ['type' => 'soon', 'msg' => 'Départ imminent : "' . $row['titre'] . '" — ' . $row['date_depart']];
        }

        // 3. Places presque pleines (<= 3 places restantes, > 0)
        $stmt = $this->pdo->prepare("
            SELECT c.titre, c.nb_places
            FROM circuit c
            WHERE c.nb_places > 0 AND c.nb_places <= 3 AND c.date_depart >= ?
        ");
        $stmt->execute([$today]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $notifs[] = ['type' => 'full', 'msg' => 'Places presque pleines : "' . $row['titre'] . '" — ' . $row['nb_places'] . ' restante(s)'];
        }

        // 4. Destinations sans circuits
        $stmt = $this->pdo->prepare("
            SELECT d.ville, d.pays
            FROM destination d
            LEFT JOIN circuit c ON c.id_destination = d.id_destination
            WHERE c.id_circuit IS NULL
        ");
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $notifs[] = ['type' => 'empty', 'msg' => 'Aucun circuit : ' . $row['ville'] . ' (' . $row['pays'] . ')'];
        }

        return $notifs;
    }
}
?>
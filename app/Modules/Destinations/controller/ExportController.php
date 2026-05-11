<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Circuit.php';
require_once __DIR__ . '/../model/Destination.php';

// ===== TCPDF via Composer =====
// Si TCPDF n'est pas installé, vérifier vendor/autoload.php
$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    die('TCPDF non installé. Exécutez : composer require tecnickcom/tcpdf dans le dossier du projet.');
}
require_once $autoload;

$db  = new Database();
$pdo = $db->connect();

$type = $_GET['type'] ?? 'circuits';

// =============================================
// HELPER — couleurs Voyagio
// =============================================
define('CLR_BEIGE',  [245, 237, 230]);
define('CLR_NUDE',   [232, 207, 193]);
define('CLR_BROWN_R', 166); define('CLR_BROWN_G', 123); define('CLR_BROWN_B', 91);
define('CLR_GREEN_R', 156); define('CLR_GREEN_G', 175); define('CLR_GREEN_B', 136);
define('CLR_DARK_R',   58); define('CLR_DARK_G',   58); define('CLR_DARK_B',   58);

// =============================================
// CRÉER PDF
// =============================================
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator('Voyagio');
$pdf->SetAuthor('Voyagio');
$pdf->SetMargins(18, 28, 18);
$pdf->SetHeaderMargin(8);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// =============================================
// BACKGROUND
// =============================================
$pdf->SetFillColor(CLR_BEIGE[0], CLR_BEIGE[1], CLR_BEIGE[2]);
$pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'F');

// =============================================
// HEADER BAND
// =============================================
$pdf->SetFillColor(CLR_BROWN_R, CLR_BROWN_G, CLR_BROWN_B);
$pdf->Rect(0, 0, $pdf->getPageWidth(), 22, 'F');

// Logo text
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetXY(18, 5);
$pdf->Cell(60, 12, 'VOYAGIO', 0, 0, 'L');

// Title right
$pdf->SetFont('helvetica', '', 11);
$pdf->SetXY(0, 7);
$pdf->Cell($pdf->getPageWidth() - 18, 8,
    $type === 'circuits' ? 'Liste des Circuits' : 'Liste des Destinations',
    0, 0, 'R'
);

// Date
$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(0, 14);
$pdf->SetTextColor(255, 255, 255, 70);
$pdf->Cell($pdf->getPageWidth() - 18, 6, 'Généré le ' . date('d/m/Y à H:i'), 0, 0, 'R');

$pdf->SetTextColor(CLR_DARK_R, CLR_DARK_G, CLR_DARK_B);
$pdf->SetY(28);

// =============================================
// EXPORT CIRCUITS
// =============================================
if ($type === 'circuits') {
    $circuitModel = new Circuit($pdo);
    $params = [
        'search'       => $_GET['search']    ?? '',
        'sort'         => $_GET['sort']      ?? 'date_asc',
        'prix_min'     => $_GET['prix_min']  ?? '',
        'prix_max'     => $_GET['prix_max']  ?? '',
        'duree_min'    => $_GET['duree_min'] ?? '',
        'duree_max'    => $_GET['duree_max'] ?? '',
        'date_from'    => $_GET['date_from'] ?? '',
        'date_to'      => $_GET['date_to']   ?? '',
        'dispo'        => $_GET['dispo']     ?? '',
        'id_destination' => isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : '',
    ];
    $data = $circuitModel->search($params);

    // TABLE HEADER
    $pdf->SetFillColor(CLR_BROWN_R, CLR_BROWN_G, CLR_BROWN_B);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 9);

    $cols = [
        ['label' => 'Titre',       'w' => 72],
        ['label' => 'Destination', 'w' => 52],
        ['label' => 'Durée',       'w' => 28],
        ['label' => 'Prix (TND)',  'w' => 36],
        ['label' => 'Places',      'w' => 28],
        ['label' => 'Date départ', 'w' => 38],
    ];
    foreach ($cols as $col) {
        $pdf->Cell($col['w'], 9, $col['label'], 0, 0, 'C', true);
    }
    $pdf->Ln();

    // ROWS
    $pdf->SetFont('helvetica', '', 8.5);
    $odd = true;
    foreach ($data as $c) {
        if ($odd) $pdf->SetFillColor(CLR_NUDE[0], CLR_NUDE[1], CLR_NUDE[2]);
        else      $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(CLR_DARK_R, CLR_DARK_G, CLR_DARK_B);

        $fill = true;
        $pdf->Cell(72, 8, mb_substr($c['titre'], 0, 35), 0, 0, 'L', $fill);
        $dest = !empty($c['ville']) ? $c['ville'] . ' — ' . $c['pays'] : '—';
        $pdf->Cell(52, 8, mb_substr($dest, 0, 26), 0, 0, 'L', $fill);
        $pdf->Cell(28, 8, $c['duree'] . ' j',       0, 0, 'C', $fill);
        $pdf->Cell(36, 8, number_format((float)$c['prix'], 2, ',', ' '), 0, 0, 'C', $fill);
        $places = (int)$c['nb_places'];
        if ($places <= 3 && $places > 0) {
            $pdf->SetTextColor(192, 57, 43);
        }
        $pdf->Cell(28, 8, $places, 0, 0, 'C', $fill);
        $pdf->SetTextColor(CLR_DARK_R, CLR_DARK_G, CLR_DARK_B);
        $pdf->Cell(38, 8, $c['date_depart'], 0, 0, 'C', $fill);
        $pdf->Ln();
        $odd = !$odd;
    }

    // TOTAL
    $pdf->Ln(4);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(CLR_BROWN_R, CLR_BROWN_G, CLR_BROWN_B);
    $pdf->Cell(0, 8, 'Total : ' . count($data) . ' circuit(s)', 0, 1, 'R');

    $pdf->Output('voyagio_circuits_' . date('Ymd') . '.pdf', 'D');

// =============================================
// EXPORT DESTINATIONS
// =============================================
} else {
    $destModel = new Destination($pdo);
    $circModel = new Circuit($pdo);
    $data      = $destModel->getAll();
    $counts    = $circModel->countByDestination();

    // TABLE HEADER
    $pdf->SetFillColor(CLR_BROWN_R, CLR_BROWN_G, CLR_BROWN_B);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 9);

    $cols = [
        ['label' => 'Ville',       'w' => 60],
        ['label' => 'Pays',        'w' => 55],
        ['label' => 'Catégorie',   'w' => 50],
        ['label' => 'Nb Circuits', 'w' => 38],
        ['label' => 'Description', 'w' => 95],
    ];
    foreach ($cols as $col) {
        $pdf->Cell($col['w'], 9, $col['label'], 0, 0, 'C', true);
    }
    $pdf->Ln();

    $pdf->SetFont('helvetica', '', 8.5);
    $odd = true;
    foreach ($data as $d) {
        if ($odd) $pdf->SetFillColor(CLR_NUDE[0], CLR_NUDE[1], CLR_NUDE[2]);
        else      $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(CLR_DARK_R, CLR_DARK_G, CLR_DARK_B);

        $nb   = $counts[$d['id_destination']] ?? 0;
        $desc = mb_substr($d['description'], 0, 60);

        $pdf->Cell(60, 8, mb_substr($d['ville'],     0, 28), 0, 0, 'L', true);
        $pdf->Cell(55, 8, mb_substr($d['pays'],      0, 25), 0, 0, 'L', true);
        $pdf->Cell(50, 8, mb_substr($d['categorie'], 0, 22), 0, 0, 'L', true);
        $pdf->Cell(38, 8, $nb,                               0, 0, 'C', true);
        $pdf->Cell(95, 8, $desc,                             0, 0, 'L', true);
        $pdf->Ln();
        $odd = !$odd;
    }

    $pdf->Ln(4);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(CLR_BROWN_R, CLR_BROWN_G, CLR_BROWN_B);
    $pdf->Cell(0, 8, 'Total : ' . count($data) . ' destination(s)', 0, 1, 'R');

    $pdf->Output('voyagio_destinations_' . date('Ymd') . '.pdf', 'D');
}
?>
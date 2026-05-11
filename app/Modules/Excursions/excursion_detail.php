<?php
require_once __DIR__ . '/config/database.php'; 

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $excursion_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($excursion_id <= 0) {
        die('Invalid excursion ID');
    }
    
    // Fetch excursion details
    $query = $db->prepare("SELECT * FROM excursion WHERE id = :id");
    $query->execute(['id' => $excursion_id]);
    $excursion = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$excursion) {
        die('Excursion not found');
    }
    
    // Fetch guide details based on guide_id
    $guide_query = $db->prepare("SELECT * FROM guide WHERE id = :id");
    $guide_query->execute(['id' => $excursion['guide_id']]);
    $guide = $guide_query->fetch(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/model/RecommendationEngine.php';
    $recommendationEngine = new RecommendationEngine($db);
    $recommendedExcursions = $recommendationEngine->getSimilarExcursions($excursion, 4);
    
} catch (Exception $e) {
    die('Database Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($excursion['titre']); ?> - Voyagio</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        :root {
            --beige-primary: #D2B48C;
            --beige-dark: #C19A6B;
            --text-dark: #4A4036;
        }

        body {
            background-color: #FDF5E6 !important;
        }

        .navbar {
            border-bottom: 3px solid var(--beige-primary);
        }

        .breadcrumb-section {
            background: linear-gradient(rgba(74, 64, 54, 0.7), rgba(74, 64, 54, 0.7));
            padding: 2rem 0;
        }

        .excursion-header {
            background-color: #fff;
            padding: 2rem 0;
            border-bottom: 1px solid #eee;
        }

        .excursion-title {
            color: var(--text-dark);
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .excursion-meta {
            display: flex;
            gap: 2rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .meta-label {
            color: #666;
            font-size: 0.95rem;
        }

        .meta-value {
            color: var(--beige-dark);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .excursion-content {
            padding: 2rem 0;
        }

        .description-section h3 {
            color: var(--text-dark);
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .description-text {
            color: #555;
            line-height: 1.8;
            font-size: 1rem;
        }

        .guide-section {
            background-color: #f9f7f4;
            padding: 2rem;
            border-radius: 15px;
            margin-top: 2rem;
        }

        .guide-section h3 {
            color: var(--text-dark);
            font-weight: 800;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .guide-card {
            background: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(210, 180, 140, 0.2);
            border: 1px solid #eee;
            padding: 1.5rem;
        }

        .guide-info {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .guide-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--beige-primary), var(--beige-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 2rem;
            flex-shrink: 0;
        }

        .guide-details h4 {
            color: var(--text-dark);
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .guide-details p {
            color: #666;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .guide-detail-label {
            color: var(--beige-dark);
            font-weight: 700;
        }

        .btn-primary {
            background-color: var(--beige-primary) !important;
            border-color: var(--beige-primary) !important;
            color: #fff !important;
        }

        .back-link {
            color: var(--beige-dark);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
<link rel="stylesheet" href="../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../dashboard.php">&larr; Dashboard</a>

    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0" style="color: var(--beige-dark);"><i class="fa fa-compass me-3"></i>Voyagio</h2>
        </a>
        <div class="collapse navbar-collapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link">Home</a>
                <a href="admin.php" class="nav-item nav-link">Admin Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0">
                    <li class="breadcrumb-item"><a href="index.php" style="color: var(--beige-primary);">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php#excursions" style="color: var(--beige-primary);">Excursions</a></li>
                    <li class="breadcrumb-item active text-white"><?php echo htmlspecialchars($excursion['titre']); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <a href="index.php" class="back-link">
                <i class="fa fa-arrow-left"></i> Back to Excursions
            </a>

            <div class="excursion-header">
                <h1 class="excursion-title"><?php echo htmlspecialchars($excursion['titre']); ?></h1>
                
                <div class="excursion-meta">
                    <div class="meta-item">
                        <i class="fa fa-clock" style="color: var(--beige-dark); font-size: 1.2rem;"></i>
                        <div>
                            <div class="meta-label">Duration</div>
                            <div class="meta-value"><?php echo htmlspecialchars($excursion['duree']); ?></div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-tag" style="color: var(--beige-dark); font-size: 1.2rem;"></i>
                        <div>
                            <div class="meta-label">Price</div>
                            <div class="meta-value"><?php echo htmlspecialchars($excursion['prix']); ?> TND</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-map" style="color: var(--beige-dark); font-size: 1.2rem;"></i>
                        <div>
                            <div class="meta-label">Circuit ID</div>
                            <div class="meta-value">#<?php echo htmlspecialchars($excursion['circuit_id']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="excursion-content">
                <div class="description-section">
                    <h3><i class="fa fa-info-circle me-2" style="color: var(--beige-dark);"></i>About This Excursion</h3>
                    <p class="description-text"><?php echo nl2br(htmlspecialchars($excursion['description'])); ?></p>
                </div>

                <?php if ($guide): ?>
                <div class="guide-section">
                    <h3>
                        <i class="fa fa-user-tie" style="color: var(--beige-dark);"></i>
                        Your Guide
                    </h3>
                    
                    <div class="guide-card">
                        <div class="guide-info">
                            <div class="guide-avatar">
                                <?php echo strtoupper(substr($guide['prenom'], 0, 1)) . strtoupper(substr($guide['nom'], 0, 1)); ?>
                            </div>
                            <div class="guide-details flex-grow-1">
                                <h4><?php echo htmlspecialchars($guide['prenom'] . ' ' . $guide['nom']); ?></h4>
                                
                                <p>
                                    <span class="guide-detail-label"><i class="fa fa-briefcase me-1"></i>Specialty:</span>
                                    <?php echo htmlspecialchars($guide['specialite']); ?>
                                </p>
                                
                                <p>
                                    <span class="guide-detail-label"><i class="fa fa-language me-1"></i>Languages:</span>
                                    <?php echo htmlspecialchars($guide['langue']); ?>
                                </p>
                                
                                <p>
                                    <span class="guide-detail-label"><i class="fa fa-phone me-1"></i>Contact:</span>
                                    <a href="tel:<?php echo htmlspecialchars($guide['tel']); ?>" style="color: var(--beige-dark); text-decoration: none;">
                                        <?php echo htmlspecialchars($guide['tel']); ?>
                                    </a>
                                </p>

                                <p style="margin-top: 1rem;">
                                    <i class="fa fa-certificate" style="color: var(--beige-dark);"></i>
                                    <span style="color: var(--beige-dark); font-weight: 700;">Certified Guide</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="guide-section">
                    <h3><i class="fa fa-user-tie me-2" style="color: var(--beige-dark);"></i>Your Guide</h3>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>Guide information not available at the moment.
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($recommendedExcursions)): ?>
            <div class="recommendation-section mt-5">
                <div class="row mb-4">
                    <div class="col-12">
                        <h3 class="section-title">Similar Excursions</h3>
                        <p class="text-muted">These AI-assisted suggestions match the current trip style and activity.</p>
                    </div>
                </div>
                <div class="row g-4">
                    <?php foreach ($recommendedExcursions as $rec): ?>
                        <div class="col-lg-3 col-md-6">
                            <a href="excursion_detail.php?id=<?php echo $rec['id']; ?>" style="text-decoration: none; color: inherit;">
                                <div class="excursion-item" style="border-radius: 15px; padding: 1rem; background: #fff; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(210, 180, 140, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(210, 180, 140, 0.2)';">
                                    <h5 class="excursion-name"><?php echo htmlspecialchars($rec['titre']); ?></h5>
                                    <p class="excursion-description"><?php echo htmlspecialchars(substr($rec['description'], 0, 70)) . '...'; ?></p>
                                    <div class="excursion-details">
                                        <span class="excursion-duration"><i class="fa fa-clock me-1" style="color: var(--beige-dark);"></i><?php echo htmlspecialchars($rec['duree']); ?></span>
                                        <span class="excursion-price"><?php echo htmlspecialchars($rec['prix']); ?> TND</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="mt-4">
                <a href="index.php" class="btn btn-primary">
                    <i class="fa fa-arrow-left me-2"></i>Back to Excursions
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

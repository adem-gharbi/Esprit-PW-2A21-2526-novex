<?php
require_once __DIR__ . '/config/database.php'; 
require_once __DIR__ . '/model/RecommendationEngine.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = $db->query("SELECT * FROM guide");
    $guides = $query->fetchAll(PDO::FETCH_ASSOC);

    $query = $db->query("SELECT * FROM excursion");
    $excursions = $query->fetchAll(PDO::FETCH_ASSOC);

    $recommendationEngine = new RecommendationEngine($db);
    $recommendQuery = trim($_POST['recommend_query'] ?? $_GET['recommend_query'] ?? '');
    $recommendMessage = '';
    if ($recommendQuery === '') {
        $recommendedExcursions = [];
    } else {
        $recommendedExcursions = $recommendationEngine->recommendForPreferences($recommendQuery, 4);
        if (empty($recommendedExcursions)) {
            $recommendMessage = 'No recommendations were found for "' . htmlspecialchars($recommendQuery, ENT_QUOTES, 'UTF-8') . '". Try broader keywords like adventure, family, or budget.';
        }
    }
} catch (Exception $e) {
    die('Database Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Voyagio - Travel with Experts</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        :root {
            --beige-primary: #D2B48C; /* Tan/Beige */
            --beige-dark: #C19A6B;    /* Fallow/Dark Beige */
            --text-dark: #4A4036;     /* Deep Coffee/Gray */
        }

        .recommendation-widget {
            background: #ffffff;
            border-radius: 22px;
            border: 1px solid #eee;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.05);
        }

        .widget-card {
            padding: 2rem;
        }

        .widget-suggestions li {
            margin-bottom: 0.6rem;
            color: #4a4036;
            font-weight: 600;
        }

        .widget-suggestions li i {
            color: var(--beige-dark);
        }

        .recommendation-note {
            color: #7f6e5d;
            font-size: 0.95rem;
        }

        body {
            background-color: #FDF5E6 !important; /* Old Lace / Soft Cream */
        }

        .navbar {
            border-bottom: 3px solid var(--beige-primary);
        }

        .hero-header {
            background: linear-gradient(rgba(74, 64, 54, 0.7), rgba(74, 64, 54, 0.7)), url('assets/images/hero-bg.jpg') center center no-repeat;
            background-size: cover;
            background-color: var(--beige-dark) !important;
        }

        .guide-item {
            background: #ffffff !important;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(210, 180, 140, 0.2);
            transition: 0.3s;
            border: 1px solid #eee;
        }
        
        .guide-name {
            color: var(--text-dark) !important; 
            font-weight: 800;
        }

        /* Swapped Red for Beige */
        .guide-specialty {
            color: var(--beige-dark) !important;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .excursion-item {
            background: #ffffff !important;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(210, 180, 140, 0.2);
            transition: 0.3s;
            border: 1px solid #eee;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .excursion-img {
            height: 220px;
            overflow: hidden;
            border-radius: 15px 15px 0 0;
        }

        .excursion-img img {
            height: 100%;
            object-fit: cover;
            width: 100%;
            filter: sepia(10%);
        }

        .excursion-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .excursion-name {
            color: var(--text-dark) !important; 
            font-weight: 800;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .excursion-destination {
            color: var(--beige-dark) !important;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }

        .excursion-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .excursion-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 1rem;
        }

        .excursion-duration {
            color: var(--text-dark) !important;
            font-size: 0.85rem;
        }

        .excursion-price {
            color: var(--beige-dark) !important;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .text-primary {
            color: var(--beige-dark) !important;
        }

        .btn-primary {
            background-color: var(--beige-primary) !important;
            border-color: var(--beige-primary) !important;
            color: #fff !important;
        }

        .guide-img img {
            height: 280px;
            object-fit: cover;
            width: 100%;
            filter: sepia(10%); /* Subtle travel filter */
        }

        .section-title {
            color: var(--text-dark) !important;
            font-weight: 800;
            margin-bottom: 3rem;
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
                <a href="index.php" class="nav-item nav-link active">Home</a>
                <a href="admin.php" class="nav-item nav-link">Admin Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid hero-header py-5 mb-5">
        <div class="container py-5 text-center">
            <h1 class="display-3 text-white mb-3">Our Expert Guides & Excursions</h1>
            <p class="text-white-50">Discover authentic experiences curated by professionals.</p>
        </div>
    </div>

    <!-- Smart Trip Widget -->
    <div class="container-xxl pb-5">
        <div class="container">
            <div class="recommendation-widget widget-card mb-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="section-title mb-3">Smart Trip Widget</h2>
                        <p class="recommendation-note mb-3">Type a travel preference and let the engine suggest the best matching excursions for your mood, budget, and pace.</p>
                        <ul class="list-unstyled widget-suggestions">
                            <li><i class="fa fa-check-circle me-2"></i>Family-friendly cultural tour</li>
                            <li><i class="fa fa-check-circle me-2"></i>Budget day trip</li>
                            <li><i class="fa fa-check-circle me-2"></i>Luxury experience</li>
                            <li><i class="fa fa-check-circle me-2"></i>Adventure and outdoor activities</li>
                        </ul>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <span class="btn btn-primary btn-lg">Search smarter</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guides Section -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row mb-5 text-center">
                <div class="col-12">
                    <h2 class="section-title">Our Expert Guides</h2>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                <?php foreach ($guides as $g): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="guide-item">
                        <div class="guide-img overflow-hidden">
                            <img src="assets/images/<?php echo htmlspecialchars($g['photo']); ?>" alt="Guide">
                        </div>
                        <div class="text-center p-4">
                            <h5 class="guide-name mb-1">
                                <?php echo htmlspecialchars($g['prenom'] . ' ' . $g['nom']); ?>
                            </h5>
                            
                            <p class="guide-specialty mb-3">
                                <?php echo htmlspecialchars($g['specialite']); ?>
                            </p>
                            
                            <div class="d-flex justify-content-center border-top pt-3">
                                <small class="text-muted me-3">
                                    <i class="fa fa-language me-2" style="color: var(--beige-dark);"></i>
                                    <?php echo htmlspecialchars($g['langue']); ?>
                                </small>
                                <small style="color: var(--beige-dark);">
                                    <i class="fa fa-certificate me-1"></i>Certified
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Smart Trip Recommendation Section -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row mb-4 text-center">
                <div class="col-12">
                    <h2 class="section-title">Smart Trip Recommendation Engine</h2>
                    <p class="text-muted">Tell us what type of experience you want and get personalized trip suggestions powered by our AI-style match engine.</p>
                </div>
            </div>
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8">
                    <form method="post" class="d-flex gap-2">
                        <input type="text" name="recommend_query" value="<?php echo htmlspecialchars($recommendQuery); ?>" class="form-control" placeholder="e.g. cultural adventure, family-friendly, budget day trip" />
                        <button type="submit" class="btn btn-primary">Find Trips</button>
                    </form>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                <?php if ($recommendQuery === ''): ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">Enter a preference above to see matching trips here.</p>
                    </div>
                <?php elseif (!empty($recommendedExcursions)): ?>
                    <?php foreach ($recommendedExcursions as $rec): ?>
                    <div class="col-lg-3 col-md-6">
                        <a href="excursion_detail.php?id=<?php echo $rec['id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="excursion-item" style="cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(210, 180, 140, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(210, 180, 140, 0.2)';">
                                <div class="excursion-content">
                                    <h5 class="excursion-name"><?php echo htmlspecialchars($rec['titre']); ?></h5>
                                    <p class="excursion-description"><?php echo htmlspecialchars(substr($rec['description'], 0, 80)) . '...'; ?></p>
                                    <div class="excursion-details">
                                        <span class="excursion-duration"><i class="fa fa-clock me-1" style="color: var(--beige-dark);"></i><?php echo htmlspecialchars($rec['duree']); ?></span>
                                        <span class="excursion-price"><?php echo htmlspecialchars($rec['prix']); ?> TND</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No recommendations were found for "<?php echo htmlspecialchars($recommendQuery); ?>". Try broader keywords like adventure, family, or budget.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Excursions Section -->
    <div class="container-xxl py-5" style="background-color: #f9f7f4;">
        <div class="container">
            <div class="row mb-5 text-center">
                <div class="col-12">
                    <h2 class="section-title">Featured Excursions</h2>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                <?php if (!empty($excursions)): ?>
                    <?php foreach ($excursions as $e): ?>
                    <div class="col-lg-3 col-md-6">
                        <a href="excursion_detail.php?id=<?php echo $e['id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="excursion-item" style="cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(210, 180, 140, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(210, 180, 140, 0.2)';">
                                <div class="excursion-content">
                                    <h5 class="excursion-name">
                                        <?php echo htmlspecialchars($e['titre']); ?>
                                    </h5>
                                    <p class="excursion-description">
                                        <?php echo htmlspecialchars(substr($e['description'], 0, 80)) . '...'; ?>
                                    </p>
                                    <div class="excursion-details">
                                        <span class="excursion-duration">
                                            <i class="fa fa-clock me-1" style="color: var(--beige-dark);"></i><?php echo htmlspecialchars($e['duree']); ?>
                                        </span>
                                        <span class="excursion-price">
                                            <?php echo htmlspecialchars($e['prix']); ?> TND
                                        </span>
                                    </div>
                                    <div style="margin-top: 1rem;">
                                        <span class="btn btn-sm btn-primary" style="display: inline-block;">
                                            View Details <i class="fa fa-arrow-right ms-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No excursions available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
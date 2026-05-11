<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Vacation - Nos Guides</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="assets/css/jquery.timepicker.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/icomoon.css">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../dashboard.php">&larr; Dashboard</a>

	  <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="index.php">Vacation<span>Travel Agency</span></a>
	      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> Menu
	      </button>

	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav ml-auto">
	          <li class="nav-item active"><a href="index.php" class="nav-link">Home</a></li>
	          <li class="nav-item"><a href="#" class="nav-link">About</a></li>
	          <li class="nav-item"><a href="#" class="nav-link">Destination</a></li>
	          <li class="nav-item"><a href="#" class="nav-link">Contact</a></li>
	        </ul>
	      </div>
	    </div>
	  </nav>
    
    <div class="hero-wrap js-fullheight" style="background-image: url('assets/images/bg_2.jpg');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-center" data-scrollax-parent="true">
          <div class="col-md-9 text text-center ftco-animate" data-scrollax=" properties: { translateY: '70%' }">
          	<a href="https://vimeo.com/45830194" class="icon-video popup-vimeo d-flex align-items-center justify-content-center mb-4">
          		<span class="ion-ios-play"></span>
            </a>
            <p class="caps" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">Travel to any corner of the world</p>
            <h1 data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">Make Your Tour Amazing With Us</h1>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section services-section bg-light">
      <div class="container">
        <div class="row d-flex">
          <div class="col-md-6 order-md-last heading-section pl-md-5 ftco-animate">
          	<h2 class="mb-4">It's time to start your adventure</h2>
            <p>A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
            <p><a href="#" class="btn btn-primary py-3 px-4">Search Destination</a></p>
          </div>
          <div class="col-md-6">
          	<div class="row">
          		<div class="col-md-6 d-flex align-self-stretch ftco-animate">
		            <div class="media block-6 services d-block">
		              <div class="icon"><span class="flaticon-paragliding"></span></div>
		              <div class="media-body">
		                <h3 class="heading mb-3">Activities</h3>
		                <p>A small river named Duden flows by their place.</p>
		              </div>
		            </div>      
		          </div>
		          <div class="col-md-6 d-flex align-self-stretch ftco-animate">
		            <div class="media block-6 services d-block">
		              <div class="icon"><span class="flaticon-tour-guide"></span></div>
		              <div class="media-body">
		                <h3 class="heading mb-3">Private Guide</h3>
		                <p>A small river named Duden flows by their place.</p>
		              </div>
		            </div>      
		          </div>
          	</div>
          </div>
        </div>
      </div>
    </section>

    <section class="ftco-section">
    	<div class="container">
    		<div class="row justify-content-center pb-4">
          <div class="col-md-12 heading-section text-center ftco-animate">
            <h2 class="mb-4">Best Place Guides</h2>
          </div>
        </div>
        <div class="row">
          <?php if (!empty($guides)): ?>
            <?php foreach ($guides as $g): ?>
        	<div class="col-md-3 ftco-animate">
        		<div class="project-destination">
        			<div class="img" style="background-image: url(<?php 
                        $photoPath = isset($g['photo']) ? $g['photo'] : '';
                        // Hard-coded path check for your environment
                        if (!empty($photoPath) && file_exists(__DIR__ . '/../../assets/images/' . $photoPath)) {
                            echo 'assets/images/' . htmlspecialchars($photoPath);
                        } else {
                            echo "https://ui-avatars.com/api/?name=" . urlencode(isset($g['prenom']) ? $g['prenom'] : 'Guide') . "&background=random&size=500"; 
                        }
                    ?>);">
        				<div class="text">
        					<h3><?php echo htmlspecialchars((isset($g['prenom']) ? $g['prenom'] : '') . ' ' . (isset($g['nom']) ? $g['nom'] : '')); ?></h3>
        					<span><?php echo htmlspecialchars(isset($g['specialite']) ? $g['specialite'] : 'Expert Guide'); ?></span>
        				</div>
        			</div>
        		</div>
        	</div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-md-12 text-center">
              <p>Aucun guide trouvé dans la base <b>voyage</b>.</p>
            </div>
          <?php endif; ?>
        </div>
    	</div>
    </section>

    <section class="ftco-section">
    	<div class="container">
    		<div class="row justify-content-center pb-4">
          <div class="col-md-12 heading-section text-center ftco-animate">
            <h2 class="mb-4">Featured Excursions</h2>
          </div>
        </div>
        <div class="row">
          <?php if (!empty($excursions)): ?>
            <?php foreach ($excursions as $e): ?>
        	<div class="col-md-3 ftco-animate">
        		<a href="excursion_detail.php?id=<?php echo htmlspecialchars($e['id']); ?>" style="text-decoration: none; color: inherit;">
        			<div class="project-destination" style="cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.08)';">
        				<div class="text">
        					<h3><?php echo htmlspecialchars(isset($e['titre']) ? $e['titre'] : 'Excursion'); ?></h3>
        					<span><?php echo htmlspecialchars(isset($e['duree']) ? $e['duree'] : 'Duration'); ?></span>
        				</div>
        				<div class="p-3">
        					<p class="mb-2"><small><?php echo htmlspecialchars(substr(isset($e['description']) ? $e['description'] : '', 0, 80)) . '...'; ?></small></p>
        					<div class="d-flex justify-content-between align-items-center">
        						<small style="color: #C19A6B;"><i class="fa fa-clock me-1"></i><?php echo htmlspecialchars(isset($e['duree']) ? $e['duree'] : ''); ?></small>
        						<small style="color: #C19A6B; font-weight: bold;"><?php echo htmlspecialchars(isset($e['prix']) ? $e['prix'] : '0'); ?> TND</small>
        					</div>
        					<div class="mt-2">
        						<small style="color: #C19A6B; font-weight: bold;"><i class="fa fa-arrow-right me-1"></i>View Details</small>
        					</div>
        				</div>
        			</div>
        		</a>
        	</div>
            <?php endforeach; ?>
          <?php else: ?>
        					</div>
        					<div class="mt-2">
        						<small style="color: #C19A6B; font-weight: bold;"><i class="fa fa-arrow-right me-1"></i>View Details</small>
        					</div>
        				</div>
        			</div>
        		</a>
        	</div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-md-12 text-center">
              <p>No excursions available at the moment.</p>
            </div>
          <?php endif; ?>
        </div>
    	</div>
    </section>

    <footer class="ftco-footer bg-bottom" style="background-image: url(assets/images/footer-bg.jpg);">
      <div class="container">
        <div class="row mb-5">
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Vacation</h2>
              <p>Far far away, behind the word mountains.</p>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
            	<h2 class="ftco-heading-2">Have a Questions?</h2>
            	<div class="block-23 mb-3">
	              <ul>
	                <li><span class="icon icon-map-marker"></span><span class="text">Tunis, Tunisia</span></li>
	                <li><a href="#"><span class="icon icon-phone"></span><span class="text">+216 12 345 678</span></a></li>
	              </ul>
	            </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">
            <p>
              Copyright &copy;<span class="copyright-year"></span> All rights reserved | This template is made with <i class="icon-heart color-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
            </p>
          </div>
        </div>
      </div>
    </footer>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery-migrate-3.0.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.easing.1.3.js"></script>
    <script src="assets/js/jquery.waypoints.min.js"></script>
    <script src="assets/js/jquery.stellar.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/main.js"></script>
  </body>
</html>
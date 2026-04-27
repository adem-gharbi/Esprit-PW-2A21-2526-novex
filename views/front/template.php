<!DOCTYPE html>
<html>
<head>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins&display=swap" rel="stylesheet">

<style>

/* =========================
   GLOBAL STYLE VOYAGIO
========================= */

body{
    margin: 0;
    font-family: Poppins;
    background: #F5EDE6; /* beige clair */
    color: #3A3A3A;
}

/* =========================
   HEADER
========================= */

header{
    background: linear-gradient(rgba(166,123,91,0.35), rgba(166,123,91,0.35)),
                url('/voyagio_final/assets/ph3.jpg');

    background-size: cover;
    background-position: center;

    height: 350px;

    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    color: white;
    text-align: center;
}

header h1{
    font-family: "Playfair Display";
    font-size: 42px;
    margin: 0;
}

/* =========================
   NAV
========================= */

nav{
    margin-top: 10px;
}

nav a{
    color: #ffffff;
    margin: 0 12px;
    text-decoration: none;
    font-weight: bold;
    position: relative;
    transition: 0.3s;
}

nav a:hover{
    color: #E8CFC1; /* nude */
}

/* underline effect */
nav a::after{
    content: "";
    position: absolute;
    left: 0;
    bottom: -4px;
    width: 0;
    height: 2px;
    background: #E8CFC1;
    transition: 0.3s;
}

nav a:hover::after{
    width: 100%;
}

/* =========================
   GRID
========================= */

.grid{
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 40px;
}

/* =========================
   CARD HOTEL
========================= */

.card{
    background: #E8CFC1; /* nude chaud */
    width: 280px;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    transition: 0.4s;
    text-align: center;
}

.card:hover{
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

/* IMAGE */
.card img{
    width: 100%;
    height: 180px;
    object-fit: cover;
}

/* CONTENT */
.card-content{
    padding: 15px;
}

.card h3{
    margin: 0;
    font-family: "Playfair Display";
    color: #3A3A3A;
}

.card p{
    margin: 5px 0;
    color: #555;
}

/* =========================
   BADGE
========================= */

.badge{
    background: #9CAF88; /* vert sauge */
    color: white;
    padding: 5px 10px;
    border-radius: 8px;
    display: inline-block;
}

/* =========================
   BUTTON (SIMPLE ONLY)
========================= */

.btn{
    display: inline-block;
    margin-top: 10px;
    background: #A67B5B;
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    transition: 0.3s;
}

.btn:hover{
    background: #8c6248;
    transform: scale(1.05);
}

/* =========================
   CONTAINER
========================= */

.container{
    padding: 20px;
}
.actions{
    display:flex;
    justify-content:center;
    gap:10px;
    margin-top:12px;
}

/* MODIFIER */
.btn-edit{
    background: linear-gradient(135deg, #9CAF88, #7f9a70);
    color: white;
    padding: 8px 12px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: bold;
    font-size: 13px;
    transition: 0.3s;
    box-shadow: 0 5px 12px rgba(0,0,0,0.15);
}

.btn-edit:hover{
    transform: translateY(-3px);
}

/* SUPPRIMER */
.btn-delete{
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    padding: 8px 12px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: bold;
    font-size: 13px;
    transition: 0.3s;
    box-shadow: 0 5px 12px rgba(0,0,0,0.15);
}

.btn-delete:hover{
    transform: translateY(-3px);
}
/* =========================
   FORMULAIRE STYLE SEARCH
========================= */

.search-box{
    position: relative;

    display: flex;
    gap: 15px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;   /* 🔥 important si champs nombreux */

    background: #F5EDE6;

    padding: 50px 25px;   /* 🔥 PLUS GRAND espace interne */
    border-radius: 25px;

    width: 98%;
    max-width: 1400px;

    margin: -120px auto 50px auto;  /* 🔥 monte sur la photo */

    box-shadow: 0 25px 50px rgba(0,0,0,0.30);
    z-index: 100;
}

/* INPUT + SELECT */
.search-box input,
.search-box select{
    padding: 14px 16px;   /* 🔥 espace d'écriture augmenté */
    border-radius: 10px;
    border: 1px solid #ccc;
    min-width: 180px;     /* 🔥 champs plus larges */
    font-size: 15px;      /* 🔥 texte plus lisible */
}
/* BUTTON */
.search-box .btn{
    margin:0;
}
</style>
</head>

<body>

<header>
    <h1>Voyagio 🌍</h1>

    <nav>
        <a href="index.php">🏨 Hotels</a>
        <a href="reservation.php">📅 Reservations</a>
    </nav>
</header>

<div class="container">

    <?= $content ?>

</div>

</body>
</html>
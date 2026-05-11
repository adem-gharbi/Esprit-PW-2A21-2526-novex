<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link href="https://fonts.googleapis.com/css2?family=Poppins&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
/* ===== VOYAGIO BACKOFFICE PREMIUM ===== */

body {
    margin: 0;
    font-family: 'Outfit', sans-serif;
    background: linear-gradient(135deg, #F5EDE6, #fdfaf7);
}

/* APP LAYOUT */
.app-shell {
    display: grid;
    grid-template-columns: 270px 1fr;
    min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
    background: linear-gradient(180deg, #A67B5B, #7a5a3f);
    color: white;
    padding: 30px;
    box-shadow: 10px 0 30px rgba(0,0,0,0.1);
    position: relative;
}

.brand {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 40px;
    letter-spacing: 1px;
}

.menu {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.menu-item {
    padding: 14px;
    border-radius: 12px;
    color: white;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    transition: 0.3s;
}

.menu-item::before {
    content: "";
    position: absolute;
    left: -100%;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.15);
    transition: 0.3s;
}

.menu-item:hover::before {
    left: 0;
}

/* CONTENT */
.content {
    padding: 35px;
}

/* TOPBAR */
.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 18px 22px;
    background: white;
    border-radius: 18px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.page-title {
    font-size: 26px;
    font-weight: 800;
    color: #A67B5B;
}

.user {
    background: linear-gradient(135deg, #E8CFC1, #f3e6de);
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: 600;
}

/* PANEL */
.panel-card {
    background: white;
    padding: 30px;
    border-radius: 22px;
    box-shadow: 0 15px 40px rgba(166,123,91,0.15);
    position: relative;
    overflow: hidden;
}

/* décor cercle */
.panel-card::before {
    content: "";
    position: absolute;
    top: -60px;
    right: -60px;
    width: 180px;
    height: 180px;
    background: rgba(166,123,91,0.08);
    border-radius: 50%;
}

/* BUTTON GLOBAL */
.btn {
    background: linear-gradient(135deg, #A67B5B, #8d6a4f);
    padding: 10px 15px;
    border-radius: 10px;
    color: white;
    text-decoration: none;
    transition: 0.3s;
    display: inline-block;
}

.btn:hover {
    transform: translateY(-2px);
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th {
    background: #E8CFC1;
    padding: 14px;
    text-transform: uppercase;
    font-size: 12px;
}

td {
    padding: 14px;
    border-bottom: 1px solid #eee;
}

tr:hover {
    background: #f9f6f3;
}

/* ACTION BUTTONS */
.btn-edit {
    background: #2ecc71;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    text-decoration: none;
}

.btn-delete {
    background: #e74c3c;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    text-decoration: none;
}
.res-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.res-title {
    font-size: 20px;
    font-weight: 800;
    color: #A67B5B;
}

.search-box {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-box input {
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid #ddd;
    outline: none;
}

.search-box button {
    background: linear-gradient(135deg, #A67B5B, #8d6a4f);
    color: white;
    border: none;
    padding: 10px 14px;
    border-radius: 10px;
    cursor: pointer;
}

/* GRID */
.res-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 18px;
}

/* CARD */
.res-card {
    background: white;
    border-radius: 18px;
    padding: 18px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
    transition: 0.3s;
}

.res-card:hover {
    transform: translateY(-6px);
}

/* HEADER CARD */
.res-hotel {
    font-size: 18px;
    font-weight: 700;
    color: #A67B5B;
}

.res-location {
    font-size: 13px;
    color: #777;
    margin-bottom: 10px;
}

.star {
    background: #E8CFC1;
    padding: 3px 8px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: bold;
}

/* INFO */
.res-info {
    font-size: 14px;
    color: #444;
    line-height: 1.6;
}

/* ID */
.res-id {
    position: absolute;
    bottom: 10px;
    right: 12px;
    font-size: 11px;
    color: #aaa;
}
.res-title {
    font-size: 28px;
    font-weight: 800;
    color: #A67B5B;
}

/* CARDS TEXT GLOBAL */
.res-card {
    font-size: 16px;
    line-height: 1.9;
}

/* TITRE HOTEL */
.res-hotel {
    font-size: 20px;
    font-weight: 800;
    margin-bottom: 6px;
}

/* LOCATION + STARS */
.res-location {
    font-size: 14px;
    color: #666;
}

/* CLIENT NAME (important) */
.res-info b {
    font-size: 18px;
    font-weight: 700;
}

/* INFO TEXT PLUS CLAIR */
.res-info {
    font-size: 15px;
    color: #333;
}

/* ESPACEMENT PLUS AÉRÉ */
.res-card {
    padding: 22px;
}

/* SEARCH PLUS LISIBLE */
.search-box input {
    font-size: 15px;
}
.panel-form {
    display: grid;
    gap: 16px;
    margin-top: 20px;
}

/* LABEL */
.panel-form label {
    font-weight: 600;
    font-size: 14px;
    color: #444;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

/* INPUTS */
.panel-form input {
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #ddd;
    font-size: 15px;
    outline: none;
    transition: 0.2s;
}

/* FOCUS EFFECT */
.panel-form input:focus {
    border-color: #A67B5B;
    box-shadow: 0 0 0 3px rgba(166,123,91,0.2);
}

/* BUTTON AREA */
.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}
/* =========================
   DASHBOARD HEADER
========================= */
.dash-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    padding:15px 20px;
    background:white;
    border-radius:16px;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);
}

/* =========================
   HOTEL SUMMARY CARD
========================= */
.hotel-summary{
    background: linear-gradient(135deg, #A67B5B, #8d6a4f);
    color:white;
    padding:20px;
    border-radius:18px;
    margin-bottom:20px;
    box-shadow:0 10px 25px rgba(166,123,91,0.25);
}

.hotel-main h2{
    margin:0;
    font-size:22px;
}

.hotel-main p{
    margin:6px 0 0;
    opacity:0.9;
}

.stars{
    background: rgba(255,255,255,0.2);
    padding:4px 10px;
    border-radius:8px;
    margin-left:10px;
}

/* =========================
   SECTION TITLE
========================= */
.section-title{
    font-size:18px;
    font-weight:700;
    margin:20px 0;
    color:#A67B5B;
}

/* =========================
   TIMELINE STYLE
========================= */
.reservation-timeline{
    display:flex;
    flex-direction:column;
    gap:15px;
}

/* ITEM */
.res-item{
    display:flex;
    gap:15px;
    background:white;
    padding:15px;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
    position:relative;
}

/* DOT */
.res-dot{
    width:12px;
    height:12px;
    background:#A67B5B;
    border-radius:50%;
    margin-top:6px;
}

/* CONTENT */
.res-client{
    font-weight:700;
    font-size:15px;
    color:#333;
}

.res-dates{
    font-size:13px;
    color:#666;
    margin-top:5px;
    line-height:1.5;
}

.res-people{
    margin-top:6px;
    font-weight:600;
    color:#A67B5B;
} 
.stat-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background: linear-gradient(135deg,#36a2eb,#9966ff);
    color:white;
    padding:10px 18px;
    border-radius:12px;
    text-decoration:none;
    font-weight:600;
    box-shadow:0 6px 15px rgba(0,0,0,0.2);
    transition:0.3s;
}

.stat-btn:hover{
    transform: translateY(-2px);
    box-shadow:0 10px 20px rgba(0,0,0,0.3);
}

</style>

</head>

<body>
<link rel="stylesheet" href="../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../back.php">&larr; Back Dashboard</a>


<div class="app-shell">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">Voyagio Admin</div>

        <div class="menu">
            <a href="admin.php" class="menu-item">Hotels</a>
            <a href="admin_reservation.php" class="menu-item">Reservations</a>
            <a href="index.php" class="menu-item">Front</a>
            <a href="admin.php?action=create" class="menu-item">Ajouter</a>
        </div>
    </aside>

    <!-- CONTENT -->
    <main class="content">

        <!-- TOPBAR -->
        <div class="topbar">
            <div>
                <div class="page-title">Admin Dashboard</div>
                <small style="color:#888;">Welcome back 👋</small>
            </div>

            <div class="user">Admin</div>
        </div>

        <!-- CONTENT DYNAMIQUE (NE PAS TOUCHER) -->
        <div class="panel-card">
            <?= $content ?>
        </div>

    </main>

</div>

</body>
</html>

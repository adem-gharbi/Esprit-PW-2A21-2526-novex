<?php
// 🚀 démarrage session (permet de stocker admin connecté)
session_start();

// 🔗 import connexion base de données
require_once "../../config/database.php";

// 📦 création objet Database
$db = new Database();

// 🔌 récupération connexion PDO
$conn = $db->getConnection();


// ============================
// 🔐 TRAITEMENT LOGIN
// ============================

// 📌 si bouton login cliqué
if(isset($_POST['login'])){

    // 👤 récupération username du formulaire
    $u = $_POST['username'];

    // 🔑 récupération password du formulaire
    $p = $_POST['password'];

    // 🧾 requête SQL sécurisée (PDO + placeholders)
    $stmt = $conn->prepare("
        SELECT * FROM admin 
        WHERE username = :u AND password = :p
    ");

    // ▶️ exécution requête avec données utilisateur
    $stmt->execute([
        ':u' => $u,
        ':p' => $p
    ]);

    // 🔍 si utilisateur trouvé
    if($stmt->rowCount() > 0){

        // 🧠 stocker admin dans session
        $_SESSION['admin'] = $u;

        // 🔁 redirection vers dashboard
        header("Location: dashboard.php");
        exit();

    } else {

        // ❌ message erreur login
        $error = "❌ Login incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <!-- 📱 responsive -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- 🏷️ titre page -->
  <title>Login - Voyagio</title>

  <!-- 🎨 CSS admin global -->
  <link rel="stylesheet" href="../../assets/css/admin.css">

  <style>

    /* =========================
       📌 PAGE LOGIN WRAPPER
    ========================== */
    .login-wrapper {
      min-height: 100vh; /* plein écran */
      display: flex; /* centrer contenu */
      align-items: center;
      justify-content: center;
      background: linear-gradient(180deg, var(--beige) 0%, #f9f5f0 100%);
      padding: 20px;
    }

    /* =========================
       📌 CARD LOGIN
    ========================== */
    .login-card {
      background: var(--panel);
      border: 1px solid var(--border);
      border-radius: 24px;
      padding: 32px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 12px 40px rgba(166, 123, 91, 0.15);
    }

    /* =========================
       📌 BRAND LOGIN
    ========================== */
    .login-card .brand {
      text-align: center;
      margin-bottom: 24px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--border);
    }

    /* 🔤 logo icon */
    .login-card .brand-icon {
      width: 56px;
      height: 56px;
      margin: 0 auto 12px;
      background: linear-gradient(135deg, var(--brown), #8d6a4f);
      border-radius: 16px;
      display: grid;
      place-items: center;
      color: #fff;
      font-weight: 700;
      font-size: 1.3rem;
    }

    /* 📛 titre */
    .login-card .brand-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.4rem;
      color: var(--brown);
      font-weight: 700;
    }

    /* 📌 sous-titre */
    .login-card .brand-subtitle {
      color: var(--text-muted);
      font-size: 0.95rem;
    }

    /* =========================
       📌 FORMULAIRE
    ========================== */
    .login-form label {
      display: block;
      margin-bottom: 16px;
      color: var(--dark);
      font-weight: 500;
    }

    /* ✏️ inputs */
    .login-form input {
      width: 100%;
      padding: 14px 16px;
      margin-top: 6px;
      border: 1px solid var(--border);
      border-radius: 12px;
      background: #faf8f6;
      color: var(--dark);
      font-size: 1rem;
    }

    /* 🎯 focus input */
    .login-form input:focus {
      border-color: var(--brown);
      box-shadow: 0 0 0 4px var(--accent-soft);
      outline: none;
    }

    /* 🔘 bouton login */
    .login-form .btn {
      width: 100%;
      margin-top: 8px;
      padding: 14px;
      font-size: 1rem;
      justify-content: center;
    }

    /* =========================
       ❌ MESSAGE ERREUR
    ========================== */
    .login-error {
      background: rgba(192, 57, 43, 0.12);
      border: 1px solid rgba(192, 57, 43, 0.3);
      color: var(--error);
      padding: 12px 16px;
      border-radius: 12px;
      margin-bottom: 20px;
      font-weight: 500;
    }

  </style>
</head>

<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../back.php">&larr; Back Dashboard</a>


  <!-- =========================
       LOGIN PAGE CENTER
  ========================== -->
  <div class="login-wrapper">

    <!-- 📦 card login -->
    <div class="login-card">

      <!-- =========================
           BRAND SECTION
      ========================== -->
      <div class="brand">

        <!-- 🔤 logo -->
        <div class="brand-icon">VA</div>

        <!-- 📛 titre -->
        <div class="brand-title">Voyagio</div>

        <!-- 📌 sous-titre -->
        <div class="brand-subtitle">Back Office Login</div>

      </div>

      <!-- ❌ affichage erreur si login faux -->
      <?php if(isset($error)): ?>
        <div class="login-error"><?= $error ?></div>
      <?php endif; ?>

      <!-- =========================
           FORM LOGIN
      ========================== -->
      <form class="login-form" method="POST">

        <!-- 👤 username -->
        <label>
          Username
          <input type="text" name="username" placeholder="Enter username" required>
        </label>

        <!-- 🔑 password -->
        <label>
          Password
          <input type="password" name="password" placeholder="Enter password" required>
        </label>

        <!-- 🔘 submit -->
        <button class="btn btn-primary" type="submit" name="login">
          Login
        </button>

      </form>

    </div>
  </div>

</body>
</html>
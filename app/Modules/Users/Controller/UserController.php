<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Client.php';
require_once __DIR__ . '/../Model/Admin.php';

class UserController
{
    private function ensureUsersSchema()
    {
        global $pdo;

        $columns = [
            'client' => [
                'recovery_key' => "ALTER TABLE client ADD COLUMN recovery_key VARCHAR(20) DEFAULT NULL AFTER password",
                'profile_photo' => "ALTER TABLE client ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL AFTER recovery_key",
                'status' => "ALTER TABLE client ADD COLUMN status VARCHAR(30) NOT NULL DEFAULT 'active' AFTER profile_photo",
            ],
            'admin' => [
                'profile_photo' => "ALTER TABLE admin ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL AFTER role",
            ],
        ];

        foreach ($columns as $table => $tableColumns) {
            foreach ($tableColumns as $column => $alterSql) {
                $check = $pdo->prepare(
                    "SELECT COUNT(*)
                     FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE()
                       AND TABLE_NAME = :table_name
                       AND COLUMN_NAME = :column_name"
                );
                $check->execute([
                    'table_name' => $table,
                    'column_name' => $column,
                ]);

                if ((int) $check->fetchColumn() === 0) {
                    $pdo->exec($alterSql);
                }
            }
        }
    }

    public function addClient($client, $photo_base64 = null)
    {
        global $pdo;
        try {
            $this->ensureUsersSchema();

            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $key = substr(str_shuffle($chars), 0, 3) . '-' . substr(str_shuffle($chars), 0, 3) . '-' . substr(str_shuffle($chars), 0, 3);

            // --- GESTION PHOTO WEBRTC ---
            $photoPath = null;
            if ($photo_base64 && strpos($photo_base64, 'data:image') === 0) {
                // Nettoyer l'en-tête (ex: "data:image/png;base64,")
                list($type, $data) = explode(';', $photo_base64);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                // Forcer le répertoire de destination
                $uploadDir = __DIR__ . '/../uploads/profiles/';
                $filename = 'profile_' . time() . '_' . uniqid() . '.png';

                if (file_put_contents($uploadDir . $filename, $data)) {
                    $photoPath = 'uploads/profiles/' . $filename;
                }
            }

            $query = $pdo->prepare(
                'INSERT INTO client (fullname, email, birthdate, tel, sexe, password, recovery_key, profile_photo) 
                 VALUES (:fullname, :email, :birthdate, :tel, :sexe, :password, :recovery_key, :profile_photo)'
            );
            $query->execute([
                'fullname' => $client->getFullname(),
                'email' => $client->getEmail(),
                'birthdate' => $client->getBirthdate(),
                'tel' => $client->getTel(),
                'sexe' => $client->getSexe(),
                'password' => $client->getPassword(),
                'recovery_key' => $key,
                'profile_photo' => $photoPath
            ]);

            return 'success_client|' . $key;
        } catch (PDOException $e) {
            echo "Erreur SQL : " . $e->getMessage();
            return false;
        }
    }

    public function addAdmin($admin_obj, $photo_base64 = null)
    {
        global $pdo;
        try {
            $this->ensureUsersSchema();

            // --- GESTION PHOTO WEBRTC POUR ADMIN ---
            $photoPath = null;
            if ($photo_base64 && strpos($photo_base64, 'data:image') === 0) {
                list($type, $data) = explode(';', $photo_base64);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                $uploadDir = __DIR__ . '/../uploads/profiles/';
                $filename = 'admin_' . time() . '_' . uniqid() . '.png';

                if (file_put_contents($uploadDir . $filename, $data)) {
                    $photoPath = 'uploads/profiles/' . $filename;
                }
            }

            $query = $pdo->prepare(
                'INSERT INTO admin (fullname, email, password, role, profile_photo) 
                 VALUES (:fullname, :email, :password, :role, :profile_photo)'
            );
            $query->execute([
                'fullname' => $admin_obj->getFullname(),
                'email' => $admin_obj->getEmail(),
                'password' => $admin_obj->getPassword(),
                'role' => $admin_obj->getRole(),
                'profile_photo' => $photoPath
            ]);

            return 'success_admin_created';
        } catch (PDOException $e) {
            echo "Erreur SQL Admin : " . $e->getMessage();
            return false;
        }
    }

    public function getAllClients()
    {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT * FROM client ORDER BY id DESC');
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getClientById($id)
    {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT * FROM client WHERE id = :id');
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateClient($id, $fullname, $email, $birthdate, $tel, $sexe)
    {
        global $pdo;
        try {
            $query = $pdo->prepare(
                'UPDATE client SET fullname = :fullname, email = :email, birthdate = :birthdate, tel = :tel, sexe = :sexe WHERE id = :id'
            );
            $query->execute([
                'fullname' => $fullname,
                'email' => $email,
                'birthdate' => $birthdate,
                'tel' => $tel,
                'sexe' => $sexe,
                'id' => $id
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteClient($id)
    {
        global $pdo;
        try {
            $query = $pdo->prepare('DELETE FROM client WHERE id = :id');
            $query->execute(['id' => $id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function toggleClientStatus($id, $status)
    {
        global $pdo;
        try {
            $query = $pdo->prepare('UPDATE client SET status = :status WHERE id = :id');
            $query->execute(['status' => $status, 'id' => $id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function loginClient($email, $password)
    {
        global $pdo;
        try {
            $queryClient = $pdo->prepare('SELECT * FROM client WHERE email = :email AND password = :password');
            $queryClient->execute(['email' => $email, 'password' => $password]);
            $client = $queryClient->fetch();
            
            if ($client) {
                if (isset($client['status']) && $client['status'] === 'blocked') {
                    return 'error_blocked';
                }
                $_SESSION['client_email'] = $email; // Démarrage de session client
                return 'success_client';
            }
            return 'Mot de passe ou adresse e-mail incorect.';
        } catch (PDOException $e) {
            return 'Erreur DB: ' . $e->getMessage();
        }
    }

    public function loginAdmin($id, $email, $password)
    {
        global $pdo;
        try {
            $queryAdmin = $pdo->prepare('SELECT * FROM admin WHERE id = :id AND email = :email AND password = :password');
            $queryAdmin->execute([
                'id' => trim($id),
                'email' => trim($email),
                'password' => $password
            ]);

            $admin = $queryAdmin->fetch();
            if ($admin) {
                // Démarre la session au niveau admin
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_name'] = $admin['fullname'];
                return 'success_admin';
            }
            // En cas d'erreur, on peut renvoyer un message plus précis si les données sont vides
            if (empty($id) || empty($email) || empty($password)) {
                return "Veuillez remplir tous les champs.";
            }
            return "Identifiants Administrateur incorrects (ID: $id, Email: $email). Vérifiez votre base de données.";
        } catch (PDOException $e) {
            return 'Erreur DB: ' . $e->getMessage();
        }
    }

    public function getAdminById($id)
    {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT * FROM admin WHERE id = :id');
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllAdmins()
    {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT * FROM admin ORDER BY id DESC');
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function forgotPassword($fullname, $email)
    {
        global $pdo;
        try {
            // Chercher d'abord dans client
            $query = $pdo->prepare('SELECT password FROM client WHERE TRIM(fullname) = :fullname AND email = :email');
            $query->execute([
                'fullname' => trim($fullname),
                'email' => trim($email)
            ]);
            $user = $query->fetch();

            // Si non trouvé, on cherche dans admin
            if (!$user) {
                $queryAdmin = $pdo->prepare('SELECT password FROM admin WHERE TRIM(fullname) = :fullname AND email = :email');
                $queryAdmin->execute([
                    'fullname' => trim($fullname),
                    'email' => trim($email)
                ]);
                $user = $queryAdmin->fetch();
            }

            if ($user) {
                $password = $user['password'];
                $subject = "Récupération de votre mot de passe";
                $message = "Bonjour " . $fullname . ",\n\nSuite à votre demande, voici votre mot de passe : " . $password . "\n\nCordialement,\nL'équipe Projet Écologique";
                $headers = "From: noreply@projet-ecologique.com\r\n";
                $headers .= "Reply-To: noreply@projet-ecologique.com\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                // Envoi de l'email (le @ supprime les warnings disgracieux si le serveur mail n'est pas configuré)
                if (@mail($email, $subject, $message, $headers)) {
                    return 'success_forgot_password';
                } else {
                    return "Le serveur mail n'est pas configuré. \n\n[Mode Développement] Votre mot de passe est : " . $password;
                }
            } else {
                return "Aucun compte ne correspond à ces informations.";
            }

        } catch (PDOException $e) {
            return 'Erreur DB: ' . $e->getMessage();
        }
    }

    public function forgotPasswordClient($email, $recovery_key)
    {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT password FROM client WHERE email = :email AND recovery_key = :recovery_key');
            $query->execute([
                'email' => trim($email),
                'recovery_key' => trim($recovery_key)
            ]);
            $user = $query->fetch();

            if ($user) {
                return 'success_forgot_client|' . $user['password'];
            } else {
                return "Clé de récupération invalide ou adresse email incorrecte.";
            }

        } catch (PDOException $e) {
            return 'Erreur DB: ' . $e->getMessage();
        }
    }
    public function forgotPasswordClientFaceID($email, $faceid_token)
    {
        global $pdo;
        try {
            if ($faceid_token !== "AUTHORIZED_FACE_" . $email) {
                return "Jeton de sécurité invalide.";
            }

            $query = $pdo->prepare('SELECT password FROM client WHERE email = :email');
            $query->execute(['email' => $email]);
            $client = $query->fetch();

            if ($client) {
                return 'success_forgot_client|' . $client['password'];
            } else {
                return 'Email introuvable.';
            }
        } catch (PDOException $e) {
            return "Erreur serveur.";
        }
    }

    public function getPhotoByEmail($email)
    {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT profile_photo FROM client WHERE email = :email LIMIT 1');
            $query->execute(['email' => $email]);
            $client = $query->fetch();

            if ($client && !empty($client['profile_photo'])) {
                return $client['profile_photo'];
            } else {
                return 'erreur_no_photo';
            }
        } catch (PDOException $e) {
            return 'erreur_sql';
        }
    }

    public function getClientByEmail($email)
    {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT * FROM client WHERE email = :email LIMIT 1');
            $query->execute(['email' => $email]);
            return $query->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function editClientProfile($email, $fullname, $birthdate, $tel, $sexe, $password, $photo_base64 = null)
    {
        global $pdo;
        try {
            // --- GESTION PHOTO WEBRTC ---
            $photoPath = null;
            if ($photo_base64 && strpos($photo_base64, 'data:image') === 0) {
                list($type, $data) = explode(';', $photo_base64);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                $uploadDir = __DIR__ . '/../uploads/profiles/';
                $filename = 'profile_' . time() . '_' . uniqid() . '.png';

                if (file_put_contents($uploadDir . $filename, $data)) {
                    $photoPath = 'uploads/profiles/' . $filename;
                }
            }

            if (!empty($password)) {
                $query = $pdo->prepare('UPDATE client SET fullname = :fullname, birthdate = :birthdate, tel = :tel, sexe = :sexe, password = :password WHERE email = :email');
                $query->execute([
                    'fullname' => $fullname,
                    'birthdate' => $birthdate,
                    'tel' => $tel,
                    'sexe' => $sexe,
                    'password' => $password,
                    'email' => $email
                ]);
            } else {
                $query = $pdo->prepare('UPDATE client SET fullname = :fullname, birthdate = :birthdate, tel = :tel, sexe = :sexe WHERE email = :email');
                $query->execute([
                    'fullname' => $fullname,
                    'birthdate' => $birthdate,
                    'tel' => $tel,
                    'sexe' => $sexe,
                    'email' => $email
                ]);
            }

            // Si une nouvelle photo locale a été prise, on la met à jour
            if ($photoPath !== null) {
                $updatePhoto = $pdo->prepare('UPDATE client SET profile_photo = :photo WHERE email = :email');
                $updatePhoto->execute(['photo' => $photoPath, 'email' => $email]);
            }

            return 'success_edit_profile';
        } catch (PDOException $e) {
            return 'erreur_sql: ' . $e->getMessage();
        }
    }

    public function loginGoogle()
    {
        $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online'
        ]);
        header('Location: ' . $url);
        exit();
    }

    public function googleCallback($code)
    {
        global $pdo;

        // 1. Échanger le code contre un token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code',
            'code' => $code
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);

        if (isset($tokenData['access_token'])) {
            $accessToken = $tokenData['access_token'];

            // 2. Récupérer les infos utilisateur
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v2/userinfo');
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $userInfoResponse = curl_exec($ch);
            curl_close($ch);

            $userInfo = json_decode($userInfoResponse, true);

            if (isset($userInfo['email'])) {
                $email = $userInfo['email'];
                $fullname = $userInfo['name'];
                $photo = $userInfo['picture'] ?? null;

                try {
                    // Vérifier si le client existe
                    $query = $pdo->prepare('SELECT * FROM client WHERE email = :email');
                    $query->execute(['email' => $email]);
                    $client = $query->fetch();

                    if (!$client) {
                        // Créer le client avec des valeurs par défaut
                        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                        $key = substr(str_shuffle($chars), 0, 3) . '-' . substr(str_shuffle($chars), 0, 3) . '-' . substr(str_shuffle($chars), 0, 3);
                        $defaultPassword = substr(str_shuffle($chars), 0, 8); // Mot de passe aléatoire généré

                        $insert = $pdo->prepare('INSERT INTO client (fullname, email, birthdate, tel, sexe, password, recovery_key, profile_photo) VALUES (:fullname, :email, :birthdate, :tel, :sexe, :password, :recovery_key, :profile_photo)');
                        $insert->execute([
                            'fullname' => $fullname,
                            'email' => $email,
                            'birthdate' => '2000-01-01', // Valeur par défaut
                            'tel' => '00000000',         // Valeur par défaut
                            'sexe' => 'Inconnu',         // Valeur par défaut
                            'password' => $defaultPassword,
                            'recovery_key' => $key,
                            'profile_photo' => $photo
                        ]);
                    } else if (!empty($photo) && (empty($client['profile_photo']) || $client['profile_photo'] === 'erreur_no_photo')) {
                        // Mettre à jour la photo si elle est manquante dans la DB mais fournie par Google
                        $update = $pdo->prepare('UPDATE client SET profile_photo = :photo WHERE email = :email');
                        $update->execute(['photo' => $photo, 'email' => $email]);
                    }

                    // Créer la session pour le client
                    $_SESSION['client_email'] = $email;

                    // Redirection vers le dashboard client integre
                    header('Location: ../../../../dashboard.php');
                    exit();
                } catch (PDOException $e) {
                    echo "Erreur BDD : " . $e->getMessage();
                }
            } else {
                echo "Erreur lors de la récupération des informations Google.";
            }
        } else {
            echo "Erreur d'authentification Google (Token invalide ou expiré).";
        }
    }

}

// Routeur basique
$userController = new UserController();
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'register') {
        if (isset($_POST['fullname'], $_POST['email'], $_POST['birthdate'], $_POST['tel'], $_POST['sexe'], $_POST['password'])) {
            $photo_base64 = isset($_POST['photo_base64']) ? $_POST['photo_base64'] : null;
            $client = new Client(
                null,
                $_POST['fullname'],
                $_POST['email'],
                $_POST['birthdate'],
                $_POST['tel'],
                $_POST['sexe'],
                $_POST['password']
            );
            $result = $userController->addClient($client, $photo_base64);
            if (strpos($result, 'success_client') !== false) {
                echo $result;
            } else {
                echo "Erreur lors de l'inscription.";
            }
        }
    } else if ($_GET['action'] == 'confirmRecovery') {
        if (isset($_GET['email'], $_GET['token'])) {
            $userController->confirmRecovery($_GET['email'], $_GET['token']);
        } else {
            echo "Erreur : Paramètres manquants.";
        }
    } else if ($_GET['action'] == 'forgotPasswordClient') {
        if (isset($_POST['email'], $_POST['recovery_key'])) {
            $result = $userController->forgotPasswordClient($_POST['email'], $_POST['recovery_key']);
            echo $result;
        }
    } else if ($_GET['action'] == 'getPhotoByEmail') {
        if (isset($_POST['email'])) {
            echo $userController->getPhotoByEmail($_POST['email']);
        }
    } else if ($_GET['action'] == 'recoverPasswordFaceID') {
        if (isset($_POST['email'], $_POST['faceid_token'])) {
            echo $userController->forgotPasswordClientFaceID($_POST['email'], $_POST['faceid_token']);
        }
    } else if ($_GET['action'] == 'registerAdmin') {
        if (isset($_POST['fullname'], $_POST['email'], $_POST['password'])) {
            $photo_base64 = isset($_POST['photo_base64']) ? $_POST['photo_base64'] : null;
            $newAdmin = new Admin(
                null,
                $_POST['fullname'],
                $_POST['email'],
                $_POST['password'],
                'admin'
            );
            $result = $userController->addAdmin($newAdmin, $photo_base64);
            if ($result === 'success_admin_created') {
                echo $result;
            } else {
                echo "Erreur lors de la création de l'administrateur.";
            }
        } else {
            echo "Champs manquants.";
        }
    } else if ($_GET['action'] == 'loginClient') {
        if (isset($_POST['email'], $_POST['password'])) {
            $result = $userController->loginClient($_POST['email'], $_POST['password']);
            echo $result;
        }
    } else if ($_GET['action'] == 'loginAdmin') {
        if (isset($_POST['id'], $_POST['email'], $_POST['password'])) {
            $result = $userController->loginAdmin($_POST['id'], $_POST['email'], $_POST['password']);
            echo $result;
        }
    } else if ($_GET['action'] == 'editUser') {
        if (isset($_POST['id'], $_POST['fullname'], $_POST['email'], $_POST['birthdate'], $_POST['tel'], $_POST['sexe'])) {
            $userController->updateClient(
                $_POST['id'],
                $_POST['fullname'],
                $_POST['email'],
                $_POST['birthdate'],
                $_POST['tel'],
                $_POST['sexe']
            );
            // Redirection vers la liste des utilisateurs après modification
            header('Location: ../view/admin/users.php');
            exit();
        }
    } else if ($_GET['action'] == 'deleteUser') {
        if (isset($_GET['id'])) {
            $userController->deleteClient($_GET['id']);
            header('Location: ../view/admin/users.php');
            exit();
        }
    } else if ($_GET['action'] == 'toggleStatus') {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $userController->toggleClientStatus($_GET['id'], $_GET['status']);
            header('Location: ../view/admin/users.php');
            exit();
        }
    } else if ($_GET['action'] == 'forgotPassword') {
        if (isset($_POST['fullname'], $_POST['email'])) {
            $result = $userController->forgotPassword($_POST['fullname'], $_POST['email']);
            echo $result;
        }
    } else if ($_GET['action'] == 'loginGoogle') {
        $userController->loginGoogle();
    } else if ($_GET['action'] == 'googleCallback') {
        if (isset($_GET['code'])) {
            $userController->googleCallback($_GET['code']);
        } else {
            echo "Erreur : Code Google manquant.";
        }
    } else if ($_GET['action'] == 'editProfile') {
        if (isset($_SESSION['client_email']) && isset($_POST['fullname'], $_POST['birthdate'], $_POST['tel'], $_POST['sexe'])) {
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $photo_base64 = isset($_POST['photo_base64']) ? $_POST['photo_base64'] : null;
            $result = $userController->editClientProfile($_SESSION['client_email'], $_POST['fullname'], $_POST['birthdate'], $_POST['tel'], $_POST['sexe'], $password, $photo_base64);
            echo $result;
        } else {
            echo "Erreur d'accès ou paramètres manquants.";
        }
    } else if ($_GET['action'] == 'logoutClient') {
        unset($_SESSION['client_email']);
        header('Location: ../view/login.html');
        exit();
    } else if ($_GET['action'] == 'changeLanguage') {
        if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en', 'ar'])) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['lang'] = $_GET['lang'];
        }
        // Rediriger vers la page précédente ou vers le login par défaut
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../view/login.html';
        header('Location: ' . $referer);
        exit();
    }
}
?>

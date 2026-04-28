<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Client.php';
require_once __DIR__ . '/../Model/Admin.php';

class UserController {
    public function addClient($client, $photo_base64 = null) {
        global $pdo;
        try {
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

    public function addAdmin($admin_obj, $photo_base64 = null) {
        global $pdo;
        try {
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

    public function getAllClients() {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT * FROM client ORDER BY id DESC');
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getClientById($id) {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT * FROM client WHERE id = :id');
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateClient($id, $fullname, $email, $birthdate, $tel, $sexe) {
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

    public function deleteClient($id) {
        global $pdo;
        try {
            $query = $pdo->prepare('DELETE FROM client WHERE id = :id');
            $query->execute(['id' => $id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function loginClient($email, $password) {
        global $pdo;
        try {
            $queryClient = $pdo->prepare('SELECT * FROM client WHERE email = :email AND password = :password');
            $queryClient->execute(['email' => $email, 'password' => $password]);
            if ($queryClient->rowCount() > 0) {
                return 'success_client';
            }
            return 'Mot de passe ou adresse e-mail incorect.';
        } catch (PDOException $e) {
            return 'Erreur DB: ' . $e->getMessage();
        }
    }

    public function loginAdmin($id, $email, $password) {
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
            if(empty($id) || empty($email) || empty($password)) {
                return "Veuillez remplir tous les champs.";
            }
            return "Identifiants Administrateur incorrects (ID: $id, Email: $email). Vérifiez votre base de données.";
        } catch (PDOException $e) {
            return 'Erreur DB: ' . $e->getMessage();
        }
    }

    public function getAdminById($id) {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT * FROM admin WHERE id = :id');
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllAdmins() {
        global $pdo;
        try {
            $query = $pdo->prepare('SELECT * FROM admin ORDER BY id DESC');
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function forgotPassword($fullname, $email) {
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

    public function forgotPasswordClient($email, $recovery_key) {
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
    public function forgotPasswordClientFaceID($email, $faceid_token) {
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

    public function getPhotoByEmail($email) {
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
            // Redirection vers la liste des utilisateurs après suppression
            header('Location: ../view/admin/users.php');
            exit();
        }
    } else if ($_GET['action'] == 'forgotPassword') {
        if (isset($_POST['fullname'], $_POST['email'])) {
            $result = $userController->forgotPassword($_POST['fullname'], $_POST['email']);
            echo $result;
        }
    }
}
?>

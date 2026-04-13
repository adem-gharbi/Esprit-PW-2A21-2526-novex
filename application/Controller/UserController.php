<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Client.php';

class UserController {
    public function addClient($client) {
        global $pdo;
        $db = $pdo;
        try {
            $query = $db->prepare(
                'INSERT INTO client (fullname, email, birthdate, tel, sexe, password) 
                 VALUES (:fullname, :email, :birthdate, :tel, :sexe, :password)'
            );
            $query->execute([
                'fullname' => $client->getFullname(),
                'email' => $client->getEmail(),
                'birthdate' => $client->getBirthdate(),
                'tel' => $client->getTel(),
                'sexe' => $client->getSexe(),
                // Idéalement on utilise password_hash, mais on garde le code simple pour commencer
                'password' => $client->getPassword()
            ]);
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
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
}

// Routeur basique
$userController = new UserController();
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'register') {
        if (isset($_POST['fullname'], $_POST['email'], $_POST['birthdate'], $_POST['tel'], $_POST['sexe'], $_POST['password'])) {
            $client = new Client(
                null,
                $_POST['fullname'],
                $_POST['email'],
                $_POST['birthdate'],
                $_POST['tel'],
                $_POST['sexe'],
                $_POST['password']
            );
            if ($userController->addClient($client)) {
                // On peut envoyer une réponse (pour AJAX ou redirection)
                echo "success";
            }
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
    }
}
?>

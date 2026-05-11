<?php
require_once __DIR__ . '/config.php';
try {
    $id = '1';
    $email = 'admin@adem.com';
    $password = 'adminadmin';
    echo "Testing direct login...\n";
    $queryAdmin = $pdo->prepare('SELECT * FROM admin WHERE id = :id AND email = :email AND password = :password');
    // Also log the PDO error info if it fails
    if (!$queryAdmin->execute([
        'id' => $id, 
        'email' => $email, 
        'password' => $password
    ])) {
        print_r($queryAdmin->errorInfo());
    }

    if ($queryAdmin->rowCount() > 0) {
        echo "SUCCESS! PDO row count > 0\n";
    } else {
        echo "FAIL! Row Count is 0\n";
        
        // Debug exactly what is in DB
        $stmt = $pdo->query("SELECT id, email, password, LENGTH(password) as pwd_len, LENGTH(id) as id_len, LENGTH(email) as em_len FROM admin");
        echo "Values in DB:\n";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch(Exception $e) {
    echo "Exception: " . $e->getMessage();
}

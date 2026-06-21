<?php
/**
 * ADEPTIO – Shared PDO connection.
 *
 * Single source of truth for database credentials, used by both the public
 * /api endpoints and the /admin panel (admin/config/database.php is a thin
 * shim that simply requires this file). Same style as the original admin
 * config; charset bumped to utf8mb4 so multibyte text inserts correctly and
 * matches the tables' utf8mb4_general_ci collation.
 */

$host     = "localhost";
$dbname   = "adeptio_db";
$user     = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    die("Erreur connexion : " . $e->getMessage());

}

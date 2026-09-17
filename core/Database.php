<?php
// PDO singleton connection, reads credentials from config/config.php
class Database {

    private static ?PDO $pdo = null;


    public static function getConnection(): PDO
    {
    if (self::$pdo == null) {
        require __DIR__.'/../config/config.php';
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        self::$pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    return self::$pdo;

    }

    

}


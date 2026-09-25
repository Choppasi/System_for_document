<?php

class User
{
    public static function all( string $search = '', int $page = 1, int $perPage = 5): array
    {
        $pdo = Database::getConnection();
        $where = '';
        $params = [];
        if ($search !== ''){
            $where = 'WHERE fio LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM users 
                $where
                ORDER BY created_at DESC
                LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function count(string $search = ''): int
    {
        $pdo = Database::getConnection();
        $where = '';
        $params = [];
        if ($search !== ''){
            $where = 'WHERE fio LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql = "SELECT COUNT(*) FROM users $where";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();

    }


    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute((['id' => $id]));

        return $stmt->fetch() ?:null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        
        $sql = 'INSERT INTO users (fio, city, phone, email, login, password) VALUES (:fio, :city, :phone, :email, :login, :password)';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::getConnection();

        $sql = 'UPDATE users 
                SET fio = :fio, city = :city, phone = :phone, email = :email, login = :login, password = :password
                WHERE id = :id';
        
        $stmt = $pdo->prepare($sql);

        $data['id'] = $id;
        $stmt->execute($data);
    }


    public static function delete(int $id): void
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function emailExists(string $email, int $excludeID = 0): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email AND id != :excludeID');
        $stmt->execute(['email' => $email, 'excludeID' => $excludeID]);

        return $stmt->fetchColumn() > 0;
    }


    public static function loginExists(string $login, int $excludeID = 0): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE login = :login AND id != :excludeID');
        $stmt->execute(['login' => $login, 'excludeID' => $excludeID]);

        return $stmt->fetchColumn() > 0;
    }

    public static function allForSelect(): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT id, fio FROM users ORDER BY fio');
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
}
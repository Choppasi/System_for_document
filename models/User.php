<?php

class User
{
    public function __construct(private PDO $pdo) {}

    public function all( string $search = '', int $page = 1, int $perPage = 5): array
    {
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int
    {
        $where = '';
        $params = [];
        if ($search !== ''){
            $where = 'WHERE fio LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql = "SELECT COUNT(*) FROM users $where";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();

    }

    public function findID(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute((['id' => $id]));

        return $stmt->fetch() ?:null;
    }

    public function findLogin(string $login): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE login = :login');
        $stmt->execute(['login' => $login]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (fio, city, phone, email, login, password) VALUES (:fio, :city, :phone, :email, :login, :password)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE users 
                SET fio = :fio, city = :city, phone = :phone, email = :email, login = :login, password = :password
                WHERE id = :id';
        
        $stmt = $this->pdo->prepare($sql);

        $data['id'] = $id;
        $stmt->execute($data);
    }


    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function emailExists(string $email, int $excludeID = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email AND id != :excludeID');
        $stmt->execute(['email' => $email, 'excludeID' => $excludeID]);

        return $stmt->fetchColumn() > 0;
    }


    public function loginExists(string $login, int $excludeID = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE login = :login AND id != :excludeID');
        $stmt->execute(['login' => $login, 'excludeID' => $excludeID]);

        return $stmt->fetchColumn() > 0;
    }

    public function allForSelect(): array
    {
        $stmt = $this->pdo->prepare('SELECT id, fio FROM users ORDER BY fio');
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
}
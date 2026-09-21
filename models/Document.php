<?php

class Document 
{
    public static function all(string $search = '', int $page = 1, int $perPage = 5, int $userID = 0): array
    {
        $pdo = Database::getConnection();

        $where = [];
        $params = [];

        if ($search !== ''){
            $where[] = 'd.name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        if ($userID > 0){
            $where[] = 'd.user_id = :user_id';
            $params['user_id'] = $userID;
        }

        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT d.*, u.fio AS user_fio FROM documents d
                JOIN users u ON u.id = d.user_id
                $whereSql
                LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        usort($rows, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        return $rows;
        
    }

    public static function count(string $search = '', int $userID = 0): int{

        $pdo = Database::getConnection();

        $where = [];
        $params = [];

        if ($search !== ''){
            $where[] = 'd.name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        if ($userID > 0){
            $where[] = 'd.user_id = :user_id';
            $params['user_id'] = $userID;
        }

        $wheresql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT COUNT(*) FROM documents d $wheresql";


        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();

    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT * FROM documents WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int{

        $pdo = Database::getConnection();

        $sql = 'INSERT INTO documents (user_id, name, description, doc_type) VALUES (:user_id, :name, :description, :doc_type)';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return (int)$pdo->lastInsertId();


    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::getConnection();

        $sql = 'UPDATE documents 
                SET user_id = :user_id, name = :name, description = :description, doc_type = :doc_type
                WHERE id = :id';
        
        $stmt = $pdo->prepare($sql);

        $data['id'] = $id;

        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('DELETE FROM documents WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
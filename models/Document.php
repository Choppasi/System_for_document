<?php

class Document
{
    public function __construct(private PDO $pdo) {}

    public function all(string $search = '', int $page = 1, int $perPage = 5, int $userID = 0): array
    {
        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = 'd.name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        if ($userID > 0) {
            $where[] = 'd.user_id = :user_id';
            $params['user_id'] = $userID;
        }

        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT d.*, u.fio AS user_fio FROM documents d
                JOIN users u ON u.id = d.user_id
                $whereSql
                LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        usort($rows, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        return $rows;
    }

    public function count(string $search = '', int $userID = 0): int
    {
        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = 'd.name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        if ($userID > 0) {
            $where[] = 'd.user_id = :user_id';
            $params['user_id'] = $userID;
        }

        $wheresql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT COUNT(*) FROM documents d $wheresql";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM documents WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO documents (user_id, name, description, doc_type) VALUES (:user_id, :name, :description, :doc_type)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE documents 
                SET user_id = :user_id, name = :name, description = :description, doc_type = :doc_type
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);

        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM documents WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
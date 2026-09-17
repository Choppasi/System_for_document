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
            $params['user_id'] = '%' . $userID . '%';
        }

        $whereSql = $where === [] ? '' : 'WHERE' . implode(' AND ', $where);

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
}
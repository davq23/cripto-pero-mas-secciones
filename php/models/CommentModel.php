<?php

namespace models;

class CommentModel extends Model {
    public function getAll(int $limit, int $offsetID = 0)
    {
        $limitPlusOne = $limit + 1;
        $db = $this->db->getConnection();

        if ($db instanceof \PDO) {
            $statement = $db->prepare('SELECT id, author, body, datetime FROM cripto_comments WHERE verified = 1 ORDER BY id DESC LIMIT ? OFFSET ?');

            $statement->bindParam(1, $limitPlusOne, \PDO::PARAM_INT);
            $statement->bindParam(2, $offsetID, \PDO::PARAM_INT);

            $ok = $statement->execute();

            if (!$ok) {
                error_log(json_encode($statement->errorInfo()));
                return 'DB error';
            }

            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        }

        return null;
    }


    public function create(array $commentArray) {
        $db = $this->db->getConnection();

        if ($db instanceof \PDO) {
            $statement = $db->prepare('INSERT INTO cripto_comments (author, body, datetime) VALUES (?, ?, ?)');

            $statement->bindParam(1, $commentArray['author'], \PDO::PARAM_STR);
            $statement->bindParam(2, $commentArray['body'], \PDO::PARAM_STR);
            $statement->bindParam(3, date('Y-m-d h:i:s'), \PDO::PARAM_STR);

            $ok = $statement->execute();

            if (!$ok) {
                return 'DB error';
            }
            
            return (int)$db->lastInsertId();
        }

        return null;
    }
}
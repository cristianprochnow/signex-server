<?php

    namespace Signex\Data;

    use PDO;
    use Signex\Lib\Database;
    use Signex\Lib\Str;

    class User {
        public function getByEmail(string $email): array {
            if (empty($email)) {
                return [];
            }

            $statement = Database::getConnection()->prepare(
                "SELECT id, password, name, email
                FROM user
                WHERE email = :email"
            );
            $statement->bindValue('email', $email);
            $statement->execute();

            $results = [];
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }

            return $results;
        }

        /**
         * @param array{
         *     name: string,
         *     email: string,
         *     password: string
         * } $data
         * @return int
         */
        public function add(array $data): int {
            $statement = Database::getConnection()->prepare(
                "INSERT IGNORE INTO user
                SET name=:name, 
                    email=:email, 
                    password=:password"
            );
            $statement->bindValue('name', $data['name']);
            $statement->bindValue('email', $data['email']);
            $statement->bindValue(
                'password', Str::crypt($data['password'])
            );
            $statement->execute();

            return (int) Database::getConnection()->lastInsertId();
        }

        public function buildToken(int $id): string {
            $statement = Database::getConnection()->prepare(
                "SELECT id, created_at
                FROM user
                WHERE id = :id"
            );
            $statement->bindValue('id', $id);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);

            return sprintf(
                "%d-%s",
                $result['id'], $result['created_at']
            );
        }
    }

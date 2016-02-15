<?php
final class UsersDao {

    /** @var PDO */
    private $db = null;


    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * Find all {@link Foc}s by search criteria.
     * @return array array of {@link Foc}s
     */
    public function find(UsersSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $Users = new User();
            UsersMapper::map($Users, $row);
            $result[$Users->getId()] = $Users;
        }
        return $result;
    }


    public function findById($id) {
        $row = $this->query('SELECT * FROM Users WHERE deleted = 0 and id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $Users = new User();
        UsersMapper::map($Users, $row);
        return $Users;
    }

    /**
     * Save {@link Foc}.
     * @param User $Users {@link Foc} to be saved
     * @return User saved {@link Foc} instance
     */
    public function save(User $Users) {
        if ($Users->getId() === null) {
            return $this->insert($Users);
        }
        return $this->update($Users);
    }

    /**
     * Delete {@link Users} by identifier.
     * @param int $id {@link Users} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            UPDATE Users SET
                username = :username,
                password = :password
            WHERE
                id = :id';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, array(
            ':username' => self::formatDateTime(new DateTime()),
            ':password' => true,
            ':id' => $id,
        ));
        return $statement->rowCount() == 1;
    }

    /**
     * @return PDO
     */
    private function getDb() {
        if ($this->db !== null) {
            return $this->db;
        }
        $config = Config::getConfig("db");
        try {
            $this->db = new PDO($config['dsn'], $config['username'], $config['password']);
        } catch (Exception $ex) {
            throw new Exception('DB connection error: ' . $ex->getMessage());
        }
        return $this->db;
    }

    private function getFindSql(UsersSearchCriteria $search = null) {
        $sql = 'SELECT u.username, c.country FROM users u, country c WHERE u.id = c.country_id AND u.id = id';
        return $sql;
    }

    /**
     * @return User
     * @throws Exception
     */
    private function insert(User $Users) {
        $now = new DateTime();
        $Users->setId(null);
        $sql = '
            INSERT INTO Users (id, username, email, password)
                VALUES (:id, :username, :email, :password)';
        return $this->execute($sql, $Users);
    }

    /**
     * @return User
     * @throws Exception
     */
    private function update(User $Users) {
        $Users->setLastModifiedOn(new DateTime());
        $sql = '
            UPDATE Users SET
                username = :username,
                email = :email,
                password = :password,
            WHERE
                id = :id';
        return $this->execute($sql, $Users);
    }

    /**
     * @return User
     * @throws Exception
     */
    private function execute($sql, User $Users) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($Users));
        if (!$Users->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
//        if (!$statement->rowCount()) {
//            throw new NotFoundException('Users with ID "' . $Users->getId() . '" does not exist.');
//        }
        return $Users;
    }

    private function getParams(User $Users) {
        $params = array(
            ':id' => $Users->getId(),
            ':username' => $Users->getUsername(),
            ':email' => $Users->getEmail(),
            ':password' => $Users->getPassword()
        );
        return $params;
    }

    private function executeStatement(PDOStatement $statement, array $params) {
        if (!$statement->execute($params)) {
            self::throwDbError($this->getDb()->errorInfo());
        }
    }

    /**
     * @return PDOStatement
     */
    private function query($sql) {
        $statement = $this->getDb()->query($sql, PDO::FETCH_ASSOC);
        if ($statement === false) {
            self::throwDbError($this->getDb()->errorInfo());
        }
        return $statement;
    }

    private static function throwDbError(array $errorInfo) {
        // log error, send email, etc.
        throw new Exception('DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2]);
    }

    private static function formatDateTime(DateTime $date) {
        return $date->format(DateTime::ISO8601);
    }

}

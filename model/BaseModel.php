<?php

abstract class BaseModel
{
    protected $conn;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct($conn = null)
    {
        $this->conn = $conn ?: getDbConnection();
    }

    /**
     * Find a single record by primary key.
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /**
     * Find all records matching conditions.
     * $conditions: ['column' => value, ...] uses AND + equality.
     * $orderBy: e.g. 'created_at DESC'
     * $limit: max rows
     */
    public function findAll(array $conditions = [], string $orderBy = '', int $limit = 0): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        $params = [];
        $types = '';

        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $col => $val) {
                if (is_null($val)) {
                    $clauses[] = "`{$col}` IS NULL";
                } else {
                    $clauses[] = "`{$col}` = ?";
                    $params[] = $val;
                    $types .= is_int($val) ? 'i' : (is_float($val) ? 'd' : 's');
                }
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        if ($orderBy !== '') {
            $sql .= " ORDER BY {$orderBy}";
        }
        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    /**
     * Find a single record matching conditions.
     */
    public function findOne(array $conditions = []): ?array
    {
        $rows = $this->findAll($conditions, '', 1);
        return $rows[0] ?? null;
    }

    /**
     * Insert a new record. Returns the new auto-increment ID.
     */
    public function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $types = '';
        $values = [];

        foreach ($data as $val) {
            if (is_int($val)) {
                $types .= 'i';
            } elseif (is_float($val)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $val;
        }

        $colStr = '`' . implode('`, `', $columns) . '`';
        $phStr = implode(', ', $placeholders);
        $sql = "INSERT INTO `{$this->table}` ({$colStr}) VALUES ({$phStr})";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    /**
     * Update a record by primary key.
     */
    public function update(int $id, array $data): bool
    {
        $setClauses = [];
        $types = '';
        $values = [];

        foreach ($data as $col => $val) {
            $setClauses[] = "`{$col}` = ?";
            if (is_int($val)) {
                $types .= 'i';
            } elseif (is_float($val)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $val;
        }

        $types .= 'i';
        $values[] = $id;

        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $setClauses) . " WHERE `{$this->primaryKey}` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Soft delete by setting deleted_at.
     */
    public function softDelete(int $id): bool
    {
        $now = dateNow();
        $sql = "UPDATE `{$this->table}` SET `deleted_at` = ? WHERE `{$this->primaryKey}` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $now, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Count rows matching conditions.
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `{$this->table}`";
        $params = [];
        $types = '';

        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $col => $val) {
                if (is_null($val)) {
                    $clauses[] = "`{$col}` IS NULL";
                } else {
                    $clauses[] = "`{$col}` = ?";
                    $params[] = $val;
                    $types .= is_int($val) ? 'i' : (is_float($val) ? 'd' : 's');
                }
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return (int) $row['cnt'];
    }

    /**
     * Execute a raw prepared SELECT query. Returns array of rows.
     */
    protected function query(string $sql, string $types = '', array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);
        if ($types !== '' && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    /**
     * Execute a raw prepared INSERT/UPDATE/DELETE. Returns true on success.
     */
    protected function execute(string $sql, string $types = '', array $params = []): bool
    {
        $stmt = $this->conn->prepare($sql);
        if ($types !== '' && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}

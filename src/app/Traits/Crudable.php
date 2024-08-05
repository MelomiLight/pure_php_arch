<?php

namespace app\Traits;

use Exception;
use PDO;

trait Crudable
{
    /**
     * @throws Exception
     */
    public static function create(array $data)
    {
        $fields = self::$fields;
        $table_name = self::$table_name;

        try {
            $GLOBALS['pdo']->beginTransaction();

            $fieldNames = implode(', ', $fields);
            $placeholders = ':' . implode(', :', $fields);

            $stmt = $GLOBALS['pdo']->prepare("INSERT INTO $table_name ($fieldNames) VALUES ($placeholders)");
            foreach ($fields as $field) {
                if(isset($data[$field])){
                    $stmt->bindValue(":$field", $data[$field]);
                }else{
                    $stmt->bindValue(":$field", null);
                }
            }

            $stmt->execute();
            $stmt->fetch();
            $lastInsertId = $GLOBALS['pdo']->lastInsertId();
            $GLOBALS['pdo']->commit();

            return self::find($lastInsertId);
        } catch (Exception $e) {
            $GLOBALS['pdo']->rollBack();
            throw new Exception("Could not create " . $table_name . $e->getMessage());
        }
    }

    public static function find(int $id, int $type = PDO::FETCH_OBJ)
    {
        $table_name = self::$table_name;

        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM $table_name WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch($type);
    }

    public static function where(string $column_name, $value)
    {
        $table_name = self::$table_name;

        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM $table_name WHERE $column_name = :value");
        $stmt->bindValue(':value', $value);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}

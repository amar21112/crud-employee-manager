<?php

class AbstractModel
{

//    protected static $tableName;
    const DATA_TYPE_BOOL = PDO::PARAM_BOOL;
    const DATA_TYPE_STR = PDO::PARAM_STR;
    const DATA_TYPE_INT = PDO::PARAM_INT;
    const DATA_TYPE_DECIMAL = 4;

    private function prepareValues(PDOStatement &$stmt ){
        foreach (static::$tableSchema as $colName => $type) {
            if($type == 4){
                $sanitizeValue = filter_var($this->$colName , FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $stmt->bindValue(':' . $colName, $sanitizeValue);
            }else
            $stmt->bindValue(':' . $colName, $this->$colName , $type);
        }
    }
    private static function buildNameParameterSQL() :string
    {
        $namedPrams='';
        foreach (static::$tableSchema as $colName => $type) {
            $namedPrams .= $colName . ' = :' . $colName . ', ';
        }

        return trim($namedPrams , ', ');
    }
    private function create(){
        global $pdo_connection;

        $sql = 'INSERT INTO ' . static::$tableName. ' SET '.self::buildNameParameterSQL();
        $stmt = $pdo_connection->prepare($sql);
        $this->prepareValues($stmt);
        return $stmt->execute();
    }

    private function update(){
        global $pdo_connection;

        $sql = 'UPDATE ' . static::$tableName. ' SET '.self::buildNameParameterSQL() . ' WHERE '.static::$primaryKey  .' = '.$this->{static::$primaryKey};

        $stmt = $pdo_connection->prepare($sql);
        $this->prepareValues($stmt);
        return $stmt->execute();
    }

    public function save(){
        return ($this->{static::$primaryKey}  === null) ? $this->create() : $this->update();
    }
    public function delete(){
        global $pdo_connection;

        $sql = 'DELETE FROM '.static::$tableName. ' WHERE ' .static::$primaryKey . ' = '.$this->{static::$primaryKey};

        $stmt = $pdo_connection->prepare($sql);

        return $stmt->execute();
    }

    public static function getAll()
    {
        global $pdo_connection;
        $sql = 'SELECT * FROM ' . static::$tableName;
        $stmt = $pdo_connection->prepare($sql);
        $stmt->execute();
        $result =  $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema));

        return (is_array($result)&& !empty($result) )? $result : false ;
    }

    public static function getByPK($id){
        global $pdo_connection;
        $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE ' .static::$primaryKey . ' = ' .$id;
        $stmt = $pdo_connection->prepare($sql);
        if( $stmt->execute() === true) {
            $obj = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema)) ;
            $obj = array_shift($obj);
        } else
            return false;

        return $obj;
    }

    public static function get($sql , $options = array())
    {
        global $pdo_connection;
        $stmt = $pdo_connection->prepare($sql);
        if(!empty($options)){
            foreach ($options as $colName => $type) {
                if($type[0] == 4){
                    $sanitizeValue = filter_var($type[1] , FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $stmt->bindValue(':' . $colName, $sanitizeValue);
                }else
                    $stmt->bindValue(':' . $colName, $type[1], $type[0]);
            }
        }
        $stmt->execute();
        $result =  $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema));

        return (is_array($result)&& !empty($result) )? $result : false ;
    }
}
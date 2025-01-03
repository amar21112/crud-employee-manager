<?php
class Employee extends AbstractModel{
    private $id;
    private $name;
    private $age;
    private $address;
    private $salary;
    private $tax;
    protected static $tableName = "employees";
    protected static $tableSchema = array(
//        'id' ,  auto incremented
        'name' => self::DATA_TYPE_STR ,
        'age' => self::DATA_TYPE_INT,
        'address' => self::DATA_TYPE_STR ,
        'salary' => self::DATA_TYPE_DECIMAL ,
        'tax'=> self::DATA_TYPE_DECIMAL
    );

    protected static $primaryKey = 'id';
    public function __construct($name , $age , $address,$salary , $tax )
    {

        $this->name = $name;
        $this->tax = $tax;
        $this->address = $address;
        $this->age = $age;
        $this->salary = $salary;

    }

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __set($prop , $val)
    {
        $this->$prop = $val;
    }
    public function calcSalary() : float {
        return $this->salary - ($this->salary * $this->tax/100.00 );
    }

    public function getTableName() :string {
        return self::$tableName;
    }

}
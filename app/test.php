<?php

require_once 'DB.php';
require_once 'AbstractModel.php';
require_once 'employee.php';

$emps = Employee::get('SELECT name , salary FROM employees WHERE age > 30' ,
                            array()
                );
echo '<pre>';
var_dump($emps);
echo '</pre>';

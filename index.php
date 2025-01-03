<?php
  session_start();
    require_once 'app/DB.php';
    require_once 'app/AbstractModel.php';
    require_once 'app/employee.php';

    // insert or update object
  if(isset($_POST['submit'])){
    
    $name = preg_replace("/[^\p{Arabic}\p{Latin} '-]/u", '', $_POST['name']);
    $address = preg_replace("/[^\p{Arabic}\p{Latin} '-]/u", '', $_POST['address']);
    $age = filter_input(INPUT_POST,'age',FILTER_SANITIZE_NUMBER_INT);
    $salary = filter_input(INPUT_POST,'salary',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
    $tax = filter_input(INPUT_POST,'tax',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
  
    // inserting Employees into dat base
    // $sql = 'INSERT INTO employees SET name = "'.$name.'",age = '.$age.'
    //               , address ="'.$address.'",salary = '.$salary.', tax = '.$tax.'';

    if(isset($_GET['action']) && $_GET['action']=='edit' && isset($_GET['id'])){
        $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
        if($id > 0){
            $user = Employee::getByPK($id);
            $user->name = $name;
            $user->address = $address;
            $user->salary = $salary;
            $user->tax = $tax;
            $user->age = $age;
        }
    } else{
        $user = new Employee($name ,$age , $address, $salary, $tax);
    }

    if($user->save() === true){
        $_SESSION['massage'] = 'Employee saved';
        header('location: index.php');
        session_write_close();
        exit;
    }else{
        $error = true;
        $_SESSION['massage'] = 'error saved employee';
    }

  }

//  read object to edit
  if(isset($_GET['action']) && $_GET['action']=='edit' && isset($_GET['id'])){
    $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
    if($id > 0){ 
      $user = Employee::getByPK($id);
    }
  }

  // deletion an employee
  if(isset($_GET['action']) && $_GET['action']=='delete' && isset($_GET['id'])){
      $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
      if($id > 0){
          $user = Employee::getByPK($id);
           if( $user->delete() === true) {
               $_SESSION['massage'] = 'Employee deleted successfully';
               header('location: index.php');
               session_write_close();
               exit;
           }
      }
      else
          $_SESSION['massage'] = 'Error deleting Employee , '.$id;
  }
  // Reading Data From DB


  $result = Employee::getAll();

?>

<html lang="en">
  <head>
    <title>Employee Form</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  </head>
  <body>
    <div class = "wrapper">
      <div class="empForm">
        <form class= "appForm" method="POST" enctype="application/x-www-form-urlencoded">
          <fieldset>
            <legend>Employee Information</legend>

            <?php 
             if(isset( $_SESSION['massage'])) { ?>
             <p class="massage<?=isset($error)?'error' : '' ?>"><?=  $_SESSION['massage']?></p>
              <?php
                  unset($_SESSION['massage']);
                } ?>

              <table>
                <tr>
                  <td>
                    <label for = "name">Employee Name:</label>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input required type="text" name="name" id="name" placeholder="Write the employee name here" maxlength="50" value="<?=isset($user) ? $user->name : ''; ?>">
                  </td>
                </tr>

                <tr>
                  <td>
                    <label for = "age">Employee Age:</label>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input required type="number" name="age" id="age" min="22" max="60"  value="<?=isset($user) ? $user->age : ''; ?>">
                  </td>
                </tr>

                <tr>
                  <td>
                    <label for = "address">Employee Address:</label>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input required type="text" name="address" id="address"placeholder="Write the employee Address here" maxlength="100" value="<?=isset($user) ? $user->address : ''; ?>">
                  </td>
                </tr>
                <tr>
                  <td>
                    <label for = "salary">Employee Salary:</label>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input required type="number" step="0.01" name="salary" id="salary" placeholder="Write the employee salary here" min="1500" max="99999" value="<?=isset($user) ? $user->salary : ''; ?>">
                  </td>
                </tr>
              
                <tr>
                  <td>
                    <label for = "tax">Employee Tax(%):</label>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input required type="number" step="0.01" name="tax" id="tax" placeholder="Write the employee tax here" min="1" max="5" value="<?=isset($user) ? $user->tax : ''; ?>">
                  </td>
                </tr>

                <tr>
                  <td>
                      <input type="submit" name="submit" value= "save">
                  </td>
                </tr>
              </table>
          </fieldset>
        </form>
      </div>
      
      <div class="employees">
        <table>
          <thead>
              <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Address</th>
                <th>Salary</th>
                <th>Tax</th>
                <th>Controler</th>
              </tr>
          </thead>
          <tbody>
             <?php 
                if(false !== $result){
                  foreach($result as $employee){
              ?>
              <tr>
                <td><?=$employee->name?></td>
                <td><?=$employee->age?></td>
                <td><?=$employee->address?></td>
                <td><?=$employee->calcSalary()?> $</td>
                <td><?=$employee->tax?></td>    
                <td>
                  <a href="/PDO/?action=edit&id=<?= $employee->id?>">edit</i></a>
                  <a href="/PDO/?action=delete&id=<?= $employee->id?>" onclick="if(!confirm('Do you want to delete this employee')) return false;">delete</i></a>
                </td>
              </tr>  
              <?php
                  }
                }else {?>
                      <td colspan="5">No Employees Found</td>
               <?php }
             ?>
          </tbody>

        </table>
      </div>
    </div>
      
    
  </body>
</html>
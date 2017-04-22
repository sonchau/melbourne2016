<?php 

  const SQL_DB_NAME       = 'melbou99_mysql';
  const SQL_DB_USERNAME   = 'melbou99_mysql';
  const SQL_DB_PASSWORD   = 'YOUR_PASSWORD';

  /* Include class file */
  //include ("medoo.php");
  require 'medoo.php';

  function createDb(){

  	//create new db connection
    $database = new medoo([
      // required
      'database_type' => 'mysql',
      'database_name' => SQL_DB_NAME,
      'server' => 'localhost',
      'username' => SQL_DB_USERNAME,
      'password' => SQL_DB_PASSWORD,
      'charset' => 'utf8'
     
      // [optional]
      //'port' => 3306,
     
      // [optional] Table prefix
      //'prefix' => 'PREFIX_',
     
      // driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
      //'option' => [
      //  PDO::ATTR_CASE => PDO::CASE_NATURAL
      //]
      ]);


    //sets the timezone for the connection
    $database->query("SET time_zone = '+10:00'")->fetchAll();

    return $database;
  }

  //function to  prettify boolean
  function ToYesNo($b){
    if ($b == 1 || $b || $b == "1" ) {
      return 'Yes';
    }   
    return 'No';
  }
  

?>
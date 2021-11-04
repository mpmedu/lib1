<?php
  require_once('myFunctions.php');
  
  
  // First check if the database and tables exists
  $mc=new mysqli(DB_HOST, DB_USER, DB_PASSWORD) or die(".........");
  $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
  if(!$mc->query($sql)){
    die("Failed creating new database: ".$mc->connect_errno);
  }
  $mc->select_db(DB_NAME);
  
  $create_table = 'CREATE TABLE IF NOT EXISTS admin  (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    pw VARCHAR(100) NOT NULL,
    PRIMARY KEY(id)
  )';
  $create_tbl = $mc->query($create_table);
  if (!$create_tbl) {
    die("Error in creating table");
  }

  $create_table = 'CREATE TABLE IF NOT EXISTS categories  (
    id SMALLINT NOT NULL AUTO_INCREMENT,
    name VARCHAR(40) NOT NULL,
    bflag TINYINT NOT NULL,
    cflag TINYINT NOT NULL,
    pnext SMALLINT NOT NULL,
    psub SMALLINT NOT NULL,
    PRIMARY KEY(id)
  )';
  $create_tbl = $mc->query($create_table);
  if (!$create_tbl) {
    die("Error in creating table");
  }
  
  $sql = 'SELECT id FROM categories';
  $result = mysqli_query($mc,$sql);
  if (mysqli_num_rows($result) === 0) {
    $sql = "INSERT INTO categories VALUES ('',  '', 0, 0, 0, 0)";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    $stmt = $mc->prepare($sql);
    check($stmt, $mc);
    $stmt->execute();
  }

  $create_table = 'CREATE TABLE IF NOT EXISTS pre_businesses  (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bname VARCHAR(40) NOT NULL,
    bdes VARCHAR(200) NOT NULL,
    bcontact VARCHAR(200) NOT NULL,
    blogo VARCHAR(30) NOT NULL,
    bcname VARCHAR(40) NOT NULL,
    bcemail VARCHAR(40) NOT NULL,
    bctel VARCHAR(12) NOT NULL,
    cat_id SMALLINT NOT NULL,
    dt INT NOT NULL,
    mailstatus TINYINT NOT NULL,
    PRIMARY KEY(id)
  )';
  $create_tbl = $mc->query($create_table);
  if (!$create_tbl) {
    die("Error in creating table");
  }
  
  $create_table = 'CREATE TABLE IF NOT EXISTS tbl_businesses  (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bname VARCHAR(40) NOT NULL,
    bdes VARCHAR(200) NOT NULL,
    bcontact VARCHAR(200) NOT NULL,
    blogo VARCHAR(30) NOT NULL,
    bcname VARCHAR(40) NOT NULL,
    bcemail VARCHAR(40) NOT NULL,
    bctel VARCHAR(12) NOT NULL,
    cat_id SMALLINT NOT NULL,
    dt INT NOT NULL,
    hi_id MEDIUMINT UNSIGNED NOT NULL,
    PRIMARY KEY(id)
  )';
  $create_tbl = $mc->query($create_table);
  if (!$create_tbl) {
    die("Error in creating table");
  }
  
  $create_table = 'CREATE TABLE IF NOT EXISTS hilite_b (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cat_id SMALLINT NOT NULL,
    ad VARCHAR(30) NOT NULL,
    tp TINYINT NOT NULL,
    pics_id MEDIUMINT UNSIGNED NOT NULL,
    PRIMARY KEY(id)
  )';
  $create_tbl = $mc->query($create_table);
  if (!$create_tbl) {
    die("Error in creating table");
  }
  
  $create_table = 'CREATE TABLE IF NOT EXISTS pics (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(12) NOT NULL,
    pic VARCHAR(30) NOT NULL,
    nextp MEDIUMINT UNSIGNED NOT NULL,
    PRIMARY KEY(id)
  )';
  $create_tbl = $mc->query($create_table);
  if (!$create_tbl) {
    die("Error in creating table");
  }

  $create_table = 'CREATE TABLE IF NOT EXISTS pre_classifieds  (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bname VARCHAR(40) NOT NULL,
    bdes VARCHAR(300) NOT NULL,
    bcontact VARCHAR(200) NOT NULL,
    blogo VARCHAR(30) NOT NULL,
    bcname VARCHAR(40) NOT NULL,
    bcemail VARCHAR(40) NOT NULL,
    bctel VARCHAR(12) NOT NULL,
    cat_id SMALLINT NOT NULL,
    dt INT NOT NULL,
    mailstatus TINYINT NOT NULL,
    
    PRIMARY KEY(id)
  )';
  $create_tbl = $mc->query($create_table);
  if (!$create_tbl) {
    die("Error in creating table");
  }
  
  $create_table = 'CREATE TABLE IF NOT EXISTS tbl_classifieds  (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bname VARCHAR(40) NOT NULL,
    bdes VARCHAR(300) NOT NULL,
    bcontact VARCHAR(200) NOT NULL,
    blogo VARCHAR(30) NOT NULL,
    bcname VARCHAR(40) NOT NULL,
    bcemail VARCHAR(40) NOT NULL,
    bctel VARCHAR(12) NOT NULL,
    cat_id SMALLINT NOT NULL,
    dt INT NOT NULL,
    hi_id MEDIUMINT UNSIGNED NOT NULL,
    PRIMARY KEY(id)
  )';
  $create_tbl = $mc->query($create_table);
  if (!$create_tbl) {
    die("Error in creating table");
  }
  
  
  // Get the passwords and username from the admin table
  $query = "SELECT id FROM admin";
  $result = mysqli_query($mc,$query);
  $num_rows = mysqli_num_rows($result);  

  if ($num_rows === 0) {
    $_SESSION['masterKey'] = -1;
  } else {
    $lastrownum = $num_rows -1;
    $sql = "SELECT pw FROM admin LIMIT 1";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_result($_SESSION['pw2_hash']);
    $stmt->execute();
    if (!$stmt->fetch()) {
      exit;
    }
    $sql = "SELECT username, pw FROM admin LIMIT $lastrownum,1";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_result($_SESSION['username_hash'], $_SESSION['pw1_hash']);
    $stmt->execute();
    if (!$stmt->fetch()) {
      exit;
    }
    $_SESSION['masterKey'] = 1;
  }
  $mc->close();
  
  
?>

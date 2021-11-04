<?php
  header("content-type:application/json");

  require_once('myFunctions.php');
  
  $todo = $_GET['todo'];
  $result = array('value' => 'success');
  $result['point'] = '';
  
  if (isset($_POST['fname'])) {$fname = $_POST['fname'];} else {die('file defective0');};
  if (isset($_POST['baseDir'])) {$baseDir = $_POST['baseDir'];} else {$baseDir = NULL;};
  if (isset($_POST['id'])) $id = (int)$_POST['id'];
  if (isset($_POST['cat'])) $cat_name = $_POST['cat'];
  if (isset($_POST['isSub'])) $isSub = (bool)$_POST['isSub'];
 
  require_once('myFileClass2.php');
  
    //********** makejscats ***************
  if ('makejscats' === $todo) {
    if (isset($_POST['cname'])) {$cname = $_POST['cname'];} else {die('file defective0.1');};
    $f1 = new myFileClass2($fname,$baseDir,$cname,1,1);
    //$f1->setListName($cname);
    //$f1->setVersion(1,1);
    if ($f1->init() === false) die('file defective1');
    $ss = $f1->makemarrays();
    if ($ss === false) die('cats defective 1');
    $jscode =  "{";
    for ($i = 0; $i < count($ss); $i++) {
      $jscode .=  $ss[$i];
    }
    $jscode .= "}";
    $result['jscode'] = $jscode;
    
  } else {
    $f1 = new myFileClass2($fname,$baseDir);
    if ($f1->init() === false) die('file defective1');
    
    //********** add ***************
    if ('add' === $todo) {
      if ($f1->addCategory($cat_name,$id,$isSub) === false) die('file defective add');
      
      //********** edit ***************
    } else if ('edit' === $todo) {
      if ($f1->editCategory($cat_name,$id) === false) die('file defective edit');
      
      //********** move ***************
    } else if ('move' === $todo) {
      if (isset($_POST['id2move'])) $id2move = (int)$_POST['id2move'];
      if (isset($_POST['pre2move'])) $pre2move = (int)$_POST['pre2move'];
      if (isset($_POST['isSub2move'])) $isSub2move = (bool)$_POST['isSub2move'];
      if ($f1->moveCategory($id,$isSub,$id2move,$pre2move,$isSub2move) === false) die('file defective move');
      
      //********** remove ***************
    } else if ('remove' === $todo) {
      if ($f1->removeCategory($id,$isSub) === false) die('file defective remove');
    }
    // remake the arrays since the categories have changed
    $ss = $f1->makemarrays();
    if ($ss === false) die('cats defective 2');
    $jscode =  "{";
    for ($i = 0; $i < count($ss); $i++) {
      $jscode .=  $ss[$i];
    }
    $jscode .= "}";
    $result['jscode'] = $jscode;
  }
  
  echo json_encode($result); 
  exit;
bad_exit:
  $result['value'] = 'fail';
  if (isset($emsg)) $result['emsg'] = $emsg;
  echo json_encode($result); 
  exit;
  
?>


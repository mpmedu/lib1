<?php
  session_start();
  header("content-type:application/json");

  $todo = $_GET['todo'];
  $result = array('value' => 'success');
  $result['point'] = '';

  switch( $todo) {
  case 'deleteoldtempimages':
    require_once('myFunctions.php');
    $t = $_POST['t'];
    $baseDir = $_POST['baseDir'];
    
    //echo __DIR__;
  // $result['__DIR__'] = __DIR__;
  // $result['$url_base'] = $baseDir;
  // break;
  
    $temppath = $baseDir . '/' . TEMPPATH;
    if ($dh = opendir($temppath)) {
      while (($file = readdir($dh)) !== false) {
        if (is_file($temppath.$file)) {
          $s = substr($file,0,1);
          if ($s === 'b'||$s === 'c') $s = '_'; elseif ($s === 's') $s = '.'; else continue;
          $p = strpos($file,$s);
          if ($p > 0) $tt = (float)substr($file,1,$p-1);
          if ($tt < 100) continue;
          $dt = $t - $tt;
          if ($dt > 600000) {        // 10 minutes
            @unlink($temppath.$file);
          }
        }
      }
    }
    break;
    
  case 'checkpic':
    try {
      require_once('myFunctions.php');
      require_once('myClasses_GD_IM2.php');
      
      if (!is_uploaded_file($_FILES[0]['tmp_name'])) goto be;
      $tname = $_FILES[0]['tmp_name'];
      
      // $bf = $_POST['bf'];
      
      // $baseDir = $_POST['baseDir'];
      
      // $minw = (int)$_POST['minw'];
      // $minh = (int)$_POST['minh'];
      // $maxw = (int)$_POST['maxw'];
      // $maxh = (int)$_POST['maxh'];
      
      $baseDir = $_GET['baseDir'];
      
      $minw = (int)$_GET['minw'];
      $minh = (int)$_GET['minh'];
      $maxw = (int)$_GET['maxw'];
      $maxh = (int)$_GET['maxh'];
      $bf = $_GET['bf'];
      

      if ( (extension_loaded('imagick')) || (class_exists("Imagick") )){ 
        // 'Imagick is installed';
        $image = new IMImage($tname);
      } else {
        // 'Imagick is NOT installed';
        $image = new GDImage($tname); 
      }
        //$image = new GDImage($tname); 
      
      $temppath = $baseDir . '/' . TEMPPATH;
      
      if (!$image->checkImageSize($minw, $minh)) {$emsg = "image too small < $minw x $minh"; goto be;}
      if (!$image->createNew($maxw,$maxh)) goto be;
      if (!$image->saveNew($bf, $temppath)) goto be;
      
      $result['bf'] = $bf;
    } catch (Exception $e) {
        // handle exception
    }  
    break;
    
  case 'wait':
    $wait_time = $_POST['wt'];
    usleep($wait_time);
    break;
    
    
    
  }   // end switch
  
  echo json_encode($result); 
  exit;
be:
  $result['value'] = 'fail';
  if (isset($emsg)) $result['emsg'] = $emsg;
  echo json_encode($result); 
  exit;
  
?>

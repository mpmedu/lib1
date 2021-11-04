<?php
  header("content-type:application/json");

  require_once('myFunctions.php');
  
  // Connect to the database
  $mc =  connectToDB();
  if ($mc->connect_errno) {
    die('Connect Error (' . $mc->connect_errno . ') ' . $mc->connect_error);
  }
  $todo = $_GET['todo'];
  $cat_id = (int)$_POST['cat_id'];
  
  $n = 0;
  
  if ($todo === 'Businesses') {
    $sql="SELECT id, bname,bdes,bcontact,blogo,dt,hi_id FROM tbl_businesses WHERE cat_id = ? 
      ORDER BY bname ASC";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i", $cat_id);
    $stmt->bind_result($id, $bname, $bdes, $bcontact, $blogo,$dt,$hi_id);
    $stmt->execute();
    // $lastmargin = 0;
    while ($stmt->fetch()) {
      $n++;
      $arr['id'][] = $id;
      $arr['bname'][] = '"' . $bname . '"';
      $arr['bdes'][] = '"' . $bdes . '"';
      $arr['bcontact'][] = '"' . $bcontact . '"';
      $arr['blogo'][] = '"' . $blogo . '"';
      $arr['dt'][] = '"' . $dt . '"';
      $arr['hi_id'][] = $hi_id;
    }
    if ($n > 0) {
      $s[] = '"id":[' . implode($arr['id'],',') . '],';
      $s[] = '"bname":[' . implode($arr['bname'],',') . '],';
      $s[] = '"bdes":[' . implode($arr['bdes'],',') . '],';
      $s[] = '"bcontact":[' . implode($arr['bcontact'],',') . '],';
      $s[] = '"blogo":[' . implode($arr['blogo'],',') . '],';
      $s[] = '"dt":[' . implode($arr['dt'],',') . '],';
      $s[] = '"hi_id":[' . implode($arr['hi_id'],',') . ']';
    }
    
    // **************************
  } else if ($todo === 'Classifieds') {  
    // ***************************
    $sql="SELECT id, bname, bdes, bcontact, blogo, dt FROM tbl_classifieds WHERE cat_id = ? ";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i", $cat_id);
    $stmt->bind_result($id, $bname, $bdes, $bcontact, $blogo, $dt);
    $stmt->execute();
    // $lastmargin = 0;
    // $nextmargin = 1;
    while ($stmt->fetch()) {
      $n++;
      $arr['id'][] = $id;
      $arr['bname'][] = '"' . $bname . '"';
      $arr['bdes'][] = '"' . $bdes . '"';
      $arr['bcontact'][] = '"' . $bcontact . '"';
      $arr['blogo'][] = '"' . $blogo . '"';
      $arr['dt'][] = '"' . $dt . '"';
    }
    if ($n > 0) {
      $s[] = '"id":[' . implode($arr['id'],',') . '],';
      $s[] = '"bname":[' . implode($arr['bname'],',') . '],';
      $s[] = '"bdes":[' . implode($arr['bdes'],',') . '],';
      $s[] = '"bcontact":[' . implode($arr['bcontact'],',') . '],';
      $s[] = '"blogo":[' . implode($arr['blogo'],',') . '],';
      $s[] = '"dt":[' . implode($arr['dt'],',') . ']';
    }
    
    // *******************************
  } else if ($todo === 'EditBusinesses') {  
    // *******************************
    $sql="SELECT id, bname,bdes,bcontact,bcname,bcemail,bctel,blogo,cat_id,dt,hi_id FROM tbl_businesses WHERE cat_id = ? 
    ORDER BY bname ASC";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i", $cat_id);
    $stmt->bind_result($id,$bname,$bdes,$bcontact,$bcname,$bcemail,$bctel,$plim,$cat_id,$dt,$hi_id);
    $stmt->execute();
    // $lastmargin = 0;
    while ($stmt->fetch()) {
      $n++;
      $arr['id'][] = $id;
      $arr['bname'][] = '"' . $bname . '"';
      $arr['bdes'][] = '"' . $bdes . '"';
      $arr['bcontact'][] = '"' . $bcontact . '"';
      $arr['bcname'][] = '"' . $bcname . '"';
      $arr['bcemail'][] = '"' . $bcemail . '"';
      $arr['bctel'][] = '"' . $bctel . '"';
      $arr['plim'][] = '"' . $plim . '"';
      $arr['cat_id'][] = $cat_id;
      $arr['dt'][] = '"' . $dt . '"';
      $arr['hi_id'][] = $hi_id;
    }
  if ($n > 0) {
      $s[] = '"id":[' . implode($arr['id'],',') . '],';
      $s[] = '"bname":[' . implode($arr['bname'],',') . '],';
      $s[] = '"bdes":[' . implode($arr['bdes'],',') . '],';
      $s[] = '"bcontact":[' . implode($arr['bcontact'],',') . '],';
      $s[] = '"bcname":[' . implode($arr['bcname'],',') . '],';
      $s[] = '"bcemail":[' . implode($arr['bcemail'],',') . '],';
      $s[] = '"bctel":[' . implode($arr['bctel'],',') . '],';
      $s[] = '"pclim":[' . implode($arr['pclim'],',') . '],';
      $s[] = '"cat_id":[' . implode($arr['cat_id'],',') . '],';
      $s[] = '"dt":[' . implode($arr['dt'],',') . '],';
      $s[] = '"hi_id":[' . implode($arr['hi_id'],',') . ']';
    }
      
    // *********************************
  } else if ($todo === 'EditClassifieds') {  
    // *********************************
   
    $sql="SELECT id, bname,bdes,bcontact,bcname,bcemail,bctel,blogo,cat_id,dt FROM tbl_classifieds WHERE cat_id = ?";
    //ORDER BY bname ASC";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i", $cat_id);
  $stmt->bind_result($id,$bname,$bdes,$bcontact,$bcname,$bcemail,$bctel,$clim,$cat_id,$dt);
    $stmt->execute();
    while ($stmt->fetch()) {
      $n++;
      $arr['id'][] = $id;
      $arr['bname'][] = '"' . $bname . '"';
      $arr['bdes'][] = '"' . $bdes . '"';
      $arr['bcontact'][] = '"' . $bcontact . '"';
      $arr['bcname'][] = '"' . $bcname . '"';
      $arr['bcemail'][] = '"' . $bcemail . '"';
      $arr['bctel'][] = '"' . $bctel . '"';
      $arr['clim'][] = '"' . $clim . '"';
      $arr['cat_id'][] = $cat_id;
      $arr['dt'][] = '"' . $dt . '"';
    }
    if ($n > 0) {
      $s[] = '"id":[' . implode($arr['id'],',') . '],';
      $s[] = '"bname":[' . implode($arr['bname'],',') . '],';
      $s[] = '"bdes":[' . implode($arr['bdes'],',') . '],';
      $s[] = '"bcontact":[' . implode($arr['bcontact'],',') . '],';
      $s[] = '"bcname":[' . implode($arr['bcname'],',') . '],';
      $s[] = '"bcemail":[' . implode($arr['bcemail'],',') . '],';
      $s[] = '"bctel":[' . implode($arr['bctel'],',') . '],';
      $s[] = '"clim":[' . implode($arr['clim'],',') . '],';
      $s[] = '"cat_id":[' . implode($arr['cat_id'],',') . '],';
      $s[] = '"dt":[' . implode($arr['dt'],',') . ']';
    }
  }
  $stmt->close();
  $mc->close();
  
  $jscode =  "{";
    if ($n > 0) {
      for ($i = 0; $i < count($s); $i++) {
        $jscode .=  $s[$i];
      }
    }
    $jscode .= "}";
  $result['jscode'] = $jscode;
  $result['value'] = 'success';
  echo json_encode($result); 
  exit;
bad_exit:
  $result['value'] = 'fail';
  if (isset($errmsg)) $result['errmsg'] = $errmsg;
  echo json_encode($result); 
  exit;
  
?>

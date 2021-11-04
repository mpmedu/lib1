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
  
  if ($todo === 'Businesses' || $todo === 'Classifieds') {
    if ($todo === 'Businesses') {
      $sql="SELECT id, bname,bdes,bcontact,blogo,dt,hi_id FROM tbl_businesses WHERE cat_id = ? 
        ORDER BY bname ASC";
    } else {
      $sql="SELECT id, bname, bdes, bcontact, blogo, dt FROM tbl_classifieds WHERE cat_id = ? ";
    }
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i", $cat_id);
    if ($todo === 'Businesses') {
      $stmt->bind_result($id, $bname, $bdes, $bcontact, $blogo,$dt,$hi_id);
    } else {
      $stmt->bind_result($id, $bname, $bdes, $bcontact, $blogo, $dt);
    }
    $stmt->execute();
    while ($stmt->fetch()) {
      $n++;
      $arr['id'][] = $id;
      $arr['bname'][] = '"' . $bname . '"';
      $arr['bdes'][] = '"' . $bdes . '"';
      $arr['bcontact'][] = '"' . $bcontact . '"';
      $arr['blogo'][] = '"' . $blogo . '"';
      $arr['dt'][] = '"' . $dt . '"';
      if ($todo === 'Businesses') $arr['hi_id'][] = $hi_id;
    }
    if ($n > 0) {
      $s[] = '"id":[' . implode($arr['id'],',') . '],';
      $s[] = '"bname":[' . implode($arr['bname'],',') . '],';
      $s[] = '"bdes":[' . implode($arr['bdes'],',') . '],';
      $s[] = '"bcontact":[' . implode($arr['bcontact'],',') . '],';
      $s[] = '"blogo":[' . implode($arr['blogo'],',') . '],';
      $s[] = '"dt":[' . implode($arr['dt'],',') . ']';
      if ($todo === 'Businesses') $s[] = ',"hi_id":[' . implode($arr['hi_id'],',') . ']';
    }
    
    // *******************************
  } else if ($todo === 'EditBusinesses' || $todo === 'EditClassifieds') {  
    // *******************************
    if ($todo === 'EditBusinesses') {
      $sql="SELECT id, bname,bdes,bcontact,bcname,bcemail,bctel,blogo,cat_id,dt,hi_id FROM tbl_businesses WHERE cat_id = ? 
        ORDER BY bname ASC";
    } else {
      $sql="SELECT id, bname,bdes,bcontact,bcname,bcemail,bctel,blogo,cat_id,dt FROM tbl_classifieds WHERE cat_id = ?";
    }
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i", $cat_id);
    if ($todo === 'EditBusinesses') {
      $stmt->bind_result($id,$bname,$bdes,$bcontact,$bcname,$bcemail,$bctel,$blogo,$cat_id,$dt,$hi_id);
    } else {
      $stmt->bind_result($id,$bname,$bdes,$bcontact,$bcname,$bcemail,$bctel,$blogo,$cat_id,$dt);
    }
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
      $arr['blogo'][] = '"' . $blogo . '"';
      $arr['cat_id'][] = $cat_id;
      $arr['dt'][] = '"' . $dt . '"';
      if ($todo === 'EditBusinesses') $arr['hi_id'][] = $hi_id;
    }
    if ($n > 0) {
      $s[] = '"id":[' . implode($arr['id'],',') . '],';
      $s[] = '"bname":[' . implode($arr['bname'],',') . '],';
      $s[] = '"bdes":[' . implode($arr['bdes'],',') . '],';
      $s[] = '"bcontact":[' . implode($arr['bcontact'],',') . '],';
      $s[] = '"bcname":[' . implode($arr['bcname'],',') . '],';
      $s[] = '"bcemail":[' . implode($arr['bcemail'],',') . '],';
      $s[] = '"bctel":[' . implode($arr['bctel'],',') . '],';
      $s[] = '"blogo":[' . implode($arr['blogo'],',') . '],';
      $s[] = '"cat_id":[' . implode($arr['cat_id'],',') . '],';
      $s[] = '"dt":[' . implode($arr['dt'],',') . ']';
      if ($todo === 'EditBusinesses') $s[] = ',"hi_id":[' . implode($arr['hi_id'],',') . ']';
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

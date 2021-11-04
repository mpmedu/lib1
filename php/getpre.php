<?php
  header("content-type:application/json");

  require_once('myFunctions.php');
  // Connect to the database
  $mc =  connectToDB();
  if ($mc->connect_errno) {
    die('Connect Error (' . $mc->connect_errno . ') ' . $mc->connect_error);
  }
  
  $todo = $_GET['todo'];
  
  $result = array('value' => 'success');
  $result['point'] = '';
  
  $table = ($todo === 'businesses')? 'pre_businesses' : 'pre_classifieds';
  
  $sql="SELECT id, bname,bdes,bcontact,bcname,bcemail,bctel,blogo,cat_id,dt,mailstatus FROM $table";
  $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
  if($stmt->prepare($sql) === false) {
    trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
  }
  $stmt->bind_result($id,$bname,$bdes,$bcontact,$bcname,$bcemail,$bctel,$blogo,$cat_id,$dt,$mailstatus);
  $stmt->execute();
  $n = 0;
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
    $arr['mailstatus'][] = $mailstatus;
  }
  $stmt->close();
  $mc->close();
    
  $s[] = '"id":[' . implode($arr['id'],',') . '],';
  $s[] = '"bname":[' . implode($arr['bname'],',') . '],';
  $s[] = '"bdes":[' . implode($arr['bdes'],',') . '],';
  $s[] = '"bcontact":[' . implode($arr['bcontact'],',') . '],';
  $s[] = '"bcname":[' . implode($arr['bcname'],',') . '],';
  $s[] = '"bcemail":[' . implode($arr['bcemail'],',') . '],';
  $s[] = '"bctel":[' . implode($arr['bctel'],',') . '],';
  $s[] = '"blogo":[' . implode($arr['blogo'],',') . '],';
  $s[] = '"cat_id":[' . implode($arr['cat_id'],',') . '],';
  $s[] = '"dt":[' . implode($arr['dt'],',') . '],';
  $s[] = '"mailstatus":[' . implode($arr['mailstatus'],',') . ']';
  $jscode =  "{";
  for ($i = 0; $i < count($s); $i++) {
    $jscode .=  $s[$i];
  }
  $jscode .= "}";
  $result['jscode'] = $jscode;
  echo json_encode($result); 
  exit;
bad_exit:
  $result['value'] = 'fail';
  if (isset($emsg)) $result['emsg'] = $emsg;
  echo json_encode($result); 
  exit;
  
?>


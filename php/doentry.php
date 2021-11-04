<?php
  header("content-type:application/json");
  
  require_once('myFunctions.php');
  
//************** code starts here *********************
  $result = array('value' => 'fail');
//try {
  $todo = $_GET['todo'];
  $bid = $_POST['bactive'];
    
  // $temppath = __DIR__ . '/' . TEMPPATH;
  // $path = __DIR__ . '/' . UPLOADPATH;
  
  $temppath = TEMPPATH;
  $path = UPLOADPATH;
  
  $errmsg = '';
  $resmsg = '';
  $mailmsg = '';
  
  $mc =  connectToDB();
  if ($mc->connect_errno) {
    die('Connect Error (' . $mc->connect_errno . ') ' . $mc->connect_error);
  }
  
//************** either $todo = getbusinfo or  editbuspics *********************
  if ($todo === 'getbusinfo') {
    // ************************
    // get pictures for a business
    // ************************
    $docopy = (isset($_POST['copy']) && $_POST['copy']==='1')? true:false;
    if ($docopy) $t = $_POST['tt'];
    $sql="SELECT bname,dt,hi_id FROM tbl_businesses WHERE id=?";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i",$bid);
    $stmt->bind_result($bname,$dt,$hi_id);
    $stmt->execute();
    if (!$stmt->fetch()) goto bad_exit;
    $stmt->close();
    if (isset($_POST['code'])) {
      $code = $_POST['code'];
      if ($code != bcode($dt,$bid,$hi_id)) {
        $result['wrongcode'] = 1;
        goto bad_exit;
      }
    }
    // get the picture that is the ad for this 
    $sql="SELECT ad,pics_id FROM hilite_b WHERE id=?";
    $stmt = $mc->stmt_init();
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i",$hi_id);
    $stmt->bind_result($bfarr[0],$pics_id);
    $stmt->execute();
    if (!$stmt->fetch()) goto bad_exit;
    $stmt->close();
    if ($docopy) {
      if ($bfarr[0] && $bfarr[0] != '') {
          $img = fixImageName($bfarr[0], 'c'.(string)$t);
        mycopy($path,$temppath,$bfarr[0],$img);
        $bfarr[0]=$img;
      }
    }
    $sql="SELECT nextp FROM pics WHERE id=$pics_id";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_result($pics_id);
    $stmt->execute();
    if (!$stmt->fetch()) goto bad_exit;
    $stmt->close();
    // get the pictures that will show in the lightbox
    $title[0] = '';
    while ($pics_id != 0) {
      $sql="SELECT title,pic,nextp FROM pics WHERE id=$pics_id";
      $stmt = $mc->stmt_init();
      if($stmt->prepare($sql) === false) {
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
      }
      $stmt->bind_result($tit,$s,$pics_id);
      $stmt->execute();
      if (!$stmt->fetch()) goto bad_exit;
      $stmt->close();
      $n = (int)substr($s,0,1);
      $bfarr[$n] = substr($s,2);
      $title[$n]=$tit;
      if ($docopy) {
        if ($bfarr[$n] && $bfarr[$n] != '') {
          $t++;
          $img = fixImageName($bfarr[$n], 'c'.(string)$t);
          mycopy($path,$temppath,$bfarr[$n],$img);
          $bfarr[$n]=$img;
        }
      }
    }
    $result['bname'] = $bname;
    $result['bfarr'] = $bfarr;
    $result['title'] = $title;
    
  } else if ($todo === 'editbuspics') {
    // ************************
    // Adding or editing pictures for a business
    // ************************
    $sql="SELECT cat_id,hi_id FROM tbl_businesses WHERE id=?";
    $stmt = $mc->stmt_init();
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i",$bid);
    $stmt->bind_result($cat_id,$hi_id);
    $stmt->execute();
    if (!$stmt->fetch()) goto bad_exit;
    $stmt->close();
    //$old = array();
    $sql="SELECT ad,pics_id FROM hilite_b WHERE id=?";
    $stmt = $mc->stmt_init();
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i",$hi_id);
    $stmt->bind_result($old,$pics_id);
    $stmt->execute();
    if (!$stmt->fetch()) goto bad_exit;
    $stmt->close();
    if (isset($old) && $old != '') {
      @unlink($path . $old);
    }
    // get tab titles and pictures passed in
    $title = json_decode($_POST['title']);
    $bfarr = json_decode($_POST['bfs']);
    $marr = array();
    if ($bfarr[0] === null) {
      $img = '';
    } else if ($bfarr[0] === '') {
      $img = '';
    } else {
      $img = fixImageName($bfarr[0], 'ad' . $bid . '.' . mt_rand(1000,9999));
      array_push($marr,new fm($temppath . $bfarr[0], $path . $img));
      //if (!rename($temppath . $bfarr[0], $path . $img)) goto bad_exit;
    }
    $sql = "UPDATE hilite_b SET ad=? WHERE id=?"; 
    $stmt = $mc->stmt_init();
    $stmt = $mc->prepare($sql);
    if (!check($stmt, $mc)) goto bad_exit;
    $stmt->bind_param('si',$img,$hi_id);
    if (!check($stmt, $mc)) goto bad_exit;
    $stmt->execute();
    if (!check($stmt, $mc)) goto bad_exit;
    $stmt->close();
    
    $lastp = $pics_id;
    $sql="SELECT nextp FROM pics WHERE id=?";
    $stmt = $mc->stmt_init();
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_param("i",$lastp);
    $stmt->bind_result($nextp);
    $stmt->execute();
    if (!$stmt->fetch()) goto bad_exit;
    $stmt->close();
    for ($i = 1; $i < 5; $i++) {
      $tit = '';
      if ($title[$i] != null) {$tit = $title[$i];};
      if ($bfarr[$i] != null) {
        $img = fixImageName($bfarr[$i], 'b' . $bid . '.' . $i . '.' . mt_rand(1000,9999));
        if (isset($img) && $img != '') {
          @unlink($path . $img);
        }
        array_push($marr,new fm($temppath . $bfarr[$i], $path . $img));
        //if (!rename($temppath . $bfarr[$i], $path . $img)) goto bad_exit;
        $img2 = $i . ',' . $img;
        if ($nextp === 0) {
          // create a new link with insert and get its insert pos
          $sql = "INSERT INTO pics VALUES ('', ?, ?, 0)";
          $stmt = $mc->stmt_init();   
          $stmt = $mc->prepare($sql);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->bind_param('ss',$tit,$img2);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->execute();
          $nextp = $mc->insert_id;
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->close(); 
          // update last link
          $sql = "UPDATE pics SET nextp=? WHERE id=?";
          $stmt = $mc->stmt_init();
          $stmt = $mc->prepare($sql);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->bind_param('ii',$nextp,$lastp);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->execute();
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->close();
          $lastp = $nextp;
          $nextp = 0;
        } else {
          $lastp = $nextp;
          // first get the next pointer
          $sql="SELECT pic, nextp FROM pics WHERE id=?";
          $stmt = $mc->stmt_init();
          if($stmt->prepare($sql) === false) {
            trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
          }
          $stmt->bind_param("i",$lastp);
          $stmt->bind_result($old,$nextp);
          $stmt->execute();
          if (!$stmt->fetch()) goto bad_exit;
          $stmt->close();
          $old = substr($old,2);
          if (isset($old) && $old != '') {
            @unlink($path . $old);
          }
          $sql = "UPDATE pics SET title=?,pic=?,nextp=? WHERE id=?";
          $stmt = $mc->stmt_init();
          $stmt = $mc->prepare($sql);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->bind_param('ssii',$tit,$img2,$nextp,$lastp);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->execute();
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->close();
        }
      }
    }
    if ($nextp != 0) {
      $sql = "UPDATE pics SET nextp=0 WHERE id=?";
      $stmt = $mc->stmt_init();
      $stmt = $mc->prepare($sql);
      if (!check($stmt, $mc)) goto bad_exit;
      $stmt->bind_param('i',$lastp);
      if (!check($stmt, $mc)) goto bad_exit;
      $stmt->execute();
      if (!check($stmt, $mc)) goto bad_exit;
      $stmt->close();
      while ($nextp != 0) {
        $lastp = $nextp;
        $sql="SELECT pic, nextp FROM pics WHERE id=?";
        $stmt = $mc->stmt_init();
        if($stmt->prepare($sql) === false) {
          trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
        }
        $stmt->bind_param("i",$lastp);
        $stmt->bind_result($old,$nextp);
        $stmt->execute();
        if (!$stmt->fetch()) goto bad_exit;
        $stmt->close();
        $old = substr($old,2);
        if (isset($old) && $old != '') {
          @unlink($path . $old);
        }
        $sql = "DELETE FROM pics WHERE id=?";
        $stmt = $mc->stmt_init();   
        $stmt = $mc->prepare($sql);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->bind_param('i', $lastp);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->execute();
        $stmt->close(); 
      }
    }
    foreach($marr as $ob) {$ob->move();};
  }
 
  $mc->close();
  $result['value'] = 'success';
  $result['resmsg'] = $resmsg;
  $result['mailmsg'] = $mailmsg;
  $result['errmsg'] = $errmsg;
  echo json_encode($result); 
  exit;
bad_exit:
  //@$mc->close();
  $result['resmsg'] = $resmsg;
  $result['mailmsg'] = $mailmsg;
  $result['errmsg'] = $errmsg;
  echo json_encode($result);
  exit;
  
  class fm {
    private $from;
    private $to;
    function fm($f,$t) {
      $this->from = $f;
      $this->to = $t;
    }
    public function move() {
      rename($this->from, $this->to);
    }
  }
  
?>
  

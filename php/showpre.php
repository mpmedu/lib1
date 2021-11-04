<?php
  require_once('myFunctions.php');
  // Connect to the database
  $mc =  connectToDB();
  if ($mc->connect_errno) {
    die('Connect Error (' . $mc->connect_errno . ') ' . $mc->connect_error);
  }
  $ca = getcats($mc);
  if (!$ca) {
    echo "Fault in database";
    exit;
  }
  
  $what = $_GET['what'];
  
  $n = 0;
  $odd = false;
  echo "<div id='imgbox'>";
  
  if ($what === 'businesses') {
    $sql="SELECT id, bname,bdes,bcontact,bcname,bcemail,bctel,blogo,cat_id,dt,mailstatus FROM pre_businesses";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_result($id,$bname,$bdes,$bcontact,$bcname,$bcemail,$bctel,$plim,$cat_id,$dt,$mailstatus);
    $stmt->execute();
    while ($stmt->fetch()) {
      $n++;
      $s = ($odd)? 'odd':'even';
      echo "<div class='listcell " . $s ."' style='margin-top:-1px;'>";
      if ($plim != '') $plim = UPLOADPATH . $plim;
      $ss = '1'.SEP.$bname. SEP. $bdes. SEP. $bcontact. SEP. $plim. SEP. date('M d, Y',$dt)
        . SEP. $bcname. SEP. $bcemail. SEP. $bctel;
      echo "<div id='di_" . $id . "' data-ss='" . $ss . "'>";
      echo "<table class='outer' id='r".$id. "'>";
      echo "<tr>\n";
      if ($plim != '') {
        // get width and height from the filename
        $s = substr($plim, strpos($plim, '_') + 1);
        $p1 = strpos($s, "x");
        $p2 = strpos($s, ".", $p1);
        $w = (int)(substr($s, 0, $p1));
        $h = (int)(substr($s, $p1 + 1, $p2-$p1));
        echo "<td style='padding:4px;width:".$w."px;height:".$h."px;'><img src='" . $plim . "' /></td>\n";
      }
      echo "<td  class=''>\n";
      echo "<table class='inner1'>\n";
      echo "<tr>\n";
      echo "<tr><td class='toprow'>".$bname. "</td></tr>\n";
      echo "<tr><td class='botrow'>".$bdes. "</td></tr>\n";
      echo "<tr><td  class='botrow'>".$bcontact."</td></tr>";
      echo "</table>";
      echo "</td>\n";
      echo "<td style='width:30%;border-left:solid 1px;padding:0px;'>\n";
      echo "<div style='padding:8px;'>\n";
      echo "$ca[$cat_id]\n";
      echo "</div>\n";
      echo "<div id='bott1'>\n";
       echo "<table style='border:0px;'>\n";
       echo "<tr>";
       echo "<td>".date('M d, Y',$dt). "</td>";
       
       $xta = ($mailstatus > 0)? '&#x2714;' : '&#x2717;'; 
       echo "<td>Sent ".$xta. "</td>";
       $xta = ($mailstatus > 1)? '&#x2714;': '&#x2717;'; 
       echo "<td>Confirmed ".$xta. "</td>";
       
       echo "<td><input type='checkbox' class='cb_cls2' id='pid" . $id . "' /></td>";
       echo "</tr>\n";
       echo "</table>";
      echo "</div>\n";
      echo "</td>\n";
      echo "</tr>\n";
      echo "</table>\n";
      echo "</div>";
      echo "</div>";
      $odd = !$odd;
    }
    // *************************************************
  } else {  // what === 'classifieds'
    // *************************************************
    $sql="SELECT id, bname,bdes,bcontact,bcname,bcemail,bctel,blogo,cat_id,dt,mailstatus FROM pre_classifieds";
    
    //$sql="SELECT id, bname,bdes,bcontact,bcname,bcemail,bctel,blogo,cat_id,dt FROM pre_classifieds";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_result($id,$bname,$bdes,$bcontact,$bcname,$bcemail,$bctel,$pcim,$cat_id,$dt,$mailstatus);
    $stmt->execute();
    while ($stmt->fetch()) {
      $n++;
      $s = ($odd)? 'odd':'even';
      echo "<div class='listcell " . $s ."' style='margin-top:-1px;'>";
      if ($pcim != '') $pcim = UPLOADPATH . $pcim;
      $ss = '2'.SEP.$bname. SEP. $bdes. SEP. $bcontact. SEP. $pcim. SEP. date('M d, Y',$dt)
        . SEP. $bcname. SEP. $bcemail. SEP. $bctel;
      echo "<div id='di_" . $id . "' data-ss='" . $ss . "'>";
      echo "<table class='outer' id='r".$id. "'>";
      echo "<tr>\n";
      echo "<td  class=''>\n";
      echo "<table class='inner1'>\n";
      echo "<tr>\n";
      echo "<tr><td class='toprow'>".$bname. "</td></tr>\n";
      echo "<tr><td class='botrow'>".$bdes. "</td></tr>\n";
      echo "<tr><td  class='botrow'>".$bcontact."</td></tr>";
      echo "</table>";
      echo "</td>\n";
      
      echo "<td style='width:30%;border-left:solid 1px;padding:0px;'>\n";
      echo "<div style='padding:8px;'>\n";
      echo "$ca[$cat_id]\n";
      echo "</div>\n";
      echo "<div id='bott1'>\n";
       echo "<table style='border:0px;'>\n";
       echo "<tr>";
       echo "<td>".date('M d, Y',$dt). "</td>";
       $xta = ($mailstatus > 0)? '&#x2714;' : '&#x2717;'; 
       echo "<td>Sent ".$xta. "</td>";
       $xta = ($mailstatus > 1)? '&#x2714;': '&#x2717;'; 
       echo "<td>Confirmed ".$xta. "</td>";
       
       echo "<td><input type='checkbox' class='cb_cls2' id='pid" . $id . "' /></td>";
       echo "</tr>\n";
       echo "</table>";
      echo "</div>\n";
      echo "</td>\n";
      echo "</tr>\n";
      echo "</table>\n";
      echo "</div>";
      echo "</div>";
      $odd = !$odd;
    }
  }
  
  
  
  echo "</div>";
  if ($n === 0) {
    echo '<h3 style="text-align:center">There are no new applications</h3>';
  }
  $stmt->close();
  $mc->close();
  
?>


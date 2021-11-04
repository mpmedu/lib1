<?php
  require_once('myFunctions.php');
  
  // Connect to the database
  $mc =  connectToDB();
  if ($mc->connect_errno) {
    die('Connect Error (' . $mc->connect_errno . ') ' . $mc->connect_error);
  }
  $todo = $_GET['todo'];
  $cat_id = (int)$_GET['cat_id'];
  //$cat = $_GET['cat'];
  
  $n = 0;
  $odd = false;
  echo "<div id='imgbox'>";
  
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
    //echo "<h2 style='text-align:center'>" . $cat . "</h2>";
    $lastmargin = 0;
    while ($stmt->fetch()) {
      $n++;
      if ($hi_id > 0) {
        $nextmargin = 4;
        $s = 'hi ';
      } else {
        $nextmargin = 1;
        $s = ' ';
      }
      $s .= ($odd)? 'odd':'even';
      $want = $lastmargin;
      if ($nextmargin > $want) $want = $nextmargin;
      $ss = $want-$lastmargin-$nextmargin;
      $lastmargin = $nextmargin;
      echo "<div class='listcell " . $s ."' style='margin-top:" . $ss ."px;'>";
        // if ($hi_id > 0) {
          // $ss = ' hilite';
          // $sss = " data-sss='" . $hi_id . SEP .$dt . "'";
        // } else {
          // $ss = ' lowlite';
          // $sss = "";
        // }
        // echo "<table class='outer" . $ss . "' id='r".$id. "'" . $sss . ">";
        $ss = ($hi_id > 0)? ' hilite' : ' lowlite';
        echo "<table class='outer" . $ss . "' id='r".$id. "'>";
        echo "<tr>\n";
        if ($blogo != '') {
          // get width and height from the filename
          $s = substr($blogo, strpos($blogo, '_') + 1);
          $p1 = strpos($s, "x");
          $p2 = strpos($s, ".", $p1);
          $w = (int)(substr($s, 0, $p1));
          $h = (int)(substr($s, $p1 + 1, $p2-$p1));
          echo "<td style='padding:4px;width:".$w."px;height:".$h."px;'><img src='" . UPLOADPATH . $blogo . "' /></td>\n";
        }
        echo "<td  class=''>\n";
        echo "<table class='inner1'>\n";
        echo "<tr><td class='toprow'>".$bname. "</td></tr>\n";
        echo "<tr><td class='botrow'>".$bdes. "</td></tr>\n";
        echo "<tr><td  class='botrow'>".$bcontact."</td></tr>";
        echo "</table>";
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
      echo "</div>";
      $odd = !$odd;
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
    $stmt->bind_result($id, $bname, $bdes, $bcontact, $cim, $dt);
    $stmt->execute();
    //echo "<h2 style='text-align:center'>" . $cat . "</h2>";
    $lastmargin = 0;
    $nextmargin = 1;
    while ($stmt->fetch()) {
      $n++;
      $s = ($odd)? 'odd':'even';
      $want = $lastmargin;
      if ($nextmargin > $want) $want = $nextmargin;
      $ss = $want-$lastmargin-$nextmargin;
      $lastmargin = $nextmargin;
      echo "<div class='listcell clsf " . $s ."' style='margin-top:" . $ss ."px;'>";
      if ($cim != '') $cim = UPLOADPATH . $cim;
      $ss = SEP.$bname. SEP. $bdes. SEP. $bcontact. SEP. $cim. SEP. date('M d, Y',$dt);
      //echo "<a href='' id='di_" . $id . "' data-ss='" . $ss . "'>";
      echo "<div id='di_" . $id . "' data-ss='" . $ss . "'>";
        echo "<table class='outer' id='r".$id. "'>";
        echo "<tr>\n";
        echo "<td  class=''>\n";
        echo "<table class='inner2'>\n";
        echo "<tr><td id='bname" .$id. "' class='toprow'>".$bname. "</td></tr>\n";
        echo "<tr><td id='bdes" .$id. "' class='botrow'>".$bdes. "</td></tr>\n";
        echo "</table>";
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
      //echo "</a>";
      echo "</div>";
      echo "</div>";
      $odd = !$odd;
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
    $lastmargin = 0;
    while ($stmt->fetch()) {
      $n++;
      if ($hi_id > 0) {
        $nextmargin = 4;
        $s = 'hi ';
      } else {
        $nextmargin = 1;
        $s = ' ';
      }
      $s .= ($odd)? 'odd':'even';
      $want = $lastmargin;
      if ($nextmargin > $want) $want = $nextmargin;
      $ss = $want-$lastmargin-$nextmargin;
      $lastmargin = $nextmargin;
      echo "<div class='listcell " . $s ."' style='margin-top:" . $ss ."px;'>";
      if ($plim != '') $plim = UPLOADPATH . $plim;
      $ss = '1'.SEP.$bname. SEP. $bdes. SEP. $bcontact. SEP. $plim. SEP. date('M d, Y',$dt)
        . SEP. $bcname. SEP. $bcemail. SEP. $bctel;
      echo "<div id='di_" . $id . "' data-ss='" . $ss . "'>";
      $ss = ($hi_id > 0)? ' hilite' : '';
      echo "<table class='outer" . $ss . "' id='r".$id. "'>";
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
      echo "<td style='width:24%;border-left:solid 1px;padding:0px;'>\n";
      $ss = ($hi_id > 0)? '8px' : '0';
      echo "<div style='display:inline-block; background-color:black; color:white; padding:" . $ss . "; margin:5px;'>\n";
      if ($hi_id > 0) echo 'Upgraded';
      echo "</div>\n";
      echo "<div id='bott1'>\n";
       echo "<table style='border:0px;'>\n";
       echo "<tr>";
       echo "<td style='padding-left:6px;'>".date('M d, Y',$dt). "</td>";
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
  $stmt->bind_result($id,$bname,$bdes,$bcontact,$bcname,$bcemail,$bctel,$pcim,$cat_id,$dt);
    $stmt->execute();
    //echo "<h2 style='text-align:center'>" . $cat . "</h2>";
    $lastmargin = 0;
    $nextmargin = 1;
    while ($stmt->fetch()) {
      $n++;
      $s = ($odd)? 'odd':'even';
      $want = $lastmargin;
      if ($nextmargin > $want) $want = $nextmargin;
      $ss = $want-$lastmargin-$nextmargin;
      $lastmargin = $nextmargin;
      echo "<div class='listcell " . $s ."' style='margin-top:" . $ss ."px;'>";
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
      echo "<td style='width:24%;border-left:solid 1px;padding:0px;'>\n";
      echo "<div style='padding:8px;'>\n";
      //echo "$ca[$cat_id]\n";
      echo "</div>\n";
      echo "<div id='bott1'>\n";
       echo "<table style='border:0px;'>\n";
       echo "<tr>";
       echo "<td>".date('M d, Y',$dt). "</td>";
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
    echo '<br><br><h3 style="text-align:center">There are no entries for this category</h3>';
  }
  $stmt->close();
  $mc->close();
?>


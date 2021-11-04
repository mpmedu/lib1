<?php
  header("content-type:application/json");
  
  require_once('myFunctions.php');
  //echo "point 0 reached";
  //exit;
  
// function updateversion(&$bim, &$sim, $newv=0) {
  // $p2 = strrpos($sim, '.');
  // $ext = substr($sim, $p2);
  // $p1 = backwardStrpos($sim,'.', $p2);
  // if ($newv === 0) {
    // $newv = (int)substr($sim, $p1 + 1, $p2 - $p1 -1) + 1;
    // $bim = substr($bim, 0, $p1 + 1) . $newv . $ext;
  // } else {
    // $p3 = strpos($bim, '_', $p1);
    // $bim = substr($bim, 0, $p1 + 1) . $newv . substr($bim, $p3);
  // }
  // $sim = substr($sim, 0, $p1 + 1) . $newv . $ext;
// }

/* 
In this file, dopre.php, todo can be 1 of 6 values as follows:

1. 'addtopre' - this is executed when a user applies to have his business or classified added. The application data is added to the pre_businesses or pre_classifieds tables.

2. 'Add Selected' - called when New businesses or New classifieds is selected and then the website manager selects one or more businesses or classifieds and clicks on 'Add Selected'. The entries in the pre_businesses/pre_classifieds tables are put into the tbl_businesses/tbl_classifieds tables.

3. 'Delete Selected' - called when New businesses or New classifieds is selected and then the website manager selects one or more businesses or classifieds and clicks on 'Delete Selected'. The entries in the pre_businesses/pre_classifieds tables are deleted.

4. 'Upgrade Selected' - called when Edit businesses or Edit classifieds is selected and then the website manager selects one or more businesses or classifieds and clicks on 'Upgrade Selected'. The entries in the tbl_businesses/tbl_classifieds tables are upgraded by setting their hi_id field to point to an id in the hilite_b table (only done for businesses at present).

5. 'Downgrade Selected' - called when Edit businesses or Edit classifieds is selected and then the website manager selects one or more businesses or classifieds and clicks on 'Downgrade Selected'. The entries in the tbl_businesses/tbl_classifieds tables are downgraded by setting their hi_id field to 0.

6. 'Remove Selected' - called when Edit businesses or Edit classifieds is selected and then the website manager selects one or more businesses or classifieds and clicks on 'Remove Selected'. The entries in the tbl_businesses/tbl_classifieds tables are removed.

The input argument, what, is either 'businesses' or 'classifieds'.    
 */
 
//************** code starts here *********************
  $result = array('value' => 'fail');
  $errmsg = '';
  $resmsg = '';
  $mailmsg = '';
  $eemsg  = '';
  $eefile  = '';
  $eeline  = '';
  
  // exit;
  // exit("goodbye");
  //exit(json_encode($result));
/*   try {   // code for testing php errors, it should be commented out
    $trying = new Exception("tesing this");
    $result['abc'] = $trying->message;
     // $vv = 1/0;
     // echo "zpoint 00 reached";
    //exit(json_encode($result));
  } catch (ErrorOrWarningException $ee) {
     doError($ee);
     goto bad_exit;
    //exit(json_encode($result));
  } catch (Exception $ee) {
     doError($ee);
     goto bad_exit;
    //echo "echo in Exception catch block";
    //exit(json_encode($result));
  }
  echo "more sturff3";
  exit(json_encode($result));

 */ 
try {
  $todo = $_GET['todo'];
  $what = $_POST['what'];
  
  if (isset($_POST['baseDir'])) $baseDir = $_POST['baseDir'];
  if (isset($_POST['URL_base'])) $URL_base = $_POST['URL_base'];
  if (isset($_POST['isLocal'])) $isLocal = ($_POST['isLocal'] === '1')? true : false;
  
  $temppath3 = $baseDir . '/' . TEMPPATH;
  $path3 = $baseDir . '/' . UPLOADPATH;
  
  
  $mc =  connectToDB();
  if ($mc->connect_errno) {
    die('Connect Error (' . $mc->connect_errno . ') ' . $mc->connect_error);
  }
  $dt = time();
  $result['phpdate'] = $dt;
  
  if ($todo === 'addtopre') {
    // ************************
    // Adding to the pre tables, ie pre_businesses and pre_classifieds
    // ************************
    $cat_id = $_POST['cat_id'];
    $bname = $_POST['bname'];
    $bdes = $_POST['bdes'];
    $bcontact = $_POST['bcontact'];
    $bcname = $_POST['bcname'];
    $bcemail = $_POST['bcemail'];
    $bctel = $_POST['bctel'];
    $blogo = '';
    
    if ($what === 'businesses') {  // Add a business
      $mailstatus = 0;
      $sql = "INSERT INTO pre_businesses VALUES ('', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $mc->prepare($sql);
      if (!check($stmt, $mc)) goto bad_exit;
      $stmt->bind_param('sssssssiii', $bname,$bdes,$bcontact,$blogo,$bcname,$bcemail,$bctel,$cat_id,$dt,$mailstatus);
      if (!check($stmt, $mc)) goto bad_exit;
      $stmt->execute();
      $lastId = $mc->insert_id;
      if (!check($stmt, $mc)) goto bad_exit;
      $fullurl = $URL_base . "/php/confirm.php?em=" . $bcemail . "&r=". $lastId . "&v=". scramble($dt) . "&db=1";
      if ((int)$_POST['files_set'] === 1) {
        $bf = $_POST['bf'];
        $img = fixImageName($bf, 'plim' . $lastId);
        if (!rename($temppath3 . $bf, $path3 . $img)) goto bad_exit;
        $sql = "UPDATE pre_businesses SET blogo=? WHERE id=?"; 
        $stmt = $mc->prepare($sql);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->bind_param('si', $img, $lastId);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->execute();
        if (!check($stmt, $mc)) goto bad_exit;
      }
      // OK up to here so try to send email to the given email address
      $sitename = 'EzFind('. LOCATION . ')';
      $msg = "Hi $bcname\n\nYou have received this message because your email address was used to apply for a free business listing at the EzFind website. If you did not apply at our site, please disregard this email.
\n-------------------------------------
            Confirmation
-------------------------------------
Thank you for submitting the form to apply for your business, $bname, to be listed on our free business listing. Please click on the link below to confirm your application.
$fullurl
\nRegards
$sitename team
\n";
      if ($isLocal) {
        if (!mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Business listing',$msg, $mailmsg, $errmsg)) goto bad_exit;
        //if (!mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Business listing',$msg, $mailmsg, $errmsg)) goto bad_exit;
      } else {
        if (!mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Business listing',$msg, $mailmsg, $errmsg)) goto bad_exit;
      }
      // OK up to here so set mailstatus = 1 in DB to say that email was sent ok
      $sql = "UPDATE pre_businesses SET mailstatus=? WHERE id=?"; 
      $stmt = $mc->prepare($sql);
      if (!check($stmt, $mc)) goto bad_exit;
      $mailstatus = 1;
      $stmt->bind_param('ii', $mailstatus, $lastId);
      if (!check($stmt, $mc)) goto bad_exit;
      $stmt->execute();
      if (!check($stmt, $mc)) goto bad_exit;
      $resmsg = 'An email has been sent to ' . $bcemail . '.  You must click on the link in the email to confirm that you applied for a business listing on our site.';
    } else {    //  'Add a classified advert'
      $mailstatus = 0;
      $sql = "INSERT INTO pre_classifieds VALUES ('', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $mc->prepare($sql);
      if (!check($stmt, $mc)) goto bad_exit;
      
      $stmt->bind_param('sssssssiii', $bname,$bdes,$bcontact,$blogo,$bcname,$bcemail,$bctel,$cat_id,$dt,$mailstatus);
      
      //$stmt->bind_param('sssssssii', $bname,$bdes,$bcontact,$blogo,$bcname,$bcemail,$bctel,$cat_id,$dt);
      if (!check($stmt, $mc)) goto bad_exit;
      $stmt->execute();
      $lastId = $mc->insert_id;
      if (!check($stmt, $mc)) goto bad_exit;
      
      $fullurl = $URL_base . "/php/confirm.php?em=" . $bcemail . "&r=". $lastId . "&v=". scramble($dt) . "&db=2";
      
      if ((int)$_POST['files_set'] === 1) {
        $bf = $_POST['bf'];
        //if (!checkImage($temppath . $bf)) goto bad_exit;
        $img = fixImageName($bf, 'pcim' . $lastId);
        if (!rename($temppath3 . $bf, $path3 . $img)) goto bad_exit;
        $sql = "UPDATE pre_classifieds SET blogo=? WHERE id=?"; 
        $stmt = $mc->prepare($sql);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->bind_param('si', $img, $lastId);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->execute();
        if (!check($stmt, $mc)) goto bad_exit;
      }
      // OK up to here so try to send email to the given email address
      $sitename = 'EzFind('. LOCATION . ')';
      $msg = "Hi $bcname\n\nYou have received this message because your email address was used to apply for a free classified advert at the EzFind website. If you did not apply at our site, please disregard this email.
\n-------------------------------------
            Confirmation
-------------------------------------
Please click on the link below to confirm your application.
$fullurl
\nRegards
$sitename team
\n";
      if ($isLocal) {
        if (!mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Classified advert',$msg, $mailmsg, $errmsg)) goto bad_exit;
        //if (!mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Classified advert',$msg, $mailmsg, $errmsg)) goto bad_exit;
      } else {
        if (!mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Classified advert',$msg, $mailmsg, $errmsg)) goto bad_exit;
      }
      // OK up to here so set mailstatus = 1 in DB to say that email was sent ok
      $sql = "UPDATE pre_classifieds SET mailstatus=? WHERE id=?"; 
      $stmt = $mc->prepare($sql);
      if (!check($stmt, $mc)) goto bad_exit;
      $mailstatus = 1;
      $stmt->bind_param('ii', $mailstatus, $lastId);
      if (!check($stmt, $mc)) goto bad_exit;
      $stmt->execute();
      if (!check($stmt, $mc)) goto bad_exit;
      $resmsg = 'An email has been sent to ' . $bcemail . '.  You must click on the link in the email to confirm that you applied for a free classified advert on our site.';
    }
    $stmt->close();
    
  //} else if ($todo === 'addselected') {
  } else if ($todo === 'Add Selected') {
    
    // ************************
    // Adding to the tbl tables, ie tbl_businesses and tbl_classifieds from the pre tables
    // ************************
    $list = $_POST['list'];
    if ($list == '') goto good_exit;
    $items = explode(',', $list);
    $blank = '';
    $count = count($items);
    $cnames = explode(SEP, $_POST['cnames']);
    if (count($cnames) != $count) {
      $errmsg = "count is different for 2 lists";
      goto bad_exit;
    }
    $sitename = 'EzFind('. LOCATION . ')';
    if ($what === 'businesses') { 
      for ($i = 0; $i < $count; $i++) {
        $sql="SELECT bname,bdes,bcontact,blogo,bcname,bcemail,bctel,cat_id FROM pre_businesses WHERE id=$items[$i]";
        $stmt = $mc->stmt_init();
        if($stmt->prepare($sql) === false) {
          trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
        }
        $stmt->bind_result($bname,$bdes,$bcontact,$blogo,$bcname,$bcemail,$bctel,$cat_id);
        $stmt->execute();
        if (!$stmt->fetch()) goto bad_exit;
        $stmt->close(); 
        $sql = "DELETE FROM pre_businesses WHERE id=$items[$i]";
        $mc->query($sql);
        // now insert into the table tbl_businesses
        $sql = "INSERT INTO tbl_businesses VALUES ('', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
        $stmt = $mc->prepare($sql);
        if (!check($stmt, $mc)) goto bad_exit;
        $hi_id = 0;
        $stmt->bind_param('sssssssiii', $bname,$bdes,$bcontact,$blank,$bcname,$bcemail,$bctel,$cat_id,$dt,$hi_id);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->execute();
        $lastId = $mc->insert_id;
        if (!check($stmt, $mc)) goto bad_exit;
        if ($blogo != '') {
          $img = fixImageName($blogo, 'lim' . $lastId . '.0');
          
          try {
            //error_reporting(0);
//error_reporting(E_ALL & ~E_NOTICE); // everything but notices
error_reporting(E_ALL | E_STRICT); // everything
ini_set('display_errors',1);
//echo 'sending a message';
            //if (!rename($path3 . $blogo, $path3 . $img)) goto bad_exit;
        //trigger_error('reached this point',E_USER_ERROR);
            //$sql = "UPDATE tbl_businesses SET blogo=? WHERE id=?";
            if (rename($path3 . $blogo, $path3 . $img)) {
            //trigger_error('reached this point',E_USER_ERROR);
              $sql = "UPDATE tbl_businesses SET blogo=? WHERE id=?";
            // } else {
              // throw new Exception('I threw something');
            }
          } catch (Exception $e) {
echo 'sending a message';
            $errmsg = 'trying this out' . "\n";
            //trigger_error('reached this point',E_USER_ERROR);
            //$errmsg = $e . "\n";
            goto bad_exit;
          }
//echo 'all is ok';
          $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
          $stmt = $mc->prepare($sql);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->bind_param('si', $img, $lastId);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->execute();
          if (!check($stmt, $mc)) goto bad_exit;
        }
        $stmt->close(); 
        // OK up to here so send email that business has been added to listing
        $fullurl = $URL_base . "/php/delete.php?em=" . $bcemail . "&t=0&r=". $lastId . "&v=". scramble($dt);
        $msg = "Hi $bcname\n\nYour business, $bname, has been listed on our website in the category $cnames[$i].
\nSave this email - you can remove your business from the list by clicking on the link below.
$fullurl
\nRegards
$sitename team
\n";
        if ($isLocal) {
          if (!mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Business listed',$msg, $mailmsg, $errmsg  )) goto bad_exit;
          //mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Business listed',$msg, $mailmsg, $errmsg);
          //mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Business listed',$msg, $mailmsg, $errmsg);
          //if (!mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Business listed',$msg, $mailmsg,       $errmsg)) goto bad_exit;
        } else {
          mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Business listed',$msg, $mailmsg, $errmsg);
          //if (!mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Business listed',$msg, $mailmsg, $errmsg)) goto bad_exit;
        }
      }
    } else {    // $what === 'classifieds')
      for ($i = 0; $i < $count; $i++) {
        $sql="SELECT bname,bdes,bcontact,blogo,bcname,bcemail,bctel,cat_id,dt FROM pre_classifieds WHERE id=$items[$i]";
        $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
        if($stmt->prepare($sql) === false) {
          trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
        }
        $stmt->bind_result($bname,$bdes,$bcontact,$blogo,$bcname,$bcemail,$bctel,$cat_id,$dt);
        $stmt->execute();
        if (!$stmt->fetch()) goto bad_exit;
        $stmt->close(); 
        $sql = "DELETE FROM pre_classifieds WHERE id=$items[$i]";
        $mc->query($sql);
        // now insert into the table tbl_businesses
        
        $sql = "INSERT INTO tbl_classifieds VALUES ('', ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
        $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
        $stmt = $mc->prepare($sql);
        if (!check($stmt, $mc)) goto bad_exit;
        $hi_id = 0;
        $stmt->bind_param('sssssssiii', $bname,$bdes,$bcontact,$blank,$bcname,$bcemail,$bctel,$cat_id,$dt,$hi_id);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->execute();
        $lastId = $mc->insert_id;
        if (!check($stmt, $mc)) goto bad_exit;
        if ($blogo != '') {
          $img = fixImageName($blogo, 'cim' . $lastId . '.0');
          if (!rename($path3 . $blogo, $path3 . $img)) goto bad_exit;
          $sql = "UPDATE tbl_classifieds SET blogo=? WHERE id=?"; 
          $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
          $stmt = $mc->prepare($sql);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->bind_param('si', $img, $lastId);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->execute();
          if (!check($stmt, $mc)) goto bad_exit;
        }
        $stmt->close(); 
//$reached = 1;
        // OK up to here so send email that classified ad has been added to listing
        $fullurl = $URL_base . "/php/delete.php?em=" . $bcemail . "&t=1&r=". $lastId . "&v=". scramble($dt);
        $msg = "Hi $bcname\n\nYour classified advert has been listed on our website in the category $cnames[$i].
\nThe advert will remain on the site for 3 weeks - it will then be removed automatically. You can remove it manually by clicking on the link below.
$fullurl
\nRegards
$sitename team
\n";
//$reached = 2;
        if ($isLocal) {
          if (!mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg))  goto bad_exit;
          //mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg);
          //mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg);
          //if (!mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg)) goto bad_exit;
        } else {
          mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg);
          //if (!mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg)) goto bad_exit;
        }
      }
    }
    
  } else if ($todo === 'Delete Selected') {
    // ************************
    // Deleting from the pre tables
    // ************************
    $tbl = ($what === 'businesses')? 'pre_businesses' : 'pre_classifieds';
    if (!deleteRec_and_pic($path3,$mc,$tbl)) goto bad_exit;
    
  } else if ($todo === 'Upgrade Selected') {  
    // ************************
    // Upgrading in tbl tables
    // ************************
    $list = $_POST['list'];
    if ($list == '') goto good_exit;
    $items = explode(',', $list);
    $blank = '';
    $count = count($items);
    $sitename = 'EzFind('. LOCATION . ')';
    if ($what === 'businesses') {
          // $result['point'] = 1;
          // $result['count'] = $count;
      for ($i = 0; $i < $count; $i++) {
        $id = $items[$i];
        $sql="SELECT bname,bcname,bcemail,cat_id,dt,hi_id FROM tbl_businesses WHERE id=$id";
        $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
        if($stmt->prepare($sql) === false) {
          trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
        }
        $stmt->bind_result($bname,$bcname,$bcemail,$cat_id,$dt,$hi_id);
        $stmt->execute();
        if (!$stmt->fetch()) goto bad_exit;
        $stmt->close();
          //$result['hi_id'] = $hi_id;
        if ($hi_id === 0) {
          // first create the chain start in pics
          $sql = "INSERT INTO pics VALUES ('', '', '', 0)";
          $stmt = $mc->stmt_init();
          $stmt = $mc->prepare($sql);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->execute();
          $pics_id = $mc->insert_id;
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->close(); 
          // insert a record in hilite_b table
          $sql = "INSERT INTO hilite_b VALUES ('', $cat_id,'', 0, $pics_id)";
          $stmt = $mc->stmt_init();
          $stmt = $mc->prepare($sql);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->execute();
          $lastId = $mc->insert_id;
          //$result['lastid'] = $lastId;
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->close(); 
          $sql = "UPDATE tbl_businesses SET hi_id=? WHERE id=?"; 
          $stmt = $mc->stmt_init();
          $stmt = $mc->prepare($sql);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->bind_param('ii', $lastId, $id);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->execute();
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->close(); 
          // OK up to here so send email that business has been upgraded
          $fullurl = $URL_base . "/editentry.php?em=" . $bcemail . "&t=0&r=". $id . "&v=". scramble($dt);
          $bcode = bcode($dt,$id,$lastId);
          $msg = "Hi $bcname\n\nYour business, $bname, has been upgraded on our website.
\nSave this email - when you want to edit your entry click on the link below.
$fullurl
\nYou can also make changes as follows: click on your business while holding down the Alt key, then type in $bcode and press Enter within a time limit of 10 seconds.
\nRegards
$sitename team
\n";
          if ($isLocal) {
            if (!mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Business upgraded',$msg, $mailmsg, $errmsg)) goto bad_exit;
            mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Business upgraded',$msg, $mailmsg, $errmsg);
            //mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Business upgraded',$msg, $mailmsg, $errmsg);
            //if (!mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Business upgraded',$msg, $mailmsg, $errmsg)) goto bad_exit;
          } else {
            mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Business upgraded',$msg, $mailmsg, $errmsg);
            //if (!mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Business upgraded',$msg, $mailmsg, $errmsg)) goto bad_exit;
          }
        }
      }
    } else {    // $what === 'classifieds')
      for ($i = 0; $i < $count; $i++) {
        $sql="SELECT bname,bdes,bcontact,blogo,bcname,bcemail,bctel,cat_id,dt FROM pre_classifieds WHERE id=$items[$i]";
        $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
        if($stmt->prepare($sql) === false) {
          trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
        }
        $stmt->bind_result($bname,$bdes,$bcontact,$blogo,$bcname,$bcemail,$bctel,$cat_id,$dt);
        $stmt->execute();
        if (!$stmt->fetch()) goto bad_exit;
        $stmt->close(); 
        $sql = "DELETE FROM pre_classifieds WHERE id=$items[$i]";
        $mc->query($sql);
        // now insert into the table tbl_businesses
        $sql = "INSERT INTO tbl_classifieds VALUES ('', ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
        $stmt = $mc->prepare($sql);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->bind_param('sssssssii', $bname,$bdes,$bcontact,$blank,$bcname,$bcemail,$bctel,$cat_id,$dt);
        if (!check($stmt, $mc)) goto bad_exit;
        $stmt->execute();
        $lastId = $mc->insert_id;
        if (!check($stmt, $mc)) goto bad_exit;
        if ($blogo != '') {
          $img = fixImageName($blogo, 'cim' . $lastId . '.0');
          if (!rename($path3 . $blogo, $path3 . $img)) goto bad_exit;
          $sql = "UPDATE tbl_classifieds SET blogo=? WHERE id=?"; 
          $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
          $stmt = $mc->prepare($sql);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->bind_param('si', $img, $lastId);
          if (!check($stmt, $mc)) goto bad_exit;
          $stmt->execute();
          if (!check($stmt, $mc)) goto bad_exit;
        }
        $stmt->close(); 
//$reached = 1;
        // OK up to here so send email that business has been added to listing
        $fullurl = $URL_base . "/php/delete.php?em=" . $bcemail . "&t=1&r=". $lastId . "&v=". scramble($dt);
        $msg = "Hi $bcname\n\nYour classified advert has been upgraded on our website.
\nThe advert will remain on the site for 5 weeks - it will then be removed automatically. You can remove it manually by clicking on the link below.
$fullurl
\nRegards
$sitename team
\n";
//$reached = 2;
        if ($isLocal) {
          if (!mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg)) goto bad_exit;
          //mySend_swiftmailer($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg);
          //mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg);
          //if (!mySend_phpmailer($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg)) goto bad_exit;
        } else {
          mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg);
          //if (!mySend_sendgrid($bname,$bcname,$bcemail,$sitename,'Advert listed',$msg, $mailmsg, $errmsg)) goto bad_exit;
        }
      }
    }
    
  } else if ($todo === 'Downgrade Selected') {
    // ************************
    // Degrading selected items in tbl tables
    // ************************
    if ($what === 'businesses') {
      $tbl='tbl_businesses';
      $tbl2='hilite_b';
    } else {
      $tbl='tbl_classifieds';
      $tbl2='hilite_c';
    }
    if (!degrade_remove_hi_rec($path3,$mc,$tbl,$tbl2, 1)) goto bad_exit;

    
  } else if ($todo === 'Remove Selected') {
    // ************************
    // removing from the tbl tables
    // ************************
    if ($what === 'businesses') {
      $tbl='tbl_businesses';
      $tbl2='hilite_b';
    } else {
      $tbl='tbl_classifieds';
      $tbl2='hilite_c';
    }
    // must delete the record in hilite_b
    if (!degrade_remove_hi_rec($path3,$mc,$tbl,$tbl2, 0)) goto bad_exit;
  }
} catch (Exception $ee) {
  doError($ee);
  $result['point'] = 10;
  goto bad_exit;
}
 
good_exit:
  $mc->close();
  $result['value'] = 'success';
  $result['resmsg'] = $resmsg;
  $result['mailmsg'] = $mailmsg;
  $result['errmsg'] = $errmsg;
  // echo json_encode($result); 
  // exit;
  exit(json_encode($result));
  // die(json_encode($result));
bad_exit:
  //@$mc->close();
  $result['value'] = 'fail';
  $result['resmsg'] = 'Bad exit';
  $result['mailmsg'] = $mailmsg;
  $result['errmsg'] = $errmsg;
  if ($mailmsg !== '') error_log('$mailmsg : ' . $mailmsg);
  if ($errmsg !== '') error_log('$errmsg : ' . $errmsg);
  if (isset($result['eemsg'])) error_log('$eemsg : ' . $result['eemsg']);
  if (isset($result['eefile'])) error_log('$eefile : ' . $result['eefile']);
  if (isset($result['eeline'])) error_log('$eeline : ' . $result['eeline']);
  if (isset($result['eehost'])) error_log('$eehost : ' . $result['eehost']);
  if (isset($result['eeerrno'])) error_log('$eeerrno : ' . $result['eeerrno']);
  if (isset($result['eegtas'])) error_log('$eegtas : ' . $result['eegtas']);
  if (isset($result['eecode'])) error_log('$eecode : ' . $result['eecode']);
  
  // echo json_encode($result); 
  // exit;
  //exit(json_encode($result)); // same as echo json_encode($result);  exit;
  die(json_encode($result)); // same as echo json_encode($result);  exit;
?>

  
<?php

//***************************************************
//***************************************************
// functions used by the above code
//***************************************************
//***************************************************


function mySend_swiftmailer($to_busname,$to_name,$to_email,$sitename,$subject,$msg, &$mailmsg, &$errmsg) {
  require_once '../swiftmailer/lib/swift_required.php';
  //try {
    $mailmsg = "Message could not be sent using SwiftMailer.\nerror";
    //return false;
    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, "tls")
    //$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
      ->setUsername(EMAIL)
      ->setPassword(EMAIL_PASSWORD);
    $mailer = Swift_Mailer::newInstance($transport);
    $message = Swift_Message::newInstance($subject)
      ->setFrom(array(EMAIL => $sitename))
      ->setTo(array($to_email => $to_busname. '('.$to_name. ')'))
      ->setBody($msg);
    $numSent = $mailer->send($message);
    if ($numSent === 0) {
      $errmsg .= "Message could not be sent using SwiftMailer.\n";
      return false;
    } else {
      $mailmsg = "Message has been sent via gmail using SwiftMailer.\n";
      return true;
    }
/*   } catch (Exception $ee) {
    throw new Exception $ee;
    //doError($ee);
    return false;
  }
 */}

function mySend_phpmailer($to_busname,$to_name,$to_email,$sitename,$subject,$msg, &$mailmsg, &$errmsg) {
  require_once '../phpmailer/PHPMailerAutoload.php';
  try {
    $mailmsg = "Message could not be sent using PHPMailer.\nerror";
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = EMAIL;                 // SMTP username
    $mail->Password = EMAIL_PASSWORD;                           // SMTP password
    $mail->SMTPSecure = 'tls';      // Enable encryption, 'ssl' also accepted
    //$mail->SMTPSecure = 'ssl';      // Enable encryption, 'ssl' also accepted

    $mail->From = EMAIL;
    $mail->FromName = $sitename;
    $mail->addAddress($to_email, $to_busname. '(' .$to_name. ')');     // Add a recipient
     $mail->isHTML(false);                         // Set email format to plain text

    $mail->Subject = $subject;
    $mail->Body    = $msg;

    if(!$mail->send()) {
      $errmsg .= "Message could not be sent using PHPMailer.\n";
      $errmsg .= " Mailer Error: " . $mail->ErrorInfo . "\n";
      return false;
    } else {
      $mailmsg = "Message has been sent via gmail using PHPMailer.\n";
      return true;
    }
  } catch (Exception $e) {
    $errmsg .= $e . "\n";
    return false;
  }
}

function mySend_sendgrid($to_busname,$to_name,$to_email,$sitename,$subject,$msg, &$mailmsg, &$errmsg) {
  require_once '../vendor/autoload.php';
  try {
    $mailmsg = "Message could not be sent using SendGrid.\nerror";
    $sendgrid = new SendGrid(SENDGRID_USERNAME, SENDGRID_PASSWORD);
    $email    = new SendGrid\Email();
    $email->addTo($to_email, $to_busname. '(' .$to_name. ')')->
      setFrom(EMAIL, $sitename)->
      setSubject($subject)->
      setText($msg);
    if (!$sendgrid->send($email)) {
      $errmsg =  "Message could not be sent using SendGrid.\n";
      return false;
    } else {
      $mailmsg = "Message has been sent via SendGrid\n";
      return true;
    }
  } catch (Exception $e) {
    $errmsg .= $e . "\n";
    return false;
  }
}

function deleteRec_and_pic($path,$mc,$tbl) {
  $list = $_POST['list'];
  if ($list == '') return true;
  $items = explode(',', $list);
  $count = count($items);
  $n = 0;
  for ($i = 0; $i < $count; $i++) {
    $sql="SELECT blogo FROM $tbl WHERE id=$items[$i]";
    $stmt = $mc->stmt_init();   // this is optional but good practice, next must be prepare
    if($stmt->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt->bind_result($blogo);
    $stmt->execute();
    if (!$stmt->fetch()) {
      $stmt->close(); 
      $n++;
      continue;
    }
    $stmt->close(); 
    $sql = "DELETE FROM $tbl WHERE id=$items[$i]";
    $mc->query($sql);
    if (isset($blogo) && $blogo != '') {
      @unlink($path . $blogo);
    }
  }
  if ($n > 0) return false;
  return true;
}

function degrade_remove_hi_rec($path,$mc,$tbl,$tbl2,$degrade=0) {
  $list = $_POST['list'];
  if ($list == '') return true;
  $items = explode(',', $list);
  $count = count($items);
  $n = 0;
  for ($i = 0; $i < $count; $i++) {
    $sql="SELECT blogo,hi_id FROM $tbl WHERE id=$items[$i]";
    $stmt2 = $mc->stmt_init();
    if($stmt2->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt2->bind_result($blogo,$hi_id);
    $stmt2->execute();
    if (!$stmt2->fetch()) {
      $stmt2->close(); 
      $n++;
      continue;
    }
    $stmt2->close();
    if ($degrade===1) {
      // must degrade
      $zero = 0;
      $sql = "UPDATE $tbl SET hi_id=$zero WHERE id=$items[$i]";
      $stmt2 = $mc->stmt_init();
      if($stmt2->prepare($sql) === false) {
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
      }
      //$stmt2->bind_result($hi_id);
      $stmt2->execute();
      $stmt2->close();
    } else {
      // must remove
      $sql = "DELETE FROM $tbl WHERE id=$items[$i]";
      $mc->query($sql);
      if (isset($blogo) && $blogo != '') {
        @unlink($path . $blogo);
      }
    }
    // if this is not a hilite business then return else remove from hilite_b and pics tables
    if ($hi_id === 0) return true;
    $sql="SELECT ad,pics_id FROM $tbl2 WHERE id=$hi_id";
    $stmt2 = $mc->stmt_init();
    if($stmt2->prepare($sql) === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
    }
    $stmt2->bind_result($ad,$pics_id);
    $stmt2->execute();
    if (!$stmt2->fetch()) {
      $stmt2->close(); 
      $n++;
      continue;
    }
    $stmt2->close(); 
    if (!delete_pic($path,$mc,'pics',$pics_id)) {
      $n++;
      //continue;
    }
    $sql = "DELETE FROM $tbl2 WHERE id=$hi_id";
    $mc->query($sql);
    if (isset($ad) && $ad != '') {
      @unlink($path . $ad);
    }
    
  }
  if ($n > 0) return false;
  return true;
}

function delete_pic($path,$mc,$tbl,$p) {
  if ($p === 0) return true;
  $sql="SELECT pic,nextp FROM $tbl WHERE id=$p";
  $stmt3 = $mc->stmt_init();
  if($stmt3->prepare($sql) === false) {
    trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
  }
  $stmt3->bind_result($pic,$nextp);
  $stmt3->execute();
  if (!$stmt3->fetch()) {
    $stmt3->close(); 
    return false;
  }
  $stmt3->close(); 
  $sql = "DELETE FROM $tbl WHERE id=$p";
  $mc->query($sql);
  if ($pic != '') {
    $pic = substr($pic,strpos($pic,',') + 1);
    if (isset($pic) && $pic != '') {
      @unlink($path . $pic);
    }
  }
  return delete_pic($path,$mc,$tbl,$nextp);
}

function doError($ee){
  global $result;
  $result['eeline'] = $ee->getLine();
  $result['eefile'] = $ee->getFile();
  $result['eemsg'] = $ee->getMessage();
  $result['eegtas'] = $ee->getTraceAsString();
  $result['eecode'] = $ee->getCode();
  $context = $ee->getContext();
  if (isset($context['host'])) $result['eehost'] = $context['host'];
  if (isset($context['errno'])) $result['eeerrno'] = $context['errno'];
}
  
?>


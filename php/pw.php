<?php
session_start();
header("content-type:application/json");

/* 
pw1_hash = hash of normal login password
pw2_hash = hash of masterkey password
For a normal login, ie checkLoginPw, you must only enter one password - you can enter either 
the normal password or the masterkey password.
For a full check, ie checkAllPw, both the normal password must be correct and
the masterkey password must be correct.
The dosave and dochange cases do not check the existing passwords, they simply save
the new passwords in the admin table, either with INSERT for dosave or with UPDATE for dochange.
 */
 
  $result = array('value' => 'success');
  $result['res'] = false;
   
  if (!array_key_exists("todo", $_GET)) goto bad_exit;
  $todo = $_GET['todo'];

  if ($todo === 'checkLoginPw') {
    try {
      require_once('myencrypt.php');
      if ((check_hash($_POST['username'], $_SESSION['username_hash']))) {
        if ((check_hash($_POST['pw1'], $_SESSION['pw1_hash'])) 
          || (check_hash($_POST['pw1'], $_SESSION['pw2_hash']))) {
          $result['res'] = true;
          $_SESSION['masterKey'] = 2;
          if (!getAdminLevel($_SESSION['adminlevel'],$emsg)) goto bad_exit;
          adjustCookie($_SESSION['adminlevel'],$emsg);
        }
      }
    } catch (Exception $e) {
      $emsg = $e . "\n";
      goto bad_exit;
    }
    
  } else if ($todo === 'checkAllPw') {
    try {
      require_once('myencrypt.php');
      if ((check_hash($_POST['username'], $_SESSION['username_hash']))) {
        if ((check_hash($_POST['pw1'], $_SESSION['pw1_hash']))) {
          if ((check_hash($_POST['pw2'], $_SESSION['pw2_hash']))) {
            $result['res'] = true;
            if (!getAdminLevel($_SESSION['adminlevel'],$emsg)) goto bad_exit;
            adjustCookie($_SESSION['adminlevel'],$emsg);
          }
        }
      }
    } catch (Exception $e) {
      $emsg = $e . "\n";
      goto bad_exit;
    }
    
  } else if ($todo === 'dosave' || $todo === 'dochange') {
    try {
      $result['res'] = true;
      require_once('myFunctions.php');
      require_once('myencrypt.php');
      $_SESSION['username_hash'] = create_hash($_POST['username']);
      $_SESSION['pw1_hash'] = create_hash($_POST['pw1']);
      $_SESSION['pw2_hash'] = create_hash($_POST['pw2']);
      $mc =  connectToDB();
      if ($mc->connect_errno) {
        die('Connect Error (' . $mc->connect_errno . ') ' . $mc->connect_error);
      }
      if ($todo === 'dosave') {
        $sql = "INSERT INTO admin VALUES ('',  '', ?)";
      } else {
        $sql = "UPDATE admin SET pw=? WHERE id=1";
      }
      $stmt = $mc->prepare($sql);
      check($stmt, $mc);
      $stmt->bind_param('s', $_SESSION['pw2_hash']);
      check($stmt, $mc);
      $stmt->execute();
      $sql = "INSERT INTO admin VALUES ('',  ?, ?)";
      $stmt = $mc->prepare($sql);
      check($stmt, $mc);
      $stmt->bind_param('ss', $_SESSION['username_hash'], $_SESSION['pw1_hash']);
      check($stmt, $mc);
      $stmt->execute();
      $stmt->close();
      $mc->close();
      if (!getAdminLevel($_SESSION['adminlevel'],$emsg)) goto bad_exit;
      adjustCookie($_SESSION['adminlevel'],$emsg);
    } catch (Exception $e) {
      $emsg = $e . "\n";
      goto bad_exit;
    }
    
  } else if ($todo === 'checkAdminLevel') {
    // This procedure checks if clicking on the admin menu is valid.
    // It returns true if the latest adminlevel = the session adminlevel.
    // Note that this should usually return true - the only time that it will not return true is if the master
    // administrator changed the passwords after this user logged in manually or automatically for this session.
    try { 
      if (!getAdminLevel($num_rows,$emsg)) goto bad_exit;
      if (isset($_SESSION['adminlevel']) && $num_rows === $_SESSION['adminlevel']) {
        $result['res'] = true;
      }
    } catch (Exception $e) {
      $emsg = $e . "\n";
      goto bad_exit;
    }
    
  } else if ($todo === 'setLoginCookie') {
    // This procedure either sets the adminlevel cookie to plus or minus.  
    // A negative value means that the cookie is set to off.
    try {
      $onoroff = $_POST['onoroff'];
      $n = $_SESSION['adminlevel'];
      if ($onoroff !== 'on') $n = -$n;
      setcookie('adminlevel',$n,time()+ 60*60*24*30);  
    } catch (Exception $e) {
      $emsg = $e . "\n";
      goto bad_exit;
    }
    
  } else if ($todo === 'checkLoginCookie') {
    // called to see if auto login applies.  if auto login is true then 
    // $result['value'] = 'success' is returned.
    // this procedure also resets the expiry time of the adminlevel cookie
      //$result['point'] = $result['point'] . " point 1";
    try {
      //$result['point'] = $result['point'] . " point 1";
      if (!getAdminLevel($_SESSION['adminlevel'],$emsg)) goto bad_exit;
      if (isset($_COOKIE['adminlevel'])) {
        if ((int)$_COOKIE['adminlevel'] === (int)$_SESSION['adminlevel']) { 
          //$result['point'] = $result['point'] . " point 2";
          $result['res'] = true;
          adjustCookie($_SESSION['adminlevel'],$emsg);
        }
      }
    } catch (Exception $e) {
      $emsg = $e . "\n";
      //$result['point'] = $result['point'] . " catch 1";
      goto bad_exit;
    }
    
  } else if ($todo === 'setacookie') {
    // this procedure is not used in this project
    try {
      $name = $_POST['name'];
      $value = $_POST['value'];
      $seconds = $_POST['seconds'];
      setcookie($name,$value,time()+ $seconds);  
    } catch (Exception $e) {
      $emsg = $e . "\n";
      goto bad_exit;
    }
  }
  
  // $result['numrows'] = $_SESSION['adminlevel'];
  // $result['cookielevel'] = $_COOKIE['adminlevel'];

  echo json_encode($result); 
  exit;
  
bad_exit:
  $result['value'] = 'fail';
  if (isset($emsg)) $result['emsg'] = $emsg;
  echo json_encode($result); 
  exit;
  
function getAdminLevel(&$n,&$emsg){
  require_once('myFunctions.php');
  $mc =  connectToDB();
  if ($mc->connect_errno) {
    $emsg = 'Connect error';
    return false;
  }
  try {
    $query = "SELECT id FROM admin";
    $n = mysqli_num_rows(mysqli_query($mc,$query));  
    $mc->close();
    if ($n === 0) {
      $emsg = 'num rows is 0' . "\n";
      return false;
    }
  } catch (Exception $e) {
    $emsg = $e . "\n";
    return false;
  }
  return true;
}

function adjustCookie($n,&$emsg){
  try {
    if (!isset($_COOKIE['adminlevel'])) return false;
    $cv = (int)$_COOKIE['adminlevel'];
    if ($cv < 0) $n = -$n;
    setcookie('adminlevel',$n,time()+ 60*60*24*30);
  } catch (Exception $e) {
    $emsg = $e . "\n";
    return false;
  }
  return true;
}
  
?>

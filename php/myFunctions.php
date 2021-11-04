<?php

define('YR', date("Y"));

define('LOCATION', 'Gotham');
define('EMAIL', 'mpm.ezfind@gmail.com');    // eg gmail such as mpm.ezfind@gmail.com, always needed
define('EMAIL_PASSWORD', 'ezf3555943');  // password on gmail, not needed for SendGrid
define('SENDGRID_USERNAME', 'mpmezf');     // for SendGrid
define('SENDGRID_PASSWORD', 'sgg3555943');  // for SendGrid
// database constants
define('DB_HOST', 'localhost');
define('DB_USER', 'mpm');
define('DB_PASSWORD', 'xyz123');
define('DB_NAME', 'ezf4');


// constants that will always remain the same
define('DOCPATH', 'docs');
define('TEMPPATH', 'temp_images/');
define('UPLOADPATH', 'images/');
define('RHSIMGPATH', 'rhs_images/');
//define('MAXFILESIZE', 2000000);      // 2 MB
define('SEP', '&#x2714;' . '||' . '&#x2717;');

/*

// constants for sending mail
define('EMAIL', 'mpm.ezfind@gmail.com');  // for SendGrid & PHPMailer/SwiftMailer 
define('EMAIL_PASSWORD', 'ezf3555943');  // for PHPMailer & SwiftMailer(using GMAIL server)
define('SENDGRID_USERNAME', 'mpmezf');     // for SendGrid
define('SENDGRID_PASSWORD', 'sgg3555943');  // for SendGrid

// database constants
define('DB_HOST', 'localhost');
define('DB_USER', 'mpm');
define('DB_PASSWORD', 'xyz123');
define('DB_NAME', 'ezf4');

// ******* connect variables for FreeHostingEU
// define('DB_HOST', 'fdb4.freehostingeu.com');
// define('DB_USER', '1288124_db1');
// define('DB_PASSWORD', 'testing123ABC###');
// define('DB_NAME', '1288124_db1');

// ******* connect variables for HelioHost.org
// define('DB_HOST', 'localhost');
// define('DB_USER', 'mpmedu_home');
// define('DB_PASSWORD', 'mpm3555943pm');
// define('DB_NAME', 'mpmedu_home');

// ******* connect variables for Hostinger
// define('DB_HOST', 'mysql.hostinger.co.uk');
// define('DB_USER', 'u676010237_mpm');
// define('DB_PASSWORD', 'mpmedu');
// define('DB_NAME', 'u676010237_mpm');
*/


/* $topmenu[0] = 'myPhotos';
$topmenu[1] = 'myVideos';
$topmenu[2] = 'myLinks';
$topmenu[3] = 'Admin';
$submenu[3][1] = 'Add/Edit categories';
$submenu[3][2] = 'Add/Edit content';
 */
 
 
/* set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
  if (error_reporting()) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
  }
});
 */ 
 
 error_reporting(E_ALL | E_STRICT); // everything

function errortype($v) {
$errortype = array(
  E_ERROR           => 'error',
  E_WARNING         => 'warning',
  E_PARSE           => 'parsing error',
  E_NOTICE          => 'notice',
  E_CORE_ERROR      => 'core error',
  E_CORE_WARNING    => 'core warning',
  E_COMPILE_ERROR   => 'compile error',
  E_COMPILE_WARNING => 'compile warning',
  E_USER_ERROR      => 'user error',
  E_USER_WARNING    => 'user warning',
  E_USER_NOTICE     => 'user notice');
  if(defined('E_STRICT'))  $errortype[E_STRICT] = 'runtime notice';
  return $errortype[$v];
}

function shutdown_function(){
  $err = error_get_last();
  //echo 'in sf';
  if ($err) {
    echo "{qqqxxx";
    $err['name'] = errortype($err['type']);
    echo json_encode($err);
    echo "xxxqqq}";
  } 
}

register_shutdown_function('shutdown_function');
 
class ErrorOrWarningException extends Exception {
  protected $_Context = null;
  public function getContext() {
    return $this->_Context;
  }
  public function setContext( $value ) {
    $this->_Context = $value;
  }
  
  public function __construct($code, $message, $file, $line, $context) {
    parent::__construct($message, $code);
    $this->file = $file;
    $this->line = $line;
    $this->setContext($context);
  }
}

function error_to_exception($code, $message, $file, $line, $context) {
  //echo "in error_to_exception";
  throw new ErrorOrWarningException($code, $message, $file, $line, $context);
}
set_error_handler('error_to_exception');



function global_exception_handler( $ex ) {
  // ob_start();
  // dump_exception( $ex );
  // $dump = ob_get_clean();
  
  $dump = dump_exception( $ex );
  
  // send email of dump to administrator?...
  // if we are in debug mode we are allowed to dump exceptions to the browser.
  if ( defined( 'DEBUG' ) && DEBUG == true ) {
    echo $dump;
  }
  else {  // if we are in production we give our visitor a nice message without all the details.
    //echo file_get_contents( 'static/errors/fatalexception.html' );
  }
  exit;
}

function dump_exception( Exception $ex ) {
  // $file = $ex->getFile();
  // $line = $ex->getLine();
  // if ( file_exists( $file ) ) {
    // $lines = file( $file );
  // }
  return $ex->getMessage();
}

set_exception_handler('global_exception_handler');
 
 
 
function connectToDB() {
//die('a statement');
  //    die('Connect Error (' . $mc->connect_errno . ') ' . $mc->connect_error);
  try {
    $con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 
    if (!$con) die('Error connecting');
    return $con;
  } catch (Exception $e) {
    echo 'exception caught';
      //handle_exception();
  }
}
  
function check($stmt, $mc) {
  // if($stmt === false) {
    // trigger_error('Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
  // } 
  if ($mc->errno) return;  
  if($stmt === false) return;
  return 1;
}

function backwardStrpos($haystack, $needle, $offset = 0){
  $length = strlen($haystack);
  $offset = ($offset > 0)?($length - $offset):abs($offset);
  $pos = strpos(strrev($haystack), strrev($needle), $offset);
  return ($pos === false)?false:( $length - $pos - strlen($needle) );
}

/* function getcats($mc2) {
  // get all the category ids and names from the categories DB
  $sql2 = "SELECT id, name FROM categories";
  $stmt2 = $mc2->stmt_init();   // this is optional but good practice, next must be prepare
  if($stmt2->prepare($sql2) === false) {
  echo "problem after prepare";
    return false;
    //trigger_error('Wrong SQL: ' . $sql2 . ' Error: ' . $mc->errno . ' ' . $mc->error, E_USER_ERROR);
  }
  $stmt2->bind_result($id, $name);
  $stmt2->execute();
  while ($stmt2->fetch()) {
    $arr[$id] = $name;
  }
  $stmt2->close();
  return $arr;
}
 */
function scramble($s) {
  // scrambles a timestamp, puts last 3 digits at beginning
  $nn = strlen($s) - 3;
  //$ss = substr($s,$nn);
  return substr($s,$nn) . substr($s,0,$nn);
}

function fixImageName($bf, $im) {
// changes an image name keeping everything form _ in the old name
  $pos = strrpos($bf,'_');
  return $im . substr($bf, $pos);
}

function bcode($ts,$id,$hi_id) {
// returns a 6 digit number that a user must use to edit his entry
  $indarr = array();
  for ($i = 0;$i<6;$i++) {
    $indarr[$i] = $i;
  }
  $a = array();
  $n = 6;
  $lastv = -1;
  $total = 0;
  $ts = (string)$ts;
  for ($i = 1;$i<5;$i++) {
    $v = (int)substr($ts,-$i,1);
    $v = nextv($v,$lastv,$n);
    $ind = indx($total,$v,$n);
    $a[$indarr[$ind]] = $v;
    if ($ind != $n) $indarr[$ind] = $indarr[$n];
  }
  $v = nextv($id,$lastv,$n);
  $ind = indx($total,$v,$n);
  $a[$indarr[$ind]] = $v;
  if ($ind != $n) $indarr[$ind] = $indarr[$n];
  $v = nextv($hi_id,$lastv,$n);
  $ind = indx($total,$v,$n);
  $a[$indarr[$ind]] = $v;
  return implode($a);
}

function indx(&$total,$v,&$n) {
  $total += $v;
  $ind = $total % $n;
  $n--;
  return $ind;
}

function nextv($v,&$lastv,$n) {
  $v = $v % 10;
  if ($v === $lastv) $v = ($v + $n) % 10;
  $lastv = $v;
  return $v;
}

function mycopy($srcdir, $desdir='', $srcfile, $desfile='') {
  if ($srcfile==='') return;
  if ($desdir==='') $desdir = $srcdir;
  if ($desfile==='') $desfile = $srcfile;
  $src = $srcdir . $srcfile;
  if (!file_exists($src)) return;
  copy($src, $desdir . $desfile);
}

function check_cookie() {
  // to check if cookies are enabled you must set a cookie and then reload the page see if
  // the cookie was set properly.  this is because the cookie won't show immediately.
  // there are way to check if cookies are enabled without a reload, or a full reload but i
  // will leave this for now.
  // in this project i don't really need to check if cookies are enabled
}

/* function makemainmenu($topmenu,$submenu=[]) {
// function makemainmenu() {
  // called from index.php; it returns a string, html code, which shows the top menu with its 
  // submenu items from the php $topmenu and $submenu arrays
  $s = '<ul id="theMenu" class="links">';
  $n = 0;
  while (true) {
    if (!isset($topmenu[$n])) break;
    $ss =  preg_replace('/[ -\/]/', '', $topmenu[$n]);
    $v = $n*100;
    if (!isset($submenu[$n][1])) {    // no dropdown submenus
      $s .= '<li class="menu" data-func="mnu' . $n . '_' . $ss . '" data-v="' . $v . '">' . $topmenu[$n] . '</li>';
    } else {    // top menu which has dropdown submenus
      $s .= '<li class="menu" data-func="mnu' . $n . '_0' . $ss . '" data-v="' . $v . '">';
      $s .= $topmenu[$n].' &#9660;';   // 9660 is the down arrow, \u25bc in js
      $s .= '<ul class="ul_' . $n . ' nodisplay">';
      $m = 1;
      foreach ($submenu[$n] as $func) {
        $ss =  preg_replace('/[ -\/]/', '', $func);
        $v = $n*100 + $m;
        $s .= '<li class="submenu" data-func="mnu' . $n . '_' . $m . $ss . '" data-v="' . $v . '">' . $func . '</li>';
        $m++;
      }
      $s .= '</ul>';
      $s .= '</li>';
    }
    $n++;
  }
  $s .= '</ul>';
  return $s; 
}
 */
 

 
 
 
 

?>
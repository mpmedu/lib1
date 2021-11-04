<!--
  This module displays a list of input boxes for entering a username and passwords.
  If $arg1 === 0 is for login, a username and only 1 password must be entered, password type input
  If $arg1 === 1 a username and 2 passwords must be entered, text type input
  If $arg1 === 2 a username and 2 passwords must be entered, password type input
-->

<?php
  if (!isset($arg1)) $arg1 = 0;
?>  

<ul class="ul_style">

  <li>
  <label>Username:</label>
  </li>
  <li>
  <input type="<?php echo ($arg1 === 1)? 'text' : 'password'; ?>"  class="textinput" autofocus>
  </li>
  <li>
  <br/>
  </li>

  <li>
  <label>Password:</label>
  </li>
  <li>
  <input type="<?php echo ($arg1 === 1)? 'text' : 'password'; ?>"  class="textinput">
  </li>
  <li>
  <br/>
  </li>
  
  <?php
  if ($arg1 > 0) {
  ?>
  <li>
  <label>Master password:</label>
  </li>
  <li>
  <input type="<?php echo ($arg1 === 1)? 'text' : 'password'; ?>"  class="textinput">
  </li>
  <li>
  <br/>
  </li>
  <?php
  }
  ?>

</ul>
    
    
    
    

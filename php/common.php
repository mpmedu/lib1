 
  
  
   <!--
   ********************************************
  <div> for mask for login and getting the pw at startup
   ********************************************
  -->
  <div id="pw_and_login_mask" class="nodisplay"></div>

  
   <!--
   ********************************************
  <div>s for showing spinner and mask when Ajax is called
   ********************************************
<div id='loading' class="nodisplay box" style='background-color:white;width:200px;'>
  -->
<div id='loading' class="nodisplay box">
  <div id='loadmsg' style='margin-bottom:6px'>Loading, please wait</div>
 <!-- <img src="../../lib/icons/spinner.gif">  when running common  -->
  <img src="../../../lib/icons/spinner.gif">    <!--  when running categories  -->
</div>
<div id='loadingmask' class="nodisplay"></div>
  

  <!--
   ********************************************
  <div>s for showing image in lightbox and their mask
   ********************************************
  -->
  <div id="dialog" class="nodisplay">
    <img id="d_image" />
    <div id="d_ps">
    </div>
  </div>
  
  <div id="tabs_dialog" class="nodisplay">
  </div>
  
  <!-- mask2 is for dialog and tabs_dialog divs  -->
  <div id="mask2" class="nodisplay"></div>


 <!--
   ********************************************
  <div>s for showing message in dialog box, either with OK or with Yes No 
  and the background mask for this dialog
   ********************************************
  -->
   
  <div id="msgbox" class="nodisplay mbox">
    <br>
    <p class="bmessage"></p>
    <br><br>
    <p id='msgButton' class='nodisplay'> <button>OK</button> </p>
    <p id='yesnoButtons' class='nodisplay'>
      <button style='margin-right:10px;'>Yes</button>
      <button>No</button>
    </p>
  </div>
    
  <div id="msgboxmask" class="nodisplay"></div>
  

'use strict';

window.xx.module('ezfhcode', function (exports) {
  var xx = window.xx;
  var vars = window.xx.vars;
  var common;
  xx.imports.push(function () {
    common = xx.common;
    common.addhcode(hcode);
  });

  exports.extend({
    //part_login: part_login,
    getPwsAtStart_id: getPwsAtStart_id,
    login_id: login_id,
    tem_change_pw2: tem_change_pw2,
    tem_change_pw3: tem_change_pw3,
    tem_set_auto_login2:tem_set_auto_login2,
    tem_pop_menu:tem_pop_menu
  });
   

var hcode = {
  
tem_startup :  // the content of wrapper at start up
  `<div id='fixed_sidebar'>
    <div id='col1'></div>
  </div>
  <div id='content'>
    <div id='col2'>
      <div id='startup'>
        <h2>List your business or place an advert for free</h2>
        <b>
        <p id='welcome'>Welcome!  This site has a categorised directory of businesses in {loc}.  
        It also has a section for classified adverts. </p>
        <br>
        <p>To list your business or place a classified advert click on the appropriate button in the top menu and enter 
        your particulars. You will receive an email within the next day or two. Please respond to it to confirm your application.
        </p>
        </b>
        <br>
      </div>
    </div>
    <div id='col3'></div>
    <div style='clear:both;'></div>
  </div>`,
  
tem_add_business :
  `<div id='addABusiness' class='boxx'>
    <h2>Add a business</h2>
    <ul class='ul_style'>
      <li>Select the category from the category list on left:</li>
      <li><input type='text' id='addBorCinput' class='textinput' readonly='true'></li>
    <div id='buscontact' class='forminfo'>
      <h3 style='margin:12px 0 8px 0;'>Contact information</h3>
      <li>Name of contact person:</li>
      <li><input type='text' id='bcname' name='bcname' value='{bcname}' autofocus class='textinput'></li>
      <li>email:</li>
      <li><input type='text' id='bcemail' name='bcemail' value='{bcemail}' class='textinput'></li>
      <li>Telephone:</li>
      <li><input type='text' id='bctel' name='bctel' value='{bctel}' class='textinput'></li>
    </div>
    <div id='businfo' class='forminfo'>
      <h3 style='margin:12px 0 8px 0;'>Business information to appear in list</h3>
      <li>Business name:</li>
      <li><input type='text' id='bname' name='bname' value='{bname}' class='textinput'></li>
      <li>Description:</li>
      <li><textarea id='bdes' name='bdes' rows='1' class='textinput'>{bdes}</textarea></li>
      <li>Tel/Address:</li>
      <li><input type='text' id='bcontact' name='bcontact' value='{bcontact}' class='textinput'></li>
      <li>
        <div style='display:inline-block; vertical-align:top;width:50%;overflow:hidden;'>
          Business logo: 
          <br>
          <input type='file' id='photo' name='photo' style='width:auto'>
        </div>
        <div style='display:inline-block;'>
          <img id ='showthumb' class='nodisplay'>
        </div>
      </li>
    </div>
    </ul>
    <br><br>
    <div id='add_butt' class='okButton'>
    <input type='submit' value='OK' name='submit' style='width:auto'>
    </div>
  </div>`,
  
tem_add_classified :
  `<div id='addAClassified' class='boxx'>
    <h2>Place a classified advert</h2>
    <ul class='ul_style'>
      <li>Select the category from the category list on left:</li>
      <li><input type='text' id='addBorCinput' class='textinput' readonly='true'></li>
    <div id='clfcontact' class='forminfo'>
      <h3 style='margin:12px 0 8px 0;'>Contact information</h3>
      <li>Name of contact person:</li>
      <li><input type='text' id='bcname' name='bcname' value='{bcname}' autofocus class='textinput'></li>
      <li>email:</li>
      <li><input type='text' id='bcemail' name='bcemail' value='{bcemail}' class='textinput'></li>
      <li>Telephone:</li>
      <li><input type='text' id='bctel' name='bctel' value='{bctel}' class='textinput'></li>
    </div>
    <div id='clfinfo' class='forminfo'>
      <h3 style='margin:12px 0 8px 0;'>Information to appear in the advert</h3>
      <li>Heading:</li>
      <li><input type='text' id='bname' name='bname' value='{bname}' class='textinput'></li>
      <li>Description:</li>
      <li><textarea id='bdes' name='bdes' rows='1' class='textinput'>{bdes}</textarea></li>
      <li>Tel/Address:</li>
      <li><input type='text' id='bcontact' name='bcontact' value='{bcontact}' class='textinput'></li>
      <li style='margin-top:8px;'>Picture: <input type='file' id='photo' name='photo' style='vertical-align:top; width:auto'></li>
      <img id ='showthumb' class='nodisplay'>
    </div>
    </ul>
    <br><br>
    <div id='add_butt' class='okButton'>
    <input type='submit' value='OK' name='submit' style='width:auto'>
    </div>
  </div>`,
  
tem_add_edit_categories :
  `<div id='addEditCategories' class='boxx nodisplay'>
    <h2  class='h2_m'>Add/Edit categories</h2>
    <ul class='ul_style'>
      <li><b>Choose an action:</b></li>
      <li id='actionRadio'>
      <input type='radio' name='action' value='add'>Add&nbsp; &nbsp;
      <input type='radio' name='action' value='edit'>Edit&nbsp; &nbsp;
      <input type='radio' name='action' value='move'>Move&nbsp; &nbsp;
      <input type='radio' name='action' value='remove'>Remove
      </li>
      <li><br/></li>
      <li>
      <div class='addCat nodisplay'>
      1. Enter the name of the new category.<br/>
      2. Select the target category where the new category must be inserted.<br/>
      3. Choose where the new category must be inserted relative to the target category.<br/>
      4. Click on the OK button.<br/>
      </div>
      <div class='editCat nodisplay'>
      1. Select the category.<br/>
      2. Enter the new name for the selected category.<br/>
      3. Click on the OK button.<br/>
      </div>
      <div class='moveCat nodisplay'>
      <i>Note that the input box must be active in order to receive the category. You can click on it to make it active.</i><br/>
      1. Select the category that you want to move.<br/>
      2. Click on the target category where the selected category must be moved to.<br/>
      3. Choose where the selected category must be inserted relative to the target category.<br/>
      4. Click on the OK button.<br/>
      </div>
      <div class='removeCat nodisplay'> 
      1. Select the category to be removed.<br/>
      <i><b>Warning!!</b> All of the subcategories in the selected category will also be removed.</i><br/>
      2. Click on the OK button.<br/>
      </div>
      
      </li>
      <li><br/></li>
      
      <div id='aemr_add' class='addCat nodisplay'>
      <li><b>New Category Name:</b></li>
      <li><input type='text' id='addInput_name' class='textinput' autofocus></li>
      <li><br/></li>
      <li><b>Target category:</b>
      <li><input type='text' id='addInput' class='textinput' readonly='true' /></li>
      <li><br/></li>
      <li><b>Insert the new category relative to the target category:</b></li>
      <li>
      <input type='radio' name='add_action' value='before'>Before&nbsp; &nbsp;
      <input type='radio' name='add_action' value='after'>After&nbsp; &nbsp;
      <input type='radio' name='add_action' value='assub' checked>As a subcategory&nbsp; &nbsp;
      </li>
      </div>
      
      <div id='aemr_edit' class='editCat nodisplay'>
      <li><b>Selected category:</b></li>
      <li><input type='text' id='editInput' class='textinput' readonly='true' /></li>
      <li><br/></li>
      <li><b>New Name:</b></li>
      <li><input type='text' id='editInput_name' class='textinput' autofocus></li>
      </div>
      
      <div id='aemr_move' class='moveCat nodisplay'>
      <li><b>Selected category to be moved:</b></li>
      <li><input type='text' id='moveInput0' data-n='0' class='textinput' readonly='true'></li>
      <li><br></li>
      <li><b>Target category:</b></li>
      <li><input type='text' id='moveInput1' data-n='1' class='textinput' readonly='true'></li>
      
      <li><br></li>
      <li><b>Insert the category relative to the target category:</b></li>
      <li class='radio_input'>
      <input type='radio' name='move_action' value='before'>Before&nbsp; &nbsp;
      <input type='radio' name='move_action' value='after'>After&nbsp; &nbsp;
      <input type='radio' name='move_action' value='assub' checked>As a subcategory&nbsp; &nbsp;
      </li>
      </div>
     
      <div id='aemr_remove' class='removeCat nodisplay'>
      <li><b>Selected category to be removed:</b></li>
      <li><input type='text' id='removeInput' class='textinput' readonly='true'></li>
      </div>
      
      <li><br/></li>
    </ul>
    <div id='categoriesButton' class='okButton'>
    <button>OK</button>
    </div>
  </div>`,
  
tem_show_pre_lists :
  `<div id='prelist'>
    <div style='padding:1em'>
      <p>This is a list of {borc} to be added. It shows if an email was successfully sent to the applicant and
       whether there was a confirmation to the email.</p>
      <br> 
      <p>Select items to be added/deleted by clicking the check boxes... then click on a button in the 
          <b>Select \u25bc</b> dropdown box.
      </p>
    </div>
  </div>`,
  
tem_show_edit_buttons_b :
  `<br>
  <div id='edit_buttons'>
  <p style='text-align:center;'>
  <button id='upgradeselected'>Upgrade selected</button>
  </p>
  <p style='text-align:center;'>
  <button id='downgradeselected'>Downgrade selected</button>
  </p>
  <p style='text-align:center;'>
  <button id='removeselected'>Remove selected</button>
  </p>
  </div>`,

tem_show_edit_buttons_c :
  `<br>
  <div id='edit_buttons'>
  <p style='text-align:center;'>
  <button id='removeselected'>Remove selected</button>
  </p>
  </div>`,
  
tem_dialog :
  `<p><b>{type}</b> - {dt}</p>
  <p><b>{head}</b></p>
  <p>{des}</p>
  <p>{contact}</p>`,

tem_dialog_xtra :
  `<p><b>{type}</b> - {dt}</p>
  <p><b>{head}</b></p>
  <p>{des}</p>
  <p>{contact}</p>
  <p><b>Submitted by:</b></p>
  <p>{name}</p>
  <p>{email}</p>
  <p>{tel}</p>`,

tem_edit_entry_tobefixed :
  `<div class='column' id='col1'>
  <h2 style='text-align:center'>
  Add/edit pictures</h2>
  <div style='padding:1em'>
  <p>Browse and choose a picture file for an advert which will be scaled to a width of 200 pixels.</p>
  <p>You can also have up to 4 pictures which will display in a box when someone clicks 
  on your business.
  </p>
  <p>The original size of an image must be at least 200 x 200 pixels. Large images will be scaled to a width and height of 700 x 600 pixels.
  </p>
  <p>When you have made your selection click on the button below to save them.
  </p>
  <br>
  <p style='text-align:center;'>
  <button id='savepictures'>Save</button>
  </p>
  <p style='text-align:center;'>
  <button id='exitpictures'>Exit</button>
  </p>
  </div>
  </div>
  <div class='column' id='col2'>
  <div id='aeb' class='form'>
  <h2  class='h2_m'>Add/Edit pictures for <span style='color:navy;'>{bname}</span></h2>
  <ul class='ul_style'>
    <li>
    <label>Picture for ad box:</label>
    </li>
    <li id='lip0'>
    <div style='display: inline-block;float:left;'>
    <input type='text' class='txt_cls nodisplay' />
    <input type='file' id='ip0' /><br>
    <label for 'cbp0' style='display: inline;'>Remove existing</label>    
    <input type='checkbox' id='cbp0' class='cb_cls'/>
    </div>
    <!--
    <input type='text' id='it0' /><br>
    <img src='' style='width:50px; height:80px;display:inline-block;float:right;margin-right:5%;' />
    <img src='' style='height:70px;display:inline-block;float:right;margin-right:4%;' />
    <img src='' style='max-height:70px;display:inline-block;float:right;margin-right:4%;' />
    -->
    <img src='' style='height:70px;display:inline-block;float:right;margin-right:4%;' />
    </li>
    <li>
    <br>
    </li>
    {p1}{p2}{p3}{p4}
  </ul>
  </div>
  </div>
  <div class='column' id='col3'>
  </div>`,

tem_edit_entry_xtra :
  `<li>
  <label>Tab {num}:</label>
  </li>
  <li id='lip{num}'>
  <div style='display: inline;float:left;'>
  <input type='text' id='it{num}' class='txt_cls' /><span>12 chars max</span><br>
  <input type='file' id='ip{num}' /><br>
  <label for 'cbp{num}' style='display: inline;'>Remove existing</label>    
  <input type='checkbox' id='cbp{num}' class='cb_cls'/>
  </div>
    <!--
  <img src='' style='width:70px; height:70px;display:inline-block;float:right;margin-right:5%;' />
  <img src='' style='max-height:70px;display:inline-block;float:right;margin-right:4%;' />
    -->
  
    <img src='' style='height:70px;display:inline-block;float:right;margin-right:4%;' />
  </li>
  <li>
  <br>
  </li>`,

tem_business_pictures :
  `<div id='amenu' class='bmenu'>
    {list}
    <div id='tabs_bname' style='float:right;font-size:1.5em;color:white;padding-left:0em;padding-top:0.2em;overflow:hidden;'>{bname}</div>
   </div> 
  <div id='description' class='content'>
  {imgs}
  </div>`,

tem_start_extras :
  `<div class='column' id='col1'>
  </div>
  <div class='column' id='col2'>
    <br><br><br><br><br><br><br><br><br>
    <p style='text-align:center;'>
    <button id='reload'>Reload</button>
    </p>
  </div>
  <div class='column' id='col3'>
  </div>`,
  
getPwsAtStart_id :   
  `<div id='getPwsAtStart_id' class='nodisplay bbox startbox'>
  <p><b>New Site - Enter a username and passwords for the website</b>
  </p>
  <p>
  After entering a username and passwords a new site will be created and you will be automatically 
  logged in as Administrator. The first thing that you should do is add categories so that businesses 
  can apply to be listed. Anyone can apply for an advert to be listed in classifieds.
  </p>
  <p>
  The site can be viewed by anyone but to make changes you must be logged in. To log in you must press Alt-L 
  and then enter the username and password in the pop-up box. If accepted 'Admin' will appear as the last 
  item in the top menu - this means that you are logged in as Administrator.   
  </p>
  <p>
  Anyone who knows the username and one of the passwords can make additions to the site.  It is 
  recommended that you keep the master password to yourself so that you can change the username and 
  passwords if the need arises.    
  </p>
  {v1}
  <p style='text-align:center;'>
  <button>Save</button>
  <button>Cancel</button>
  </p>
  </div>`,
  
login_id:
  `<div id='login_id' class='nodisplay box'>
    <h2 style='max-width:400px;'>Please login</h2>
    {v1}
    <p style='text-align:center;'>
    <button>Login</button>
    <button>Cancel</button>
    </p>
  </div>`,
  
// for col1  
tem_change_pw1 :
  `<div style='padding:1em'>
  <p>You must first enter the current username and passwords.</p>
  <br>
  <p>If they are verified to be correct then a box will open allowing you to enter a 
  new username and passwords.
  </p>
  </div>`,
  
// for col2  
tem_change_pw2 :
  `<div id='pwcheck_id' class='boxx'>
  <h3 style='text-align:center;'>Enter the current username and passwords</h3>
  {v1}
  <p style='text-align:center;'>
  <button>OK</button>
  </p>
  </div>`,
 
// for col2  
tem_change_pw3 : 
  `<div id='tem_change_pw3' class='bbox newpwbox'>
    <h3 style='text-align:center;'>Enter new username and passwords</h3>
    {v1}
    <p style='text-align:center;'>
    <button>Save</button>
    <button>Cancel</button>
    </p>
  </div>`, 

tem_set_auto_login1 :   // for column1
  `<div style='padding:1em'>
  <p>You must first enter the current username and password.</p>
  <br>
  <p>If correct then a box will appear on the right. By selecting the appropriate option in the box you 
  can choose to login automatically on this computer when pressing Alt-L.</p>
  <br>
  <p>Note that this requires cookies to be enabled.</p>
  </div>`,
  
// for col2  
tem_set_auto_login2 :
  `<div id='pwcheck_id2' class='boxx'>
  <h3 style='text-align:center;'>Enter the current username and password</h3>
  {v1}
  <p style='text-align:center;'>
  <button>OK</button>
  </p>
  </div>`,
  
tem_set_auto_login3 :  // for column2
  `<div id='auto_login_id' class='boxx' style='position:relative'>
  <h3 style='text-align:center;'>Set Auto login</h3>
  <p><b>Auto login</b> - if Auto login is set to On then pressing Alt-L will log you in without entering a password</p>
  <br>
  <ul class='ul_style'>
    <li><input type='radio' name='autologin' value='off'> Off</li>
    <li><input type='radio' name='autologin' value='on'> On</li>
  </ul>
  <br>
  <p style='text-align:center;'>
  <button>OK</button>
  </p>
  <br>
  </div>`,
  
tem_pre_entry :
  `<div id='pe_{v1}' class='listcell' >
    <div class='pe_left' style='display:inline-block;width:75%;vertical-align:top;
      height:100%;margin-left:10px;border-right:1px solid black;'>
      <img src = '{v4}' style='float:right;margin:10px;' class='{v4a}'>
      <h3 style='margin:5px 0;'>{v2}</h3>
      <p class='pe_p'>{v3}</p>
      <p class='pe_p'>{v5}</p>
    </div>
    <div class='pe_right' style='display:inline-block;width:auto;position:absolute;right:0;margin:0 10px;
     max-width:200px;'>
      <p>Category: <b>{v6}</b></p>
      <p class='pe_p'>Applied: {v7}</p>
      <p class='pe_p'>
      email sent {v8} &nbsp;&nbsp; email confirmed {v9}
      </p>
      <div style='position:absolute; left:75px; top:51px;'>
        <input type='checkbox'  id='pid{v10}' class='cb_cls'/>
      </div>
    </div>
  </div>`,
  
tem_no_pres :
  `<br><br>
  <h3 style='text-align:center;' >
    There are no new {v1}
  </h3>`,
  
tem_entry :
  `<div id='pe_{v1}' class='listcell{v0}' >
    <div class='pe_left' style='display:inline-block;width:99%;vertical-align:top;
      height:100%;margin-left:10px;'>
      <img src = '{v4}' style='float:right;margin:10px;' class='{v4a}'>
      <h3 style='margin:5px 0;'>{v2}</h3>
      <p class='pe_p'>{v3}</p>
      <p class='pe_p'>{v5}</p>
    </div>
  </div>`,
  
tem_edit_entry :
  `<div id='pe_{v1}' class='listcell{v0}' >
    <div class='pe_left' style='display:inline-block;width:75%;vertical-align:top;
      height:100%;margin-left:10px;border-right:1px solid black;'>
      <img src = '{v4}' style='float:right;margin:10px;' class='{v4a}'>
      <h3 style='margin:5px 0;'>{v2}</h3>
      <p class='pe_p'>{v3}</p>
      <p class='pe_p'>{v5}</p>
    </div>
    <div class='pe_right' style='display:inline-block;width:auto;position:absolute;right:0;margin:0 10px;
     max-width:200px;'>
      <br>
      <p class='pe_p'>Applied: {v7}</p>
      <div style='position:absolute; left:65px; top:40px;'>
        <input type='checkbox'  id='pid{v10}' class='cb_cls'/>
      </div>
    </div>
  </div>`,
  
tem_no_entries :
  `<br><br>
  <h3 style='text-align:center;'>
    There are no entries in this category
  </h3>`,
  
tem_pop_menu :
  `<div id='pop_menu' style='position:absolute;z-index:100;right:1.4%;top:60px;' data-v='{v0}'>
    <b style='display:block;'>
      Select \u25bc
    </b>
    <div id='pop_buttons' class='nodisplay' style='padding-top:8px;' data-v='{v0}'>
      {v1}
    </div>
  </div>`
  
};
  
  
function part_login(arg1) {
/*   
  This function returns html code for displaying a list of input boxes for entering a username and passwords.
  If $arg1 === 0 is for login, a username and only 1 password must be entered, password type input
  If $arg1 === 1 a username and 2 passwords must be entered, text type input
  If $arg1 === 2 a username and 2 passwords must be entered, password type input
 */
  let sss = `<li> {v1} <input type='{v2}'  class='textinput' {v3}> </li>
            <li> <br> </li>`;
   let ss = (arg1 === 1)? 'text' : 'password';
   let s = `<ul class='ul_style' style='text-align:left;'>`;
   s += common.replacer(sss,{'v1':'Username','v2':ss,'v3':'autofocus'});
   s += common.replacer(sss,{'v1':'Password','v2':ss,'v3':''});
   if (arg1 > 0) s += common.replacer(sss,{'v1':'Master password','v2':ss,'v3':''});
   s += '</ul>';
   return s;
}

function getPwsAtStart_id() {
  // gets the html for showing the box when the program is run for the first time
  return common.replacer(vars.hcode.getPwsAtStart_id,{'v1':part_login(1)});
}

function login_id() {
  // gets the html for showing the box to allow you to login by pressing Alt-L
  return common.replacer(vars.hcode.login_id,{'v1':part_login(0)});
}

function tem_change_pw2() {
  return common.replacer(vars.hcode.tem_change_pw2,{'v1':part_login(1)});
}

function tem_change_pw3() {
  return common.replacer(vars.hcode.tem_change_pw3,{'v1':part_login(1)});
}

function tem_set_auto_login2() {
  return common.replacer(vars.hcode.tem_set_auto_login2,{'v1':part_login(0)});
}

function tem_pop_menu(arr,v0) {
  if (arr === undefined) return null;
  let n = arr.length;
  if (n === 0) return null;
  let s='';
  for (let i = 0; i < n; i++){
    s +=  '<button>' + arr[i] + '</button><br>';
  }
  return common.replacer(vars.hcode.tem_pop_menu,{'v0':v0,'v1':s});
}

});

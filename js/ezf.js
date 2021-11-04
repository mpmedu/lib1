"use strict";

window.xx.module('ezf', function (exports) {
  var xx = window.xx;
  var vars = window.xx.vars;
  
  var common,menu,categories,ezfhcode,changecolor;
  xx.imports.push(function () {
    common = xx.common;
    menu = xx.menu;
    categories = xx.categories;
    ezfhcode = xx.ezfhcode;
    changecolor = xx.changecolor;
  });

  exports.extend({
    init:init,
    showDialog:showDialog,
    showTabs:showTabs,
    getadminlevel:getadminlevel,
    getlogincookie:getlogincookie,
    clearMenu:clearMenu,
    showrhspic:showrhspic
  });

vars.what = '';
   
// files gets saved when a user selects a file
var files;

var bf;   // saves filename of picture

var afarr;   // holds existing file names
var bfarr;   // holds new file names

// for checking when a business enters a code after right clicking
var checkkey = false;
var bactive;   // the business id as in tbl_business
var starttime;
var codeentered;


$(function() {
  // action when document has finished loading
  //alert("ready1");
  //common.wait2(5000);
  
  // var arr = ['www.testu.biz.ht','http://abc.com','go_ogle.com','www.ab-c.co.za'];
  // arr.forEach(function(url) {
    // console.log(url + '  ');
    // if (common.isValidURL(url)) {
      // console.log('is valid');
    // } else {
      // console.log('NOT valid');
    // }
  // });
  
  changecolor.clearInter();
});

$(document).ready(function () {
  //alert("ready2");
  //alert(get_ext('abc.txt'));
  
  // show startup message
  $('#wrapper').html(xx.common.getHtmlCode('tem_startup',{'loc':xx.vars.loc}));
  // read the categories from file into arrays
  $.when(categories.writeOneCatArrays('catlist','List of Categories'),deleteoldfiles())
    .then(()=> {
      for (let i = 0; i < vars.errs.length; i++) {
        if (vars.errs[i].todo === 'makejscats') {
          common.myStop("The function for creating categories failed. This is a fatal error which means that the program cannot continue.");
        }
      }
      clearErrorArray();
    });
  init();
  xx.menu.checkWidth();
  xx.menu.checkHeight();
});

function clearErrorArray() {
  vars.errs = [];
}

document.onkeydown = testKeyCode;  

 
$('#wrapper').on('click', '#reload', function evt_reload(ee){ 
  ee.stopPropagation();
  ee.preventDefault();
  window.location = vars.URL_base + "/index.php";
});
  
function deleteoldfiles() {
  var dfd = $.Deferred();
  var data = {'t':new Date().getTime(),'baseDir':vars.baseDir};
  common.doAjax('general.php','deleteoldtempimages', data,'',null,null,()=>{dfd.resolve();});
  return dfd.promise();
}

function init() {
  // testing
  // let a = $('div');
  // testing over
  
  common.makemenu();
  menu.init();
  initLoginHtmlCode();
}

function initLoginHtmlCode() {
  $('body').append(ezfhcode.login_id());
}
 
function showbox(s) {
  //checkWidth(); 
  //equalHeight();
  common.fixElementAndMask(s,'pw_and_login_mask',true);
  let $ele = $('#' + s + ' input');
  $ele.val('');
  $ele[0].focus();
}


  //$stmt->bind_result($id,$bname,$bdes,$bcontact,$bcname,$bcemail,$bctel,$blogo,$cat_id,$dt,$hi_id);

function showDialog(pob,pid,showcontact) {
  common.loadingOn();
  let dt = new Date(pob.dt[pid]*1000).toDateString();
  var fname = vars.img_path + pob.blogo[pid];
  var $img = $('#d_image');
  var w;
  if (!fname || fname === '') {
    $img.addClass('nodisplay');
    w = 500;
  } else {
    var s = fname.substring( fname.lastIndexOf("_") + 1);
    var p1 = s.indexOf("x");
    var p2 = s.indexOf(".", p1);
    w = Number(s.substring(0, p1));
    var h = Number(s.substring(p1 + 1, p2));
    if ($('body').width() < w + 48) {
      $('body').width(w + 48);
    }
    $img.attr('src',fname).attr('width',w).attr('height',h).removeClass('nodisplay');
    if (w < 500) {
      w = 500;
      $img.removeClass().addClass('thin');
    } else {
      $img.removeClass().addClass('fat');
      w = w + 20;
    }
  }
  var localMob = common.getMob(),ss,tem;
  var smActive = localMob.submenuActive;
  if (smActive === -1 || smActive > 4) {
    ss = "added: ";
  } else {
    ss = "submitted: ";
  }
  var ob = {'dt':"Date " + ss + dt, 'head':pob.bname[pid],'des':pob.bdes[pid],'contact':pob.bcontact[pid]};
  ob['type'] = (localMob.menuActive === 0 || smActive === 3 || smActive === 5)? 'Business' : 'Classified Ad';
  if (showcontact) {    // must show contact details
    tem = 'tem_dialog_xtra';
    ob['name'] = pob.bcname[pid];
    ob['email'] = pob.bcemail[pid];
    ob['tel'] = pob.bctel[pid];
  } else {
    tem = 'tem_dialog';
  }
  $('div#d_ps').css('width', w).html(common.getHtmlCode(tem,ob));
  common.fixElementAndMask('dialog','mask2',true);   // this shows the dialog box
  common.loadingOff();
 }
 
function showbusentry(code){
// this function calls doentry.php with todo=getbusinfo and then displays
// the info returned in a form so that the user can edit and save changes
  afarr = new Array(5);
  bfarr = new Array(5);
  var data = {'bactive':bactive, 'copy':'1', 'tt':new Date().getTime()};
  // if a code was supplied then add it to data ob so it can be checked
  if (code) data['code'] = code;
  common.doAjax('doentry.php','getbusinfo',data,'', function(json) {
    if(json.value === 'success') {
      var bname = json.bname;
      var s = new Array();
      s[0] = common.getHtmlCode('tem_edit_entry_xtra');
      for (var i = 1; i < 5; i++) {
        s[i] = common.replacer(s[0],{num:i});
      }
      $('#wrapper').html(common.getHtmlCode('tem_edit_entry',{'bname':bname,'p1':s[1],'p2':s[2],'p3':s[3],'p4':s[4]}));
      $('#wrapper #col2 img').each(function(index) {
        if (json.bfarr[index]){
          $(this).attr('src', vars.tmp_path + json.bfarr[index]);
          afarr[index] = json.bfarr[index];
          if (json.title[index]) $('#wrapper #col2 input:text')[index].value = json.title[index];
        }
      });
      //menu.equalHeight();
    }
  });
}

 function showTabs(bid) {
// this function calls doentry.php with todo=getbusinfo and then displays
// the info returned in a tabs dialog
  common.doAjax('doentry','getbusinfo',{'bactive':bid,},'', function(json) {
    if(json.value === 'success') {
      var bname = json.bname;
      var s1 = '', s2='',s,cls;
      var n = 0,odd=true;
      for (var i = 1;i < 5;i++) {
        if (json.bfarr[i]){
          n++;
          //cls = (odd)? 'class="odd"':'class="even"';
          cls = 'class="even"';
          //s = (json.title[i] === '')? "&nbsp;":json.title[i];
          s = (json.title[i] === '')? n:json.title[i];
          s1 += '<li ' + cls + '>' + s + '</li>';
          s2 += '<img src="' + vars.img_path + json.bfarr[i] + '" class="nodisplay tab_img" />';
          odd = !odd;
        }
      }
      if (n === 0) {
        if (vars.debugon) common.showMessage('<b>' + bname + '</b><br>' + 'no pictures','#ddd');
      } else {
        $('#tabs_dialog').html(common.getHtmlCode('tem_business_pictures',{bname:bname,list:s1,imgs:s2}));
        common.fixElementAndMask('tabs_dialog','mask2',true);   // this shows the tabs_dialog box
        $('#tabs_dialog').css({'display':'inline','top':'50px'});
        var w = 0;
        var $v = $('#tabs_dialog li');
        $v.each(function(i) {
          w += $(this).outerWidth();
        });
        $('#tabs_dialog #tabs_bname').width(710-w);
        $('#tabs_dialog #amenu > li:first-child').trigger('click');
        
        //equalHeight();
      }
    }
  });
}

/////////////////////////////////////////////////////////////////////////////

// *******************************************
// 
// *******************************************

function prepare4checkpic(ee,data,params,$ele) {
// function prepare4checkpic(ee,data,$ele) {
  var ext = common.get_ext($ele);
  if (!((ext == 'gif') || (ext == 'jpeg') || (ext == 'jpg') || (ext == 'png'))) {
    alert('File extension must be for an image of type jpg, gif or png');
    $ele.val('');
    files = undefined;
    return false;
  } 
	files = ee.target.files;
  // check the file chosen
  var ftype = files[0].type;
  if (!((ftype == 'image/gif') || (ftype == 'image/jpeg') || (ftype == 'image/pjpeg') || (ftype == 'image/png'))) {
    alert('File must be an image of type jpg, gif or png');
    $ele.val('');
    files = undefined;
    return false;
  } 
  var fsize = files[0].size;
  if (fsize > xx.constants.MAX_FILE_SIZE) {
    alert('File size must not be greater than ' + xx.constants.MAX_FILE_SIZE / 1000000 + ' MB');
    $ele.val('');
    files = undefined;
    return false;
  }
  var uid = new Date().getTime();
  bf = 'b' + uid + '.' + ext;
  $.each(files, function(key, value){
    data.append(key, value);
  });
  params.bf = bf;
  return true;
}

 // ******************************************
 // event handler for adding a business or classified advert to the pre table
 // ******************************************
    
$('#wrapper').on('click', '#add_butt', function __clickToAddBusinessClassified(e){ 
  e.stopPropagation();
  e.preventDefault();
  let cat_selected = categories.getCob().cat_selected;
  if (!cat_selected || (0 === cat_selected)) {
    common.showMessage('You must select a category','b');
    return;
  }
  let $container = $(this).parent().parent();
  let what = $(this).parent().parent()[0].id;
  var $bi_inp = $container.find('.textinput:not([readonly])');
  var bi_inp = new Array(), i;
  for (i = 0; i < 6; i++) {
    bi_inp[i] = common.trim($bi_inp[i].value);
    if (bi_inp[i] === '') break;
    if (i === 1) {
      // test that this is a valid email address
      var em = common.trim(bi_inp[1]);
      if (!common.validateEmail(em)) {
        common.showMessage('Error with email address','b');
        return;
      }
      if (!common.isEmail(em)) {
        common.showMessage('Error with email address','b');
        return;
      }
    } else if (badinput(bi_inp[i])) break;
  }
  if (i < 6) {
    common.showMessage('No or bad input detected - please enter all input boxes with valid input','b');
    return;
  }
  var data = {
    'baseDir':vars.baseDir,
    'URL_base':vars.URL_base,
    'isLocal': (vars.isLocal)? '1' : '0',
    'what':vars.what, 
    'cat_id':cat_selected,
    'bcname':bi_inp[0], 
    'bcemail':bi_inp[1], 
    'bctel':bi_inp[2],
    'bname':bi_inp[3], 
    'bdes':bi_inp[4],
    'bcontact':bi_inp[5] 
  };
  var files_set = 0;
  if (files) {
    files_set = 1;
    data['bf'] = bf;
  } 
  data['files_set'] = files_set;
  common.doAjax('dopre.php','addtopre',data,'Processing...', function(json) {
    let jsdate = new Date();
    let ms = jsdate.getTime();
    
    $('#col2 input:not(#addBorCinput, [type="submit"])').val('');
    document.getElementById('bdes').value = "";
    $('#showthumb').removeClass('thumbon');
    //menu.equalHeight();
    $(window).scrollTop(0);
    
    files = undefined;
  });
});

 // ******************************************
 // event handler for adding a photo logo to a business or classified advert
 // ******************************************

$('#wrapper').on('change', '#photo', function(ee){ 
// when a user selects a file
  $('#showthumb').removeClass('thumbon');   // if thumb is showing, remove it
  var $this = $(this);
  var data=new FormData(),params = {};
  if (!prepare4checkpic(ee,data,params,$this)) return;
  if ($(this).parent().parent().parent()[0].id === 'businfo') {
    params['minw'] = 60; params['minh'] = 40; 
    params['maxw'] = 90; params['maxh'] = 60;
  } else {
    params['minw'] = 150; params['minh'] = 150; 
    params['maxw'] = -500; params['maxh'] = -400;
  }
  params['baseDir'] = vars.baseDir;
  common.doAjaxFormData('general.php','checkpic',params,data,'', function(json) {
    if(json.value === 'success') {
      bf = json.bf;
      $('#showthumb').attr('src','temp_images/' + bf);
      $('#showthumb').addClass('thumbon');
      //checkWidth(); 
      //menu.equalHeight();
    } else {
      if (json.errmsg != undefined) {
        alert(json.errmsg);
      } else {
        alert('There was a problem with uploading the picture');
      }
      $this.val('');
      files = undefined;
    }
  });
});

 // ******************************************
 // event handler for adding a photos for an upgraded business
 // ******************************************

 
$('#wrapper').on('change', '#aeb input:file', function(ee) {
  var $this = $(this);
  var $lip = $(this).parent().parent();
  var ind = Number($lip[0].id.slice(3));
  var data=new FormData(),params = {};
  if (!prepare4checkpic(ee,data,params,$this)) return;
  if (ind === 0) {
    params['minw'] = 200; params['minh'] = 200; 
    params['maxw'] = 200; params['maxh'] = -1;
  } else {
    params['minw'] = 200; params['minh'] = 200; 
    params['maxw'] = -700; params['maxh'] = -600;
  }
  common.doAjaxFormData('general.php','checkpic',params,data,'', function(json) {
    if(json.value === 'success') {
      bf = json.bf;
      bfarr[ind] = json.bf;
      $lip.find('img').attr('src','temp_images/' + bf);
      //menu.equalHeight();
    } else {
      if (json.errmsg != undefined) {
        alert(json.errmsg);
      } else {
        alert('There was a problem with uploading the picture');
      }
      $this.val('');
    }
    files = undefined;
  });
});  

function badinput(s) {
  if (s.indexOf('<') !== -1) return true;
  if (s.indexOf('>') !== -1) return true;
  return false;
}

function showrhspic(dontchange=false) {
  if (vars.nia > 0) {
    var $el = $('img#adimg');
    if ($el.length === 0) return;
    if (!dontchange) {
      vars.img_on = (vars.img_on + 1) % vars.nia;
      $el.attr('src', vars.imgdir + vars.imgarr[vars.img_on]);
    }
    var dw = $('#addiv').width();
    var w = vars.imgdimarr[vars.img_on]['w'];
    if (w < 1) return;
    var h = vars.imgdimarr[vars.img_on]['h'];
    var dh = (dw * h) / w;
    $el.css({'width':dw,'height':dh});
  }
}

// ******************************************
// event handler for resizing
// ******************************************

$(window).on('resize',function(){
  common.fixElementAndMask('dialog','mask2');
  common.fixElementAndMask('tabs_dialog','mask2');
  common.fixElementAndMask('getPwsAtStart_id','pw_and_login_mask');
  common.fixElementAndMask('login_id','pw_and_login_mask');
  common.fixElementAndMask('msgbox','msgboxmask');
  menu.checkWidth(); 
  //checkHeight();
  showrhspic(true);
  menu.fixWrapper();
  //menu.equalHeight();
});

function getadminlevel(){
  // returns a promise, either resolve or reject
  var dfd = $.Deferred();
  common.doAjax('pw.php','checkAdminLevel',{},'', function getadminlevelGood(json) { 
    // no error with Ajax call
    if(json.value === 'success') {
      dfd.resolve(json.res);
    } else { 
      dfd.resolve(false);
    }
  }, function getadminlevelBad() {   
    // error with Ajax call
    alert('Error occurred in database call');
    //window.location = vars.URL_base + "/index.php";
    dfd.reject();
  });   
  return dfd.promise();
}

function getlogincookie(){
  // returns a promise and argument, checks, which is true if login cookie is set properly
  var dfd = $.Deferred();
  common.doAjax('pw.php','checkLoginCookie',{},'', function getlogincookieGood(json) {
    if(json.value === 'success') {
      dfd.resolve(json.res);
    } else {
      dfd.resolve(false);
    }
  }, function getlogincookieBad() {
      alert('Error occurred in database call');
      dfd.reject();
  });
  return dfd.promise();
}

 // ******************************************
 // event handler for 
 // 1. Adding/Deleting businesses or classifieds from the pre list
 // 2. Upgrading/Downgrading/Removing businesses or classifieds from the main list
 // Both of these are triggered by clicks on buttons in the pop menu dropdown.
 // In each case an Ajax call is made to dopre.php with todo = the button name and what = businesses/classifieds
 // ******************************************

$('#topdiv').on('click', '#pop_buttons button', function __clickTo_Add_Delete_Upgrade_Downgrade_Remove_Selected(ee){
  ee.stopPropagation(); // Stop stuff happening
  ee.preventDefault();
  let todo = $(this).text();
  let v = $(this).parent().addClass('nodisplay').attr('data-v');
  var ichecked = $('#col2 input:checkbox:checked');
  var checkedValues = ichecked.map(function() {
      return this.id.slice(3);
  }).get().join(',');
  var data = {
    'baseDir':vars.baseDir,
    'URL_base':vars.URL_base,
    'isLocal': (vars.isLocal)? '1' : '0',
    'what':vars.what, 
    'list':checkedValues
  };
  if (v === 'pre') {
    var checkedCatnames = ichecked.map(function() {
      let ele = $(this).parent().parent().find('> p > b').text();
      return ele;
    }).get().join(vars.jsSEP);
    data['cnames'] = checkedCatnames;
  }
  common.doAjax('dopre.php',todo,data,'', function() {
    if (v === 'pre' || todo === 'Remove Selected') {
      removeChecked();
    }
  });
});

// ******** event handler for clicking on a business in the list ********************

$('#wrapper').on('mousedown', '#imgbox > div > table', function(ee){ 
  ee.stopPropagation(); // Stop stuff happening
  ee.preventDefault(); // Totally stop stuff happening
  var v = this.id.slice(1);
  var $pa =$(this).parent();
  var hi = $pa.hasClass('hi');
  //if (ee.button === 2 && hi) {   //alert('right mouse button used');
  if (ee.altKey === true && hi) {
    //alert('alt key was down');
    checkkey = true;
    bactive = v;
    starttime = new Date().getTime();
    codeentered = '';
  } else {
    checkkey = false;
    if (ee.button != 0) return;
    $pa.siblings().removeClass('itemActive');
    $pa.addClass('itemActive');
    if (hi) {  
      showTabs(v);
    }
  }
});
  
// ******** functions related to dialog box and mask2 when they are clicked ********************

$('#wrapper').on('click', '#imgbox > div > a', function(ee){ 
  ee.stopPropagation(); // Stop stuff happening
  ee.preventDefault(); // Totally stop stuff happening
  var v = this.id.slice(3);
  var $pa =$(this).parent();
  //var t = $(this).siblings();
  $pa.siblings().removeClass('itemActive');
  $pa.addClass('itemActive');
  showDialog(v);
});



//if dialog box is clicked
$('#dialog').on("click",function () {
  imageOff();
}); 

//if mask2 is clicked
$('#mask2').click(function () {
  if (!$('#dialog').hasClass('nodisplay')) imageOff();
  if (!$('#tabs_dialog').hasClass('nodisplay')) imageOff2();
});  

function imageOff() {
  $('#dialog').addClass('nodisplay');
  $('#d_image').attr('src','');
  $('#mask2').addClass('nodisplay');
  var $b = $('body');
  var w = $(window).width();
  if (w < 800) w = 800;
  if ($b.width() > w) $b.width(w);
}

$('#tabs_dialog').on("click",function () {
  imageOff2();
}); 

function imageOff2() {
  //$('#tabs_dialog').addClass('nodisplay');
  $('#tabs_dialog').css('display','none');
  $('#mask2').addClass('nodisplay');
  // var $b = $('body');
  // var w = $(window).width();
  // if (w < 800) w = 800;
  // if ($b.width() > w) $b.width(w);
}


//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
// **** events related to getting the username and passwords *******
//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

$('body').on('click', '#getPwsAtStart_id button', function(e) {
  // this is called when the program is run for the first time. it gets the passwords
  // that have been entered for the first time and saves them to the admin DB
  if ($(this).text() === 'Save') {
    var $upw = $('#getPwsAtStart_id input');
    var data = {'username': $upw[0].value, 'pw1':$upw[1].value, 'pw2':$upw[2].value};
    common.doAjax('pw.php','dosave',data,'', function(json) {
      if (json.value === 'success') {
        common.showMessage('Saved','g',0,true,function() {  // g is for good or green
          $('#getPwsAtStart_id').addClass('nodisplay');
          $('#pw_and_login_mask').addClass('nodisplay');
        });   
      }
    });
  } else {    // $(this).text() === 'Cancel'
    location.replace('https://www.google.com');  // go to google.com
  }
});

$('body').on('keypress', '#getPwsAtStart_id',function(e) {
  // this calls the click event handler above if Enter was pressed
  if (common.enterKeyOnInput(e,this)){
    $('#getPwsAtStart_id button:eq(0)').trigger('click');
  }
});

//***********
// XXXXXXXXXXXXXXXXX normal login XXXXXXXXXXXXXXXXXX
//***********
$('body').on('click', '#login_id button', function __clickOnLoginButton(e) {
  // this is a normal login by a user when Alt-L is pressed
  if ($(this).text() === 'Login') {
    let $upw = $('#login_id input')
    let username = $upw[0].value;
    let pw1 = $upw[1].value;
    let data = {'username': username, 'pw1':pw1};
    common.doAjax('pw.php','checkLoginPw',data,'', function(json) {
      if (json.value === 'success' && json.res) {
        showAdminButton();
        $('div#login_id').addClass('nodisplay');
        $('#pw_and_login_mask').addClass('nodisplay');
      } else {     // if (json.res === false) {
        common.showMessage('Please try again','b',0,true,function(){
          $upw.val('');
          $upw[0].focus();
        });
      }
    });
  } else {    // $(this).text() === 'Cancel'
    $('#login_id').addClass('nodisplay');
    $('#pw_and_login_mask').addClass('nodisplay');
  }
});

$('body').on('keypress', '#login_id',function(e) {
  // this calls the click event handler above if Enter was pressed
  if (common.enterKeyOnInput(e,this)){
    $('#login_id button:eq(0)').trigger('click');
  }
});


/* ----------------------------------------
The next 4 event handlers are for changing the current passwords.
1. When a user clicks on the menu item to change the passwords, then a box is displayed with 
html returned by tem_change_pw2() which contains the <div id="pwcheck_id">. After entering the
current passwords and clicking the OK button the first event handler is triggered.
2. pwcall() -> pw.php is called with todo = checkAllPw so that all passwords are checked. If they are ok
then a box is displayed with html returned by tem_change_pw3().
3. The user must enter new passwords and click on Save which triggers the 3rd event handler which calls
pwcall() -> pw.php with todo = save and the new passwords are saved to the admin database.
4. The 2nd and 4th are keypress event handlers that check if Enter was pressed. They will either set focus
to the next input box or trigger the appropriate event handler.
 ---------------------------------------------*/
$('#wrapper').on('click', '#pwcheck_id button', function(e){
  // this is called after the Master Administrator wants to change the passwords
  // it checks that the passwords entered are correct
  let $upw = $('#pwcheck_id input')
  let data = {'username': $upw[0].value, 'pw1':$upw[1].value, 'pw2':$upw[2].value};
  common.doAjax('pw.php','checkAllPw',data,'', function(json) {
    if (json.value === 'success' && json.res) {
      $('#col2').html(ezfhcode.tem_change_pw3());  // show box to get new passwords
      $('#col2 input')[0].focus();
    } else {       //if (json.res === 'notok') {
      common.showMessage('Please try again','b');
    }
  });
});

$('#wrapper').on('keypress', '#pwcheck_id',function(e) {
  // this calls the click event handler above if Enter was pressed
  if (common.enterKeyOnInput(e,this)){
    $('#pwcheck_id button:eq(0)').trigger('click');
  }
});
  
$('#wrapper').on('click', '#tem_change_pw3 button', function(ee){ 
  // ee.stopPropagation(); 
  // ee.preventDefault();
  if ($(this).text() === 'Save') {
    let $upw = $('#tem_change_pw3 input')
    let data = {'username': $upw[0].value, 'pw1':$upw[1].value, 'pw2':$upw[2].value};
    common.doAjax('pw.php','dochange',data,'', function(json) {
      if (json.value === 'success') {
        common.showMessage('Saved','g',0,true,function() {  // g is for good or green
          clearMenu();   // will also remove the box and mask
        });
      } 
    });
  } else {   // must be 'Cancel'
    clearMenu();     // will also remove the box and mask
  }
});

$('#wrapper').on('keypress', '#tem_change_pw3',function(e) {
  // this calls the click event handler above if Enter was pressed
  if (common.enterKeyOnInput(e,this)){
    $('#tem_change_pw3 button:eq(0)').trigger('click');
  }
});

/* ----------------------------------------
The next 3 event handlers are for setting Auto login.
1. When a user clicks on the menu for setting Auto login, then a box is displayed with 
html returned by tem_set_auto_login2() which contains the <div id="pwcheck_id2">. After entering the
username and password and clicking the OK button the first event handler is triggered.
2. pwcall() -> pw.php is called with todo = checkLoginPw so that passwords are checked. If they are ok
then a box is displayed with html returned by tem_change_pw3().
3. The user can then set Auto login to On or Off.
4. The 2nd event handler is a keypress event which checks if Enter was pressed. It will either set focus
to the next input box or trigger the event handler for clicking OK.
 ---------------------------------------------*/
$('#wrapper').on('click', '#pwcheck_id2 button', function(e){
  // password check before setting Auto login
  // it checks that the username & password entered are correct
  let $upw = $('#pwcheck_id2 input')
  let data = {'username': $upw[0].value, 'pw1':$upw[1].value};
  common.doAjax('pw.php','checkLoginPw',data,'', function(json) {
    if (json.value === 'success' && json.res) {
      getlogincookie().done(function(isOn){
        let s = '<div class="column" id="col2">';
        let s1 = common.getHtmlCode('tem_set_auto_login3');
        let $s1 = $(s1);
        if (isOn) {
          $s1.find('ul li > input[type="radio"]').last().attr("checked", true);
          vars.cookie = true;
        } else {
          $s1.find('ul li > input[type="radio"]').first().attr("checked", true);
        }
        s += $s1[0].outerHTML;
        $('#col2').replaceWith(s);
      });
    } else {   // if (json.res === 'notok') {
      common.showMessage('Please try again','b');
    }
  });
});

$('#wrapper').on('keypress', '#pwcheck_id2',function(e) {
  // this calls the click event handler above if Enter was pressed
  if (common.enterKeyOnInput(e,this)){
    $('#pwcheck_id2 button:eq(0)').trigger('click');
  }
});

$('#wrapper').on('click', '#auto_login_id button', function __clickOnOkToSaveAutoLogin(e) { 
  let $rbs = $('#wrapper #auto_login_id input');
  let numradios = $rbs.length;
  //alert(numradios);
  let onoroff;
  for (let i = 0; i < numradios; i++){
    if ($rbs[i].checked) {
      onoroff = $rbs[i].value;
      break;
      //alert($rbs[i].value);
    }
  }
  // set cookie to $_SESSION.adminlevel
  common.doAjax('pw.php','setLoginCookie', {'onoroff':onoroff},'', function(json) {
    if(json.value === 'success') {
      common.showMessage('Saved','g',0,true,function() {  // g is for good or green
        clearMenu();
      });   
    } else {
      common.showMessage('Problem with saving cookie','b');
    }
  });
});

// turns the Admin menu button on
function showAdminButton() {
  $('#admin_button').removeClass('nodisplay');
  menu.initTopVariables();
  menu.checkWidth(true);
}



$('#wrapper').on('click', '#savepictures', function(e) {
// clicked to save pictures
//console.log('entered click on button to save pictures');
  e.stopPropagation(); // Stop stuff happening
  e.preventDefault(); // Totally stop stuff happening
  let ttsarr = new Array(5);
  let $tts = $('#wrapper #col2 .txt_cls');
  let $cbs = $('#wrapper #col2 .cb_cls');
  if ($cbs.length != 5) {
    if (vars.debugon) alert('$cbs.length = ' + $cbs.length);
  }
  for (let i=0;i<5;i++) {
    if (bfarr[i] && bfarr[i] != '') {
      ttsarr[i]=$tts[i].value;
      if (vars.debugon) alert('new i= '+ i);
    } else if (afarr[i] && afarr[i] != '') {
      if (!$cbs[i].checked) {bfarr[i] = afarr[i]; ttsarr[i]=$tts[i].value;};
    }
  }
  let data = {'bactive':bactive,'title':JSON.stringify(ttsarr),'bfs':JSON.stringify(bfarr)};
  common.doAjax('doentry.php','editbuspics',data,'', function() {
    // this exits and shows the business list
    let $p1 = $("#theMenu > li:first-child");
    if ($p1.length > 0) {
      $p1.trigger( "click" );
    } else {
      $('#wrapper').html(common.getHtmlCode('tem_start_extras'));
      //menu.equalHeight();
    }
  });
});

$('#wrapper').on('click', '#exitpictures', function(e) {
// clicked to exit pictures
//console.log('entered click on button to exit pictures');
  e.stopPropagation(); // Stop stuff happening
  e.preventDefault(); // Totally stop stuff happening
  let $p1 = $("#theMenu > li:first-child");
  if ($p1.length > 0) {
    $p1.trigger( "click" );
  } else {
    $('#wrapper').html(common.getHtmlCode('tem_start_extras'));
    //menu.equalHeight();
  }
});

$('#tabs_dialog').on('click', '#amenu > li', function(e) {
// clicking on a tab in tabs_dialog  
  e.stopPropagation();
  e.preventDefault();
  let $this = $(this);
  let $gp = $this.siblings().andSelf();
  let index = $gp.index($this);
  $this.siblings().removeClass('active').end().addClass('active');
  let vv = $('#tabs_dialog img')[index];
  //$(vv).siblings().addClass('nodisplay').end().removeClass('nodisplay');
  $(vv).siblings().css('display','none').end().css('display','inline');
});

function testKeyCode(e) {  
  let keycode;  
  if (window.event) {
    keycode = window.event.keyCode;
  } else if (e) {
    keycode = e.which;
  }    
  e = e || window.event;
  if (e.altKey) {
    if(keycode===108 || keycode===76){   // Alt-L was pressed
      if ($('#login_id').hasClass("nodisplay") && $('#admin_button').hasClass("nodisplay")) {
        // first check if cookie is set to allow auto login
        common.doAjax('pw.php','checkLoginCookie',{},'Checking, please wait', function(json) {
          if(json.value === 'success' && json.res) {
            showAdminButton();
          } else {
            showbox('login_id');
          }
        });
      }
    } else if (keycode===83) {
      vars.debugon = !vars.debugon;
      (vars.debugon)? $('#debug').removeClass('nodisplay') : $('#debug').addClass('nodisplay');
    }
  } else {
    if (!checkkey) return;
    if (new Date().getTime() - starttime > 12000) {checkkey = false; return};
    if (keycode===13) {
      if (codeentered != '')  showbusentry(codeentered);
    } else {
      codeentered += String.fromCharCode(keycode);
    }
  }
} 

function clearMenu() {
  //$('#theMenu li').removeClass('menuOn');
  common.setMob(-1,-1);
  // menuActive = -1;
  // submenuActive = -1;
  $('#wrapper').html(common.getHtmlCode('tem_startup',{'loc':vars.loc}));
  $(window).trigger('resize');
  // checkWidth();
  // menu.equalHeight();
}

function removeChecked() {
  $('.listcell').has('input:checked').remove();
  $('.listcell').removeClass('even').removeClass('odd');
  $('.listcell').filter(':even').addClass('even').end().filter(':odd').addClass('odd');
  menu.checkHeight();
  menu.checkWidth();
  $(window).scrollTop(0);
}


$('#topdiv').on('click','#pop_menu > b',function() {
  let d = $(this).parent().find('#pop_buttons');
  if (d.hasClass('nodisplay')){
    d.removeClass('nodisplay');
  } else {
    d.addClass('nodisplay');
  }
});

 
});
 




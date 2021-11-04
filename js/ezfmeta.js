"use strict";

window.xx.module('menuFunctions', function (exports) {
  var xx = window.xx;
  var vars = window.xx.vars;
  
  var common,menu,categories,ezf,ezfhcode;
  xx.imports.push(function () {
    common = xx.common;
    menu = xx.menu;
    categories = xx.categories;
    ezf = xx.ezf;
    ezfhcode = xx.ezfhcode;
  });
  

  exports.extend({
    callMenuFunc:callMenuFunc,
    // functions related to menu items
    __showBusinesses: __showBusinesses,
    __showClassifieds: __showClassifieds,
    __AddABusiness: __AddABusiness,
    __AddAClassifiedAdvert: __AddAClassifiedAdvert,
    __AddEditCategories: __AddEditCategories,
    __newBusinesses: __newBusinesses,
    __newClassifieds: __newClassifieds,
    __EditBusinesses: __EditBusinesses,
    __EditClassifieds: __EditClassifieds,
    __ChangePWs: __ChangePWs,
    __AutoLogin: __AutoLogin,
    __Logout: __Logout
    
  });
  
/*   
This function checks if the Admin menu button was clicked and if so a check must be made to
see if the passwords haven't changed in the meantime. It might have happened that the boss
doesn't want someone to have access to the Admin functions and has changed the password. In
such a case the Admin button must be turned off by calling __Logout.
 */
function callMenuFunc(mob) {
  callit(this);
  // if (mob.menuActive === 3){    // the Admin menu is active and a submenu was clicked
    // ezf.getadminlevel().done(function(ok){
      // if (ok) {
        // callit(this);
      // } else {
        // __Logout();
      // }
    // });
  // } else {
    // callit(this);
  // }
  
  function callit(ob) {
    $('#topdiv').find('#pop_menu').remove();
    let mnu = (mob.submenuActive >= 0)? mob.sm[mob.submenuActive] : mob.tm[mob.menuActive];
    mnu = ob[mnu];  // turn mnu from a string into a function, ob = xx.menuFunctions
    // test if the function exists and if so call it
    if (typeof mnu === 'function') {
      let b = (mob.menuActive === 3)? true:false;
      common.widen(b);
      mnu();
      menu.checkWidth();
      menu.fixWrapper();
    }
    // code that uses eval to call the functions related to the menu items
    // try { 
      // eval(mnu + '();');
      // let b = (mob.menuActive === 3)? true:false;
      // common.widen(b);
      // menu.checkWidth();
      // menu.fixWrapper();
    // } catch (e){
      // if (window.xx.vars.debugon === true) alert(e);
    // };
  }
}
 
//*******************
// The menu functions 
//*******************

var pob; // object to hold data returned by Ajax calls

function __showBusinesses() {
  showBorC('Businesses','businesses');
}

function __showClassifieds() {
  showBorC('Classifieds','classifieds');
}

function showBorC(todo,what) {
  vars.what = what;
  showCol1(todo);
  // show col2
  let cob = categories.getCob();
  let id = cob.cat_selected;
  if (id && (id > 0)) {
    let catname = cob.ma_cn[id];
    common.doAjax('getcat.php',todo, {'cat_id':id} ,'', function(json) {
      let s = '<div id="imgbox" data-val="show">';
      let s1 = '', s2 = '';
      pob = JSON.parse(json.jscode);
      if (pob.id == undefined) {
        s += common.getHtmlCode('tem_no_entries');
      } else {
        // sort the entries into 2 html strings, s1 and s2
        for (let i = 0; i < pob.id.length; i++) {
          let v4 = '', v4a = '';
          if (pob.blogo[i] === '') {
            v4a = 'nodisplay';
          } else {
            v4 = vars.img_path + pob.blogo[i];
          }
          let ob = {'v1':i,'v2':pob.bname[i],'v3':pob.bdes[i],'v4':v4,'v4a':v4a,'v5':pob.bcontact[i]};
          //if (pob.hi_id[i] > 0) {
          if (pob.hi_id && pob.hi_id[i] > 0) {
            ob.v0 = ' hi';
            s1 += common.getHtmlCode('tem_entry',ob);
          } else {
            s2 += common.getHtmlCode('tem_entry',ob);
          }
        }
      }
      s += s1 + s2 + '</div>';
      $('#col2').html(s);
      
//$('#col2').removeClass('nodisplay');
      
      menu.checkHeight();
    });
    put_tdhead(todo,catname);
  } else {
    $('#col2').html('');
    $('#tdhead').addClass('nodisplay');
//$('#col2').removeClass('nodisplay');
  }
  showCol3();
}

function __AddABusiness() {
  vars.what = 'businesses';
  showCol1('Add a business');
  if ($('#addABusiness').length === 0) {
    let obb = {
      'bcname': "Mike Myburg",
      'bcemail': "mikemyburg@yahoo.com",
      'bctel': "12345678",
      'bname': "Business Name",
      'bdes': "Fixes computers",
      'bcontact': "123456, 5 High Street, PE, www.bus.co.za"
    }
    //obb ={};
    let s = '<div id="col2">';
    s += common.getHtmlCode('tem_add_business',obb);
    s += '</div>';
    $('#col2').replaceWith(s);
    let ob = categories.getCob();
    let id = ob.cat_selected;
    if (id > 0) {
      s = common.htmlspecialchars_decode(ob.ma_cn[id]);
      $('#addBorCinput').val(s).attr('data-catnum',id); 
    }
  }
//$('#col2').removeClass('nodisplay');
  showCol3();
}

function __AddAClassifiedAdvert() {
  vars.what = 'classifieds';
  showCol1('Add a classified advert');
  if ($('#addAClassified').length === 0) {
    let obb = {
      'bcname': "Mike Myburg",
      'bcemail': "mikemyburg@yahoo.com",
      'bctel': "12345678",
      'bname': "Classified heading",
      'bdes': "Selling computers",
      'bcontact': "123456, 5 High Street, PE"
    }
    //obb ={};
    let s = '<div id="col2">';
    s += common.getHtmlCode('tem_add_classified',obb);
    s += '</div>';
    $('#col2').replaceWith(s);
    let ob = categories.getCob();
    let id = ob.cat_selected;
    if (id > 0) {
      s = common.htmlspecialchars_decode(ob.ma_cn[id]);
      $('#addBorCinput').val(s).attr('data-catnum',id); 
    }
  }
//$('#col2').removeClass('nodisplay');
  showCol3();
}

function __AddEditCategories() {
  if ($('#addEditCategories').length === 0) {
    showCol1('Add/edit categories');
    let s = '<div id="col2">';
    s += common.getHtmlCode('tem_add_edit_categories');
    s += '</div>';
    $('#col2').replaceWith(s);
    let $rb = $('#actionRadio  input:checked');
    if ($rb.length === 0) {
      $('#actionRadio  input:eq(0)').trigger('click');
    }
    $('#addEditCategories').removeClass('nodisplay');
  } 
  //showCol3();
}

function __newBusinesses() {
  showNewBorC('New businesses','Businesses to be added','businesses');
}

function __newClassifieds() {
  showNewBorC('New classifieds','Classifieds to be added','classifieds');
}

function showNewBorC(hcol1,hcol2,what){
  vars.what = what;
  let s = '<h2 id="col1Heading" style="text-align:center;cursor:default;">' + hcol1 + '</h2>';
  s += common.getHtmlCode('tem_show_pre_lists',{'borc':what});
  $('#col1').html(s);
  // col2  // todo = what, either businesses or classifieds
  common.doAjax('getpre.php',what, {} ,'', function(json) {
    s = '<div id="imgbox" data-val="new">';
    pob = JSON.parse(json.jscode);
    if (pob.id === undefined) {
      s += common.getHtmlCode('tem_no_pres',{'v1':what});
    } else {
      $('#topdiv').append(ezfhcode.tem_pop_menu(['Add Selected','Delete Selected'],'pre'));
      for (let i = 0; i < pob.id.length; i++) {
        let v8 = (pob.mailstatus[i] > 0)? '&#x2714;' : '&#x2717;';
        let v9 = (pob.mailstatus[i] > 1)? '&#x2714;' : '&#x2717;';
        let dt = new Date(pob.dt[i]*1000).toDateString();
        let v4 = '', v4a = '';
        if (pob.blogo[i] === '') {
          v4a = 'nodisplay';
        } else {
          v4 = vars.img_path + pob.blogo[i];
        }
        let ob = {'v1':i,'v2':pob.bname[i],'v3':pob.bdes[i],'v4':v4,'v4a':v4a,'v5':pob.bcontact[i],
                  'v6':categories.getCob().ma_cn[pob.cat_id[i]],'v7':dt,'v8':v8, 'v9':v9,'v10':pob.id[i]};
        s += common.getHtmlCode('tem_pre_entry',ob);
      }
    }
    s += '</div>';
    $('#col2').html(s);
    put_tdhead(hcol2);
    menu.checkHeight();
    fixPE();
  });
}

$(window).on('resize',function(){
  fixPE();
});

function fixPE() {
  let pe = $('#wrapper').find('#pe_0');
  if (pe.length === 0) return;
  let w1 = pe.outerWidth();
  let w2 = pe.find('.pe_right').outerWidth();
  let w3 = w1 - w2 - 40;
  $('#wrapper').find('.pe_left').outerWidth(w3);
}


function __EditBusinesses() {
  showEditBorC('EditBusinesses','Edit Businesses','businesses');
}

function __EditClassifieds() {
  showEditBorC('EditClassifieds','Edit Classifieds','classifieds');
}

function showEditBorC(todo,heading,what){
  vars.what = what;
  showCol1(heading);
  // col2
  let cob = categories.getCob();
  let id = cob.cat_selected;
  if (id > 0) {
    let catname = cob.ma_cn[id];
    common.doAjax('getcat.php',todo, {'cat_id':id} ,'', function(json) {
      let s = '<div id="imgbox" data-val="edit">';
      let s1 = '', s2 = '';
      pob = JSON.parse(json.jscode);
      if (pob.id == undefined) {
        s += common.getHtmlCode('tem_no_entries');
      } else {
        $('#topdiv').append(ezfhcode.tem_pop_menu(['Upgrade Selected','Downgrade Selected','Remove Selected'],'tbl'));
        // s1 is for the upgraded entries
        for (let i = 0; i < pob.id.length; i++) {
          let v4 = '', v4a = '';
          if (pob.blogo[i] === '') {
            v4a = 'nodisplay';
          } else {
            v4 = vars.img_path + pob.blogo[i];
          }
          let dt = new Date(pob.dt[i]*1000).toDateString();
          let ob = {'v1':i,'v2':pob.bname[i],'v3':pob.bdes[i],'v4':v4,'v4a':v4a,'v5':pob.bcontact[i],
                    'v7':dt ,'v10':pob.id[i]};
          //if (pob.hi_id[i] > 0) {
          if (pob.hi_id && pob.hi_id[i] > 0) {
            ob['v0'] = ' hi';
            s1 += common.getHtmlCode('tem_edit_entry',ob);
          } else {
            s2 += common.getHtmlCode('tem_edit_entry',ob);
          }
        }
      }
      s += s1 + s2 + '</div>';
      $('#col2').html(s);
      //menu.checkWidth();
      menu.checkHeight();
      menu.checkWidth();
      fixPE();
    });
    put_tdhead(todo,catname);
  } else {
    $('#col2').html('');
    $('#tdhead').addClass('nodisplay');
    menu.checkHeight();
    menu.checkWidth();
  }
}


function __ChangePWs() {
  // contents of col1
  let s = '<div id="col1"><h3 style="text-align:center;margin-left:1em;margin-right:1em;">';
  s += 'Change the username and passwords';
  s += '</h3>';
  s += common.getHtmlCode('tem_change_pw1');
  $('#col1').replaceWith(s);
  // contents of col2
  s = '<div class="column" id="col2">';
  s += ezfhcode.tem_change_pw2();
  $('#col2').replaceWith(s);
  $('#col2 input')[0].focus();
}

function __AutoLogin() {
  // contents of col1
  let s = '<div id="col1"><h3 style="text-align:center;margin-left:1em;margin-right:1em;">';
  s += 'Auto login using Alt-L';
  s += '</h3>';
  s += common.getHtmlCode('tem_set_auto_login1');
  $('#col1').replaceWith(s);
  // contents of col2
  s = '<div class="column" id="col2">';
  s += ezfhcode.tem_set_auto_login2();
  $('#col2').replaceWith(s);
  $('#col2 input')[0].focus();
}

function __Logout() {
  $('#admin_button').addClass('nodisplay');
  ezf.clearMenu();
  menu.initTopVariables();
  menu.checkWidth(true);
}

// the next few functions are called by several of the above

function showCol1(heading) {
  if ($('#catlistdiv').length === 0) {
    // must put the whole catlist
    let s = '<div id="col1">';
    s += '<h2 id="col1Heading" style="text-align:center;cursor:default;">' + heading + '</h2>';
    s += categories.col1Html4Categories();
    s += '</div>';
    $('#col1').replaceWith(s);
    let ob = categories.getCob();
    let id = ob.cat_selected;
    if (id >= 0) categories.set_selected(id);
  } else {
    $('#col1Heading').text(heading);
  }
}


function put_tdhead(heading,catname=undefined) {
  let $ele = $('#tdhead');
  $ele.addClass('nodisplay').css('left',0);
  if (catname) {
    $ele.text(heading + ' - ' + catname);
  } else {
    $ele.text(heading);
  }
  let v = 0.5*($('body').outerWidth()-$ele.outerWidth());
  $ele.css('left',v).removeClass('nodisplay');
}


function showCol3() {
  let s = '<br><div id="addiv"><img id="adimg" /></div><br>';
  $('#col3').html(s);
  ezf.showrhspic();
}


$('#wrapper').on('click', '#imgbox > div', function(ee){
  if (ee.target.type != undefined) {
    if (ee.target.type.toLowerCase() === 'checkbox') return;
  }
  ee.stopPropagation();
  // ee.preventDefault();
  var $pa = $(this).parent();
  let val = $pa.attr('data-val');
    let v = parseInt(this.id.slice(3),10);
  if (val === 'show'){
    ezf.showDialog(pob,v,false);
  } else if (val === 'new'){
    ezf.showDialog(pob,v,true);
  } else if (val === 'edit'){
    var showHow = ($pa.hasClass('clsf'))? false:true;
    ezf.showDialog(pob,v,showHow);
  }
  $pa =$(this);
  $pa.siblings().removeClass('itemActive');
  $pa.addClass('itemActive');
});


});


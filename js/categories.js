"use strict";

window.xx.module('categories', function (exports) {
  var xx = window.xx;
  var vars = xx.vars;
  var common,menu;
  xx.imports.push(function () {
    common = xx.common;
    menu = xx.menu;
  });
  
  exports.extend({
    getCob:getCob,
    writeOneCatArrays:writeOneCatArrays,
    set_selected:set_selected,
    col1Html4Categories:col1Html4Categories
  });

// if true then the category list must be reput
//var catsDirty = true;

var cob;
function getCob() {return cob;}


function START_OF_CATEGORIES() {}
/* 
##############################################
The next few functions are for displaying the category list.
# function writeOneCatArrays(fname,cname) uses Ajax to call docat.php -> myFileClass2.php to retrieve
  arrays cob.ma_cn, cob.ma_ps and cob.ma_ps which are needed for showing the cat list.
# functions fix_parr() and parr_fixsub(p) create the parr[] array which an array which stores
  the parent of each category.
# function fix_barr() creates the barr[] array which stores the before category of each category...
  if the before category is a parent then its negative id is kept and if it is in the same line/subset
  then its positive id id kept.
# function isADecendentOf(idn1,idn2) returns true or false depending if one category is a decendent of
  another category.
  
  
  
# function jsCats(p,level) returns html to show a list of the categories. It starts at the first 
  element on level 1 and then makes recursive calls if an element has a subcategory.
  This is the new version which came about because I no longer have business or classified
  categories as in EzFind15 and earlier, ie they both use the same category list.

##############################################
 */
 
 
function writeOneCatArrays(fname,cname) {
  var dfd = $.Deferred();
  let data = {'fname':fname, 'baseDir':vars.baseDir, 'cname':cname};
  common.doAjax('docat.php','makejscats', data,'', function(json) {
    // success
    cob = JSON.parse(json.jscode);
  }, function(json) {
    // fail
    // alert("just testing");
    // alert(json);
    vars.errs.push(json);
  }, function( ) {
    // always
    //alert("just testing2");
    dfd.resolve();
  });
  return dfd.promise();
}
  
 
function col1Html4Categories() {
  //$('#itemPopup').removeClass('chosen').addClass('nodisplay');    // do this just in case it is still showing
  try {
  let s = '<div id = "catlistdiv">';
  s += '<div style="cursor:default;margin-bottom:6px;"><span style="padding:2px 4px;font-size:1.08em;">';
  s += cob.ma_cn[0];
  s += '</span></div>';
  s += jsCats(cob.ma_ps[0],1);
  s += '</div>';
  return s;
  } catch (e) {
    alert("error : " + e);
  }
}

/*  
 ma_id = array for category id
 ma_cn = array for category name
 ma_pn = array for pointer next
 ma_ps = array for pointer subcategory
 */

/**
 Parameters:
   p is the id of the first element in line/subset, when first called it is 0 but changes when 
     recursive calls are made.
   level is depth of the set/subset of categories, when first called it is 1, ie the top level,
     but this increases by 1 for each recursive call.
  Return:
    html is returned to display the catlist, ie the category list
 */ 
function jsCats(p,level) {
  let s ='', s1;
  let odd = true;
  while (p != 0) {
    s1 = 'class="items"';
    s +=  '<div class="alllevels level' + level + '">';
    if (cob.ma_ps[p] > 0) {
      s +=  '<div ' + s1 + ' id="ai_' + p + '">'
        + '<span class="indx">\u25ba</span><span class="itemName">' + cob.ma_cn[p] + '</span></div>';
      s += '<div id="sm_' + p + '" class="subcatlistdiv nodisplay" style="z-index:' + (level+1) + ';">';
      s += jsCats(cob.ma_ps[p],level+1);
      s +=  '</div>';    
    } else {
      s +=  '<div ' + s1 + ' id="ai_' + p + '">' 
        + '<span class="indx">\u25cf</span><span class="itemName">' + cob.ma_cn[p] + '</span></div>';
    } 
    s +=  '</div>';    
    odd = !odd;
    p = cob.ma_pn[p];
  }
  return s;
}
 
/*
******************************************* 
The next 3 functions are no longer used.  I now make the parr[] and barr[] in php when I
fetch the objects with the ma_ arrays.  I don't want to delete this code at this stage
because it may be that creating the parr[] and barr[] in js is a better way to go.
 ******************************************/
/*  
// parr is a global array
function fix_parr() {
  parr = new Array();
  parr[0] = 0;
  parr_fixsub(0);
}

function parr_fixsub(p) {
  var pn = cob.ma_ps[p];
  while (pn != 0) {
    parr[pn] = p;
    if (cob.ma_ps[pn] > 0) parr_fixsub(pn);
    pn = cob.ma_pn[pn];
  }
}

// barr is a global array
function fix_barr() {
  barr = new Array();
  barr[0] = 0;
  let p;
  for (let i = 0; i < cob.ma_pn.length; i++) {
    p = cob.ma_ps[i];
    if (p > 0) barr[p] = -i;
    p = cob.ma_pn[i];
    if (p > 0) barr[p] = i;
  }
}
 */
 
// This function returns true if idn1 is a decendent of idn2
function isADecendentOf(idn1,idn2) {
  let p = idn1;
  while (p > 0) {
    p = cob.ma_parr[p];
    if (p === idn2) return true;
  }
  return false;
}

///////////////////////////////////////////////////////////////////////////
// This event handler deals with clicking for opening or closing a subdirectory
///////////////////////////////////////////////////////////////////////////

$('#wrapper').on('click', 'span.indx', function(e) {
  e.stopPropagation(); 
  var ss = common.trim(this.innerHTML);
  if (ss == "\u25ba") {
    // sideways, ie closed so open folder
    var $level = $(this).parent().parent();
    var $gp = $level.siblings().andSelf();
    $level.find('> div:eq(0) > span.indx')[0].innerHTML = "\u25bc";
    var $sub = $level.find('> div.subcatlistdiv').removeClass('nodisplay');
  } else if (ss == "\u25bc") {
    // down, ie open so close folder
    // I must check if this folder which is been closed contains the chosen category
    // and if so then I must turn the chosen category off
    var $level = $(this).parent().parent();
    if (cob.cat_selected > 0) {
      let c = cob.cat_selected;
      let thisid = Number($level.find('div')[0].id.slice(3));
      if (isADecendentOf(cob.cat_selected,thisid)) {
        set_selected(thisid);
      }
    }
    $level.find('> div:eq(0) > span.indx')[0].innerHTML = "\u25ba";
    $level.find('> div.subcatlistdiv').addClass('nodisplay');
  }
});

 // ******************************************
 // event handler and routines for clicks and mouse events on items in category list in left column
 // ******************************************
 
 let onCategory;  // this is the category that the mouse is over
 
$('#wrapper').on('mouseenter', '.itemName', function __mouseenterOnItemName(e){ 
  e.stopPropagation();
  let ob = $(this).offset();
  let lft = Math.round(ob.left);  
  let top = Math.round(ob.top) - 1;   // -1 to allow for a border
  $('#itemPopup').css({'left': lft, 'top': top});
  $('#itemPopup').text(common.htmlspecialchars_decode(this.innerHTML));
  if ($(this).hasClass('chosen')) {
    $('#itemPopup').addClass('chosen');
  } else {
    $('#itemPopup').removeClass('chosen');
  }
  $('#itemPopup').removeClass('nodisplay');
  onCategory = this;
});

/* 
I need to check if the mouse entered div#itemPopup. If it was moved too quickly then
it might have skipped over div#itemPopup in which case I must turn it off. It was 
put on when the mouse entered span.itemName.
 */
$('#wrapper').on('mouseleave', '.itemName', function __mouseleaveFromItemName(e){ 
  if (e.relatedTarget.id != 'itemPopup') {
    $('#itemPopup').addClass('nodisplay');
  }
});
 
$('#itemPopup').on('mouseleave', function __mouseleaveItemPopup(e){ 
  e.stopPropagation();
  $('#itemPopup').removeClass('chosen').addClass('nodisplay');
  onCategory = null;
});
 
$('#itemPopup').on('click', function __clickItemPopup(e){ 
  e.stopPropagation();
  let $item = $(onCategory).parent();
  let item = $item[0];
  let ai_id = Number(item.id.slice(3));
  $(this).addClass('chosen');
  set_selected(ai_id);
  // set_selected(ai_id,true);    // only for testing
  // call the menu item with the new category
  common.callMenuFunc();
});
 
$('#wrapper').on('click',  '#col1', function __clickOnCol1(e) {
  e.stopPropagation();
  set_selected(0);
  if (!$('#aemr_move').hasClass('nodisplay')) fixActiveInputForMove(true);
});

function set_selected(id=-1,showSubs=false) {
  if (id === undefined) id=-1;
  if (showSubs === undefined) showSubs=false;
  if (id >= 0) {
    if (id === 0) {
      if ($('#addEditCategories').length === 0) return;
    }
    if (cob.cat_selected === 0) {  // turns heading off
      $('#wrapper').find("#col1 div span").removeClass('chosen'); 
    } else {   // turns a category off
      $('#ai_' + cob.cat_selected).find("span").eq(1).removeClass('chosen');
    }
  } else {
    id = -1;
  }
  cob.cat_selected = id;
  if (id === -1) return;
  highlightChosenItem(showSubs);
  fillInInput(id);
}

// This function fills values into input boxes of the addEditCategories box
// depending on which radio button has been selected, Add, Edit, Move or Remove.
function fillInInput(catNum) {
  if (catNum === undefined) return;
  let s = common.htmlspecialchars_decode(cob.ma_cn[catNum]);
  if ($('#wrapper').find('#addEditCategories').length > 0) {   // the addEditCategories box is showing
    if (catClass === 'addCat') {
      $('#wrapper').find('#addInput').val(s).attr('data-catnum',catNum); 
    } else if (catClass === 'editCat') {
      $('#wrapper').find('#editInput').val(s).attr('data-catnum',catNum); 
    } else if (catClass === 'moveCat') {
      $('#wrapper').find('#moveInput'+activeInputForMove).val(s).attr('data-catnum',catNum);
      fixActiveInputForMove(true);
    } else {     // if (catClass === 'removeCat') {
      $('#wrapper').find('#removeInput').val(s).attr('data-catnum',catNum); 
    }
  //} else if ($('#wrapper').find('#addABusiness').length > 0) {   // the addABusiness box is showing
  } else if ($('#wrapper').find('#addABusiness').length > 0) {   // the addABusiness box is showing
    $('#wrapper').find('#addBorCinput').val(s).attr('data-catnum',catNum); 
  } else if ($('#wrapper').find('#addAClassified').length > 0) {   // the addAClassified box is showing
    $('#wrapper').find('#addBorCinput').val(s).attr('data-catnum',catNum); 
  }
}

/* 
function turnSelectedOff() {
  var v = get_selected();
  if (v != 0) {$('#catlistdiv div[id="ai_' + v + '"]').removeClass('chosen');}
}
 */
 
/*  
 This function is from EzFind. It was used when cob.cat_selected could be for either businesses 
 or classifieds and then the categories needed to be opened if they were closed so that the 
 chosen category could be reached.
 I have changed it a bit.  Originally it highlighted the full path to the selected category,
 but that does not look nice.  What is nice about it is that it will open up all parent
 categories of the selected category and then highlight the chosen item.  It can also show
 the subcategories of the chosen item if showsubitems is set to true.
 I don't really need it if the chosen category is already visible - I should shorten it if 
 this is the case.
 */ 
function highlightChosenItem(showsubitems) {
  // create a path in idarr from the chosen p to its root
  let p = cob.cat_selected;
  if (p === 0) {
    $('#wrapper').find("#catlistdiv > div > span").addClass('chosen');  // mark the heading
  } else {
    let idarr = new Array();
    let n = 0;
    while (p > 0) {
      idarr[++n] = p;
      p = cob.ma_parr[p];
    }
    // now start at the root and go down to the chosen p
    let $item = $('#ai_' + idarr[n]);
    let $sub;
    while (n > 1) {
      $item.find('> span.indx')[0].innerHTML = '\u25bc';
      $sub = $item.siblings('.subcatlistdiv').removeClass('nodisplay');
      n--;
      $item = $('#ai_' + idarr[n]);
    }
    $item.find("span").eq(1).addClass('chosen');  // mark the selected item
    let ele = $item.find('> span.indx')[0];  // 
    if (showsubitems != undefined && showsubitems === true && ele != undefined && ele.innerHTML === '\u25ba') {
      ele.innerHTML = '\u25bc';
      $sub = $item.siblings('.subcatlistdiv').removeClass('nodisplay');
    }
  }
}

 // ******************************************
 // addEditCategories is the box for getting input for adding/editing a category
 // ******************************************
 
let activeInputForMove = 0;
let catClass;
 
$('#wrapper').on('change','#actionRadio input', function __clickToChangeRadio(e) {
  let r = e.target.value;
  catClass = r + 'Cat';
  $('#addEditCategories  div').not('div#categoriesButton').addClass('nodisplay');
  $('#addEditCategories  div.' + catClass).removeClass('nodisplay');
  
  if (cob.ma_used > 1) {     // there is at least one category, ie besides the heading
    if (r === 'move') {
      activeInputForMove = 0;
      fixActiveInputForMove();
    }
    $('#wrapper').find('#addEditCategories input[type="text"]').val('').removeAttr('data-catnum');
    //set_selected(cob.cat_selected);
  }
  
  if (cob.cat_selected >= 0) fillInInput(cob.cat_selected);
});

$('#wrapper').on('click', '#aemr_move input[type="text"]', function __clickToFocusInputForMove() {
  activeInputForMove = parseInt($(this).attr('data-n'),10);
  fixActiveInputForMove();
});
 
function fixActiveInputForMove(toggle=false) {
  if (toggle === undefined) toggle=false;
  if (toggle) activeInputForMove = (activeInputForMove + 1) % 2;
  $('#aemr_move input[data-n="' + activeInputForMove + '"]').removeClass('notActiveInput');
  let notActive = (activeInputForMove + 1) % 2;
  $('#aemr_move input[data-n="' + notActive + '"]').addClass('notActiveInput');
}

 // ******************************************
 // event handler for adding, editing, moving or removing a category
 // ******************************************
$('#wrapper').on('click', 'div#categoriesButton button', function __clickOnOKToAddEditCats(e){
  // click on OK in Add/Edit categories
  e.preventDefault();
  e.stopPropagation();
  // for isSub and isSub2move: 0 is false and 1 is true when passed to php code
  let id, id2move, catname, isSub=0;  
  let b,p;
  
    //************* add ********************
  if (catClass === 'addCat') {
    catname = common.htmlspecialchars(common.trim($('#wrapper').find('#addInput_name').val()));
    if (catname === '') {
      common.showMessage('You must enter a category name','b');
      return;
    }
    id = parseInt($('#wrapper').find('#addInput').attr('data-catnum'),10);
    if ((id >= 0) === false) {
      common.showMessage('You must select a category where to insert','b');
      return;
    }
    let rad = $('#wrapper').find('#aemr_add input[type="radio"]:checked').val();
    if (id === 0) {   // the heading so can only add a sub
      if (rad === 'before' || rad === 'after') {
        common.showMessage('You cannot insert before or after the heading','b');
        return;
      }
      p = cob.ma_ps[0];
      if (p === 0) {   // should only happen if cob.ma_used = 1
        isSub = 1;  // true in php
      } else {
        id = lastInLine(p); // isSub will be 0 so cat will be added to cob.ma_pn
      }
    } else {
      if (rad === 'before') {
        b = cob.ma_barr[id];
        if (b < 0) {   // id is the first subcat of b
          // add in b as first subcat
          id = -b;
          isSub = 1;
        } else if (b === 0) {  // id is the first subcat of the heading
          id = 0;
          isSub = 1;
        } else {   
          // add after b inline
          id = b;   // isSub is already 0
        }
      } else if (rad === 'after') {
        // if heading, add as sub, otherwise add inline, ie isSub = 0
        if (id === 0) isSub = 1;   
      } else {    //if (rad === 'assub') {
        p = cob.ma_ps[id];
        if (p === 0) {
          isSub = 1;
        } else {
          id = lastInLine(p);
        }
      }
    }
    $('#wrapper').find('#addInput_name').val('');
    //todo = 'add';
    docatphp('add',{'id':id, 'cat':catname, 'isSub':isSub });
    
    
    //************* edit ********************
  } else if (catClass === 'editCat') {
    id = parseInt($('#wrapper').find('#editInput').attr('data-catnum'),10);
    if ((id >= 0) === false) {
      common.showMessage('You must select a category','b');
      return;
    }
    catname = common.htmlspecialchars(common.trim($('#wrapper').find('#editInput_name').val()));
    if (catname === '') {
      common.showMessage('You must enter the new category name','b');
      return;
    }
    $('#wrapper').find('#editInput_name').val('');
    //todo = 'edit';
    docatphp('edit',{'id':id, 'cat':catname});
    
    //************* move ********************
  } else if (catClass === 'moveCat') {
    // id is the insertion cat and id2move is the cat that must be moved
    let id2move, pre2move, isSub2move=0;  
    id2move = parseInt($('#wrapper').find('#moveInput0').attr('data-catnum'),10);
    if ((id2move >= 0) === false) {
      common.showMessage('You must select a category to move','b');
      return;
    }
    id = parseInt($('#wrapper').find('#moveInput1').attr('data-catnum'),10);
    if ((id >= 0) === false) {
      common.showMessage('You must select a category where to move to','b');
      return;
    }
    if (id2move === 0) {
      common.showMessage('You cannot move the heading','b');
      return;
    }
    if (id === id2move) {
      common.showMessage('You cannot move the category to itself','b');
      return;
    }
    if (isADecendentOf(id,id2move)) {
      common.showMessage('You cannot move a category to a decendent of itself','b');
      return;
    }
    let rad = $('#wrapper').find('#aemr_move input[type="radio"]:checked').val();
    if (id === 0) {   // move to the heading so can only insert as a sub
      if (rad === 'before' || rad === 'after') {
        common.showMessage('You cannot insert before or after the heading','b');
        return;
      }
      p = cob.ma_ps[0];
      if (p === 0) {   // should only happen if cob.ma_used = 1
        isSub = 1;  // true in php
      } else {
        id = lastInLine(p); // isSub will be 0 so cat will be added to cob.ma_pn
      }
    } else {
      if (rad === 'before') {
        b = cob.ma_barr[id];
        if (b < 0) {   // id is the first subcat of b
          // add in b as first subcat
          id = -b;
          isSub = 1;
        } else if (b === 0) {  // id is the first subcat of the heading
          id = 0;
          isSub = 1;
        } else {   
          // add after b inline, isSub is already = 0
          id = b;
        }
      } else if (rad === 'after') {
        // if heading, add as sub, otherwise add inline, ie isSub = 0
        if (id === 0) isSub = 1;   // This should already have been dealt with
      } else {    //if (rad === 'assub') {
        p = cob.ma_ps[id];
        if (p === 0) {
          isSub = 1;
        } else {
          id = lastInLine(p);
        }
      }
      if (id === id2move) {
        //common.showMessage('The category won't be moved','b');
        return;
      }
      if (isSub === 1) {
        if (cob.ma_ps[id] === id2move) {
          //common.showMessage('The category won't be moved','b');
          return;
        }
      } else {   // isSub = 0
        if (cob.ma_pn[id] === id2move) {
          //common.showMessage('The category won't be moved','b');
          return;
        }
      }
      // must fix the link where the category was moved from 
      b = cob.ma_barr[id2move];
      if (b < 0) {   // id2move is the first subcat of b
        pre2move = -b;
        isSub2move = 1;
      } else if (b === 0) {  // id2move is the first subcat of the heading
        pre2move = 0;
        isSub2move = 1;
      } else {   // relink after b inline
        pre2move = b;  // isSub2move is already 0
      }
    } 
    //todo = 'move';
    activeInputForMove = 0;
    fixActiveInputForMove();
    docatphp('move',{'id':id,'isSub':isSub,'id2move':id2move,'pre2move':pre2move,'isSub2move':isSub2move });
    
    //************* remove ********************
  } else {     // if (catClass === 'removeCat') {
    id = parseInt($('#wrapper').find('#removeInput').attr('data-catnum'),10);
    if ((id >= 0) === false) {
      common.showMessage('You must select a category to remove','b');
      return;
    }
    catname = cob.ma_cn[id];
    let msg;
    if (id=== 0) {
      if (cob.ma_ps[0] === 0) {
        // there are no categories
        return;
      }
      msg = 'You cannot remove the heading, <i>' + catname + '</i>, but all its contents will be removed. Proceed?'
      id = -1;
      isSub = 1;
    } else {
      msg = 'This category, <i>' + catname + '</i>, and all its contents will be removed. Proceed?';
      // must fix the link where the category was removed from 
      b = cob.ma_barr[id];
      if (b < 0) {   // id is the first subcat of b
        id = -b;
        isSub = 1;
      } else if (b === 0) {  // id is the first subcat of the heading
        id = 0;
        isSub = 1;
      } else {   // remove after b inline
        id = b;  // isSub is already 0
      }
    }
    common.showMessage("<b>Warning! </b>" + msg,'w',1,true,function(yes) {
      if (yes != true) return;
      //todo = 'remove';
      docatphp('remove',{'id':id,'isSub':isSub });
    });
  }
  
  function lastInLine(p) {
    // p must not be 0 at start
    let b = 0;
    while (p != 0) {
      b = p;
      p = cob.ma_pn[b];
    }
    return b;
  }
  
});
 
function docatphp(todo,data) {
// adds a new category or edits, moves or removes an existing category
  common.doAjax('docat.php',todo,{'fname':'catlist','baseDir':vars.baseDir},'', function(json) {
    if (json.value === 'fail') {
      alert('Error in adding/editing category');
    } else if (json.value === 'success') {
      let cat_selected = cob.cat_selected;
      cob = JSON.parse(json.jscode);
      cob.cat_selected = cat_selected;
      $("#catlistdiv").replaceWith(col1Html4Categories());
      set_selected(cob.ma_highlight);
    }
  });
}  
  

$('#wrapper').on('keypress', '#addInput_name,#editInput_name', function __keypressOnInputs(e) {
  if (e.which === 13) $('#wrapper').find('#categoriesButton button').trigger('click');
});

});

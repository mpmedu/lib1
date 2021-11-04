"use strict";

window.xx.module('menu', function (exports) {
  var xx = window.xx;
  var meta,common;
  xx.imports.push(function () {
    meta = xx.meta;
    common = xx.common;
  });

  exports.extend({
    init:init,
    initTopVariables:initTopVariables,
    checkWidth:checkWidth,
    checkHeight:checkHeight,
    fixWrapper:fixWrapper,
    checkSidebarHeight:checkSidebarHeight
    //equalHeight:equalHeight,
  });

let lastBw = 0;

// saves widths of menu items so that they can be properly placed when window is resized
let siteNameWidth;
let pageNameWidth;
let topMenuWidth;

let th;      // topdiv height
let tbh;     // topdiv + bottomdiv height


 
////////////////////////////////////////////////////////////////////
function INIT(){}
////////////////////////////////////////////////////////////////////

function init() {
  initTopVariables();
  checkWidth();
  common.tm_enable();
  $('#toprightmenumask').addClass('nodisplay');
  //$($('#theMenu li')[0]).trigger('click');
  //$('#wrapper').html(common.getHtmlCode('tem_layout1'));
  fixWrapper();   // does checkHeight() and checkSidebarHeight()
}

function initTopVariables() {
  $('#siteName').text(meta.siteVars.siteName);
  $('#pageName').text(meta.siteVars.pageName);
  // init size of body mask
  //$('#bodymask').css({'width':screen.width, 'height':screen.height});
  // inits variables topMenuWidth, pageNameWidth and siteNameWidth
  let $topdiv = $('#topdiv');
  siteNameWidth = $topdiv.find('#siteName').outerWidth();
  pageNameWidth = $topdiv.find('#pageName').outerWidth();
  // width of top menu items
  let $theMenu = $topdiv.find('#theMenu');
  topMenuWidth = $theMenu.outerWidth();
  //$('#topmenumask').outerWidth(topMenuWidth).outerHeight($theMenu.outerHeight());
  $('#topmenumask').width(topMenuWidth).height($theMenu.outerHeight());
  //$('#toprightmenumask').outerWidth(topMenuWidth - $($theMenu[0].firstChild).outerWidth()).outerHeight($theMenu.outerHeight());
  $('#toprightmenumask').width(topMenuWidth - $($theMenu[0].firstChild).outerWidth()).height($theMenu.outerHeight());
  // height of topdiv + bottomdiv
  th = document.getElementById('topdiv').clientHeight;
  let h = document.getElementById('bottomdiv').clientHeight;
  tbh = th + h;
  $('body').css({'padding-top':th + 'px'});
  $('#fixed_sidebar').css('top',th + 'px');
  // also init the top of the wrappermask and dialogmask to be the top of the wrapper
  $('#wrappermask').css('top',th);
  $('#dialogmask').css('top',th);
}
 
function checkWidth(fullCheck=false) {
  //if (fullCheck === undefined) fullCheck = false;
  //if browserWidth less rhs scroll < xx.constants..MIN_DOC_WIDTH, ie 800, then don't make narrower
  // a horizontal scrollbar will be shown
  let bw = Math.round($(window).width());   // this is the display width, vertical scrollbar not included
  //myCheck(3, 'checkWidth1 \n' + getDimensions());
  if (bw === lastBw) if (!fullCheck) return;
  if (bw < xx.constants.MIN_DOC_WIDTH) {
    bw = xx.constants.MIN_DOC_WIDTH;
    $('body').width(bw);
  } else {
    $('body').width('100%');
    bw = $('body').width();
  }
  $('#fixed_sidebar').width(0.21 * bw);
  lastBw = bw;
  //$('body').width(bw);
  //myCheck(3, 'checkWidth2 \n' + getDimensions());
  putTopdiv(bw);
  //myCheck(3, 'checkWidth5 \n' + getDimensions());
};

function checkHeight() {
  //myCheck(4, 'checkHeight1 \n' + getDimensions());
  let $wrap = $('#content');
  $wrap.css('height','auto');
  let $col3 = $('#col3');
  $col3.css('height','auto');
  //myCheck(4, 'checkHeight2 \n' + getDimensions());
  // the wrapper/content height should be such that the whole window is filled
  let wh = $(window).height() - tbh - 5;  // 5 is for the top border of the bottomdiv
  // don't allow the wrapper/content to be less that 250 in height
  //if (wh < 250) wh = 250;   // I now have #content{min-height:250px;}
  let col2_h = $wrap.outerHeight();
  //let col3_h = $col3.outerHeight();
  if (col2_h > wh) wh = col2_h;
  //if (col3_h > wh) wh = col3_h;
  // $wrap.outerHeight(wh);
  // $col3.outerHeight(wh);
  $wrap.height(wh);
  $col3.height(wh);
  //myCheck(4, 'checkHeight3 \n' + getDimensions());
}
 
function putTopdiv(bw) {
  //---------- This puts the elements in the Top Div ------------
  let v = Math.round(0.05*bw);    // bw is the body width
  $('#siteName').css('left',v-2);
  $('#pageName').css('left',v + siteNameWidth);
  v = 0.92*(bw-topMenuWidth);
  $('#topmenucontainer').css('left',v);
  let $ele = $('#tdhead');
  if (!$ele.hasClass('nodisplay')) {
    $ele.addClass('nodisplay').css('left',0);
    v = 0.5*(bw-$ele.outerWidth());
    $ele.removeClass('nodisplay').css('left',v);
  }
  $(window).trigger('scroll');
}

function equalHeight(h) {
  var tp = $('#wrapper').position().top;
  var hb = $('#bottomdiv').outerHeight();
  var tallest = 0;
  if (h) tallest = h;
  h = $(window).height() - tp - hb;
  if (h > tallest) tallest = h;
  var $cols = $('#wrapper > div');
  $cols.each(function() {
     $(this).height('auto');
    var thisHeight = $(this).outerHeight();
    if(thisHeight > tallest) {
       tallest = thisHeight;
    }
  });
  $('#wrapper').height(tallest);
  $cols.outerHeight(tallest,true);
}

 
function fixWrapper() {
  checkHeight();
  //let bw = $('body').outerWidth();
  //$('#fixed_sidebar').outerWidth(0.2*bw);
  //$('#content').outerWidth(0.8*bw).css('left',(0.2*bw)+'px');
  checkSidebarHeight();
}

 
$(window).on('resize',function(){
  checkWidth();
  fixWrapper();
});

var lastLeft = -1;
window.onscroll = function() {
  let n = $(window).scrollLeft();
  if (n != lastLeft) {
    lastLeft = n;
    n = -n + 'px';
    $('#topdiv').css('left',n);
    $('#fixed_sidebar').css('left',n);
  }
  checkSidebarHeight();
};

function checkSidebarHeight() {
  let y = $('#content').outerHeight() - $(window).scrollTop();
  let $ele = $('#fixed_sidebar');
  let h = $ele.height();
  let wh = $(window).height() - th;
  if (y > wh) {
    y = wh;
  }
  if (h != y){
    //$ele.outerHeight(y);
    $ele.height(y);
  }
}

});

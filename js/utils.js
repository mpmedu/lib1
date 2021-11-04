"use strict";



//////////////////////////////////////////////////////////////////////
function CHECKS() {}
//////////////////////////////////////////////////////////////////////
 
 let checks = [
   {name: "Dimensions in resize",    // 0
    al: false,
    co: false},
   {name: "Dimensions in showDialog",    // 1
    al: false,
    co: false},
   {name: "Dimensions in centreDialog",    // 2
    al: false,
    co: false},
   {name: "Dimensions in checkWidth",    // 3
    al: false,
    co: false},
   {name: "Dimensions in checkHeight",    // 4
    al: false,
    co: false},
   {name: "Scroll in getDimensions",    // 5
    al: false,
    co: false},
   {name: "Keycode value",      // 6
    al: false,
    co: false}
  ]
  
// boolean which is true if category selection boxes have been clicked
  
function showChecklist() {
  let ob = {};
  if (getchecklist(ob)){
    document.getElementById('checklist_container').innerHTML = ob.s;
    showDialog('checklist_container');
  } else {
    alert('Problem with checklist');
  }
}

function getchecklist(ob){
  let bk = new Array();
  bk[0] = '#eee'; bk[1] = '#fff';
  // first the heading
  let s = '<div id="checklistDialog" style="background-color:' + bk[(checks.length)%2] + '">';
  s += '<div style="background-color:#666; color:white;">';
  s += '<div class="check_name" >Check</div>';
  s += '<div  class="al_cb" style="padding-left:0;">Alert</div>';
  s += '<div  class="co_cb" style="padding-left:0;">Console</div></div>';
  // now all the checks
  for (let i = 0; i < checks.length; i++) {
    s += '<div class="check_row" style="background-color:' + bk[i%2]+ '">';
    s += '<div class="check_name" >' + i + ') '+ checks[i].name + '</div>'; 
    let alval = '';
    if (checks[i]['al'] === true) {
      alval = ' checked';
    }
    s += '<div class="al_cb">';
    s += "<input type='checkbox'" + alval + ">";
    s += '</div>';
    let coval = '';
    if (checks[i]['co'] === true) {
      coval = ' checked';
    }
    s += '<div class="co_cb">';
    s += "<input type='checkbox'" + coval + ">";
    s += '</div>';
    s += '</div>';
  }
  s += '<br>';
  // finally put the OK button at the bottom
  s += '<div style="margin-right:3%">';
  s += '<button class="butt_class1" style="padding:2px 10px;">OK</button>';
  s += '</div>';
  ob.s = s;
  return true;
}

//****************************************
// event handler when clicking OK in the checklist dialog
//****************************************
  
$('#checklist_container').on('click', 'button', function(e){
  // clicking on the OK button the checklist dialog
  hideDialog('checklist_container');
  // update the checks array
  // first use jQ to get the input values
  let $clal = $('#checklist_container .al_cb>input');
  let $clco = $('#checklist_container .co_cb>input');
  // now write the new values to the checks array
  for (let i = 0; i < checks.length; i++) {
    checks[i].al = $clal[i].checked;
    checks[i].co = $clco[i].checked;
  }
});

//***************
//** This function is for testing - I used it a lot when resizing the window to see how things look
//** It will only get called if xx.vars.debugon is true
//***************
function getDimensions(){
  let s = '';
  let wrap = document.getElementById('wrapper');
  let c4 = wrap.clientHeight;
  let c2 = wrap.offsetHeight;
  let c3 = wrap.scrollHeight;
  let c5 = wrap.clientWidth;
  let c6 = wrap.offsetWidth;
  let c7 = wrap.scrollWidth;
  
  let $wrap = $('#wrapper');
  let c8 = $wrap.outerHeight();
  let c9 = $wrap.outerWidth();
  
  //---------------------------
  // height
  // window
  // jQ
  let t = $(window);
  let w3 = t.innerHeight();
  let w2 = t.outerHeight();
  let x2 = t.outerHeight(true);
  let x3 = t.height();
/*   s += "$(window)" + '  ' + ".innerHeight() = " + w3  + '\n';
  s += ".outerHeight() = " + w2  + '\n';
  s += ".outerHeight(true) = " + x2  + '\n';
  s += ".height() = " + x3  + '\n';
 */  
  
  // js
  let e2 = window.innerHeight;
  let y6 = window.outerHeight;
  
  // document.body
  // jQ
  let d1 = $(document).height();
  let d11 = $(document).innerHeight();
  let d12 = $(document).outerHeight();
  let d13 = $(document).outerHeight(true);
  let d14 = $('body').height();
  let d15 = $('body').innerHeight();
  let d16 = $('body').outerHeight();
  let d17 = $('body').outerHeight(true);
  // js
  let b1 = document.body.clientHeight;
  let d3 = document.body.offsetHeight;
  let d4 = document.body.scrollHeight;
  //-------------------------------------------
  // width
  // window
  // jQ
  let w5 = $(window).innerWidth();
  let w4 = $(window).outerWidth();
  let x4 = $(window).outerWidth(true);
  let x5 = $(window).width();
  // js
  let w6 = window.innerWidth;
  let w7 = window.outerWidth;
  
  // document.body
  // jQ
  let d5 = $(document).width();
  let d20 = $(document).innerWidth();
  let d21 = $(document).outerWidth();
  let d22 = $(document).outerWidth(true);
  let d23 = $('body').width();
  let d24 = $('body').innerWidth();
  let d25 = $('body').outerWidth();
  let d26 = $('body').outerWidth(true);
  //js
  let b11 = document.body.clientWidth;
  let b12 = document.body.offsetWidth;
  let b13 = document.body.scrollWidth;
  
/*   s += "window.outerWidth = " + w7  + " || " + "window.outerHeight = " + y6 + '\n';
  s += "$(window).width() = " + x5  + " || " + "window.innerWidth = " + w6  + '\n';
  s += "$(window).height() = " + x3  +  " || " + "window.innerHeight = " + e2  + '\n';
  s += "body width = " + b11  + "   body height = " + b1  + '\n';
  //s += "$body width = " + d23  + "   $body height = " + d14  + '\n';
  s += "wrapper width = " + c5  + "   wrapper height = " + c4  + '\n';
  //s += "$wrapper width = " + c9  + "   $wrapper height = " + c8  + '\n';
  
  //s += "window" + '  ' + ".innerWidth = " + w6  + '\n';
  //s += "window" + '  ' + ".innerHeight = " + e2  + '\n';
  
  s += "document.body.clientWidth = " + b11  + "   document.body.scrollWidth = " + b13  + '\n';
  s += "document.body.clientHeight = " + b1  + "   document.body.scrollHeight = " + d4  + '\n';
  
  return s;
 */  
  s += "window.outerWidth = " + w7  + " || " + "window.outerHeight = " + y6 + '\n';
  s += "window.innerWidth = " + w6  + " || " + "window.innerHeight = " + e2 + '\n';
  s += "$(window).width() = " + x5  + " || " + "$(window).height() = " + x3  + '\n';
  s += "-----------------------------------------------------------------------" + '\n';
  s += "$('body').outerWidth(true) = " + d26  + " || " + "$('body').outerHeight(true) = " + d17  + '\n';
  
  return s;

  if (b1 > w2) {
    // there is a rhs scroll
    myCheck(5, 'rhs scroll');
  }
  if (b11 > w5) {
    // there is a lhs scroll
    myCheck(5, 'bottom scroll');
    if (b1 > w2) {
    }
  }
  
  if (w6 > x4) {
    myCheck(5, 'rhs scroll using window dimensions');
  }
  
  if (e2 > x2) {
    myCheck(5, 'bottom scroll using window dimensions');
  }
  
  let z3 = document.body.getBoundingClientRect();
  //let z4 = window.getBoundingClientRect();
  let z5 = wrap.getBoundingClientRect();
  
  //document.body.style.overflowX = "hidden";
  let z0 = document.body.style.overflow;
  let z1 = document.body.style.overflowX;
  let z2 = document.body.style.overflowY;
}

let checkOn = true;
function myCheck(n,s) {
  // this uses confirm instead of alert, if you choose cancel then checkOn is set to false so subsequent
  // calls to myCheck() will not show the message box, but the value of checkOn is set back to true after
  // two seconds so that the message box will again show after that.
  //console.log(s);
  //console.log('*****************************');
  if (xx.vars.debugOn === false) return;
  if (checks[n].al === true) {
    if (checkOn) {
      let ans = confirm(s);
      if (ans === false) {
        checkOn = false;
        setTimeout('checkOn = true',2000);
      }
    }
  }
  if (checks[n].co === true) {
    console.log(s);
  }
}


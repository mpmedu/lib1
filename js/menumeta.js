"use strict";
 
window.xx.module('meta', function (exports) {

  exports.extend({
    siteVars : {
      siteName:'EzFind',
      pageName:'Gotham'
    },
    topmenu : [
      {name:'Business list',func:'__showBusinesses'},          // 0
      {name:'Classifieds',func:'__showClassifieds'},            // 1
      {name:'Add a business/advert',   // 2
       submenu: [
        {name:'Add a business',func:'__AddABusiness'},
        {name:'Add a classified advert',func:'__AddAClassifiedAdvert'}
        ]
      },
      {name:'Admin',           // 3
       submenu: [
        {name:'Add/edit categories',func:'__AddEditCategories'},    // 0
        {name:'New businesses',func:'__newBusinesses'},         // 1
        {name:'New classifieds',func:'__newClassifieds'},        // 2
        {name:'Edit businesses',func:'__EditBusinesses'},        // 3
        {name:'Edit classifieds',func:'__EditClassifieds'},        // 4
        {name:'Change the username and passwords',func:'__ChangePWs'},   // 5
        {name:'Auto login using Alt-L',func:'__AutoLogin'},        // 6
        {name:'Logout',func:'__Logout'}        // 7
       ],
       //hide:true,
       id:'admin_button'
      }
    ]
  });

});
 
window.xx.module('menuFunctions', function (exports) {

  exports.extend({
    callMenuFunc:callMenuFunc,
    // functions related to menu items
    __showBusinesses: __showBusinesses,
    __showClassifieds: __showClassifieds,
    __AddEditCategories: __AddEditCategories,
    __AutoLogin: __AutoLogin
  });
  
function callMenuFunc(mob){
  let mnu = (mob.submenuActive >= 0)? mob.sm[mob.submenuActive] : mob.tm[mob.menuActive];
  mnu = this[mnu];  // turn mnu from a string into a function, this = xx.menuFunctions
  //test if the function exists and if so call it
  if (typeof mnu === 'function') {
    mnu();
  }
  // try { 
    // eval(mnu + '();');
  // } catch (e){
    // if (window['xx']['vars']['debugon'] === true) alert(e);
  // };
}
  

function __showBusinesses() {
  alert('Businesses');
}

function __showClassifieds() {
  alert('Classifieds');
}

function __AddEditCategories() {
  alert('Add/edit categories');
}

function __AutoLogin() {
  alert('AutoLogin');
}

});
  
 
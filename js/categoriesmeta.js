"use strict";
 
xx.module('menuFunctions', function (exports) {
  var categories;
  xx.imports.push(function () {
    categories = xx.categories;
  });

  exports.extend({
    __showBusinesses: __showBusinesses,
    __showClassifieds: __showClassifieds,
    __AddEditCategories: __AddEditCategories
  });

function __showBusinesses() {
  showCol1('Businesses');
}

function __showClassifieds() {
  showCol1('Classifieds');
}

function __AddEditCategories() {
  showCol1('Add/edit categories');
}

function showCol1(heading) {
  if ($('#catlistdiv').length === 0) {
    // must put the whole catlist
    let s = '<div id="col1">';
    s += '<h2 id="col1Heading" style="text-align:center;cursor:default;">' + heading + '</h2>';
    s += categories.col1Html4Categories();
    s += '</div>';
    $('#col1').replaceWith(s);
  } else {
    $('#col1Heading').text(heading);
  }
}

});
  
 
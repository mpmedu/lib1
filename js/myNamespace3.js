"use strict";

(function(wnd) {
  let baseNode = 'xx';
  /** * @constructor */function APod() {}
  let root = wnd[baseNode];
  if (root) {
    APod = root.constructor;
  } else {
    wnd[baseNode] = root = new APod();
  }

  let proto = APod.prototype;
  
  proto.module = function(path, fn) {
    // first add the nodes on the path
    path = path.replace(/ /g, '');
    let nodes = path.split('.');
    let i,ns = root;
    for (i = 0; i < nodes.length; i++) {
      if (ns[nodes[i]] === undefined) {
        ns[nodes[i]] = new APod();
      }
      ns = ns[nodes[i]];
    }
    // call the function, fn, using the last node in the path
    if (fn) fn(ns);
  };
  
  proto.extend = function(exports) {
    for (let prop in exports) {
      if (exports.hasOwnProperty(prop)) {
        this[prop] = exports[prop];
      }
    }
  };
  
/*   proto.require = function(path) {
    path = path.replace(/ /g, '');
    return eval(baseNode + '.' + path);
    // let nodes = path.split('.');
    // let i,ns = root;
    // for (i = 0; i < nodes.length; i++) {
      // ns = ns[nodes[i]];
    // }
    // return ns;
  };
 */  
  root.imports = new Array();    // imports are delayed until all the modules have been loaded
  proto.fixImports = function() {
    for (let i = 0; i < root.imports.length; i++) {
      root.imports[i]();
    }
    delete root.imports;
  }
  
  let vars = wnd[baseNode].vars = new APod();
  let s = wnd.location.href.toLowerCase();
  vars.URL_base = s.substring(0,s.lastIndexOf('/'));
  vars.isLocal = vars.debugon = (s.substr(s.indexOf('//') + 2, 9) === 'localhost');
  vars.bkcol = '#fff';  // default for setcolor is white
  // URL_lib must be set here for each project depending on the position of the /lib/ folder
  vars.URL_lib = vars.URL_base + "/lib/";  // when uploaded to the server
  s = wnd.location.pathname;
  s = s.substring(0,s.lastIndexOf('/')+1);
  vars.relPath = s.substr(1);
  vars.docPath = vars.relPath + 'docs/';
  
// *************** startblocks module  
xx.module('startblocks', function (exports) {
  exports.extend({
    clearInter:clearInter
  });
  
var ele = document.createElement("div");
//ele.style.background = 'green';
//ele.style.background = 'white';
ele.style.background = 'silver';
ele.style.background = 'blue';
ele.style.zIndex = 7000;
ele.style.position = 'absolute';
document.body.appendChild(ele);
var d = 30;
var n = 0,r = 0;;
var iw = window.innerWidth;
var ih = window.innerHeight;
ele.style.width = iw + 'px';
ele.style.height = ih + 'px';
var maxn = Math.trunc(iw / d);
var maxr = Math.trunc(ih / d);
var block;
var si2;
var si = setInterval(function () {
  block = document.createElement("div");
  block.className = "block";
  if ((n + r)% 2 === 1) {
    block.style.background = 'black';
  }
  block.style.top = (r * d) + 'px';
  block.style.left = (n * d) + 'px';
  block.style.width = d + 'px';
  block.style.height = d + 'px';
  block.style.display = 'inline-block';
  block.style.position = 'absolute';
  ele.appendChild(block);
  if (n === maxn) {
    n = 0;
    if (r++  >= maxr) {
      clearInterval(si);
      si2 = setInterval(function () {
        if (ele.style.background == 'green') {
          ele.style.background = 'white';
        }else {
          ele.style.background = 'green';
        }
      },500);
    }
  } else {
    n++;
  }
  
}, 15);
  
  function clearInter() {
    clearInterval(si2);
    clearInterval(si);
    var tmp = document.querySelector("body");
    tmp.removeChild(ele);
  }
  
  });
  
}(window));
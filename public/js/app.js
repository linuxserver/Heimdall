/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/ev-emitter/ev-emitter.js":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
 * EvEmitter v1.1.0
 * Lil' event emitter
 * MIT License
 */

/* jshint unused: true, undef: true, strict: true */

( function( global, factory ) {
  // universal module definition
  /* jshint strict: false */ /* globals define, module, window */
  if ( true ) {
    // AMD - RequireJS
    !(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) :
				__WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else if ( typeof module == 'object' && module.exports ) {
    // CommonJS - Browserify, Webpack
    module.exports = factory();
  } else {
    // Browser globals
    global.EvEmitter = factory();
  }

}( typeof window != 'undefined' ? window : this, function() {

"use strict";

function EvEmitter() {}

var proto = EvEmitter.prototype;

proto.on = function( eventName, listener ) {
  if ( !eventName || !listener ) {
    return;
  }
  // set events hash
  var events = this._events = this._events || {};
  // set listeners array
  var listeners = events[ eventName ] = events[ eventName ] || [];
  // only add once
  if ( listeners.indexOf( listener ) == -1 ) {
    listeners.push( listener );
  }

  return this;
};

proto.once = function( eventName, listener ) {
  if ( !eventName || !listener ) {
    return;
  }
  // add event
  this.on( eventName, listener );
  // set once flag
  // set onceEvents hash
  var onceEvents = this._onceEvents = this._onceEvents || {};
  // set onceListeners object
  var onceListeners = onceEvents[ eventName ] = onceEvents[ eventName ] || {};
  // set flag
  onceListeners[ listener ] = true;

  return this;
};

proto.off = function( eventName, listener ) {
  var listeners = this._events && this._events[ eventName ];
  if ( !listeners || !listeners.length ) {
    return;
  }
  var index = listeners.indexOf( listener );
  if ( index != -1 ) {
    listeners.splice( index, 1 );
  }

  return this;
};

proto.emitEvent = function( eventName, args ) {
  var listeners = this._events && this._events[ eventName ];
  if ( !listeners || !listeners.length ) {
    return;
  }
  // copy over to avoid interference if .off() in listener
  listeners = listeners.slice(0);
  args = args || [];
  // once stuff
  var onceListeners = this._onceEvents && this._onceEvents[ eventName ];

  for ( var i=0; i < listeners.length; i++ ) {
    var listener = listeners[i]
    var isOnce = onceListeners && onceListeners[ listener ];
    if ( isOnce ) {
      // remove listener
      // remove before trigger to prevent recursion
      this.off( eventName, listener );
      // unset once flag
      delete onceListeners[ listener ];
    }
    // trigger listener
    listener.apply( this, args );
  }

  return this;
};

proto.allOff = function() {
  delete this._events;
  delete this._onceEvents;
};

return EvEmitter;

}));


/***/ }),

/***/ "./node_modules/huebee/huebee.js":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
 * Huebee v2.0.0
 * 1-click color picker
 * MIT license
 * http://huebee.buzz
 * Copyright 2018 Metafizzy
 */

/*jshint browser: true, unused: true, undef: true */

( function( window, factory ) {
  // universal module definition
  /* globals define, module, require */
  if ( true ) {
    // AMD
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [
      __webpack_require__("./node_modules/ev-emitter/ev-emitter.js"),
      __webpack_require__("./node_modules/unipointer/unipointer.js"),
    ], __WEBPACK_AMD_DEFINE_RESULT__ = (function( EvEmitter, Unipointer ) {
      return factory( window, EvEmitter, Unipointer );
    }).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else if ( typeof module == 'object' && module.exports ) {
    // CommonJS
    module.exports = factory(
      window,
      require('ev-emitter'),
      require('unipointer')
    );
  } else {
    // browser global
    window.Huebee = factory(
      window,
      window.EvEmitter,
      window.Unipointer
    );
  }

}( window, function factory( window, EvEmitter, Unipointer ) {

function Huebee( anchor, options ) {
  // anchor
  anchor = getQueryElement( anchor );
  if ( !anchor ) {
    throw 'Bad element for Huebee: ' + anchor;
  }
  this.anchor = anchor;
  // options
  this.options = {};
  this.option( Huebee.defaults );
  this.option( options );
  // kick things off
  this.create();
}

Huebee.defaults = {
  hues: 12,
  hue0: 0,
  shades: 5,
  saturations: 3,
  notation: 'shortHex',
  setText: true,
  setBGColor: true,
};

var proto = Huebee.prototype = Object.create( EvEmitter.prototype );

proto.option = function( options ) {
  this.options = extend( this.options, options );
};

// globally unique identifiers
var GUID = 0;
// internal store of all Colcade intances
var instances = {};

proto.create = function() {
  // add guid for Colcade.data
  var guid = this.guid = ++GUID;
  this.anchor.huebeeGUID = guid;
  instances[ guid ] = this; // associate via id
  // properties
  this.setBGElems = this.getSetElems( this.options.setBGColor );
  this.setTextElems = this.getSetElems( this.options.setText );
  // events
  // HACK: this is getting ugly
  this.outsideCloseIt = this.outsideClose.bind( this );
  this.onDocKeydown = this.docKeydown.bind( this );
  this.closeIt = this.close.bind( this );
  this.openIt = this.open.bind( this );
  this.onElemTransitionend = this.elemTransitionend.bind( this );
  // open events
  this.isInputAnchor = this.anchor.nodeName == 'INPUT';
  if ( !this.options.staticOpen ) {
    this.anchor.addEventListener( 'click', this.openIt );
    this.anchor.addEventListener( 'focus', this.openIt );
  }
  // change event
  if ( this.isInputAnchor ) {
    this.anchor.addEventListener( 'input', this.inputInput.bind( this ) );
  }
  // create element
  var element = this.element = document.createElement('div');
  element.className = 'huebee ';
  element.className += this.options.staticOpen ? 'is-static-open ' :
    'is-hidden ';
  element.className += this.options.className || '';
  // create container
  var container = this.container = document.createElement('div');
  container.className = 'huebee__container';
  // do not blur if padding clicked
  function onContainerPointerStart( event ) {
    if ( event.target == container ) {
      event.preventDefault();
    }
  }
  container.addEventListener( 'mousedown', onContainerPointerStart );
  container.addEventListener( 'touchstart', onContainerPointerStart );
  // create canvas
  this.createCanvas();
  // create cursor
  this.cursor = document.createElement('div');
  this.cursor.className = 'huebee__cursor is-hidden';
  container.appendChild( this.cursor );
  // create close button
  this.createCloseButton();

  element.appendChild( container );
  // set relative position on parent
  if ( !this.options.staticOpen ) {
    var parentStyle = getComputedStyle( this.anchor.parentNode );
    if ( parentStyle.position != 'relative' && parentStyle.position != 'absolute' ) {
      this.anchor.parentNode.style.position = 'relative';
    }
  }

  // satY
  var hues = this.options.hues;
  var customColors = this.options.customColors;
  var customLength = customColors && customColors.length;
  // y position where saturation grid starts
  this.satY = customLength ? Math.ceil( customLength/hues ) + 1 : 0;
  // colors
  this.updateColors();
  this.setAnchorColor();
  if ( this.options.staticOpen ) {
    this.open();
  }
};

proto.getSetElems = function( option ) {
  if ( option === true ) {
    return [ this.anchor ];
  } else if ( typeof option == 'string' ) {
    return document.querySelectorAll( option );
  }
};

proto.createCanvas = function() {
  var canvas = this.canvas = document.createElement('canvas');
  canvas.className = 'huebee__canvas';
  this.ctx = canvas.getContext('2d');
  // canvas pointer events
  var canvasPointer = this.canvasPointer = new Unipointer();
  canvasPointer._bindStartEvent( canvas );
  canvasPointer.on( 'pointerDown', this.canvasPointerDown.bind( this ) );
  canvasPointer.on( 'pointerMove', this.canvasPointerMove.bind( this ) );
  this.container.appendChild( canvas );
};

var svgURI = 'http://www.w3.org/2000/svg';

proto.createCloseButton = function() {
  if ( this.options.staticOpen ) {
    return;
  }
  var svg = document.createElementNS( svgURI, 'svg');
  svg.setAttribute( 'class', 'huebee__close-button' );
  svg.setAttribute( 'viewBox', '0 0 24 24' );
  svg.setAttribute( 'width', '24' );
  svg.setAttribute( 'height', '24' );
  var path = document.createElementNS( svgURI, 'path');
  path.setAttribute( 'd', 'M 7,7 L 17,17 M 17,7 L 7,17' );
  path.setAttribute( 'class', 'huebee__close-button__x' );
  svg.appendChild( path );
  svg.addEventListener( 'click', this.closeIt );
  this.container.appendChild( svg );
};

proto.updateColors = function() {
  // hash of color, h, s, l according to x,y grid position
  // [x,y] = { color, h, s, l }
  this.swatches = {};
  // hash of gridX,gridY position according to color
  // [#09F] = { x, y }
  this.colorGrid = {};
  this.updateColorModer();

  var shades = this.options.shades;
  var sats = this.options.saturations;
  var hues = this.options.hues;
  var customColors = this.options.customColors;

  // render custom colors
  if ( customColors && customColors.length ) {
    var customI = 0;
    customColors.forEach( function( color ) {
      var x = customI % hues;
      var y = Math.floor( customI/hues );
      var swatch = getSwatch( color );
      if ( swatch ) {
        this.addSwatch( swatch, x, y );
        customI++;
      }
    }.bind( this ) );
  }

  // render saturation grids
  for ( var i=0; i < sats; i++ ) {
    var sat = 1 - i/sats;
    var yOffset = shades*i + this.satY;
    this.updateSaturationGrid( i, sat, yOffset );
  }

  // render grays
  for ( i=0; i < shades+2; i++ ) {
    var lum = 1 - i/(shades+1);
    var color = this.colorModer( 0, 0, lum );
    var swatch = getSwatch( color );
    this.addSwatch( swatch, hues+1, i );
  }
};

proto.updateSaturationGrid = function( i, sat, yOffset ) {
  var shades = this.options.shades;
  var hues = this.options.hues;
  var hue0 = this.options.hue0;
  for ( var row = 0; row < shades; row++ ) {
    for ( var col = 0; col < hues; col++ ) {
      var hue = Math.round( col * 360/hues + hue0 ) % 360;
      var lum = 1 - (row+1) / (shades+1);
      var color = this.colorModer( hue, sat, lum );
      var swatch = getSwatch( color );
      var gridY = row + yOffset;
      this.addSwatch( swatch, col, gridY );
    }
  }
};

proto.addSwatch = function( swatch, gridX, gridY ) {
  // add swatch color to hash
  this.swatches[ gridX + ',' + gridY ] = swatch;
  // add color to colorGrid
  this.colorGrid[ swatch.color.toUpperCase() ] = {
    x: gridX,
    y: gridY,
  };
};

var colorModers = {
  hsl: function( h, s, l ) {
    s = Math.round( s * 100 );
    l = Math.round( l * 100 );
    return 'hsl(' + h + ', ' + s + '%, ' + l + '%)';
  },
  hex: hsl2hex,
  shortHex: function( h, s, l ) {
    var hex = hsl2hex( h, s, l );
    return roundHex( hex );
  }
};

proto.updateColorModer = function() {
  this.colorModer = colorModers[ this.options.notation ] || colorModers.shortHex;
};

proto.renderColors = function() {
  var gridSize = this.gridSize*2;
  for ( var position in this.swatches ) {
    var swatch = this.swatches[ position ];
    var duple = position.split(',');
    var gridX = duple[0];
    var gridY = duple[1];
    this.ctx.fillStyle = swatch.color;
    this.ctx.fillRect( gridX*gridSize, gridY*gridSize, gridSize, gridSize );
  }
};

proto.setAnchorColor = function() {
  if ( this.isInputAnchor ) {
    this.setColor( this.anchor.value );
  }
};

// ----- events ----- //

var docElem = document.documentElement;

proto.open = function() {
  /* jshint unused: false */
  if ( this.isOpen ) {
    return;
  }
  var anchor = this.anchor;
  var elem = this.element;
  if ( !this.options.staticOpen ) {
    elem.style.left = anchor.offsetLeft + 'px';
    elem.style.top = anchor.offsetTop + anchor.offsetHeight + 'px';
  }
  this.bindOpenEvents( true );
  elem.removeEventListener( 'transitionend', this.onElemTransitionend );
  // add huebee to DOM
  anchor.parentNode.insertBefore( elem, anchor.nextSibling );
  // measurements
  var duration = getComputedStyle( elem ).transitionDuration;
  this.hasTransition = duration && duration != 'none' && parseFloat( duration );

  this.isOpen = true;
  this.updateSizes();
  this.renderColors();
  this.setAnchorColor();

  // trigger reflow for transition
  var h = elem.offsetHeight;
  elem.classList.remove('is-hidden');
};

proto.bindOpenEvents = function( isAdd ) {
  if ( this.options.staticOpen ) {
    return;
  }
  var method = ( isAdd ? 'add' : 'remove' ) + 'EventListener';
  docElem[ method]( 'mousedown', this.outsideCloseIt );
  docElem[ method]( 'touchstart', this.outsideCloseIt );
  document[ method ]( 'focusin', this.outsideCloseIt );
  document[ method ]( 'keydown', this.onDocKeydown );
  this.anchor[ method ]( 'blur', this.closeIt );
};

proto.updateSizes = function() {
  var hues = this.options.hues;
  var shades = this.options.shades;
  var sats = this.options.saturations;

  this.cursorBorder = parseInt( getComputedStyle( this.cursor ).borderTopWidth, 10 );
  this.gridSize = Math.round( this.cursor.offsetWidth - this.cursorBorder*2 );
  this.canvasOffset = {
    x: this.canvas.offsetLeft,
    y: this.canvas.offsetTop,
  };
  var height = Math.max( shades*sats + this.satY, shades+2 );
  var width = this.gridSize * (hues+2);
  this.canvas.width = width * 2;
  this.canvas.style.width = width + 'px';
  this.canvas.height = this.gridSize * height * 2;
};

// close if target is not anchor or element
proto.outsideClose = function( event ) {
  var isAnchor = this.anchor.contains( event.target );
  var isElement = this.element.contains( event.target );
  if ( !isAnchor && !isElement ) {
    this.close();
  }
};

var closeKeydowns = {
  13: true, // enter
  27: true, // esc
};

proto.docKeydown = function( event ) {
  if ( closeKeydowns[ event.keyCode ] ) {
    this.close();
  }
};

var supportsTransitions = typeof docElem.style.transform == 'string';

proto.close = function() {
  if ( !this.isOpen ) {
    return;
  }

  if ( supportsTransitions && this.hasTransition ) {
    this.element.addEventListener( 'transitionend', this.onElemTransitionend );
  } else {
    this.remove();
  }
  this.element.classList.add('is-hidden');

  this.bindOpenEvents( false );
  this.isOpen = false;
};

proto.remove = function() {
  var parent = this.element.parentNode;
  if ( parent.contains( this.element ) ) {
    parent.removeChild( this.element );
  }
};

proto.elemTransitionend = function( event ) {
  if ( event.target != this.element ) {
    return;
  }
  this.element.removeEventListener( 'transitionend', this.onElemTransitionend );
  this.remove();
};

proto.inputInput = function() {
  this.setColor( this.anchor.value );
};

// ----- canvas pointer ----- //

proto.canvasPointerDown = function( event, pointer ) {
  event.preventDefault();
  this.updateOffset();
  this.canvasPointerChange( pointer );
};

proto.updateOffset = function() {
  var boundingRect = this.canvas.getBoundingClientRect();
  this.offset = {
    x: boundingRect.left + window.pageXOffset,
    y: boundingRect.top + window.pageYOffset,
  };
};

proto.canvasPointerMove = function( event, pointer ) {
  this.canvasPointerChange( pointer );
};

proto.canvasPointerChange = function( pointer ) {
  var x = Math.round( pointer.pageX - this.offset.x );
  var y = Math.round( pointer.pageY - this.offset.y );
  var gridSize = this.gridSize;
  var sx = Math.floor( x/gridSize );
  var sy = Math.floor( y/gridSize );

  var swatch = this.swatches[ sx + ',' + sy ];
  this.setSwatch( swatch );
};

// ----- select ----- //

proto.setColor = function( color ) {
  var swatch = getSwatch( color );
  this.setSwatch( swatch );
};

proto.setSwatch = function( swatch ) {
  var color = swatch && swatch.color;
  if ( !swatch ) {
    return;
  }
  var wasSameColor = color == this.color;
  // color properties
  this.color = color;
  this.hue = swatch.hue;
  this.sat = swatch.sat;
  this.lum = swatch.lum;
  // estimate if color can have dark or white text
  var lightness = this.lum - Math.cos( (this.hue+70) / 180*Math.PI ) * 0.15;
  this.isLight = lightness > 0.5;
  // cursor
  var gridPosition = this.colorGrid[ color.toUpperCase() ];
  this.updateCursor( gridPosition );
  // set texts & backgrounds
  this.setTexts();
  this.setBackgrounds();
  // event
  if ( !wasSameColor ) {
    this.emitEvent( 'change', [ color, swatch.hue, swatch.sat, swatch.lum ] );
  }
};

proto.setTexts = function() {
  if ( !this.setTextElems ) {
    return;
  }
  for ( var i=0; i < this.setTextElems.length; i++ ) {
    var elem = this.setTextElems[i];
    var property = elem.nodeName == 'INPUT' ? 'value' : 'textContent';
    elem[ property ] = this.color;
  }
};

proto.setBackgrounds = function() {
  if ( !this.setBGElems ) {
    return;
  }
  var textColor = this.isLight ? '#222' : 'white';
  for ( var i=0; i < this.setBGElems.length; i++ ) {
    var elem = this.setBGElems[i];
    elem.style.backgroundColor = this.color;
    elem.style.color = textColor;
  }
};

proto.updateCursor = function( position ) {
  if ( !this.isOpen ) {
    return;
  }
  // show cursor if color is on the grid
  var classMethod = position ? 'remove' : 'add';
  this.cursor.classList[ classMethod ]('is-hidden');

  if ( !position ) {
    return;
  }
  var gridSize = this.gridSize;
  var offset = this.canvasOffset;
  var border = this.cursorBorder;
  this.cursor.style.left = position.x*gridSize + offset.x - border + 'px';
  this.cursor.style.top = position.y*gridSize + offset.y - border + 'px';
};

// -------------------------- htmlInit -------------------------- //

var console = window.console;

function htmlInit() {
  var elems = document.querySelectorAll('[data-huebee]');
  for ( var i=0; i < elems.length; i++ ) {
    var elem = elems[i];
    var attr = elem.getAttribute('data-huebee');
    var options;
    try {
      options = attr && JSON.parse( attr );
    } catch ( error ) {
      // log error, do not initialize
      if ( console ) {
        console.error( 'Error parsing data-huebee on ' + elem.className +
          ': ' + error );
      }
      continue;
    }
    // initialize
    new Huebee( elem, options );
  }
}

var readyState = document.readyState;
if ( readyState == 'complete' || readyState == 'interactive' ) {
  htmlInit();
} else {
  document.addEventListener( 'DOMContentLoaded', htmlInit );
}

// -------------------------- Huebee.data -------------------------- //

Huebee.data = function( elem ) {
  elem = getQueryElement( elem );
  var id = elem && elem.huebeeGUID;
  return id && instances[ id ];
};

// -------------------------- getSwatch -------------------------- //

// proxy canvas used to check colors
var proxyCanvas = document.createElement('canvas');
proxyCanvas.width = proxyCanvas.height = 1;
var proxyCtx = proxyCanvas.getContext('2d');

function getSwatch( color ) {
  // check that color value is valid
  proxyCtx.clearRect( 0, 0, 1, 1 );
  proxyCtx.fillStyle = '#010203'; // reset value
  proxyCtx.fillStyle = color;
  proxyCtx.fillRect( 0, 0, 1, 1 );
  var data = proxyCtx.getImageData( 0, 0, 1, 1 ).data;
  // convert to array, imageData not array, #10
  data = [ data[0], data[1], data[2], data[3] ];
  if ( data.join(',') == '1,2,3,255' ) {
    // invalid color
    return;
  }
  // convert rgb to hsl
  var hsl = rgb2hsl.apply( this, data );
  return {
    color: color.trim(),
    hue: hsl[0],
    sat: hsl[1],
    lum: hsl[2],
  };
}

// -------------------------- utils -------------------------- //

function extend( a, b ) {
  for ( var prop in b ) {
    a[ prop ] = b[ prop ];
  }
  return a;
}

function getQueryElement( elem ) {
  if ( typeof elem == 'string' ) {
    elem = document.querySelector( elem );
  }
  return elem;
}

function hsl2hex( h, s, l ) {
  var rgb = hsl2rgb( h, s, l );
  return rgb2hex( rgb );
}

// thx jfsiii
// https://github.com/jfsiii/chromath/blob/master/src/static.js#L312
function hsl2rgb(h, s, l) {

  var C = (1 - Math.abs(2*l-1)) * s;
  var hp = h/60;
  var X = C * (1-Math.abs(hp%2-1));
  var rgb, m;

  switch ( Math.floor(hp) ) {
    case 0:  rgb = [C,X,0]; break;
    case 1:  rgb = [X,C,0]; break;
    case 2:  rgb = [0,C,X]; break;
    case 3:  rgb = [0,X,C]; break;
    case 4:  rgb = [X,0,C]; break;
    case 5:  rgb = [C,0,X]; break;
    default: rgb = [0,0,0];
  }

  m = l - (C/2);
  rgb = rgb.map( function( v ) {
    return v + m;
  });

  return rgb;
}

function rgb2hsl(r, g, b) {
  r /= 255; g /= 255; b /= 255;

  var M = Math.max(r, g, b);
  var m = Math.min(r, g, b);
  var C = M - m;
  var L = 0.5*(M + m);
  var S = (C === 0) ? 0 : C/(1-Math.abs(2*L-1));

  var h;
  if (C === 0) h = 0; // spec'd as undefined, but usually set to 0
  else if (M === r) h = ((g-b)/C) % 6;
  else if (M === g) h = ((b-r)/C) + 2;
  else if (M === b) h = ((r-g)/C) + 4;

  var H = 60 * h;

  return [H, parseFloat(S), parseFloat(L)];
}

function rgb2hex( rgb ) {
  var hex = rgb.map( function( value ) {
    value = Math.round( value * 255 );
    var hexNum = value.toString(16).toUpperCase();
    // left pad 0
    hexNum = hexNum.length < 2 ? '0' + hexNum : hexNum;
    return hexNum;
  });

  return '#' + hex.join('');
}

// #123456 -> #135
// grab first digit from hex
// not mathematically accurate, but makes for better palette
function roundHex( hex ) {
  return '#' + hex[1] + hex[3] + hex[5];
}

// --------------------------  -------------------------- //

return Huebee;

}));


/***/ }),

/***/ "./node_modules/unipointer/unipointer.js":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * Unipointer v2.3.0
 * base class for doing one thing with pointer event
 * MIT license
 */

/*jshint browser: true, undef: true, unused: true, strict: true */

( function( window, factory ) {
  // universal module definition
  /* jshint strict: false */ /*global define, module, require */
  if ( true ) {
    // AMD
    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [
      __webpack_require__("./node_modules/ev-emitter/ev-emitter.js")
    ], __WEBPACK_AMD_DEFINE_RESULT__ = (function( EvEmitter ) {
      return factory( window, EvEmitter );
    }).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  } else if ( typeof module == 'object' && module.exports ) {
    // CommonJS
    module.exports = factory(
      window,
      require('ev-emitter')
    );
  } else {
    // browser global
    window.Unipointer = factory(
      window,
      window.EvEmitter
    );
  }

}( window, function factory( window, EvEmitter ) {

'use strict';

function noop() {}

function Unipointer() {}

// inherit EvEmitter
var proto = Unipointer.prototype = Object.create( EvEmitter.prototype );

proto.bindStartEvent = function( elem ) {
  this._bindStartEvent( elem, true );
};

proto.unbindStartEvent = function( elem ) {
  this._bindStartEvent( elem, false );
};

/**
 * Add or remove start event
 * @param {Boolean} isAdd - remove if falsey
 */
proto._bindStartEvent = function( elem, isAdd ) {
  // munge isAdd, default to true
  isAdd = isAdd === undefined ? true : isAdd;
  var bindMethod = isAdd ? 'addEventListener' : 'removeEventListener';

  // default to mouse events
  var startEvent = 'mousedown';
  if ( window.PointerEvent ) {
    // Pointer Events
    startEvent = 'pointerdown';
  } else if ( 'ontouchstart' in window ) {
    // Touch Events. iOS Safari
    startEvent = 'touchstart';
  }
  elem[ bindMethod ]( startEvent, this );
};

// trigger handler methods for events
proto.handleEvent = function( event ) {
  var method = 'on' + event.type;
  if ( this[ method ] ) {
    this[ method ]( event );
  }
};

// returns the touch that we're keeping track of
proto.getTouch = function( touches ) {
  for ( var i=0; i < touches.length; i++ ) {
    var touch = touches[i];
    if ( touch.identifier == this.pointerIdentifier ) {
      return touch;
    }
  }
};

// ----- start event ----- //

proto.onmousedown = function( event ) {
  // dismiss clicks from right or middle buttons
  var button = event.button;
  if ( button && ( button !== 0 && button !== 1 ) ) {
    return;
  }
  this._pointerDown( event, event );
};

proto.ontouchstart = function( event ) {
  this._pointerDown( event, event.changedTouches[0] );
};

proto.onpointerdown = function( event ) {
  this._pointerDown( event, event );
};

/**
 * pointer start
 * @param {Event} event
 * @param {Event or Touch} pointer
 */
proto._pointerDown = function( event, pointer ) {
  // dismiss right click and other pointers
  // button = 0 is okay, 1-4 not
  if ( event.button || this.isPointerDown ) {
    return;
  }

  this.isPointerDown = true;
  // save pointer identifier to match up touch events
  this.pointerIdentifier = pointer.pointerId !== undefined ?
    // pointerId for pointer events, touch.indentifier for touch events
    pointer.pointerId : pointer.identifier;

  this.pointerDown( event, pointer );
};

proto.pointerDown = function( event, pointer ) {
  this._bindPostStartEvents( event );
  this.emitEvent( 'pointerDown', [ event, pointer ] );
};

// hash of events to be bound after start event
var postStartEvents = {
  mousedown: [ 'mousemove', 'mouseup' ],
  touchstart: [ 'touchmove', 'touchend', 'touchcancel' ],
  pointerdown: [ 'pointermove', 'pointerup', 'pointercancel' ],
};

proto._bindPostStartEvents = function( event ) {
  if ( !event ) {
    return;
  }
  // get proper events to match start event
  var events = postStartEvents[ event.type ];
  // bind events to node
  events.forEach( function( eventName ) {
    window.addEventListener( eventName, this );
  }, this );
  // save these arguments
  this._boundPointerEvents = events;
};

proto._unbindPostStartEvents = function() {
  // check for _boundEvents, in case dragEnd triggered twice (old IE8 bug)
  if ( !this._boundPointerEvents ) {
    return;
  }
  this._boundPointerEvents.forEach( function( eventName ) {
    window.removeEventListener( eventName, this );
  }, this );

  delete this._boundPointerEvents;
};

// ----- move event ----- //

proto.onmousemove = function( event ) {
  this._pointerMove( event, event );
};

proto.onpointermove = function( event ) {
  if ( event.pointerId == this.pointerIdentifier ) {
    this._pointerMove( event, event );
  }
};

proto.ontouchmove = function( event ) {
  var touch = this.getTouch( event.changedTouches );
  if ( touch ) {
    this._pointerMove( event, touch );
  }
};

/**
 * pointer move
 * @param {Event} event
 * @param {Event or Touch} pointer
 * @private
 */
proto._pointerMove = function( event, pointer ) {
  this.pointerMove( event, pointer );
};

// public
proto.pointerMove = function( event, pointer ) {
  this.emitEvent( 'pointerMove', [ event, pointer ] );
};

// ----- end event ----- //


proto.onmouseup = function( event ) {
  this._pointerUp( event, event );
};

proto.onpointerup = function( event ) {
  if ( event.pointerId == this.pointerIdentifier ) {
    this._pointerUp( event, event );
  }
};

proto.ontouchend = function( event ) {
  var touch = this.getTouch( event.changedTouches );
  if ( touch ) {
    this._pointerUp( event, touch );
  }
};

/**
 * pointer up
 * @param {Event} event
 * @param {Event or Touch} pointer
 * @private
 */
proto._pointerUp = function( event, pointer ) {
  this._pointerDone();
  this.pointerUp( event, pointer );
};

// public
proto.pointerUp = function( event, pointer ) {
  this.emitEvent( 'pointerUp', [ event, pointer ] );
};

// ----- pointer done ----- //

// triggered on pointer up & pointer cancel
proto._pointerDone = function() {
  this._pointerReset();
  this._unbindPostStartEvents();
  this.pointerDone();
};

proto._pointerReset = function() {
  // reset properties
  this.isPointerDown = false;
  delete this.pointerIdentifier;
};

proto.pointerDone = noop;

// ----- pointer cancel ----- //

proto.onpointercancel = function( event ) {
  if ( event.pointerId == this.pointerIdentifier ) {
    this._pointerCancel( event, event );
  }
};

proto.ontouchcancel = function( event ) {
  var touch = this.getTouch( event.changedTouches );
  if ( touch ) {
    this._pointerCancel( event, touch );
  }
};

/**
 * pointer cancel
 * @param {Event} event
 * @param {Event or Touch} pointer
 * @private
 */
proto._pointerCancel = function( event, pointer ) {
  this._pointerDone();
  this.pointerCancel( event, pointer );
};

// public
proto.pointerCancel = function( event, pointer ) {
  this.emitEvent( 'pointerCancel', [ event, pointer ] );
};

// -----  ----- //

// utility function for getting x/y coords from event
Unipointer.getPointerPoint = function( pointer ) {
  return {
    x: pointer.pageX,
    y: pointer.pageY
  };
};

// -----  ----- //

return Unipointer;

}));


/***/ }),

/***/ "./resources/assets/js/app.js":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_huebee__ = __webpack_require__("./node_modules/huebee/huebee.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_huebee___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_huebee__);

window.Huebee = __WEBPACK_IMPORTED_MODULE_0_huebee___default.a;

$.when($.ready).then(function () {

    if ($('.message-container').length) {
        setTimeout(function () {
            $('.message-container').fadeOut();
        }, 3500);
    }

    if ($('.livestats-container').length) {
        $('.livestats-container').each(function (index) {
            var id = $(this).data('id');
            var dataonly = $(this).data('dataonly');
            var increaseby = dataonly == 1 ? 20000 : 1000;
            var container = $(this);
            var max_timer = 30000;
            var timer = 5000;
            (function worker() {
                $.ajax({
                    url: '/get_stats/' + id,
                    dataType: 'json',
                    success: function success(data) {
                        container.html(data.html);
                        if (data.status == 'active') timer = increaseby;else {
                            if (timer < max_timer) timer += 2000;
                        }
                    },
                    complete: function complete() {
                        // Schedule the next request when the current one's complete
                        setTimeout(worker, timer);
                    }
                });
            })();
        });
    }

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#appimage img').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#upload').change(function () {
        readURL(this);
    });
    /*$(".droppable").droppable({
        tolerance: "intersect",
        drop: function( event, ui ) {
            var tag = $( this ).data('id');
            var item = $( ui.draggable ).data('id');
             $.get('tag/add/'+tag+'/'+item, function(data) {
                if(data == 1) {
                    $( ui.draggable ).remove();
                } else {
                    alert('not added');
                }
            });
         }
      });*/

    $('#sortable').sortable({
        stop: function stop(event, ui) {
            var idsInOrder = $('#sortable').sortable('toArray', {
                attribute: 'data-id'
            });
            $.post('/order', { order: idsInOrder });
        }

    });
    $('#sortable').sortable('disable');

    $('#app').on('click', '#config-button', function (e) {
        e.preventDefault();
        var app = $('#app');
        var active = app.hasClass('header');
        app.toggleClass('header');
        if (active) {
            $('.add-item').hide();
            $('.item-edit').hide();
            $('#app').removeClass('sidebar');
            $('#sortable').sortable('disable');
        } else {
            $('#sortable').sortable('enable');
            setTimeout(function () {
                $('.add-item').fadeIn();
                $('.item-edit').fadeIn();
            }, 350);
        }
    }).on('click', '#add-item, #pin-item', function (e) {
        e.preventDefault();
        var app = $('#app');
        var active = app.hasClass('sidebar');
        app.toggleClass('sidebar');
    }).on('click', '.close-sidenav', function (e) {
        e.preventDefault();
        var app = $('#app');
        app.removeClass('sidebar');
    }).on('click', '#test_config', function (e) {
        e.preventDefault();
        var apiurl = $('#create input[name=url]').val();

        var override_url = $('#create input[name="config[override_url]"').val();
        if (override_url.length && override_url != '') {
            apiurl = override_url;
        }

        var data = {};
        data['url'] = apiurl;
        $('input.config-item').each(function (index) {
            var config = $(this).data('config');
            data[config] = $(this).val();
        });

        $.post('/test_config', { data: data }, function (data) {
            alert(data);
        });
    });
    $('#pinlist').on('click', 'a', function (e) {
        e.preventDefault();
        var current = $(this);
        var id = current.data('id');
        $.get('items/pintoggle/' + id + '/true', function (data) {
            var inner = $(data).filter('#sortable').html();
            $('#sortable').html(inner);
            current.toggleClass('active');
        });
    });
});

/***/ }),

/***/ "./resources/assets/js/huebee.js":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_LOCAL_MODULE_0__, __WEBPACK_LOCAL_MODULE_0__factory, __WEBPACK_LOCAL_MODULE_0__module;var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_LOCAL_MODULE_1__;var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/**
 * Huebee PACKAGED v2.0.0
 * 1-click color picker
 * MIT license
 * http://huebee.buzz
 * Copyright 2018 Metafizzy
 */

!function (t, e) {
   true ? !(__WEBPACK_LOCAL_MODULE_0__factory = (e), (__WEBPACK_LOCAL_MODULE_0__module = { id: "ev-emitter/ev-emitter", exports: {}, loaded: false }), __WEBPACK_LOCAL_MODULE_0__ = (typeof __WEBPACK_LOCAL_MODULE_0__factory === 'function' ? (__WEBPACK_LOCAL_MODULE_0__factory.call(__WEBPACK_LOCAL_MODULE_0__module.exports, __webpack_require__, __WEBPACK_LOCAL_MODULE_0__module.exports, __WEBPACK_LOCAL_MODULE_0__module)) : __WEBPACK_LOCAL_MODULE_0__factory), (__WEBPACK_LOCAL_MODULE_0__module.loaded = true), __WEBPACK_LOCAL_MODULE_0__ === undefined && (__WEBPACK_LOCAL_MODULE_0__ = __WEBPACK_LOCAL_MODULE_0__module.exports)) : "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) && module.exports ? module.exports = e() : t.EvEmitter = e();
}("undefined" != typeof window ? window : this, function () {
  function t() {}var e = t.prototype;return e.on = function (t, e) {
    if (t && e) {
      var n = this._events = this._events || {},
          i = n[t] = n[t] || [];return i.indexOf(e) == -1 && i.push(e), this;
    }
  }, e.once = function (t, e) {
    if (t && e) {
      this.on(t, e);var n = this._onceEvents = this._onceEvents || {},
          i = n[t] = n[t] || {};return i[e] = !0, this;
    }
  }, e.off = function (t, e) {
    var n = this._events && this._events[t];if (n && n.length) {
      var i = n.indexOf(e);return i != -1 && n.splice(i, 1), this;
    }
  }, e.emitEvent = function (t, e) {
    var n = this._events && this._events[t];if (n && n.length) {
      var i = 0,
          o = n[i];e = e || [];for (var s = this._onceEvents && this._onceEvents[t]; o;) {
        var r = s && s[o];r && (this.off(t, o), delete s[o]), o.apply(this, e), i += r ? 0 : 1, o = n[i];
      }return this;
    }
  }, t;
}), function (t, e) {
   true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__WEBPACK_LOCAL_MODULE_0__], __WEBPACK_LOCAL_MODULE_1__ = ((function (n) {
    return e(t, n);
  }).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__))) : "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) && module.exports ? module.exports = e(t, require("ev-emitter")) : t.Unipointer = e(t, t.EvEmitter);
}(window, function (t, e) {
  function n() {}function i() {}var o = i.prototype = Object.create(e.prototype);o.bindStartEvent = function (t) {
    this._bindStartEvent(t, !0);
  }, o.unbindStartEvent = function (t) {
    this._bindStartEvent(t, !1);
  }, o._bindStartEvent = function (e, n) {
    n = void 0 === n || !!n;var i = n ? "addEventListener" : "removeEventListener";t.navigator.pointerEnabled ? e[i]("pointerdown", this) : t.navigator.msPointerEnabled ? e[i]("MSPointerDown", this) : (e[i]("mousedown", this), e[i]("touchstart", this));
  }, o.handleEvent = function (t) {
    var e = "on" + t.type;this[e] && this[e](t);
  }, o.getTouch = function (t) {
    for (var e = 0; e < t.length; e++) {
      var n = t[e];if (n.identifier == this.pointerIdentifier) return n;
    }
  }, o.onmousedown = function (t) {
    var e = t.button;e && 0 !== e && 1 !== e || this._pointerDown(t, t);
  }, o.ontouchstart = function (t) {
    this._pointerDown(t, t.changedTouches[0]);
  }, o.onMSPointerDown = o.onpointerdown = function (t) {
    this._pointerDown(t, t);
  }, o._pointerDown = function (t, e) {
    this.isPointerDown || (this.isPointerDown = !0, this.pointerIdentifier = void 0 !== e.pointerId ? e.pointerId : e.identifier, this.pointerDown(t, e));
  }, o.pointerDown = function (t, e) {
    this._bindPostStartEvents(t), this.emitEvent("pointerDown", [t, e]);
  };var s = { mousedown: ["mousemove", "mouseup"], touchstart: ["touchmove", "touchend", "touchcancel"], pointerdown: ["pointermove", "pointerup", "pointercancel"], MSPointerDown: ["MSPointerMove", "MSPointerUp", "MSPointerCancel"] };return o._bindPostStartEvents = function (e) {
    if (e) {
      var n = s[e.type];n.forEach(function (e) {
        t.addEventListener(e, this);
      }, this), this._boundPointerEvents = n;
    }
  }, o._unbindPostStartEvents = function () {
    this._boundPointerEvents && (this._boundPointerEvents.forEach(function (e) {
      t.removeEventListener(e, this);
    }, this), delete this._boundPointerEvents);
  }, o.onmousemove = function (t) {
    this._pointerMove(t, t);
  }, o.onMSPointerMove = o.onpointermove = function (t) {
    t.pointerId == this.pointerIdentifier && this._pointerMove(t, t);
  }, o.ontouchmove = function (t) {
    var e = this.getTouch(t.changedTouches);e && this._pointerMove(t, e);
  }, o._pointerMove = function (t, e) {
    this.pointerMove(t, e);
  }, o.pointerMove = function (t, e) {
    this.emitEvent("pointerMove", [t, e]);
  }, o.onmouseup = function (t) {
    this._pointerUp(t, t);
  }, o.onMSPointerUp = o.onpointerup = function (t) {
    t.pointerId == this.pointerIdentifier && this._pointerUp(t, t);
  }, o.ontouchend = function (t) {
    var e = this.getTouch(t.changedTouches);e && this._pointerUp(t, e);
  }, o._pointerUp = function (t, e) {
    this._pointerDone(), this.pointerUp(t, e);
  }, o.pointerUp = function (t, e) {
    this.emitEvent("pointerUp", [t, e]);
  }, o._pointerDone = function () {
    this.isPointerDown = !1, delete this.pointerIdentifier, this._unbindPostStartEvents(), this.pointerDone();
  }, o.pointerDone = n, o.onMSPointerCancel = o.onpointercancel = function (t) {
    t.pointerId == this.pointerIdentifier && this._pointerCancel(t, t);
  }, o.ontouchcancel = function (t) {
    var e = this.getTouch(t.changedTouches);e && this._pointerCancel(t, e);
  }, o._pointerCancel = function (t, e) {
    this._pointerDone(), this.pointerCancel(t, e);
  }, o.pointerCancel = function (t, e) {
    this.emitEvent("pointerCancel", [t, e]);
  }, i.getPointerPoint = function (t) {
    return { x: t.pageX, y: t.pageY };
  }, i;
}), function (t, e) {
   true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__WEBPACK_LOCAL_MODULE_0__, __WEBPACK_LOCAL_MODULE_1__], __WEBPACK_AMD_DEFINE_RESULT__ = (function (n, i) {
    return e(t, n, i);
  }).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) && module.exports ? module.exports = e(t, require("ev-emitter"), require("unipointer")) : t.Huebee = e(t, t.EvEmitter, t.Unipointer);
}(window, function (t, e, n) {
  function i(t, e) {
    if (t = h(t), !t) throw "Bad element for Huebee: " + t;this.anchor = t, this.options = {}, this.option(i.defaults), this.option(e), this.create();
  }function o() {
    for (var t = document.querySelectorAll("[data-huebee]"), e = 0; e < t.length; e++) {
      var n,
          o = t[e],
          s = o.getAttribute("data-huebee");try {
        n = s && JSON.parse(s);
      } catch (t) {
        C && C.error("Error parsing data-huebee on " + o.className + ": " + t);continue;
      }new i(o, n);
    }
  }function s(t) {
    _.clearRect(0, 0, 1, 1), _.fillStyle = "#010203", _.fillStyle = t, _.fillRect(0, 0, 1, 1);var e = _.getImageData(0, 0, 1, 1).data;if (e = [e[0], e[1], e[2], e[3]], "1,2,3,255" != e.join(",")) {
      var n = u.apply(this, e);return { color: t.trim(), hue: n[0], sat: n[1], lum: n[2] };
    }
  }function r(t, e) {
    for (var n in e) {
      t[n] = e[n];
    }return t;
  }function h(t) {
    return "string" == typeof t && (t = document.querySelector(t)), t;
  }function a(t, e, n) {
    var i = c(t, e, n);return d(i);
  }function c(t, e, n) {
    var i,
        o,
        s = (1 - Math.abs(2 * n - 1)) * e,
        r = t / 60,
        h = s * (1 - Math.abs(r % 2 - 1));switch (Math.floor(r)) {case 0:
        i = [s, h, 0];break;case 1:
        i = [h, s, 0];break;case 2:
        i = [0, s, h];break;case 3:
        i = [0, h, s];break;case 4:
        i = [h, 0, s];break;case 5:
        i = [s, 0, h];break;default:
        i = [0, 0, 0];}return o = n - s / 2, i = i.map(function (t) {
      return t + o;
    });
  }function u(t, e, n) {
    t /= 255, e /= 255, n /= 255;var i,
        o = Math.max(t, e, n),
        s = Math.min(t, e, n),
        r = o - s,
        h = .5 * (o + s),
        a = 0 === r ? 0 : r / (1 - Math.abs(2 * h - 1));0 === r ? i = 0 : o === t ? i = (e - n) / r % 6 : o === e ? i = (n - t) / r + 2 : o === n && (i = (t - e) / r + 4);var c = 60 * i;return [c, parseFloat(a), parseFloat(h)];
  }function d(t) {
    var e = t.map(function (t) {
      t = Math.round(255 * t);var e = t.toString(16).toUpperCase();return e = e.length < 2 ? "0" + e : e;
    });return "#" + e.join("");
  }function p(t) {
    return "#" + t[1] + t[3] + t[5];
  }i.defaults = { hues: 12, hue0: 0, shades: 5, saturations: 3, notation: "shortHex", setText: !0, setBGColor: !0 };var f = i.prototype = Object.create(e.prototype);f.option = function (t) {
    this.options = r(this.options, t);
  };var v = 0,
      l = {};f.create = function () {
    function t(t) {
      t.target == i && t.preventDefault();
    }var e = this.guid = ++v;this.anchor.huebeeGUID = e, l[e] = this, this.setBGElems = this.getSetElems(this.options.setBGColor), this.setTextElems = this.getSetElems(this.options.setText), this.outsideCloseIt = this.outsideClose.bind(this), this.onDocKeydown = this.docKeydown.bind(this), this.closeIt = this.close.bind(this), this.openIt = this.open.bind(this), this.onElemTransitionend = this.elemTransitionend.bind(this), this.isInputAnchor = "INPUT" == this.anchor.nodeName, this.options.staticOpen || (this.anchor.addEventListener("click", this.openIt), this.anchor.addEventListener("focus", this.openIt)), this.isInputAnchor && this.anchor.addEventListener("input", this.inputInput.bind(this));var n = this.element = document.createElement("div");n.className = "huebee ", n.className += this.options.staticOpen ? "is-static-open " : "is-hidden ", n.className += this.options.className || "";var i = this.container = document.createElement("div");if (i.className = "huebee__container", i.addEventListener("mousedown", t), i.addEventListener("touchstart", t), this.createCanvas(), this.cursor = document.createElement("div"), this.cursor.className = "huebee__cursor is-hidden", i.appendChild(this.cursor), this.createCloseButton(), n.appendChild(i), !this.options.staticOpen) {
      var o = getComputedStyle(this.anchor.parentNode);"relative" != o.position && "absolute" != o.position && (this.anchor.parentNode.style.position = "relative");
    }var s = this.options.hues,
        r = this.options.customColors,
        h = r && r.length;this.satY = h ? Math.ceil(h / s) + 1 : 0, this.updateColors(), this.setAnchorColor(), this.options.staticOpen && this.open();
  }, f.getSetElems = function (t) {
    return t === !0 ? [this.anchor] : "string" == typeof t ? document.querySelectorAll(t) : void 0;
  }, f.createCanvas = function () {
    var t = this.canvas = document.createElement("canvas");t.className = "huebee__canvas", this.ctx = t.getContext("2d");var e = this.canvasPointer = new n();e._bindStartEvent(t), e.on("pointerDown", this.canvasPointerDown.bind(this)), e.on("pointerMove", this.canvasPointerMove.bind(this)), this.container.appendChild(t);
  };var m = "http://www.w3.org/2000/svg";f.createCloseButton = function () {
    if (!this.options.staticOpen) {
      var t = document.createElementNS(m, "svg");t.setAttribute("class", "huebee__close-button"), t.setAttribute("viewBox", "0 0 24 24"), t.setAttribute("width", "24"), t.setAttribute("height", "24");var e = document.createElementNS(m, "path");e.setAttribute("d", "M 7,7 L 17,17 M 17,7 L 7,17"), e.setAttribute("class", "huebee__close-button__x"), t.appendChild(e), t.addEventListener("click", this.closeIt), this.container.appendChild(t);
    }
  }, f.updateColors = function () {
    this.swatches = {}, this.colorGrid = {}, this.updateColorModer();var t = this.options.shades,
        e = this.options.saturations,
        n = this.options.hues,
        i = this.options.customColors;if (i && i.length) {
      var o = 0;i.forEach(function (t) {
        var e = o % n,
            i = Math.floor(o / n),
            r = s(t);r && (this.addSwatch(r, e, i), o++);
      }.bind(this));
    }for (var r = 0; r < e; r++) {
      var h = 1 - r / e,
          a = t * r + this.satY;this.updateSaturationGrid(r, h, a);
    }for (r = 0; r < t + 2; r++) {
      var c = 1 - r / (t + 1),
          u = this.colorModer(0, 0, c),
          d = s(u);this.addSwatch(d, n + 1, r);
    }
  }, f.updateSaturationGrid = function (t, e, n) {
    for (var i = this.options.shades, o = this.options.hues, r = this.options.hue0, h = 0; h < i; h++) {
      for (var a = 0; a < o; a++) {
        var c = Math.round(360 * a / o + r) % 360,
            u = 1 - (h + 1) / (i + 1),
            d = this.colorModer(c, e, u),
            p = s(d),
            f = h + n;this.addSwatch(p, a, f);
      }
    }
  }, f.addSwatch = function (t, e, n) {
    this.swatches[e + "," + n] = t, this.colorGrid[t.color.toUpperCase()] = { x: e, y: n };
  };var E = { hsl: function hsl(t, e, n) {
      return e = Math.round(100 * e), n = Math.round(100 * n), "hsl(" + t + ", " + e + "%, " + n + "%)";
    }, hex: a, shortHex: function shortHex(t, e, n) {
      var i = a(t, e, n);return p(i);
    } };f.updateColorModer = function () {
    this.colorModer = E[this.options.notation] || E.shortHex;
  }, f.renderColors = function () {
    var t = 2 * this.gridSize;for (var e in this.swatches) {
      var n = this.swatches[e],
          i = e.split(","),
          o = i[0],
          s = i[1];this.ctx.fillStyle = n.color, this.ctx.fillRect(o * t, s * t, t, t);
    }
  }, f.setAnchorColor = function () {
    this.isInputAnchor && this.setColor(this.anchor.value);
  };var g = document.documentElement;f.open = function () {
    if (!this.isOpen) {
      var t = this.anchor,
          e = this.element;this.options.staticOpen || (e.style.left = t.offsetLeft + "px", e.style.top = t.offsetTop + t.offsetHeight + "px"), this.bindOpenEvents(!0), e.removeEventListener("transitionend", this.onElemTransitionend), t.parentNode.insertBefore(e, t.nextSibling);var n = getComputedStyle(e).transitionDuration;this.hasTransition = n && "none" != n && parseFloat(n), this.isOpen = !0, this.updateSizes(), this.renderColors(), this.setAnchorColor();e.offsetHeight;e.classList.remove("is-hidden");
    }
  }, f.bindOpenEvents = function (t) {
    if (!this.options.staticOpen) {
      var e = (t ? "add" : "remove") + "EventListener";g[e]("mousedown", this.outsideCloseIt), g[e]("touchstart", this.outsideCloseIt), document[e]("focusin", this.outsideCloseIt), document[e]("keydown", this.onDocKeydown), this.anchor[e]("blur", this.closeIt);
    }
  }, f.updateSizes = function () {
    var t = this.options.hues,
        e = this.options.shades,
        n = this.options.saturations;this.cursorBorder = parseInt(getComputedStyle(this.cursor).borderTopWidth, 10), this.gridSize = Math.round(this.cursor.offsetWidth - 2 * this.cursorBorder), this.canvasOffset = { x: this.canvas.offsetLeft, y: this.canvas.offsetTop };var i = Math.max(e * n + this.satY, e + 2),
        o = this.gridSize * (t + 2);this.canvas.width = 2 * o, this.canvas.style.width = o + "px", this.canvas.height = this.gridSize * i * 2;
  }, f.outsideClose = function (t) {
    var e = this.anchor.contains(t.target),
        n = this.element.contains(t.target);e || n || this.close();
  };var b = { 13: !0, 27: !0 };f.docKeydown = function (t) {
    b[t.keyCode] && this.close();
  };var w = "string" == typeof g.style.transform;f.close = function () {
    this.isOpen && (w && this.hasTransition ? this.element.addEventListener("transitionend", this.onElemTransitionend) : this.remove(), this.element.classList.add("is-hidden"), this.bindOpenEvents(!1), this.isOpen = !1);
  }, f.remove = function () {
    var t = this.element.parentNode;t.contains(this.element) && t.removeChild(this.element);
  }, f.elemTransitionend = function (t) {
    t.target == this.element && (this.element.removeEventListener("transitionend", this.onElemTransitionend), this.remove());
  }, f.inputInput = function () {
    this.setColor(this.anchor.value);
  }, f.canvasPointerDown = function (t, e) {
    t.preventDefault(), this.updateOffset(), this.canvasPointerChange(e);
  }, f.updateOffset = function () {
    var e = this.canvas.getBoundingClientRect();this.offset = { x: e.left + t.pageXOffset, y: e.top + t.pageYOffset };
  }, f.canvasPointerMove = function (t, e) {
    this.canvasPointerChange(e);
  }, f.canvasPointerChange = function (t) {
    var e = Math.round(t.pageX - this.offset.x),
        n = Math.round(t.pageY - this.offset.y),
        i = this.gridSize,
        o = Math.floor(e / i),
        s = Math.floor(n / i),
        r = this.swatches[o + "," + s];this.setSwatch(r);
  }, f.setColor = function (t) {
    var e = s(t);this.setSwatch(e);
  }, f.setSwatch = function (t) {
    var e = t && t.color;if (t) {
      var n = e == this.color;this.color = e, this.hue = t.hue, this.sat = t.sat, this.lum = t.lum;var i = this.lum - .15 * Math.cos((this.hue + 70) / 180 * Math.PI);this.isLight = i > .5;var o = this.colorGrid[e.toUpperCase()];this.updateCursor(o), this.setTexts(), this.setBackgrounds(), n || this.emitEvent("change", [e, t.hue, t.sat, t.lum]);
    }
  }, f.setTexts = function () {
    if (this.setTextElems) for (var t = 0; t < this.setTextElems.length; t++) {
      var e = this.setTextElems[t],
          n = "INPUT" == e.nodeName ? "value" : "textContent";e[n] = this.color;
    }
  }, f.setBackgrounds = function () {
    if (this.setBGElems) for (var t = this.isLight ? "#222" : "white", e = 0; e < this.setBGElems.length; e++) {
      var n = this.setBGElems[e];n.style.backgroundColor = this.color, n.style.color = t;
    }
  }, f.updateCursor = function (t) {
    if (this.isOpen) {
      var e = t ? "remove" : "add";if (this.cursor.classList[e]("is-hidden"), t) {
        var n = this.gridSize,
            i = this.canvasOffset,
            o = this.cursorBorder;this.cursor.style.left = t.x * n + i.x - o + "px", this.cursor.style.top = t.y * n + i.y - o + "px";
      }
    }
  };var C = t.console,
      S = document.readyState;"complete" == S || "interactive" == S ? o() : document.addEventListener("DOMContentLoaded", o), i.data = function (t) {
    t = h(t);var e = t && t.huebeeGUID;return e && l[e];
  };var y = document.createElement("canvas");y.width = y.height = 1;var _ = y.getContext("2d");return i;
});

/***/ }),

/***/ "./resources/assets/sass/app.scss":
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("./resources/assets/js/huebee.js");
__webpack_require__("./resources/assets/js/app.js");
module.exports = __webpack_require__("./resources/assets/sass/app.scss");


/***/ })

/******/ });
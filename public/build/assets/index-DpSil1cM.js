// \0rolldown/runtime.js
var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __commonJSMin = (cb, mod) => () => (mod || (cb((mod = { exports: {} }).exports, mod, mod.exports), cb = null), mod.exports);
var __copyProps = (to, from, except, desc) => {
	if (from && typeof from === "object" || typeof from === "function") for (var keys = __getOwnPropNames(from), i = 0, n = keys.length, key; i < n; i++) {
		key = keys[i];
		if (!__hasOwnProp.call(to, key) && key !== except) __defProp(to, key, {
			get: ((k) => from[k]).bind(null, key),
			enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable
		});
	}
	return to;
};
var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(isNodeMode || !mod || !mod.__esModule || !__hasOwnProp.call(mod, "default") ? __defProp(target, "default", {
	value: mod,
	enumerable: true
}) : target, mod));
// node_modules/sortablejs/modular/sortable.esm.js
/**!
* Sortable 1.15.7
* @author	RubaXa   <trash@rubaxa.org>
* @author	owenm    <owen23355@gmail.com>
* @license MIT
*/
function _defineProperty$1(e, r, t) {
	return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
		value: t,
		enumerable: !0,
		configurable: !0,
		writable: !0
	}) : e[r] = t, e;
}
function _extends() {
	return _extends = Object.assign ? Object.assign.bind() : function(n) {
		for (var e = 1; e < arguments.length; e++) {
			var t = arguments[e];
			for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]);
		}
		return n;
	}, _extends.apply(null, arguments);
}
function ownKeys$1(e, r) {
	var t = Object.keys(e);
	if (Object.getOwnPropertySymbols) {
		var o = Object.getOwnPropertySymbols(e);
		r && (o = o.filter(function(r) {
			return Object.getOwnPropertyDescriptor(e, r).enumerable;
		})), t.push.apply(t, o);
	}
	return t;
}
function _objectSpread2$1(e) {
	for (var r = 1; r < arguments.length; r++) {
		var t = null != arguments[r] ? arguments[r] : {};
		r % 2 ? ownKeys$1(Object(t), !0).forEach(function(r) {
			_defineProperty$1(e, r, t[r]);
		}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$1(Object(t)).forEach(function(r) {
			Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
		});
	}
	return e;
}
function _objectWithoutProperties(e, t) {
	if (null == e) return {};
	var o, r, i = _objectWithoutPropertiesLoose(e, t);
	if (Object.getOwnPropertySymbols) {
		var n = Object.getOwnPropertySymbols(e);
		for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]);
	}
	return i;
}
function _objectWithoutPropertiesLoose(r, e) {
	if (null == r) return {};
	var t = {};
	for (var n in r) if ({}.hasOwnProperty.call(r, n)) {
		if (-1 !== e.indexOf(n)) continue;
		t[n] = r[n];
	}
	return t;
}
function _toPrimitive(t, r) {
	if ("object" != typeof t || !t) return t;
	var e = t[Symbol.toPrimitive];
	if (void 0 !== e) {
		var i = e.call(t, r || "default");
		if ("object" != typeof i) return i;
		throw new TypeError("@@toPrimitive must return a primitive value.");
	}
	return ("string" === r ? String : Number)(t);
}
function _toPropertyKey(t) {
	var i = _toPrimitive(t, "string");
	return "symbol" == typeof i ? i : i + "";
}
function _typeof$1(o) {
	"@babel/helpers - typeof";
	return _typeof$1 = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(o) {
		return typeof o;
	} : function(o) {
		return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
	}, _typeof$1(o);
}
var version = "1.15.7";
function userAgent(pattern) {
	if (typeof window !== "undefined" && window.navigator) return !!/*@__PURE__*/ navigator.userAgent.match(pattern);
}
var IE11OrLess = userAgent(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i);
var Edge = userAgent(/Edge/i);
var FireFox = userAgent(/firefox/i);
var Safari = userAgent(/safari/i) && !userAgent(/chrome/i) && !userAgent(/android/i);
var IOS = userAgent(/iP(ad|od|hone)/i);
var ChromeForAndroid = userAgent(/chrome/i) && userAgent(/android/i);
var captureMode = {
	capture: false,
	passive: false
};
function on(el, event, fn) {
	el.addEventListener(event, fn, !IE11OrLess && captureMode);
}
function off(el, event, fn) {
	el.removeEventListener(event, fn, !IE11OrLess && captureMode);
}
function matches(el, selector) {
	if (!selector) return;
	selector[0] === ">" && (selector = selector.substring(1));
	if (el) try {
		if (el.matches) return el.matches(selector);
		else if (el.msMatchesSelector) return el.msMatchesSelector(selector);
		else if (el.webkitMatchesSelector) return el.webkitMatchesSelector(selector);
	} catch (_) {
		return false;
	}
	return false;
}
function getParentOrHost(el) {
	return el.host && el !== document && el.host.nodeType && el.host !== el ? el.host : el.parentNode;
}
function closest(el, selector, ctx, includeCTX) {
	if (el) {
		ctx = ctx || document;
		do {
			if (selector != null && (selector[0] === ">" ? el.parentNode === ctx && matches(el, selector) : matches(el, selector)) || includeCTX && el === ctx) return el;
			if (el === ctx) break;
		} while (el = getParentOrHost(el));
	}
	return null;
}
var R_SPACE = /\s+/g;
function toggleClass(el, name, state) {
	if (el && name) {
		if (el.classList) el.classList[state ? "add" : "remove"](name);
		else el.className = ((" " + el.className + " ").replace(R_SPACE, " ").replace(" " + name + " ", " ") + (state ? " " + name : "")).replace(R_SPACE, " ");
	}
}
function css(el, prop, val) {
	var style = el && el.style;
	if (style) {
		if (val === void 0) {
			if (document.defaultView && document.defaultView.getComputedStyle) val = document.defaultView.getComputedStyle(el, "");
			else if (el.currentStyle) val = el.currentStyle;
			return prop === void 0 ? val : val[prop];
		} else {
			if (!(prop in style) && prop.indexOf("webkit") === -1) prop = "-webkit-" + prop;
			style[prop] = val + (typeof val === "string" ? "" : "px");
		}
	}
}
function matrix(el, selfOnly) {
	var appliedTransforms = "";
	if (typeof el === "string") appliedTransforms = el;
	else do {
		var transform = css(el, "transform");
		if (transform && transform !== "none") appliedTransforms = transform + " " + appliedTransforms;
	} while (!selfOnly && (el = el.parentNode));
	var matrixFn = window.DOMMatrix || window.WebKitCSSMatrix || window.CSSMatrix || window.MSCSSMatrix;
	return matrixFn && new matrixFn(appliedTransforms);
}
function find(ctx, tagName, iterator) {
	if (ctx) {
		var list = ctx.getElementsByTagName(tagName), i = 0, n = list.length;
		if (iterator) for (; i < n; i++) iterator(list[i], i);
		return list;
	}
	return [];
}
function getWindowScrollingElement() {
	var scrollingElement = document.scrollingElement;
	if (scrollingElement) return scrollingElement;
	else return document.documentElement;
}
/**
* Returns the "bounding client rect" of given element
* @param  {HTMLElement} el                       The element whose boundingClientRect is wanted
* @param  {[Boolean]} relativeToContainingBlock  Whether the rect should be relative to the containing block of (including) the container
* @param  {[Boolean]} relativeToNonStaticParent  Whether the rect should be relative to the relative parent of (including) the contaienr
* @param  {[Boolean]} undoScale                  Whether the container's scale() should be undone
* @param  {[HTMLElement]} container              The parent the element will be placed in
* @return {Object}                               The boundingClientRect of el, with specified adjustments
*/
function getRect(el, relativeToContainingBlock, relativeToNonStaticParent, undoScale, container) {
	if (!el.getBoundingClientRect && el !== window) return;
	var elRect, top, left, bottom, right, height, width;
	if (el !== window && el.parentNode && el !== getWindowScrollingElement()) {
		elRect = el.getBoundingClientRect();
		top = elRect.top;
		left = elRect.left;
		bottom = elRect.bottom;
		right = elRect.right;
		height = elRect.height;
		width = elRect.width;
	} else {
		top = 0;
		left = 0;
		bottom = window.innerHeight;
		right = window.innerWidth;
		height = window.innerHeight;
		width = window.innerWidth;
	}
	if ((relativeToContainingBlock || relativeToNonStaticParent) && el !== window) {
		container = container || el.parentNode;
		if (!IE11OrLess) do
			if (container && container.getBoundingClientRect && (css(container, "transform") !== "none" || relativeToNonStaticParent && css(container, "position") !== "static")) {
				var containerRect = container.getBoundingClientRect();
				top -= containerRect.top + parseInt(css(container, "border-top-width"));
				left -= containerRect.left + parseInt(css(container, "border-left-width"));
				bottom = top + elRect.height;
				right = left + elRect.width;
				break;
			}
		while (container = container.parentNode);
	}
	if (undoScale && el !== window) {
		var elMatrix = matrix(container || el), scaleX = elMatrix && elMatrix.a, scaleY = elMatrix && elMatrix.d;
		if (elMatrix) {
			top /= scaleY;
			left /= scaleX;
			width /= scaleX;
			height /= scaleY;
			bottom = top + height;
			right = left + width;
		}
	}
	return {
		top,
		left,
		bottom,
		right,
		width,
		height
	};
}
/**
* Checks if a side of an element is scrolled past a side of its parents
* @param  {HTMLElement}  el           The element who's side being scrolled out of view is in question
* @param  {String}       elSide       Side of the element in question ('top', 'left', 'right', 'bottom')
* @param  {String}       parentSide   Side of the parent in question ('top', 'left', 'right', 'bottom')
* @return {HTMLElement}               The parent scroll element that the el's side is scrolled past, or null if there is no such element
*/
function isScrolledPast(el, elSide, parentSide) {
	var parent = getParentAutoScrollElement(el, true), elSideVal = getRect(el)[elSide];
	while (parent) {
		var parentSideVal = getRect(parent)[parentSide], visible = void 0;
		if (parentSide === "top" || parentSide === "left") visible = elSideVal >= parentSideVal;
		else visible = elSideVal <= parentSideVal;
		if (!visible) return parent;
		if (parent === getWindowScrollingElement()) break;
		parent = getParentAutoScrollElement(parent, false);
	}
	return false;
}
/**
* Gets nth child of el, ignoring hidden children, sortable's elements (does not ignore clone if it's visible)
* and non-draggable elements
* @param  {HTMLElement} el       The parent element
* @param  {Number} childNum      The index of the child
* @param  {Object} options       Parent Sortable's options
* @return {HTMLElement}          The child at index childNum, or null if not found
*/
function getChild(el, childNum, options, includeDragEl) {
	var currentChild = 0, i = 0, children = el.children;
	while (i < children.length) {
		if (children[i].style.display !== "none" && children[i] !== Sortable$1.ghost && (includeDragEl || children[i] !== Sortable$1.dragged) && closest(children[i], options.draggable, el, false)) {
			if (currentChild === childNum) return children[i];
			currentChild++;
		}
		i++;
	}
	return null;
}
/**
* Gets the last child in the el, ignoring ghostEl or invisible elements (clones)
* @param  {HTMLElement} el       Parent element
* @param  {selector} selector    Any other elements that should be ignored
* @return {HTMLElement}          The last child, ignoring ghostEl
*/
function lastChild(el, selector) {
	var last = el.lastElementChild;
	while (last && (last === Sortable$1.ghost || css(last, "display") === "none" || selector && !matches(last, selector))) last = last.previousElementSibling;
	return last || null;
}
/**
* Returns the index of an element within its parent for a selected set of
* elements
* @param  {HTMLElement} el
* @param  {selector} selector
* @return {number}
*/
function index(el, selector) {
	var index = 0;
	if (!el || !el.parentNode) return -1;
	while (el = el.previousElementSibling) if (el.nodeName.toUpperCase() !== "TEMPLATE" && el !== Sortable$1.clone && (!selector || matches(el, selector))) index++;
	return index;
}
/**
* Returns the scroll offset of the given element, added with all the scroll offsets of parent elements.
* The value is returned in real pixels.
* @param  {HTMLElement} el
* @return {Array}             Offsets in the format of [left, top]
*/
function getRelativeScrollOffset(el) {
	var offsetLeft = 0, offsetTop = 0, winScroller = getWindowScrollingElement();
	if (el) do {
		var elMatrix = matrix(el), scaleX = elMatrix.a, scaleY = elMatrix.d;
		offsetLeft += el.scrollLeft * scaleX;
		offsetTop += el.scrollTop * scaleY;
	} while (el !== winScroller && (el = el.parentNode));
	return [offsetLeft, offsetTop];
}
/**
* Returns the index of the object within the given array
* @param  {Array} arr   Array that may or may not hold the object
* @param  {Object} obj  An object that has a key-value pair unique to and identical to a key-value pair in the object you want to find
* @return {Number}      The index of the object in the array, or -1
*/
function indexOfObject(arr, obj) {
	for (var i in arr) {
		if (!arr.hasOwnProperty(i)) continue;
		for (var key in obj) if (obj.hasOwnProperty(key) && obj[key] === arr[i][key]) return Number(i);
	}
	return -1;
}
function getParentAutoScrollElement(el, includeSelf) {
	if (!el || !el.getBoundingClientRect) return getWindowScrollingElement();
	var elem = el;
	var gotSelf = false;
	do
		if (elem.clientWidth < elem.scrollWidth || elem.clientHeight < elem.scrollHeight) {
			var elemCSS = css(elem);
			if (elem.clientWidth < elem.scrollWidth && (elemCSS.overflowX == "auto" || elemCSS.overflowX == "scroll") || elem.clientHeight < elem.scrollHeight && (elemCSS.overflowY == "auto" || elemCSS.overflowY == "scroll")) {
				if (!elem.getBoundingClientRect || elem === document.body) return getWindowScrollingElement();
				if (gotSelf || includeSelf) return elem;
				gotSelf = true;
			}
		}
	while (elem = elem.parentNode);
	return getWindowScrollingElement();
}
function extend(dst, src) {
	if (dst && src) {
		for (var key in src) if (src.hasOwnProperty(key)) dst[key] = src[key];
	}
	return dst;
}
function isRectEqual(rect1, rect2) {
	return Math.round(rect1.top) === Math.round(rect2.top) && Math.round(rect1.left) === Math.round(rect2.left) && Math.round(rect1.height) === Math.round(rect2.height) && Math.round(rect1.width) === Math.round(rect2.width);
}
var _throttleTimeout;
function throttle(callback, ms) {
	return function() {
		if (!_throttleTimeout) {
			var args = arguments, _this = this;
			if (args.length === 1) callback.call(_this, args[0]);
			else callback.apply(_this, args);
			_throttleTimeout = setTimeout(function() {
				_throttleTimeout = void 0;
			}, ms);
		}
	};
}
function cancelThrottle() {
	clearTimeout(_throttleTimeout);
	_throttleTimeout = void 0;
}
function scrollBy(el, x, y) {
	el.scrollLeft += x;
	el.scrollTop += y;
}
function clone(el) {
	var Polymer = window.Polymer;
	var $ = window.jQuery || window.Zepto;
	if (Polymer && Polymer.dom) return Polymer.dom(el).cloneNode(true);
	else if ($) return $(el).clone(true)[0];
	else return el.cloneNode(true);
}
function getChildContainingRectFromElement(container, options, ghostEl) {
	var rect = {};
	Array.from(container.children).forEach(function(child) {
		var _rect$left, _rect$top, _rect$right, _rect$bottom;
		if (!closest(child, options.draggable, container, false) || child.animated || child === ghostEl) return;
		var childRect = getRect(child);
		rect.left = Math.min((_rect$left = rect.left) !== null && _rect$left !== void 0 ? _rect$left : Infinity, childRect.left);
		rect.top = Math.min((_rect$top = rect.top) !== null && _rect$top !== void 0 ? _rect$top : Infinity, childRect.top);
		rect.right = Math.max((_rect$right = rect.right) !== null && _rect$right !== void 0 ? _rect$right : -Infinity, childRect.right);
		rect.bottom = Math.max((_rect$bottom = rect.bottom) !== null && _rect$bottom !== void 0 ? _rect$bottom : -Infinity, childRect.bottom);
	});
	rect.width = rect.right - rect.left;
	rect.height = rect.bottom - rect.top;
	rect.x = rect.left;
	rect.y = rect.top;
	return rect;
}
var expando = "Sortable" + (/* @__PURE__ */ new Date()).getTime();
function AnimationStateManager() {
	var animationStates = [], animationCallbackId;
	return {
		captureAnimationState: function captureAnimationState() {
			animationStates = [];
			if (!this.options.animation) return;
			[].slice.call(this.el.children).forEach(function(child) {
				if (css(child, "display") === "none" || child === Sortable$1.ghost) return;
				animationStates.push({
					target: child,
					rect: getRect(child)
				});
				var fromRect = _objectSpread2$1({}, animationStates[animationStates.length - 1].rect);
				if (child.thisAnimationDuration) {
					var childMatrix = matrix(child, true);
					if (childMatrix) {
						fromRect.top -= childMatrix.f;
						fromRect.left -= childMatrix.e;
					}
				}
				child.fromRect = fromRect;
			});
		},
		addAnimationState: function addAnimationState(state) {
			animationStates.push(state);
		},
		removeAnimationState: function removeAnimationState(target) {
			animationStates.splice(indexOfObject(animationStates, { target }), 1);
		},
		animateAll: function animateAll(callback) {
			var _this = this;
			if (!this.options.animation) {
				clearTimeout(animationCallbackId);
				if (typeof callback === "function") callback();
				return;
			}
			var animating = false, animationTime = 0;
			animationStates.forEach(function(state) {
				var time = 0, target = state.target, fromRect = target.fromRect, toRect = getRect(target), prevFromRect = target.prevFromRect, prevToRect = target.prevToRect, animatingRect = state.rect, targetMatrix = matrix(target, true);
				if (targetMatrix) {
					toRect.top -= targetMatrix.f;
					toRect.left -= targetMatrix.e;
				}
				target.toRect = toRect;
				if (target.thisAnimationDuration) {
					if (isRectEqual(prevFromRect, toRect) && !isRectEqual(fromRect, toRect) && (animatingRect.top - toRect.top) / (animatingRect.left - toRect.left) === (fromRect.top - toRect.top) / (fromRect.left - toRect.left)) time = calculateRealTime(animatingRect, prevFromRect, prevToRect, _this.options);
				}
				if (!isRectEqual(toRect, fromRect)) {
					target.prevFromRect = fromRect;
					target.prevToRect = toRect;
					if (!time) time = _this.options.animation;
					_this.animate(target, animatingRect, toRect, time);
				}
				if (time) {
					animating = true;
					animationTime = Math.max(animationTime, time);
					clearTimeout(target.animationResetTimer);
					target.animationResetTimer = setTimeout(function() {
						target.animationTime = 0;
						target.prevFromRect = null;
						target.fromRect = null;
						target.prevToRect = null;
						target.thisAnimationDuration = null;
					}, time);
					target.thisAnimationDuration = time;
				}
			});
			clearTimeout(animationCallbackId);
			if (!animating) {
				if (typeof callback === "function") callback();
			} else animationCallbackId = setTimeout(function() {
				if (typeof callback === "function") callback();
			}, animationTime);
			animationStates = [];
		},
		animate: function animate(target, currentRect, toRect, duration) {
			if (duration) {
				css(target, "transition", "");
				css(target, "transform", "");
				var elMatrix = matrix(this.el), scaleX = elMatrix && elMatrix.a, scaleY = elMatrix && elMatrix.d, translateX = (currentRect.left - toRect.left) / (scaleX || 1), translateY = (currentRect.top - toRect.top) / (scaleY || 1);
				target.animatingX = !!translateX;
				target.animatingY = !!translateY;
				css(target, "transform", "translate3d(" + translateX + "px," + translateY + "px,0)");
				this.forRepaintDummy = repaint(target);
				css(target, "transition", "transform " + duration + "ms" + (this.options.easing ? " " + this.options.easing : ""));
				css(target, "transform", "translate3d(0,0,0)");
				typeof target.animated === "number" && clearTimeout(target.animated);
				target.animated = setTimeout(function() {
					css(target, "transition", "");
					css(target, "transform", "");
					target.animated = false;
					target.animatingX = false;
					target.animatingY = false;
				}, duration);
			}
		}
	};
}
function repaint(target) {
	return target.offsetWidth;
}
function calculateRealTime(animatingRect, fromRect, toRect, options) {
	return Math.sqrt(Math.pow(fromRect.top - animatingRect.top, 2) + Math.pow(fromRect.left - animatingRect.left, 2)) / Math.sqrt(Math.pow(fromRect.top - toRect.top, 2) + Math.pow(fromRect.left - toRect.left, 2)) * options.animation;
}
var plugins = [];
var defaults = { initializeByDefault: true };
var PluginManager = {
	mount: function mount(plugin) {
		for (var option in defaults) if (defaults.hasOwnProperty(option) && !(option in plugin)) plugin[option] = defaults[option];
		plugins.forEach(function(p) {
			if (p.pluginName === plugin.pluginName) throw "Sortable: Cannot mount plugin ".concat(plugin.pluginName, " more than once");
		});
		plugins.push(plugin);
	},
	pluginEvent: function pluginEvent(eventName, sortable, evt) {
		var _this = this;
		this.eventCanceled = false;
		evt.cancel = function() {
			_this.eventCanceled = true;
		};
		var eventNameGlobal = eventName + "Global";
		plugins.forEach(function(plugin) {
			if (!sortable[plugin.pluginName]) return;
			if (sortable[plugin.pluginName][eventNameGlobal]) sortable[plugin.pluginName][eventNameGlobal](_objectSpread2$1({ sortable }, evt));
			if (sortable.options[plugin.pluginName] && sortable[plugin.pluginName][eventName]) sortable[plugin.pluginName][eventName](_objectSpread2$1({ sortable }, evt));
		});
	},
	initializePlugins: function initializePlugins(sortable, el, defaults, options) {
		plugins.forEach(function(plugin) {
			var pluginName = plugin.pluginName;
			if (!sortable.options[pluginName] && !plugin.initializeByDefault) return;
			var initialized = new plugin(sortable, el, sortable.options);
			initialized.sortable = sortable;
			initialized.options = sortable.options;
			sortable[pluginName] = initialized;
			_extends(defaults, initialized.defaults);
		});
		for (var option in sortable.options) {
			if (!sortable.options.hasOwnProperty(option)) continue;
			var modified = this.modifyOption(sortable, option, sortable.options[option]);
			if (typeof modified !== "undefined") sortable.options[option] = modified;
		}
	},
	getEventProperties: function getEventProperties(name, sortable) {
		var eventProperties = {};
		plugins.forEach(function(plugin) {
			if (typeof plugin.eventProperties !== "function") return;
			_extends(eventProperties, plugin.eventProperties.call(sortable[plugin.pluginName], name));
		});
		return eventProperties;
	},
	modifyOption: function modifyOption(sortable, name, value) {
		var modifiedValue;
		plugins.forEach(function(plugin) {
			if (!sortable[plugin.pluginName]) return;
			if (plugin.optionListeners && typeof plugin.optionListeners[name] === "function") modifiedValue = plugin.optionListeners[name].call(sortable[plugin.pluginName], value);
		});
		return modifiedValue;
	}
};
function dispatchEvent(_ref) {
	var sortable = _ref.sortable, rootEl = _ref.rootEl, name = _ref.name, targetEl = _ref.targetEl, cloneEl = _ref.cloneEl, toEl = _ref.toEl, fromEl = _ref.fromEl, oldIndex = _ref.oldIndex, newIndex = _ref.newIndex, oldDraggableIndex = _ref.oldDraggableIndex, newDraggableIndex = _ref.newDraggableIndex, originalEvent = _ref.originalEvent, putSortable = _ref.putSortable, extraEventProperties = _ref.extraEventProperties;
	sortable = sortable || rootEl && rootEl[expando];
	if (!sortable) return;
	var evt, options = sortable.options, onName = "on" + name.charAt(0).toUpperCase() + name.substr(1);
	if (window.CustomEvent && !IE11OrLess && !Edge) evt = new CustomEvent(name, {
		bubbles: true,
		cancelable: true
	});
	else {
		evt = document.createEvent("Event");
		evt.initEvent(name, true, true);
	}
	evt.to = toEl || rootEl;
	evt.from = fromEl || rootEl;
	evt.item = targetEl || rootEl;
	evt.clone = cloneEl;
	evt.oldIndex = oldIndex;
	evt.newIndex = newIndex;
	evt.oldDraggableIndex = oldDraggableIndex;
	evt.newDraggableIndex = newDraggableIndex;
	evt.originalEvent = originalEvent;
	evt.pullMode = putSortable ? putSortable.lastPutMode : void 0;
	var allEventProperties = _objectSpread2$1(_objectSpread2$1({}, extraEventProperties), PluginManager.getEventProperties(name, sortable));
	for (var option in allEventProperties) evt[option] = allEventProperties[option];
	if (rootEl) rootEl.dispatchEvent(evt);
	if (options[onName]) options[onName].call(sortable, evt);
}
var _excluded = ["evt"];
var pluginEvent = function pluginEvent(eventName, sortable) {
	var _ref = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {}, originalEvent = _ref.evt, data = _objectWithoutProperties(_ref, _excluded);
	PluginManager.pluginEvent.bind(Sortable$1)(eventName, sortable, _objectSpread2$1({
		dragEl,
		parentEl,
		ghostEl,
		rootEl,
		nextEl,
		lastDownEl,
		cloneEl,
		cloneHidden,
		dragStarted: moved,
		putSortable,
		activeSortable: Sortable$1.active,
		originalEvent,
		oldIndex,
		oldDraggableIndex,
		newIndex,
		newDraggableIndex,
		hideGhostForTarget: _hideGhostForTarget,
		unhideGhostForTarget: _unhideGhostForTarget,
		cloneNowHidden: function cloneNowHidden() {
			cloneHidden = true;
		},
		cloneNowShown: function cloneNowShown() {
			cloneHidden = false;
		},
		dispatchSortableEvent: function dispatchSortableEvent(name) {
			_dispatchEvent({
				sortable,
				name,
				originalEvent
			});
		}
	}, data));
};
function _dispatchEvent(info) {
	dispatchEvent(_objectSpread2$1({
		putSortable,
		cloneEl,
		targetEl: dragEl,
		rootEl,
		oldIndex,
		oldDraggableIndex,
		newIndex,
		newDraggableIndex
	}, info));
}
var dragEl;
var parentEl;
var ghostEl;
var rootEl;
var nextEl;
var lastDownEl;
var cloneEl;
var cloneHidden;
var oldIndex;
var newIndex;
var oldDraggableIndex;
var newDraggableIndex;
var activeGroup;
var putSortable;
var awaitingDragStarted = false;
var ignoreNextClick = false;
var sortables = [];
var tapEvt;
var touchEvt;
var lastDx;
var lastDy;
var tapDistanceLeft;
var tapDistanceTop;
var moved;
var lastTarget;
var lastDirection;
var pastFirstInvertThresh = false;
var isCircumstantialInvert = false;
var targetMoveDistance;
var ghostRelativeParent;
var ghostRelativeParentInitialScroll = [];
var _silent = false;
var savedInputChecked = [];
/** @const */
var documentExists = typeof document !== "undefined";
var PositionGhostAbsolutely = IOS;
var CSSFloatProperty = Edge || IE11OrLess ? "cssFloat" : "float";
var supportDraggable = documentExists && !ChromeForAndroid && !IOS && "draggable" in document.createElement("div");
var supportCssPointerEvents = function() {
	if (!documentExists) return;
	if (IE11OrLess) return false;
	var el = document.createElement("x");
	el.style.cssText = "pointer-events:auto";
	return el.style.pointerEvents === "auto";
}();
var _detectDirection = function _detectDirection(el, options) {
	var elCSS = css(el), elWidth = parseInt(elCSS.width) - parseInt(elCSS.paddingLeft) - parseInt(elCSS.paddingRight) - parseInt(elCSS.borderLeftWidth) - parseInt(elCSS.borderRightWidth), child1 = getChild(el, 0, options), child2 = getChild(el, 1, options), firstChildCSS = child1 && css(child1), secondChildCSS = child2 && css(child2), firstChildWidth = firstChildCSS && parseInt(firstChildCSS.marginLeft) + parseInt(firstChildCSS.marginRight) + getRect(child1).width, secondChildWidth = secondChildCSS && parseInt(secondChildCSS.marginLeft) + parseInt(secondChildCSS.marginRight) + getRect(child2).width;
	if (elCSS.display === "flex") return elCSS.flexDirection === "column" || elCSS.flexDirection === "column-reverse" ? "vertical" : "horizontal";
	if (elCSS.display === "grid") return elCSS.gridTemplateColumns.split(" ").length <= 1 ? "vertical" : "horizontal";
	if (child1 && firstChildCSS["float"] && firstChildCSS["float"] !== "none") {
		var touchingSideChild2 = firstChildCSS["float"] === "left" ? "left" : "right";
		return child2 && (secondChildCSS.clear === "both" || secondChildCSS.clear === touchingSideChild2) ? "vertical" : "horizontal";
	}
	return child1 && (firstChildCSS.display === "block" || firstChildCSS.display === "flex" || firstChildCSS.display === "table" || firstChildCSS.display === "grid" || firstChildWidth >= elWidth && elCSS[CSSFloatProperty] === "none" || child2 && elCSS[CSSFloatProperty] === "none" && firstChildWidth + secondChildWidth > elWidth) ? "vertical" : "horizontal";
};
var _dragElInRowColumn = function _dragElInRowColumn(dragRect, targetRect, vertical) {
	var dragElS1Opp = vertical ? dragRect.left : dragRect.top, dragElS2Opp = vertical ? dragRect.right : dragRect.bottom, dragElOppLength = vertical ? dragRect.width : dragRect.height, targetS1Opp = vertical ? targetRect.left : targetRect.top, targetS2Opp = vertical ? targetRect.right : targetRect.bottom, targetOppLength = vertical ? targetRect.width : targetRect.height;
	return dragElS1Opp === targetS1Opp || dragElS2Opp === targetS2Opp || dragElS1Opp + dragElOppLength / 2 === targetS1Opp + targetOppLength / 2;
};
var _detectNearestEmptySortable = function _detectNearestEmptySortable(x, y) {
	var ret;
	sortables.some(function(sortable) {
		var threshold = sortable[expando].options.emptyInsertThreshold;
		if (!threshold || lastChild(sortable)) return;
		var rect = getRect(sortable), insideHorizontally = x >= rect.left - threshold && x <= rect.right + threshold, insideVertically = y >= rect.top - threshold && y <= rect.bottom + threshold;
		if (insideHorizontally && insideVertically) return ret = sortable;
	});
	return ret;
};
var _prepareGroup = function _prepareGroup(options) {
	function toFn(value, pull) {
		return function(to, from, dragEl, evt) {
			var sameGroup = to.options.group.name && from.options.group.name && to.options.group.name === from.options.group.name;
			if (value == null && (pull || sameGroup)) return true;
			else if (value == null || value === false) return false;
			else if (pull && value === "clone") return value;
			else if (typeof value === "function") return toFn(value(to, from, dragEl, evt), pull)(to, from, dragEl, evt);
			else {
				var otherGroup = (pull ? to : from).options.group.name;
				return value === true || typeof value === "string" && value === otherGroup || value.join && value.indexOf(otherGroup) > -1;
			}
		};
	}
	var group = {};
	var originalGroup = options.group;
	if (!originalGroup || _typeof$1(originalGroup) != "object") originalGroup = { name: originalGroup };
	group.name = originalGroup.name;
	group.checkPull = toFn(originalGroup.pull, true);
	group.checkPut = toFn(originalGroup.put);
	group.revertClone = originalGroup.revertClone;
	options.group = group;
};
var _hideGhostForTarget = function _hideGhostForTarget() {
	if (!supportCssPointerEvents && ghostEl) css(ghostEl, "display", "none");
};
var _unhideGhostForTarget = function _unhideGhostForTarget() {
	if (!supportCssPointerEvents && ghostEl) css(ghostEl, "display", "");
};
if (documentExists && !ChromeForAndroid) document.addEventListener("click", function(evt) {
	if (ignoreNextClick) {
		evt.preventDefault();
		evt.stopPropagation && evt.stopPropagation();
		evt.stopImmediatePropagation && evt.stopImmediatePropagation();
		ignoreNextClick = false;
		return false;
	}
}, true);
var nearestEmptyInsertDetectEvent = function nearestEmptyInsertDetectEvent(evt) {
	if (dragEl) {
		evt = evt.touches ? evt.touches[0] : evt;
		var nearest = _detectNearestEmptySortable(evt.clientX, evt.clientY);
		if (nearest) {
			var event = {};
			for (var i in evt) if (evt.hasOwnProperty(i)) event[i] = evt[i];
			event.target = event.rootEl = nearest;
			event.preventDefault = void 0;
			event.stopPropagation = void 0;
			nearest[expando]._onDragOver(event);
		}
	}
};
var _checkOutsideTargetEl = function _checkOutsideTargetEl(evt) {
	if (dragEl) dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
};
/**
* @class  Sortable
* @param  {HTMLElement}  el
* @param  {Object}       [options]
*/
function Sortable$1(el, options) {
	if (!(el && el.nodeType && el.nodeType === 1)) throw "Sortable: `el` must be an HTMLElement, not ".concat({}.toString.call(el));
	this.el = el;
	this.options = options = _extends({}, options);
	el[expando] = this;
	var defaults = {
		group: null,
		sort: true,
		disabled: false,
		store: null,
		handle: null,
		draggable: /^[uo]l$/i.test(el.nodeName) ? ">li" : ">*",
		swapThreshold: 1,
		invertSwap: false,
		invertedSwapThreshold: null,
		removeCloneOnHide: true,
		direction: function direction() {
			return _detectDirection(el, this.options);
		},
		ghostClass: "sortable-ghost",
		chosenClass: "sortable-chosen",
		dragClass: "sortable-drag",
		ignore: "a, img",
		filter: null,
		preventOnFilter: true,
		animation: 0,
		easing: null,
		setData: function setData(dataTransfer, dragEl) {
			dataTransfer.setData("Text", dragEl.textContent);
		},
		dropBubble: false,
		dragoverBubble: false,
		dataIdAttr: "data-id",
		delay: 0,
		delayOnTouchOnly: false,
		touchStartThreshold: (Number.parseInt ? Number : window).parseInt(window.devicePixelRatio, 10) || 1,
		forceFallback: false,
		fallbackClass: "sortable-fallback",
		fallbackOnBody: false,
		fallbackTolerance: 0,
		fallbackOffset: {
			x: 0,
			y: 0
		},
		supportPointer: Sortable$1.supportPointer !== false && "PointerEvent" in window && (!Safari || IOS),
		emptyInsertThreshold: 5
	};
	PluginManager.initializePlugins(this, el, defaults);
	for (var name in defaults) !(name in options) && (options[name] = defaults[name]);
	_prepareGroup(options);
	for (var fn in this) if (fn.charAt(0) === "_" && typeof this[fn] === "function") this[fn] = this[fn].bind(this);
	this.nativeDraggable = options.forceFallback ? false : supportDraggable;
	if (this.nativeDraggable) this.options.touchStartThreshold = 1;
	if (options.supportPointer) on(el, "pointerdown", this._onTapStart);
	else {
		on(el, "mousedown", this._onTapStart);
		on(el, "touchstart", this._onTapStart);
	}
	if (this.nativeDraggable) {
		on(el, "dragover", this);
		on(el, "dragenter", this);
	}
	sortables.push(this.el);
	options.store && options.store.get && this.sort(options.store.get(this) || []);
	_extends(this, AnimationStateManager());
}
Sortable$1.prototype = (/** @lends Sortable.prototype */ {
	constructor: Sortable$1,
	_isOutsideThisEl: function _isOutsideThisEl(target) {
		if (!this.el.contains(target) && target !== this.el) lastTarget = null;
	},
	_getDirection: function _getDirection(evt, target) {
		return typeof this.options.direction === "function" ? this.options.direction.call(this, evt, target, dragEl) : this.options.direction;
	},
	_onTapStart: function _onTapStart(evt) {
		if (!evt.cancelable) return;
		var _this = this, el = this.el, options = this.options, preventOnFilter = options.preventOnFilter, type = evt.type, touch = evt.touches && evt.touches[0] || evt.pointerType && evt.pointerType === "touch" && evt, target = (touch || evt).target, originalTarget = evt.target.shadowRoot && (evt.path && evt.path[0] || evt.composedPath && evt.composedPath()[0]) || target, filter = options.filter;
		_saveInputCheckedState(el);
		if (dragEl) return;
		if (/mousedown|pointerdown/.test(type) && evt.button !== 0 || options.disabled) return;
		if (originalTarget.isContentEditable) return;
		if (!this.nativeDraggable && Safari && target && target.tagName.toUpperCase() === "SELECT") return;
		target = closest(target, options.draggable, el, false);
		if (target && target.animated) return;
		if (lastDownEl === target) return;
		oldIndex = index(target);
		oldDraggableIndex = index(target, options.draggable);
		if (typeof filter === "function") {
			if (filter.call(this, evt, target, this)) {
				_dispatchEvent({
					sortable: _this,
					rootEl: originalTarget,
					name: "filter",
					targetEl: target,
					toEl: el,
					fromEl: el
				});
				pluginEvent("filter", _this, { evt });
				preventOnFilter && evt.preventDefault();
				return;
			}
		} else if (filter) {
			filter = filter.split(",").some(function(criteria) {
				criteria = closest(originalTarget, criteria.trim(), el, false);
				if (criteria) {
					_dispatchEvent({
						sortable: _this,
						rootEl: criteria,
						name: "filter",
						targetEl: target,
						fromEl: el,
						toEl: el
					});
					pluginEvent("filter", _this, { evt });
					return true;
				}
			});
			if (filter) {
				preventOnFilter && evt.preventDefault();
				return;
			}
		}
		if (options.handle && !closest(originalTarget, options.handle, el, false)) return;
		this._prepareDragStart(evt, touch, target);
	},
	_prepareDragStart: function _prepareDragStart(evt, touch, target) {
		var _this = this, el = _this.el, options = _this.options, ownerDocument = el.ownerDocument, dragStartFn;
		if (target && !dragEl && target.parentNode === el) {
			var dragRect = getRect(target);
			rootEl = el;
			dragEl = target;
			parentEl = dragEl.parentNode;
			nextEl = dragEl.nextSibling;
			lastDownEl = target;
			activeGroup = options.group;
			Sortable$1.dragged = dragEl;
			tapEvt = {
				target: dragEl,
				clientX: (touch || evt).clientX,
				clientY: (touch || evt).clientY
			};
			tapDistanceLeft = tapEvt.clientX - dragRect.left;
			tapDistanceTop = tapEvt.clientY - dragRect.top;
			this._lastX = (touch || evt).clientX;
			this._lastY = (touch || evt).clientY;
			dragEl.style["will-change"] = "all";
			dragStartFn = function dragStartFn() {
				pluginEvent("delayEnded", _this, { evt });
				if (Sortable$1.eventCanceled) {
					_this._onDrop();
					return;
				}
				_this._disableDelayedDragEvents();
				if (!FireFox && _this.nativeDraggable) dragEl.draggable = true;
				_this._triggerDragStart(evt, touch);
				_dispatchEvent({
					sortable: _this,
					name: "choose",
					originalEvent: evt
				});
				toggleClass(dragEl, options.chosenClass, true);
			};
			options.ignore.split(",").forEach(function(criteria) {
				find(dragEl, criteria.trim(), _disableDraggable);
			});
			on(ownerDocument, "dragover", nearestEmptyInsertDetectEvent);
			on(ownerDocument, "mousemove", nearestEmptyInsertDetectEvent);
			on(ownerDocument, "touchmove", nearestEmptyInsertDetectEvent);
			if (options.supportPointer) {
				on(ownerDocument, "pointerup", _this._onDrop);
				!this.nativeDraggable && on(ownerDocument, "pointercancel", _this._onDrop);
			} else {
				on(ownerDocument, "mouseup", _this._onDrop);
				on(ownerDocument, "touchend", _this._onDrop);
				on(ownerDocument, "touchcancel", _this._onDrop);
			}
			if (FireFox && this.nativeDraggable) {
				this.options.touchStartThreshold = 4;
				dragEl.draggable = true;
			}
			pluginEvent("delayStart", this, { evt });
			if (options.delay && (!options.delayOnTouchOnly || touch) && (!this.nativeDraggable || !(Edge || IE11OrLess))) {
				if (Sortable$1.eventCanceled) {
					this._onDrop();
					return;
				}
				if (options.supportPointer) {
					on(ownerDocument, "pointerup", _this._disableDelayedDrag);
					on(ownerDocument, "pointercancel", _this._disableDelayedDrag);
				} else {
					on(ownerDocument, "mouseup", _this._disableDelayedDrag);
					on(ownerDocument, "touchend", _this._disableDelayedDrag);
					on(ownerDocument, "touchcancel", _this._disableDelayedDrag);
				}
				on(ownerDocument, "mousemove", _this._delayedDragTouchMoveHandler);
				on(ownerDocument, "touchmove", _this._delayedDragTouchMoveHandler);
				options.supportPointer && on(ownerDocument, "pointermove", _this._delayedDragTouchMoveHandler);
				_this._dragStartTimer = setTimeout(dragStartFn, options.delay);
			} else dragStartFn();
		}
	},
	_delayedDragTouchMoveHandler: function _delayedDragTouchMoveHandler(e) {
		var touch = e.touches ? e.touches[0] : e;
		if (Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) >= Math.floor(this.options.touchStartThreshold / (this.nativeDraggable && window.devicePixelRatio || 1))) this._disableDelayedDrag();
	},
	_disableDelayedDrag: function _disableDelayedDrag() {
		dragEl && _disableDraggable(dragEl);
		clearTimeout(this._dragStartTimer);
		this._disableDelayedDragEvents();
	},
	_disableDelayedDragEvents: function _disableDelayedDragEvents() {
		var ownerDocument = this.el.ownerDocument;
		off(ownerDocument, "mouseup", this._disableDelayedDrag);
		off(ownerDocument, "touchend", this._disableDelayedDrag);
		off(ownerDocument, "touchcancel", this._disableDelayedDrag);
		off(ownerDocument, "pointerup", this._disableDelayedDrag);
		off(ownerDocument, "pointercancel", this._disableDelayedDrag);
		off(ownerDocument, "mousemove", this._delayedDragTouchMoveHandler);
		off(ownerDocument, "touchmove", this._delayedDragTouchMoveHandler);
		off(ownerDocument, "pointermove", this._delayedDragTouchMoveHandler);
	},
	_triggerDragStart: function _triggerDragStart(evt, touch) {
		touch = touch || evt.pointerType == "touch" && evt;
		if (!this.nativeDraggable || touch) {
			if (this.options.supportPointer) on(document, "pointermove", this._onTouchMove);
			else if (touch) on(document, "touchmove", this._onTouchMove);
			else on(document, "mousemove", this._onTouchMove);
		} else {
			on(dragEl, "dragend", this);
			on(rootEl, "dragstart", this._onDragStart);
		}
		try {
			if (document.selection) _nextTick(function() {
				document.selection.empty();
			});
			else window.getSelection().removeAllRanges();
		} catch (err) {}
	},
	_dragStarted: function _dragStarted(fallback, evt) {
		awaitingDragStarted = false;
		if (rootEl && dragEl) {
			pluginEvent("dragStarted", this, { evt });
			if (this.nativeDraggable) on(document, "dragover", _checkOutsideTargetEl);
			var options = this.options;
			!fallback && toggleClass(dragEl, options.dragClass, false);
			toggleClass(dragEl, options.ghostClass, true);
			Sortable$1.active = this;
			fallback && this._appendGhost();
			_dispatchEvent({
				sortable: this,
				name: "start",
				originalEvent: evt
			});
		} else this._nulling();
	},
	_emulateDragOver: function _emulateDragOver() {
		if (touchEvt) {
			this._lastX = touchEvt.clientX;
			this._lastY = touchEvt.clientY;
			_hideGhostForTarget();
			var target = document.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
			var parent = target;
			while (target && target.shadowRoot) {
				target = target.shadowRoot.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
				if (target === parent) break;
				parent = target;
			}
			dragEl.parentNode[expando]._isOutsideThisEl(target);
			if (parent) do {
				if (parent[expando]) {
					var inserted = void 0;
					inserted = parent[expando]._onDragOver({
						clientX: touchEvt.clientX,
						clientY: touchEvt.clientY,
						target,
						rootEl: parent
					});
					if (inserted && !this.options.dragoverBubble) break;
				}
				target = parent;
			} while (parent = getParentOrHost(parent));
			_unhideGhostForTarget();
		}
	},
	_onTouchMove: function _onTouchMove(evt) {
		if (tapEvt) {
			var options = this.options, fallbackTolerance = options.fallbackTolerance, fallbackOffset = options.fallbackOffset, touch = evt.touches ? evt.touches[0] : evt, ghostMatrix = ghostEl && matrix(ghostEl, true), scaleX = ghostEl && ghostMatrix && ghostMatrix.a, scaleY = ghostEl && ghostMatrix && ghostMatrix.d, relativeScrollOffset = PositionGhostAbsolutely && ghostRelativeParent && getRelativeScrollOffset(ghostRelativeParent), dx = (touch.clientX - tapEvt.clientX + fallbackOffset.x) / (scaleX || 1) + (relativeScrollOffset ? relativeScrollOffset[0] - ghostRelativeParentInitialScroll[0] : 0) / (scaleX || 1), dy = (touch.clientY - tapEvt.clientY + fallbackOffset.y) / (scaleY || 1) + (relativeScrollOffset ? relativeScrollOffset[1] - ghostRelativeParentInitialScroll[1] : 0) / (scaleY || 1);
			if (!Sortable$1.active && !awaitingDragStarted) {
				if (fallbackTolerance && Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) < fallbackTolerance) return;
				this._onDragStart(evt, true);
			}
			if (ghostEl) {
				if (ghostMatrix) {
					ghostMatrix.e += dx - (lastDx || 0);
					ghostMatrix.f += dy - (lastDy || 0);
				} else ghostMatrix = {
					a: 1,
					b: 0,
					c: 0,
					d: 1,
					e: dx,
					f: dy
				};
				var cssMatrix = "matrix(".concat(ghostMatrix.a, ",").concat(ghostMatrix.b, ",").concat(ghostMatrix.c, ",").concat(ghostMatrix.d, ",").concat(ghostMatrix.e, ",").concat(ghostMatrix.f, ")");
				css(ghostEl, "webkitTransform", cssMatrix);
				css(ghostEl, "mozTransform", cssMatrix);
				css(ghostEl, "msTransform", cssMatrix);
				css(ghostEl, "transform", cssMatrix);
				lastDx = dx;
				lastDy = dy;
				touchEvt = touch;
			}
			evt.cancelable && evt.preventDefault();
		}
	},
	_appendGhost: function _appendGhost() {
		if (!ghostEl) {
			var container = this.options.fallbackOnBody ? document.body : rootEl, rect = getRect(dragEl, true, PositionGhostAbsolutely, true, container), options = this.options;
			if (PositionGhostAbsolutely) {
				ghostRelativeParent = container;
				while (css(ghostRelativeParent, "position") === "static" && css(ghostRelativeParent, "transform") === "none" && ghostRelativeParent !== document) ghostRelativeParent = ghostRelativeParent.parentNode;
				if (ghostRelativeParent !== document.body && ghostRelativeParent !== document.documentElement) {
					if (ghostRelativeParent === document) ghostRelativeParent = getWindowScrollingElement();
					rect.top += ghostRelativeParent.scrollTop;
					rect.left += ghostRelativeParent.scrollLeft;
				} else ghostRelativeParent = getWindowScrollingElement();
				ghostRelativeParentInitialScroll = getRelativeScrollOffset(ghostRelativeParent);
			}
			ghostEl = dragEl.cloneNode(true);
			toggleClass(ghostEl, options.ghostClass, false);
			toggleClass(ghostEl, options.fallbackClass, true);
			toggleClass(ghostEl, options.dragClass, true);
			css(ghostEl, "transition", "");
			css(ghostEl, "transform", "");
			css(ghostEl, "box-sizing", "border-box");
			css(ghostEl, "margin", 0);
			css(ghostEl, "top", rect.top);
			css(ghostEl, "left", rect.left);
			css(ghostEl, "width", rect.width);
			css(ghostEl, "height", rect.height);
			css(ghostEl, "opacity", "0.8");
			css(ghostEl, "position", PositionGhostAbsolutely ? "absolute" : "fixed");
			css(ghostEl, "zIndex", "100000");
			css(ghostEl, "pointerEvents", "none");
			Sortable$1.ghost = ghostEl;
			container.appendChild(ghostEl);
			css(ghostEl, "transform-origin", tapDistanceLeft / parseInt(ghostEl.style.width) * 100 + "% " + tapDistanceTop / parseInt(ghostEl.style.height) * 100 + "%");
		}
	},
	_onDragStart: function _onDragStart(evt, fallback) {
		var _this = this;
		var dataTransfer = evt.dataTransfer;
		var options = _this.options;
		pluginEvent("dragStart", this, { evt });
		if (Sortable$1.eventCanceled) {
			this._onDrop();
			return;
		}
		pluginEvent("setupClone", this);
		if (!Sortable$1.eventCanceled) {
			cloneEl = clone(dragEl);
			cloneEl.removeAttribute("id");
			cloneEl.draggable = false;
			cloneEl.style["will-change"] = "";
			this._hideClone();
			toggleClass(cloneEl, this.options.chosenClass, false);
			Sortable$1.clone = cloneEl;
		}
		_this.cloneId = _nextTick(function() {
			pluginEvent("clone", _this);
			if (Sortable$1.eventCanceled) return;
			if (!_this.options.removeCloneOnHide) rootEl.insertBefore(cloneEl, dragEl);
			_this._hideClone();
			_dispatchEvent({
				sortable: _this,
				name: "clone"
			});
		});
		!fallback && toggleClass(dragEl, options.dragClass, true);
		if (fallback) {
			ignoreNextClick = true;
			_this._loopId = setInterval(_this._emulateDragOver, 50);
		} else {
			off(document, "mouseup", _this._onDrop);
			off(document, "touchend", _this._onDrop);
			off(document, "touchcancel", _this._onDrop);
			if (dataTransfer) {
				dataTransfer.effectAllowed = "move";
				options.setData && options.setData.call(_this, dataTransfer, dragEl);
			}
			on(document, "drop", _this);
			css(dragEl, "transform", "translateZ(0)");
		}
		awaitingDragStarted = true;
		_this._dragStartId = _nextTick(_this._dragStarted.bind(_this, fallback, evt));
		on(document, "selectstart", _this);
		moved = true;
		window.getSelection().removeAllRanges();
		if (Safari) css(document.body, "user-select", "none");
	},
	_onDragOver: function _onDragOver(evt) {
		var el = this.el, target = evt.target, dragRect, targetRect, revert, options = this.options, group = options.group, activeSortable = Sortable$1.active, isOwner = activeGroup === group, canSort = options.sort, fromSortable = putSortable || activeSortable, vertical, _this = this, completedFired = false;
		if (_silent) return;
		function dragOverEvent(name, extra) {
			pluginEvent(name, _this, _objectSpread2$1({
				evt,
				isOwner,
				axis: vertical ? "vertical" : "horizontal",
				revert,
				dragRect,
				targetRect,
				canSort,
				fromSortable,
				target,
				completed,
				onMove: function onMove(target, after) {
					return _onMove(rootEl, el, dragEl, dragRect, target, getRect(target), evt, after);
				},
				changed
			}, extra));
		}
		function capture() {
			dragOverEvent("dragOverAnimationCapture");
			_this.captureAnimationState();
			if (_this !== fromSortable) fromSortable.captureAnimationState();
		}
		function completed(insertion) {
			dragOverEvent("dragOverCompleted", { insertion });
			if (insertion) {
				if (isOwner) activeSortable._hideClone();
				else activeSortable._showClone(_this);
				if (_this !== fromSortable) {
					toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : activeSortable.options.ghostClass, false);
					toggleClass(dragEl, options.ghostClass, true);
				}
				if (putSortable !== _this && _this !== Sortable$1.active) putSortable = _this;
				else if (_this === Sortable$1.active && putSortable) putSortable = null;
				if (fromSortable === _this) _this._ignoreWhileAnimating = target;
				_this.animateAll(function() {
					dragOverEvent("dragOverAnimationComplete");
					_this._ignoreWhileAnimating = null;
				});
				if (_this !== fromSortable) {
					fromSortable.animateAll();
					fromSortable._ignoreWhileAnimating = null;
				}
			}
			if (target === dragEl && !dragEl.animated || target === el && !target.animated) lastTarget = null;
			if (!options.dragoverBubble && !evt.rootEl && target !== document) {
				dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
				!insertion && nearestEmptyInsertDetectEvent(evt);
			}
			!options.dragoverBubble && evt.stopPropagation && evt.stopPropagation();
			return completedFired = true;
		}
		function changed() {
			newIndex = index(dragEl);
			newDraggableIndex = index(dragEl, options.draggable);
			_dispatchEvent({
				sortable: _this,
				name: "change",
				toEl: el,
				newIndex,
				newDraggableIndex,
				originalEvent: evt
			});
		}
		if (evt.preventDefault !== void 0) evt.cancelable && evt.preventDefault();
		target = closest(target, options.draggable, el, true);
		dragOverEvent("dragOver");
		if (Sortable$1.eventCanceled) return completedFired;
		if (dragEl.contains(evt.target) || target.animated && target.animatingX && target.animatingY || _this._ignoreWhileAnimating === target) return completed(false);
		ignoreNextClick = false;
		if (activeSortable && !options.disabled && (isOwner ? canSort || (revert = parentEl !== rootEl) : putSortable === this || (this.lastPutMode = activeGroup.checkPull(this, activeSortable, dragEl, evt)) && group.checkPut(this, activeSortable, dragEl, evt))) {
			vertical = this._getDirection(evt, target) === "vertical";
			dragRect = getRect(dragEl);
			dragOverEvent("dragOverValid");
			if (Sortable$1.eventCanceled) return completedFired;
			if (revert) {
				parentEl = rootEl;
				capture();
				this._hideClone();
				dragOverEvent("revert");
				if (!Sortable$1.eventCanceled) {
					if (nextEl) rootEl.insertBefore(dragEl, nextEl);
					else rootEl.appendChild(dragEl);
				}
				return completed(true);
			}
			var elLastChild = lastChild(el, options.draggable);
			if (!elLastChild || _ghostIsLast(evt, vertical, this) && !elLastChild.animated) {
				if (elLastChild === dragEl) return completed(false);
				if (elLastChild && el === evt.target) target = elLastChild;
				if (target) targetRect = getRect(target);
				if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, !!target) !== false) {
					capture();
					if (elLastChild && elLastChild.nextSibling) el.insertBefore(dragEl, elLastChild.nextSibling);
					else el.appendChild(dragEl);
					parentEl = el;
					changed();
					return completed(true);
				}
			} else if (elLastChild && _ghostIsFirst(evt, vertical, this)) {
				var firstChild = getChild(el, 0, options, true);
				if (firstChild === dragEl) return completed(false);
				target = firstChild;
				targetRect = getRect(target);
				if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, false) !== false) {
					capture();
					el.insertBefore(dragEl, firstChild);
					parentEl = el;
					changed();
					return completed(true);
				}
			} else if (target.parentNode === el) {
				targetRect = getRect(target);
				var direction = 0, targetBeforeFirstSwap, differentLevel = dragEl.parentNode !== el, differentRowCol = !_dragElInRowColumn(dragEl.animated && dragEl.toRect || dragRect, target.animated && target.toRect || targetRect, vertical), side1 = vertical ? "top" : "left", scrolledPastTop = isScrolledPast(target, "top", "top") || isScrolledPast(dragEl, "top", "top"), scrollBefore = scrolledPastTop ? scrolledPastTop.scrollTop : void 0;
				if (lastTarget !== target) {
					targetBeforeFirstSwap = targetRect[side1];
					pastFirstInvertThresh = false;
					isCircumstantialInvert = !differentRowCol && options.invertSwap || differentLevel;
				}
				direction = _getSwapDirection(evt, target, targetRect, vertical, differentRowCol ? 1 : options.swapThreshold, options.invertedSwapThreshold == null ? options.swapThreshold : options.invertedSwapThreshold, isCircumstantialInvert, lastTarget === target);
				var sibling;
				if (direction !== 0) {
					var dragIndex = index(dragEl);
					do {
						dragIndex -= direction;
						sibling = parentEl.children[dragIndex];
					} while (sibling && (css(sibling, "display") === "none" || sibling === ghostEl));
				}
				if (direction === 0 || sibling === target) return completed(false);
				lastTarget = target;
				lastDirection = direction;
				var nextSibling = target.nextElementSibling, after = false;
				after = direction === 1;
				var moveVector = _onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, after);
				if (moveVector !== false) {
					if (moveVector === 1 || moveVector === -1) after = moveVector === 1;
					_silent = true;
					setTimeout(_unsilent, 30);
					capture();
					if (after && !nextSibling) el.appendChild(dragEl);
					else target.parentNode.insertBefore(dragEl, after ? nextSibling : target);
					if (scrolledPastTop) scrollBy(scrolledPastTop, 0, scrollBefore - scrolledPastTop.scrollTop);
					parentEl = dragEl.parentNode;
					if (targetBeforeFirstSwap !== void 0 && !isCircumstantialInvert) targetMoveDistance = Math.abs(targetBeforeFirstSwap - getRect(target)[side1]);
					changed();
					return completed(true);
				}
			}
			if (el.contains(dragEl)) return completed(false);
		}
		return false;
	},
	_ignoreWhileAnimating: null,
	_offMoveEvents: function _offMoveEvents() {
		off(document, "mousemove", this._onTouchMove);
		off(document, "touchmove", this._onTouchMove);
		off(document, "pointermove", this._onTouchMove);
		off(document, "dragover", nearestEmptyInsertDetectEvent);
		off(document, "mousemove", nearestEmptyInsertDetectEvent);
		off(document, "touchmove", nearestEmptyInsertDetectEvent);
	},
	_offUpEvents: function _offUpEvents() {
		var ownerDocument = this.el.ownerDocument;
		off(ownerDocument, "mouseup", this._onDrop);
		off(ownerDocument, "touchend", this._onDrop);
		off(ownerDocument, "pointerup", this._onDrop);
		off(ownerDocument, "pointercancel", this._onDrop);
		off(ownerDocument, "touchcancel", this._onDrop);
		off(document, "selectstart", this);
	},
	_onDrop: function _onDrop(evt) {
		var el = this.el, options = this.options;
		newIndex = index(dragEl);
		newDraggableIndex = index(dragEl, options.draggable);
		pluginEvent("drop", this, { evt });
		parentEl = dragEl && dragEl.parentNode;
		newIndex = index(dragEl);
		newDraggableIndex = index(dragEl, options.draggable);
		if (Sortable$1.eventCanceled) {
			this._nulling();
			return;
		}
		awaitingDragStarted = false;
		isCircumstantialInvert = false;
		pastFirstInvertThresh = false;
		clearInterval(this._loopId);
		clearTimeout(this._dragStartTimer);
		_cancelNextTick(this.cloneId);
		_cancelNextTick(this._dragStartId);
		if (this.nativeDraggable) {
			off(document, "drop", this);
			off(el, "dragstart", this._onDragStart);
		}
		this._offMoveEvents();
		this._offUpEvents();
		if (Safari) css(document.body, "user-select", "");
		css(dragEl, "transform", "");
		if (evt) {
			if (moved) {
				evt.cancelable && evt.preventDefault();
				!options.dropBubble && evt.stopPropagation();
			}
			ghostEl && ghostEl.parentNode && ghostEl.parentNode.removeChild(ghostEl);
			if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== "clone") cloneEl && cloneEl.parentNode && cloneEl.parentNode.removeChild(cloneEl);
			if (dragEl) {
				if (this.nativeDraggable) off(dragEl, "dragend", this);
				_disableDraggable(dragEl);
				dragEl.style["will-change"] = "";
				if (moved && !awaitingDragStarted) toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : this.options.ghostClass, false);
				toggleClass(dragEl, this.options.chosenClass, false);
				_dispatchEvent({
					sortable: this,
					name: "unchoose",
					toEl: parentEl,
					newIndex: null,
					newDraggableIndex: null,
					originalEvent: evt
				});
				if (rootEl !== parentEl) {
					if (newIndex >= 0) {
						_dispatchEvent({
							rootEl: parentEl,
							name: "add",
							toEl: parentEl,
							fromEl: rootEl,
							originalEvent: evt
						});
						_dispatchEvent({
							sortable: this,
							name: "remove",
							toEl: parentEl,
							originalEvent: evt
						});
						_dispatchEvent({
							rootEl: parentEl,
							name: "sort",
							toEl: parentEl,
							fromEl: rootEl,
							originalEvent: evt
						});
						_dispatchEvent({
							sortable: this,
							name: "sort",
							toEl: parentEl,
							originalEvent: evt
						});
					}
					putSortable && putSortable.save();
				} else if (newIndex !== oldIndex) {
					if (newIndex >= 0) {
						_dispatchEvent({
							sortable: this,
							name: "update",
							toEl: parentEl,
							originalEvent: evt
						});
						_dispatchEvent({
							sortable: this,
							name: "sort",
							toEl: parentEl,
							originalEvent: evt
						});
					}
				}
				if (Sortable$1.active) {
					if (newIndex == null || newIndex === -1) {
						newIndex = oldIndex;
						newDraggableIndex = oldDraggableIndex;
					}
					_dispatchEvent({
						sortable: this,
						name: "end",
						toEl: parentEl,
						originalEvent: evt
					});
					this.save();
				}
			}
		}
		this._nulling();
	},
	_nulling: function _nulling() {
		pluginEvent("nulling", this);
		rootEl = dragEl = parentEl = ghostEl = nextEl = cloneEl = lastDownEl = cloneHidden = tapEvt = touchEvt = moved = newIndex = newDraggableIndex = oldIndex = oldDraggableIndex = lastTarget = lastDirection = putSortable = activeGroup = Sortable$1.dragged = Sortable$1.ghost = Sortable$1.clone = Sortable$1.active = null;
		var el = this.el;
		savedInputChecked.forEach(function(checkEl) {
			if (el.contains(checkEl)) checkEl.checked = true;
		});
		savedInputChecked.length = lastDx = lastDy = 0;
	},
	handleEvent: function handleEvent(evt) {
		switch (evt.type) {
			case "drop":
			case "dragend":
				this._onDrop(evt);
				break;
			case "dragenter":
			case "dragover":
				if (dragEl) {
					this._onDragOver(evt);
					_globalDragOver(evt);
				}
				break;
			case "selectstart": evt.preventDefault();
		}
	},
	/**
	* Serializes the item into an array of string.
	* @returns {String[]}
	*/
	toArray: function toArray() {
		var order = [], el, children = this.el.children, i = 0, n = children.length, options = this.options;
		for (; i < n; i++) {
			el = children[i];
			if (closest(el, options.draggable, this.el, false)) order.push(el.getAttribute(options.dataIdAttr) || _generateId(el));
		}
		return order;
	},
	/**
	* Sorts the elements according to the array.
	* @param  {String[]}  order  order of the items
	*/
	sort: function sort(order, useAnimation) {
		var items = {}, rootEl = this.el;
		this.toArray().forEach(function(id, i) {
			var el = rootEl.children[i];
			if (closest(el, this.options.draggable, rootEl, false)) items[id] = el;
		}, this);
		useAnimation && this.captureAnimationState();
		order.forEach(function(id) {
			if (items[id]) {
				rootEl.removeChild(items[id]);
				rootEl.appendChild(items[id]);
			}
		});
		useAnimation && this.animateAll();
	},
	/**
	* Save the current sorting
	*/
	save: function save() {
		var store = this.options.store;
		store && store.set && store.set(this);
	},
	/**
	* For each element in the set, get the first element that matches the selector by testing the element itself and traversing up through its ancestors in the DOM tree.
	* @param   {HTMLElement}  el
	* @param   {String}       [selector]  default: `options.draggable`
	* @returns {HTMLElement|null}
	*/
	closest: function closest$1(el, selector) {
		return closest(el, selector || this.options.draggable, this.el, false);
	},
	/**
	* Set/get option
	* @param   {string} name
	* @param   {*}      [value]
	* @returns {*}
	*/
	option: function option(name, value) {
		var options = this.options;
		if (value === void 0) return options[name];
		else {
			var modifiedValue = PluginManager.modifyOption(this, name, value);
			if (typeof modifiedValue !== "undefined") options[name] = modifiedValue;
			else options[name] = value;
			if (name === "group") _prepareGroup(options);
		}
	},
	/**
	* Destroy
	*/
	destroy: function destroy() {
		pluginEvent("destroy", this);
		var el = this.el;
		el[expando] = null;
		off(el, "mousedown", this._onTapStart);
		off(el, "touchstart", this._onTapStart);
		off(el, "pointerdown", this._onTapStart);
		if (this.nativeDraggable) {
			off(el, "dragover", this);
			off(el, "dragenter", this);
		}
		Array.prototype.forEach.call(el.querySelectorAll("[draggable]"), function(el) {
			el.removeAttribute("draggable");
		});
		this._onDrop();
		this._disableDelayedDragEvents();
		sortables.splice(sortables.indexOf(this.el), 1);
		this.el = el = null;
	},
	_hideClone: function _hideClone() {
		if (!cloneHidden) {
			pluginEvent("hideClone", this);
			if (Sortable$1.eventCanceled) return;
			css(cloneEl, "display", "none");
			if (this.options.removeCloneOnHide && cloneEl.parentNode) cloneEl.parentNode.removeChild(cloneEl);
			cloneHidden = true;
		}
	},
	_showClone: function _showClone(putSortable) {
		if (putSortable.lastPutMode !== "clone") {
			this._hideClone();
			return;
		}
		if (cloneHidden) {
			pluginEvent("showClone", this);
			if (Sortable$1.eventCanceled) return;
			if (dragEl.parentNode == rootEl && !this.options.group.revertClone) rootEl.insertBefore(cloneEl, dragEl);
			else if (nextEl) rootEl.insertBefore(cloneEl, nextEl);
			else rootEl.appendChild(cloneEl);
			if (this.options.group.revertClone) this.animate(dragEl, cloneEl);
			css(cloneEl, "display", "");
			cloneHidden = false;
		}
	}
});
function _globalDragOver(evt) {
	if (evt.dataTransfer) evt.dataTransfer.dropEffect = "move";
	evt.cancelable && evt.preventDefault();
}
function _onMove(fromEl, toEl, dragEl, dragRect, targetEl, targetRect, originalEvent, willInsertAfter) {
	var evt, sortable = fromEl[expando], onMoveFn = sortable.options.onMove, retVal;
	if (window.CustomEvent && !IE11OrLess && !Edge) evt = new CustomEvent("move", {
		bubbles: true,
		cancelable: true
	});
	else {
		evt = document.createEvent("Event");
		evt.initEvent("move", true, true);
	}
	evt.to = toEl;
	evt.from = fromEl;
	evt.dragged = dragEl;
	evt.draggedRect = dragRect;
	evt.related = targetEl || toEl;
	evt.relatedRect = targetRect || getRect(toEl);
	evt.willInsertAfter = willInsertAfter;
	evt.originalEvent = originalEvent;
	fromEl.dispatchEvent(evt);
	if (onMoveFn) retVal = onMoveFn.call(sortable, evt, originalEvent);
	return retVal;
}
function _disableDraggable(el) {
	el.draggable = false;
}
function _unsilent() {
	_silent = false;
}
function _ghostIsFirst(evt, vertical, sortable) {
	var firstElRect = getRect(getChild(sortable.el, 0, sortable.options, true));
	var childContainingRect = getChildContainingRectFromElement(sortable.el, sortable.options, ghostEl);
	var spacer = 10;
	return vertical ? evt.clientX < childContainingRect.left - spacer || evt.clientY < firstElRect.top && evt.clientX < firstElRect.right : evt.clientY < childContainingRect.top - spacer || evt.clientY < firstElRect.bottom && evt.clientX < firstElRect.left;
}
function _ghostIsLast(evt, vertical, sortable) {
	var lastElRect = getRect(lastChild(sortable.el, sortable.options.draggable));
	var childContainingRect = getChildContainingRectFromElement(sortable.el, sortable.options, ghostEl);
	var spacer = 10;
	return vertical ? evt.clientX > childContainingRect.right + spacer || evt.clientY > lastElRect.bottom && evt.clientX > lastElRect.left : evt.clientY > childContainingRect.bottom + spacer || evt.clientX > lastElRect.right && evt.clientY > lastElRect.top;
}
function _getSwapDirection(evt, target, targetRect, vertical, swapThreshold, invertedSwapThreshold, invertSwap, isLastTarget) {
	var mouseOnAxis = vertical ? evt.clientY : evt.clientX, targetLength = vertical ? targetRect.height : targetRect.width, targetS1 = vertical ? targetRect.top : targetRect.left, targetS2 = vertical ? targetRect.bottom : targetRect.right, invert = false;
	if (!invertSwap) {
		if (isLastTarget && targetMoveDistance < targetLength * swapThreshold) {
			if (!pastFirstInvertThresh && (lastDirection === 1 ? mouseOnAxis > targetS1 + targetLength * invertedSwapThreshold / 2 : mouseOnAxis < targetS2 - targetLength * invertedSwapThreshold / 2)) pastFirstInvertThresh = true;
			if (!pastFirstInvertThresh) {
				if (lastDirection === 1 ? mouseOnAxis < targetS1 + targetMoveDistance : mouseOnAxis > targetS2 - targetMoveDistance) return -lastDirection;
			} else invert = true;
		} else if (mouseOnAxis > targetS1 + targetLength * (1 - swapThreshold) / 2 && mouseOnAxis < targetS2 - targetLength * (1 - swapThreshold) / 2) return _getInsertDirection(target);
	}
	invert = invert || invertSwap;
	if (invert) {
		if (mouseOnAxis < targetS1 + targetLength * invertedSwapThreshold / 2 || mouseOnAxis > targetS2 - targetLength * invertedSwapThreshold / 2) return mouseOnAxis > targetS1 + targetLength / 2 ? 1 : -1;
	}
	return 0;
}
/**
* Gets the direction dragEl must be swapped relative to target in order to make it
* seem that dragEl has been "inserted" into that element's position
* @param  {HTMLElement} target       The target whose position dragEl is being inserted at
* @return {Number}                   Direction dragEl must be swapped
*/
function _getInsertDirection(target) {
	if (index(dragEl) < index(target)) return 1;
	else return -1;
}
/**
* Generate id
* @param   {HTMLElement} el
* @returns {String}
* @private
*/
function _generateId(el) {
	var str = el.tagName + el.className + el.src + el.href + el.textContent, i = str.length, sum = 0;
	while (i--) sum += str.charCodeAt(i);
	return sum.toString(36);
}
function _saveInputCheckedState(root) {
	savedInputChecked.length = 0;
	var inputs = root.getElementsByTagName("input");
	var idx = inputs.length;
	while (idx--) {
		var el = inputs[idx];
		el.checked && savedInputChecked.push(el);
	}
}
function _nextTick(fn) {
	return setTimeout(fn, 0);
}
function _cancelNextTick(id) {
	return clearTimeout(id);
}
if (documentExists) on(document, "touchmove", function(evt) {
	if ((Sortable$1.active || awaitingDragStarted) && evt.cancelable) evt.preventDefault();
});
Sortable$1.utils = {
	on,
	off,
	css,
	find,
	is: function is(el, selector) {
		return !!closest(el, selector, el, false);
	},
	extend,
	throttle,
	closest,
	toggleClass,
	clone,
	index,
	nextTick: _nextTick,
	cancelNextTick: _cancelNextTick,
	detectDirection: _detectDirection,
	getChild,
	expando
};
/**
* Get the Sortable instance of an element
* @param  {HTMLElement} element The element
* @return {Sortable|undefined}         The instance of Sortable
*/
Sortable$1.get = function(element) {
	return element[expando];
};
/**
* Mount a plugin to Sortable
* @param  {...SortablePlugin|SortablePlugin[]} plugins       Plugins being mounted
*/
Sortable$1.mount = function() {
	for (var _len = arguments.length, plugins = new Array(_len), _key = 0; _key < _len; _key++) plugins[_key] = arguments[_key];
	if (plugins[0].constructor === Array) plugins = plugins[0];
	plugins.forEach(function(plugin) {
		if (!plugin.prototype || !plugin.prototype.constructor) throw "Sortable: Mounted plugin must be a constructor function, not ".concat({}.toString.call(plugin));
		if (plugin.utils) Sortable$1.utils = _objectSpread2$1(_objectSpread2$1({}, Sortable$1.utils), plugin.utils);
		PluginManager.mount(plugin);
	});
};
/**
* Create sortable instance
* @param {HTMLElement}  el
* @param {Object}      [options]
*/
Sortable$1.create = function(el, options) {
	return new Sortable$1(el, options);
};
Sortable$1.version = version;
var autoScrolls = [];
var scrollEl;
var scrollRootEl;
var scrolling = false;
var lastAutoScrollX;
var lastAutoScrollY;
var touchEvt$1;
var pointerElemChangedInterval;
function AutoScrollPlugin() {
	function AutoScroll() {
		this.defaults = {
			scroll: true,
			forceAutoScrollFallback: false,
			scrollSensitivity: 30,
			scrollSpeed: 10,
			bubbleScroll: true
		};
		for (var fn in this) if (fn.charAt(0) === "_" && typeof this[fn] === "function") this[fn] = this[fn].bind(this);
	}
	AutoScroll.prototype = {
		dragStarted: function dragStarted(_ref) {
			var originalEvent = _ref.originalEvent;
			if (this.sortable.nativeDraggable) on(document, "dragover", this._handleAutoScroll);
			else if (this.options.supportPointer) on(document, "pointermove", this._handleFallbackAutoScroll);
			else if (originalEvent.touches) on(document, "touchmove", this._handleFallbackAutoScroll);
			else on(document, "mousemove", this._handleFallbackAutoScroll);
		},
		dragOverCompleted: function dragOverCompleted(_ref2) {
			var originalEvent = _ref2.originalEvent;
			if (!this.options.dragOverBubble && !originalEvent.rootEl) this._handleAutoScroll(originalEvent);
		},
		drop: function drop() {
			if (this.sortable.nativeDraggable) off(document, "dragover", this._handleAutoScroll);
			else {
				off(document, "pointermove", this._handleFallbackAutoScroll);
				off(document, "touchmove", this._handleFallbackAutoScroll);
				off(document, "mousemove", this._handleFallbackAutoScroll);
			}
			clearPointerElemChangedInterval();
			clearAutoScrolls();
			cancelThrottle();
		},
		nulling: function nulling() {
			touchEvt$1 = scrollRootEl = scrollEl = scrolling = pointerElemChangedInterval = lastAutoScrollX = lastAutoScrollY = null;
			autoScrolls.length = 0;
		},
		_handleFallbackAutoScroll: function _handleFallbackAutoScroll(evt) {
			this._handleAutoScroll(evt, true);
		},
		_handleAutoScroll: function _handleAutoScroll(evt, fallback) {
			var _this = this;
			var x = (evt.touches ? evt.touches[0] : evt).clientX, y = (evt.touches ? evt.touches[0] : evt).clientY, elem = document.elementFromPoint(x, y);
			touchEvt$1 = evt;
			if (fallback || this.options.forceAutoScrollFallback || Edge || IE11OrLess || Safari) {
				autoScroll(evt, this.options, elem, fallback);
				var ogElemScroller = getParentAutoScrollElement(elem, true);
				if (scrolling && (!pointerElemChangedInterval || x !== lastAutoScrollX || y !== lastAutoScrollY)) {
					pointerElemChangedInterval && clearPointerElemChangedInterval();
					pointerElemChangedInterval = setInterval(function() {
						var newElem = getParentAutoScrollElement(document.elementFromPoint(x, y), true);
						if (newElem !== ogElemScroller) {
							ogElemScroller = newElem;
							clearAutoScrolls();
						}
						autoScroll(evt, _this.options, newElem, fallback);
					}, 10);
					lastAutoScrollX = x;
					lastAutoScrollY = y;
				}
			} else {
				if (!this.options.bubbleScroll || getParentAutoScrollElement(elem, true) === getWindowScrollingElement()) {
					clearAutoScrolls();
					return;
				}
				autoScroll(evt, this.options, getParentAutoScrollElement(elem, false), false);
			}
		}
	};
	return _extends(AutoScroll, {
		pluginName: "scroll",
		initializeByDefault: true
	});
}
function clearAutoScrolls() {
	autoScrolls.forEach(function(autoScroll) {
		clearInterval(autoScroll.pid);
	});
	autoScrolls = [];
}
function clearPointerElemChangedInterval() {
	clearInterval(pointerElemChangedInterval);
}
var autoScroll = throttle(function(evt, options, rootEl, isFallback) {
	if (!options.scroll) return;
	var x = (evt.touches ? evt.touches[0] : evt).clientX, y = (evt.touches ? evt.touches[0] : evt).clientY, sens = options.scrollSensitivity, speed = options.scrollSpeed, winScroller = getWindowScrollingElement();
	var scrollThisInstance = false, scrollCustomFn;
	if (scrollRootEl !== rootEl) {
		scrollRootEl = rootEl;
		clearAutoScrolls();
		scrollEl = options.scroll;
		scrollCustomFn = options.scrollFn;
		if (scrollEl === true) scrollEl = getParentAutoScrollElement(rootEl, true);
	}
	var layersOut = 0;
	var currentParent = scrollEl;
	do {
		var el = currentParent, rect = getRect(el), top = rect.top, bottom = rect.bottom, left = rect.left, right = rect.right, width = rect.width, height = rect.height, canScrollX = void 0, canScrollY = void 0, scrollWidth = el.scrollWidth, scrollHeight = el.scrollHeight, elCSS = css(el), scrollPosX = el.scrollLeft, scrollPosY = el.scrollTop;
		if (el === winScroller) {
			canScrollX = width < scrollWidth && (elCSS.overflowX === "auto" || elCSS.overflowX === "scroll" || elCSS.overflowX === "visible");
			canScrollY = height < scrollHeight && (elCSS.overflowY === "auto" || elCSS.overflowY === "scroll" || elCSS.overflowY === "visible");
		} else {
			canScrollX = width < scrollWidth && (elCSS.overflowX === "auto" || elCSS.overflowX === "scroll");
			canScrollY = height < scrollHeight && (elCSS.overflowY === "auto" || elCSS.overflowY === "scroll");
		}
		var vx = canScrollX && (Math.abs(right - x) <= sens && scrollPosX + width < scrollWidth) - (Math.abs(left - x) <= sens && !!scrollPosX);
		var vy = canScrollY && (Math.abs(bottom - y) <= sens && scrollPosY + height < scrollHeight) - (Math.abs(top - y) <= sens && !!scrollPosY);
		if (!autoScrolls[layersOut]) {
			for (var i = 0; i <= layersOut; i++) if (!autoScrolls[i]) autoScrolls[i] = {};
		}
		if (autoScrolls[layersOut].vx != vx || autoScrolls[layersOut].vy != vy || autoScrolls[layersOut].el !== el) {
			autoScrolls[layersOut].el = el;
			autoScrolls[layersOut].vx = vx;
			autoScrolls[layersOut].vy = vy;
			clearInterval(autoScrolls[layersOut].pid);
			if (vx != 0 || vy != 0) {
				scrollThisInstance = true;
				autoScrolls[layersOut].pid = setInterval(function() {
					if (isFallback && this.layer === 0) Sortable$1.active._onTouchMove(touchEvt$1);
					var scrollOffsetY = autoScrolls[this.layer].vy ? autoScrolls[this.layer].vy * speed : 0;
					var scrollOffsetX = autoScrolls[this.layer].vx ? autoScrolls[this.layer].vx * speed : 0;
					if (typeof scrollCustomFn === "function") {
						if (scrollCustomFn.call(Sortable$1.dragged.parentNode[expando], scrollOffsetX, scrollOffsetY, evt, touchEvt$1, autoScrolls[this.layer].el) !== "continue") return;
					}
					scrollBy(autoScrolls[this.layer].el, scrollOffsetX, scrollOffsetY);
				}.bind({ layer: layersOut }), 24);
			}
		}
		layersOut++;
	} while (options.bubbleScroll && currentParent !== winScroller && (currentParent = getParentAutoScrollElement(currentParent, false)));
	scrolling = scrollThisInstance;
}, 30);
var drop = function drop(_ref) {
	var originalEvent = _ref.originalEvent, putSortable = _ref.putSortable, dragEl = _ref.dragEl, activeSortable = _ref.activeSortable, dispatchSortableEvent = _ref.dispatchSortableEvent, hideGhostForTarget = _ref.hideGhostForTarget, unhideGhostForTarget = _ref.unhideGhostForTarget;
	if (!originalEvent) return;
	var toSortable = putSortable || activeSortable;
	hideGhostForTarget();
	var touch = originalEvent.changedTouches && originalEvent.changedTouches.length ? originalEvent.changedTouches[0] : originalEvent;
	var target = document.elementFromPoint(touch.clientX, touch.clientY);
	unhideGhostForTarget();
	if (toSortable && !toSortable.el.contains(target)) {
		dispatchSortableEvent("spill");
		this.onSpill({
			dragEl,
			putSortable
		});
	}
};
function Revert() {}
Revert.prototype = {
	startIndex: null,
	dragStart: function dragStart(_ref2) {
		var oldDraggableIndex = _ref2.oldDraggableIndex;
		this.startIndex = oldDraggableIndex;
	},
	onSpill: function onSpill(_ref3) {
		var dragEl = _ref3.dragEl, putSortable = _ref3.putSortable;
		this.sortable.captureAnimationState();
		if (putSortable) putSortable.captureAnimationState();
		var nextSibling = getChild(this.sortable.el, this.startIndex, this.options);
		if (nextSibling) this.sortable.el.insertBefore(dragEl, nextSibling);
		else this.sortable.el.appendChild(dragEl);
		this.sortable.animateAll();
		if (putSortable) putSortable.animateAll();
	},
	drop
};
_extends(Revert, { pluginName: "revertOnSpill" });
function Remove() {}
Remove.prototype = {
	onSpill: function onSpill(_ref4) {
		var dragEl = _ref4.dragEl;
		var parentSortable = _ref4.putSortable || this.sortable;
		parentSortable.captureAnimationState();
		dragEl.parentNode && dragEl.parentNode.removeChild(dragEl);
		parentSortable.animateAll();
	},
	drop
};
_extends(Remove, { pluginName: "removeOnSpill" });
Sortable$1.mount(new AutoScrollPlugin());
Sortable$1.mount(Remove, Revert);
// node_modules/ev-emitter/ev-emitter.js
var require_ev_emitter = /* @__PURE__ */ __commonJSMin(((exports, module, this$1) => {
	/**
	* EvEmitter v1.1.0
	* Lil' event emitter
	* MIT License
	*/
	(function(global, factory) {
		if (typeof define == "function" && define.amd) define(factory);
		else if (typeof module == "object" && module.exports) module.exports = factory();
		else global.EvEmitter = factory();
	})(typeof window != "undefined" ? window : this$1, function() {
		"use strict";
		function EvEmitter() {}
		var proto = EvEmitter.prototype;
		proto.on = function(eventName, listener) {
			if (!eventName || !listener) return;
			var events = this._events = this._events || {};
			var listeners = events[eventName] = events[eventName] || [];
			if (listeners.indexOf(listener) == -1) listeners.push(listener);
			return this;
		};
		proto.once = function(eventName, listener) {
			if (!eventName || !listener) return;
			this.on(eventName, listener);
			var onceEvents = this._onceEvents = this._onceEvents || {};
			var onceListeners = onceEvents[eventName] = onceEvents[eventName] || {};
			onceListeners[listener] = true;
			return this;
		};
		proto.off = function(eventName, listener) {
			var listeners = this._events && this._events[eventName];
			if (!listeners || !listeners.length) return;
			var index = listeners.indexOf(listener);
			if (index != -1) listeners.splice(index, 1);
			return this;
		};
		proto.emitEvent = function(eventName, args) {
			var listeners = this._events && this._events[eventName];
			if (!listeners || !listeners.length) return;
			listeners = listeners.slice(0);
			args = args || [];
			var onceListeners = this._onceEvents && this._onceEvents[eventName];
			for (var i = 0; i < listeners.length; i++) {
				var listener = listeners[i];
				if (onceListeners && onceListeners[listener]) {
					this.off(eventName, listener);
					delete onceListeners[listener];
				}
				listener.apply(this, args);
			}
			return this;
		};
		proto.allOff = function() {
			delete this._events;
			delete this._onceEvents;
		};
		return EvEmitter;
	});
}));
// node_modules/unipointer/unipointer.js
var require_unipointer = /* @__PURE__ */ __commonJSMin(((exports, module) => {
	/*!
	* Unipointer v2.4.0
	* base class for doing one thing with pointer event
	* MIT license
	*/
	(function(window, factory) {
		if (typeof define == "function" && define.amd) define(["ev-emitter/ev-emitter"], function(EvEmitter) {
			return factory(window, EvEmitter);
		});
		else if (typeof module == "object" && module.exports) module.exports = factory(window, require_ev_emitter());
		else window.Unipointer = factory(window, window.EvEmitter);
	})(window, function factory(window, EvEmitter) {
		"use strict";
		function noop() {}
		function Unipointer() {}
		var proto = Unipointer.prototype = Object.create(EvEmitter.prototype);
		proto.bindStartEvent = function(elem) {
			this._bindStartEvent(elem, true);
		};
		proto.unbindStartEvent = function(elem) {
			this._bindStartEvent(elem, false);
		};
		/**
		* Add or remove start event
		* @param {Boolean} isAdd - remove if falsey
		*/
		proto._bindStartEvent = function(elem, isAdd) {
			isAdd = isAdd === void 0 ? true : isAdd;
			var bindMethod = isAdd ? "addEventListener" : "removeEventListener";
			var startEvent = "mousedown";
			if ("ontouchstart" in window) startEvent = "touchstart";
			else if (window.PointerEvent) startEvent = "pointerdown";
			elem[bindMethod](startEvent, this);
		};
		proto.handleEvent = function(event) {
			var method = "on" + event.type;
			if (this[method]) this[method](event);
		};
		proto.getTouch = function(touches) {
			for (var i = 0; i < touches.length; i++) {
				var touch = touches[i];
				if (touch.identifier == this.pointerIdentifier) return touch;
			}
		};
		proto.onmousedown = function(event) {
			var button = event.button;
			if (button && button !== 0 && button !== 1) return;
			this._pointerDown(event, event);
		};
		proto.ontouchstart = function(event) {
			this._pointerDown(event, event.changedTouches[0]);
		};
		proto.onpointerdown = function(event) {
			this._pointerDown(event, event);
		};
		/**
		* pointer start
		* @param {Event} event
		* @param {Event or Touch} pointer
		*/
		proto._pointerDown = function(event, pointer) {
			if (event.button || this.isPointerDown) return;
			this.isPointerDown = true;
			this.pointerIdentifier = pointer.pointerId !== void 0 ? pointer.pointerId : pointer.identifier;
			this.pointerDown(event, pointer);
		};
		proto.pointerDown = function(event, pointer) {
			this._bindPostStartEvents(event);
			this.emitEvent("pointerDown", [event, pointer]);
		};
		var postStartEvents = {
			mousedown: ["mousemove", "mouseup"],
			touchstart: [
				"touchmove",
				"touchend",
				"touchcancel"
			],
			pointerdown: [
				"pointermove",
				"pointerup",
				"pointercancel"
			]
		};
		proto._bindPostStartEvents = function(event) {
			if (!event) return;
			var events = postStartEvents[event.type];
			events.forEach(function(eventName) {
				window.addEventListener(eventName, this);
			}, this);
			this._boundPointerEvents = events;
		};
		proto._unbindPostStartEvents = function() {
			if (!this._boundPointerEvents) return;
			this._boundPointerEvents.forEach(function(eventName) {
				window.removeEventListener(eventName, this);
			}, this);
			delete this._boundPointerEvents;
		};
		proto.onmousemove = function(event) {
			this._pointerMove(event, event);
		};
		proto.onpointermove = function(event) {
			if (event.pointerId == this.pointerIdentifier) this._pointerMove(event, event);
		};
		proto.ontouchmove = function(event) {
			var touch = this.getTouch(event.changedTouches);
			if (touch) this._pointerMove(event, touch);
		};
		/**
		* pointer move
		* @param {Event} event
		* @param {Event or Touch} pointer
		* @private
		*/
		proto._pointerMove = function(event, pointer) {
			this.pointerMove(event, pointer);
		};
		proto.pointerMove = function(event, pointer) {
			this.emitEvent("pointerMove", [event, pointer]);
		};
		proto.onmouseup = function(event) {
			this._pointerUp(event, event);
		};
		proto.onpointerup = function(event) {
			if (event.pointerId == this.pointerIdentifier) this._pointerUp(event, event);
		};
		proto.ontouchend = function(event) {
			var touch = this.getTouch(event.changedTouches);
			if (touch) this._pointerUp(event, touch);
		};
		/**
		* pointer up
		* @param {Event} event
		* @param {Event or Touch} pointer
		* @private
		*/
		proto._pointerUp = function(event, pointer) {
			this._pointerDone();
			this.pointerUp(event, pointer);
		};
		proto.pointerUp = function(event, pointer) {
			this.emitEvent("pointerUp", [event, pointer]);
		};
		proto._pointerDone = function() {
			this._pointerReset();
			this._unbindPostStartEvents();
			this.pointerDone();
		};
		proto._pointerReset = function() {
			this.isPointerDown = false;
			delete this.pointerIdentifier;
		};
		proto.pointerDone = noop;
		proto.onpointercancel = function(event) {
			if (event.pointerId == this.pointerIdentifier) this._pointerCancel(event, event);
		};
		proto.ontouchcancel = function(event) {
			var touch = this.getTouch(event.changedTouches);
			if (touch) this._pointerCancel(event, touch);
		};
		/**
		* pointer cancel
		* @param {Event} event
		* @param {Event or Touch} pointer
		* @private
		*/
		proto._pointerCancel = function(event, pointer) {
			this._pointerDone();
			this.pointerCancel(event, pointer);
		};
		proto.pointerCancel = function(event, pointer) {
			this.emitEvent("pointerCancel", [event, pointer]);
		};
		Unipointer.getPointerPoint = function(pointer) {
			return {
				x: pointer.pageX,
				y: pointer.pageY
			};
		};
		return Unipointer;
	});
}));
// resources/assets/js/globals.js
var import_huebee = /* @__PURE__ */ __toESM((/* @__PURE__ */ __commonJSMin(((exports, module) => {
	/**
	* Huebee v2.0.0
	* 1-click color picker
	* MIT license
	* http://huebee.buzz
	* Copyright 2018 Metafizzy
	*/
	(function(window, factory) {
		if (typeof define == "function" && define.amd) define(["ev-emitter/ev-emitter", "unipointer/unipointer"], function(EvEmitter, Unipointer) {
			return factory(window, EvEmitter, Unipointer);
		});
		else if (typeof module == "object" && module.exports) module.exports = factory(window, require_ev_emitter(), require_unipointer());
		else window.Huebee = factory(window, window.EvEmitter, window.Unipointer);
	})(window, function factory(window, EvEmitter, Unipointer) {
		function Huebee(anchor, options) {
			anchor = getQueryElement(anchor);
			if (!anchor) throw "Bad element for Huebee: " + anchor;
			this.anchor = anchor;
			this.options = {};
			this.option(Huebee.defaults);
			this.option(options);
			this.create();
		}
		Huebee.defaults = {
			hues: 12,
			hue0: 0,
			shades: 5,
			saturations: 3,
			notation: "shortHex",
			setText: true,
			setBGColor: true
		};
		var proto = Huebee.prototype = Object.create(EvEmitter.prototype);
		proto.option = function(options) {
			this.options = extend(this.options, options);
		};
		var GUID = 0;
		var instances = {};
		proto.create = function() {
			var guid = this.guid = ++GUID;
			this.anchor.huebeeGUID = guid;
			instances[guid] = this;
			this.setBGElems = this.getSetElems(this.options.setBGColor);
			this.setTextElems = this.getSetElems(this.options.setText);
			this.outsideCloseIt = this.outsideClose.bind(this);
			this.onDocKeydown = this.docKeydown.bind(this);
			this.closeIt = this.close.bind(this);
			this.openIt = this.open.bind(this);
			this.onElemTransitionend = this.elemTransitionend.bind(this);
			this.isInputAnchor = this.anchor.nodeName == "INPUT";
			if (!this.options.staticOpen) {
				this.anchor.addEventListener("click", this.openIt);
				this.anchor.addEventListener("focus", this.openIt);
			}
			if (this.isInputAnchor) this.anchor.addEventListener("input", this.inputInput.bind(this));
			var element = this.element = document.createElement("div");
			element.className = "huebee ";
			element.className += this.options.staticOpen ? "is-static-open " : "is-hidden ";
			element.className += this.options.className || "";
			var container = this.container = document.createElement("div");
			container.className = "huebee__container";
			function onContainerPointerStart(event) {
				if (event.target == container) event.preventDefault();
			}
			container.addEventListener("mousedown", onContainerPointerStart);
			container.addEventListener("touchstart", onContainerPointerStart);
			this.createCanvas();
			this.cursor = document.createElement("div");
			this.cursor.className = "huebee__cursor is-hidden";
			container.appendChild(this.cursor);
			this.createCloseButton();
			element.appendChild(container);
			if (!this.options.staticOpen) {
				var parentStyle = getComputedStyle(this.anchor.parentNode);
				if (parentStyle.position != "relative" && parentStyle.position != "absolute") this.anchor.parentNode.style.position = "relative";
			}
			var hues = this.options.hues;
			var customColors = this.options.customColors;
			var customLength = customColors && customColors.length;
			this.satY = customLength ? Math.ceil(customLength / hues) + 1 : 0;
			this.updateColors();
			this.setAnchorColor();
			if (this.options.staticOpen) this.open();
		};
		proto.getSetElems = function(option) {
			if (option === true) return [this.anchor];
			else if (typeof option == "string") return document.querySelectorAll(option);
		};
		proto.createCanvas = function() {
			var canvas = this.canvas = document.createElement("canvas");
			canvas.className = "huebee__canvas";
			this.ctx = canvas.getContext("2d");
			var canvasPointer = this.canvasPointer = new Unipointer();
			canvasPointer._bindStartEvent(canvas);
			canvasPointer.on("pointerDown", this.canvasPointerDown.bind(this));
			canvasPointer.on("pointerMove", this.canvasPointerMove.bind(this));
			this.container.appendChild(canvas);
		};
		var svgURI = "http://www.w3.org/2000/svg";
		proto.createCloseButton = function() {
			if (this.options.staticOpen) return;
			var svg = document.createElementNS(svgURI, "svg");
			svg.setAttribute("class", "huebee__close-button");
			svg.setAttribute("viewBox", "0 0 24 24");
			svg.setAttribute("width", "24");
			svg.setAttribute("height", "24");
			var path = document.createElementNS(svgURI, "path");
			path.setAttribute("d", "M 7,7 L 17,17 M 17,7 L 7,17");
			path.setAttribute("class", "huebee__close-button__x");
			svg.appendChild(path);
			svg.addEventListener("click", this.closeIt);
			this.container.appendChild(svg);
		};
		proto.updateColors = function() {
			this.swatches = {};
			this.colorGrid = {};
			this.updateColorModer();
			var shades = this.options.shades;
			var sats = this.options.saturations;
			var hues = this.options.hues;
			var customColors = this.options.customColors;
			if (customColors && customColors.length) {
				var customI = 0;
				customColors.forEach(function(color) {
					var x = customI % hues;
					var y = Math.floor(customI / hues);
					var swatch = getSwatch(color);
					if (swatch) {
						this.addSwatch(swatch, x, y);
						customI++;
					}
				}.bind(this));
			}
			for (var i = 0; i < sats; i++) {
				var sat = 1 - i / sats;
				var yOffset = shades * i + this.satY;
				this.updateSaturationGrid(i, sat, yOffset);
			}
			for (i = 0; i < shades + 2; i++) {
				var lum = 1 - i / (shades + 1);
				var swatch = getSwatch(this.colorModer(0, 0, lum));
				this.addSwatch(swatch, hues + 1, i);
			}
		};
		proto.updateSaturationGrid = function(i, sat, yOffset) {
			var shades = this.options.shades;
			var hues = this.options.hues;
			var hue0 = this.options.hue0;
			for (var row = 0; row < shades; row++) for (var col = 0; col < hues; col++) {
				var hue = Math.round(col * 360 / hues + hue0) % 360;
				var lum = 1 - (row + 1) / (shades + 1);
				var swatch = getSwatch(this.colorModer(hue, sat, lum));
				var gridY = row + yOffset;
				this.addSwatch(swatch, col, gridY);
			}
		};
		proto.addSwatch = function(swatch, gridX, gridY) {
			this.swatches[gridX + "," + gridY] = swatch;
			this.colorGrid[swatch.color.toUpperCase()] = {
				x: gridX,
				y: gridY
			};
		};
		var colorModers = {
			hsl: function(h, s, l) {
				s = Math.round(s * 100);
				l = Math.round(l * 100);
				return "hsl(" + h + ", " + s + "%, " + l + "%)";
			},
			hex: hsl2hex,
			shortHex: function(h, s, l) {
				return roundHex(hsl2hex(h, s, l));
			}
		};
		proto.updateColorModer = function() {
			this.colorModer = colorModers[this.options.notation] || colorModers.shortHex;
		};
		proto.renderColors = function() {
			var gridSize = this.gridSize * 2;
			for (var position in this.swatches) {
				var swatch = this.swatches[position];
				var duple = position.split(",");
				var gridX = duple[0];
				var gridY = duple[1];
				this.ctx.fillStyle = swatch.color;
				this.ctx.fillRect(gridX * gridSize, gridY * gridSize, gridSize, gridSize);
			}
		};
		proto.setAnchorColor = function() {
			if (this.isInputAnchor) this.setColor(this.anchor.value);
		};
		var docElem = document.documentElement;
		proto.open = function() {
			if (this.isOpen) return;
			var anchor = this.anchor;
			var elem = this.element;
			if (!this.options.staticOpen) {
				elem.style.left = anchor.offsetLeft + "px";
				elem.style.top = anchor.offsetTop + anchor.offsetHeight + "px";
			}
			this.bindOpenEvents(true);
			elem.removeEventListener("transitionend", this.onElemTransitionend);
			anchor.parentNode.insertBefore(elem, anchor.nextSibling);
			var duration = getComputedStyle(elem).transitionDuration;
			this.hasTransition = duration && duration != "none" && parseFloat(duration);
			this.isOpen = true;
			this.updateSizes();
			this.renderColors();
			this.setAnchorColor();
			elem.offsetHeight;
			elem.classList.remove("is-hidden");
		};
		proto.bindOpenEvents = function(isAdd) {
			if (this.options.staticOpen) return;
			var method = (isAdd ? "add" : "remove") + "EventListener";
			docElem[method]("mousedown", this.outsideCloseIt);
			docElem[method]("touchstart", this.outsideCloseIt);
			document[method]("focusin", this.outsideCloseIt);
			document[method]("keydown", this.onDocKeydown);
			this.anchor[method]("blur", this.closeIt);
		};
		proto.updateSizes = function() {
			var hues = this.options.hues;
			var shades = this.options.shades;
			var sats = this.options.saturations;
			this.cursorBorder = parseInt(getComputedStyle(this.cursor).borderTopWidth, 10);
			this.gridSize = Math.round(this.cursor.offsetWidth - this.cursorBorder * 2);
			this.canvasOffset = {
				x: this.canvas.offsetLeft,
				y: this.canvas.offsetTop
			};
			var height = Math.max(shades * sats + this.satY, shades + 2);
			var width = this.gridSize * (hues + 2);
			this.canvas.width = width * 2;
			this.canvas.style.width = width + "px";
			this.canvas.height = this.gridSize * height * 2;
		};
		proto.outsideClose = function(event) {
			var isAnchor = this.anchor.contains(event.target);
			var isElement = this.element.contains(event.target);
			if (!isAnchor && !isElement) this.close();
		};
		var closeKeydowns = {
			13: true,
			27: true
		};
		proto.docKeydown = function(event) {
			if (closeKeydowns[event.keyCode]) this.close();
		};
		var supportsTransitions = typeof docElem.style.transform == "string";
		proto.close = function() {
			if (!this.isOpen) return;
			if (supportsTransitions && this.hasTransition) this.element.addEventListener("transitionend", this.onElemTransitionend);
			else this.remove();
			this.element.classList.add("is-hidden");
			this.bindOpenEvents(false);
			this.isOpen = false;
		};
		proto.remove = function() {
			var parent = this.element.parentNode;
			if (parent.contains(this.element)) parent.removeChild(this.element);
		};
		proto.elemTransitionend = function(event) {
			if (event.target != this.element) return;
			this.element.removeEventListener("transitionend", this.onElemTransitionend);
			this.remove();
		};
		proto.inputInput = function() {
			this.setColor(this.anchor.value);
		};
		proto.canvasPointerDown = function(event, pointer) {
			event.preventDefault();
			this.updateOffset();
			this.canvasPointerChange(pointer);
		};
		proto.updateOffset = function() {
			var boundingRect = this.canvas.getBoundingClientRect();
			this.offset = {
				x: boundingRect.left + window.pageXOffset,
				y: boundingRect.top + window.pageYOffset
			};
		};
		proto.canvasPointerMove = function(event, pointer) {
			this.canvasPointerChange(pointer);
		};
		proto.canvasPointerChange = function(pointer) {
			var x = Math.round(pointer.pageX - this.offset.x);
			var y = Math.round(pointer.pageY - this.offset.y);
			var gridSize = this.gridSize;
			var sx = Math.floor(x / gridSize);
			var sy = Math.floor(y / gridSize);
			var swatch = this.swatches[sx + "," + sy];
			this.setSwatch(swatch);
		};
		proto.setColor = function(color) {
			var swatch = getSwatch(color);
			this.setSwatch(swatch);
		};
		proto.setSwatch = function(swatch) {
			var color = swatch && swatch.color;
			if (!swatch) return;
			var wasSameColor = color == this.color;
			this.color = color;
			this.hue = swatch.hue;
			this.sat = swatch.sat;
			this.lum = swatch.lum;
			var lightness = this.lum - Math.cos((this.hue + 70) / 180 * Math.PI) * .15;
			this.isLight = lightness > .5;
			var gridPosition = this.colorGrid[color.toUpperCase()];
			this.updateCursor(gridPosition);
			this.setTexts();
			this.setBackgrounds();
			if (!wasSameColor) this.emitEvent("change", [
				color,
				swatch.hue,
				swatch.sat,
				swatch.lum
			]);
		};
		proto.setTexts = function() {
			if (!this.setTextElems) return;
			for (var i = 0; i < this.setTextElems.length; i++) {
				var elem = this.setTextElems[i];
				var property = elem.nodeName == "INPUT" ? "value" : "textContent";
				elem[property] = this.color;
			}
		};
		proto.setBackgrounds = function() {
			if (!this.setBGElems) return;
			var textColor = this.isLight ? "#222" : "white";
			for (var i = 0; i < this.setBGElems.length; i++) {
				var elem = this.setBGElems[i];
				elem.style.backgroundColor = this.color;
				elem.style.color = textColor;
			}
		};
		proto.updateCursor = function(position) {
			if (!this.isOpen) return;
			var classMethod = position ? "remove" : "add";
			this.cursor.classList[classMethod]("is-hidden");
			if (!position) return;
			var gridSize = this.gridSize;
			var offset = this.canvasOffset;
			var border = this.cursorBorder;
			this.cursor.style.left = position.x * gridSize + offset.x - border + "px";
			this.cursor.style.top = position.y * gridSize + offset.y - border + "px";
		};
		var console = window.console;
		function htmlInit() {
			var elems = document.querySelectorAll("[data-huebee]");
			for (var i = 0; i < elems.length; i++) {
				var elem = elems[i];
				var attr = elem.getAttribute("data-huebee");
				var options;
				try {
					options = attr && JSON.parse(attr);
				} catch (error) {
					if (console) console.error("Error parsing data-huebee on " + elem.className + ": " + error);
					continue;
				}
				new Huebee(elem, options);
			}
		}
		var readyState = document.readyState;
		if (readyState == "complete" || readyState == "interactive") htmlInit();
		else document.addEventListener("DOMContentLoaded", htmlInit);
		Huebee.data = function(elem) {
			elem = getQueryElement(elem);
			var id = elem && elem.huebeeGUID;
			return id && instances[id];
		};
		var proxyCanvas = document.createElement("canvas");
		proxyCanvas.width = proxyCanvas.height = 1;
		var proxyCtx = proxyCanvas.getContext("2d");
		function getSwatch(color) {
			proxyCtx.clearRect(0, 0, 1, 1);
			proxyCtx.fillStyle = "#010203";
			proxyCtx.fillStyle = color;
			proxyCtx.fillRect(0, 0, 1, 1);
			var data = proxyCtx.getImageData(0, 0, 1, 1).data;
			data = [
				data[0],
				data[1],
				data[2],
				data[3]
			];
			if (data.join(",") == "1,2,3,255") return;
			var hsl = rgb2hsl.apply(this, data);
			return {
				color: color.trim(),
				hue: hsl[0],
				sat: hsl[1],
				lum: hsl[2]
			};
		}
		function extend(a, b) {
			for (var prop in b) a[prop] = b[prop];
			return a;
		}
		function getQueryElement(elem) {
			if (typeof elem == "string") elem = document.querySelector(elem);
			return elem;
		}
		function hsl2hex(h, s, l) {
			return rgb2hex(hsl2rgb(h, s, l));
		}
		function hsl2rgb(h, s, l) {
			var C = (1 - Math.abs(2 * l - 1)) * s;
			var hp = h / 60;
			var X = C * (1 - Math.abs(hp % 2 - 1));
			var rgb, m;
			switch (Math.floor(hp)) {
				case 0:
					rgb = [
						C,
						X,
						0
					];
					break;
				case 1:
					rgb = [
						X,
						C,
						0
					];
					break;
				case 2:
					rgb = [
						0,
						C,
						X
					];
					break;
				case 3:
					rgb = [
						0,
						X,
						C
					];
					break;
				case 4:
					rgb = [
						X,
						0,
						C
					];
					break;
				case 5:
					rgb = [
						C,
						0,
						X
					];
					break;
				default: rgb = [
					0,
					0,
					0
				];
			}
			m = l - C / 2;
			rgb = rgb.map(function(v) {
				return v + m;
			});
			return rgb;
		}
		function rgb2hsl(r, g, b) {
			r /= 255;
			g /= 255;
			b /= 255;
			var M = Math.max(r, g, b);
			var m = Math.min(r, g, b);
			var C = M - m;
			var L = .5 * (M + m);
			var S = C === 0 ? 0 : C / (1 - Math.abs(2 * L - 1));
			var h;
			if (C === 0) h = 0;
			else if (M === r) h = (g - b) / C % 6;
			else if (M === g) h = (b - r) / C + 2;
			else if (M === b) h = (r - g) / C + 4;
			return [
				60 * h,
				parseFloat(S),
				parseFloat(L)
			];
		}
		function rgb2hex(rgb) {
			return "#" + rgb.map(function(value) {
				value = Math.round(value * 255);
				var hexNum = value.toString(16).toUpperCase();
				hexNum = hexNum.length < 2 ? "0" + hexNum : hexNum;
				return hexNum;
			}).join("");
		}
		function roundHex(hex) {
			return "#" + hex[1] + hex[3] + hex[5];
		}
		return Huebee;
	});
})))(), 1);
window.Sortable = Sortable$1;
window.Huebee = import_huebee.default;
// resources/assets/js/jquery-ui.min.js
/*! jQuery UI - v1.13.2 - 2023-02-04
* http://jqueryui.com
* Includes: widget.js, position.js, data.js, keycode.js, scroll-parent.js, unique-id.js, widgets/draggable.js, widgets/sortable.js, widgets/autocomplete.js, widgets/menu.js, widgets/mouse.js
* Copyright jQuery Foundation and other contributors; Licensed MIT */
(function(t) {
	"use strict";
	"function" == typeof define && define.amd ? define(["jquery"], t) : t(jQuery);
})(function(w) {
	"use strict";
	w.ui = w.ui || {};
	w.ui.version = "1.13.2";
	var o, i = 0, r = Array.prototype.hasOwnProperty, h = Array.prototype.slice;
	w.cleanData = (o = w.cleanData, function(t) {
		for (var e, i, s = 0; null != (i = t[s]); s++) (e = w._data(i, "events")) && e.remove && w(i).triggerHandler("remove");
		o(t);
	}), w.widget = function(t, i, e) {
		var s, o, n, r = {}, h = t.split(".")[0], a = h + "-" + (t = t.split(".")[1]);
		return e || (e = i, i = w.Widget), Array.isArray(e) && (e = w.extend.apply(null, [{}].concat(e))), w.expr.pseudos[a.toLowerCase()] = function(t) {
			return !!w.data(t, a);
		}, w[h] = w[h] || {}, s = w[h][t], o = w[h][t] = function(t, e) {
			if (!this || !this._createWidget) return new o(t, e);
			arguments.length && this._createWidget(t, e);
		}, w.extend(o, s, {
			version: e.version,
			_proto: w.extend({}, e),
			_childConstructors: []
		}), (n = new i()).options = w.widget.extend({}, n.options), w.each(e, function(e, s) {
			function o() {
				return i.prototype[e].apply(this, arguments);
			}
			function n(t) {
				return i.prototype[e].apply(this, t);
			}
			r[e] = "function" == typeof s ? function() {
				var t, e = this._super, i = this._superApply;
				return this._super = o, this._superApply = n, t = s.apply(this, arguments), this._super = e, this._superApply = i, t;
			} : s;
		}), o.prototype = w.widget.extend(n, { widgetEventPrefix: s && n.widgetEventPrefix || t }, r, {
			constructor: o,
			namespace: h,
			widgetName: t,
			widgetFullName: a
		}), s ? (w.each(s._childConstructors, function(t, e) {
			var i = e.prototype;
			w.widget(i.namespace + "." + i.widgetName, o, e._proto);
		}), delete s._childConstructors) : i._childConstructors.push(o), w.widget.bridge(t, o), o;
	}, w.widget.extend = function(t) {
		for (var e, i, s = h.call(arguments, 1), o = 0, n = s.length; o < n; o++) for (e in s[o]) i = s[o][e], r.call(s[o], e) && void 0 !== i && (w.isPlainObject(i) ? t[e] = w.isPlainObject(t[e]) ? w.widget.extend({}, t[e], i) : w.widget.extend({}, i) : t[e] = i);
		return t;
	}, w.widget.bridge = function(n, e) {
		var r = e.prototype.widgetFullName || n;
		w.fn[n] = function(i) {
			var t = "string" == typeof i, s = h.call(arguments, 1), o = this;
			return t ? this.length || "instance" !== i ? this.each(function() {
				var t, e = w.data(this, r);
				return "instance" === i ? (o = e, !1) : e ? "function" != typeof e[i] || "_" === i.charAt(0) ? w.error("no such method '" + i + "' for " + n + " widget instance") : (t = e[i].apply(e, s)) !== e && void 0 !== t ? (o = t && t.jquery ? o.pushStack(t.get()) : t, !1) : void 0 : w.error("cannot call methods on " + n + " prior to initialization; attempted to call method '" + i + "'");
			}) : o = void 0 : (s.length && (i = w.widget.extend.apply(null, [i].concat(s))), this.each(function() {
				var t = w.data(this, r);
				t ? (t.option(i || {}), t._init && t._init()) : w.data(this, r, new e(i, this));
			})), o;
		};
	}, w.Widget = function() {}, w.Widget._childConstructors = [], w.Widget.prototype = {
		widgetName: "widget",
		widgetEventPrefix: "",
		defaultElement: "<div>",
		options: {
			classes: {},
			disabled: !1,
			create: null
		},
		_createWidget: function(t, e) {
			e = w(e || this.defaultElement || this)[0], this.element = w(e), this.uuid = i++, this.eventNamespace = "." + this.widgetName + this.uuid, this.bindings = w(), this.hoverable = w(), this.focusable = w(), this.classesElementLookup = {}, e !== this && (w.data(e, this.widgetFullName, this), this._on(!0, this.element, { remove: function(t) {
				t.target === e && this.destroy();
			} }), this.document = w(e.style ? e.ownerDocument : e.document || e), this.window = w(this.document[0].defaultView || this.document[0].parentWindow)), this.options = w.widget.extend({}, this.options, this._getCreateOptions(), t), this._create(), this.options.disabled && this._setOptionDisabled(this.options.disabled), this._trigger("create", null, this._getCreateEventData()), this._init();
		},
		_getCreateOptions: function() {
			return {};
		},
		_getCreateEventData: w.noop,
		_create: w.noop,
		_init: w.noop,
		destroy: function() {
			var i = this;
			this._destroy(), w.each(this.classesElementLookup, function(t, e) {
				i._removeClass(e, t);
			}), this.element.off(this.eventNamespace).removeData(this.widgetFullName), this.widget().off(this.eventNamespace).removeAttr("aria-disabled"), this.bindings.off(this.eventNamespace);
		},
		_destroy: w.noop,
		widget: function() {
			return this.element;
		},
		option: function(t, e) {
			var i, s, o, n = t;
			if (0 === arguments.length) return w.widget.extend({}, this.options);
			if ("string" == typeof t) if (n = {}, t = (i = t.split(".")).shift(), i.length) {
				for (s = n[t] = w.widget.extend({}, this.options[t]), o = 0; o < i.length - 1; o++) s[i[o]] = s[i[o]] || {}, s = s[i[o]];
				if (t = i.pop(), 1 === arguments.length) return void 0 === s[t] ? null : s[t];
				s[t] = e;
			} else {
				if (1 === arguments.length) return void 0 === this.options[t] ? null : this.options[t];
				n[t] = e;
			}
			return this._setOptions(n), this;
		},
		_setOptions: function(t) {
			for (var e in t) this._setOption(e, t[e]);
			return this;
		},
		_setOption: function(t, e) {
			return "classes" === t && this._setOptionClasses(e), this.options[t] = e, "disabled" === t && this._setOptionDisabled(e), this;
		},
		_setOptionClasses: function(t) {
			var e, i, s;
			for (e in t) s = this.classesElementLookup[e], t[e] !== this.options.classes[e] && s && s.length && (i = w(s.get()), this._removeClass(s, e), i.addClass(this._classes({
				element: i,
				keys: e,
				classes: t,
				add: !0
			})));
		},
		_setOptionDisabled: function(t) {
			this._toggleClass(this.widget(), this.widgetFullName + "-disabled", null, !!t), t && (this._removeClass(this.hoverable, null, "ui-state-hover"), this._removeClass(this.focusable, null, "ui-state-focus"));
		},
		enable: function() {
			return this._setOptions({ disabled: !1 });
		},
		disable: function() {
			return this._setOptions({ disabled: !0 });
		},
		_classes: function(o) {
			var n = [], r = this;
			function t(t, e) {
				for (var i, s = 0; s < t.length; s++) i = r.classesElementLookup[t[s]] || w(), i = o.add ? (function() {
					var i = [];
					o.element.each(function(t, e) {
						w.map(r.classesElementLookup, function(t) {
							return t;
						}).some(function(t) {
							return t.is(e);
						}) || i.push(e);
					}), r._on(w(i), { remove: "_untrackClassesElement" });
				}(), w(w.uniqueSort(i.get().concat(o.element.get())))) : w(i.not(o.element).get()), r.classesElementLookup[t[s]] = i, n.push(t[s]), e && o.classes[t[s]] && n.push(o.classes[t[s]]);
			}
			return (o = w.extend({
				element: this.element,
				classes: this.options.classes || {}
			}, o)).keys && t(o.keys.match(/\S+/g) || [], !0), o.extra && t(o.extra.match(/\S+/g) || []), n.join(" ");
		},
		_untrackClassesElement: function(i) {
			var s = this;
			w.each(s.classesElementLookup, function(t, e) {
				-1 !== w.inArray(i.target, e) && (s.classesElementLookup[t] = w(e.not(i.target).get()));
			}), this._off(w(i.target));
		},
		_removeClass: function(t, e, i) {
			return this._toggleClass(t, e, i, !1);
		},
		_addClass: function(t, e, i) {
			return this._toggleClass(t, e, i, !0);
		},
		_toggleClass: function(t, e, i, s) {
			var o = "string" == typeof t || null === t, i = {
				extra: o ? e : i,
				keys: o ? t : e,
				element: o ? this.element : t,
				add: s = "boolean" == typeof s ? s : i
			};
			return i.element.toggleClass(this._classes(i), s), this;
		},
		_on: function(o, n, t) {
			var r, h = this;
			"boolean" != typeof o && (t = n, n = o, o = !1), t ? (n = r = w(n), this.bindings = this.bindings.add(n)) : (t = n, n = this.element, r = this.widget()), w.each(t, function(t, e) {
				function i() {
					if (o || !0 !== h.options.disabled && !w(this).hasClass("ui-state-disabled")) return ("string" == typeof e ? h[e] : e).apply(h, arguments);
				}
				"string" != typeof e && (i.guid = e.guid = e.guid || i.guid || w.guid++);
				var s = t.match(/^([\w:-]*)\s*(.*)$/), t = s[1] + h.eventNamespace, s = s[2];
				s ? r.on(t, s, i) : n.on(t, i);
			});
		},
		_off: function(t, e) {
			e = (e || "").split(" ").join(this.eventNamespace + " ") + this.eventNamespace, t.off(e), this.bindings = w(this.bindings.not(t).get()), this.focusable = w(this.focusable.not(t).get()), this.hoverable = w(this.hoverable.not(t).get());
		},
		_delay: function(t, e) {
			var i = this;
			return setTimeout(function() {
				return ("string" == typeof t ? i[t] : t).apply(i, arguments);
			}, e || 0);
		},
		_hoverable: function(t) {
			this.hoverable = this.hoverable.add(t), this._on(t, {
				mouseenter: function(t) {
					this._addClass(w(t.currentTarget), null, "ui-state-hover");
				},
				mouseleave: function(t) {
					this._removeClass(w(t.currentTarget), null, "ui-state-hover");
				}
			});
		},
		_focusable: function(t) {
			this.focusable = this.focusable.add(t), this._on(t, {
				focusin: function(t) {
					this._addClass(w(t.currentTarget), null, "ui-state-focus");
				},
				focusout: function(t) {
					this._removeClass(w(t.currentTarget), null, "ui-state-focus");
				}
			});
		},
		_trigger: function(t, e, i) {
			var s, o, n = this.options[t];
			if (i = i || {}, (e = w.Event(e)).type = (t === this.widgetEventPrefix ? t : this.widgetEventPrefix + t).toLowerCase(), e.target = this.element[0], o = e.originalEvent) for (s in o) s in e || (e[s] = o[s]);
			return this.element.trigger(e, i), !("function" == typeof n && !1 === n.apply(this.element[0], [e].concat(i)) || e.isDefaultPrevented());
		}
	}, w.each({
		show: "fadeIn",
		hide: "fadeOut"
	}, function(n, r) {
		w.Widget.prototype["_" + n] = function(e, t, i) {
			var s, o = (t = "string" == typeof t ? { effect: t } : t) ? !0 !== t && "number" != typeof t && t.effect || r : n;
			"number" == typeof (t = t || {}) ? t = { duration: t } : !0 === t && (t = {}), s = !w.isEmptyObject(t), t.complete = i, t.delay && e.delay(t.delay), s && w.effects && w.effects.effect[o] ? e[n](t) : o !== n && e[o] ? e[o](t.duration, t.easing, i) : e.queue(function(t) {
				w(this)[n](), i && i.call(e[0]), t();
			});
		};
	});
	var s, x, I, n, a, l, c, u, C;
	w.widget;
	function T(t, e, i) {
		return [parseFloat(t[0]) * (u.test(t[0]) ? e / 100 : 1), parseFloat(t[1]) * (u.test(t[1]) ? i / 100 : 1)];
	}
	function k(t, e) {
		return parseInt(w.css(t, e), 10) || 0;
	}
	function H(t) {
		return null != t && t === t.window;
	}
	x = Math.max, I = Math.abs, n = /left|center|right/, a = /top|center|bottom/, l = /[\+\-]\d+(\.[\d]+)?%?/, c = /^\w+/, u = /%$/, C = w.fn.position, w.position = {
		scrollbarWidth: function() {
			if (void 0 !== s) return s;
			var t, e = w("<div style='display:block;position:absolute;width:200px;height:200px;overflow:hidden;'><div style='height:300px;width:auto;'></div></div>"), i = e.children()[0];
			return w("body").append(e), t = i.offsetWidth, e.css("overflow", "scroll"), t === (i = i.offsetWidth) && (i = e[0].clientWidth), e.remove(), s = t - i;
		},
		getScrollInfo: function(t) {
			var e = t.isWindow || t.isDocument ? "" : t.element.css("overflow-x"), i = t.isWindow || t.isDocument ? "" : t.element.css("overflow-y"), e = "scroll" === e || "auto" === e && t.width < t.element[0].scrollWidth;
			return {
				width: "scroll" === i || "auto" === i && t.height < t.element[0].scrollHeight ? w.position.scrollbarWidth() : 0,
				height: e ? w.position.scrollbarWidth() : 0
			};
		},
		getWithinInfo: function(t) {
			var e = w(t || window), i = H(e[0]), s = !!e[0] && 9 === e[0].nodeType;
			return {
				element: e,
				isWindow: i,
				isDocument: s,
				offset: !i && !s ? w(t).offset() : {
					left: 0,
					top: 0
				},
				scrollLeft: e.scrollLeft(),
				scrollTop: e.scrollTop(),
				width: e.outerWidth(),
				height: e.outerHeight()
			};
		}
	}, w.fn.position = function(u) {
		if (!u || !u.of) return C.apply(this, arguments);
		var p, f, d, m, g, t, v = "string" == typeof (u = w.extend({}, u)).of ? w(document).find(u.of) : w(u.of), _ = w.position.getWithinInfo(u.within), y = w.position.getScrollInfo(_), b = (u.collision || "flip").split(" "), P = {}, e = 9 === (t = (e = v)[0]).nodeType ? {
			width: e.width(),
			height: e.height(),
			offset: {
				top: 0,
				left: 0
			}
		} : H(t) ? {
			width: e.width(),
			height: e.height(),
			offset: {
				top: e.scrollTop(),
				left: e.scrollLeft()
			}
		} : t.preventDefault ? {
			width: 0,
			height: 0,
			offset: {
				top: t.pageY,
				left: t.pageX
			}
		} : {
			width: e.outerWidth(),
			height: e.outerHeight(),
			offset: e.offset()
		};
		return v[0].preventDefault && (u.at = "left top"), f = e.width, d = e.height, g = w.extend({}, m = e.offset), w.each(["my", "at"], function() {
			var t, e, i = (u[this] || "").split(" ");
			(i = 1 === i.length ? n.test(i[0]) ? i.concat(["center"]) : a.test(i[0]) ? ["center"].concat(i) : ["center", "center"] : i)[0] = n.test(i[0]) ? i[0] : "center", i[1] = a.test(i[1]) ? i[1] : "center", t = l.exec(i[0]), e = l.exec(i[1]), P[this] = [t ? t[0] : 0, e ? e[0] : 0], u[this] = [c.exec(i[0])[0], c.exec(i[1])[0]];
		}), 1 === b.length && (b[1] = b[0]), "right" === u.at[0] ? g.left += f : "center" === u.at[0] && (g.left += f / 2), "bottom" === u.at[1] ? g.top += d : "center" === u.at[1] && (g.top += d / 2), p = T(P.at, f, d), g.left += p[0], g.top += p[1], this.each(function() {
			var i, t, r = w(this), h = r.outerWidth(), a = r.outerHeight(), e = k(this, "marginLeft"), s = k(this, "marginTop"), o = h + e + k(this, "marginRight") + y.width, n = a + s + k(this, "marginBottom") + y.height, l = w.extend({}, g), c = T(P.my, r.outerWidth(), r.outerHeight());
			"right" === u.my[0] ? l.left -= h : "center" === u.my[0] && (l.left -= h / 2), "bottom" === u.my[1] ? l.top -= a : "center" === u.my[1] && (l.top -= a / 2), l.left += c[0], l.top += c[1], i = {
				marginLeft: e,
				marginTop: s
			}, w.each(["left", "top"], function(t, e) {
				w.ui.position[b[t]] && w.ui.position[b[t]][e](l, {
					targetWidth: f,
					targetHeight: d,
					elemWidth: h,
					elemHeight: a,
					collisionPosition: i,
					collisionWidth: o,
					collisionHeight: n,
					offset: [p[0] + c[0], p[1] + c[1]],
					my: u.my,
					at: u.at,
					within: _,
					elem: r
				});
			}), u.using && (t = function(t) {
				var e = m.left - l.left, i = e + f - h, s = m.top - l.top, o = s + d - a, n = {
					target: {
						element: v,
						left: m.left,
						top: m.top,
						width: f,
						height: d
					},
					element: {
						element: r,
						left: l.left,
						top: l.top,
						width: h,
						height: a
					},
					horizontal: i < 0 ? "left" : 0 < e ? "right" : "center",
					vertical: o < 0 ? "top" : 0 < s ? "bottom" : "middle"
				};
				f < h && I(e + i) < f && (n.horizontal = "center"), d < a && I(s + o) < d && (n.vertical = "middle"), x(I(e), I(i)) > x(I(s), I(o)) ? n.important = "horizontal" : n.important = "vertical", u.using.call(this, t, n);
			}), r.offset(w.extend(l, { using: t }));
		});
	}, w.ui.position = {
		fit: {
			left: function(t, e) {
				var i = e.within, s = i.isWindow ? i.scrollLeft : i.offset.left, o = i.width, n = t.left - e.collisionPosition.marginLeft, r = s - n, h = n + e.collisionWidth - o - s;
				e.collisionWidth > o ? 0 < r && h <= 0 ? (i = t.left + r + e.collisionWidth - o - s, t.left += r - i) : t.left = !(0 < h && r <= 0) && h < r ? s + o - e.collisionWidth : s : 0 < r ? t.left += r : 0 < h ? t.left -= h : t.left = x(t.left - n, t.left);
			},
			top: function(t, e) {
				var i = e.within, s = i.isWindow ? i.scrollTop : i.offset.top, o = e.within.height, n = t.top - e.collisionPosition.marginTop, r = s - n, h = n + e.collisionHeight - o - s;
				e.collisionHeight > o ? 0 < r && h <= 0 ? (i = t.top + r + e.collisionHeight - o - s, t.top += r - i) : t.top = !(0 < h && r <= 0) && h < r ? s + o - e.collisionHeight : s : 0 < r ? t.top += r : 0 < h ? t.top -= h : t.top = x(t.top - n, t.top);
			}
		},
		flip: {
			left: function(t, e) {
				var i = e.within, s = i.offset.left + i.scrollLeft, o = i.width, n = i.isWindow ? i.scrollLeft : i.offset.left, r = t.left - e.collisionPosition.marginLeft, h = r - n, a = r + e.collisionWidth - o - n, l = "left" === e.my[0] ? -e.elemWidth : "right" === e.my[0] ? e.elemWidth : 0, i = "left" === e.at[0] ? e.targetWidth : "right" === e.at[0] ? -e.targetWidth : 0, r = -2 * e.offset[0];
				h < 0 ? ((s = t.left + l + i + r + e.collisionWidth - o - s) < 0 || s < I(h)) && (t.left += l + i + r) : 0 < a && (0 < (n = t.left - e.collisionPosition.marginLeft + l + i + r - n) || I(n) < a) && (t.left += l + i + r);
			},
			top: function(t, e) {
				var i = e.within, s = i.offset.top + i.scrollTop, o = i.height, n = i.isWindow ? i.scrollTop : i.offset.top, r = t.top - e.collisionPosition.marginTop, h = r - n, a = r + e.collisionHeight - o - n, l = "top" === e.my[1] ? -e.elemHeight : "bottom" === e.my[1] ? e.elemHeight : 0, i = "top" === e.at[1] ? e.targetHeight : "bottom" === e.at[1] ? -e.targetHeight : 0, r = -2 * e.offset[1];
				h < 0 ? ((s = t.top + l + i + r + e.collisionHeight - o - s) < 0 || s < I(h)) && (t.top += l + i + r) : 0 < a && (0 < (n = t.top - e.collisionPosition.marginTop + l + i + r - n) || I(n) < a) && (t.top += l + i + r);
			}
		},
		flipfit: {
			left: function() {
				w.ui.position.flip.left.apply(this, arguments), w.ui.position.fit.left.apply(this, arguments);
			},
			top: function() {
				w.ui.position.flip.top.apply(this, arguments), w.ui.position.fit.top.apply(this, arguments);
			}
		}
	};
	w.ui.position, w.extend(w.expr.pseudos, { data: w.expr.createPseudo ? w.expr.createPseudo(function(e) {
		return function(t) {
			return !!w.data(t, e);
		};
	}) : function(t, e, i) {
		return !!w.data(t, i[3]);
	} }), w.ui.keyCode = {
		BACKSPACE: 8,
		COMMA: 188,
		DELETE: 46,
		DOWN: 40,
		END: 35,
		ENTER: 13,
		ESCAPE: 27,
		HOME: 36,
		LEFT: 37,
		PAGE_DOWN: 34,
		PAGE_UP: 33,
		PERIOD: 190,
		RIGHT: 39,
		SPACE: 32,
		TAB: 9,
		UP: 38
	}, w.fn.scrollParent = function(t) {
		var e = this.css("position"), i = "absolute" === e, s = t ? /(auto|scroll|hidden)/ : /(auto|scroll)/, t = this.parents().filter(function() {
			var t = w(this);
			return (!i || "static" !== t.css("position")) && s.test(t.css("overflow") + t.css("overflow-y") + t.css("overflow-x"));
		}).eq(0);
		return "fixed" !== e && t.length ? t : w(this[0].ownerDocument || document);
	}, w.fn.extend({
		uniqueId: (t = 0, function() {
			return this.each(function() {
				this.id || (this.id = "ui-id-" + ++t);
			});
		}),
		removeUniqueId: function() {
			return this.each(function() {
				/^ui-id-\d+$/.test(this.id) && w(this).removeAttr("id");
			});
		}
	}), w.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase());
	var t, p = !1;
	w(document).on("mouseup", function() {
		p = !1;
	});
	w.widget("ui.mouse", {
		version: "1.13.2",
		options: {
			cancel: "input, textarea, button, select, option",
			distance: 1,
			delay: 0
		},
		_mouseInit: function() {
			var e = this;
			this.element.on("mousedown." + this.widgetName, function(t) {
				return e._mouseDown(t);
			}).on("click." + this.widgetName, function(t) {
				if (!0 === w.data(t.target, e.widgetName + ".preventClickEvent")) return w.removeData(t.target, e.widgetName + ".preventClickEvent"), t.stopImmediatePropagation(), !1;
			}), this.started = !1;
		},
		_mouseDestroy: function() {
			this.element.off("." + this.widgetName), this._mouseMoveDelegate && this.document.off("mousemove." + this.widgetName, this._mouseMoveDelegate).off("mouseup." + this.widgetName, this._mouseUpDelegate);
		},
		_mouseDown: function(t) {
			if (!p) {
				this._mouseMoved = !1, this._mouseStarted && this._mouseUp(t), this._mouseDownEvent = t;
				var e = this, i = 1 === t.which, s = !("string" != typeof this.options.cancel || !t.target.nodeName) && w(t.target).closest(this.options.cancel).length;
				return i && !s && this._mouseCapture(t) ? (this.mouseDelayMet = !this.options.delay, this.mouseDelayMet || (this._mouseDelayTimer = setTimeout(function() {
					e.mouseDelayMet = !0;
				}, this.options.delay)), this._mouseDistanceMet(t) && this._mouseDelayMet(t) && (this._mouseStarted = !1 !== this._mouseStart(t), !this._mouseStarted) ? (t.preventDefault(), !0) : (!0 === w.data(t.target, this.widgetName + ".preventClickEvent") && w.removeData(t.target, this.widgetName + ".preventClickEvent"), this._mouseMoveDelegate = function(t) {
					return e._mouseMove(t);
				}, this._mouseUpDelegate = function(t) {
					return e._mouseUp(t);
				}, this.document.on("mousemove." + this.widgetName, this._mouseMoveDelegate).on("mouseup." + this.widgetName, this._mouseUpDelegate), t.preventDefault(), p = !0)) : !0;
			}
		},
		_mouseMove: function(t) {
			if (this._mouseMoved) {
				if (w.ui.ie && (!document.documentMode || document.documentMode < 9) && !t.button) return this._mouseUp(t);
				if (!t.which) {
					if (t.originalEvent.altKey || t.originalEvent.ctrlKey || t.originalEvent.metaKey || t.originalEvent.shiftKey) this.ignoreMissingWhich = !0;
					else if (!this.ignoreMissingWhich) return this._mouseUp(t);
				}
			}
			return (t.which || t.button) && (this._mouseMoved = !0), this._mouseStarted ? (this._mouseDrag(t), t.preventDefault()) : (this._mouseDistanceMet(t) && this._mouseDelayMet(t) && (this._mouseStarted = !1 !== this._mouseStart(this._mouseDownEvent, t), this._mouseStarted ? this._mouseDrag(t) : this._mouseUp(t)), !this._mouseStarted);
		},
		_mouseUp: function(t) {
			this.document.off("mousemove." + this.widgetName, this._mouseMoveDelegate).off("mouseup." + this.widgetName, this._mouseUpDelegate), this._mouseStarted && (this._mouseStarted = !1, t.target === this._mouseDownEvent.target && w.data(t.target, this.widgetName + ".preventClickEvent", !0), this._mouseStop(t)), this._mouseDelayTimer && (clearTimeout(this._mouseDelayTimer), delete this._mouseDelayTimer), this.ignoreMissingWhich = !1, p = !1, t.preventDefault();
		},
		_mouseDistanceMet: function(t) {
			return Math.max(Math.abs(this._mouseDownEvent.pageX - t.pageX), Math.abs(this._mouseDownEvent.pageY - t.pageY)) >= this.options.distance;
		},
		_mouseDelayMet: function() {
			return this.mouseDelayMet;
		},
		_mouseStart: function() {},
		_mouseDrag: function() {},
		_mouseStop: function() {},
		_mouseCapture: function() {
			return !0;
		}
	}), w.ui.plugin = {
		add: function(t, e, i) {
			var s, o = w.ui[t].prototype;
			for (s in i) o.plugins[s] = o.plugins[s] || [], o.plugins[s].push([e, i[s]]);
		},
		call: function(t, e, i, s) {
			var o, n = t.plugins[e];
			if (n && (s || t.element[0].parentNode && 11 !== t.element[0].parentNode.nodeType)) for (o = 0; o < n.length; o++) t.options[n[o][0]] && n[o][1].apply(t.element, i);
		}
	}, w.ui.safeActiveElement = function(e) {
		var i;
		try {
			i = e.activeElement;
		} catch (t) {
			i = e.body;
		}
		return i = !(i = i || e.body).nodeName ? e.body : i;
	}, w.ui.safeBlur = function(t) {
		t && "body" !== t.nodeName.toLowerCase() && w(t).trigger("blur");
	};
	w.widget("ui.draggable", w.ui.mouse, {
		version: "1.13.2",
		widgetEventPrefix: "drag",
		options: {
			addClasses: !0,
			appendTo: "parent",
			axis: !1,
			connectToSortable: !1,
			containment: !1,
			cursor: "auto",
			cursorAt: !1,
			grid: !1,
			handle: !1,
			helper: "original",
			iframeFix: !1,
			opacity: !1,
			refreshPositions: !1,
			revert: !1,
			revertDuration: 500,
			scope: "default",
			scroll: !0,
			scrollSensitivity: 20,
			scrollSpeed: 20,
			snap: !1,
			snapMode: "both",
			snapTolerance: 20,
			stack: !1,
			zIndex: !1,
			drag: null,
			start: null,
			stop: null
		},
		_create: function() {
			"original" === this.options.helper && this._setPositionRelative(), this.options.addClasses && this._addClass("ui-draggable"), this._setHandleClassName(), this._mouseInit();
		},
		_setOption: function(t, e) {
			this._super(t, e), "handle" === t && (this._removeHandleClassName(), this._setHandleClassName());
		},
		_destroy: function() {
			(this.helper || this.element).is(".ui-draggable-dragging") ? this.destroyOnClear = !0 : (this._removeHandleClassName(), this._mouseDestroy());
		},
		_mouseCapture: function(t) {
			var e = this.options;
			return !(this.helper || e.disabled || 0 < w(t.target).closest(".ui-resizable-handle").length) && (this.handle = this._getHandle(t), !!this.handle && (this._blurActiveElement(t), this._blockFrames(!0 === e.iframeFix ? "iframe" : e.iframeFix), !0));
		},
		_blockFrames: function(t) {
			this.iframeBlocks = this.document.find(t).map(function() {
				var t = w(this);
				return w("<div>").css("position", "absolute").appendTo(t.parent()).outerWidth(t.outerWidth()).outerHeight(t.outerHeight()).offset(t.offset())[0];
			});
		},
		_unblockFrames: function() {
			this.iframeBlocks && (this.iframeBlocks.remove(), delete this.iframeBlocks);
		},
		_blurActiveElement: function(t) {
			var e = w.ui.safeActiveElement(this.document[0]);
			w(t.target).closest(e).length || w.ui.safeBlur(e);
		},
		_mouseStart: function(t) {
			var e = this.options;
			return this.helper = this._createHelper(t), this._addClass(this.helper, "ui-draggable-dragging"), this._cacheHelperProportions(), w.ui.ddmanager && (w.ui.ddmanager.current = this), this._cacheMargins(), this.cssPosition = this.helper.css("position"), this.scrollParent = this.helper.scrollParent(!0), this.offsetParent = this.helper.offsetParent(), this.hasFixedAncestor = 0 < this.helper.parents().filter(function() {
				return "fixed" === w(this).css("position");
			}).length, this.positionAbs = this.element.offset(), this._refreshOffsets(t), this.originalPosition = this.position = this._generatePosition(t, !1), this.originalPageX = t.pageX, this.originalPageY = t.pageY, e.cursorAt && this._adjustOffsetFromHelper(e.cursorAt), this._setContainment(), !1 === this._trigger("start", t) ? (this._clear(), !1) : (this._cacheHelperProportions(), w.ui.ddmanager && !e.dropBehaviour && w.ui.ddmanager.prepareOffsets(this, t), this._mouseDrag(t, !0), w.ui.ddmanager && w.ui.ddmanager.dragStart(this, t), !0);
		},
		_refreshOffsets: function(t) {
			this.offset = {
				top: this.positionAbs.top - this.margins.top,
				left: this.positionAbs.left - this.margins.left,
				scroll: !1,
				parent: this._getParentOffset(),
				relative: this._getRelativeOffset()
			}, this.offset.click = {
				left: t.pageX - this.offset.left,
				top: t.pageY - this.offset.top
			};
		},
		_mouseDrag: function(t, e) {
			if (this.hasFixedAncestor && (this.offset.parent = this._getParentOffset()), this.position = this._generatePosition(t, !0), this.positionAbs = this._convertPositionTo("absolute"), !e) {
				e = this._uiHash();
				if (!1 === this._trigger("drag", t, e)) return this._mouseUp(new w.Event("mouseup", t)), !1;
				this.position = e.position;
			}
			return this.helper[0].style.left = this.position.left + "px", this.helper[0].style.top = this.position.top + "px", w.ui.ddmanager && w.ui.ddmanager.drag(this, t), !1;
		},
		_mouseStop: function(t) {
			var e = this, i = !1;
			return w.ui.ddmanager && !this.options.dropBehaviour && (i = w.ui.ddmanager.drop(this, t)), this.dropped && (i = this.dropped, this.dropped = !1), "invalid" === this.options.revert && !i || "valid" === this.options.revert && i || !0 === this.options.revert || "function" == typeof this.options.revert && this.options.revert.call(this.element, i) ? w(this.helper).animate(this.originalPosition, parseInt(this.options.revertDuration, 10), function() {
				!1 !== e._trigger("stop", t) && e._clear();
			}) : !1 !== this._trigger("stop", t) && this._clear(), !1;
		},
		_mouseUp: function(t) {
			return this._unblockFrames(), w.ui.ddmanager && w.ui.ddmanager.dragStop(this, t), this.handleElement.is(t.target) && this.element.trigger("focus"), w.ui.mouse.prototype._mouseUp.call(this, t);
		},
		cancel: function() {
			return this.helper.is(".ui-draggable-dragging") ? this._mouseUp(new w.Event("mouseup", { target: this.element[0] })) : this._clear(), this;
		},
		_getHandle: function(t) {
			return !this.options.handle || !!w(t.target).closest(this.element.find(this.options.handle)).length;
		},
		_setHandleClassName: function() {
			this.handleElement = this.options.handle ? this.element.find(this.options.handle) : this.element, this._addClass(this.handleElement, "ui-draggable-handle");
		},
		_removeHandleClassName: function() {
			this._removeClass(this.handleElement, "ui-draggable-handle");
		},
		_createHelper: function(t) {
			var e = this.options, i = "function" == typeof e.helper, t = i ? w(e.helper.apply(this.element[0], [t])) : "clone" === e.helper ? this.element.clone().removeAttr("id") : this.element;
			return t.parents("body").length || t.appendTo("parent" === e.appendTo ? this.element[0].parentNode : e.appendTo), i && t[0] === this.element[0] && this._setPositionRelative(), t[0] === this.element[0] || /(fixed|absolute)/.test(t.css("position")) || t.css("position", "absolute"), t;
		},
		_setPositionRelative: function() {
			/^(?:r|a|f)/.test(this.element.css("position")) || (this.element[0].style.position = "relative");
		},
		_adjustOffsetFromHelper: function(t) {
			"string" == typeof t && (t = t.split(" ")), "left" in (t = Array.isArray(t) ? {
				left: +t[0],
				top: +t[1] || 0
			} : t) && (this.offset.click.left = t.left + this.margins.left), "right" in t && (this.offset.click.left = this.helperProportions.width - t.right + this.margins.left), "top" in t && (this.offset.click.top = t.top + this.margins.top), "bottom" in t && (this.offset.click.top = this.helperProportions.height - t.bottom + this.margins.top);
		},
		_isRootNode: function(t) {
			return /(html|body)/i.test(t.tagName) || t === this.document[0];
		},
		_getParentOffset: function() {
			var t = this.offsetParent.offset(), e = this.document[0];
			return "absolute" === this.cssPosition && this.scrollParent[0] !== e && w.contains(this.scrollParent[0], this.offsetParent[0]) && (t.left += this.scrollParent.scrollLeft(), t.top += this.scrollParent.scrollTop()), {
				top: (t = this._isRootNode(this.offsetParent[0]) ? {
					top: 0,
					left: 0
				} : t).top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
				left: t.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
			};
		},
		_getRelativeOffset: function() {
			if ("relative" !== this.cssPosition) return {
				top: 0,
				left: 0
			};
			var t = this.element.position(), e = this._isRootNode(this.scrollParent[0]);
			return {
				top: t.top - (parseInt(this.helper.css("top"), 10) || 0) + (e ? 0 : this.scrollParent.scrollTop()),
				left: t.left - (parseInt(this.helper.css("left"), 10) || 0) + (e ? 0 : this.scrollParent.scrollLeft())
			};
		},
		_cacheMargins: function() {
			this.margins = {
				left: parseInt(this.element.css("marginLeft"), 10) || 0,
				top: parseInt(this.element.css("marginTop"), 10) || 0,
				right: parseInt(this.element.css("marginRight"), 10) || 0,
				bottom: parseInt(this.element.css("marginBottom"), 10) || 0
			};
		},
		_cacheHelperProportions: function() {
			this.helperProportions = {
				width: this.helper.outerWidth(),
				height: this.helper.outerHeight()
			};
		},
		_setContainment: function() {
			var t, e, i, s = this.options, o = this.document[0];
			this.relativeContainer = null, s.containment ? "window" !== s.containment ? "document" !== s.containment ? s.containment.constructor !== Array ? ("parent" === s.containment && (s.containment = this.helper[0].parentNode), (i = (e = w(s.containment))[0]) && (t = /(scroll|auto)/.test(e.css("overflow")), this.containment = [
				(parseInt(e.css("borderLeftWidth"), 10) || 0) + (parseInt(e.css("paddingLeft"), 10) || 0),
				(parseInt(e.css("borderTopWidth"), 10) || 0) + (parseInt(e.css("paddingTop"), 10) || 0),
				(t ? Math.max(i.scrollWidth, i.offsetWidth) : i.offsetWidth) - (parseInt(e.css("borderRightWidth"), 10) || 0) - (parseInt(e.css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left - this.margins.right,
				(t ? Math.max(i.scrollHeight, i.offsetHeight) : i.offsetHeight) - (parseInt(e.css("borderBottomWidth"), 10) || 0) - (parseInt(e.css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top - this.margins.bottom
			], this.relativeContainer = e)) : this.containment = s.containment : this.containment = [
				0,
				0,
				w(o).width() - this.helperProportions.width - this.margins.left,
				(w(o).height() || o.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top
			] : this.containment = [
				w(window).scrollLeft() - this.offset.relative.left - this.offset.parent.left,
				w(window).scrollTop() - this.offset.relative.top - this.offset.parent.top,
				w(window).scrollLeft() + w(window).width() - this.helperProportions.width - this.margins.left,
				w(window).scrollTop() + (w(window).height() || o.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top
			] : this.containment = null;
		},
		_convertPositionTo: function(t, e) {
			e = e || this.position;
			var i = "absolute" === t ? 1 : -1, t = this._isRootNode(this.scrollParent[0]);
			return {
				top: e.top + this.offset.relative.top * i + this.offset.parent.top * i - ("fixed" === this.cssPosition ? -this.offset.scroll.top : t ? 0 : this.offset.scroll.top) * i,
				left: e.left + this.offset.relative.left * i + this.offset.parent.left * i - ("fixed" === this.cssPosition ? -this.offset.scroll.left : t ? 0 : this.offset.scroll.left) * i
			};
		},
		_generatePosition: function(t, e) {
			var i, s = this.options, o = this._isRootNode(this.scrollParent[0]), n = t.pageX, r = t.pageY;
			return o && this.offset.scroll || (this.offset.scroll = {
				top: this.scrollParent.scrollTop(),
				left: this.scrollParent.scrollLeft()
			}), e && (this.containment && (i = this.relativeContainer ? (i = this.relativeContainer.offset(), [
				this.containment[0] + i.left,
				this.containment[1] + i.top,
				this.containment[2] + i.left,
				this.containment[3] + i.top
			]) : this.containment, t.pageX - this.offset.click.left < i[0] && (n = i[0] + this.offset.click.left), t.pageY - this.offset.click.top < i[1] && (r = i[1] + this.offset.click.top), t.pageX - this.offset.click.left > i[2] && (n = i[2] + this.offset.click.left), t.pageY - this.offset.click.top > i[3] && (r = i[3] + this.offset.click.top)), s.grid && (t = s.grid[1] ? this.originalPageY + Math.round((r - this.originalPageY) / s.grid[1]) * s.grid[1] : this.originalPageY, r = !i || t - this.offset.click.top >= i[1] || t - this.offset.click.top > i[3] ? t : t - this.offset.click.top >= i[1] ? t - s.grid[1] : t + s.grid[1], t = s.grid[0] ? this.originalPageX + Math.round((n - this.originalPageX) / s.grid[0]) * s.grid[0] : this.originalPageX, n = !i || t - this.offset.click.left >= i[0] || t - this.offset.click.left > i[2] ? t : t - this.offset.click.left >= i[0] ? t - s.grid[0] : t + s.grid[0]), "y" === s.axis && (n = this.originalPageX), "x" === s.axis && (r = this.originalPageY)), {
				top: r - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + ("fixed" === this.cssPosition ? -this.offset.scroll.top : o ? 0 : this.offset.scroll.top),
				left: n - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + ("fixed" === this.cssPosition ? -this.offset.scroll.left : o ? 0 : this.offset.scroll.left)
			};
		},
		_clear: function() {
			this._removeClass(this.helper, "ui-draggable-dragging"), this.helper[0] === this.element[0] || this.cancelHelperRemoval || this.helper.remove(), this.helper = null, this.cancelHelperRemoval = !1, this.destroyOnClear && this.destroy();
		},
		_trigger: function(t, e, i) {
			return i = i || this._uiHash(), w.ui.plugin.call(this, t, [
				e,
				i,
				this
			], !0), /^(drag|start|stop)/.test(t) && (this.positionAbs = this._convertPositionTo("absolute"), i.offset = this.positionAbs), w.Widget.prototype._trigger.call(this, t, e, i);
		},
		plugins: {},
		_uiHash: function() {
			return {
				helper: this.helper,
				position: this.position,
				originalPosition: this.originalPosition,
				offset: this.positionAbs
			};
		}
	}), w.ui.plugin.add("draggable", "connectToSortable", {
		start: function(e, t, i) {
			var s = w.extend({}, t, { item: i.element });
			i.sortables = [], w(i.options.connectToSortable).each(function() {
				var t = w(this).sortable("instance");
				t && !t.options.disabled && (i.sortables.push(t), t.refreshPositions(), t._trigger("activate", e, s));
			});
		},
		stop: function(e, t, i) {
			var s = w.extend({}, t, { item: i.element });
			i.cancelHelperRemoval = !1, w.each(i.sortables, function() {
				var t = this;
				t.isOver ? (t.isOver = 0, i.cancelHelperRemoval = !0, t.cancelHelperRemoval = !1, t._storedCSS = {
					position: t.placeholder.css("position"),
					top: t.placeholder.css("top"),
					left: t.placeholder.css("left")
				}, t._mouseStop(e), t.options.helper = t.options._helper) : (t.cancelHelperRemoval = !0, t._trigger("deactivate", e, s));
			});
		},
		drag: function(i, s, o) {
			w.each(o.sortables, function() {
				var t = !1, e = this;
				e.positionAbs = o.positionAbs, e.helperProportions = o.helperProportions, e.offset.click = o.offset.click, e._intersectsWith(e.containerCache) && (t = !0, w.each(o.sortables, function() {
					return this.positionAbs = o.positionAbs, this.helperProportions = o.helperProportions, this.offset.click = o.offset.click, t = this !== e && this._intersectsWith(this.containerCache) && w.contains(e.element[0], this.element[0]) ? !1 : t;
				})), t ? (e.isOver || (e.isOver = 1, o._parent = s.helper.parent(), e.currentItem = s.helper.appendTo(e.element).data("ui-sortable-item", !0), e.options._helper = e.options.helper, e.options.helper = function() {
					return s.helper[0];
				}, i.target = e.currentItem[0], e._mouseCapture(i, !0), e._mouseStart(i, !0, !0), e.offset.click.top = o.offset.click.top, e.offset.click.left = o.offset.click.left, e.offset.parent.left -= o.offset.parent.left - e.offset.parent.left, e.offset.parent.top -= o.offset.parent.top - e.offset.parent.top, o._trigger("toSortable", i), o.dropped = e.element, w.each(o.sortables, function() {
					this.refreshPositions();
				}), o.currentItem = o.element, e.fromOutside = o), e.currentItem && (e._mouseDrag(i), s.position = e.position)) : e.isOver && (e.isOver = 0, e.cancelHelperRemoval = !0, e.options._revert = e.options.revert, e.options.revert = !1, e._trigger("out", i, e._uiHash(e)), e._mouseStop(i, !0), e.options.revert = e.options._revert, e.options.helper = e.options._helper, e.placeholder && e.placeholder.remove(), s.helper.appendTo(o._parent), o._refreshOffsets(i), s.position = o._generatePosition(i, !0), o._trigger("fromSortable", i), o.dropped = !1, w.each(o.sortables, function() {
					this.refreshPositions();
				}));
			});
		}
	}), w.ui.plugin.add("draggable", "cursor", {
		start: function(t, e, i) {
			var s = w("body"), i = i.options;
			s.css("cursor") && (i._cursor = s.css("cursor")), s.css("cursor", i.cursor);
		},
		stop: function(t, e, i) {
			i = i.options;
			i._cursor && w("body").css("cursor", i._cursor);
		}
	}), w.ui.plugin.add("draggable", "opacity", {
		start: function(t, e, i) {
			e = w(e.helper), i = i.options;
			e.css("opacity") && (i._opacity = e.css("opacity")), e.css("opacity", i.opacity);
		},
		stop: function(t, e, i) {
			i = i.options;
			i._opacity && w(e.helper).css("opacity", i._opacity);
		}
	}), w.ui.plugin.add("draggable", "scroll", {
		start: function(t, e, i) {
			i.scrollParentNotHidden || (i.scrollParentNotHidden = i.helper.scrollParent(!1)), i.scrollParentNotHidden[0] !== i.document[0] && "HTML" !== i.scrollParentNotHidden[0].tagName && (i.overflowOffset = i.scrollParentNotHidden.offset());
		},
		drag: function(t, e, i) {
			var s = i.options, o = !1, n = i.scrollParentNotHidden[0], r = i.document[0];
			n !== r && "HTML" !== n.tagName ? (s.axis && "x" === s.axis || (i.overflowOffset.top + n.offsetHeight - t.pageY < s.scrollSensitivity ? n.scrollTop = o = n.scrollTop + s.scrollSpeed : t.pageY - i.overflowOffset.top < s.scrollSensitivity && (n.scrollTop = o = n.scrollTop - s.scrollSpeed)), s.axis && "y" === s.axis || (i.overflowOffset.left + n.offsetWidth - t.pageX < s.scrollSensitivity ? n.scrollLeft = o = n.scrollLeft + s.scrollSpeed : t.pageX - i.overflowOffset.left < s.scrollSensitivity && (n.scrollLeft = o = n.scrollLeft - s.scrollSpeed))) : (s.axis && "x" === s.axis || (t.pageY - w(r).scrollTop() < s.scrollSensitivity ? o = w(r).scrollTop(w(r).scrollTop() - s.scrollSpeed) : w(window).height() - (t.pageY - w(r).scrollTop()) < s.scrollSensitivity && (o = w(r).scrollTop(w(r).scrollTop() + s.scrollSpeed))), s.axis && "y" === s.axis || (t.pageX - w(r).scrollLeft() < s.scrollSensitivity ? o = w(r).scrollLeft(w(r).scrollLeft() - s.scrollSpeed) : w(window).width() - (t.pageX - w(r).scrollLeft()) < s.scrollSensitivity && (o = w(r).scrollLeft(w(r).scrollLeft() + s.scrollSpeed)))), !1 !== o && w.ui.ddmanager && !s.dropBehaviour && w.ui.ddmanager.prepareOffsets(i, t);
		}
	}), w.ui.plugin.add("draggable", "snap", {
		start: function(t, e, i) {
			var s = i.options;
			i.snapElements = [], w(s.snap.constructor !== String ? s.snap.items || ":data(ui-draggable)" : s.snap).each(function() {
				var t = w(this), e = t.offset();
				this !== i.element[0] && i.snapElements.push({
					item: this,
					width: t.outerWidth(),
					height: t.outerHeight(),
					top: e.top,
					left: e.left
				});
			});
		},
		drag: function(t, e, i) {
			for (var s, o, n, r, h, a, l, c, u, p = i.options, f = p.snapTolerance, d = e.offset.left, m = d + i.helperProportions.width, g = e.offset.top, v = g + i.helperProportions.height, _ = i.snapElements.length - 1; 0 <= _; _--) a = (h = i.snapElements[_].left - i.margins.left) + i.snapElements[_].width, c = (l = i.snapElements[_].top - i.margins.top) + i.snapElements[_].height, m < h - f || a + f < d || v < l - f || c + f < g || !w.contains(i.snapElements[_].item.ownerDocument, i.snapElements[_].item) ? (i.snapElements[_].snapping && i.options.snap.release && i.options.snap.release.call(i.element, t, w.extend(i._uiHash(), { snapItem: i.snapElements[_].item })), i.snapElements[_].snapping = !1) : ("inner" !== p.snapMode && (s = Math.abs(l - v) <= f, o = Math.abs(c - g) <= f, n = Math.abs(h - m) <= f, r = Math.abs(a - d) <= f, s && (e.position.top = i._convertPositionTo("relative", {
				top: l - i.helperProportions.height,
				left: 0
			}).top), o && (e.position.top = i._convertPositionTo("relative", {
				top: c,
				left: 0
			}).top), n && (e.position.left = i._convertPositionTo("relative", {
				top: 0,
				left: h - i.helperProportions.width
			}).left), r && (e.position.left = i._convertPositionTo("relative", {
				top: 0,
				left: a
			}).left)), u = s || o || n || r, "outer" !== p.snapMode && (s = Math.abs(l - g) <= f, o = Math.abs(c - v) <= f, n = Math.abs(h - d) <= f, r = Math.abs(a - m) <= f, s && (e.position.top = i._convertPositionTo("relative", {
				top: l,
				left: 0
			}).top), o && (e.position.top = i._convertPositionTo("relative", {
				top: c - i.helperProportions.height,
				left: 0
			}).top), n && (e.position.left = i._convertPositionTo("relative", {
				top: 0,
				left: h
			}).left), r && (e.position.left = i._convertPositionTo("relative", {
				top: 0,
				left: a - i.helperProportions.width
			}).left)), !i.snapElements[_].snapping && (s || o || n || r || u) && i.options.snap.snap && i.options.snap.snap.call(i.element, t, w.extend(i._uiHash(), { snapItem: i.snapElements[_].item })), i.snapElements[_].snapping = s || o || n || r || u);
		}
	}), w.ui.plugin.add("draggable", "stack", { start: function(t, e, i) {
		var s, i = i.options, i = w.makeArray(w(i.stack)).sort(function(t, e) {
			return (parseInt(w(t).css("zIndex"), 10) || 0) - (parseInt(w(e).css("zIndex"), 10) || 0);
		});
		i.length && (s = parseInt(w(i[0]).css("zIndex"), 10) || 0, w(i).each(function(t) {
			w(this).css("zIndex", s + t);
		}), this.css("zIndex", s + i.length));
	} }), w.ui.plugin.add("draggable", "zIndex", {
		start: function(t, e, i) {
			e = w(e.helper), i = i.options;
			e.css("zIndex") && (i._zIndex = e.css("zIndex")), e.css("zIndex", i.zIndex);
		},
		stop: function(t, e, i) {
			i = i.options;
			i._zIndex && w(e.helper).css("zIndex", i._zIndex);
		}
	});
	w.ui.draggable, w.widget("ui.sortable", w.ui.mouse, {
		version: "1.13.2",
		widgetEventPrefix: "sort",
		ready: !1,
		options: {
			appendTo: "parent",
			axis: !1,
			connectWith: !1,
			containment: !1,
			cursor: "auto",
			cursorAt: !1,
			dropOnEmpty: !0,
			forcePlaceholderSize: !1,
			forceHelperSize: !1,
			grid: !1,
			handle: !1,
			helper: "original",
			items: "> *",
			opacity: !1,
			placeholder: !1,
			revert: !1,
			scroll: !0,
			scrollSensitivity: 20,
			scrollSpeed: 20,
			scope: "default",
			tolerance: "intersect",
			zIndex: 1e3,
			activate: null,
			beforeStop: null,
			change: null,
			deactivate: null,
			out: null,
			over: null,
			receive: null,
			remove: null,
			sort: null,
			start: null,
			stop: null,
			update: null
		},
		_isOverAxis: function(t, e, i) {
			return e <= t && t < e + i;
		},
		_isFloating: function(t) {
			return /left|right/.test(t.css("float")) || /inline|table-cell/.test(t.css("display"));
		},
		_create: function() {
			this.containerCache = {}, this._addClass("ui-sortable"), this.refresh(), this.offset = this.element.offset(), this._mouseInit(), this._setHandleClassName(), this.ready = !0;
		},
		_setOption: function(t, e) {
			this._super(t, e), "handle" === t && this._setHandleClassName();
		},
		_setHandleClassName: function() {
			var t = this;
			this._removeClass(this.element.find(".ui-sortable-handle"), "ui-sortable-handle"), w.each(this.items, function() {
				t._addClass(this.instance.options.handle ? this.item.find(this.instance.options.handle) : this.item, "ui-sortable-handle");
			});
		},
		_destroy: function() {
			this._mouseDestroy();
			for (var t = this.items.length - 1; 0 <= t; t--) this.items[t].item.removeData(this.widgetName + "-item");
			return this;
		},
		_mouseCapture: function(t, e) {
			var i = null, s = !1, o = this;
			return !this.reverting && !this.options.disabled && "static" !== this.options.type && (this._refreshItems(t), w(t.target).parents().each(function() {
				if (w.data(this, o.widgetName + "-item") === o) return i = w(this), !1;
			}), !!(i = w.data(t.target, o.widgetName + "-item") === o ? w(t.target) : i) && !(this.options.handle && !e && (w(this.options.handle, i).find("*").addBack().each(function() {
				this === t.target && (s = !0);
			}), !s)) && (this.currentItem = i, this._removeCurrentsFromItems(), !0));
		},
		_mouseStart: function(t, e, i) {
			var s, o, n = this.options;
			if ((this.currentContainer = this).refreshPositions(), this.appendTo = w("parent" !== n.appendTo ? n.appendTo : this.currentItem.parent()), this.helper = this._createHelper(t), this._cacheHelperProportions(), this._cacheMargins(), this.offset = this.currentItem.offset(), this.offset = {
				top: this.offset.top - this.margins.top,
				left: this.offset.left - this.margins.left
			}, w.extend(this.offset, {
				click: {
					left: t.pageX - this.offset.left,
					top: t.pageY - this.offset.top
				},
				relative: this._getRelativeOffset()
			}), this.helper.css("position", "absolute"), this.cssPosition = this.helper.css("position"), n.cursorAt && this._adjustOffsetFromHelper(n.cursorAt), this.domPosition = {
				prev: this.currentItem.prev()[0],
				parent: this.currentItem.parent()[0]
			}, this.helper[0] !== this.currentItem[0] && this.currentItem.hide(), this._createPlaceholder(), this.scrollParent = this.placeholder.scrollParent(), w.extend(this.offset, { parent: this._getParentOffset() }), n.containment && this._setContainment(), n.cursor && "auto" !== n.cursor && (o = this.document.find("body"), this.storedCursor = o.css("cursor"), o.css("cursor", n.cursor), this.storedStylesheet = w("<style>*{ cursor: " + n.cursor + " !important; }</style>").appendTo(o)), n.zIndex && (this.helper.css("zIndex") && (this._storedZIndex = this.helper.css("zIndex")), this.helper.css("zIndex", n.zIndex)), n.opacity && (this.helper.css("opacity") && (this._storedOpacity = this.helper.css("opacity")), this.helper.css("opacity", n.opacity)), this.scrollParent[0] !== this.document[0] && "HTML" !== this.scrollParent[0].tagName && (this.overflowOffset = this.scrollParent.offset()), this._trigger("start", t, this._uiHash()), this._preserveHelperProportions || this._cacheHelperProportions(), !i) for (s = this.containers.length - 1; 0 <= s; s--) this.containers[s]._trigger("activate", t, this._uiHash(this));
			return w.ui.ddmanager && (w.ui.ddmanager.current = this), w.ui.ddmanager && !n.dropBehaviour && w.ui.ddmanager.prepareOffsets(this, t), this.dragging = !0, this._addClass(this.helper, "ui-sortable-helper"), this.helper.parent().is(this.appendTo) || (this.helper.detach().appendTo(this.appendTo), this.offset.parent = this._getParentOffset()), this.position = this.originalPosition = this._generatePosition(t), this.originalPageX = t.pageX, this.originalPageY = t.pageY, this.lastPositionAbs = this.positionAbs = this._convertPositionTo("absolute"), this._mouseDrag(t), !0;
		},
		_scroll: function(t) {
			var e = this.options, i = !1;
			return this.scrollParent[0] !== this.document[0] && "HTML" !== this.scrollParent[0].tagName ? (this.overflowOffset.top + this.scrollParent[0].offsetHeight - t.pageY < e.scrollSensitivity ? this.scrollParent[0].scrollTop = i = this.scrollParent[0].scrollTop + e.scrollSpeed : t.pageY - this.overflowOffset.top < e.scrollSensitivity && (this.scrollParent[0].scrollTop = i = this.scrollParent[0].scrollTop - e.scrollSpeed), this.overflowOffset.left + this.scrollParent[0].offsetWidth - t.pageX < e.scrollSensitivity ? this.scrollParent[0].scrollLeft = i = this.scrollParent[0].scrollLeft + e.scrollSpeed : t.pageX - this.overflowOffset.left < e.scrollSensitivity && (this.scrollParent[0].scrollLeft = i = this.scrollParent[0].scrollLeft - e.scrollSpeed)) : (t.pageY - this.document.scrollTop() < e.scrollSensitivity ? i = this.document.scrollTop(this.document.scrollTop() - e.scrollSpeed) : this.window.height() - (t.pageY - this.document.scrollTop()) < e.scrollSensitivity && (i = this.document.scrollTop(this.document.scrollTop() + e.scrollSpeed)), t.pageX - this.document.scrollLeft() < e.scrollSensitivity ? i = this.document.scrollLeft(this.document.scrollLeft() - e.scrollSpeed) : this.window.width() - (t.pageX - this.document.scrollLeft()) < e.scrollSensitivity && (i = this.document.scrollLeft(this.document.scrollLeft() + e.scrollSpeed))), i;
		},
		_mouseDrag: function(t) {
			var e, i, s, o, n = this.options;
			for (this.position = this._generatePosition(t), this.positionAbs = this._convertPositionTo("absolute"), this.options.axis && "y" === this.options.axis || (this.helper[0].style.left = this.position.left + "px"), this.options.axis && "x" === this.options.axis || (this.helper[0].style.top = this.position.top + "px"), n.scroll && !1 !== this._scroll(t) && (this._refreshItemPositions(!0), w.ui.ddmanager && !n.dropBehaviour && w.ui.ddmanager.prepareOffsets(this, t)), this.dragDirection = {
				vertical: this._getDragVerticalDirection(),
				horizontal: this._getDragHorizontalDirection()
			}, e = this.items.length - 1; 0 <= e; e--) if (s = (i = this.items[e]).item[0], (o = this._intersectsWithPointer(i)) && i.instance === this.currentContainer && !(s === this.currentItem[0] || this.placeholder[1 === o ? "next" : "prev"]()[0] === s || w.contains(this.placeholder[0], s) || "semi-dynamic" === this.options.type && w.contains(this.element[0], s))) {
				if (this.direction = 1 === o ? "down" : "up", "pointer" !== this.options.tolerance && !this._intersectsWithSides(i)) break;
				this._rearrange(t, i), this._trigger("change", t, this._uiHash());
				break;
			}
			return this._contactContainers(t), w.ui.ddmanager && w.ui.ddmanager.drag(this, t), this._trigger("sort", t, this._uiHash()), this.lastPositionAbs = this.positionAbs, !1;
		},
		_mouseStop: function(t, e) {
			var i, s, o, n;
			if (t) return w.ui.ddmanager && !this.options.dropBehaviour && w.ui.ddmanager.drop(this, t), this.options.revert ? (s = (i = this).placeholder.offset(), n = {}, (o = this.options.axis) && "x" !== o || (n.left = s.left - this.offset.parent.left - this.margins.left + (this.offsetParent[0] === this.document[0].body ? 0 : this.offsetParent[0].scrollLeft)), o && "y" !== o || (n.top = s.top - this.offset.parent.top - this.margins.top + (this.offsetParent[0] === this.document[0].body ? 0 : this.offsetParent[0].scrollTop)), this.reverting = !0, w(this.helper).animate(n, parseInt(this.options.revert, 10) || 500, function() {
				i._clear(t);
			})) : this._clear(t, e), !1;
		},
		cancel: function() {
			if (this.dragging) {
				this._mouseUp(new w.Event("mouseup", { target: null })), "original" === this.options.helper ? (this.currentItem.css(this._storedCSS), this._removeClass(this.currentItem, "ui-sortable-helper")) : this.currentItem.show();
				for (var t = this.containers.length - 1; 0 <= t; t--) this.containers[t]._trigger("deactivate", null, this._uiHash(this)), this.containers[t].containerCache.over && (this.containers[t]._trigger("out", null, this._uiHash(this)), this.containers[t].containerCache.over = 0);
			}
			return this.placeholder && (this.placeholder[0].parentNode && this.placeholder[0].parentNode.removeChild(this.placeholder[0]), "original" !== this.options.helper && this.helper && this.helper[0].parentNode && this.helper.remove(), w.extend(this, {
				helper: null,
				dragging: !1,
				reverting: !1,
				_noFinalSort: null
			}), this.domPosition.prev ? w(this.domPosition.prev).after(this.currentItem) : w(this.domPosition.parent).prepend(this.currentItem)), this;
		},
		serialize: function(e) {
			var t = this._getItemsAsjQuery(e && e.connected), i = [];
			return e = e || {}, w(t).each(function() {
				var t = (w(e.item || this).attr(e.attribute || "id") || "").match(e.expression || /(.+)[\-=_](.+)/);
				t && i.push((e.key || t[1] + "[]") + "=" + (e.key && e.expression ? t[1] : t[2]));
			}), !i.length && e.key && i.push(e.key + "="), i.join("&");
		},
		toArray: function(t) {
			var e = this._getItemsAsjQuery(t && t.connected), i = [];
			return t = t || {}, e.each(function() {
				i.push(w(t.item || this).attr(t.attribute || "id") || "");
			}), i;
		},
		_intersectsWith: function(t) {
			var e = this.positionAbs.left, i = e + this.helperProportions.width, s = this.positionAbs.top, o = s + this.helperProportions.height, n = t.left, r = n + t.width, h = t.top, a = h + t.height, l = this.offset.click.top, c = this.offset.click.left, l = "x" === this.options.axis || h < s + l && s + l < a, c = "y" === this.options.axis || n < e + c && e + c < r;
			return "pointer" === this.options.tolerance || this.options.forcePointerForContainers || "pointer" !== this.options.tolerance && this.helperProportions[this.floating ? "width" : "height"] > t[this.floating ? "width" : "height"] ? l && c : n < e + this.helperProportions.width / 2 && i - this.helperProportions.width / 2 < r && h < s + this.helperProportions.height / 2 && o - this.helperProportions.height / 2 < a;
		},
		_intersectsWithPointer: function(t) {
			var e = "x" === this.options.axis || this._isOverAxis(this.positionAbs.top + this.offset.click.top, t.top, t.height), t = "y" === this.options.axis || this._isOverAxis(this.positionAbs.left + this.offset.click.left, t.left, t.width);
			return !(!e || !t) && (e = this.dragDirection.vertical, t = this.dragDirection.horizontal, this.floating ? "right" === t || "down" === e ? 2 : 1 : e && ("down" === e ? 2 : 1));
		},
		_intersectsWithSides: function(t) {
			var e = this._isOverAxis(this.positionAbs.top + this.offset.click.top, t.top + t.height / 2, t.height), i = this._isOverAxis(this.positionAbs.left + this.offset.click.left, t.left + t.width / 2, t.width), s = this.dragDirection.vertical, t = this.dragDirection.horizontal;
			return this.floating && t ? "right" === t && i || "left" === t && !i : s && ("down" === s && e || "up" === s && !e);
		},
		_getDragVerticalDirection: function() {
			var t = this.positionAbs.top - this.lastPositionAbs.top;
			return 0 != t && (0 < t ? "down" : "up");
		},
		_getDragHorizontalDirection: function() {
			var t = this.positionAbs.left - this.lastPositionAbs.left;
			return 0 != t && (0 < t ? "right" : "left");
		},
		refresh: function(t) {
			return this._refreshItems(t), this._setHandleClassName(), this.refreshPositions(), this;
		},
		_connectWith: function() {
			var t = this.options;
			return t.connectWith.constructor === String ? [t.connectWith] : t.connectWith;
		},
		_getItemsAsjQuery: function(t) {
			var e, i, s, o, n = [], r = [], h = this._connectWith();
			if (h && t) for (e = h.length - 1; 0 <= e; e--) for (i = (s = w(h[e], this.document[0])).length - 1; 0 <= i; i--) (o = w.data(s[i], this.widgetFullName)) && o !== this && !o.options.disabled && r.push(["function" == typeof o.options.items ? o.options.items.call(o.element) : w(o.options.items, o.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), o]);
			function a() {
				n.push(this);
			}
			for (r.push(["function" == typeof this.options.items ? this.options.items.call(this.element, null, {
				options: this.options,
				item: this.currentItem
			}) : w(this.options.items, this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), this]), e = r.length - 1; 0 <= e; e--) r[e][0].each(a);
			return w(n);
		},
		_removeCurrentsFromItems: function() {
			var i = this.currentItem.find(":data(" + this.widgetName + "-item)");
			this.items = w.grep(this.items, function(t) {
				for (var e = 0; e < i.length; e++) if (i[e] === t.item[0]) return !1;
				return !0;
			});
		},
		_refreshItems: function(t) {
			this.items = [], this.containers = [this];
			var e, i, s, o, n, r, h, a, l = this.items, c = [["function" == typeof this.options.items ? this.options.items.call(this.element[0], t, { item: this.currentItem }) : w(this.options.items, this.element), this]], u = this._connectWith();
			if (u && this.ready) for (e = u.length - 1; 0 <= e; e--) for (i = (s = w(u[e], this.document[0])).length - 1; 0 <= i; i--) (o = w.data(s[i], this.widgetFullName)) && o !== this && !o.options.disabled && (c.push(["function" == typeof o.options.items ? o.options.items.call(o.element[0], t, { item: this.currentItem }) : w(o.options.items, o.element), o]), this.containers.push(o));
			for (e = c.length - 1; 0 <= e; e--) for (n = c[e][1], a = (r = c[e][i = 0]).length; i < a; i++) (h = w(r[i])).data(this.widgetName + "-item", n), l.push({
				item: h,
				instance: n,
				width: 0,
				height: 0,
				left: 0,
				top: 0
			});
		},
		_refreshItemPositions: function(t) {
			for (var e, i, s = this.items.length - 1; 0 <= s; s--) e = this.items[s], this.currentContainer && e.instance !== this.currentContainer && e.item[0] !== this.currentItem[0] || (i = this.options.toleranceElement ? w(this.options.toleranceElement, e.item) : e.item, t || (e.width = i.outerWidth(), e.height = i.outerHeight()), i = i.offset(), e.left = i.left, e.top = i.top);
		},
		refreshPositions: function(t) {
			var e, i;
			if (this.floating = !!this.items.length && ("x" === this.options.axis || this._isFloating(this.items[0].item)), this.offsetParent && this.helper && (this.offset.parent = this._getParentOffset()), this._refreshItemPositions(t), this.options.custom && this.options.custom.refreshContainers) this.options.custom.refreshContainers.call(this);
			else for (e = this.containers.length - 1; 0 <= e; e--) i = this.containers[e].element.offset(), this.containers[e].containerCache.left = i.left, this.containers[e].containerCache.top = i.top, this.containers[e].containerCache.width = this.containers[e].element.outerWidth(), this.containers[e].containerCache.height = this.containers[e].element.outerHeight();
			return this;
		},
		_createPlaceholder: function(i) {
			var s, o, n = (i = i || this).options;
			n.placeholder && n.placeholder.constructor !== String || (s = n.placeholder, o = i.currentItem[0].nodeName.toLowerCase(), n.placeholder = {
				element: function() {
					var t = w("<" + o + ">", i.document[0]);
					return i._addClass(t, "ui-sortable-placeholder", s || i.currentItem[0].className)._removeClass(t, "ui-sortable-helper"), "tbody" === o ? i._createTrPlaceholder(i.currentItem.find("tr").eq(0), w("<tr>", i.document[0]).appendTo(t)) : "tr" === o ? i._createTrPlaceholder(i.currentItem, t) : "img" === o && t.attr("src", i.currentItem.attr("src")), s || t.css("visibility", "hidden"), t;
				},
				update: function(t, e) {
					s && !n.forcePlaceholderSize || (e.height() && (!n.forcePlaceholderSize || "tbody" !== o && "tr" !== o) || e.height(i.currentItem.innerHeight() - parseInt(i.currentItem.css("paddingTop") || 0, 10) - parseInt(i.currentItem.css("paddingBottom") || 0, 10)), e.width() || e.width(i.currentItem.innerWidth() - parseInt(i.currentItem.css("paddingLeft") || 0, 10) - parseInt(i.currentItem.css("paddingRight") || 0, 10)));
				}
			}), i.placeholder = w(n.placeholder.element.call(i.element, i.currentItem)), i.currentItem.after(i.placeholder), n.placeholder.update(i, i.placeholder);
		},
		_createTrPlaceholder: function(t, e) {
			var i = this;
			t.children().each(function() {
				w("<td>&#160;</td>", i.document[0]).attr("colspan", w(this).attr("colspan") || 1).appendTo(e);
			});
		},
		_contactContainers: function(t) {
			for (var e, i, s, o, n, r, h, a, l, c = null, u = null, p = this.containers.length - 1; 0 <= p; p--) w.contains(this.currentItem[0], this.containers[p].element[0]) || (this._intersectsWith(this.containers[p].containerCache) ? c && w.contains(this.containers[p].element[0], c.element[0]) || (c = this.containers[p], u = p) : this.containers[p].containerCache.over && (this.containers[p]._trigger("out", t, this._uiHash(this)), this.containers[p].containerCache.over = 0));
			if (c) if (1 === this.containers.length) this.containers[u].containerCache.over || (this.containers[u]._trigger("over", t, this._uiHash(this)), this.containers[u].containerCache.over = 1);
			else {
				for (i = 1e4, s = null, o = (a = c.floating || this._isFloating(this.currentItem)) ? "left" : "top", n = a ? "width" : "height", l = a ? "pageX" : "pageY", e = this.items.length - 1; 0 <= e; e--) w.contains(this.containers[u].element[0], this.items[e].item[0]) && this.items[e].item[0] !== this.currentItem[0] && (r = this.items[e].item.offset()[o], h = !1, t[l] - r > this.items[e][n] / 2 && (h = !0), Math.abs(t[l] - r) < i && (i = Math.abs(t[l] - r), s = this.items[e], this.direction = h ? "up" : "down"));
				(s || this.options.dropOnEmpty) && (this.currentContainer !== this.containers[u] ? (s ? this._rearrange(t, s, null, !0) : this._rearrange(t, null, this.containers[u].element, !0), this._trigger("change", t, this._uiHash()), this.containers[u]._trigger("change", t, this._uiHash(this)), this.currentContainer = this.containers[u], this.options.placeholder.update(this.currentContainer, this.placeholder), this.scrollParent = this.placeholder.scrollParent(), this.scrollParent[0] !== this.document[0] && "HTML" !== this.scrollParent[0].tagName && (this.overflowOffset = this.scrollParent.offset()), this.containers[u]._trigger("over", t, this._uiHash(this)), this.containers[u].containerCache.over = 1) : this.currentContainer.containerCache.over || (this.containers[u]._trigger("over", t, this._uiHash()), this.currentContainer.containerCache.over = 1));
			}
		},
		_createHelper: function(t) {
			var e = this.options, t = "function" == typeof e.helper ? w(e.helper.apply(this.element[0], [t, this.currentItem])) : "clone" === e.helper ? this.currentItem.clone() : this.currentItem;
			return t.parents("body").length || this.appendTo[0].appendChild(t[0]), t[0] === this.currentItem[0] && (this._storedCSS = {
				width: this.currentItem[0].style.width,
				height: this.currentItem[0].style.height,
				position: this.currentItem.css("position"),
				top: this.currentItem.css("top"),
				left: this.currentItem.css("left")
			}), t[0].style.width && !e.forceHelperSize || t.width(this.currentItem.width()), t[0].style.height && !e.forceHelperSize || t.height(this.currentItem.height()), t;
		},
		_adjustOffsetFromHelper: function(t) {
			"string" == typeof t && (t = t.split(" ")), "left" in (t = Array.isArray(t) ? {
				left: +t[0],
				top: +t[1] || 0
			} : t) && (this.offset.click.left = t.left + this.margins.left), "right" in t && (this.offset.click.left = this.helperProportions.width - t.right + this.margins.left), "top" in t && (this.offset.click.top = t.top + this.margins.top), "bottom" in t && (this.offset.click.top = this.helperProportions.height - t.bottom + this.margins.top);
		},
		_getParentOffset: function() {
			this.offsetParent = this.helper.offsetParent();
			var t = this.offsetParent.offset();
			return "absolute" === this.cssPosition && this.scrollParent[0] !== this.document[0] && w.contains(this.scrollParent[0], this.offsetParent[0]) && (t.left += this.scrollParent.scrollLeft(), t.top += this.scrollParent.scrollTop()), {
				top: (t = this.offsetParent[0] === this.document[0].body || this.offsetParent[0].tagName && "html" === this.offsetParent[0].tagName.toLowerCase() && w.ui.ie ? {
					top: 0,
					left: 0
				} : t).top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
				left: t.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
			};
		},
		_getRelativeOffset: function() {
			if ("relative" !== this.cssPosition) return {
				top: 0,
				left: 0
			};
			var t = this.currentItem.position();
			return {
				top: t.top - (parseInt(this.helper.css("top"), 10) || 0) + this.scrollParent.scrollTop(),
				left: t.left - (parseInt(this.helper.css("left"), 10) || 0) + this.scrollParent.scrollLeft()
			};
		},
		_cacheMargins: function() {
			this.margins = {
				left: parseInt(this.currentItem.css("marginLeft"), 10) || 0,
				top: parseInt(this.currentItem.css("marginTop"), 10) || 0
			};
		},
		_cacheHelperProportions: function() {
			this.helperProportions = {
				width: this.helper.outerWidth(),
				height: this.helper.outerHeight()
			};
		},
		_setContainment: function() {
			var t, e, i = this.options;
			"parent" === i.containment && (i.containment = this.helper[0].parentNode), "document" !== i.containment && "window" !== i.containment || (this.containment = [
				0 - this.offset.relative.left - this.offset.parent.left,
				0 - this.offset.relative.top - this.offset.parent.top,
				"document" === i.containment ? this.document.width() : this.window.width() - this.helperProportions.width - this.margins.left,
				("document" === i.containment ? this.document.height() || document.body.parentNode.scrollHeight : this.window.height() || this.document[0].body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top
			]), /^(document|window|parent)$/.test(i.containment) || (t = w(i.containment)[0], e = w(i.containment).offset(), i = "hidden" !== w(t).css("overflow"), this.containment = [
				e.left + (parseInt(w(t).css("borderLeftWidth"), 10) || 0) + (parseInt(w(t).css("paddingLeft"), 10) || 0) - this.margins.left,
				e.top + (parseInt(w(t).css("borderTopWidth"), 10) || 0) + (parseInt(w(t).css("paddingTop"), 10) || 0) - this.margins.top,
				e.left + (i ? Math.max(t.scrollWidth, t.offsetWidth) : t.offsetWidth) - (parseInt(w(t).css("borderLeftWidth"), 10) || 0) - (parseInt(w(t).css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left,
				e.top + (i ? Math.max(t.scrollHeight, t.offsetHeight) : t.offsetHeight) - (parseInt(w(t).css("borderTopWidth"), 10) || 0) - (parseInt(w(t).css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top
			]);
		},
		_convertPositionTo: function(t, e) {
			e = e || this.position;
			var i = "absolute" === t ? 1 : -1, s = "absolute" !== this.cssPosition || this.scrollParent[0] !== this.document[0] && w.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent : this.offsetParent, t = /(html|body)/i.test(s[0].tagName);
			return {
				top: e.top + this.offset.relative.top * i + this.offset.parent.top * i - ("fixed" === this.cssPosition ? -this.scrollParent.scrollTop() : t ? 0 : s.scrollTop()) * i,
				left: e.left + this.offset.relative.left * i + this.offset.parent.left * i - ("fixed" === this.cssPosition ? -this.scrollParent.scrollLeft() : t ? 0 : s.scrollLeft()) * i
			};
		},
		_generatePosition: function(t) {
			var e = this.options, i = t.pageX, s = t.pageY, o = "absolute" !== this.cssPosition || this.scrollParent[0] !== this.document[0] && w.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent : this.offsetParent, n = /(html|body)/i.test(o[0].tagName);
			return "relative" !== this.cssPosition || this.scrollParent[0] !== this.document[0] && this.scrollParent[0] !== this.offsetParent[0] || (this.offset.relative = this._getRelativeOffset()), this.originalPosition && (this.containment && (t.pageX - this.offset.click.left < this.containment[0] && (i = this.containment[0] + this.offset.click.left), t.pageY - this.offset.click.top < this.containment[1] && (s = this.containment[1] + this.offset.click.top), t.pageX - this.offset.click.left > this.containment[2] && (i = this.containment[2] + this.offset.click.left), t.pageY - this.offset.click.top > this.containment[3] && (s = this.containment[3] + this.offset.click.top)), e.grid && (t = this.originalPageY + Math.round((s - this.originalPageY) / e.grid[1]) * e.grid[1], s = !this.containment || t - this.offset.click.top >= this.containment[1] && t - this.offset.click.top <= this.containment[3] ? t : t - this.offset.click.top >= this.containment[1] ? t - e.grid[1] : t + e.grid[1], t = this.originalPageX + Math.round((i - this.originalPageX) / e.grid[0]) * e.grid[0], i = !this.containment || t - this.offset.click.left >= this.containment[0] && t - this.offset.click.left <= this.containment[2] ? t : t - this.offset.click.left >= this.containment[0] ? t - e.grid[0] : t + e.grid[0])), {
				top: s - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + ("fixed" === this.cssPosition ? -this.scrollParent.scrollTop() : n ? 0 : o.scrollTop()),
				left: i - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + ("fixed" === this.cssPosition ? -this.scrollParent.scrollLeft() : n ? 0 : o.scrollLeft())
			};
		},
		_rearrange: function(t, e, i, s) {
			i ? i[0].appendChild(this.placeholder[0]) : e.item[0].parentNode.insertBefore(this.placeholder[0], "down" === this.direction ? e.item[0] : e.item[0].nextSibling), this.counter = this.counter ? ++this.counter : 1;
			var o = this.counter;
			this._delay(function() {
				o === this.counter && this.refreshPositions(!s);
			});
		},
		_clear: function(t, e) {
			this.reverting = !1;
			var i, s = [];
			if (!this._noFinalSort && this.currentItem.parent().length && this.placeholder.before(this.currentItem), this._noFinalSort = null, this.helper[0] === this.currentItem[0]) {
				for (i in this._storedCSS) "auto" !== this._storedCSS[i] && "static" !== this._storedCSS[i] || (this._storedCSS[i] = "");
				this.currentItem.css(this._storedCSS), this._removeClass(this.currentItem, "ui-sortable-helper");
			} else this.currentItem.show();
			function o(e, i, s) {
				return function(t) {
					s._trigger(e, t, i._uiHash(i));
				};
			}
			for (this.fromOutside && !e && s.push(function(t) {
				this._trigger("receive", t, this._uiHash(this.fromOutside));
			}), !this.fromOutside && this.domPosition.prev === this.currentItem.prev().not(".ui-sortable-helper")[0] && this.domPosition.parent === this.currentItem.parent()[0] || e || s.push(function(t) {
				this._trigger("update", t, this._uiHash());
			}), this !== this.currentContainer && (e || (s.push(function(t) {
				this._trigger("remove", t, this._uiHash());
			}), s.push(function(e) {
				return function(t) {
					e._trigger("receive", t, this._uiHash(this));
				};
			}.call(this, this.currentContainer)), s.push(function(e) {
				return function(t) {
					e._trigger("update", t, this._uiHash(this));
				};
			}.call(this, this.currentContainer)))), i = this.containers.length - 1; 0 <= i; i--) e || s.push(o("deactivate", this, this.containers[i])), this.containers[i].containerCache.over && (s.push(o("out", this, this.containers[i])), this.containers[i].containerCache.over = 0);
			if (this.storedCursor && (this.document.find("body").css("cursor", this.storedCursor), this.storedStylesheet.remove()), this._storedOpacity && this.helper.css("opacity", this._storedOpacity), this._storedZIndex && this.helper.css("zIndex", "auto" === this._storedZIndex ? "" : this._storedZIndex), this.dragging = !1, e || this._trigger("beforeStop", t, this._uiHash()), this.placeholder[0].parentNode.removeChild(this.placeholder[0]), this.cancelHelperRemoval || (this.helper[0] !== this.currentItem[0] && this.helper.remove(), this.helper = null), !e) {
				for (i = 0; i < s.length; i++) s[i].call(this, t);
				this._trigger("stop", t, this._uiHash());
			}
			return this.fromOutside = !1, !this.cancelHelperRemoval;
		},
		_trigger: function() {
			!1 === w.Widget.prototype._trigger.apply(this, arguments) && this.cancel();
		},
		_uiHash: function(t) {
			var e = t || this;
			return {
				helper: e.helper,
				placeholder: e.placeholder || w([]),
				position: e.position,
				originalPosition: e.originalPosition,
				offset: e.positionAbs,
				item: e.currentItem,
				sender: t ? t.element : null
			};
		}
	}), w.widget("ui.menu", {
		version: "1.13.2",
		defaultElement: "<ul>",
		delay: 300,
		options: {
			icons: { submenu: "ui-icon-caret-1-e" },
			items: "> *",
			menus: "ul",
			position: {
				my: "left top",
				at: "right top"
			},
			role: "menu",
			blur: null,
			focus: null,
			select: null
		},
		_create: function() {
			this.activeMenu = this.element, this.mouseHandled = !1, this.lastMousePosition = {
				x: null,
				y: null
			}, this.element.uniqueId().attr({
				role: this.options.role,
				tabIndex: 0
			}), this._addClass("ui-menu", "ui-widget ui-widget-content"), this._on({
				"mousedown .ui-menu-item": function(t) {
					t.preventDefault(), this._activateItem(t);
				},
				"click .ui-menu-item": function(t) {
					var e = w(t.target), i = w(w.ui.safeActiveElement(this.document[0]));
					!this.mouseHandled && e.not(".ui-state-disabled").length && (this.select(t), t.isPropagationStopped() || (this.mouseHandled = !0), e.has(".ui-menu").length ? this.expand(t) : !this.element.is(":focus") && i.closest(".ui-menu").length && (this.element.trigger("focus", [!0]), this.active && 1 === this.active.parents(".ui-menu").length && clearTimeout(this.timer)));
				},
				"mouseenter .ui-menu-item": "_activateItem",
				"mousemove .ui-menu-item": "_activateItem",
				mouseleave: "collapseAll",
				"mouseleave .ui-menu": "collapseAll",
				focus: function(t, e) {
					var i = this.active || this._menuItems().first();
					e || this.focus(t, i);
				},
				blur: function(t) {
					this._delay(function() {
						w.contains(this.element[0], w.ui.safeActiveElement(this.document[0])) || this.collapseAll(t);
					});
				},
				keydown: "_keydown"
			}), this.refresh(), this._on(this.document, { click: function(t) {
				this._closeOnDocumentClick(t) && this.collapseAll(t, !0), this.mouseHandled = !1;
			} });
		},
		_activateItem: function(t) {
			var e, i;
			this.previousFilter || t.clientX === this.lastMousePosition.x && t.clientY === this.lastMousePosition.y || (this.lastMousePosition = {
				x: t.clientX,
				y: t.clientY
			}, e = w(t.target).closest(".ui-menu-item"), i = w(t.currentTarget), e[0] === i[0] && (i.is(".ui-state-active") || (this._removeClass(i.siblings().children(".ui-state-active"), null, "ui-state-active"), this.focus(t, i))));
		},
		_destroy: function() {
			var t = this.element.find(".ui-menu-item").removeAttr("role aria-disabled").children(".ui-menu-item-wrapper").removeUniqueId().removeAttr("tabIndex role aria-haspopup");
			this.element.removeAttr("aria-activedescendant").find(".ui-menu").addBack().removeAttr("role aria-labelledby aria-expanded aria-hidden aria-disabled tabIndex").removeUniqueId().show(), t.children().each(function() {
				var t = w(this);
				t.data("ui-menu-submenu-caret") && t.remove();
			});
		},
		_keydown: function(t) {
			var e, i, s, o = !0;
			switch (t.keyCode) {
				case w.ui.keyCode.PAGE_UP:
					this.previousPage(t);
					break;
				case w.ui.keyCode.PAGE_DOWN:
					this.nextPage(t);
					break;
				case w.ui.keyCode.HOME:
					this._move("first", "first", t);
					break;
				case w.ui.keyCode.END:
					this._move("last", "last", t);
					break;
				case w.ui.keyCode.UP:
					this.previous(t);
					break;
				case w.ui.keyCode.DOWN:
					this.next(t);
					break;
				case w.ui.keyCode.LEFT:
					this.collapse(t);
					break;
				case w.ui.keyCode.RIGHT:
					this.active && !this.active.is(".ui-state-disabled") && this.expand(t);
					break;
				case w.ui.keyCode.ENTER:
				case w.ui.keyCode.SPACE:
					this._activate(t);
					break;
				case w.ui.keyCode.ESCAPE:
					this.collapse(t);
					break;
				default: e = this.previousFilter || "", s = o = !1, i = 96 <= t.keyCode && t.keyCode <= 105 ? (t.keyCode - 96).toString() : String.fromCharCode(t.keyCode), clearTimeout(this.filterTimer), i === e ? s = !0 : i = e + i, e = this._filterMenuItems(i), (e = s && -1 !== e.index(this.active.next()) ? this.active.nextAll(".ui-menu-item") : e).length || (i = String.fromCharCode(t.keyCode), e = this._filterMenuItems(i)), e.length ? (this.focus(t, e), this.previousFilter = i, this.filterTimer = this._delay(function() {
					delete this.previousFilter;
				}, 1e3)) : delete this.previousFilter;
			}
			o && t.preventDefault();
		},
		_activate: function(t) {
			this.active && !this.active.is(".ui-state-disabled") && (this.active.children("[aria-haspopup='true']").length ? this.expand(t) : this.select(t));
		},
		refresh: function() {
			var t, e, s = this, o = this.options.icons.submenu, i = this.element.find(this.options.menus);
			this._toggleClass("ui-menu-icons", null, !!this.element.find(".ui-icon").length), e = i.filter(":not(.ui-menu)").hide().attr({
				role: this.options.role,
				"aria-hidden": "true",
				"aria-expanded": "false"
			}).each(function() {
				var t = w(this), e = t.prev(), i = w("<span>").data("ui-menu-submenu-caret", !0);
				s._addClass(i, "ui-menu-icon", "ui-icon " + o), e.attr("aria-haspopup", "true").prepend(i), t.attr("aria-labelledby", e.attr("id"));
			}), this._addClass(e, "ui-menu", "ui-widget ui-widget-content ui-front"), (t = i.add(this.element).find(this.options.items)).not(".ui-menu-item").each(function() {
				var t = w(this);
				s._isDivider(t) && s._addClass(t, "ui-menu-divider", "ui-widget-content");
			}), i = (e = t.not(".ui-menu-item, .ui-menu-divider")).children().not(".ui-menu").uniqueId().attr({
				tabIndex: -1,
				role: this._itemRole()
			}), this._addClass(e, "ui-menu-item")._addClass(i, "ui-menu-item-wrapper"), t.filter(".ui-state-disabled").attr("aria-disabled", "true"), this.active && !w.contains(this.element[0], this.active[0]) && this.blur();
		},
		_itemRole: function() {
			return {
				menu: "menuitem",
				listbox: "option"
			}[this.options.role];
		},
		_setOption: function(t, e) {
			var i;
			"icons" === t && (i = this.element.find(".ui-menu-icon"), this._removeClass(i, null, this.options.icons.submenu)._addClass(i, null, e.submenu)), this._super(t, e);
		},
		_setOptionDisabled: function(t) {
			this._super(t), this.element.attr("aria-disabled", String(t)), this._toggleClass(null, "ui-state-disabled", !!t);
		},
		focus: function(t, e) {
			var i;
			this.blur(t, t && "focus" === t.type), this._scrollIntoView(e), this.active = e.first(), i = this.active.children(".ui-menu-item-wrapper"), this._addClass(i, null, "ui-state-active"), this.options.role && this.element.attr("aria-activedescendant", i.attr("id")), i = this.active.parent().closest(".ui-menu-item").children(".ui-menu-item-wrapper"), this._addClass(i, null, "ui-state-active"), t && "keydown" === t.type ? this._close() : this.timer = this._delay(function() {
				this._close();
			}, this.delay), (i = e.children(".ui-menu")).length && t && /^mouse/.test(t.type) && this._startOpening(i), this.activeMenu = e.parent(), this._trigger("focus", t, { item: e });
		},
		_scrollIntoView: function(t) {
			var e, i, s;
			this._hasScroll() && (i = parseFloat(w.css(this.activeMenu[0], "borderTopWidth")) || 0, s = parseFloat(w.css(this.activeMenu[0], "paddingTop")) || 0, e = t.offset().top - this.activeMenu.offset().top - i - s, i = this.activeMenu.scrollTop(), s = this.activeMenu.height(), t = t.outerHeight(), e < 0 ? this.activeMenu.scrollTop(i + e) : s < e + t && this.activeMenu.scrollTop(i + e - s + t));
		},
		blur: function(t, e) {
			e || clearTimeout(this.timer), this.active && (this._removeClass(this.active.children(".ui-menu-item-wrapper"), null, "ui-state-active"), this._trigger("blur", t, { item: this.active }), this.active = null);
		},
		_startOpening: function(t) {
			clearTimeout(this.timer), "true" === t.attr("aria-hidden") && (this.timer = this._delay(function() {
				this._close(), this._open(t);
			}, this.delay));
		},
		_open: function(t) {
			var e = w.extend({ of: this.active }, this.options.position);
			clearTimeout(this.timer), this.element.find(".ui-menu").not(t.parents(".ui-menu")).hide().attr("aria-hidden", "true"), t.show().removeAttr("aria-hidden").attr("aria-expanded", "true").position(e);
		},
		collapseAll: function(e, i) {
			clearTimeout(this.timer), this.timer = this._delay(function() {
				var t = i ? this.element : w(e && e.target).closest(this.element.find(".ui-menu"));
				t.length || (t = this.element), this._close(t), this.blur(e), this._removeClass(t.find(".ui-state-active"), null, "ui-state-active"), this.activeMenu = t;
			}, i ? 0 : this.delay);
		},
		_close: function(t) {
			(t = t || (this.active ? this.active.parent() : this.element)).find(".ui-menu").hide().attr("aria-hidden", "true").attr("aria-expanded", "false");
		},
		_closeOnDocumentClick: function(t) {
			return !w(t.target).closest(".ui-menu").length;
		},
		_isDivider: function(t) {
			return !/[^\-\u2014\u2013\s]/.test(t.text());
		},
		collapse: function(t) {
			var e = this.active && this.active.parent().closest(".ui-menu-item", this.element);
			e && e.length && (this._close(), this.focus(t, e));
		},
		expand: function(t) {
			var e = this.active && this._menuItems(this.active.children(".ui-menu")).first();
			e && e.length && (this._open(e.parent()), this._delay(function() {
				this.focus(t, e);
			}));
		},
		next: function(t) {
			this._move("next", "first", t);
		},
		previous: function(t) {
			this._move("prev", "last", t);
		},
		isFirstItem: function() {
			return this.active && !this.active.prevAll(".ui-menu-item").length;
		},
		isLastItem: function() {
			return this.active && !this.active.nextAll(".ui-menu-item").length;
		},
		_menuItems: function(t) {
			return (t || this.element).find(this.options.items).filter(".ui-menu-item");
		},
		_move: function(t, e, i) {
			var s;
			(s = this.active ? "first" === t || "last" === t ? this.active["first" === t ? "prevAll" : "nextAll"](".ui-menu-item").last() : this.active[t + "All"](".ui-menu-item").first() : s) && s.length && this.active || (s = this._menuItems(this.activeMenu)[e]()), this.focus(i, s);
		},
		nextPage: function(t) {
			var e, i, s;
			this.active ? this.isLastItem() || (this._hasScroll() ? (i = this.active.offset().top, s = this.element.innerHeight(), 0 === w.fn.jquery.indexOf("3.2.") && (s += this.element[0].offsetHeight - this.element.outerHeight()), this.active.nextAll(".ui-menu-item").each(function() {
				return (e = w(this)).offset().top - i - s < 0;
			}), this.focus(t, e)) : this.focus(t, this._menuItems(this.activeMenu)[this.active ? "last" : "first"]())) : this.next(t);
		},
		previousPage: function(t) {
			var e, i, s;
			this.active ? this.isFirstItem() || (this._hasScroll() ? (i = this.active.offset().top, s = this.element.innerHeight(), 0 === w.fn.jquery.indexOf("3.2.") && (s += this.element[0].offsetHeight - this.element.outerHeight()), this.active.prevAll(".ui-menu-item").each(function() {
				return 0 < (e = w(this)).offset().top - i + s;
			}), this.focus(t, e)) : this.focus(t, this._menuItems(this.activeMenu).first())) : this.next(t);
		},
		_hasScroll: function() {
			return this.element.outerHeight() < this.element.prop("scrollHeight");
		},
		select: function(t) {
			this.active = this.active || w(t.target).closest(".ui-menu-item");
			var e = { item: this.active };
			this.active.has(".ui-menu").length || this.collapseAll(t, !0), this._trigger("select", t, e);
		},
		_filterMenuItems: function(t) {
			var t = t.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"), e = new RegExp("^" + t, "i");
			return this.activeMenu.find(this.options.items).filter(".ui-menu-item").filter(function() {
				return e.test(String.prototype.trim.call(w(this).children(".ui-menu-item-wrapper").text()));
			});
		}
	});
	w.widget("ui.autocomplete", {
		version: "1.13.2",
		defaultElement: "<input>",
		options: {
			appendTo: null,
			autoFocus: !1,
			delay: 300,
			minLength: 1,
			position: {
				my: "left top",
				at: "left bottom",
				collision: "none"
			},
			source: null,
			change: null,
			close: null,
			focus: null,
			open: null,
			response: null,
			search: null,
			select: null
		},
		requestIndex: 0,
		pending: 0,
		liveRegionTimer: null,
		_create: function() {
			var i, s, o, t = this.element[0].nodeName.toLowerCase(), e = "textarea" === t, t = "input" === t;
			this.isMultiLine = e || !t && this._isContentEditable(this.element), this.valueMethod = this.element[e || t ? "val" : "text"], this.isNewMenu = !0, this._addClass("ui-autocomplete-input"), this.element.attr("autocomplete", "off"), this._on(this.element, {
				keydown: function(t) {
					if (this.element.prop("readOnly")) s = o = i = !0;
					else {
						s = o = i = !1;
						var e = w.ui.keyCode;
						switch (t.keyCode) {
							case e.PAGE_UP:
								i = !0, this._move("previousPage", t);
								break;
							case e.PAGE_DOWN:
								i = !0, this._move("nextPage", t);
								break;
							case e.UP:
								i = !0, this._keyEvent("previous", t);
								break;
							case e.DOWN:
								i = !0, this._keyEvent("next", t);
								break;
							case e.ENTER:
								this.menu.active && (i = !0, t.preventDefault(), this.menu.select(t));
								break;
							case e.TAB:
								this.menu.active && this.menu.select(t);
								break;
							case e.ESCAPE:
								this.menu.element.is(":visible") && (this.isMultiLine || this._value(this.term), this.close(t), t.preventDefault());
								break;
							default: s = !0, this._searchTimeout(t);
						}
					}
				},
				keypress: function(t) {
					if (i) return i = !1, void (this.isMultiLine && !this.menu.element.is(":visible") || t.preventDefault());
					if (!s) {
						var e = w.ui.keyCode;
						switch (t.keyCode) {
							case e.PAGE_UP:
								this._move("previousPage", t);
								break;
							case e.PAGE_DOWN:
								this._move("nextPage", t);
								break;
							case e.UP:
								this._keyEvent("previous", t);
								break;
							case e.DOWN: this._keyEvent("next", t);
						}
					}
				},
				input: function(t) {
					if (o) return o = !1, void t.preventDefault();
					this._searchTimeout(t);
				},
				focus: function() {
					this.selectedItem = null, this.previous = this._value();
				},
				blur: function(t) {
					clearTimeout(this.searching), this.close(t), this._change(t);
				}
			}), this._initSource(), this.menu = w("<ul>").appendTo(this._appendTo()).menu({ role: null }).hide().attr({ unselectable: "on" }).menu("instance"), this._addClass(this.menu.element, "ui-autocomplete", "ui-front"), this._on(this.menu.element, {
				mousedown: function(t) {
					t.preventDefault();
				},
				menufocus: function(t, e) {
					var i, s;
					if (this.isNewMenu && (this.isNewMenu = !1, t.originalEvent && /^mouse/.test(t.originalEvent.type))) return this.menu.blur(), void this.document.one("mousemove", function() {
						w(t.target).trigger(t.originalEvent);
					});
					s = e.item.data("ui-autocomplete-item"), !1 !== this._trigger("focus", t, { item: s }) && t.originalEvent && /^key/.test(t.originalEvent.type) && this._value(s.value), (i = e.item.attr("aria-label") || s.value) && String.prototype.trim.call(i).length && (clearTimeout(this.liveRegionTimer), this.liveRegionTimer = this._delay(function() {
						this.liveRegion.html(w("<div>").text(i));
					}, 100));
				},
				menuselect: function(t, e) {
					var i = e.item.data("ui-autocomplete-item"), s = this.previous;
					this.element[0] !== w.ui.safeActiveElement(this.document[0]) && (this.element.trigger("focus"), this.previous = s, this._delay(function() {
						this.previous = s, this.selectedItem = i;
					})), !1 !== this._trigger("select", t, { item: i }) && this._value(i.value), this.term = this._value(), this.close(t), this.selectedItem = i;
				}
			}), this.liveRegion = w("<div>", {
				role: "status",
				"aria-live": "assertive",
				"aria-relevant": "additions"
			}).appendTo(this.document[0].body), this._addClass(this.liveRegion, null, "ui-helper-hidden-accessible"), this._on(this.window, { beforeunload: function() {
				this.element.removeAttr("autocomplete");
			} });
		},
		_destroy: function() {
			clearTimeout(this.searching), this.element.removeAttr("autocomplete"), this.menu.element.remove(), this.liveRegion.remove();
		},
		_setOption: function(t, e) {
			this._super(t, e), "source" === t && this._initSource(), "appendTo" === t && this.menu.element.appendTo(this._appendTo()), "disabled" === t && e && this.xhr && this.xhr.abort();
		},
		_isEventTargetInWidget: function(t) {
			var e = this.menu.element[0];
			return t.target === this.element[0] || t.target === e || w.contains(e, t.target);
		},
		_closeOnClickOutside: function(t) {
			this._isEventTargetInWidget(t) || this.close();
		},
		_appendTo: function() {
			var t = this.options.appendTo;
			return t = !(t = !(t = t && (t.jquery || t.nodeType ? w(t) : this.document.find(t).eq(0))) || !t[0] ? this.element.closest(".ui-front, dialog") : t).length ? this.document[0].body : t;
		},
		_initSource: function() {
			var i, s, o = this;
			Array.isArray(this.options.source) ? (i = this.options.source, this.source = function(t, e) {
				e(w.ui.autocomplete.filter(i, t.term));
			}) : "string" == typeof this.options.source ? (s = this.options.source, this.source = function(t, e) {
				o.xhr && o.xhr.abort(), o.xhr = w.ajax({
					url: s,
					data: t,
					dataType: "json",
					success: function(t) {
						e(t);
					},
					error: function() {
						e([]);
					}
				});
			}) : this.source = this.options.source;
		},
		_searchTimeout: function(s) {
			clearTimeout(this.searching), this.searching = this._delay(function() {
				var t = this.term === this._value(), e = this.menu.element.is(":visible"), i = s.altKey || s.ctrlKey || s.metaKey || s.shiftKey;
				t && (e || i) || (this.selectedItem = null, this.search(null, s));
			}, this.options.delay);
		},
		search: function(t, e) {
			return t = null != t ? t : this._value(), this.term = this._value(), t.length < this.options.minLength ? this.close(e) : !1 !== this._trigger("search", e) ? this._search(t) : void 0;
		},
		_search: function(t) {
			this.pending++, this._addClass("ui-autocomplete-loading"), this.cancelSearch = !1, this.source({ term: t }, this._response());
		},
		_response: function() {
			var e = ++this.requestIndex;
			return function(t) {
				e === this.requestIndex && this.__response(t), this.pending--, this.pending || this._removeClass("ui-autocomplete-loading");
			}.bind(this);
		},
		__response: function(t) {
			t = t && this._normalize(t), this._trigger("response", null, { content: t }), !this.options.disabled && t && t.length && !this.cancelSearch ? (this._suggest(t), this._trigger("open")) : this._close();
		},
		close: function(t) {
			this.cancelSearch = !0, this._close(t);
		},
		_close: function(t) {
			this._off(this.document, "mousedown"), this.menu.element.is(":visible") && (this.menu.element.hide(), this.menu.blur(), this.isNewMenu = !0, this._trigger("close", t));
		},
		_change: function(t) {
			this.previous !== this._value() && this._trigger("change", t, { item: this.selectedItem });
		},
		_normalize: function(t) {
			return t.length && t[0].label && t[0].value ? t : w.map(t, function(t) {
				return "string" == typeof t ? {
					label: t,
					value: t
				} : w.extend({}, t, {
					label: t.label || t.value,
					value: t.value || t.label
				});
			});
		},
		_suggest: function(t) {
			var e = this.menu.element.empty();
			this._renderMenu(e, t), this.isNewMenu = !0, this.menu.refresh(), e.show(), this._resizeMenu(), e.position(w.extend({ of: this.element }, this.options.position)), this.options.autoFocus && this.menu.next(), this._on(this.document, { mousedown: "_closeOnClickOutside" });
		},
		_resizeMenu: function() {
			var t = this.menu.element;
			t.outerWidth(Math.max(t.width("").outerWidth() + 1, this.element.outerWidth()));
		},
		_renderMenu: function(i, t) {
			var s = this;
			w.each(t, function(t, e) {
				s._renderItemData(i, e);
			});
		},
		_renderItemData: function(t, e) {
			return this._renderItem(t, e).data("ui-autocomplete-item", e);
		},
		_renderItem: function(t, e) {
			return w("<li>").append(w("<div>").text(e.label)).appendTo(t);
		},
		_move: function(t, e) {
			if (this.menu.element.is(":visible")) return this.menu.isFirstItem() && /^previous/.test(t) || this.menu.isLastItem() && /^next/.test(t) ? (this.isMultiLine || this._value(this.term), void this.menu.blur()) : void this.menu[t](e);
			this.search(null, e);
		},
		widget: function() {
			return this.menu.element;
		},
		_value: function() {
			return this.valueMethod.apply(this.element, arguments);
		},
		_keyEvent: function(t, e) {
			this.isMultiLine && !this.menu.element.is(":visible") || (this._move(t, e), e.preventDefault());
		},
		_isContentEditable: function(t) {
			if (!t.length) return !1;
			var e = t.prop("contentEditable");
			return "inherit" === e ? this._isContentEditable(t.parent()) : "true" === e;
		}
	}), w.extend(w.ui.autocomplete, {
		escapeRegex: function(t) {
			return t.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
		},
		filter: function(t, e) {
			var i = new RegExp(w.ui.autocomplete.escapeRegex(e), "i");
			return w.grep(t, function(t) {
				return i.test(t.label || t.value || t);
			});
		}
	}), w.widget("ui.autocomplete", w.ui.autocomplete, {
		options: { messages: {
			noResults: "No search results.",
			results: function(t) {
				return t + (1 < t ? " results are" : " result is") + " available, use up and down arrow keys to navigate.";
			}
		} },
		__response: function(t) {
			var e;
			this._superApply(arguments), this.options.disabled || this.cancelSearch || (e = t && t.length ? this.options.messages.results(t.length) : this.options.messages.noResults, clearTimeout(this.liveRegionTimer), this.liveRegionTimer = this._delay(function() {
				this.liveRegion.html(w("<div>").text(e));
			}, 100));
		}
	});
	w.ui.autocomplete;
});
// \0@oxc-project+runtime@0.156.0/helpers/esm/typeof.js
function _typeof(o) {
	"@babel/helpers - typeof";
	return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(o) {
		return typeof o;
	} : function(o) {
		return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
	}, _typeof(o);
}
// \0@oxc-project+runtime@0.156.0/helpers/esm/toPrimitive.js
function toPrimitive(t, r) {
	if ("object" != _typeof(t) || !t) return t;
	var e = t[Symbol.toPrimitive];
	if (void 0 !== e) {
		var i = e.call(t, r || "default");
		if ("object" != _typeof(i)) return i;
		throw new TypeError("@@toPrimitive must return a primitive value.");
	}
	return ("string" === r ? String : Number)(t);
}
// \0@oxc-project+runtime@0.156.0/helpers/esm/toPropertyKey.js
function toPropertyKey(t) {
	var i = toPrimitive(t, "string");
	return "symbol" == _typeof(i) ? i : i + "";
}
// \0@oxc-project+runtime@0.156.0/helpers/esm/defineProperty.js
function _defineProperty(e, r, t) {
	return (r = toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
		value: t,
		enumerable: !0,
		configurable: !0,
		writable: !0
	}) : e[r] = t, e;
}
// \0@oxc-project+runtime@0.156.0/helpers/esm/objectSpread2.js
function ownKeys(e, r) {
	var t = Object.keys(e);
	if (Object.getOwnPropertySymbols) {
		var o = Object.getOwnPropertySymbols(e);
		r && (o = o.filter(function(r) {
			return Object.getOwnPropertyDescriptor(e, r).enumerable;
		})), t.push.apply(t, o);
	}
	return t;
}
function _objectSpread2(e) {
	for (var r = 1; r < arguments.length; r++) {
		var t = null != arguments[r] ? arguments[r] : {};
		r % 2 ? ownKeys(Object(t), !0).forEach(function(r) {
			_defineProperty(e, r, t[r]);
		}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function(r) {
			Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
		});
	}
	return e;
}
// resources/assets/js/app.js
$.when($.ready).then(() => {
	const base = (document.querySelector("base") || {}).href;
	const itemID = $("form[data-item-id]").data("item-id");
	const fakePassword = "*****";
	if (itemID) {
		const passwordField = $("input[name=\"config[password]\"]").first();
		if (passwordField.length > 0) passwordField.attr("value", fakePassword);
	}
	if ($(".message-container").length) setTimeout(() => {
		$(".message-container").fadeOut();
	}, 3500);
	function readURL(input) {
		if (input.files && input.files[0]) {
			const reader = new FileReader();
			reader.onload = function(e) {
				$("#appimage img").attr("src", e.target.result);
			};
			reader.readAsDataURL(input.files[0]);
		}
	}
	$("#upload").change(function() {
		readURL(this);
	});
	const sortableEl = document.getElementById("sortable");
	const sortables = [];
	if (sortableEl !== null) {
		const isFirefox = navigator.userAgent.toLowerCase().indexOf("firefox") > -1;
		const createSortable = (el, draggable, handle) => {
			const instance = Sortable.create(el, _objectSpread2(_objectSpread2({
				disabled: true,
				animation: 150,
				forceFallback: !isFirefox,
				draggable
			}, handle ? { handle } : {}), {}, { onEnd(evt) {
				$.post(`${base}order`, { order: Sortable.get(evt.to).toArray() });
			} }));
			if (isFirefox) instance.option("setData", (dataTransfer) => {
				dataTransfer.setData("Text", "");
			});
			sortables.push(instance);
		};
		const categoryEls = Array.from(sortableEl.querySelectorAll(".category"));
		if (categoryEls.length > 0) createSortable(sortableEl, ".category", ".category > .title");
		(categoryEls.length > 0 ? categoryEls : [sortableEl]).forEach((el) => {
			createSortable(el, ".item-container");
		});
		if (isFirefox) sortableEl.addEventListener("dragstart", (event) => {
			const { target } = event;
			if (target.nodeName.toLowerCase() === "a") {
				event.preventDefault();
				event.stopPropagation();
				event.dataTransfer.setData("Text", "");
			}
		});
	}
	const setSortableDisabled = (disabled) => {
		sortables.forEach((instance) => instance.option("disabled", disabled));
	};
	$("#main").on("mouseenter", "#sortable .item", function() {
		$(this).siblings(".tooltip").addClass("active");
		$(".refresh", this).addClass("active");
	}).on("mouseleave", ".item", function() {
		$(this).siblings(".tooltip").removeClass("active");
		$(".refresh", this).removeClass("active");
	});
	$("#config-buttons").on("mouseenter", "a", function() {
		$(".tooltip", this).addClass("active");
	}).on("mouseleave", "a", function() {
		$(".tooltip", this).removeClass("active");
	});
	$(".searchform > form").on("submit", (event) => {
		if ($("#search-container select[name=provider]").val() === "tiles") event.preventDefault();
	});
	let autocompleteTimeout = null;
	let currentAutocompleteRequest = null;
	function hideAutocomplete() {
		$("#search-autocomplete").remove();
	}
	function showAutocomplete(suggestions, inputElement) {
		hideAutocomplete();
		if (!suggestions || suggestions.length === 0) return;
		const $input = $(inputElement);
		const position = $input.position();
		const width = $input.outerWidth();
		const $autocomplete = $("<div id=\"search-autocomplete\"></div>");
		suggestions.forEach((suggestion) => {
			const $item = $("<div class=\"autocomplete-item\"></div>").text(suggestion).on("click", () => {
				$input.val(suggestion);
				hideAutocomplete();
				$input.closest("form").submit();
			});
			$autocomplete.append($item);
		});
		$autocomplete.css({
			position: "absolute",
			top: `${position.top + $input.outerHeight()}px`,
			left: `${position.left}px`,
			width: `${width}px`
		});
		$input.closest("#search-container").append($autocomplete);
	}
	function fetchAutocomplete(query, provider) {
		if (currentAutocompleteRequest) currentAutocompleteRequest.abort();
		if (!query || query.trim().length < 2) {
			hideAutocomplete();
			return;
		}
		currentAutocompleteRequest = $.ajax({
			url: `${base}search/autocomplete`,
			method: "GET",
			data: {
				q: query,
				provider
			},
			success(data) {
				const inputElement = $("#search-container input[name=q]")[0];
				showAutocomplete(data, inputElement);
			},
			error() {
				hideAutocomplete();
			},
			complete() {
				currentAutocompleteRequest = null;
			}
		});
	}
	$("#search-container").on("input", "input[name=q]", function() {
		const search = this.value;
		const items = $("#sortable").find(".item-container");
		const provider = $("#search-container select[name=provider]").val() || $("#search-container input[name=provider]").val();
		if (provider === "tiles") {
			hideAutocomplete();
			if (search.length > 0) {
				items.hide();
				items.filter(function() {
					return $(this).data("name").toLowerCase().includes(search.toLowerCase());
				}).show();
			} else items.show();
		} else {
			items.show();
			clearTimeout(autocompleteTimeout);
			autocompleteTimeout = setTimeout(() => {
				fetchAutocomplete(search, provider);
			}, 300);
		}
	}).on("change", "select[name=provider]", function() {
		const items = $("#sortable").find(".item-container");
		if ($(this).val() === "tiles") {
			$("#search-container button").hide();
			const search = $("#search-container input[name=q]").val();
			if (search.length > 0) {
				items.hide();
				items.filter(function() {
					return $(this).data("name").toLowerCase().includes(search.toLowerCase());
				}).show();
			} else items.show();
		} else {
			$("#search-container button").show();
			items.show();
			hideAutocomplete();
		}
	});
	$(document).on("click", (e) => {
		if (!$(e.target).closest("#search-container").length) hideAutocomplete();
	});
	$(document).on("keydown", (e) => {
		if (e.key === "Escape") hideAutocomplete();
	});
	$("#search-container select[name=provider]").trigger("change");
	$("#app").on("click", "#config-button", (e) => {
		e.preventDefault();
		const app = $("#app");
		const active = app.hasClass("header");
		app.toggleClass("header");
		if (active) {
			$(".add-item").hide();
			$(".item-edit").hide();
			$("#app").removeClass("sidebar");
			$("#sortable .tooltip").css("display", "");
			setSortableDisabled(true);
		} else {
			$("#sortable .tooltip").css("display", "none");
			setSortableDisabled(false);
			setTimeout(() => {
				$(".add-item").fadeIn();
				$(".item-edit").fadeIn();
			}, 350);
		}
	}).on("click", ".tag", (e) => {
		e.preventDefault();
		const tag = $(e.target).data("tag");
		$("#taglist .tag").removeClass("current");
		$(e.target).addClass("current");
		$("#sortable .item-container").show();
		if (tag !== "all") $(`#sortable .item-container:not(.${tag})`).hide();
	}).on("click", "#add-item, #pin-item", (e) => {
		e.preventDefault();
		$("#app").toggleClass("sidebar");
	}).on("click", ".close-sidenav", (e) => {
		e.preventDefault();
		$("#app").removeClass("sidebar");
	}).on("click", "#test_config", (e) => {
		e.preventDefault();
		let apiurl = $("#create input[name=url]").val();
		const overrideUrl = $("#sapconfig input[name=\"config[override_url]\"]").val();
		if (typeof overrideUrl === "string" && overrideUrl !== "") apiurl = overrideUrl;
		const data = {};
		data.url = apiurl;
		$(".config-item").each(function() {
			const config = $(this).data("config");
			if ($(this).is(":checkbox")) data[config] = $(this).is(":checked") ? "1" : "0";
			else data[config] = $(this).val();
		});
		data.id = $("form[data-item-id]").data("item-id");
		if (data.password && data.password === fakePassword) data.password = "";
		$.post(`${base}test_config`, { data }).done((responseData) => {
			alert(responseData);
		}).fail((responseData) => {
			alert(`Something went wrong: ${responseData.responseText.substring(0, 100)}`);
		});
	});
	const taglist = document.getElementById("taglist");
	if (taglist !== null) {
		const defaultTag = taglist.getAttribute("data-default-tag");
		if (typeof defaultTag === "string" && defaultTag !== "") $(`#taglist .tag[data-tag="tag-${defaultTag}"]`).trigger("click");
	}
	$("#pinlist").on("click", "a", function(e) {
		e.preventDefault();
		const current = $(this);
		const id = current.data("id");
		const tag = current.data("tag");
		$.get(`${base}items/pintoggle/${id}/true/${tag}`, (data) => {
			const inner = $(data).filter("#sortable").html();
			$("#sortable").html(inner);
			current.toggleClass("active");
		});
	});
	$("#itemform").on("submit", () => {
		const passwordField = $("input[name=\"config[password]\"]").first();
		if (passwordField.length > 0) {
			if (passwordField.attr("value") === fakePassword) passwordField.attr("value", "");
		}
	});
});
// resources/assets/js/keyBindings.js
var focusSearch = (event) => {
	const searchInput = document.querySelector("input[name=\"q\"]");
	if (searchInput) {
		event.preventDefault();
		searchInput.focus();
	}
};
var openFirstNonHiddenItem = (event) => {
	if (event.target !== document.querySelector("input[name=\"q\"]")) return;
	if (document.querySelector("#search-container select[name=provider]").value !== "tiles") return;
	const item = document.querySelector("#sortable section.item-container:not([style=\"display: none;\"]) a");
	if ("href" in item) {
		event.preventDefault();
		window.open(item.href);
	}
};
var KEY_BINDINGS = {
	"/": focusSearch,
	Enter: openFirstNonHiddenItem
};
document.addEventListener("keydown", (event) => {
	try {
		if (event.key in KEY_BINDINGS) KEY_BINDINGS[event.key](event);
	} catch (e) {}
});
// resources/assets/js/itemExport.js
var EXPORT_FILE_NAME = "HeimdallExport.json";
var EXPORT_API_URL = "api/item";
/**
*
* @param {string} fileName
* @param {string} data
*/
function triggerFileDownload(fileName, data) {
	const a = document.createElement("a");
	const file = new Blob([data], { type: "text/plain" });
	a.href = URL.createObjectURL(file);
	a.download = EXPORT_FILE_NAME;
	a.click();
}
/**
*
* @param {Event} event
*/
var exportItems = (event) => {
	event.preventDefault();
	fetch(EXPORT_API_URL).then((response) => {
		if (response.status !== 200) window.alert("An error occurred while exporting...");
		return response.json();
	}).then((data) => {
		triggerFileDownload(EXPORT_FILE_NAME, JSON.stringify(data, null, 2));
	});
};
var exportButton = document.querySelector("#item-export");
if (exportButton) exportButton.addEventListener("click", exportItems);
// resources/assets/js/itemImport.js
var IMPORT_API_URL = "api/item";
var APP_LOAD_URL = "appload";
/**
*
* @param {object} item
* @param {array} errors
*/
var updateStatus = ({ item, errors }) => {
	console.log(item, errors);
	let statusLine;
	if (errors.length === 0) statusLine = `<li class="success"><i class="fas fa-circle-check"></i> Imported: ${item.title} </li>`;
	else statusLine = `<li class="fail"><i class="fas fa-circle-xmark"></i> Failed: ${item.title} - ${errors[0]} </li>`;
	document.querySelector(".import-status").innerHTML += statusLine;
};
/**
*
*/
function clearStatus() {
	const statusContainer = document.querySelector(".import-status");
	statusContainer.innerHTML = "";
}
/**
*
* @param {object} data
* @param {string} csrfToken
*/
var postToApi = (data, csrfToken) => fetch(IMPORT_API_URL, {
	method: "POST",
	cache: "no-cache",
	redirect: "follow",
	headers: {
		"Content-Type": "application/json",
		"X-CSRF-TOKEN": csrfToken
	},
	body: JSON.stringify(data)
});
/**
*
* @returns {string}
*/
var getCSRFToken = () => {
	return document.querySelector("input[name=\"_token\"]").value;
};
/**
*
* @param {object} item
* @param {object} appDetails
* @returns {object}
*/
var mergeItemWithAppDetails = (item, appDetails) => ({
	pinned: item.pinned !== void 0 ? item.pinned : 1,
	order: item.pinned_order !== void 0 ? item.pinned_order : 0,
	tags: Array.isArray(item.tags) && item.tags.length ? item.tags : [0],
	appid: item.appid,
	title: item.title,
	colour: item.colour,
	url: item.url,
	appdescription: item.appdescription ? item.appdescription : appDetails.description,
	website: appDetails.website,
	icon: appDetails.iconview,
	config: item.description ? JSON.parse(item.description) : null
});
/**
*
* @param {string|null} appId
* @returns {Promise<{}>|Promise<any>}
*/
var fetchAppDetails = (appId) => {
	if (appId === null || appId === "null") return Promise.resolve({});
	return fetch(APP_LOAD_URL, {
		method: "POST",
		headers: { "Content-Type": "application/json" },
		body: JSON.stringify({ app: appId })
	}).then((response) => {
		if (!response.ok) return Promise.reject(/* @__PURE__ */ new Error(`Failed to find app id: ${appId}`));
		return response.json();
	});
};
/**
*
* @param {array} items
*/
var importItems = (items) => {
	items.forEach((item) => {
		const errors = [];
		fetchAppDetails(item.appid).catch(() => errors.push(/* @__PURE__ */ new Error(`Failed to find app id: ${item.appid}`))).then((appDetails) => {
			return postToApi(mergeItemWithAppDetails(item, appDetails), getCSRFToken());
		}).catch(() => errors.push(/* @__PURE__ */ new Error(`Failed to create item: ${item.title}`))).finally(() => {
			updateStatus({
				item,
				errors
			});
		});
	});
};
/**
*
* @param {Blob} file
* @returns {Promise<unknown>}
*/
var readJSON = (file) => new Promise((resolve, reject) => {
	try {
		const reader = new FileReader();
		reader.onload = (event) => {
			const contents = event.target.result;
			resolve(JSON.parse(contents));
		};
		reader.readAsText(file);
	} catch (e) {
		reject(/* @__PURE__ */ new Error("Unable to read file"));
	}
});
/**
*
* @param {Blob} file
*/
var openFileForImport = (file) => {
	clearStatus();
	return readJSON(file).catch((error) => {
		console.error(error);
	}).then(importItems);
};
var fileInput = document.querySelector("input[name='import']");
var importButtons = document.querySelectorAll(".import-button");
if (fileInput && importButtons) {
	importButtons.forEach((importButton) => {
		importButton.addEventListener("click", () => {
			const file = fileInput.files[0];
			if (!file) return;
			openFileForImport(file);
		});
	});
	fileInput.addEventListener("change", openFileForImport, false);
}
// resources/assets/js/liveStatRefresh.js
var REFRESH_INTERVAL_SMALL = 5e3;
var REFRESH_INTERVAL_BIG = 3e4;
var QUEUE_PROCESSING_INTERVAL = 1e3;
var CONTAINER_SELECTOR = ".livestats-container";
/**
* @returns {*[]}
*/
function createQueue() {
	const queue = [];
	let suspended = false;
	function processQueue() {
		if (queue.length === 0 || suspended === true) return;
		queue.shift()();
	}
	document.addEventListener("visibilitychange", () => {
		suspended = document.hidden;
	});
	setInterval(processQueue, QUEUE_PROCESSING_INTERVAL);
	return queue;
}
/**
* @returns {NodeListOf<Element>}
*/
function getContainers() {
	return document.querySelectorAll(CONTAINER_SELECTOR);
}
/**
*
* @param {boolean} dataOnly
* @param {boolean} active
* @returns {number}
*/
function getQueueInterval(dataOnly, active) {
	if (dataOnly) return REFRESH_INTERVAL_BIG;
	if (active) return REFRESH_INTERVAL_SMALL;
	return REFRESH_INTERVAL_BIG;
}
/**
* @param {HTMLElement} container
* @param {array} queue
* @returns {function(): Promise<Response>}
*/
function createUpdateJob(container, queue) {
	const id = container.getAttribute("data-id");
	const isDataOnly = container.getAttribute("data-dataonly") === "1";
	return () => fetch(`get_stats/${id}`).then((response) => {
		if (response.ok) return response.json();
		throw new Error(`Network response was not ok: ${response.status}`);
	}).then((data) => {
		container.innerHTML = data.html;
		const isActive = data.status === "active";
		if (queue) setTimeout(() => {
			queue.push(createUpdateJob(container, queue));
		}, getQueueInterval(isDataOnly, isActive));
	}).catch((error) => {
		console.error(error);
	});
}
var livestatContainers = getContainers();
if (livestatContainers.length > 0) {
	const myQueue = createQueue();
	livestatContainers.forEach((container) => {
		createUpdateJob(container, myQueue)();
	});
}

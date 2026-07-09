function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/*! Sortable 1.15.2 - MIT | git://github.com/SortableJS/Sortable.git */
!function (t, e) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "undefined" != typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define(e) : (t = t || self).Sortable = e();
}(this, function () {
  "use strict";

  function e(e, t) {
    var n,
      o = Object.keys(e);
    return Object.getOwnPropertySymbols && (n = Object.getOwnPropertySymbols(e), t && (n = n.filter(function (t) {
      return Object.getOwnPropertyDescriptor(e, t).enumerable;
    })), o.push.apply(o, n)), o;
  }
  function I(o) {
    for (var t = 1; t < arguments.length; t++) {
      var i = null != arguments[t] ? arguments[t] : {};
      t % 2 ? e(Object(i), !0).forEach(function (t) {
        var e, n;
        e = o, t = i[n = t], n in e ? Object.defineProperty(e, n, {
          value: t,
          enumerable: !0,
          configurable: !0,
          writable: !0
        }) : e[n] = t;
      }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(o, Object.getOwnPropertyDescriptors(i)) : e(Object(i)).forEach(function (t) {
        Object.defineProperty(o, t, Object.getOwnPropertyDescriptor(i, t));
      });
    }
    return o;
  }
  function o(t) {
    return (o = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (t) {
      return _typeof(t);
    } : function (t) {
      return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : _typeof(t);
    })(t);
  }
  function a() {
    return (a = Object.assign || function (t) {
      for (var e = 1; e < arguments.length; e++) {
        var n,
          o = arguments[e];
        for (n in o) {
          Object.prototype.hasOwnProperty.call(o, n) && (t[n] = o[n]);
        }
      }
      return t;
    }).apply(this, arguments);
  }
  function i(t, e) {
    if (null == t) return {};
    var n,
      o = function (t, e) {
        if (null == t) return {};
        for (var n, o = {}, i = Object.keys(t), r = 0; r < i.length; r++) {
          n = i[r], 0 <= e.indexOf(n) || (o[n] = t[n]);
        }
        return o;
      }(t, e);
    if (Object.getOwnPropertySymbols) for (var i = Object.getOwnPropertySymbols(t), r = 0; r < i.length; r++) {
      n = i[r], 0 <= e.indexOf(n) || Object.prototype.propertyIsEnumerable.call(t, n) && (o[n] = t[n]);
    }
    return o;
  }
  function r(t) {
    return function (t) {
      if (Array.isArray(t)) return l(t);
    }(t) || function (t) {
      if ("undefined" != typeof Symbol && null != t[Symbol.iterator] || null != t["@@iterator"]) return Array.from(t);
    }(t) || function (t, e) {
      if (t) {
        if ("string" == typeof t) return l(t, e);
        var n = Object.prototype.toString.call(t).slice(8, -1);
        return "Map" === (n = "Object" === n && t.constructor ? t.constructor.name : n) || "Set" === n ? Array.from(t) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? l(t, e) : void 0;
      }
    }(t) || function () {
      throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }();
  }
  function l(t, e) {
    (null == e || e > t.length) && (e = t.length);
    for (var n = 0, o = new Array(e); n < e; n++) {
      o[n] = t[n];
    }
    return o;
  }
  function t(t) {
    if ("undefined" != typeof window && window.navigator) return !!navigator.userAgent.match(t);
  }
  var y = t(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i),
    w = t(/Edge/i),
    s = t(/firefox/i),
    u = t(/safari/i) && !t(/chrome/i) && !t(/android/i),
    n = t(/iP(ad|od|hone)/i),
    c = t(/chrome/i) && t(/android/i),
    d = {
      capture: !1,
      passive: !1
    };
  function h(t, e, n) {
    t.addEventListener(e, n, !y && d);
  }
  function f(t, e, n) {
    t.removeEventListener(e, n, !y && d);
  }
  function p(t, e) {
    if (e && (">" === e[0] && (e = e.substring(1)), t)) try {
      if (t.matches) return t.matches(e);
      if (t.msMatchesSelector) return t.msMatchesSelector(e);
      if (t.webkitMatchesSelector) return t.webkitMatchesSelector(e);
    } catch (t) {
      return;
    }
  }
  function P(t, e, n, o) {
    if (t) {
      n = n || document;
      do {
        if (null != e && (">" !== e[0] || t.parentNode === n) && p(t, e) || o && t === n) return t;
      } while (t !== n && (t = (i = t).host && i !== document && i.host.nodeType ? i.host : i.parentNode));
    }
    var i;
    return null;
  }
  var g,
    m = /\s+/g;
  function k(t, e, n) {
    var o;
    t && e && (t.classList ? t.classList[n ? "add" : "remove"](e) : (o = (" " + t.className + " ").replace(m, " ").replace(" " + e + " ", " "), t.className = (o + (n ? " " + e : "")).replace(m, " ")));
  }
  function R(t, e, n) {
    var o = t && t.style;
    if (o) {
      if (void 0 === n) return document.defaultView && document.defaultView.getComputedStyle ? n = document.defaultView.getComputedStyle(t, "") : t.currentStyle && (n = t.currentStyle), void 0 === e ? n : n[e];
      o[e = !(e in o || -1 !== e.indexOf("webkit")) ? "-webkit-" + e : e] = n + ("string" == typeof n ? "" : "px");
    }
  }
  function v(t, e) {
    var n = "";
    if ("string" == typeof t) n = t;else do {
      var o = R(t, "transform");
    } while ((o && "none" !== o && (n = o + " " + n), !e && (t = t.parentNode)));
    var i = window.DOMMatrix || window.WebKitCSSMatrix || window.CSSMatrix || window.MSCSSMatrix;
    return i && new i(n);
  }
  function b(t, e, n) {
    if (t) {
      var o = t.getElementsByTagName(e),
        i = 0,
        r = o.length;
      if (n) for (; i < r; i++) {
        n(o[i], i);
      }
      return o;
    }
    return [];
  }
  function O() {
    var t = document.scrollingElement;
    return t || document.documentElement;
  }
  function X(t, e, n, o, i) {
    if (t.getBoundingClientRect || t === window) {
      var r,
        a,
        l,
        s,
        c,
        u,
        d = t !== window && t.parentNode && t !== O() ? (a = (r = t.getBoundingClientRect()).top, l = r.left, s = r.bottom, c = r.right, u = r.height, r.width) : (l = a = 0, s = window.innerHeight, c = window.innerWidth, u = window.innerHeight, window.innerWidth);
      if ((e || n) && t !== window && (i = i || t.parentNode, !y)) do {
        if (i && i.getBoundingClientRect && ("none" !== R(i, "transform") || n && "static" !== R(i, "position"))) {
          var h = i.getBoundingClientRect();
          a -= h.top + parseInt(R(i, "border-top-width")), l -= h.left + parseInt(R(i, "border-left-width")), s = a + r.height, c = l + r.width;
          break;
        }
      } while (i = i.parentNode);
      return o && t !== window && (o = (e = v(i || t)) && e.a, t = e && e.d, e && (s = (a /= t) + (u /= t), c = (l /= o) + (d /= o))), {
        top: a,
        left: l,
        bottom: s,
        right: c,
        width: d,
        height: u
      };
    }
  }
  function Y(t, e, n) {
    for (var o = M(t, !0), i = X(t)[e]; o;) {
      var r = X(o)[n];
      if (!("top" === n || "left" === n ? r <= i : i <= r)) return o;
      if (o === O()) break;
      o = M(o, !1);
    }
    return !1;
  }
  function B(t, e, n, o) {
    for (var i = 0, r = 0, a = t.children; r < a.length;) {
      if ("none" !== a[r].style.display && a[r] !== Ft.ghost && (o || a[r] !== Ft.dragged) && P(a[r], n.draggable, t, !1)) {
        if (i === e) return a[r];
        i++;
      }
      r++;
    }
    return null;
  }
  function F(t, e) {
    for (var n = t.lastElementChild; n && (n === Ft.ghost || "none" === R(n, "display") || e && !p(n, e));) {
      n = n.previousElementSibling;
    }
    return n || null;
  }
  function j(t, e) {
    var n = 0;
    if (!t || !t.parentNode) return -1;
    for (; t = t.previousElementSibling;) {
      "TEMPLATE" === t.nodeName.toUpperCase() || t === Ft.clone || e && !p(t, e) || n++;
    }
    return n;
  }
  function E(t) {
    var e = 0,
      n = 0,
      o = O();
    if (t) do {
      var i = v(t),
        r = i.a,
        i = i.d;
    } while ((e += t.scrollLeft * r, n += t.scrollTop * i, t !== o && (t = t.parentNode)));
    return [e, n];
  }
  function M(t, e) {
    if (!t || !t.getBoundingClientRect) return O();
    var n = t,
      o = !1;
    do {
      if (n.clientWidth < n.scrollWidth || n.clientHeight < n.scrollHeight) {
        var i = R(n);
        if (n.clientWidth < n.scrollWidth && ("auto" == i.overflowX || "scroll" == i.overflowX) || n.clientHeight < n.scrollHeight && ("auto" == i.overflowY || "scroll" == i.overflowY)) {
          if (!n.getBoundingClientRect || n === document.body) return O();
          if (o || e) return n;
          o = !0;
        }
      }
    } while (n = n.parentNode);
    return O();
  }
  function D(t, e) {
    return Math.round(t.top) === Math.round(e.top) && Math.round(t.left) === Math.round(e.left) && Math.round(t.height) === Math.round(e.height) && Math.round(t.width) === Math.round(e.width);
  }
  function S(e, n) {
    return function () {
      var t;
      g || (1 === (t = arguments).length ? e.call(this, t[0]) : e.apply(this, t), g = setTimeout(function () {
        g = void 0;
      }, n));
    };
  }
  function H(t, e, n) {
    t.scrollLeft += e, t.scrollTop += n;
  }
  function _(t) {
    var e = window.Polymer,
      n = window.jQuery || window.Zepto;
    return e && e.dom ? e.dom(t).cloneNode(!0) : n ? n(t).clone(!0)[0] : t.cloneNode(!0);
  }
  function C(t, e) {
    R(t, "position", "absolute"), R(t, "top", e.top), R(t, "left", e.left), R(t, "width", e.width), R(t, "height", e.height);
  }
  function T(t) {
    R(t, "position", ""), R(t, "top", ""), R(t, "left", ""), R(t, "width", ""), R(t, "height", "");
  }
  function L(n, o, i) {
    var r = {};
    return Array.from(n.children).forEach(function (t) {
      var e;
      P(t, o.draggable, n, !1) && !t.animated && t !== i && (e = X(t), r.left = Math.min(null !== (t = r.left) && void 0 !== t ? t : 1 / 0, e.left), r.top = Math.min(null !== (t = r.top) && void 0 !== t ? t : 1 / 0, e.top), r.right = Math.max(null !== (t = r.right) && void 0 !== t ? t : -1 / 0, e.right), r.bottom = Math.max(null !== (t = r.bottom) && void 0 !== t ? t : -1 / 0, e.bottom));
    }), r.width = r.right - r.left, r.height = r.bottom - r.top, r.x = r.left, r.y = r.top, r;
  }
  var K = "Sortable" + new Date().getTime();
  function x() {
    var e,
      o = [];
    return {
      captureAnimationState: function captureAnimationState() {
        o = [], this.options.animation && [].slice.call(this.el.children).forEach(function (t) {
          var e, n;
          "none" !== R(t, "display") && t !== Ft.ghost && (o.push({
            target: t,
            rect: X(t)
          }), e = I({}, o[o.length - 1].rect), !t.thisAnimationDuration || (n = v(t, !0)) && (e.top -= n.f, e.left -= n.e), t.fromRect = e);
        });
      },
      addAnimationState: function addAnimationState(t) {
        o.push(t);
      },
      removeAnimationState: function removeAnimationState(t) {
        o.splice(function (t, e) {
          for (var n in t) {
            if (t.hasOwnProperty(n)) for (var o in e) {
              if (e.hasOwnProperty(o) && e[o] === t[n][o]) return Number(n);
            }
          }
          return -1;
        }(o, {
          target: t
        }), 1);
      },
      animateAll: function animateAll(t) {
        var c = this;
        if (!this.options.animation) return clearTimeout(e), void ("function" == typeof t && t());
        var u = !1,
          d = 0;
        o.forEach(function (t) {
          var e = 0,
            n = t.target,
            o = n.fromRect,
            i = X(n),
            r = n.prevFromRect,
            a = n.prevToRect,
            l = t.rect,
            s = v(n, !0);
          s && (i.top -= s.f, i.left -= s.e), n.toRect = i, n.thisAnimationDuration && D(r, i) && !D(o, i) && (l.top - i.top) / (l.left - i.left) == (o.top - i.top) / (o.left - i.left) && (t = l, s = r, r = a, a = c.options, e = Math.sqrt(Math.pow(s.top - t.top, 2) + Math.pow(s.left - t.left, 2)) / Math.sqrt(Math.pow(s.top - r.top, 2) + Math.pow(s.left - r.left, 2)) * a.animation), D(i, o) || (n.prevFromRect = o, n.prevToRect = i, e = e || c.options.animation, c.animate(n, l, i, e)), e && (u = !0, d = Math.max(d, e), clearTimeout(n.animationResetTimer), n.animationResetTimer = setTimeout(function () {
            n.animationTime = 0, n.prevFromRect = null, n.fromRect = null, n.prevToRect = null, n.thisAnimationDuration = null;
          }, e), n.thisAnimationDuration = e);
        }), clearTimeout(e), u ? e = setTimeout(function () {
          "function" == typeof t && t();
        }, d) : "function" == typeof t && t(), o = [];
      },
      animate: function animate(t, e, n, o) {
        var i, r;
        o && (R(t, "transition", ""), R(t, "transform", ""), i = (r = v(this.el)) && r.a, r = r && r.d, i = (e.left - n.left) / (i || 1), r = (e.top - n.top) / (r || 1), t.animatingX = !!i, t.animatingY = !!r, R(t, "transform", "translate3d(" + i + "px," + r + "px,0)"), this.forRepaintDummy = t.offsetWidth, R(t, "transition", "transform " + o + "ms" + (this.options.easing ? " " + this.options.easing : "")), R(t, "transform", "translate3d(0,0,0)"), "number" == typeof t.animated && clearTimeout(t.animated), t.animated = setTimeout(function () {
          R(t, "transition", ""), R(t, "transform", ""), t.animated = !1, t.animatingX = !1, t.animatingY = !1;
        }, o));
      }
    };
  }
  var A = [],
    N = {
      initializeByDefault: !0
    },
    W = {
      mount: function mount(e) {
        for (var t in N) {
          !N.hasOwnProperty(t) || t in e || (e[t] = N[t]);
        }
        A.forEach(function (t) {
          if (t.pluginName === e.pluginName) throw "Sortable: Cannot mount plugin ".concat(e.pluginName, " more than once");
        }), A.push(e);
      },
      pluginEvent: function pluginEvent(e, n, o) {
        var t = this;
        this.eventCanceled = !1, o.cancel = function () {
          t.eventCanceled = !0;
        };
        var i = e + "Global";
        A.forEach(function (t) {
          n[t.pluginName] && (n[t.pluginName][i] && n[t.pluginName][i](I({
            sortable: n
          }, o)), n.options[t.pluginName] && n[t.pluginName][e] && n[t.pluginName][e](I({
            sortable: n
          }, o)));
        });
      },
      initializePlugins: function initializePlugins(n, o, i, t) {
        for (var e in A.forEach(function (t) {
          var e = t.pluginName;
          (n.options[e] || t.initializeByDefault) && ((t = new t(n, o, n.options)).sortable = n, t.options = n.options, n[e] = t, a(i, t.defaults));
        }), n.options) {
          var r;
          n.options.hasOwnProperty(e) && void 0 !== (r = this.modifyOption(n, e, n.options[e])) && (n.options[e] = r);
        }
      },
      getEventProperties: function getEventProperties(e, n) {
        var o = {};
        return A.forEach(function (t) {
          "function" == typeof t.eventProperties && a(o, t.eventProperties.call(n[t.pluginName], e));
        }), o;
      },
      modifyOption: function modifyOption(e, n, o) {
        var i;
        return A.forEach(function (t) {
          e[t.pluginName] && t.optionListeners && "function" == typeof t.optionListeners[n] && (i = t.optionListeners[n].call(e[t.pluginName], o));
        }), i;
      }
    };
  function z(t) {
    var e = t.sortable,
      n = t.rootEl,
      o = t.name,
      i = t.targetEl,
      r = t.cloneEl,
      a = t.toEl,
      l = t.fromEl,
      s = t.oldIndex,
      c = t.newIndex,
      u = t.oldDraggableIndex,
      d = t.newDraggableIndex,
      h = t.originalEvent,
      f = t.putSortable,
      p = t.extraEventProperties;
    if (e = e || n && n[K]) {
      var g,
        m = e.options,
        t = "on" + o.charAt(0).toUpperCase() + o.substr(1);
      !window.CustomEvent || y || w ? (g = document.createEvent("Event")).initEvent(o, !0, !0) : g = new CustomEvent(o, {
        bubbles: !0,
        cancelable: !0
      }), g.to = a || n, g.from = l || n, g.item = i || n, g.clone = r, g.oldIndex = s, g.newIndex = c, g.oldDraggableIndex = u, g.newDraggableIndex = d, g.originalEvent = h, g.pullMode = f ? f.lastPutMode : void 0;
      var v,
        b = I(I({}, p), W.getEventProperties(o, e));
      for (v in b) {
        g[v] = b[v];
      }
      n && n.dispatchEvent(g), m[t] && m[t].call(e, g);
    }
  }
  function G(t, e) {
    var n = (o = 2 < arguments.length && void 0 !== arguments[2] ? arguments[2] : {}).evt,
      o = i(o, U);
    W.pluginEvent.bind(Ft)(t, e, I({
      dragEl: V,
      parentEl: Z,
      ghostEl: $,
      rootEl: Q,
      nextEl: J,
      lastDownEl: tt,
      cloneEl: et,
      cloneHidden: nt,
      dragStarted: gt,
      putSortable: st,
      activeSortable: Ft.active,
      originalEvent: n,
      oldIndex: ot,
      oldDraggableIndex: rt,
      newIndex: it,
      newDraggableIndex: at,
      hideGhostForTarget: Rt,
      unhideGhostForTarget: Xt,
      cloneNowHidden: function cloneNowHidden() {
        nt = !0;
      },
      cloneNowShown: function cloneNowShown() {
        nt = !1;
      },
      dispatchSortableEvent: function dispatchSortableEvent(t) {
        q({
          sortable: e,
          name: t,
          originalEvent: n
        });
      }
    }, o));
  }
  var U = ["evt"];
  function q(t) {
    z(I({
      putSortable: st,
      cloneEl: et,
      targetEl: V,
      rootEl: Q,
      oldIndex: ot,
      oldDraggableIndex: rt,
      newIndex: it,
      newDraggableIndex: at
    }, t));
  }
  var V,
    Z,
    $,
    Q,
    J,
    tt,
    et,
    nt,
    ot,
    it,
    rt,
    at,
    lt,
    st,
    ct,
    ut,
    dt,
    ht,
    ft,
    pt,
    gt,
    mt,
    vt,
    bt,
    yt,
    wt = !1,
    Et = !1,
    Dt = [],
    St = !1,
    _t = !1,
    Ct = [],
    Tt = !1,
    xt = [],
    Ot = "undefined" != typeof document,
    Mt = n,
    At = w || y ? "cssFloat" : "float",
    Nt = Ot && !c && !n && "draggable" in document.createElement("div"),
    It = function () {
      if (Ot) {
        if (y) return !1;
        var t = document.createElement("x");
        return t.style.cssText = "pointer-events:auto", "auto" === t.style.pointerEvents;
      }
    }(),
    Pt = function Pt(t, e) {
      var n = R(t),
        o = parseInt(n.width) - parseInt(n.paddingLeft) - parseInt(n.paddingRight) - parseInt(n.borderLeftWidth) - parseInt(n.borderRightWidth),
        i = B(t, 0, e),
        r = B(t, 1, e),
        a = i && R(i),
        l = r && R(r),
        s = a && parseInt(a.marginLeft) + parseInt(a.marginRight) + X(i).width,
        t = l && parseInt(l.marginLeft) + parseInt(l.marginRight) + X(r).width;
      if ("flex" === n.display) return "column" === n.flexDirection || "column-reverse" === n.flexDirection ? "vertical" : "horizontal";
      if ("grid" === n.display) return n.gridTemplateColumns.split(" ").length <= 1 ? "vertical" : "horizontal";
      if (i && a["float"] && "none" !== a["float"]) {
        e = "left" === a["float"] ? "left" : "right";
        return !r || "both" !== l.clear && l.clear !== e ? "horizontal" : "vertical";
      }
      return i && ("block" === a.display || "flex" === a.display || "table" === a.display || "grid" === a.display || o <= s && "none" === n[At] || r && "none" === n[At] && o < s + t) ? "vertical" : "horizontal";
    },
    kt = function kt(t) {
      function l(r, a) {
        return function (t, e, n, o) {
          var i = t.options.group.name && e.options.group.name && t.options.group.name === e.options.group.name;
          if (null == r && (a || i)) return !0;
          if (null == r || !1 === r) return !1;
          if (a && "clone" === r) return r;
          if ("function" == typeof r) return l(r(t, e, n, o), a)(t, e, n, o);
          e = (a ? t : e).options.group.name;
          return !0 === r || "string" == typeof r && r === e || r.join && -1 < r.indexOf(e);
        };
      }
      var e = {},
        n = t.group;
      n && "object" == o(n) || (n = {
        name: n
      }), e.name = n.name, e.checkPull = l(n.pull, !0), e.checkPut = l(n.put), e.revertClone = n.revertClone, t.group = e;
    },
    Rt = function Rt() {
      !It && $ && R($, "display", "none");
    },
    Xt = function Xt() {
      !It && $ && R($, "display", "");
    };
  Ot && !c && document.addEventListener("click", function (t) {
    if (Et) return t.preventDefault(), t.stopPropagation && t.stopPropagation(), t.stopImmediatePropagation && t.stopImmediatePropagation(), Et = !1;
  }, !0);
  function Yt(t) {
    if (V) {
      t = t.touches ? t.touches[0] : t;
      var e = (i = t.clientX, r = t.clientY, Dt.some(function (t) {
        var e = t[K].options.emptyInsertThreshold;
        if (e && !F(t)) {
          var n = X(t),
            o = i >= n.left - e && i <= n.right + e,
            e = r >= n.top - e && r <= n.bottom + e;
          return o && e ? a = t : void 0;
        }
      }), a);
      if (e) {
        var n,
          o = {};
        for (n in t) {
          t.hasOwnProperty(n) && (o[n] = t[n]);
        }
        o.target = o.rootEl = e, o.preventDefault = void 0, o.stopPropagation = void 0, e[K]._onDragOver(o);
      }
    }
    var i, r, a;
  }
  function Bt(t) {
    V && V.parentNode[K]._isOutsideThisEl(t.target);
  }
  function Ft(t, e) {
    if (!t || !t.nodeType || 1 !== t.nodeType) throw "Sortable: `el` must be an HTMLElement, not ".concat({}.toString.call(t));
    this.el = t, this.options = e = a({}, e), t[K] = this;
    var n,
      o,
      i = {
        group: null,
        sort: !0,
        disabled: !1,
        store: null,
        handle: null,
        draggable: /^[uo]l$/i.test(t.nodeName) ? ">li" : ">*",
        swapThreshold: 1,
        invertSwap: !1,
        invertedSwapThreshold: null,
        removeCloneOnHide: !0,
        direction: function direction() {
          return Pt(t, this.options);
        },
        ghostClass: "sortable-ghost",
        chosenClass: "sortable-chosen",
        dragClass: "sortable-drag",
        ignore: "a, img",
        filter: null,
        preventOnFilter: !0,
        animation: 0,
        easing: null,
        setData: function setData(t, e) {
          t.setData("Text", e.textContent);
        },
        dropBubble: !1,
        dragoverBubble: !1,
        dataIdAttr: "data-id",
        delay: 0,
        delayOnTouchOnly: !1,
        touchStartThreshold: (Number.parseInt ? Number : window).parseInt(window.devicePixelRatio, 10) || 1,
        forceFallback: !1,
        fallbackClass: "sortable-fallback",
        fallbackOnBody: !1,
        fallbackTolerance: 0,
        fallbackOffset: {
          x: 0,
          y: 0
        },
        supportPointer: !1 !== Ft.supportPointer && "PointerEvent" in window && !u,
        emptyInsertThreshold: 5
      };
    for (n in W.initializePlugins(this, t, i), i) {
      n in e || (e[n] = i[n]);
    }
    for (o in kt(e), this) {
      "_" === o.charAt(0) && "function" == typeof this[o] && (this[o] = this[o].bind(this));
    }
    this.nativeDraggable = !e.forceFallback && Nt, this.nativeDraggable && (this.options.touchStartThreshold = 1), e.supportPointer ? h(t, "pointerdown", this._onTapStart) : (h(t, "mousedown", this._onTapStart), h(t, "touchstart", this._onTapStart)), this.nativeDraggable && (h(t, "dragover", this), h(t, "dragenter", this)), Dt.push(this.el), e.store && e.store.get && this.sort(e.store.get(this) || []), a(this, x());
  }
  function jt(t, e, n, o, i, r, a, l) {
    var s,
      c,
      u = t[K],
      d = u.options.onMove;
    return !window.CustomEvent || y || w ? (s = document.createEvent("Event")).initEvent("move", !0, !0) : s = new CustomEvent("move", {
      bubbles: !0,
      cancelable: !0
    }), s.to = e, s.from = t, s.dragged = n, s.draggedRect = o, s.related = i || e, s.relatedRect = r || X(e), s.willInsertAfter = l, s.originalEvent = a, t.dispatchEvent(s), c = d ? d.call(u, s, a) : c;
  }
  function Ht(t) {
    t.draggable = !1;
  }
  function Lt() {
    Tt = !1;
  }
  function Kt(t) {
    return setTimeout(t, 0);
  }
  function Wt(t) {
    return clearTimeout(t);
  }
  Ft.prototype = {
    constructor: Ft,
    _isOutsideThisEl: function _isOutsideThisEl(t) {
      this.el.contains(t) || t === this.el || (mt = null);
    },
    _getDirection: function _getDirection(t, e) {
      return "function" == typeof this.options.direction ? this.options.direction.call(this, t, e, V) : this.options.direction;
    },
    _onTapStart: function _onTapStart(e) {
      if (e.cancelable) {
        var n = this,
          o = this.el,
          t = this.options,
          i = t.preventOnFilter,
          r = e.type,
          a = e.touches && e.touches[0] || e.pointerType && "touch" === e.pointerType && e,
          l = (a || e).target,
          s = e.target.shadowRoot && (e.path && e.path[0] || e.composedPath && e.composedPath()[0]) || l,
          c = t.filter;
        if (!function (t) {
          xt.length = 0;
          var e = t.getElementsByTagName("input"),
            n = e.length;
          for (; n--;) {
            var o = e[n];
            o.checked && xt.push(o);
          }
        }(o), !V && !(/mousedown|pointerdown/.test(r) && 0 !== e.button || t.disabled) && !s.isContentEditable && (this.nativeDraggable || !u || !l || "SELECT" !== l.tagName.toUpperCase()) && !((l = P(l, t.draggable, o, !1)) && l.animated || tt === l)) {
          if (ot = j(l), rt = j(l, t.draggable), "function" == typeof c) {
            if (c.call(this, e, l, this)) return q({
              sortable: n,
              rootEl: s,
              name: "filter",
              targetEl: l,
              toEl: o,
              fromEl: o
            }), G("filter", n, {
              evt: e
            }), void (i && e.cancelable && e.preventDefault());
          } else if (c = c && c.split(",").some(function (t) {
            if (t = P(s, t.trim(), o, !1)) return q({
              sortable: n,
              rootEl: t,
              name: "filter",
              targetEl: l,
              fromEl: o,
              toEl: o
            }), G("filter", n, {
              evt: e
            }), !0;
          })) return void (i && e.cancelable && e.preventDefault());
          t.handle && !P(s, t.handle, o, !1) || this._prepareDragStart(e, a, l);
        }
      }
    },
    _prepareDragStart: function _prepareDragStart(t, e, n) {
      var o,
        i = this,
        r = i.el,
        a = i.options,
        l = r.ownerDocument;
      n && !V && n.parentNode === r && (o = X(n), Q = r, Z = (V = n).parentNode, J = V.nextSibling, tt = n, lt = a.group, ct = {
        target: Ft.dragged = V,
        clientX: (e || t).clientX,
        clientY: (e || t).clientY
      }, ft = ct.clientX - o.left, pt = ct.clientY - o.top, this._lastX = (e || t).clientX, this._lastY = (e || t).clientY, V.style["will-change"] = "all", o = function o() {
        G("delayEnded", i, {
          evt: t
        }), Ft.eventCanceled ? i._onDrop() : (i._disableDelayedDragEvents(), !s && i.nativeDraggable && (V.draggable = !0), i._triggerDragStart(t, e), q({
          sortable: i,
          name: "choose",
          originalEvent: t
        }), k(V, a.chosenClass, !0));
      }, a.ignore.split(",").forEach(function (t) {
        b(V, t.trim(), Ht);
      }), h(l, "dragover", Yt), h(l, "mousemove", Yt), h(l, "touchmove", Yt), h(l, "mouseup", i._onDrop), h(l, "touchend", i._onDrop), h(l, "touchcancel", i._onDrop), s && this.nativeDraggable && (this.options.touchStartThreshold = 4, V.draggable = !0), G("delayStart", this, {
        evt: t
      }), !a.delay || a.delayOnTouchOnly && !e || this.nativeDraggable && (w || y) ? o() : Ft.eventCanceled ? this._onDrop() : (h(l, "mouseup", i._disableDelayedDrag), h(l, "touchend", i._disableDelayedDrag), h(l, "touchcancel", i._disableDelayedDrag), h(l, "mousemove", i._delayedDragTouchMoveHandler), h(l, "touchmove", i._delayedDragTouchMoveHandler), a.supportPointer && h(l, "pointermove", i._delayedDragTouchMoveHandler), i._dragStartTimer = setTimeout(o, a.delay)));
    },
    _delayedDragTouchMoveHandler: function _delayedDragTouchMoveHandler(t) {
      t = t.touches ? t.touches[0] : t;
      Math.max(Math.abs(t.clientX - this._lastX), Math.abs(t.clientY - this._lastY)) >= Math.floor(this.options.touchStartThreshold / (this.nativeDraggable && window.devicePixelRatio || 1)) && this._disableDelayedDrag();
    },
    _disableDelayedDrag: function _disableDelayedDrag() {
      V && Ht(V), clearTimeout(this._dragStartTimer), this._disableDelayedDragEvents();
    },
    _disableDelayedDragEvents: function _disableDelayedDragEvents() {
      var t = this.el.ownerDocument;
      f(t, "mouseup", this._disableDelayedDrag), f(t, "touchend", this._disableDelayedDrag), f(t, "touchcancel", this._disableDelayedDrag), f(t, "mousemove", this._delayedDragTouchMoveHandler), f(t, "touchmove", this._delayedDragTouchMoveHandler), f(t, "pointermove", this._delayedDragTouchMoveHandler);
    },
    _triggerDragStart: function _triggerDragStart(t, e) {
      e = e || "touch" == t.pointerType && t, !this.nativeDraggable || e ? this.options.supportPointer ? h(document, "pointermove", this._onTouchMove) : h(document, e ? "touchmove" : "mousemove", this._onTouchMove) : (h(V, "dragend", this), h(Q, "dragstart", this._onDragStart));
      try {
        document.selection ? Kt(function () {
          document.selection.empty();
        }) : window.getSelection().removeAllRanges();
      } catch (t) {}
    },
    _dragStarted: function _dragStarted(t, e) {
      var n;
      wt = !1, Q && V ? (G("dragStarted", this, {
        evt: e
      }), this.nativeDraggable && h(document, "dragover", Bt), n = this.options, t || k(V, n.dragClass, !1), k(V, n.ghostClass, !0), Ft.active = this, t && this._appendGhost(), q({
        sortable: this,
        name: "start",
        originalEvent: e
      })) : this._nulling();
    },
    _emulateDragOver: function _emulateDragOver() {
      if (ut) {
        this._lastX = ut.clientX, this._lastY = ut.clientY, Rt();
        for (var t = document.elementFromPoint(ut.clientX, ut.clientY), e = t; t && t.shadowRoot && (t = t.shadowRoot.elementFromPoint(ut.clientX, ut.clientY)) !== e;) {
          e = t;
        }
        if (V.parentNode[K]._isOutsideThisEl(t), e) do {
          if (e[K]) if (e[K]._onDragOver({
            clientX: ut.clientX,
            clientY: ut.clientY,
            target: t,
            rootEl: e
          }) && !this.options.dragoverBubble) break;
        } while (e = (t = e).parentNode);
        Xt();
      }
    },
    _onTouchMove: function _onTouchMove(t) {
      if (ct) {
        var e = this.options,
          n = e.fallbackTolerance,
          o = e.fallbackOffset,
          i = t.touches ? t.touches[0] : t,
          r = $ && v($, !0),
          a = $ && r && r.a,
          l = $ && r && r.d,
          e = Mt && yt && E(yt),
          a = (i.clientX - ct.clientX + o.x) / (a || 1) + (e ? e[0] - Ct[0] : 0) / (a || 1),
          l = (i.clientY - ct.clientY + o.y) / (l || 1) + (e ? e[1] - Ct[1] : 0) / (l || 1);
        if (!Ft.active && !wt) {
          if (n && Math.max(Math.abs(i.clientX - this._lastX), Math.abs(i.clientY - this._lastY)) < n) return;
          this._onDragStart(t, !0);
        }
        $ && (r ? (r.e += a - (dt || 0), r.f += l - (ht || 0)) : r = {
          a: 1,
          b: 0,
          c: 0,
          d: 1,
          e: a,
          f: l
        }, r = "matrix(".concat(r.a, ",").concat(r.b, ",").concat(r.c, ",").concat(r.d, ",").concat(r.e, ",").concat(r.f, ")"), R($, "webkitTransform", r), R($, "mozTransform", r), R($, "msTransform", r), R($, "transform", r), dt = a, ht = l, ut = i), t.cancelable && t.preventDefault();
      }
    },
    _appendGhost: function _appendGhost() {
      if (!$) {
        var t = this.options.fallbackOnBody ? document.body : Q,
          e = X(V, !0, Mt, !0, t),
          n = this.options;
        if (Mt) {
          for (yt = t; "static" === R(yt, "position") && "none" === R(yt, "transform") && yt !== document;) {
            yt = yt.parentNode;
          }
          yt !== document.body && yt !== document.documentElement ? (yt === document && (yt = O()), e.top += yt.scrollTop, e.left += yt.scrollLeft) : yt = O(), Ct = E(yt);
        }
        k($ = V.cloneNode(!0), n.ghostClass, !1), k($, n.fallbackClass, !0), k($, n.dragClass, !0), R($, "transition", ""), R($, "transform", ""), R($, "box-sizing", "border-box"), R($, "margin", 0), R($, "top", e.top), R($, "left", e.left), R($, "width", e.width), R($, "height", e.height), R($, "opacity", "0.8"), R($, "position", Mt ? "absolute" : "fixed"), R($, "zIndex", "100000"), R($, "pointerEvents", "none"), Ft.ghost = $, t.appendChild($), R($, "transform-origin", ft / parseInt($.style.width) * 100 + "% " + pt / parseInt($.style.height) * 100 + "%");
      }
    },
    _onDragStart: function _onDragStart(t, e) {
      var n = this,
        o = t.dataTransfer,
        i = n.options;
      G("dragStart", this, {
        evt: t
      }), Ft.eventCanceled ? this._onDrop() : (G("setupClone", this), Ft.eventCanceled || ((et = _(V)).removeAttribute("id"), et.draggable = !1, et.style["will-change"] = "", this._hideClone(), k(et, this.options.chosenClass, !1), Ft.clone = et), n.cloneId = Kt(function () {
        G("clone", n), Ft.eventCanceled || (n.options.removeCloneOnHide || Q.insertBefore(et, V), n._hideClone(), q({
          sortable: n,
          name: "clone"
        }));
      }), e || k(V, i.dragClass, !0), e ? (Et = !0, n._loopId = setInterval(n._emulateDragOver, 50)) : (f(document, "mouseup", n._onDrop), f(document, "touchend", n._onDrop), f(document, "touchcancel", n._onDrop), o && (o.effectAllowed = "move", i.setData && i.setData.call(n, o, V)), h(document, "drop", n), R(V, "transform", "translateZ(0)")), wt = !0, n._dragStartId = Kt(n._dragStarted.bind(n, e, t)), h(document, "selectstart", n), gt = !0, u && R(document.body, "user-select", "none"));
    },
    _onDragOver: function _onDragOver(n) {
      var o,
        i,
        r,
        t,
        e,
        a = this.el,
        l = n.target,
        s = this.options,
        c = s.group,
        u = Ft.active,
        d = lt === c,
        h = s.sort,
        f = st || u,
        p = this,
        g = !1;
      if (!Tt) {
        if (void 0 !== n.preventDefault && n.cancelable && n.preventDefault(), l = P(l, s.draggable, a, !0), O("dragOver"), Ft.eventCanceled) return g;
        if (V.contains(n.target) || l.animated && l.animatingX && l.animatingY || p._ignoreWhileAnimating === l) return A(!1);
        if (Et = !1, u && !s.disabled && (d ? h || (i = Z !== Q) : st === this || (this.lastPutMode = lt.checkPull(this, u, V, n)) && c.checkPut(this, u, V, n))) {
          if (r = "vertical" === this._getDirection(n, l), o = X(V), O("dragOverValid"), Ft.eventCanceled) return g;
          if (i) return Z = Q, M(), this._hideClone(), O("revert"), Ft.eventCanceled || (J ? Q.insertBefore(V, J) : Q.appendChild(V)), A(!0);
          var m = F(a, s.draggable);
          if (m && (S = n, c = r, x = X(F((D = this).el, D.options.draggable)), D = L(D.el, D.options, $), !(c ? S.clientX > D.right + 10 || S.clientY > x.bottom && S.clientX > x.left : S.clientY > D.bottom + 10 || S.clientX > x.right && S.clientY > x.top) || m.animated)) {
            if (m && (t = n, e = r, C = X(B((_ = this).el, 0, _.options, !0)), _ = L(_.el, _.options, $), e ? t.clientX < _.left - 10 || t.clientY < C.top && t.clientX < C.right : t.clientY < _.top - 10 || t.clientY < C.bottom && t.clientX < C.left)) {
              var v = B(a, 0, s, !0);
              if (v === V) return A(!1);
              if (E = X(l = v), !1 !== jt(Q, a, V, o, l, E, n, !1)) return M(), a.insertBefore(V, v), Z = a, N(), A(!0);
            } else if (l.parentNode === a) {
              var b,
                y,
                w,
                E = X(l),
                D = V.parentNode !== a,
                S = (S = V.animated && V.toRect || o, x = l.animated && l.toRect || E, _ = (e = r) ? S.left : S.top, t = e ? S.right : S.bottom, C = e ? S.width : S.height, v = e ? x.left : x.top, S = e ? x.right : x.bottom, x = e ? x.width : x.height, !(_ === v || t === S || _ + C / 2 === v + x / 2)),
                _ = r ? "top" : "left",
                C = Y(l, "top", "top") || Y(V, "top", "top"),
                v = C ? C.scrollTop : void 0;
              if (mt !== l && (y = E[_], St = !1, _t = !S && s.invertSwap || D), 0 !== (b = function (t, e, n, o, i, r, a, l) {
                var s = o ? t.clientY : t.clientX,
                  c = o ? n.height : n.width,
                  t = o ? n.top : n.left,
                  o = o ? n.bottom : n.right,
                  n = !1;
                if (!a) if (l && bt < c * i) {
                  if (St = !St && (1 === vt ? t + c * r / 2 < s : s < o - c * r / 2) ? !0 : St) n = !0;else if (1 === vt ? s < t + bt : o - bt < s) return -vt;
                } else if (t + c * (1 - i) / 2 < s && s < o - c * (1 - i) / 2) return function (t) {
                  return j(V) < j(t) ? 1 : -1;
                }(e);
                if ((n = n || a) && (s < t + c * r / 2 || o - c * r / 2 < s)) return t + c / 2 < s ? 1 : -1;
                return 0;
              }(n, l, E, r, S ? 1 : s.swapThreshold, null == s.invertedSwapThreshold ? s.swapThreshold : s.invertedSwapThreshold, _t, mt === l))) for (var T = j(V); (w = Z.children[T -= b]) && ("none" === R(w, "display") || w === $);) {
                ;
              }
              if (0 === b || w === l) return A(!1);
              vt = b;
              var x = (mt = l).nextElementSibling,
                D = !1,
                S = jt(Q, a, V, o, l, E, n, D = 1 === b);
              if (!1 !== S) return 1 !== S && -1 !== S || (D = 1 === S), Tt = !0, setTimeout(Lt, 30), M(), D && !x ? a.appendChild(V) : l.parentNode.insertBefore(V, D ? x : l), C && H(C, 0, v - C.scrollTop), Z = V.parentNode, void 0 === y || _t || (bt = Math.abs(y - X(l)[_])), N(), A(!0);
            }
          } else {
            if (m === V) return A(!1);
            if ((l = m && a === n.target ? m : l) && (E = X(l)), !1 !== jt(Q, a, V, o, l, E, n, !!l)) return M(), m && m.nextSibling ? a.insertBefore(V, m.nextSibling) : a.appendChild(V), Z = a, N(), A(!0);
          }
          if (a.contains(V)) return A(!1);
        }
        return !1;
      }
      function O(t, e) {
        G(t, p, I({
          evt: n,
          isOwner: d,
          axis: r ? "vertical" : "horizontal",
          revert: i,
          dragRect: o,
          targetRect: E,
          canSort: h,
          fromSortable: f,
          target: l,
          completed: A,
          onMove: function onMove(t, e) {
            return jt(Q, a, V, o, t, X(t), n, e);
          },
          changed: N
        }, e));
      }
      function M() {
        O("dragOverAnimationCapture"), p.captureAnimationState(), p !== f && f.captureAnimationState();
      }
      function A(t) {
        return O("dragOverCompleted", {
          insertion: t
        }), t && (d ? u._hideClone() : u._showClone(p), p !== f && (k(V, (st || u).options.ghostClass, !1), k(V, s.ghostClass, !0)), st !== p && p !== Ft.active ? st = p : p === Ft.active && st && (st = null), f === p && (p._ignoreWhileAnimating = l), p.animateAll(function () {
          O("dragOverAnimationComplete"), p._ignoreWhileAnimating = null;
        }), p !== f && (f.animateAll(), f._ignoreWhileAnimating = null)), (l === V && !V.animated || l === a && !l.animated) && (mt = null), s.dragoverBubble || n.rootEl || l === document || (V.parentNode[K]._isOutsideThisEl(n.target), t || Yt(n)), !s.dragoverBubble && n.stopPropagation && n.stopPropagation(), g = !0;
      }
      function N() {
        it = j(V), at = j(V, s.draggable), q({
          sortable: p,
          name: "change",
          toEl: a,
          newIndex: it,
          newDraggableIndex: at,
          originalEvent: n
        });
      }
    },
    _ignoreWhileAnimating: null,
    _offMoveEvents: function _offMoveEvents() {
      f(document, "mousemove", this._onTouchMove), f(document, "touchmove", this._onTouchMove), f(document, "pointermove", this._onTouchMove), f(document, "dragover", Yt), f(document, "mousemove", Yt), f(document, "touchmove", Yt);
    },
    _offUpEvents: function _offUpEvents() {
      var t = this.el.ownerDocument;
      f(t, "mouseup", this._onDrop), f(t, "touchend", this._onDrop), f(t, "pointerup", this._onDrop), f(t, "touchcancel", this._onDrop), f(document, "selectstart", this);
    },
    _onDrop: function _onDrop(t) {
      var e = this.el,
        n = this.options;
      it = j(V), at = j(V, n.draggable), G("drop", this, {
        evt: t
      }), Z = V && V.parentNode, it = j(V), at = j(V, n.draggable), Ft.eventCanceled || (St = _t = wt = !1, clearInterval(this._loopId), clearTimeout(this._dragStartTimer), Wt(this.cloneId), Wt(this._dragStartId), this.nativeDraggable && (f(document, "drop", this), f(e, "dragstart", this._onDragStart)), this._offMoveEvents(), this._offUpEvents(), u && R(document.body, "user-select", ""), R(V, "transform", ""), t && (gt && (t.cancelable && t.preventDefault(), n.dropBubble || t.stopPropagation()), $ && $.parentNode && $.parentNode.removeChild($), (Q === Z || st && "clone" !== st.lastPutMode) && et && et.parentNode && et.parentNode.removeChild(et), V && (this.nativeDraggable && f(V, "dragend", this), Ht(V), V.style["will-change"] = "", gt && !wt && k(V, (st || this).options.ghostClass, !1), k(V, this.options.chosenClass, !1), q({
        sortable: this,
        name: "unchoose",
        toEl: Z,
        newIndex: null,
        newDraggableIndex: null,
        originalEvent: t
      }), Q !== Z ? (0 <= it && (q({
        rootEl: Z,
        name: "add",
        toEl: Z,
        fromEl: Q,
        originalEvent: t
      }), q({
        sortable: this,
        name: "remove",
        toEl: Z,
        originalEvent: t
      }), q({
        rootEl: Z,
        name: "sort",
        toEl: Z,
        fromEl: Q,
        originalEvent: t
      }), q({
        sortable: this,
        name: "sort",
        toEl: Z,
        originalEvent: t
      })), st && st.save()) : it !== ot && 0 <= it && (q({
        sortable: this,
        name: "update",
        toEl: Z,
        originalEvent: t
      }), q({
        sortable: this,
        name: "sort",
        toEl: Z,
        originalEvent: t
      })), Ft.active && (null != it && -1 !== it || (it = ot, at = rt), q({
        sortable: this,
        name: "end",
        toEl: Z,
        originalEvent: t
      }), this.save())))), this._nulling();
    },
    _nulling: function _nulling() {
      G("nulling", this), Q = V = Z = $ = J = et = tt = nt = ct = ut = gt = it = at = ot = rt = mt = vt = st = lt = Ft.dragged = Ft.ghost = Ft.clone = Ft.active = null, xt.forEach(function (t) {
        t.checked = !0;
      }), xt.length = dt = ht = 0;
    },
    handleEvent: function handleEvent(t) {
      switch (t.type) {
        case "drop":
        case "dragend":
          this._onDrop(t);
          break;
        case "dragenter":
        case "dragover":
          V && (this._onDragOver(t), function (t) {
            t.dataTransfer && (t.dataTransfer.dropEffect = "move");
            t.cancelable && t.preventDefault();
          }(t));
          break;
        case "selectstart":
          t.preventDefault();
      }
    },
    toArray: function toArray() {
      for (var t, e = [], n = this.el.children, o = 0, i = n.length, r = this.options; o < i; o++) {
        P(t = n[o], r.draggable, this.el, !1) && e.push(t.getAttribute(r.dataIdAttr) || function (t) {
          var e = t.tagName + t.className + t.src + t.href + t.textContent,
            n = e.length,
            o = 0;
          for (; n--;) {
            o += e.charCodeAt(n);
          }
          return o.toString(36);
        }(t));
      }
      return e;
    },
    sort: function sort(t, e) {
      var n = {},
        o = this.el;
      this.toArray().forEach(function (t, e) {
        e = o.children[e];
        P(e, this.options.draggable, o, !1) && (n[t] = e);
      }, this), e && this.captureAnimationState(), t.forEach(function (t) {
        n[t] && (o.removeChild(n[t]), o.appendChild(n[t]));
      }), e && this.animateAll();
    },
    save: function save() {
      var t = this.options.store;
      t && t.set && t.set(this);
    },
    closest: function closest(t, e) {
      return P(t, e || this.options.draggable, this.el, !1);
    },
    option: function option(t, e) {
      var n = this.options;
      if (void 0 === e) return n[t];
      var o = W.modifyOption(this, t, e);
      n[t] = void 0 !== o ? o : e, "group" === t && kt(n);
    },
    destroy: function destroy() {
      G("destroy", this);
      var t = this.el;
      t[K] = null, f(t, "mousedown", this._onTapStart), f(t, "touchstart", this._onTapStart), f(t, "pointerdown", this._onTapStart), this.nativeDraggable && (f(t, "dragover", this), f(t, "dragenter", this)), Array.prototype.forEach.call(t.querySelectorAll("[draggable]"), function (t) {
        t.removeAttribute("draggable");
      }), this._onDrop(), this._disableDelayedDragEvents(), Dt.splice(Dt.indexOf(this.el), 1), this.el = t = null;
    },
    _hideClone: function _hideClone() {
      nt || (G("hideClone", this), Ft.eventCanceled || (R(et, "display", "none"), this.options.removeCloneOnHide && et.parentNode && et.parentNode.removeChild(et), nt = !0));
    },
    _showClone: function _showClone(t) {
      "clone" === t.lastPutMode ? nt && (G("showClone", this), Ft.eventCanceled || (V.parentNode != Q || this.options.group.revertClone ? J ? Q.insertBefore(et, J) : Q.appendChild(et) : Q.insertBefore(et, V), this.options.group.revertClone && this.animate(V, et), R(et, "display", ""), nt = !1)) : this._hideClone();
    }
  }, Ot && h(document, "touchmove", function (t) {
    (Ft.active || wt) && t.cancelable && t.preventDefault();
  }), Ft.utils = {
    on: h,
    off: f,
    css: R,
    find: b,
    is: function is(t, e) {
      return !!P(t, e, t, !1);
    },
    extend: function extend(t, e) {
      if (t && e) for (var n in e) {
        e.hasOwnProperty(n) && (t[n] = e[n]);
      }
      return t;
    },
    throttle: S,
    closest: P,
    toggleClass: k,
    clone: _,
    index: j,
    nextTick: Kt,
    cancelNextTick: Wt,
    detectDirection: Pt,
    getChild: B
  }, Ft.get = function (t) {
    return t[K];
  }, Ft.mount = function () {
    for (var t = arguments.length, e = new Array(t), n = 0; n < t; n++) {
      e[n] = arguments[n];
    }
    (e = e[0].constructor === Array ? e[0] : e).forEach(function (t) {
      if (!t.prototype || !t.prototype.constructor) throw "Sortable: Mounted plugin must be a constructor function, not ".concat({}.toString.call(t));
      t.utils && (Ft.utils = I(I({}, Ft.utils), t.utils)), W.mount(t);
    });
  }, Ft.create = function (t, e) {
    return new Ft(t, e);
  };
  var zt,
    Gt,
    Ut,
    qt,
    Vt,
    Zt,
    $t = [],
    Qt = !(Ft.version = "1.15.2");
  function Jt() {
    $t.forEach(function (t) {
      clearInterval(t.pid);
    }), $t = [];
  }
  function te() {
    clearInterval(Zt);
  }
  var ee,
    ne = S(function (n, t, e, o) {
      if (t.scroll) {
        var i,
          r = (n.touches ? n.touches[0] : n).clientX,
          a = (n.touches ? n.touches[0] : n).clientY,
          l = t.scrollSensitivity,
          s = t.scrollSpeed,
          c = O(),
          u = !1;
        Gt !== e && (Gt = e, Jt(), zt = t.scroll, i = t.scrollFn, !0 === zt && (zt = M(e, !0)));
        var d = 0,
          h = zt;
        do {
          var f = h,
            p = X(f),
            g = p.top,
            m = p.bottom,
            v = p.left,
            b = p.right,
            y = p.width,
            w = p.height,
            E = void 0,
            D = void 0,
            S = f.scrollWidth,
            _ = f.scrollHeight,
            C = R(f),
            T = f.scrollLeft,
            p = f.scrollTop,
            D = f === c ? (E = y < S && ("auto" === C.overflowX || "scroll" === C.overflowX || "visible" === C.overflowX), w < _ && ("auto" === C.overflowY || "scroll" === C.overflowY || "visible" === C.overflowY)) : (E = y < S && ("auto" === C.overflowX || "scroll" === C.overflowX), w < _ && ("auto" === C.overflowY || "scroll" === C.overflowY)),
            T = E && (Math.abs(b - r) <= l && T + y < S) - (Math.abs(v - r) <= l && !!T),
            p = D && (Math.abs(m - a) <= l && p + w < _) - (Math.abs(g - a) <= l && !!p);
          if (!$t[d]) for (var x = 0; x <= d; x++) {
            $t[x] || ($t[x] = {});
          }
          $t[d].vx == T && $t[d].vy == p && $t[d].el === f || ($t[d].el = f, $t[d].vx = T, $t[d].vy = p, clearInterval($t[d].pid), 0 == T && 0 == p || (u = !0, $t[d].pid = setInterval(function () {
            o && 0 === this.layer && Ft.active._onTouchMove(Vt);
            var t = $t[this.layer].vy ? $t[this.layer].vy * s : 0,
              e = $t[this.layer].vx ? $t[this.layer].vx * s : 0;
            "function" == typeof i && "continue" !== i.call(Ft.dragged.parentNode[K], e, t, n, Vt, $t[this.layer].el) || H($t[this.layer].el, e, t);
          }.bind({
            layer: d
          }), 24))), d++;
        } while (t.bubbleScroll && h !== c && (h = M(h, !1)));
        Qt = u;
      }
    }, 30),
    c = function c(t) {
      var e = t.originalEvent,
        n = t.putSortable,
        o = t.dragEl,
        i = t.activeSortable,
        r = t.dispatchSortableEvent,
        a = t.hideGhostForTarget,
        t = t.unhideGhostForTarget;
      e && (i = n || i, a(), e = e.changedTouches && e.changedTouches.length ? e.changedTouches[0] : e, e = document.elementFromPoint(e.clientX, e.clientY), t(), i && !i.el.contains(e) && (r("spill"), this.onSpill({
        dragEl: o,
        putSortable: n
      })));
    };
  function oe() {}
  function ie() {}
  oe.prototype = {
    startIndex: null,
    dragStart: function dragStart(t) {
      t = t.oldDraggableIndex;
      this.startIndex = t;
    },
    onSpill: function onSpill(t) {
      var e = t.dragEl,
        n = t.putSortable;
      this.sortable.captureAnimationState(), n && n.captureAnimationState();
      t = B(this.sortable.el, this.startIndex, this.options);
      t ? this.sortable.el.insertBefore(e, t) : this.sortable.el.appendChild(e), this.sortable.animateAll(), n && n.animateAll();
    },
    drop: c
  }, a(oe, {
    pluginName: "revertOnSpill"
  }), ie.prototype = {
    onSpill: function onSpill(t) {
      var e = t.dragEl,
        t = t.putSortable || this.sortable;
      t.captureAnimationState(), e.parentNode && e.parentNode.removeChild(e), t.animateAll();
    },
    drop: c
  }, a(ie, {
    pluginName: "removeOnSpill"
  });
  var re,
    ae,
    le,
    se,
    ce,
    ue = [],
    de = [],
    he = !1,
    fe = !1,
    pe = !1;
  function ge(n, o) {
    de.forEach(function (t, e) {
      e = o.children[t.sortableIndex + (n ? Number(e) : 0)];
      e ? o.insertBefore(t, e) : o.appendChild(t);
    });
  }
  function me() {
    ue.forEach(function (t) {
      t !== le && t.parentNode && t.parentNode.removeChild(t);
    });
  }
  return Ft.mount(new function () {
    function t() {
      for (var t in this.defaults = {
        scroll: !0,
        forceAutoScrollFallback: !1,
        scrollSensitivity: 30,
        scrollSpeed: 10,
        bubbleScroll: !0
      }, this) {
        "_" === t.charAt(0) && "function" == typeof this[t] && (this[t] = this[t].bind(this));
      }
    }
    return t.prototype = {
      dragStarted: function dragStarted(t) {
        t = t.originalEvent;
        this.sortable.nativeDraggable ? h(document, "dragover", this._handleAutoScroll) : this.options.supportPointer ? h(document, "pointermove", this._handleFallbackAutoScroll) : t.touches ? h(document, "touchmove", this._handleFallbackAutoScroll) : h(document, "mousemove", this._handleFallbackAutoScroll);
      },
      dragOverCompleted: function dragOverCompleted(t) {
        t = t.originalEvent;
        this.options.dragOverBubble || t.rootEl || this._handleAutoScroll(t);
      },
      drop: function drop() {
        this.sortable.nativeDraggable ? f(document, "dragover", this._handleAutoScroll) : (f(document, "pointermove", this._handleFallbackAutoScroll), f(document, "touchmove", this._handleFallbackAutoScroll), f(document, "mousemove", this._handleFallbackAutoScroll)), te(), Jt(), clearTimeout(g), g = void 0;
      },
      nulling: function nulling() {
        Vt = Gt = zt = Qt = Zt = Ut = qt = null, $t.length = 0;
      },
      _handleFallbackAutoScroll: function _handleFallbackAutoScroll(t) {
        this._handleAutoScroll(t, !0);
      },
      _handleAutoScroll: function _handleAutoScroll(e, n) {
        var o,
          i = this,
          r = (e.touches ? e.touches[0] : e).clientX,
          a = (e.touches ? e.touches[0] : e).clientY,
          t = document.elementFromPoint(r, a);
        Vt = e, n || this.options.forceAutoScrollFallback || w || y || u ? (ne(e, this.options, t, n), o = M(t, !0), !Qt || Zt && r === Ut && a === qt || (Zt && te(), Zt = setInterval(function () {
          var t = M(document.elementFromPoint(r, a), !0);
          t !== o && (o = t, Jt()), ne(e, i.options, t, n);
        }, 10), Ut = r, qt = a)) : this.options.bubbleScroll && M(t, !0) !== O() ? ne(e, this.options, M(t, !1), !1) : Jt();
      }
    }, a(t, {
      pluginName: "scroll",
      initializeByDefault: !0
    });
  }()), Ft.mount(ie, oe), Ft.mount(new function () {
    function t() {
      this.defaults = {
        swapClass: "sortable-swap-highlight"
      };
    }
    return t.prototype = {
      dragStart: function dragStart(t) {
        t = t.dragEl;
        ee = t;
      },
      dragOverValid: function dragOverValid(t) {
        var e = t.completed,
          n = t.target,
          o = t.onMove,
          i = t.activeSortable,
          r = t.changed,
          a = t.cancel;
        i.options.swap && (t = this.sortable.el, i = this.options, n && n !== t && (t = ee, ee = !1 !== o(n) ? (k(n, i.swapClass, !0), n) : null, t && t !== ee && k(t, i.swapClass, !1)), r(), e(!0), a());
      },
      drop: function drop(t) {
        var e,
          n,
          o = t.activeSortable,
          i = t.putSortable,
          r = t.dragEl,
          a = i || this.sortable,
          l = this.options;
        ee && k(ee, l.swapClass, !1), ee && (l.swap || i && i.options.swap) && r !== ee && (a.captureAnimationState(), a !== o && o.captureAnimationState(), n = ee, t = (e = r).parentNode, l = n.parentNode, t && l && !t.isEqualNode(n) && !l.isEqualNode(e) && (i = j(e), r = j(n), t.isEqualNode(l) && i < r && r++, t.insertBefore(n, t.children[i]), l.insertBefore(e, l.children[r])), a.animateAll(), a !== o && o.animateAll());
      },
      nulling: function nulling() {
        ee = null;
      }
    }, a(t, {
      pluginName: "swap",
      eventProperties: function eventProperties() {
        return {
          swapItem: ee
        };
      }
    });
  }()), Ft.mount(new function () {
    function t(o) {
      for (var t in this) {
        "_" === t.charAt(0) && "function" == typeof this[t] && (this[t] = this[t].bind(this));
      }
      o.options.avoidImplicitDeselect || (o.options.supportPointer ? h(document, "pointerup", this._deselectMultiDrag) : (h(document, "mouseup", this._deselectMultiDrag), h(document, "touchend", this._deselectMultiDrag))), h(document, "keydown", this._checkKeyDown), h(document, "keyup", this._checkKeyUp), this.defaults = {
        selectedClass: "sortable-selected",
        multiDragKey: null,
        avoidImplicitDeselect: !1,
        setData: function setData(t, e) {
          var n = "";
          ue.length && ae === o ? ue.forEach(function (t, e) {
            n += (e ? ", " : "") + t.textContent;
          }) : n = e.textContent, t.setData("Text", n);
        }
      };
    }
    return t.prototype = {
      multiDragKeyDown: !1,
      isMultiDrag: !1,
      delayStartGlobal: function delayStartGlobal(t) {
        t = t.dragEl;
        le = t;
      },
      delayEnded: function delayEnded() {
        this.isMultiDrag = ~ue.indexOf(le);
      },
      setupClone: function setupClone(t) {
        var e = t.sortable,
          t = t.cancel;
        if (this.isMultiDrag) {
          for (var n = 0; n < ue.length; n++) {
            de.push(_(ue[n])), de[n].sortableIndex = ue[n].sortableIndex, de[n].draggable = !1, de[n].style["will-change"] = "", k(de[n], this.options.selectedClass, !1), ue[n] === le && k(de[n], this.options.chosenClass, !1);
          }
          e._hideClone(), t();
        }
      },
      clone: function clone(t) {
        var e = t.sortable,
          n = t.rootEl,
          o = t.dispatchSortableEvent,
          t = t.cancel;
        this.isMultiDrag && (this.options.removeCloneOnHide || ue.length && ae === e && (ge(!0, n), o("clone"), t()));
      },
      showClone: function showClone(t) {
        var e = t.cloneNowShown,
          n = t.rootEl,
          t = t.cancel;
        this.isMultiDrag && (ge(!1, n), de.forEach(function (t) {
          R(t, "display", "");
        }), e(), ce = !1, t());
      },
      hideClone: function hideClone(t) {
        var e = this,
          n = (t.sortable, t.cloneNowHidden),
          t = t.cancel;
        this.isMultiDrag && (de.forEach(function (t) {
          R(t, "display", "none"), e.options.removeCloneOnHide && t.parentNode && t.parentNode.removeChild(t);
        }), n(), ce = !0, t());
      },
      dragStartGlobal: function dragStartGlobal(t) {
        t.sortable;
        !this.isMultiDrag && ae && ae.multiDrag._deselectMultiDrag(), ue.forEach(function (t) {
          t.sortableIndex = j(t);
        }), ue = ue.sort(function (t, e) {
          return t.sortableIndex - e.sortableIndex;
        }), pe = !0;
      },
      dragStarted: function dragStarted(t) {
        var e,
          n = this,
          t = t.sortable;
        this.isMultiDrag && (this.options.sort && (t.captureAnimationState(), this.options.animation && (ue.forEach(function (t) {
          t !== le && R(t, "position", "absolute");
        }), e = X(le, !1, !0, !0), ue.forEach(function (t) {
          t !== le && C(t, e);
        }), he = fe = !0)), t.animateAll(function () {
          he = fe = !1, n.options.animation && ue.forEach(function (t) {
            T(t);
          }), n.options.sort && me();
        }));
      },
      dragOver: function dragOver(t) {
        var e = t.target,
          n = t.completed,
          t = t.cancel;
        fe && ~ue.indexOf(e) && (n(!1), t());
      },
      revert: function revert(t) {
        var n,
          o,
          e = t.fromSortable,
          i = t.rootEl,
          r = t.sortable,
          a = t.dragRect;
        1 < ue.length && (ue.forEach(function (t) {
          r.addAnimationState({
            target: t,
            rect: fe ? X(t) : a
          }), T(t), t.fromRect = a, e.removeAnimationState(t);
        }), fe = !1, n = !this.options.removeCloneOnHide, o = i, ue.forEach(function (t, e) {
          e = o.children[t.sortableIndex + (n ? Number(e) : 0)];
          e ? o.insertBefore(t, e) : o.appendChild(t);
        }));
      },
      dragOverCompleted: function dragOverCompleted(t) {
        var e,
          n = t.sortable,
          o = t.isOwner,
          i = t.insertion,
          r = t.activeSortable,
          a = t.parentEl,
          l = t.putSortable,
          t = this.options;
        i && (o && r._hideClone(), he = !1, t.animation && 1 < ue.length && (fe || !o && !r.options.sort && !l) && (e = X(le, !1, !0, !0), ue.forEach(function (t) {
          t !== le && (C(t, e), a.appendChild(t));
        }), fe = !0), o || (fe || me(), 1 < ue.length ? (o = ce, r._showClone(n), r.options.animation && !ce && o && de.forEach(function (t) {
          r.addAnimationState({
            target: t,
            rect: se
          }), t.fromRect = se, t.thisAnimationDuration = null;
        })) : r._showClone(n)));
      },
      dragOverAnimationCapture: function dragOverAnimationCapture(t) {
        var e = t.dragRect,
          n = t.isOwner,
          t = t.activeSortable;
        ue.forEach(function (t) {
          t.thisAnimationDuration = null;
        }), t.options.animation && !n && t.multiDrag.isMultiDrag && (se = a({}, e), e = v(le, !0), se.top -= e.f, se.left -= e.e);
      },
      dragOverAnimationComplete: function dragOverAnimationComplete() {
        fe && (fe = !1, me());
      },
      drop: function drop(t) {
        var e = t.originalEvent,
          n = t.rootEl,
          o = t.parentEl,
          i = t.sortable,
          r = t.dispatchSortableEvent,
          a = t.oldIndex,
          l = t.putSortable,
          s = l || this.sortable;
        if (e) {
          var c,
            u,
            d,
            h = this.options,
            f = o.children;
          if (!pe) if (h.multiDragKey && !this.multiDragKeyDown && this._deselectMultiDrag(), k(le, h.selectedClass, !~ue.indexOf(le)), ~ue.indexOf(le)) ue.splice(ue.indexOf(le), 1), re = null, z({
            sortable: i,
            rootEl: n,
            name: "deselect",
            targetEl: le,
            originalEvent: e
          });else {
            if (ue.push(le), z({
              sortable: i,
              rootEl: n,
              name: "select",
              targetEl: le,
              originalEvent: e
            }), e.shiftKey && re && i.el.contains(re)) {
              var p = j(re),
                t = j(le);
              if (~p && ~t && p !== t) for (var g, m = p < t ? (g = p, t) : (g = t, p + 1); g < m; g++) {
                ~ue.indexOf(f[g]) || (k(f[g], h.selectedClass, !0), ue.push(f[g]), z({
                  sortable: i,
                  rootEl: n,
                  name: "select",
                  targetEl: f[g],
                  originalEvent: e
                }));
              }
            } else re = le;
            ae = s;
          }
          pe && this.isMultiDrag && (fe = !1, (o[K].options.sort || o !== n) && 1 < ue.length && (c = X(le), u = j(le, ":not(." + this.options.selectedClass + ")"), !he && h.animation && (le.thisAnimationDuration = null), s.captureAnimationState(), he || (h.animation && (le.fromRect = c, ue.forEach(function (t) {
            var e;
            t.thisAnimationDuration = null, t !== le && (e = fe ? X(t) : c, t.fromRect = e, s.addAnimationState({
              target: t,
              rect: e
            }));
          })), me(), ue.forEach(function (t) {
            f[u] ? o.insertBefore(t, f[u]) : o.appendChild(t), u++;
          }), a === j(le) && (d = !1, ue.forEach(function (t) {
            t.sortableIndex !== j(t) && (d = !0);
          }), d && (r("update"), r("sort")))), ue.forEach(function (t) {
            T(t);
          }), s.animateAll()), ae = s), (n === o || l && "clone" !== l.lastPutMode) && de.forEach(function (t) {
            t.parentNode && t.parentNode.removeChild(t);
          });
        }
      },
      nullingGlobal: function nullingGlobal() {
        this.isMultiDrag = pe = !1, de.length = 0;
      },
      destroyGlobal: function destroyGlobal() {
        this._deselectMultiDrag(), f(document, "pointerup", this._deselectMultiDrag), f(document, "mouseup", this._deselectMultiDrag), f(document, "touchend", this._deselectMultiDrag), f(document, "keydown", this._checkKeyDown), f(document, "keyup", this._checkKeyUp);
      },
      _deselectMultiDrag: function _deselectMultiDrag(t) {
        if (!(void 0 !== pe && pe || ae !== this.sortable || t && P(t.target, this.options.draggable, this.sortable.el, !1) || t && 0 !== t.button)) for (; ue.length;) {
          var e = ue[0];
          k(e, this.options.selectedClass, !1), ue.shift(), z({
            sortable: this.sortable,
            rootEl: this.sortable.el,
            name: "deselect",
            targetEl: e,
            originalEvent: t
          });
        }
      },
      _checkKeyDown: function _checkKeyDown(t) {
        t.key === this.options.multiDragKey && (this.multiDragKeyDown = !0);
      },
      _checkKeyUp: function _checkKeyUp(t) {
        t.key === this.options.multiDragKey && (this.multiDragKeyDown = !1);
      }
    }, a(t, {
      pluginName: "multiDrag",
      utils: {
        select: function select(t) {
          var e = t.parentNode[K];
          e && e.options.multiDrag && !~ue.indexOf(t) && (ae && ae !== e && (ae.multiDrag._deselectMultiDrag(), ae = e), k(t, e.options.selectedClass, !0), ue.push(t));
        },
        deselect: function deselect(t) {
          var e = t.parentNode[K],
            n = ue.indexOf(t);
          e && e.options.multiDrag && ~n && (k(t, e.options.selectedClass, !1), ue.splice(n, 1));
        }
      },
      eventProperties: function eventProperties() {
        var n = this,
          o = [],
          i = [];
        return ue.forEach(function (t) {
          var e;
          o.push({
            multiDragElement: t,
            index: t.sortableIndex
          }), e = fe && t !== le ? -1 : fe ? j(t, ":not(." + n.options.selectedClass + ")") : j(t), i.push({
            multiDragElement: t,
            index: e
          });
        }), {
          items: r(ue),
          clones: [].concat(de),
          oldIndicies: o,
          newIndicies: i
        };
      },
      optionListeners: {
        multiDragKey: function multiDragKey(t) {
          return "ctrl" === (t = t.toLowerCase()) ? t = "Control" : 1 < t.length && (t = t.charAt(0).toUpperCase() + t.substr(1)), t;
        }
      }
    });
  }()), Ft;
});
/*! jQuery UI - v1.13.2 - 2023-02-04
* http://jqueryui.com
* Includes: widget.js, position.js, data.js, keycode.js, scroll-parent.js, unique-id.js, widgets/draggable.js, widgets/sortable.js, widgets/autocomplete.js, widgets/menu.js, widgets/mouse.js
* Copyright jQuery Foundation and other contributors; Licensed MIT */

!function (t) {
  "use strict";

  "function" == typeof define && define.amd ? define(["jquery"], t) : t(jQuery);
}(function (w) {
  "use strict";

  w.ui = w.ui || {};
  w.ui.version = "1.13.2";
  var o,
    i = 0,
    r = Array.prototype.hasOwnProperty,
    h = Array.prototype.slice;
  w.cleanData = (o = w.cleanData, function (t) {
    for (var e, i, s = 0; null != (i = t[s]); s++) {
      (e = w._data(i, "events")) && e.remove && w(i).triggerHandler("remove");
    }
    o(t);
  }), w.widget = function (t, i, e) {
    var s,
      o,
      n,
      r = {},
      h = t.split(".")[0],
      a = h + "-" + (t = t.split(".")[1]);
    return e || (e = i, i = w.Widget), Array.isArray(e) && (e = w.extend.apply(null, [{}].concat(e))), w.expr.pseudos[a.toLowerCase()] = function (t) {
      return !!w.data(t, a);
    }, w[h] = w[h] || {}, s = w[h][t], o = w[h][t] = function (t, e) {
      if (!this || !this._createWidget) return new o(t, e);
      arguments.length && this._createWidget(t, e);
    }, w.extend(o, s, {
      version: e.version,
      _proto: w.extend({}, e),
      _childConstructors: []
    }), (n = new i()).options = w.widget.extend({}, n.options), w.each(e, function (e, s) {
      function o() {
        return i.prototype[e].apply(this, arguments);
      }
      function n(t) {
        return i.prototype[e].apply(this, t);
      }
      r[e] = "function" == typeof s ? function () {
        var t,
          e = this._super,
          i = this._superApply;
        return this._super = o, this._superApply = n, t = s.apply(this, arguments), this._super = e, this._superApply = i, t;
      } : s;
    }), o.prototype = w.widget.extend(n, {
      widgetEventPrefix: s && n.widgetEventPrefix || t
    }, r, {
      constructor: o,
      namespace: h,
      widgetName: t,
      widgetFullName: a
    }), s ? (w.each(s._childConstructors, function (t, e) {
      var i = e.prototype;
      w.widget(i.namespace + "." + i.widgetName, o, e._proto);
    }), delete s._childConstructors) : i._childConstructors.push(o), w.widget.bridge(t, o), o;
  }, w.widget.extend = function (t) {
    for (var e, i, s = h.call(arguments, 1), o = 0, n = s.length; o < n; o++) {
      for (e in s[o]) {
        i = s[o][e], r.call(s[o], e) && void 0 !== i && (w.isPlainObject(i) ? t[e] = w.isPlainObject(t[e]) ? w.widget.extend({}, t[e], i) : w.widget.extend({}, i) : t[e] = i);
      }
    }
    return t;
  }, w.widget.bridge = function (n, e) {
    var r = e.prototype.widgetFullName || n;
    w.fn[n] = function (i) {
      var t = "string" == typeof i,
        s = h.call(arguments, 1),
        o = this;
      return t ? this.length || "instance" !== i ? this.each(function () {
        var t,
          e = w.data(this, r);
        return "instance" === i ? (o = e, !1) : e ? "function" != typeof e[i] || "_" === i.charAt(0) ? w.error("no such method '" + i + "' for " + n + " widget instance") : (t = e[i].apply(e, s)) !== e && void 0 !== t ? (o = t && t.jquery ? o.pushStack(t.get()) : t, !1) : void 0 : w.error("cannot call methods on " + n + " prior to initialization; attempted to call method '" + i + "'");
      }) : o = void 0 : (s.length && (i = w.widget.extend.apply(null, [i].concat(s))), this.each(function () {
        var t = w.data(this, r);
        t ? (t.option(i || {}), t._init && t._init()) : w.data(this, r, new e(i, this));
      })), o;
    };
  }, w.Widget = function () {}, w.Widget._childConstructors = [], w.Widget.prototype = {
    widgetName: "widget",
    widgetEventPrefix: "",
    defaultElement: "<div>",
    options: {
      classes: {},
      disabled: !1,
      create: null
    },
    _createWidget: function _createWidget(t, e) {
      e = w(e || this.defaultElement || this)[0], this.element = w(e), this.uuid = i++, this.eventNamespace = "." + this.widgetName + this.uuid, this.bindings = w(), this.hoverable = w(), this.focusable = w(), this.classesElementLookup = {}, e !== this && (w.data(e, this.widgetFullName, this), this._on(!0, this.element, {
        remove: function remove(t) {
          t.target === e && this.destroy();
        }
      }), this.document = w(e.style ? e.ownerDocument : e.document || e), this.window = w(this.document[0].defaultView || this.document[0].parentWindow)), this.options = w.widget.extend({}, this.options, this._getCreateOptions(), t), this._create(), this.options.disabled && this._setOptionDisabled(this.options.disabled), this._trigger("create", null, this._getCreateEventData()), this._init();
    },
    _getCreateOptions: function _getCreateOptions() {
      return {};
    },
    _getCreateEventData: w.noop,
    _create: w.noop,
    _init: w.noop,
    destroy: function destroy() {
      var i = this;
      this._destroy(), w.each(this.classesElementLookup, function (t, e) {
        i._removeClass(e, t);
      }), this.element.off(this.eventNamespace).removeData(this.widgetFullName), this.widget().off(this.eventNamespace).removeAttr("aria-disabled"), this.bindings.off(this.eventNamespace);
    },
    _destroy: w.noop,
    widget: function widget() {
      return this.element;
    },
    option: function option(t, e) {
      var i,
        s,
        o,
        n = t;
      if (0 === arguments.length) return w.widget.extend({}, this.options);
      if ("string" == typeof t) if (n = {}, t = (i = t.split(".")).shift(), i.length) {
        for (s = n[t] = w.widget.extend({}, this.options[t]), o = 0; o < i.length - 1; o++) {
          s[i[o]] = s[i[o]] || {}, s = s[i[o]];
        }
        if (t = i.pop(), 1 === arguments.length) return void 0 === s[t] ? null : s[t];
        s[t] = e;
      } else {
        if (1 === arguments.length) return void 0 === this.options[t] ? null : this.options[t];
        n[t] = e;
      }
      return this._setOptions(n), this;
    },
    _setOptions: function _setOptions(t) {
      for (var e in t) {
        this._setOption(e, t[e]);
      }
      return this;
    },
    _setOption: function _setOption(t, e) {
      return "classes" === t && this._setOptionClasses(e), this.options[t] = e, "disabled" === t && this._setOptionDisabled(e), this;
    },
    _setOptionClasses: function _setOptionClasses(t) {
      var e, i, s;
      for (e in t) {
        s = this.classesElementLookup[e], t[e] !== this.options.classes[e] && s && s.length && (i = w(s.get()), this._removeClass(s, e), i.addClass(this._classes({
          element: i,
          keys: e,
          classes: t,
          add: !0
        })));
      }
    },
    _setOptionDisabled: function _setOptionDisabled(t) {
      this._toggleClass(this.widget(), this.widgetFullName + "-disabled", null, !!t), t && (this._removeClass(this.hoverable, null, "ui-state-hover"), this._removeClass(this.focusable, null, "ui-state-focus"));
    },
    enable: function enable() {
      return this._setOptions({
        disabled: !1
      });
    },
    disable: function disable() {
      return this._setOptions({
        disabled: !0
      });
    },
    _classes: function _classes(o) {
      var n = [],
        r = this;
      function t(t, e) {
        for (var i, s = 0; s < t.length; s++) {
          i = r.classesElementLookup[t[s]] || w(), i = o.add ? (function () {
            var i = [];
            o.element.each(function (t, e) {
              w.map(r.classesElementLookup, function (t) {
                return t;
              }).some(function (t) {
                return t.is(e);
              }) || i.push(e);
            }), r._on(w(i), {
              remove: "_untrackClassesElement"
            });
          }(), w(w.uniqueSort(i.get().concat(o.element.get())))) : w(i.not(o.element).get()), r.classesElementLookup[t[s]] = i, n.push(t[s]), e && o.classes[t[s]] && n.push(o.classes[t[s]]);
        }
      }
      return (o = w.extend({
        element: this.element,
        classes: this.options.classes || {}
      }, o)).keys && t(o.keys.match(/\S+/g) || [], !0), o.extra && t(o.extra.match(/\S+/g) || []), n.join(" ");
    },
    _untrackClassesElement: function _untrackClassesElement(i) {
      var s = this;
      w.each(s.classesElementLookup, function (t, e) {
        -1 !== w.inArray(i.target, e) && (s.classesElementLookup[t] = w(e.not(i.target).get()));
      }), this._off(w(i.target));
    },
    _removeClass: function _removeClass(t, e, i) {
      return this._toggleClass(t, e, i, !1);
    },
    _addClass: function _addClass(t, e, i) {
      return this._toggleClass(t, e, i, !0);
    },
    _toggleClass: function _toggleClass(t, e, i, s) {
      var o = "string" == typeof t || null === t,
        i = {
          extra: o ? e : i,
          keys: o ? t : e,
          element: o ? this.element : t,
          add: s = "boolean" == typeof s ? s : i
        };
      return i.element.toggleClass(this._classes(i), s), this;
    },
    _on: function _on(o, n, t) {
      var r,
        h = this;
      "boolean" != typeof o && (t = n, n = o, o = !1), t ? (n = r = w(n), this.bindings = this.bindings.add(n)) : (t = n, n = this.element, r = this.widget()), w.each(t, function (t, e) {
        function i() {
          if (o || !0 !== h.options.disabled && !w(this).hasClass("ui-state-disabled")) return ("string" == typeof e ? h[e] : e).apply(h, arguments);
        }
        "string" != typeof e && (i.guid = e.guid = e.guid || i.guid || w.guid++);
        var s = t.match(/^([\w:-]*)\s*(.*)$/),
          t = s[1] + h.eventNamespace,
          s = s[2];
        s ? r.on(t, s, i) : n.on(t, i);
      });
    },
    _off: function _off(t, e) {
      e = (e || "").split(" ").join(this.eventNamespace + " ") + this.eventNamespace, t.off(e), this.bindings = w(this.bindings.not(t).get()), this.focusable = w(this.focusable.not(t).get()), this.hoverable = w(this.hoverable.not(t).get());
    },
    _delay: function _delay(t, e) {
      var i = this;
      return setTimeout(function () {
        return ("string" == typeof t ? i[t] : t).apply(i, arguments);
      }, e || 0);
    },
    _hoverable: function _hoverable(t) {
      this.hoverable = this.hoverable.add(t), this._on(t, {
        mouseenter: function mouseenter(t) {
          this._addClass(w(t.currentTarget), null, "ui-state-hover");
        },
        mouseleave: function mouseleave(t) {
          this._removeClass(w(t.currentTarget), null, "ui-state-hover");
        }
      });
    },
    _focusable: function _focusable(t) {
      this.focusable = this.focusable.add(t), this._on(t, {
        focusin: function focusin(t) {
          this._addClass(w(t.currentTarget), null, "ui-state-focus");
        },
        focusout: function focusout(t) {
          this._removeClass(w(t.currentTarget), null, "ui-state-focus");
        }
      });
    },
    _trigger: function _trigger(t, e, i) {
      var s,
        o,
        n = this.options[t];
      if (i = i || {}, (e = w.Event(e)).type = (t === this.widgetEventPrefix ? t : this.widgetEventPrefix + t).toLowerCase(), e.target = this.element[0], o = e.originalEvent) for (s in o) {
        s in e || (e[s] = o[s]);
      }
      return this.element.trigger(e, i), !("function" == typeof n && !1 === n.apply(this.element[0], [e].concat(i)) || e.isDefaultPrevented());
    }
  }, w.each({
    show: "fadeIn",
    hide: "fadeOut"
  }, function (n, r) {
    w.Widget.prototype["_" + n] = function (e, t, i) {
      var s,
        o = (t = "string" == typeof t ? {
          effect: t
        } : t) ? !0 !== t && "number" != typeof t && t.effect || r : n;
      "number" == typeof (t = t || {}) ? t = {
        duration: t
      } : !0 === t && (t = {}), s = !w.isEmptyObject(t), t.complete = i, t.delay && e.delay(t.delay), s && w.effects && w.effects.effect[o] ? e[n](t) : o !== n && e[o] ? e[o](t.duration, t.easing, i) : e.queue(function (t) {
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
    scrollbarWidth: function scrollbarWidth() {
      if (void 0 !== s) return s;
      var t,
        e = w("<div style='display:block;position:absolute;width:200px;height:200px;overflow:hidden;'><div style='height:300px;width:auto;'></div></div>"),
        i = e.children()[0];
      return w("body").append(e), t = i.offsetWidth, e.css("overflow", "scroll"), t === (i = i.offsetWidth) && (i = e[0].clientWidth), e.remove(), s = t - i;
    },
    getScrollInfo: function getScrollInfo(t) {
      var e = t.isWindow || t.isDocument ? "" : t.element.css("overflow-x"),
        i = t.isWindow || t.isDocument ? "" : t.element.css("overflow-y"),
        e = "scroll" === e || "auto" === e && t.width < t.element[0].scrollWidth;
      return {
        width: "scroll" === i || "auto" === i && t.height < t.element[0].scrollHeight ? w.position.scrollbarWidth() : 0,
        height: e ? w.position.scrollbarWidth() : 0
      };
    },
    getWithinInfo: function getWithinInfo(t) {
      var e = w(t || window),
        i = H(e[0]),
        s = !!e[0] && 9 === e[0].nodeType;
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
  }, w.fn.position = function (u) {
    if (!u || !u.of) return C.apply(this, arguments);
    var p,
      f,
      d,
      m,
      g,
      t,
      v = "string" == typeof (u = w.extend({}, u)).of ? w(document).find(u.of) : w(u.of),
      _ = w.position.getWithinInfo(u.within),
      y = w.position.getScrollInfo(_),
      b = (u.collision || "flip").split(" "),
      P = {},
      e = 9 === (t = (e = v)[0]).nodeType ? {
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
    return v[0].preventDefault && (u.at = "left top"), f = e.width, d = e.height, g = w.extend({}, m = e.offset), w.each(["my", "at"], function () {
      var t,
        e,
        i = (u[this] || "").split(" ");
      (i = 1 === i.length ? n.test(i[0]) ? i.concat(["center"]) : a.test(i[0]) ? ["center"].concat(i) : ["center", "center"] : i)[0] = n.test(i[0]) ? i[0] : "center", i[1] = a.test(i[1]) ? i[1] : "center", t = l.exec(i[0]), e = l.exec(i[1]), P[this] = [t ? t[0] : 0, e ? e[0] : 0], u[this] = [c.exec(i[0])[0], c.exec(i[1])[0]];
    }), 1 === b.length && (b[1] = b[0]), "right" === u.at[0] ? g.left += f : "center" === u.at[0] && (g.left += f / 2), "bottom" === u.at[1] ? g.top += d : "center" === u.at[1] && (g.top += d / 2), p = T(P.at, f, d), g.left += p[0], g.top += p[1], this.each(function () {
      var i,
        t,
        r = w(this),
        h = r.outerWidth(),
        a = r.outerHeight(),
        e = k(this, "marginLeft"),
        s = k(this, "marginTop"),
        o = h + e + k(this, "marginRight") + y.width,
        n = a + s + k(this, "marginBottom") + y.height,
        l = w.extend({}, g),
        c = T(P.my, r.outerWidth(), r.outerHeight());
      "right" === u.my[0] ? l.left -= h : "center" === u.my[0] && (l.left -= h / 2), "bottom" === u.my[1] ? l.top -= a : "center" === u.my[1] && (l.top -= a / 2), l.left += c[0], l.top += c[1], i = {
        marginLeft: e,
        marginTop: s
      }, w.each(["left", "top"], function (t, e) {
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
      }), u.using && (t = function t(_t2) {
        var e = m.left - l.left,
          i = e + f - h,
          s = m.top - l.top,
          o = s + d - a,
          n = {
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
        f < h && I(e + i) < f && (n.horizontal = "center"), d < a && I(s + o) < d && (n.vertical = "middle"), x(I(e), I(i)) > x(I(s), I(o)) ? n.important = "horizontal" : n.important = "vertical", u.using.call(this, _t2, n);
      }), r.offset(w.extend(l, {
        using: t
      }));
    });
  }, w.ui.position = {
    fit: {
      left: function left(t, e) {
        var i = e.within,
          s = i.isWindow ? i.scrollLeft : i.offset.left,
          o = i.width,
          n = t.left - e.collisionPosition.marginLeft,
          r = s - n,
          h = n + e.collisionWidth - o - s;
        e.collisionWidth > o ? 0 < r && h <= 0 ? (i = t.left + r + e.collisionWidth - o - s, t.left += r - i) : t.left = !(0 < h && r <= 0) && h < r ? s + o - e.collisionWidth : s : 0 < r ? t.left += r : 0 < h ? t.left -= h : t.left = x(t.left - n, t.left);
      },
      top: function top(t, e) {
        var i = e.within,
          s = i.isWindow ? i.scrollTop : i.offset.top,
          o = e.within.height,
          n = t.top - e.collisionPosition.marginTop,
          r = s - n,
          h = n + e.collisionHeight - o - s;
        e.collisionHeight > o ? 0 < r && h <= 0 ? (i = t.top + r + e.collisionHeight - o - s, t.top += r - i) : t.top = !(0 < h && r <= 0) && h < r ? s + o - e.collisionHeight : s : 0 < r ? t.top += r : 0 < h ? t.top -= h : t.top = x(t.top - n, t.top);
      }
    },
    flip: {
      left: function left(t, e) {
        var i = e.within,
          s = i.offset.left + i.scrollLeft,
          o = i.width,
          n = i.isWindow ? i.scrollLeft : i.offset.left,
          r = t.left - e.collisionPosition.marginLeft,
          h = r - n,
          a = r + e.collisionWidth - o - n,
          l = "left" === e.my[0] ? -e.elemWidth : "right" === e.my[0] ? e.elemWidth : 0,
          i = "left" === e.at[0] ? e.targetWidth : "right" === e.at[0] ? -e.targetWidth : 0,
          r = -2 * e.offset[0];
        h < 0 ? ((s = t.left + l + i + r + e.collisionWidth - o - s) < 0 || s < I(h)) && (t.left += l + i + r) : 0 < a && (0 < (n = t.left - e.collisionPosition.marginLeft + l + i + r - n) || I(n) < a) && (t.left += l + i + r);
      },
      top: function top(t, e) {
        var i = e.within,
          s = i.offset.top + i.scrollTop,
          o = i.height,
          n = i.isWindow ? i.scrollTop : i.offset.top,
          r = t.top - e.collisionPosition.marginTop,
          h = r - n,
          a = r + e.collisionHeight - o - n,
          l = "top" === e.my[1] ? -e.elemHeight : "bottom" === e.my[1] ? e.elemHeight : 0,
          i = "top" === e.at[1] ? e.targetHeight : "bottom" === e.at[1] ? -e.targetHeight : 0,
          r = -2 * e.offset[1];
        h < 0 ? ((s = t.top + l + i + r + e.collisionHeight - o - s) < 0 || s < I(h)) && (t.top += l + i + r) : 0 < a && (0 < (n = t.top - e.collisionPosition.marginTop + l + i + r - n) || I(n) < a) && (t.top += l + i + r);
      }
    },
    flipfit: {
      left: function left() {
        w.ui.position.flip.left.apply(this, arguments), w.ui.position.fit.left.apply(this, arguments);
      },
      top: function top() {
        w.ui.position.flip.top.apply(this, arguments), w.ui.position.fit.top.apply(this, arguments);
      }
    }
  };
  w.ui.position, w.extend(w.expr.pseudos, {
    data: w.expr.createPseudo ? w.expr.createPseudo(function (e) {
      return function (t) {
        return !!w.data(t, e);
      };
    }) : function (t, e, i) {
      return !!w.data(t, i[3]);
    }
  }), w.ui.keyCode = {
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
  }, w.fn.scrollParent = function (t) {
    var e = this.css("position"),
      i = "absolute" === e,
      s = t ? /(auto|scroll|hidden)/ : /(auto|scroll)/,
      t = this.parents().filter(function () {
        var t = w(this);
        return (!i || "static" !== t.css("position")) && s.test(t.css("overflow") + t.css("overflow-y") + t.css("overflow-x"));
      }).eq(0);
    return "fixed" !== e && t.length ? t : w(this[0].ownerDocument || document);
  }, w.fn.extend({
    uniqueId: (t = 0, function () {
      return this.each(function () {
        this.id || (this.id = "ui-id-" + ++t);
      });
    }),
    removeUniqueId: function removeUniqueId() {
      return this.each(function () {
        /^ui-id-\d+$/.test(this.id) && w(this).removeAttr("id");
      });
    }
  }), w.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase());
  var t,
    p = !1;
  w(document).on("mouseup", function () {
    p = !1;
  });
  w.widget("ui.mouse", {
    version: "1.13.2",
    options: {
      cancel: "input, textarea, button, select, option",
      distance: 1,
      delay: 0
    },
    _mouseInit: function _mouseInit() {
      var e = this;
      this.element.on("mousedown." + this.widgetName, function (t) {
        return e._mouseDown(t);
      }).on("click." + this.widgetName, function (t) {
        if (!0 === w.data(t.target, e.widgetName + ".preventClickEvent")) return w.removeData(t.target, e.widgetName + ".preventClickEvent"), t.stopImmediatePropagation(), !1;
      }), this.started = !1;
    },
    _mouseDestroy: function _mouseDestroy() {
      this.element.off("." + this.widgetName), this._mouseMoveDelegate && this.document.off("mousemove." + this.widgetName, this._mouseMoveDelegate).off("mouseup." + this.widgetName, this._mouseUpDelegate);
    },
    _mouseDown: function _mouseDown(t) {
      if (!p) {
        this._mouseMoved = !1, this._mouseStarted && this._mouseUp(t), this._mouseDownEvent = t;
        var e = this,
          i = 1 === t.which,
          s = !("string" != typeof this.options.cancel || !t.target.nodeName) && w(t.target).closest(this.options.cancel).length;
        return i && !s && this._mouseCapture(t) ? (this.mouseDelayMet = !this.options.delay, this.mouseDelayMet || (this._mouseDelayTimer = setTimeout(function () {
          e.mouseDelayMet = !0;
        }, this.options.delay)), this._mouseDistanceMet(t) && this._mouseDelayMet(t) && (this._mouseStarted = !1 !== this._mouseStart(t), !this._mouseStarted) ? (t.preventDefault(), !0) : (!0 === w.data(t.target, this.widgetName + ".preventClickEvent") && w.removeData(t.target, this.widgetName + ".preventClickEvent"), this._mouseMoveDelegate = function (t) {
          return e._mouseMove(t);
        }, this._mouseUpDelegate = function (t) {
          return e._mouseUp(t);
        }, this.document.on("mousemove." + this.widgetName, this._mouseMoveDelegate).on("mouseup." + this.widgetName, this._mouseUpDelegate), t.preventDefault(), p = !0)) : !0;
      }
    },
    _mouseMove: function _mouseMove(t) {
      if (this._mouseMoved) {
        if (w.ui.ie && (!document.documentMode || document.documentMode < 9) && !t.button) return this._mouseUp(t);
        if (!t.which) if (t.originalEvent.altKey || t.originalEvent.ctrlKey || t.originalEvent.metaKey || t.originalEvent.shiftKey) this.ignoreMissingWhich = !0;else if (!this.ignoreMissingWhich) return this._mouseUp(t);
      }
      return (t.which || t.button) && (this._mouseMoved = !0), this._mouseStarted ? (this._mouseDrag(t), t.preventDefault()) : (this._mouseDistanceMet(t) && this._mouseDelayMet(t) && (this._mouseStarted = !1 !== this._mouseStart(this._mouseDownEvent, t), this._mouseStarted ? this._mouseDrag(t) : this._mouseUp(t)), !this._mouseStarted);
    },
    _mouseUp: function _mouseUp(t) {
      this.document.off("mousemove." + this.widgetName, this._mouseMoveDelegate).off("mouseup." + this.widgetName, this._mouseUpDelegate), this._mouseStarted && (this._mouseStarted = !1, t.target === this._mouseDownEvent.target && w.data(t.target, this.widgetName + ".preventClickEvent", !0), this._mouseStop(t)), this._mouseDelayTimer && (clearTimeout(this._mouseDelayTimer), delete this._mouseDelayTimer), this.ignoreMissingWhich = !1, p = !1, t.preventDefault();
    },
    _mouseDistanceMet: function _mouseDistanceMet(t) {
      return Math.max(Math.abs(this._mouseDownEvent.pageX - t.pageX), Math.abs(this._mouseDownEvent.pageY - t.pageY)) >= this.options.distance;
    },
    _mouseDelayMet: function _mouseDelayMet() {
      return this.mouseDelayMet;
    },
    _mouseStart: function _mouseStart() {},
    _mouseDrag: function _mouseDrag() {},
    _mouseStop: function _mouseStop() {},
    _mouseCapture: function _mouseCapture() {
      return !0;
    }
  }), w.ui.plugin = {
    add: function add(t, e, i) {
      var s,
        o = w.ui[t].prototype;
      for (s in i) {
        o.plugins[s] = o.plugins[s] || [], o.plugins[s].push([e, i[s]]);
      }
    },
    call: function call(t, e, i, s) {
      var o,
        n = t.plugins[e];
      if (n && (s || t.element[0].parentNode && 11 !== t.element[0].parentNode.nodeType)) for (o = 0; o < n.length; o++) {
        t.options[n[o][0]] && n[o][1].apply(t.element, i);
      }
    }
  }, w.ui.safeActiveElement = function (e) {
    var i;
    try {
      i = e.activeElement;
    } catch (t) {
      i = e.body;
    }
    return i = !(i = i || e.body).nodeName ? e.body : i;
  }, w.ui.safeBlur = function (t) {
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
    _create: function _create() {
      "original" === this.options.helper && this._setPositionRelative(), this.options.addClasses && this._addClass("ui-draggable"), this._setHandleClassName(), this._mouseInit();
    },
    _setOption: function _setOption(t, e) {
      this._super(t, e), "handle" === t && (this._removeHandleClassName(), this._setHandleClassName());
    },
    _destroy: function _destroy() {
      (this.helper || this.element).is(".ui-draggable-dragging") ? this.destroyOnClear = !0 : (this._removeHandleClassName(), this._mouseDestroy());
    },
    _mouseCapture: function _mouseCapture(t) {
      var e = this.options;
      return !(this.helper || e.disabled || 0 < w(t.target).closest(".ui-resizable-handle").length) && (this.handle = this._getHandle(t), !!this.handle && (this._blurActiveElement(t), this._blockFrames(!0 === e.iframeFix ? "iframe" : e.iframeFix), !0));
    },
    _blockFrames: function _blockFrames(t) {
      this.iframeBlocks = this.document.find(t).map(function () {
        var t = w(this);
        return w("<div>").css("position", "absolute").appendTo(t.parent()).outerWidth(t.outerWidth()).outerHeight(t.outerHeight()).offset(t.offset())[0];
      });
    },
    _unblockFrames: function _unblockFrames() {
      this.iframeBlocks && (this.iframeBlocks.remove(), delete this.iframeBlocks);
    },
    _blurActiveElement: function _blurActiveElement(t) {
      var e = w.ui.safeActiveElement(this.document[0]);
      w(t.target).closest(e).length || w.ui.safeBlur(e);
    },
    _mouseStart: function _mouseStart(t) {
      var e = this.options;
      return this.helper = this._createHelper(t), this._addClass(this.helper, "ui-draggable-dragging"), this._cacheHelperProportions(), w.ui.ddmanager && (w.ui.ddmanager.current = this), this._cacheMargins(), this.cssPosition = this.helper.css("position"), this.scrollParent = this.helper.scrollParent(!0), this.offsetParent = this.helper.offsetParent(), this.hasFixedAncestor = 0 < this.helper.parents().filter(function () {
        return "fixed" === w(this).css("position");
      }).length, this.positionAbs = this.element.offset(), this._refreshOffsets(t), this.originalPosition = this.position = this._generatePosition(t, !1), this.originalPageX = t.pageX, this.originalPageY = t.pageY, e.cursorAt && this._adjustOffsetFromHelper(e.cursorAt), this._setContainment(), !1 === this._trigger("start", t) ? (this._clear(), !1) : (this._cacheHelperProportions(), w.ui.ddmanager && !e.dropBehaviour && w.ui.ddmanager.prepareOffsets(this, t), this._mouseDrag(t, !0), w.ui.ddmanager && w.ui.ddmanager.dragStart(this, t), !0);
    },
    _refreshOffsets: function _refreshOffsets(t) {
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
    _mouseDrag: function _mouseDrag(t, e) {
      if (this.hasFixedAncestor && (this.offset.parent = this._getParentOffset()), this.position = this._generatePosition(t, !0), this.positionAbs = this._convertPositionTo("absolute"), !e) {
        e = this._uiHash();
        if (!1 === this._trigger("drag", t, e)) return this._mouseUp(new w.Event("mouseup", t)), !1;
        this.position = e.position;
      }
      return this.helper[0].style.left = this.position.left + "px", this.helper[0].style.top = this.position.top + "px", w.ui.ddmanager && w.ui.ddmanager.drag(this, t), !1;
    },
    _mouseStop: function _mouseStop(t) {
      var e = this,
        i = !1;
      return w.ui.ddmanager && !this.options.dropBehaviour && (i = w.ui.ddmanager.drop(this, t)), this.dropped && (i = this.dropped, this.dropped = !1), "invalid" === this.options.revert && !i || "valid" === this.options.revert && i || !0 === this.options.revert || "function" == typeof this.options.revert && this.options.revert.call(this.element, i) ? w(this.helper).animate(this.originalPosition, parseInt(this.options.revertDuration, 10), function () {
        !1 !== e._trigger("stop", t) && e._clear();
      }) : !1 !== this._trigger("stop", t) && this._clear(), !1;
    },
    _mouseUp: function _mouseUp(t) {
      return this._unblockFrames(), w.ui.ddmanager && w.ui.ddmanager.dragStop(this, t), this.handleElement.is(t.target) && this.element.trigger("focus"), w.ui.mouse.prototype._mouseUp.call(this, t);
    },
    cancel: function cancel() {
      return this.helper.is(".ui-draggable-dragging") ? this._mouseUp(new w.Event("mouseup", {
        target: this.element[0]
      })) : this._clear(), this;
    },
    _getHandle: function _getHandle(t) {
      return !this.options.handle || !!w(t.target).closest(this.element.find(this.options.handle)).length;
    },
    _setHandleClassName: function _setHandleClassName() {
      this.handleElement = this.options.handle ? this.element.find(this.options.handle) : this.element, this._addClass(this.handleElement, "ui-draggable-handle");
    },
    _removeHandleClassName: function _removeHandleClassName() {
      this._removeClass(this.handleElement, "ui-draggable-handle");
    },
    _createHelper: function _createHelper(t) {
      var e = this.options,
        i = "function" == typeof e.helper,
        t = i ? w(e.helper.apply(this.element[0], [t])) : "clone" === e.helper ? this.element.clone().removeAttr("id") : this.element;
      return t.parents("body").length || t.appendTo("parent" === e.appendTo ? this.element[0].parentNode : e.appendTo), i && t[0] === this.element[0] && this._setPositionRelative(), t[0] === this.element[0] || /(fixed|absolute)/.test(t.css("position")) || t.css("position", "absolute"), t;
    },
    _setPositionRelative: function _setPositionRelative() {
      /^(?:r|a|f)/.test(this.element.css("position")) || (this.element[0].style.position = "relative");
    },
    _adjustOffsetFromHelper: function _adjustOffsetFromHelper(t) {
      "string" == typeof t && (t = t.split(" ")), "left" in (t = Array.isArray(t) ? {
        left: +t[0],
        top: +t[1] || 0
      } : t) && (this.offset.click.left = t.left + this.margins.left), "right" in t && (this.offset.click.left = this.helperProportions.width - t.right + this.margins.left), "top" in t && (this.offset.click.top = t.top + this.margins.top), "bottom" in t && (this.offset.click.top = this.helperProportions.height - t.bottom + this.margins.top);
    },
    _isRootNode: function _isRootNode(t) {
      return /(html|body)/i.test(t.tagName) || t === this.document[0];
    },
    _getParentOffset: function _getParentOffset() {
      var t = this.offsetParent.offset(),
        e = this.document[0];
      return "absolute" === this.cssPosition && this.scrollParent[0] !== e && w.contains(this.scrollParent[0], this.offsetParent[0]) && (t.left += this.scrollParent.scrollLeft(), t.top += this.scrollParent.scrollTop()), {
        top: (t = this._isRootNode(this.offsetParent[0]) ? {
          top: 0,
          left: 0
        } : t).top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
        left: t.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
      };
    },
    _getRelativeOffset: function _getRelativeOffset() {
      if ("relative" !== this.cssPosition) return {
        top: 0,
        left: 0
      };
      var t = this.element.position(),
        e = this._isRootNode(this.scrollParent[0]);
      return {
        top: t.top - (parseInt(this.helper.css("top"), 10) || 0) + (e ? 0 : this.scrollParent.scrollTop()),
        left: t.left - (parseInt(this.helper.css("left"), 10) || 0) + (e ? 0 : this.scrollParent.scrollLeft())
      };
    },
    _cacheMargins: function _cacheMargins() {
      this.margins = {
        left: parseInt(this.element.css("marginLeft"), 10) || 0,
        top: parseInt(this.element.css("marginTop"), 10) || 0,
        right: parseInt(this.element.css("marginRight"), 10) || 0,
        bottom: parseInt(this.element.css("marginBottom"), 10) || 0
      };
    },
    _cacheHelperProportions: function _cacheHelperProportions() {
      this.helperProportions = {
        width: this.helper.outerWidth(),
        height: this.helper.outerHeight()
      };
    },
    _setContainment: function _setContainment() {
      var t,
        e,
        i,
        s = this.options,
        o = this.document[0];
      this.relativeContainer = null, s.containment ? "window" !== s.containment ? "document" !== s.containment ? s.containment.constructor !== Array ? ("parent" === s.containment && (s.containment = this.helper[0].parentNode), (i = (e = w(s.containment))[0]) && (t = /(scroll|auto)/.test(e.css("overflow")), this.containment = [(parseInt(e.css("borderLeftWidth"), 10) || 0) + (parseInt(e.css("paddingLeft"), 10) || 0), (parseInt(e.css("borderTopWidth"), 10) || 0) + (parseInt(e.css("paddingTop"), 10) || 0), (t ? Math.max(i.scrollWidth, i.offsetWidth) : i.offsetWidth) - (parseInt(e.css("borderRightWidth"), 10) || 0) - (parseInt(e.css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left - this.margins.right, (t ? Math.max(i.scrollHeight, i.offsetHeight) : i.offsetHeight) - (parseInt(e.css("borderBottomWidth"), 10) || 0) - (parseInt(e.css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top - this.margins.bottom], this.relativeContainer = e)) : this.containment = s.containment : this.containment = [0, 0, w(o).width() - this.helperProportions.width - this.margins.left, (w(o).height() || o.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top] : this.containment = [w(window).scrollLeft() - this.offset.relative.left - this.offset.parent.left, w(window).scrollTop() - this.offset.relative.top - this.offset.parent.top, w(window).scrollLeft() + w(window).width() - this.helperProportions.width - this.margins.left, w(window).scrollTop() + (w(window).height() || o.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top] : this.containment = null;
    },
    _convertPositionTo: function _convertPositionTo(t, e) {
      e = e || this.position;
      var i = "absolute" === t ? 1 : -1,
        t = this._isRootNode(this.scrollParent[0]);
      return {
        top: e.top + this.offset.relative.top * i + this.offset.parent.top * i - ("fixed" === this.cssPosition ? -this.offset.scroll.top : t ? 0 : this.offset.scroll.top) * i,
        left: e.left + this.offset.relative.left * i + this.offset.parent.left * i - ("fixed" === this.cssPosition ? -this.offset.scroll.left : t ? 0 : this.offset.scroll.left) * i
      };
    },
    _generatePosition: function _generatePosition(t, e) {
      var i,
        s = this.options,
        o = this._isRootNode(this.scrollParent[0]),
        n = t.pageX,
        r = t.pageY;
      return o && this.offset.scroll || (this.offset.scroll = {
        top: this.scrollParent.scrollTop(),
        left: this.scrollParent.scrollLeft()
      }), e && (this.containment && (i = this.relativeContainer ? (i = this.relativeContainer.offset(), [this.containment[0] + i.left, this.containment[1] + i.top, this.containment[2] + i.left, this.containment[3] + i.top]) : this.containment, t.pageX - this.offset.click.left < i[0] && (n = i[0] + this.offset.click.left), t.pageY - this.offset.click.top < i[1] && (r = i[1] + this.offset.click.top), t.pageX - this.offset.click.left > i[2] && (n = i[2] + this.offset.click.left), t.pageY - this.offset.click.top > i[3] && (r = i[3] + this.offset.click.top)), s.grid && (t = s.grid[1] ? this.originalPageY + Math.round((r - this.originalPageY) / s.grid[1]) * s.grid[1] : this.originalPageY, r = !i || t - this.offset.click.top >= i[1] || t - this.offset.click.top > i[3] ? t : t - this.offset.click.top >= i[1] ? t - s.grid[1] : t + s.grid[1], t = s.grid[0] ? this.originalPageX + Math.round((n - this.originalPageX) / s.grid[0]) * s.grid[0] : this.originalPageX, n = !i || t - this.offset.click.left >= i[0] || t - this.offset.click.left > i[2] ? t : t - this.offset.click.left >= i[0] ? t - s.grid[0] : t + s.grid[0]), "y" === s.axis && (n = this.originalPageX), "x" === s.axis && (r = this.originalPageY)), {
        top: r - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + ("fixed" === this.cssPosition ? -this.offset.scroll.top : o ? 0 : this.offset.scroll.top),
        left: n - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + ("fixed" === this.cssPosition ? -this.offset.scroll.left : o ? 0 : this.offset.scroll.left)
      };
    },
    _clear: function _clear() {
      this._removeClass(this.helper, "ui-draggable-dragging"), this.helper[0] === this.element[0] || this.cancelHelperRemoval || this.helper.remove(), this.helper = null, this.cancelHelperRemoval = !1, this.destroyOnClear && this.destroy();
    },
    _trigger: function _trigger(t, e, i) {
      return i = i || this._uiHash(), w.ui.plugin.call(this, t, [e, i, this], !0), /^(drag|start|stop)/.test(t) && (this.positionAbs = this._convertPositionTo("absolute"), i.offset = this.positionAbs), w.Widget.prototype._trigger.call(this, t, e, i);
    },
    plugins: {},
    _uiHash: function _uiHash() {
      return {
        helper: this.helper,
        position: this.position,
        originalPosition: this.originalPosition,
        offset: this.positionAbs
      };
    }
  }), w.ui.plugin.add("draggable", "connectToSortable", {
    start: function start(e, t, i) {
      var s = w.extend({}, t, {
        item: i.element
      });
      i.sortables = [], w(i.options.connectToSortable).each(function () {
        var t = w(this).sortable("instance");
        t && !t.options.disabled && (i.sortables.push(t), t.refreshPositions(), t._trigger("activate", e, s));
      });
    },
    stop: function stop(e, t, i) {
      var s = w.extend({}, t, {
        item: i.element
      });
      i.cancelHelperRemoval = !1, w.each(i.sortables, function () {
        var t = this;
        t.isOver ? (t.isOver = 0, i.cancelHelperRemoval = !0, t.cancelHelperRemoval = !1, t._storedCSS = {
          position: t.placeholder.css("position"),
          top: t.placeholder.css("top"),
          left: t.placeholder.css("left")
        }, t._mouseStop(e), t.options.helper = t.options._helper) : (t.cancelHelperRemoval = !0, t._trigger("deactivate", e, s));
      });
    },
    drag: function drag(i, s, o) {
      w.each(o.sortables, function () {
        var t = !1,
          e = this;
        e.positionAbs = o.positionAbs, e.helperProportions = o.helperProportions, e.offset.click = o.offset.click, e._intersectsWith(e.containerCache) && (t = !0, w.each(o.sortables, function () {
          return this.positionAbs = o.positionAbs, this.helperProportions = o.helperProportions, this.offset.click = o.offset.click, t = this !== e && this._intersectsWith(this.containerCache) && w.contains(e.element[0], this.element[0]) ? !1 : t;
        })), t ? (e.isOver || (e.isOver = 1, o._parent = s.helper.parent(), e.currentItem = s.helper.appendTo(e.element).data("ui-sortable-item", !0), e.options._helper = e.options.helper, e.options.helper = function () {
          return s.helper[0];
        }, i.target = e.currentItem[0], e._mouseCapture(i, !0), e._mouseStart(i, !0, !0), e.offset.click.top = o.offset.click.top, e.offset.click.left = o.offset.click.left, e.offset.parent.left -= o.offset.parent.left - e.offset.parent.left, e.offset.parent.top -= o.offset.parent.top - e.offset.parent.top, o._trigger("toSortable", i), o.dropped = e.element, w.each(o.sortables, function () {
          this.refreshPositions();
        }), o.currentItem = o.element, e.fromOutside = o), e.currentItem && (e._mouseDrag(i), s.position = e.position)) : e.isOver && (e.isOver = 0, e.cancelHelperRemoval = !0, e.options._revert = e.options.revert, e.options.revert = !1, e._trigger("out", i, e._uiHash(e)), e._mouseStop(i, !0), e.options.revert = e.options._revert, e.options.helper = e.options._helper, e.placeholder && e.placeholder.remove(), s.helper.appendTo(o._parent), o._refreshOffsets(i), s.position = o._generatePosition(i, !0), o._trigger("fromSortable", i), o.dropped = !1, w.each(o.sortables, function () {
          this.refreshPositions();
        }));
      });
    }
  }), w.ui.plugin.add("draggable", "cursor", {
    start: function start(t, e, i) {
      var s = w("body"),
        i = i.options;
      s.css("cursor") && (i._cursor = s.css("cursor")), s.css("cursor", i.cursor);
    },
    stop: function stop(t, e, i) {
      i = i.options;
      i._cursor && w("body").css("cursor", i._cursor);
    }
  }), w.ui.plugin.add("draggable", "opacity", {
    start: function start(t, e, i) {
      e = w(e.helper), i = i.options;
      e.css("opacity") && (i._opacity = e.css("opacity")), e.css("opacity", i.opacity);
    },
    stop: function stop(t, e, i) {
      i = i.options;
      i._opacity && w(e.helper).css("opacity", i._opacity);
    }
  }), w.ui.plugin.add("draggable", "scroll", {
    start: function start(t, e, i) {
      i.scrollParentNotHidden || (i.scrollParentNotHidden = i.helper.scrollParent(!1)), i.scrollParentNotHidden[0] !== i.document[0] && "HTML" !== i.scrollParentNotHidden[0].tagName && (i.overflowOffset = i.scrollParentNotHidden.offset());
    },
    drag: function drag(t, e, i) {
      var s = i.options,
        o = !1,
        n = i.scrollParentNotHidden[0],
        r = i.document[0];
      n !== r && "HTML" !== n.tagName ? (s.axis && "x" === s.axis || (i.overflowOffset.top + n.offsetHeight - t.pageY < s.scrollSensitivity ? n.scrollTop = o = n.scrollTop + s.scrollSpeed : t.pageY - i.overflowOffset.top < s.scrollSensitivity && (n.scrollTop = o = n.scrollTop - s.scrollSpeed)), s.axis && "y" === s.axis || (i.overflowOffset.left + n.offsetWidth - t.pageX < s.scrollSensitivity ? n.scrollLeft = o = n.scrollLeft + s.scrollSpeed : t.pageX - i.overflowOffset.left < s.scrollSensitivity && (n.scrollLeft = o = n.scrollLeft - s.scrollSpeed))) : (s.axis && "x" === s.axis || (t.pageY - w(r).scrollTop() < s.scrollSensitivity ? o = w(r).scrollTop(w(r).scrollTop() - s.scrollSpeed) : w(window).height() - (t.pageY - w(r).scrollTop()) < s.scrollSensitivity && (o = w(r).scrollTop(w(r).scrollTop() + s.scrollSpeed))), s.axis && "y" === s.axis || (t.pageX - w(r).scrollLeft() < s.scrollSensitivity ? o = w(r).scrollLeft(w(r).scrollLeft() - s.scrollSpeed) : w(window).width() - (t.pageX - w(r).scrollLeft()) < s.scrollSensitivity && (o = w(r).scrollLeft(w(r).scrollLeft() + s.scrollSpeed)))), !1 !== o && w.ui.ddmanager && !s.dropBehaviour && w.ui.ddmanager.prepareOffsets(i, t);
    }
  }), w.ui.plugin.add("draggable", "snap", {
    start: function start(t, e, i) {
      var s = i.options;
      i.snapElements = [], w(s.snap.constructor !== String ? s.snap.items || ":data(ui-draggable)" : s.snap).each(function () {
        var t = w(this),
          e = t.offset();
        this !== i.element[0] && i.snapElements.push({
          item: this,
          width: t.outerWidth(),
          height: t.outerHeight(),
          top: e.top,
          left: e.left
        });
      });
    },
    drag: function drag(t, e, i) {
      for (var s, o, n, r, h, a, l, c, u, p = i.options, f = p.snapTolerance, d = e.offset.left, m = d + i.helperProportions.width, g = e.offset.top, v = g + i.helperProportions.height, _ = i.snapElements.length - 1; 0 <= _; _--) {
        a = (h = i.snapElements[_].left - i.margins.left) + i.snapElements[_].width, c = (l = i.snapElements[_].top - i.margins.top) + i.snapElements[_].height, m < h - f || a + f < d || v < l - f || c + f < g || !w.contains(i.snapElements[_].item.ownerDocument, i.snapElements[_].item) ? (i.snapElements[_].snapping && i.options.snap.release && i.options.snap.release.call(i.element, t, w.extend(i._uiHash(), {
          snapItem: i.snapElements[_].item
        })), i.snapElements[_].snapping = !1) : ("inner" !== p.snapMode && (s = Math.abs(l - v) <= f, o = Math.abs(c - g) <= f, n = Math.abs(h - m) <= f, r = Math.abs(a - d) <= f, s && (e.position.top = i._convertPositionTo("relative", {
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
        }).left)), !i.snapElements[_].snapping && (s || o || n || r || u) && i.options.snap.snap && i.options.snap.snap.call(i.element, t, w.extend(i._uiHash(), {
          snapItem: i.snapElements[_].item
        })), i.snapElements[_].snapping = s || o || n || r || u);
      }
    }
  }), w.ui.plugin.add("draggable", "stack", {
    start: function start(t, e, i) {
      var s,
        i = i.options,
        i = w.makeArray(w(i.stack)).sort(function (t, e) {
          return (parseInt(w(t).css("zIndex"), 10) || 0) - (parseInt(w(e).css("zIndex"), 10) || 0);
        });
      i.length && (s = parseInt(w(i[0]).css("zIndex"), 10) || 0, w(i).each(function (t) {
        w(this).css("zIndex", s + t);
      }), this.css("zIndex", s + i.length));
    }
  }), w.ui.plugin.add("draggable", "zIndex", {
    start: function start(t, e, i) {
      e = w(e.helper), i = i.options;
      e.css("zIndex") && (i._zIndex = e.css("zIndex")), e.css("zIndex", i.zIndex);
    },
    stop: function stop(t, e, i) {
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
    _isOverAxis: function _isOverAxis(t, e, i) {
      return e <= t && t < e + i;
    },
    _isFloating: function _isFloating(t) {
      return /left|right/.test(t.css("float")) || /inline|table-cell/.test(t.css("display"));
    },
    _create: function _create() {
      this.containerCache = {}, this._addClass("ui-sortable"), this.refresh(), this.offset = this.element.offset(), this._mouseInit(), this._setHandleClassName(), this.ready = !0;
    },
    _setOption: function _setOption(t, e) {
      this._super(t, e), "handle" === t && this._setHandleClassName();
    },
    _setHandleClassName: function _setHandleClassName() {
      var t = this;
      this._removeClass(this.element.find(".ui-sortable-handle"), "ui-sortable-handle"), w.each(this.items, function () {
        t._addClass(this.instance.options.handle ? this.item.find(this.instance.options.handle) : this.item, "ui-sortable-handle");
      });
    },
    _destroy: function _destroy() {
      this._mouseDestroy();
      for (var t = this.items.length - 1; 0 <= t; t--) {
        this.items[t].item.removeData(this.widgetName + "-item");
      }
      return this;
    },
    _mouseCapture: function _mouseCapture(t, e) {
      var i = null,
        s = !1,
        o = this;
      return !this.reverting && !this.options.disabled && "static" !== this.options.type && (this._refreshItems(t), w(t.target).parents().each(function () {
        if (w.data(this, o.widgetName + "-item") === o) return i = w(this), !1;
      }), !!(i = w.data(t.target, o.widgetName + "-item") === o ? w(t.target) : i) && !(this.options.handle && !e && (w(this.options.handle, i).find("*").addBack().each(function () {
        this === t.target && (s = !0);
      }), !s)) && (this.currentItem = i, this._removeCurrentsFromItems(), !0));
    },
    _mouseStart: function _mouseStart(t, e, i) {
      var s,
        o,
        n = this.options;
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
      }, this.helper[0] !== this.currentItem[0] && this.currentItem.hide(), this._createPlaceholder(), this.scrollParent = this.placeholder.scrollParent(), w.extend(this.offset, {
        parent: this._getParentOffset()
      }), n.containment && this._setContainment(), n.cursor && "auto" !== n.cursor && (o = this.document.find("body"), this.storedCursor = o.css("cursor"), o.css("cursor", n.cursor), this.storedStylesheet = w("<style>*{ cursor: " + n.cursor + " !important; }</style>").appendTo(o)), n.zIndex && (this.helper.css("zIndex") && (this._storedZIndex = this.helper.css("zIndex")), this.helper.css("zIndex", n.zIndex)), n.opacity && (this.helper.css("opacity") && (this._storedOpacity = this.helper.css("opacity")), this.helper.css("opacity", n.opacity)), this.scrollParent[0] !== this.document[0] && "HTML" !== this.scrollParent[0].tagName && (this.overflowOffset = this.scrollParent.offset()), this._trigger("start", t, this._uiHash()), this._preserveHelperProportions || this._cacheHelperProportions(), !i) for (s = this.containers.length - 1; 0 <= s; s--) {
        this.containers[s]._trigger("activate", t, this._uiHash(this));
      }
      return w.ui.ddmanager && (w.ui.ddmanager.current = this), w.ui.ddmanager && !n.dropBehaviour && w.ui.ddmanager.prepareOffsets(this, t), this.dragging = !0, this._addClass(this.helper, "ui-sortable-helper"), this.helper.parent().is(this.appendTo) || (this.helper.detach().appendTo(this.appendTo), this.offset.parent = this._getParentOffset()), this.position = this.originalPosition = this._generatePosition(t), this.originalPageX = t.pageX, this.originalPageY = t.pageY, this.lastPositionAbs = this.positionAbs = this._convertPositionTo("absolute"), this._mouseDrag(t), !0;
    },
    _scroll: function _scroll(t) {
      var e = this.options,
        i = !1;
      return this.scrollParent[0] !== this.document[0] && "HTML" !== this.scrollParent[0].tagName ? (this.overflowOffset.top + this.scrollParent[0].offsetHeight - t.pageY < e.scrollSensitivity ? this.scrollParent[0].scrollTop = i = this.scrollParent[0].scrollTop + e.scrollSpeed : t.pageY - this.overflowOffset.top < e.scrollSensitivity && (this.scrollParent[0].scrollTop = i = this.scrollParent[0].scrollTop - e.scrollSpeed), this.overflowOffset.left + this.scrollParent[0].offsetWidth - t.pageX < e.scrollSensitivity ? this.scrollParent[0].scrollLeft = i = this.scrollParent[0].scrollLeft + e.scrollSpeed : t.pageX - this.overflowOffset.left < e.scrollSensitivity && (this.scrollParent[0].scrollLeft = i = this.scrollParent[0].scrollLeft - e.scrollSpeed)) : (t.pageY - this.document.scrollTop() < e.scrollSensitivity ? i = this.document.scrollTop(this.document.scrollTop() - e.scrollSpeed) : this.window.height() - (t.pageY - this.document.scrollTop()) < e.scrollSensitivity && (i = this.document.scrollTop(this.document.scrollTop() + e.scrollSpeed)), t.pageX - this.document.scrollLeft() < e.scrollSensitivity ? i = this.document.scrollLeft(this.document.scrollLeft() - e.scrollSpeed) : this.window.width() - (t.pageX - this.document.scrollLeft()) < e.scrollSensitivity && (i = this.document.scrollLeft(this.document.scrollLeft() + e.scrollSpeed))), i;
    },
    _mouseDrag: function _mouseDrag(t) {
      var e,
        i,
        s,
        o,
        n = this.options;
      for (this.position = this._generatePosition(t), this.positionAbs = this._convertPositionTo("absolute"), this.options.axis && "y" === this.options.axis || (this.helper[0].style.left = this.position.left + "px"), this.options.axis && "x" === this.options.axis || (this.helper[0].style.top = this.position.top + "px"), n.scroll && !1 !== this._scroll(t) && (this._refreshItemPositions(!0), w.ui.ddmanager && !n.dropBehaviour && w.ui.ddmanager.prepareOffsets(this, t)), this.dragDirection = {
        vertical: this._getDragVerticalDirection(),
        horizontal: this._getDragHorizontalDirection()
      }, e = this.items.length - 1; 0 <= e; e--) {
        if (s = (i = this.items[e]).item[0], (o = this._intersectsWithPointer(i)) && i.instance === this.currentContainer && !(s === this.currentItem[0] || this.placeholder[1 === o ? "next" : "prev"]()[0] === s || w.contains(this.placeholder[0], s) || "semi-dynamic" === this.options.type && w.contains(this.element[0], s))) {
          if (this.direction = 1 === o ? "down" : "up", "pointer" !== this.options.tolerance && !this._intersectsWithSides(i)) break;
          this._rearrange(t, i), this._trigger("change", t, this._uiHash());
          break;
        }
      }
      return this._contactContainers(t), w.ui.ddmanager && w.ui.ddmanager.drag(this, t), this._trigger("sort", t, this._uiHash()), this.lastPositionAbs = this.positionAbs, !1;
    },
    _mouseStop: function _mouseStop(t, e) {
      var i, s, o, n;
      if (t) return w.ui.ddmanager && !this.options.dropBehaviour && w.ui.ddmanager.drop(this, t), this.options.revert ? (s = (i = this).placeholder.offset(), n = {}, (o = this.options.axis) && "x" !== o || (n.left = s.left - this.offset.parent.left - this.margins.left + (this.offsetParent[0] === this.document[0].body ? 0 : this.offsetParent[0].scrollLeft)), o && "y" !== o || (n.top = s.top - this.offset.parent.top - this.margins.top + (this.offsetParent[0] === this.document[0].body ? 0 : this.offsetParent[0].scrollTop)), this.reverting = !0, w(this.helper).animate(n, parseInt(this.options.revert, 10) || 500, function () {
        i._clear(t);
      })) : this._clear(t, e), !1;
    },
    cancel: function cancel() {
      if (this.dragging) {
        this._mouseUp(new w.Event("mouseup", {
          target: null
        })), "original" === this.options.helper ? (this.currentItem.css(this._storedCSS), this._removeClass(this.currentItem, "ui-sortable-helper")) : this.currentItem.show();
        for (var t = this.containers.length - 1; 0 <= t; t--) {
          this.containers[t]._trigger("deactivate", null, this._uiHash(this)), this.containers[t].containerCache.over && (this.containers[t]._trigger("out", null, this._uiHash(this)), this.containers[t].containerCache.over = 0);
        }
      }
      return this.placeholder && (this.placeholder[0].parentNode && this.placeholder[0].parentNode.removeChild(this.placeholder[0]), "original" !== this.options.helper && this.helper && this.helper[0].parentNode && this.helper.remove(), w.extend(this, {
        helper: null,
        dragging: !1,
        reverting: !1,
        _noFinalSort: null
      }), this.domPosition.prev ? w(this.domPosition.prev).after(this.currentItem) : w(this.domPosition.parent).prepend(this.currentItem)), this;
    },
    serialize: function serialize(e) {
      var t = this._getItemsAsjQuery(e && e.connected),
        i = [];
      return e = e || {}, w(t).each(function () {
        var t = (w(e.item || this).attr(e.attribute || "id") || "").match(e.expression || /(.+)[\-=_](.+)/);
        t && i.push((e.key || t[1] + "[]") + "=" + (e.key && e.expression ? t[1] : t[2]));
      }), !i.length && e.key && i.push(e.key + "="), i.join("&");
    },
    toArray: function toArray(t) {
      var e = this._getItemsAsjQuery(t && t.connected),
        i = [];
      return t = t || {}, e.each(function () {
        i.push(w(t.item || this).attr(t.attribute || "id") || "");
      }), i;
    },
    _intersectsWith: function _intersectsWith(t) {
      var e = this.positionAbs.left,
        i = e + this.helperProportions.width,
        s = this.positionAbs.top,
        o = s + this.helperProportions.height,
        n = t.left,
        r = n + t.width,
        h = t.top,
        a = h + t.height,
        l = this.offset.click.top,
        c = this.offset.click.left,
        l = "x" === this.options.axis || h < s + l && s + l < a,
        c = "y" === this.options.axis || n < e + c && e + c < r;
      return "pointer" === this.options.tolerance || this.options.forcePointerForContainers || "pointer" !== this.options.tolerance && this.helperProportions[this.floating ? "width" : "height"] > t[this.floating ? "width" : "height"] ? l && c : n < e + this.helperProportions.width / 2 && i - this.helperProportions.width / 2 < r && h < s + this.helperProportions.height / 2 && o - this.helperProportions.height / 2 < a;
    },
    _intersectsWithPointer: function _intersectsWithPointer(t) {
      var e = "x" === this.options.axis || this._isOverAxis(this.positionAbs.top + this.offset.click.top, t.top, t.height),
        t = "y" === this.options.axis || this._isOverAxis(this.positionAbs.left + this.offset.click.left, t.left, t.width);
      return !(!e || !t) && (e = this.dragDirection.vertical, t = this.dragDirection.horizontal, this.floating ? "right" === t || "down" === e ? 2 : 1 : e && ("down" === e ? 2 : 1));
    },
    _intersectsWithSides: function _intersectsWithSides(t) {
      var e = this._isOverAxis(this.positionAbs.top + this.offset.click.top, t.top + t.height / 2, t.height),
        i = this._isOverAxis(this.positionAbs.left + this.offset.click.left, t.left + t.width / 2, t.width),
        s = this.dragDirection.vertical,
        t = this.dragDirection.horizontal;
      return this.floating && t ? "right" === t && i || "left" === t && !i : s && ("down" === s && e || "up" === s && !e);
    },
    _getDragVerticalDirection: function _getDragVerticalDirection() {
      var t = this.positionAbs.top - this.lastPositionAbs.top;
      return 0 != t && (0 < t ? "down" : "up");
    },
    _getDragHorizontalDirection: function _getDragHorizontalDirection() {
      var t = this.positionAbs.left - this.lastPositionAbs.left;
      return 0 != t && (0 < t ? "right" : "left");
    },
    refresh: function refresh(t) {
      return this._refreshItems(t), this._setHandleClassName(), this.refreshPositions(), this;
    },
    _connectWith: function _connectWith() {
      var t = this.options;
      return t.connectWith.constructor === String ? [t.connectWith] : t.connectWith;
    },
    _getItemsAsjQuery: function _getItemsAsjQuery(t) {
      var e,
        i,
        s,
        o,
        n = [],
        r = [],
        h = this._connectWith();
      if (h && t) for (e = h.length - 1; 0 <= e; e--) {
        for (i = (s = w(h[e], this.document[0])).length - 1; 0 <= i; i--) {
          (o = w.data(s[i], this.widgetFullName)) && o !== this && !o.options.disabled && r.push(["function" == typeof o.options.items ? o.options.items.call(o.element) : w(o.options.items, o.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), o]);
        }
      }
      function a() {
        n.push(this);
      }
      for (r.push(["function" == typeof this.options.items ? this.options.items.call(this.element, null, {
        options: this.options,
        item: this.currentItem
      }) : w(this.options.items, this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), this]), e = r.length - 1; 0 <= e; e--) {
        r[e][0].each(a);
      }
      return w(n);
    },
    _removeCurrentsFromItems: function _removeCurrentsFromItems() {
      var i = this.currentItem.find(":data(" + this.widgetName + "-item)");
      this.items = w.grep(this.items, function (t) {
        for (var e = 0; e < i.length; e++) {
          if (i[e] === t.item[0]) return !1;
        }
        return !0;
      });
    },
    _refreshItems: function _refreshItems(t) {
      this.items = [], this.containers = [this];
      var e,
        i,
        s,
        o,
        n,
        r,
        h,
        a,
        l = this.items,
        c = [["function" == typeof this.options.items ? this.options.items.call(this.element[0], t, {
          item: this.currentItem
        }) : w(this.options.items, this.element), this]],
        u = this._connectWith();
      if (u && this.ready) for (e = u.length - 1; 0 <= e; e--) {
        for (i = (s = w(u[e], this.document[0])).length - 1; 0 <= i; i--) {
          (o = w.data(s[i], this.widgetFullName)) && o !== this && !o.options.disabled && (c.push(["function" == typeof o.options.items ? o.options.items.call(o.element[0], t, {
            item: this.currentItem
          }) : w(o.options.items, o.element), o]), this.containers.push(o));
        }
      }
      for (e = c.length - 1; 0 <= e; e--) {
        for (n = c[e][1], a = (r = c[e][i = 0]).length; i < a; i++) {
          (h = w(r[i])).data(this.widgetName + "-item", n), l.push({
            item: h,
            instance: n,
            width: 0,
            height: 0,
            left: 0,
            top: 0
          });
        }
      }
    },
    _refreshItemPositions: function _refreshItemPositions(t) {
      for (var e, i, s = this.items.length - 1; 0 <= s; s--) {
        e = this.items[s], this.currentContainer && e.instance !== this.currentContainer && e.item[0] !== this.currentItem[0] || (i = this.options.toleranceElement ? w(this.options.toleranceElement, e.item) : e.item, t || (e.width = i.outerWidth(), e.height = i.outerHeight()), i = i.offset(), e.left = i.left, e.top = i.top);
      }
    },
    refreshPositions: function refreshPositions(t) {
      var e, i;
      if (this.floating = !!this.items.length && ("x" === this.options.axis || this._isFloating(this.items[0].item)), this.offsetParent && this.helper && (this.offset.parent = this._getParentOffset()), this._refreshItemPositions(t), this.options.custom && this.options.custom.refreshContainers) this.options.custom.refreshContainers.call(this);else for (e = this.containers.length - 1; 0 <= e; e--) {
        i = this.containers[e].element.offset(), this.containers[e].containerCache.left = i.left, this.containers[e].containerCache.top = i.top, this.containers[e].containerCache.width = this.containers[e].element.outerWidth(), this.containers[e].containerCache.height = this.containers[e].element.outerHeight();
      }
      return this;
    },
    _createPlaceholder: function _createPlaceholder(i) {
      var s,
        o,
        n = (i = i || this).options;
      n.placeholder && n.placeholder.constructor !== String || (s = n.placeholder, o = i.currentItem[0].nodeName.toLowerCase(), n.placeholder = {
        element: function element() {
          var t = w("<" + o + ">", i.document[0]);
          return i._addClass(t, "ui-sortable-placeholder", s || i.currentItem[0].className)._removeClass(t, "ui-sortable-helper"), "tbody" === o ? i._createTrPlaceholder(i.currentItem.find("tr").eq(0), w("<tr>", i.document[0]).appendTo(t)) : "tr" === o ? i._createTrPlaceholder(i.currentItem, t) : "img" === o && t.attr("src", i.currentItem.attr("src")), s || t.css("visibility", "hidden"), t;
        },
        update: function update(t, e) {
          s && !n.forcePlaceholderSize || (e.height() && (!n.forcePlaceholderSize || "tbody" !== o && "tr" !== o) || e.height(i.currentItem.innerHeight() - parseInt(i.currentItem.css("paddingTop") || 0, 10) - parseInt(i.currentItem.css("paddingBottom") || 0, 10)), e.width() || e.width(i.currentItem.innerWidth() - parseInt(i.currentItem.css("paddingLeft") || 0, 10) - parseInt(i.currentItem.css("paddingRight") || 0, 10)));
        }
      }), i.placeholder = w(n.placeholder.element.call(i.element, i.currentItem)), i.currentItem.after(i.placeholder), n.placeholder.update(i, i.placeholder);
    },
    _createTrPlaceholder: function _createTrPlaceholder(t, e) {
      var i = this;
      t.children().each(function () {
        w("<td>&#160;</td>", i.document[0]).attr("colspan", w(this).attr("colspan") || 1).appendTo(e);
      });
    },
    _contactContainers: function _contactContainers(t) {
      for (var e, i, s, o, n, r, h, a, l, c = null, u = null, p = this.containers.length - 1; 0 <= p; p--) {
        w.contains(this.currentItem[0], this.containers[p].element[0]) || (this._intersectsWith(this.containers[p].containerCache) ? c && w.contains(this.containers[p].element[0], c.element[0]) || (c = this.containers[p], u = p) : this.containers[p].containerCache.over && (this.containers[p]._trigger("out", t, this._uiHash(this)), this.containers[p].containerCache.over = 0));
      }
      if (c) if (1 === this.containers.length) this.containers[u].containerCache.over || (this.containers[u]._trigger("over", t, this._uiHash(this)), this.containers[u].containerCache.over = 1);else {
        for (i = 1e4, s = null, o = (a = c.floating || this._isFloating(this.currentItem)) ? "left" : "top", n = a ? "width" : "height", l = a ? "pageX" : "pageY", e = this.items.length - 1; 0 <= e; e--) {
          w.contains(this.containers[u].element[0], this.items[e].item[0]) && this.items[e].item[0] !== this.currentItem[0] && (r = this.items[e].item.offset()[o], h = !1, t[l] - r > this.items[e][n] / 2 && (h = !0), Math.abs(t[l] - r) < i && (i = Math.abs(t[l] - r), s = this.items[e], this.direction = h ? "up" : "down"));
        }
        (s || this.options.dropOnEmpty) && (this.currentContainer !== this.containers[u] ? (s ? this._rearrange(t, s, null, !0) : this._rearrange(t, null, this.containers[u].element, !0), this._trigger("change", t, this._uiHash()), this.containers[u]._trigger("change", t, this._uiHash(this)), this.currentContainer = this.containers[u], this.options.placeholder.update(this.currentContainer, this.placeholder), this.scrollParent = this.placeholder.scrollParent(), this.scrollParent[0] !== this.document[0] && "HTML" !== this.scrollParent[0].tagName && (this.overflowOffset = this.scrollParent.offset()), this.containers[u]._trigger("over", t, this._uiHash(this)), this.containers[u].containerCache.over = 1) : this.currentContainer.containerCache.over || (this.containers[u]._trigger("over", t, this._uiHash()), this.currentContainer.containerCache.over = 1));
      }
    },
    _createHelper: function _createHelper(t) {
      var e = this.options,
        t = "function" == typeof e.helper ? w(e.helper.apply(this.element[0], [t, this.currentItem])) : "clone" === e.helper ? this.currentItem.clone() : this.currentItem;
      return t.parents("body").length || this.appendTo[0].appendChild(t[0]), t[0] === this.currentItem[0] && (this._storedCSS = {
        width: this.currentItem[0].style.width,
        height: this.currentItem[0].style.height,
        position: this.currentItem.css("position"),
        top: this.currentItem.css("top"),
        left: this.currentItem.css("left")
      }), t[0].style.width && !e.forceHelperSize || t.width(this.currentItem.width()), t[0].style.height && !e.forceHelperSize || t.height(this.currentItem.height()), t;
    },
    _adjustOffsetFromHelper: function _adjustOffsetFromHelper(t) {
      "string" == typeof t && (t = t.split(" ")), "left" in (t = Array.isArray(t) ? {
        left: +t[0],
        top: +t[1] || 0
      } : t) && (this.offset.click.left = t.left + this.margins.left), "right" in t && (this.offset.click.left = this.helperProportions.width - t.right + this.margins.left), "top" in t && (this.offset.click.top = t.top + this.margins.top), "bottom" in t && (this.offset.click.top = this.helperProportions.height - t.bottom + this.margins.top);
    },
    _getParentOffset: function _getParentOffset() {
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
    _getRelativeOffset: function _getRelativeOffset() {
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
    _cacheMargins: function _cacheMargins() {
      this.margins = {
        left: parseInt(this.currentItem.css("marginLeft"), 10) || 0,
        top: parseInt(this.currentItem.css("marginTop"), 10) || 0
      };
    },
    _cacheHelperProportions: function _cacheHelperProportions() {
      this.helperProportions = {
        width: this.helper.outerWidth(),
        height: this.helper.outerHeight()
      };
    },
    _setContainment: function _setContainment() {
      var t,
        e,
        i = this.options;
      "parent" === i.containment && (i.containment = this.helper[0].parentNode), "document" !== i.containment && "window" !== i.containment || (this.containment = [0 - this.offset.relative.left - this.offset.parent.left, 0 - this.offset.relative.top - this.offset.parent.top, "document" === i.containment ? this.document.width() : this.window.width() - this.helperProportions.width - this.margins.left, ("document" === i.containment ? this.document.height() || document.body.parentNode.scrollHeight : this.window.height() || this.document[0].body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top]), /^(document|window|parent)$/.test(i.containment) || (t = w(i.containment)[0], e = w(i.containment).offset(), i = "hidden" !== w(t).css("overflow"), this.containment = [e.left + (parseInt(w(t).css("borderLeftWidth"), 10) || 0) + (parseInt(w(t).css("paddingLeft"), 10) || 0) - this.margins.left, e.top + (parseInt(w(t).css("borderTopWidth"), 10) || 0) + (parseInt(w(t).css("paddingTop"), 10) || 0) - this.margins.top, e.left + (i ? Math.max(t.scrollWidth, t.offsetWidth) : t.offsetWidth) - (parseInt(w(t).css("borderLeftWidth"), 10) || 0) - (parseInt(w(t).css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left, e.top + (i ? Math.max(t.scrollHeight, t.offsetHeight) : t.offsetHeight) - (parseInt(w(t).css("borderTopWidth"), 10) || 0) - (parseInt(w(t).css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top]);
    },
    _convertPositionTo: function _convertPositionTo(t, e) {
      e = e || this.position;
      var i = "absolute" === t ? 1 : -1,
        s = "absolute" !== this.cssPosition || this.scrollParent[0] !== this.document[0] && w.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent : this.offsetParent,
        t = /(html|body)/i.test(s[0].tagName);
      return {
        top: e.top + this.offset.relative.top * i + this.offset.parent.top * i - ("fixed" === this.cssPosition ? -this.scrollParent.scrollTop() : t ? 0 : s.scrollTop()) * i,
        left: e.left + this.offset.relative.left * i + this.offset.parent.left * i - ("fixed" === this.cssPosition ? -this.scrollParent.scrollLeft() : t ? 0 : s.scrollLeft()) * i
      };
    },
    _generatePosition: function _generatePosition(t) {
      var e = this.options,
        i = t.pageX,
        s = t.pageY,
        o = "absolute" !== this.cssPosition || this.scrollParent[0] !== this.document[0] && w.contains(this.scrollParent[0], this.offsetParent[0]) ? this.scrollParent : this.offsetParent,
        n = /(html|body)/i.test(o[0].tagName);
      return "relative" !== this.cssPosition || this.scrollParent[0] !== this.document[0] && this.scrollParent[0] !== this.offsetParent[0] || (this.offset.relative = this._getRelativeOffset()), this.originalPosition && (this.containment && (t.pageX - this.offset.click.left < this.containment[0] && (i = this.containment[0] + this.offset.click.left), t.pageY - this.offset.click.top < this.containment[1] && (s = this.containment[1] + this.offset.click.top), t.pageX - this.offset.click.left > this.containment[2] && (i = this.containment[2] + this.offset.click.left), t.pageY - this.offset.click.top > this.containment[3] && (s = this.containment[3] + this.offset.click.top)), e.grid && (t = this.originalPageY + Math.round((s - this.originalPageY) / e.grid[1]) * e.grid[1], s = !this.containment || t - this.offset.click.top >= this.containment[1] && t - this.offset.click.top <= this.containment[3] ? t : t - this.offset.click.top >= this.containment[1] ? t - e.grid[1] : t + e.grid[1], t = this.originalPageX + Math.round((i - this.originalPageX) / e.grid[0]) * e.grid[0], i = !this.containment || t - this.offset.click.left >= this.containment[0] && t - this.offset.click.left <= this.containment[2] ? t : t - this.offset.click.left >= this.containment[0] ? t - e.grid[0] : t + e.grid[0])), {
        top: s - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + ("fixed" === this.cssPosition ? -this.scrollParent.scrollTop() : n ? 0 : o.scrollTop()),
        left: i - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + ("fixed" === this.cssPosition ? -this.scrollParent.scrollLeft() : n ? 0 : o.scrollLeft())
      };
    },
    _rearrange: function _rearrange(t, e, i, s) {
      i ? i[0].appendChild(this.placeholder[0]) : e.item[0].parentNode.insertBefore(this.placeholder[0], "down" === this.direction ? e.item[0] : e.item[0].nextSibling), this.counter = this.counter ? ++this.counter : 1;
      var o = this.counter;
      this._delay(function () {
        o === this.counter && this.refreshPositions(!s);
      });
    },
    _clear: function _clear(t, e) {
      this.reverting = !1;
      var i,
        s = [];
      if (!this._noFinalSort && this.currentItem.parent().length && this.placeholder.before(this.currentItem), this._noFinalSort = null, this.helper[0] === this.currentItem[0]) {
        for (i in this._storedCSS) {
          "auto" !== this._storedCSS[i] && "static" !== this._storedCSS[i] || (this._storedCSS[i] = "");
        }
        this.currentItem.css(this._storedCSS), this._removeClass(this.currentItem, "ui-sortable-helper");
      } else this.currentItem.show();
      function o(e, i, s) {
        return function (t) {
          s._trigger(e, t, i._uiHash(i));
        };
      }
      for (this.fromOutside && !e && s.push(function (t) {
        this._trigger("receive", t, this._uiHash(this.fromOutside));
      }), !this.fromOutside && this.domPosition.prev === this.currentItem.prev().not(".ui-sortable-helper")[0] && this.domPosition.parent === this.currentItem.parent()[0] || e || s.push(function (t) {
        this._trigger("update", t, this._uiHash());
      }), this !== this.currentContainer && (e || (s.push(function (t) {
        this._trigger("remove", t, this._uiHash());
      }), s.push(function (e) {
        return function (t) {
          e._trigger("receive", t, this._uiHash(this));
        };
      }.call(this, this.currentContainer)), s.push(function (e) {
        return function (t) {
          e._trigger("update", t, this._uiHash(this));
        };
      }.call(this, this.currentContainer)))), i = this.containers.length - 1; 0 <= i; i--) {
        e || s.push(o("deactivate", this, this.containers[i])), this.containers[i].containerCache.over && (s.push(o("out", this, this.containers[i])), this.containers[i].containerCache.over = 0);
      }
      if (this.storedCursor && (this.document.find("body").css("cursor", this.storedCursor), this.storedStylesheet.remove()), this._storedOpacity && this.helper.css("opacity", this._storedOpacity), this._storedZIndex && this.helper.css("zIndex", "auto" === this._storedZIndex ? "" : this._storedZIndex), this.dragging = !1, e || this._trigger("beforeStop", t, this._uiHash()), this.placeholder[0].parentNode.removeChild(this.placeholder[0]), this.cancelHelperRemoval || (this.helper[0] !== this.currentItem[0] && this.helper.remove(), this.helper = null), !e) {
        for (i = 0; i < s.length; i++) {
          s[i].call(this, t);
        }
        this._trigger("stop", t, this._uiHash());
      }
      return this.fromOutside = !1, !this.cancelHelperRemoval;
    },
    _trigger: function _trigger() {
      !1 === w.Widget.prototype._trigger.apply(this, arguments) && this.cancel();
    },
    _uiHash: function _uiHash(t) {
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
      icons: {
        submenu: "ui-icon-caret-1-e"
      },
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
    _create: function _create() {
      this.activeMenu = this.element, this.mouseHandled = !1, this.lastMousePosition = {
        x: null,
        y: null
      }, this.element.uniqueId().attr({
        role: this.options.role,
        tabIndex: 0
      }), this._addClass("ui-menu", "ui-widget ui-widget-content"), this._on({
        "mousedown .ui-menu-item": function mousedownUiMenuItem(t) {
          t.preventDefault(), this._activateItem(t);
        },
        "click .ui-menu-item": function clickUiMenuItem(t) {
          var e = w(t.target),
            i = w(w.ui.safeActiveElement(this.document[0]));
          !this.mouseHandled && e.not(".ui-state-disabled").length && (this.select(t), t.isPropagationStopped() || (this.mouseHandled = !0), e.has(".ui-menu").length ? this.expand(t) : !this.element.is(":focus") && i.closest(".ui-menu").length && (this.element.trigger("focus", [!0]), this.active && 1 === this.active.parents(".ui-menu").length && clearTimeout(this.timer)));
        },
        "mouseenter .ui-menu-item": "_activateItem",
        "mousemove .ui-menu-item": "_activateItem",
        mouseleave: "collapseAll",
        "mouseleave .ui-menu": "collapseAll",
        focus: function focus(t, e) {
          var i = this.active || this._menuItems().first();
          e || this.focus(t, i);
        },
        blur: function blur(t) {
          this._delay(function () {
            w.contains(this.element[0], w.ui.safeActiveElement(this.document[0])) || this.collapseAll(t);
          });
        },
        keydown: "_keydown"
      }), this.refresh(), this._on(this.document, {
        click: function click(t) {
          this._closeOnDocumentClick(t) && this.collapseAll(t, !0), this.mouseHandled = !1;
        }
      });
    },
    _activateItem: function _activateItem(t) {
      var e, i;
      this.previousFilter || t.clientX === this.lastMousePosition.x && t.clientY === this.lastMousePosition.y || (this.lastMousePosition = {
        x: t.clientX,
        y: t.clientY
      }, e = w(t.target).closest(".ui-menu-item"), i = w(t.currentTarget), e[0] === i[0] && (i.is(".ui-state-active") || (this._removeClass(i.siblings().children(".ui-state-active"), null, "ui-state-active"), this.focus(t, i))));
    },
    _destroy: function _destroy() {
      var t = this.element.find(".ui-menu-item").removeAttr("role aria-disabled").children(".ui-menu-item-wrapper").removeUniqueId().removeAttr("tabIndex role aria-haspopup");
      this.element.removeAttr("aria-activedescendant").find(".ui-menu").addBack().removeAttr("role aria-labelledby aria-expanded aria-hidden aria-disabled tabIndex").removeUniqueId().show(), t.children().each(function () {
        var t = w(this);
        t.data("ui-menu-submenu-caret") && t.remove();
      });
    },
    _keydown: function _keydown(t) {
      var e,
        i,
        s,
        o = !0;
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
        default:
          e = this.previousFilter || "", s = o = !1, i = 96 <= t.keyCode && t.keyCode <= 105 ? (t.keyCode - 96).toString() : String.fromCharCode(t.keyCode), clearTimeout(this.filterTimer), i === e ? s = !0 : i = e + i, e = this._filterMenuItems(i), (e = s && -1 !== e.index(this.active.next()) ? this.active.nextAll(".ui-menu-item") : e).length || (i = String.fromCharCode(t.keyCode), e = this._filterMenuItems(i)), e.length ? (this.focus(t, e), this.previousFilter = i, this.filterTimer = this._delay(function () {
            delete this.previousFilter;
          }, 1e3)) : delete this.previousFilter;
      }
      o && t.preventDefault();
    },
    _activate: function _activate(t) {
      this.active && !this.active.is(".ui-state-disabled") && (this.active.children("[aria-haspopup='true']").length ? this.expand(t) : this.select(t));
    },
    refresh: function refresh() {
      var t,
        e,
        s = this,
        o = this.options.icons.submenu,
        i = this.element.find(this.options.menus);
      this._toggleClass("ui-menu-icons", null, !!this.element.find(".ui-icon").length), e = i.filter(":not(.ui-menu)").hide().attr({
        role: this.options.role,
        "aria-hidden": "true",
        "aria-expanded": "false"
      }).each(function () {
        var t = w(this),
          e = t.prev(),
          i = w("<span>").data("ui-menu-submenu-caret", !0);
        s._addClass(i, "ui-menu-icon", "ui-icon " + o), e.attr("aria-haspopup", "true").prepend(i), t.attr("aria-labelledby", e.attr("id"));
      }), this._addClass(e, "ui-menu", "ui-widget ui-widget-content ui-front"), (t = i.add(this.element).find(this.options.items)).not(".ui-menu-item").each(function () {
        var t = w(this);
        s._isDivider(t) && s._addClass(t, "ui-menu-divider", "ui-widget-content");
      }), i = (e = t.not(".ui-menu-item, .ui-menu-divider")).children().not(".ui-menu").uniqueId().attr({
        tabIndex: -1,
        role: this._itemRole()
      }), this._addClass(e, "ui-menu-item")._addClass(i, "ui-menu-item-wrapper"), t.filter(".ui-state-disabled").attr("aria-disabled", "true"), this.active && !w.contains(this.element[0], this.active[0]) && this.blur();
    },
    _itemRole: function _itemRole() {
      return {
        menu: "menuitem",
        listbox: "option"
      }[this.options.role];
    },
    _setOption: function _setOption(t, e) {
      var i;
      "icons" === t && (i = this.element.find(".ui-menu-icon"), this._removeClass(i, null, this.options.icons.submenu)._addClass(i, null, e.submenu)), this._super(t, e);
    },
    _setOptionDisabled: function _setOptionDisabled(t) {
      this._super(t), this.element.attr("aria-disabled", String(t)), this._toggleClass(null, "ui-state-disabled", !!t);
    },
    focus: function focus(t, e) {
      var i;
      this.blur(t, t && "focus" === t.type), this._scrollIntoView(e), this.active = e.first(), i = this.active.children(".ui-menu-item-wrapper"), this._addClass(i, null, "ui-state-active"), this.options.role && this.element.attr("aria-activedescendant", i.attr("id")), i = this.active.parent().closest(".ui-menu-item").children(".ui-menu-item-wrapper"), this._addClass(i, null, "ui-state-active"), t && "keydown" === t.type ? this._close() : this.timer = this._delay(function () {
        this._close();
      }, this.delay), (i = e.children(".ui-menu")).length && t && /^mouse/.test(t.type) && this._startOpening(i), this.activeMenu = e.parent(), this._trigger("focus", t, {
        item: e
      });
    },
    _scrollIntoView: function _scrollIntoView(t) {
      var e, i, s;
      this._hasScroll() && (i = parseFloat(w.css(this.activeMenu[0], "borderTopWidth")) || 0, s = parseFloat(w.css(this.activeMenu[0], "paddingTop")) || 0, e = t.offset().top - this.activeMenu.offset().top - i - s, i = this.activeMenu.scrollTop(), s = this.activeMenu.height(), t = t.outerHeight(), e < 0 ? this.activeMenu.scrollTop(i + e) : s < e + t && this.activeMenu.scrollTop(i + e - s + t));
    },
    blur: function blur(t, e) {
      e || clearTimeout(this.timer), this.active && (this._removeClass(this.active.children(".ui-menu-item-wrapper"), null, "ui-state-active"), this._trigger("blur", t, {
        item: this.active
      }), this.active = null);
    },
    _startOpening: function _startOpening(t) {
      clearTimeout(this.timer), "true" === t.attr("aria-hidden") && (this.timer = this._delay(function () {
        this._close(), this._open(t);
      }, this.delay));
    },
    _open: function _open(t) {
      var e = w.extend({
        of: this.active
      }, this.options.position);
      clearTimeout(this.timer), this.element.find(".ui-menu").not(t.parents(".ui-menu")).hide().attr("aria-hidden", "true"), t.show().removeAttr("aria-hidden").attr("aria-expanded", "true").position(e);
    },
    collapseAll: function collapseAll(e, i) {
      clearTimeout(this.timer), this.timer = this._delay(function () {
        var t = i ? this.element : w(e && e.target).closest(this.element.find(".ui-menu"));
        t.length || (t = this.element), this._close(t), this.blur(e), this._removeClass(t.find(".ui-state-active"), null, "ui-state-active"), this.activeMenu = t;
      }, i ? 0 : this.delay);
    },
    _close: function _close(t) {
      (t = t || (this.active ? this.active.parent() : this.element)).find(".ui-menu").hide().attr("aria-hidden", "true").attr("aria-expanded", "false");
    },
    _closeOnDocumentClick: function _closeOnDocumentClick(t) {
      return !w(t.target).closest(".ui-menu").length;
    },
    _isDivider: function _isDivider(t) {
      return !/[^\-\u2014\u2013\s]/.test(t.text());
    },
    collapse: function collapse(t) {
      var e = this.active && this.active.parent().closest(".ui-menu-item", this.element);
      e && e.length && (this._close(), this.focus(t, e));
    },
    expand: function expand(t) {
      var e = this.active && this._menuItems(this.active.children(".ui-menu")).first();
      e && e.length && (this._open(e.parent()), this._delay(function () {
        this.focus(t, e);
      }));
    },
    next: function next(t) {
      this._move("next", "first", t);
    },
    previous: function previous(t) {
      this._move("prev", "last", t);
    },
    isFirstItem: function isFirstItem() {
      return this.active && !this.active.prevAll(".ui-menu-item").length;
    },
    isLastItem: function isLastItem() {
      return this.active && !this.active.nextAll(".ui-menu-item").length;
    },
    _menuItems: function _menuItems(t) {
      return (t || this.element).find(this.options.items).filter(".ui-menu-item");
    },
    _move: function _move(t, e, i) {
      var s;
      (s = this.active ? "first" === t || "last" === t ? this.active["first" === t ? "prevAll" : "nextAll"](".ui-menu-item").last() : this.active[t + "All"](".ui-menu-item").first() : s) && s.length && this.active || (s = this._menuItems(this.activeMenu)[e]()), this.focus(i, s);
    },
    nextPage: function nextPage(t) {
      var e, i, s;
      this.active ? this.isLastItem() || (this._hasScroll() ? (i = this.active.offset().top, s = this.element.innerHeight(), 0 === w.fn.jquery.indexOf("3.2.") && (s += this.element[0].offsetHeight - this.element.outerHeight()), this.active.nextAll(".ui-menu-item").each(function () {
        return (e = w(this)).offset().top - i - s < 0;
      }), this.focus(t, e)) : this.focus(t, this._menuItems(this.activeMenu)[this.active ? "last" : "first"]())) : this.next(t);
    },
    previousPage: function previousPage(t) {
      var e, i, s;
      this.active ? this.isFirstItem() || (this._hasScroll() ? (i = this.active.offset().top, s = this.element.innerHeight(), 0 === w.fn.jquery.indexOf("3.2.") && (s += this.element[0].offsetHeight - this.element.outerHeight()), this.active.prevAll(".ui-menu-item").each(function () {
        return 0 < (e = w(this)).offset().top - i + s;
      }), this.focus(t, e)) : this.focus(t, this._menuItems(this.activeMenu).first())) : this.next(t);
    },
    _hasScroll: function _hasScroll() {
      return this.element.outerHeight() < this.element.prop("scrollHeight");
    },
    select: function select(t) {
      this.active = this.active || w(t.target).closest(".ui-menu-item");
      var e = {
        item: this.active
      };
      this.active.has(".ui-menu").length || this.collapseAll(t, !0), this._trigger("select", t, e);
    },
    _filterMenuItems: function _filterMenuItems(t) {
      var t = t.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"),
        e = new RegExp("^" + t, "i");
      return this.activeMenu.find(this.options.items).filter(".ui-menu-item").filter(function () {
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
    _create: function _create() {
      var i,
        s,
        o,
        t = this.element[0].nodeName.toLowerCase(),
        e = "textarea" === t,
        t = "input" === t;
      this.isMultiLine = e || !t && this._isContentEditable(this.element), this.valueMethod = this.element[e || t ? "val" : "text"], this.isNewMenu = !0, this._addClass("ui-autocomplete-input"), this.element.attr("autocomplete", "off"), this._on(this.element, {
        keydown: function keydown(t) {
          if (this.element.prop("readOnly")) s = o = i = !0;else {
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
              default:
                s = !0, this._searchTimeout(t);
            }
          }
        },
        keypress: function keypress(t) {
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
              case e.DOWN:
                this._keyEvent("next", t);
            }
          }
        },
        input: function input(t) {
          if (o) return o = !1, void t.preventDefault();
          this._searchTimeout(t);
        },
        focus: function focus() {
          this.selectedItem = null, this.previous = this._value();
        },
        blur: function blur(t) {
          clearTimeout(this.searching), this.close(t), this._change(t);
        }
      }), this._initSource(), this.menu = w("<ul>").appendTo(this._appendTo()).menu({
        role: null
      }).hide().attr({
        unselectable: "on"
      }).menu("instance"), this._addClass(this.menu.element, "ui-autocomplete", "ui-front"), this._on(this.menu.element, {
        mousedown: function mousedown(t) {
          t.preventDefault();
        },
        menufocus: function menufocus(t, e) {
          var i, s;
          if (this.isNewMenu && (this.isNewMenu = !1, t.originalEvent && /^mouse/.test(t.originalEvent.type))) return this.menu.blur(), void this.document.one("mousemove", function () {
            w(t.target).trigger(t.originalEvent);
          });
          s = e.item.data("ui-autocomplete-item"), !1 !== this._trigger("focus", t, {
            item: s
          }) && t.originalEvent && /^key/.test(t.originalEvent.type) && this._value(s.value), (i = e.item.attr("aria-label") || s.value) && String.prototype.trim.call(i).length && (clearTimeout(this.liveRegionTimer), this.liveRegionTimer = this._delay(function () {
            this.liveRegion.html(w("<div>").text(i));
          }, 100));
        },
        menuselect: function menuselect(t, e) {
          var i = e.item.data("ui-autocomplete-item"),
            s = this.previous;
          this.element[0] !== w.ui.safeActiveElement(this.document[0]) && (this.element.trigger("focus"), this.previous = s, this._delay(function () {
            this.previous = s, this.selectedItem = i;
          })), !1 !== this._trigger("select", t, {
            item: i
          }) && this._value(i.value), this.term = this._value(), this.close(t), this.selectedItem = i;
        }
      }), this.liveRegion = w("<div>", {
        role: "status",
        "aria-live": "assertive",
        "aria-relevant": "additions"
      }).appendTo(this.document[0].body), this._addClass(this.liveRegion, null, "ui-helper-hidden-accessible"), this._on(this.window, {
        beforeunload: function beforeunload() {
          this.element.removeAttr("autocomplete");
        }
      });
    },
    _destroy: function _destroy() {
      clearTimeout(this.searching), this.element.removeAttr("autocomplete"), this.menu.element.remove(), this.liveRegion.remove();
    },
    _setOption: function _setOption(t, e) {
      this._super(t, e), "source" === t && this._initSource(), "appendTo" === t && this.menu.element.appendTo(this._appendTo()), "disabled" === t && e && this.xhr && this.xhr.abort();
    },
    _isEventTargetInWidget: function _isEventTargetInWidget(t) {
      var e = this.menu.element[0];
      return t.target === this.element[0] || t.target === e || w.contains(e, t.target);
    },
    _closeOnClickOutside: function _closeOnClickOutside(t) {
      this._isEventTargetInWidget(t) || this.close();
    },
    _appendTo: function _appendTo() {
      var t = this.options.appendTo;
      return t = !(t = !(t = t && (t.jquery || t.nodeType ? w(t) : this.document.find(t).eq(0))) || !t[0] ? this.element.closest(".ui-front, dialog") : t).length ? this.document[0].body : t;
    },
    _initSource: function _initSource() {
      var i,
        s,
        o = this;
      Array.isArray(this.options.source) ? (i = this.options.source, this.source = function (t, e) {
        e(w.ui.autocomplete.filter(i, t.term));
      }) : "string" == typeof this.options.source ? (s = this.options.source, this.source = function (t, e) {
        o.xhr && o.xhr.abort(), o.xhr = w.ajax({
          url: s,
          data: t,
          dataType: "json",
          success: function success(t) {
            e(t);
          },
          error: function error() {
            e([]);
          }
        });
      }) : this.source = this.options.source;
    },
    _searchTimeout: function _searchTimeout(s) {
      clearTimeout(this.searching), this.searching = this._delay(function () {
        var t = this.term === this._value(),
          e = this.menu.element.is(":visible"),
          i = s.altKey || s.ctrlKey || s.metaKey || s.shiftKey;
        t && (e || i) || (this.selectedItem = null, this.search(null, s));
      }, this.options.delay);
    },
    search: function search(t, e) {
      return t = null != t ? t : this._value(), this.term = this._value(), t.length < this.options.minLength ? this.close(e) : !1 !== this._trigger("search", e) ? this._search(t) : void 0;
    },
    _search: function _search(t) {
      this.pending++, this._addClass("ui-autocomplete-loading"), this.cancelSearch = !1, this.source({
        term: t
      }, this._response());
    },
    _response: function _response() {
      var e = ++this.requestIndex;
      return function (t) {
        e === this.requestIndex && this.__response(t), this.pending--, this.pending || this._removeClass("ui-autocomplete-loading");
      }.bind(this);
    },
    __response: function __response(t) {
      t = t && this._normalize(t), this._trigger("response", null, {
        content: t
      }), !this.options.disabled && t && t.length && !this.cancelSearch ? (this._suggest(t), this._trigger("open")) : this._close();
    },
    close: function close(t) {
      this.cancelSearch = !0, this._close(t);
    },
    _close: function _close(t) {
      this._off(this.document, "mousedown"), this.menu.element.is(":visible") && (this.menu.element.hide(), this.menu.blur(), this.isNewMenu = !0, this._trigger("close", t));
    },
    _change: function _change(t) {
      this.previous !== this._value() && this._trigger("change", t, {
        item: this.selectedItem
      });
    },
    _normalize: function _normalize(t) {
      return t.length && t[0].label && t[0].value ? t : w.map(t, function (t) {
        return "string" == typeof t ? {
          label: t,
          value: t
        } : w.extend({}, t, {
          label: t.label || t.value,
          value: t.value || t.label
        });
      });
    },
    _suggest: function _suggest(t) {
      var e = this.menu.element.empty();
      this._renderMenu(e, t), this.isNewMenu = !0, this.menu.refresh(), e.show(), this._resizeMenu(), e.position(w.extend({
        of: this.element
      }, this.options.position)), this.options.autoFocus && this.menu.next(), this._on(this.document, {
        mousedown: "_closeOnClickOutside"
      });
    },
    _resizeMenu: function _resizeMenu() {
      var t = this.menu.element;
      t.outerWidth(Math.max(t.width("").outerWidth() + 1, this.element.outerWidth()));
    },
    _renderMenu: function _renderMenu(i, t) {
      var s = this;
      w.each(t, function (t, e) {
        s._renderItemData(i, e);
      });
    },
    _renderItemData: function _renderItemData(t, e) {
      return this._renderItem(t, e).data("ui-autocomplete-item", e);
    },
    _renderItem: function _renderItem(t, e) {
      return w("<li>").append(w("<div>").text(e.label)).appendTo(t);
    },
    _move: function _move(t, e) {
      if (this.menu.element.is(":visible")) return this.menu.isFirstItem() && /^previous/.test(t) || this.menu.isLastItem() && /^next/.test(t) ? (this.isMultiLine || this._value(this.term), void this.menu.blur()) : void this.menu[t](e);
      this.search(null, e);
    },
    widget: function widget() {
      return this.menu.element;
    },
    _value: function _value() {
      return this.valueMethod.apply(this.element, arguments);
    },
    _keyEvent: function _keyEvent(t, e) {
      this.isMultiLine && !this.menu.element.is(":visible") || (this._move(t, e), e.preventDefault());
    },
    _isContentEditable: function _isContentEditable(t) {
      if (!t.length) return !1;
      var e = t.prop("contentEditable");
      return "inherit" === e ? this._isContentEditable(t.parent()) : "true" === e;
    }
  }), w.extend(w.ui.autocomplete, {
    escapeRegex: function escapeRegex(t) {
      return t.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
    },
    filter: function filter(t, e) {
      var i = new RegExp(w.ui.autocomplete.escapeRegex(e), "i");
      return w.grep(t, function (t) {
        return i.test(t.label || t.value || t);
      });
    }
  }), w.widget("ui.autocomplete", w.ui.autocomplete, {
    options: {
      messages: {
        noResults: "No search results.",
        results: function results(t) {
          return t + (1 < t ? " results are" : " result is") + " available, use up and down arrow keys to navigate.";
        }
      }
    },
    __response: function __response(t) {
      var e;
      this._superApply(arguments), this.options.disabled || this.cancelSearch || (e = t && t.length ? this.options.messages.results(t.length) : this.options.messages.noResults, clearTimeout(this.liveRegionTimer), this.liveRegionTimer = this._delay(function () {
        this.liveRegion.html(w("<div>").text(e));
      }, 100));
    }
  });
  w.ui.autocomplete;
});
/**
 * Huebee PACKAGED v2.0.0
 * 1-click color picker
 * MIT license
 * http://huebee.buzz
 * Copyright 2018 Metafizzy
 */

!function (t, e) {
  "function" == typeof define && define.amd ? define("ev-emitter/ev-emitter", e) : "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) && module.exports ? module.exports = e() : t.EvEmitter = e();
}("undefined" != typeof window ? window : this, function () {
  function t() {}
  var e = t.prototype;
  return e.on = function (t, e) {
    if (t && e) {
      var n = this._events = this._events || {},
        i = n[t] = n[t] || [];
      return i.indexOf(e) == -1 && i.push(e), this;
    }
  }, e.once = function (t, e) {
    if (t && e) {
      this.on(t, e);
      var n = this._onceEvents = this._onceEvents || {},
        i = n[t] = n[t] || {};
      return i[e] = !0, this;
    }
  }, e.off = function (t, e) {
    var n = this._events && this._events[t];
    if (n && n.length) {
      var i = n.indexOf(e);
      return i != -1 && n.splice(i, 1), this;
    }
  }, e.emitEvent = function (t, e) {
    var n = this._events && this._events[t];
    if (n && n.length) {
      var i = 0,
        o = n[i];
      e = e || [];
      for (var s = this._onceEvents && this._onceEvents[t]; o;) {
        var r = s && s[o];
        r && (this.off(t, o), delete s[o]), o.apply(this, e), i += r ? 0 : 1, o = n[i];
      }
      return this;
    }
  }, t;
}), function (t, e) {
  "function" == typeof define && define.amd ? define("unipointer/unipointer", ["ev-emitter/ev-emitter"], function (n) {
    return e(t, n);
  }) : "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) && module.exports ? module.exports = e(t, require("ev-emitter")) : t.Unipointer = e(t, t.EvEmitter);
}(window, function (t, e) {
  function n() {}
  function i() {}
  var o = i.prototype = Object.create(e.prototype);
  o.bindStartEvent = function (t) {
    this._bindStartEvent(t, !0);
  }, o.unbindStartEvent = function (t) {
    this._bindStartEvent(t, !1);
  }, o._bindStartEvent = function (e, n) {
    n = void 0 === n || !!n;
    var i = n ? "addEventListener" : "removeEventListener";
    t.navigator.pointerEnabled ? e[i]("pointerdown", this) : t.navigator.msPointerEnabled ? e[i]("MSPointerDown", this) : (e[i]("mousedown", this), e[i]("touchstart", this));
  }, o.handleEvent = function (t) {
    var e = "on" + t.type;
    this[e] && this[e](t);
  }, o.getTouch = function (t) {
    for (var e = 0; e < t.length; e++) {
      var n = t[e];
      if (n.identifier == this.pointerIdentifier) return n;
    }
  }, o.onmousedown = function (t) {
    var e = t.button;
    e && 0 !== e && 1 !== e || this._pointerDown(t, t);
  }, o.ontouchstart = function (t) {
    this._pointerDown(t, t.changedTouches[0]);
  }, o.onMSPointerDown = o.onpointerdown = function (t) {
    this._pointerDown(t, t);
  }, o._pointerDown = function (t, e) {
    this.isPointerDown || (this.isPointerDown = !0, this.pointerIdentifier = void 0 !== e.pointerId ? e.pointerId : e.identifier, this.pointerDown(t, e));
  }, o.pointerDown = function (t, e) {
    this._bindPostStartEvents(t), this.emitEvent("pointerDown", [t, e]);
  };
  var s = {
    mousedown: ["mousemove", "mouseup"],
    touchstart: ["touchmove", "touchend", "touchcancel"],
    pointerdown: ["pointermove", "pointerup", "pointercancel"],
    MSPointerDown: ["MSPointerMove", "MSPointerUp", "MSPointerCancel"]
  };
  return o._bindPostStartEvents = function (e) {
    if (e) {
      var n = s[e.type];
      n.forEach(function (e) {
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
    var e = this.getTouch(t.changedTouches);
    e && this._pointerMove(t, e);
  }, o._pointerMove = function (t, e) {
    this.pointerMove(t, e);
  }, o.pointerMove = function (t, e) {
    this.emitEvent("pointerMove", [t, e]);
  }, o.onmouseup = function (t) {
    this._pointerUp(t, t);
  }, o.onMSPointerUp = o.onpointerup = function (t) {
    t.pointerId == this.pointerIdentifier && this._pointerUp(t, t);
  }, o.ontouchend = function (t) {
    var e = this.getTouch(t.changedTouches);
    e && this._pointerUp(t, e);
  }, o._pointerUp = function (t, e) {
    this._pointerDone(), this.pointerUp(t, e);
  }, o.pointerUp = function (t, e) {
    this.emitEvent("pointerUp", [t, e]);
  }, o._pointerDone = function () {
    this.isPointerDown = !1, delete this.pointerIdentifier, this._unbindPostStartEvents(), this.pointerDone();
  }, o.pointerDone = n, o.onMSPointerCancel = o.onpointercancel = function (t) {
    t.pointerId == this.pointerIdentifier && this._pointerCancel(t, t);
  }, o.ontouchcancel = function (t) {
    var e = this.getTouch(t.changedTouches);
    e && this._pointerCancel(t, e);
  }, o._pointerCancel = function (t, e) {
    this._pointerDone(), this.pointerCancel(t, e);
  }, o.pointerCancel = function (t, e) {
    this.emitEvent("pointerCancel", [t, e]);
  }, i.getPointerPoint = function (t) {
    return {
      x: t.pageX,
      y: t.pageY
    };
  }, i;
}), function (t, e) {
  "function" == typeof define && define.amd ? define(["ev-emitter/ev-emitter", "unipointer/unipointer"], function (n, i) {
    return e(t, n, i);
  }) : "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) && module.exports ? module.exports = e(t, require("ev-emitter"), require("unipointer")) : t.Huebee = e(t, t.EvEmitter, t.Unipointer);
}(window, function (t, e, n) {
  function i(t, e) {
    if (t = h(t), !t) throw "Bad element for Huebee: " + t;
    this.anchor = t, this.options = {}, this.option(i.defaults), this.option(e), this.create();
  }
  function o() {
    for (var t = document.querySelectorAll("[data-huebee]"), e = 0; e < t.length; e++) {
      var n,
        o = t[e],
        s = o.getAttribute("data-huebee");
      try {
        n = s && JSON.parse(s);
      } catch (t) {
        C && C.error("Error parsing data-huebee on " + o.className + ": " + t);
        continue;
      }
      new i(o, n);
    }
  }
  function s(t) {
    _.clearRect(0, 0, 1, 1), _.fillStyle = "#010203", _.fillStyle = t, _.fillRect(0, 0, 1, 1);
    var e = _.getImageData(0, 0, 1, 1).data;
    if (e = [e[0], e[1], e[2], e[3]], "1,2,3,255" != e.join(",")) {
      var n = u.apply(this, e);
      return {
        color: t.trim(),
        hue: n[0],
        sat: n[1],
        lum: n[2]
      };
    }
  }
  function r(t, e) {
    for (var n in e) {
      t[n] = e[n];
    }
    return t;
  }
  function h(t) {
    return "string" == typeof t && (t = document.querySelector(t)), t;
  }
  function a(t, e, n) {
    var i = c(t, e, n);
    return d(i);
  }
  function c(t, e, n) {
    var i,
      o,
      s = (1 - Math.abs(2 * n - 1)) * e,
      r = t / 60,
      h = s * (1 - Math.abs(r % 2 - 1));
    switch (Math.floor(r)) {
      case 0:
        i = [s, h, 0];
        break;
      case 1:
        i = [h, s, 0];
        break;
      case 2:
        i = [0, s, h];
        break;
      case 3:
        i = [0, h, s];
        break;
      case 4:
        i = [h, 0, s];
        break;
      case 5:
        i = [s, 0, h];
        break;
      default:
        i = [0, 0, 0];
    }
    return o = n - s / 2, i = i.map(function (t) {
      return t + o;
    });
  }
  function u(t, e, n) {
    t /= 255, e /= 255, n /= 255;
    var i,
      o = Math.max(t, e, n),
      s = Math.min(t, e, n),
      r = o - s,
      h = .5 * (o + s),
      a = 0 === r ? 0 : r / (1 - Math.abs(2 * h - 1));
    0 === r ? i = 0 : o === t ? i = (e - n) / r % 6 : o === e ? i = (n - t) / r + 2 : o === n && (i = (t - e) / r + 4);
    var c = 60 * i;
    return [c, parseFloat(a), parseFloat(h)];
  }
  function d(t) {
    var e = t.map(function (t) {
      t = Math.round(255 * t);
      var e = t.toString(16).toUpperCase();
      return e = e.length < 2 ? "0" + e : e;
    });
    return "#" + e.join("");
  }
  function p(t) {
    return "#" + t[1] + t[3] + t[5];
  }
  i.defaults = {
    hues: 12,
    hue0: 0,
    shades: 5,
    saturations: 3,
    notation: "shortHex",
    setText: !0,
    setBGColor: !0
  };
  var f = i.prototype = Object.create(e.prototype);
  f.option = function (t) {
    this.options = r(this.options, t);
  };
  var v = 0,
    l = {};
  f.create = function () {
    function t(t) {
      t.target == i && t.preventDefault();
    }
    var e = this.guid = ++v;
    this.anchor.huebeeGUID = e, l[e] = this, this.setBGElems = this.getSetElems(this.options.setBGColor), this.setTextElems = this.getSetElems(this.options.setText), this.outsideCloseIt = this.outsideClose.bind(this), this.onDocKeydown = this.docKeydown.bind(this), this.closeIt = this.close.bind(this), this.openIt = this.open.bind(this), this.onElemTransitionend = this.elemTransitionend.bind(this), this.isInputAnchor = "INPUT" == this.anchor.nodeName, this.options.staticOpen || (this.anchor.addEventListener("click", this.openIt), this.anchor.addEventListener("focus", this.openIt)), this.isInputAnchor && this.anchor.addEventListener("input", this.inputInput.bind(this));
    var n = this.element = document.createElement("div");
    n.className = "huebee ", n.className += this.options.staticOpen ? "is-static-open " : "is-hidden ", n.className += this.options.className || "";
    var i = this.container = document.createElement("div");
    if (i.className = "huebee__container", i.addEventListener("mousedown", t), i.addEventListener("touchstart", t), this.createCanvas(), this.cursor = document.createElement("div"), this.cursor.className = "huebee__cursor is-hidden", i.appendChild(this.cursor), this.createCloseButton(), n.appendChild(i), !this.options.staticOpen) {
      var o = getComputedStyle(this.anchor.parentNode);
      "relative" != o.position && "absolute" != o.position && (this.anchor.parentNode.style.position = "relative");
    }
    var s = this.options.hues,
      r = this.options.customColors,
      h = r && r.length;
    this.satY = h ? Math.ceil(h / s) + 1 : 0, this.updateColors(), this.setAnchorColor(), this.options.staticOpen && this.open();
  }, f.getSetElems = function (t) {
    return t === !0 ? [this.anchor] : "string" == typeof t ? document.querySelectorAll(t) : void 0;
  }, f.createCanvas = function () {
    var t = this.canvas = document.createElement("canvas");
    t.className = "huebee__canvas", this.ctx = t.getContext("2d");
    var e = this.canvasPointer = new n();
    e._bindStartEvent(t), e.on("pointerDown", this.canvasPointerDown.bind(this)), e.on("pointerMove", this.canvasPointerMove.bind(this)), this.container.appendChild(t);
  };
  var m = "http://www.w3.org/2000/svg";
  f.createCloseButton = function () {
    if (!this.options.staticOpen) {
      var t = document.createElementNS(m, "svg");
      t.setAttribute("class", "huebee__close-button"), t.setAttribute("viewBox", "0 0 24 24"), t.setAttribute("width", "24"), t.setAttribute("height", "24");
      var e = document.createElementNS(m, "path");
      e.setAttribute("d", "M 7,7 L 17,17 M 17,7 L 7,17"), e.setAttribute("class", "huebee__close-button__x"), t.appendChild(e), t.addEventListener("click", this.closeIt), this.container.appendChild(t);
    }
  }, f.updateColors = function () {
    this.swatches = {}, this.colorGrid = {}, this.updateColorModer();
    var t = this.options.shades,
      e = this.options.saturations,
      n = this.options.hues,
      i = this.options.customColors;
    if (i && i.length) {
      var o = 0;
      i.forEach(function (t) {
        var e = o % n,
          i = Math.floor(o / n),
          r = s(t);
        r && (this.addSwatch(r, e, i), o++);
      }.bind(this));
    }
    for (var r = 0; r < e; r++) {
      var h = 1 - r / e,
        a = t * r + this.satY;
      this.updateSaturationGrid(r, h, a);
    }
    for (r = 0; r < t + 2; r++) {
      var c = 1 - r / (t + 1),
        u = this.colorModer(0, 0, c),
        d = s(u);
      this.addSwatch(d, n + 1, r);
    }
  }, f.updateSaturationGrid = function (t, e, n) {
    for (var i = this.options.shades, o = this.options.hues, r = this.options.hue0, h = 0; h < i; h++) {
      for (var a = 0; a < o; a++) {
        var c = Math.round(360 * a / o + r) % 360,
          u = 1 - (h + 1) / (i + 1),
          d = this.colorModer(c, e, u),
          p = s(d),
          f = h + n;
        this.addSwatch(p, a, f);
      }
    }
  }, f.addSwatch = function (t, e, n) {
    this.swatches[e + "," + n] = t, this.colorGrid[t.color.toUpperCase()] = {
      x: e,
      y: n
    };
  };
  var E = {
    hsl: function hsl(t, e, n) {
      return e = Math.round(100 * e), n = Math.round(100 * n), "hsl(" + t + ", " + e + "%, " + n + "%)";
    },
    hex: a,
    shortHex: function shortHex(t, e, n) {
      var i = a(t, e, n);
      return p(i);
    }
  };
  f.updateColorModer = function () {
    this.colorModer = E[this.options.notation] || E.shortHex;
  }, f.renderColors = function () {
    var t = 2 * this.gridSize;
    for (var e in this.swatches) {
      var n = this.swatches[e],
        i = e.split(","),
        o = i[0],
        s = i[1];
      this.ctx.fillStyle = n.color, this.ctx.fillRect(o * t, s * t, t, t);
    }
  }, f.setAnchorColor = function () {
    this.isInputAnchor && this.setColor(this.anchor.value);
  };
  var g = document.documentElement;
  f.open = function () {
    if (!this.isOpen) {
      var t = this.anchor,
        e = this.element;
      this.options.staticOpen || (e.style.left = t.offsetLeft + "px", e.style.top = t.offsetTop + t.offsetHeight + "px"), this.bindOpenEvents(!0), e.removeEventListener("transitionend", this.onElemTransitionend), t.parentNode.insertBefore(e, t.nextSibling);
      var n = getComputedStyle(e).transitionDuration;
      this.hasTransition = n && "none" != n && parseFloat(n), this.isOpen = !0, this.updateSizes(), this.renderColors(), this.setAnchorColor();
      e.offsetHeight;
      e.classList.remove("is-hidden");
    }
  }, f.bindOpenEvents = function (t) {
    if (!this.options.staticOpen) {
      var e = (t ? "add" : "remove") + "EventListener";
      g[e]("mousedown", this.outsideCloseIt), g[e]("touchstart", this.outsideCloseIt), document[e]("focusin", this.outsideCloseIt), document[e]("keydown", this.onDocKeydown), this.anchor[e]("blur", this.closeIt);
    }
  }, f.updateSizes = function () {
    var t = this.options.hues,
      e = this.options.shades,
      n = this.options.saturations;
    this.cursorBorder = parseInt(getComputedStyle(this.cursor).borderTopWidth, 10), this.gridSize = Math.round(this.cursor.offsetWidth - 2 * this.cursorBorder), this.canvasOffset = {
      x: this.canvas.offsetLeft,
      y: this.canvas.offsetTop
    };
    var i = Math.max(e * n + this.satY, e + 2),
      o = this.gridSize * (t + 2);
    this.canvas.width = 2 * o, this.canvas.style.width = o + "px", this.canvas.height = this.gridSize * i * 2;
  }, f.outsideClose = function (t) {
    var e = this.anchor.contains(t.target),
      n = this.element.contains(t.target);
    e || n || this.close();
  };
  var b = {
    13: !0,
    27: !0
  };
  f.docKeydown = function (t) {
    b[t.keyCode] && this.close();
  };
  var w = "string" == typeof g.style.transform;
  f.close = function () {
    this.isOpen && (w && this.hasTransition ? this.element.addEventListener("transitionend", this.onElemTransitionend) : this.remove(), this.element.classList.add("is-hidden"), this.bindOpenEvents(!1), this.isOpen = !1);
  }, f.remove = function () {
    var t = this.element.parentNode;
    t.contains(this.element) && t.removeChild(this.element);
  }, f.elemTransitionend = function (t) {
    t.target == this.element && (this.element.removeEventListener("transitionend", this.onElemTransitionend), this.remove());
  }, f.inputInput = function () {
    this.setColor(this.anchor.value);
  }, f.canvasPointerDown = function (t, e) {
    t.preventDefault(), this.updateOffset(), this.canvasPointerChange(e);
  }, f.updateOffset = function () {
    var e = this.canvas.getBoundingClientRect();
    this.offset = {
      x: e.left + t.pageXOffset,
      y: e.top + t.pageYOffset
    };
  }, f.canvasPointerMove = function (t, e) {
    this.canvasPointerChange(e);
  }, f.canvasPointerChange = function (t) {
    var e = Math.round(t.pageX - this.offset.x),
      n = Math.round(t.pageY - this.offset.y),
      i = this.gridSize,
      o = Math.floor(e / i),
      s = Math.floor(n / i),
      r = this.swatches[o + "," + s];
    this.setSwatch(r);
  }, f.setColor = function (t) {
    var e = s(t);
    this.setSwatch(e);
  }, f.setSwatch = function (t) {
    var e = t && t.color;
    if (t) {
      var n = e == this.color;
      this.color = e, this.hue = t.hue, this.sat = t.sat, this.lum = t.lum;
      var i = this.lum - .15 * Math.cos((this.hue + 70) / 180 * Math.PI);
      this.isLight = i > .5;
      var o = this.colorGrid[e.toUpperCase()];
      this.updateCursor(o), this.setTexts(), this.setBackgrounds(), n || this.emitEvent("change", [e, t.hue, t.sat, t.lum]);
    }
  }, f.setTexts = function () {
    if (this.setTextElems) for (var t = 0; t < this.setTextElems.length; t++) {
      var e = this.setTextElems[t],
        n = "INPUT" == e.nodeName ? "value" : "textContent";
      e[n] = this.color;
    }
  }, f.setBackgrounds = function () {
    if (this.setBGElems) for (var t = this.isLight ? "#222" : "white", e = 0; e < this.setBGElems.length; e++) {
      var n = this.setBGElems[e];
      n.style.backgroundColor = this.color, n.style.color = t;
    }
  }, f.updateCursor = function (t) {
    if (this.isOpen) {
      var e = t ? "remove" : "add";
      if (this.cursor.classList[e]("is-hidden"), t) {
        var n = this.gridSize,
          i = this.canvasOffset,
          o = this.cursorBorder;
        this.cursor.style.left = t.x * n + i.x - o + "px", this.cursor.style.top = t.y * n + i.y - o + "px";
      }
    }
  };
  var C = t.console,
    S = document.readyState;
  "complete" == S || "interactive" == S ? o() : document.addEventListener("DOMContentLoaded", o), i.data = function (t) {
    t = h(t);
    var e = t && t.huebeeGUID;
    return e && l[e];
  };
  var y = document.createElement("canvas");
  y.width = y.height = 1;
  var _ = y.getContext("2d");
  return i;
});
/* eslint-disable func-names */
$.when($.ready).then(function () {
  var base = (document.querySelector("base") || {}).href;
  var itemID = $("form[data-item-id]").data("item-id");
  var fakePassword = "*****";

  // If in edit mode and password field is present, fill it with stars
  if (itemID) {
    var passwordField = $('input[name="config[password]"]').first();
    if (passwordField.length > 0) {
      passwordField.attr("value", fakePassword);
    }
  }
  if ($(".message-container").length) {
    setTimeout(function () {
      $(".message-container").fadeOut();
    }, 3500);
  }
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $("#appimage img").attr("src", e.target.result);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#upload").change(function () {
    readURL(this);
  });
  /* $(".droppable").droppable({
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
      }); */

  var sortableEl = document.getElementById("sortable");
  var sortable;
  if (sortableEl !== null) {
    // eslint-disable-next-line no-undef
    sortable = Sortable.create(sortableEl, {
      disabled: true,
      animation: 150,
      forceFallback: !(navigator.userAgent.toLowerCase().indexOf("firefox") > -1),
      draggable: ".item-container",
      onEnd: function onEnd() {
        var idsInOrder = sortable.toArray();
        $.post("".concat(base, "order"), {
          order: idsInOrder
        });
      }
    });
    // prevent Firefox drag behavior
    if (navigator.userAgent.toLowerCase().indexOf("firefox") > -1) {
      sortable.option("setData", function (dataTransfer) {
        dataTransfer.setData("Text", "");
      });
      sortableEl.addEventListener("dragstart", function (event) {
        var target = event.target;
        if (target.nodeName.toLowerCase() === "a") {
          event.preventDefault();
          event.stopPropagation();
          event.dataTransfer.setData("Text", "");
        }
      });
    }
  }
  $("#main").on("mouseenter", "#sortable .item", function () {
    $(this).siblings(".tooltip").addClass("active");
    $(".refresh", this).addClass("active");
  }).on("mouseleave", ".item", function () {
    $(this).siblings(".tooltip").removeClass("active");
    $(".refresh", this).removeClass("active");
  });
  $("#config-buttons").on("mouseenter", "a", function () {
    $(".tooltip", this).addClass("active");
  }).on("mouseleave", "a", function () {
    $(".tooltip", this).removeClass("active");
  });
  $(".searchform > form").on("submit", function (event) {
    if ($("#search-container select[name=provider]").val() === "tiles") {
      event.preventDefault();
    }
  });

  // Autocomplete functionality
  var autocompleteTimeout = null;
  var currentAutocompleteRequest = null;
  function hideAutocomplete() {
    $("#search-autocomplete").remove();
  }
  function showAutocomplete(suggestions, inputElement) {
    hideAutocomplete();
    if (!suggestions || suggestions.length === 0) {
      return;
    }
    var $input = $(inputElement);
    var position = $input.position();
    var width = $input.outerWidth();
    var $autocomplete = $('<div id="search-autocomplete"></div>');
    suggestions.forEach(function (suggestion) {
      var $item = $('<div class="autocomplete-item"></div>').text(suggestion).on("click", function () {
        $input.val(suggestion);
        hideAutocomplete();
        $input.closest("form").submit();
      });
      $autocomplete.append($item);
    });
    $autocomplete.css({
      position: "absolute",
      top: "".concat(position.top + $input.outerHeight(), "px"),
      left: "".concat(position.left, "px"),
      width: "".concat(width, "px")
    });
    $input.closest("#search-container").append($autocomplete);
  }
  function fetchAutocomplete(query, provider) {
    // Cancel previous request if any
    if (currentAutocompleteRequest) {
      currentAutocompleteRequest.abort();
    }
    if (!query || query.trim().length < 2) {
      hideAutocomplete();
      return;
    }
    currentAutocompleteRequest = $.ajax({
      url: "".concat(base, "search/autocomplete"),
      method: "GET",
      data: {
        q: query,
        provider: provider
      },
      success: function success(data) {
        var inputElement = $("#search-container input[name=q]")[0];
        showAutocomplete(data, inputElement);
      },
      error: function error() {
        hideAutocomplete();
      },
      complete: function complete() {
        currentAutocompleteRequest = null;
      }
    });
  }
  $("#search-container").on("input", "input[name=q]", function () {
    var search = this.value;
    var items = $("#sortable").find(".item-container");
    // Get provider from either select or hidden input
    var provider = $("#search-container select[name=provider]").val() || $("#search-container input[name=provider]").val();
    if (provider === "tiles") {
      hideAutocomplete();
      if (search.length > 0) {
        items.hide();
        items.filter(function () {
          var name = $(this).data("name").toLowerCase();
          return name.includes(search.toLowerCase());
        }).show();
      } else {
        items.show();
      }
    } else {
      items.show();

      // Debounce autocomplete requests
      clearTimeout(autocompleteTimeout);
      autocompleteTimeout = setTimeout(function () {
        fetchAutocomplete(search, provider);
      }, 300);
    }
  }).on("change", "select[name=provider]", function () {
    var items = $("#sortable").find(".item-container");
    if ($(this).val() === "tiles") {
      $("#search-container button").hide();
      var search = $("#search-container input[name=q]").val();
      if (search.length > 0) {
        items.hide();
        items.filter(function () {
          var name = $(this).data("name").toLowerCase();
          return name.includes(search.toLowerCase());
        }).show();
      } else {
        items.show();
      }
    } else {
      $("#search-container button").show();
      items.show();
      hideAutocomplete();
    }
  });

  // Hide autocomplete when clicking outside
  $(document).on("click", function (e) {
    if (!$(e.target).closest("#search-container").length) {
      hideAutocomplete();
    }
  });

  // Hide autocomplete on Escape key
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      hideAutocomplete();
    }
  });
  $("#search-container select[name=provider]").trigger("change");
  $("#app").on("click", "#config-button", function (e) {
    e.preventDefault();
    var app = $("#app");
    var active = app.hasClass("header");
    app.toggleClass("header");
    if (active) {
      $(".add-item").hide();
      $(".item-edit").hide();
      $("#app").removeClass("sidebar");
      $("#sortable .tooltip").css("display", "");
      if (sortable !== undefined) sortable.option("disabled", true);
    } else {
      $("#sortable .tooltip").css("display", "none");
      if (sortable !== undefined) sortable.option("disabled", false);
      setTimeout(function () {
        $(".add-item").fadeIn();
        $(".item-edit").fadeIn();
      }, 350);
    }
  }).on("click", ".tag", function (e) {
    e.preventDefault();
    var tag = $(e.target).data("tag");
    $("#taglist .tag").removeClass("current");
    $(e.target).addClass("current");
    $("#sortable .item-container").show();
    if (tag !== "all") {
      $("#sortable .item-container:not(.".concat(tag, ")")).hide();
    }
  }).on("click", "#add-item, #pin-item", function (e) {
    e.preventDefault();
    var app = $("#app");
    // const active = app.hasClass("sidebar");
    app.toggleClass("sidebar");
  }).on("click", ".close-sidenav", function (e) {
    e.preventDefault();
    var app = $("#app");
    app.removeClass("sidebar");
  }).on("click", "#test_config", function (e) {
    e.preventDefault();
    var apiurl = $("#create input[name=url]").val();
    var overrideUrl = $('#sapconfig input[name="config[override_url]"]').val();
    if (typeof overrideUrl === "string" && overrideUrl !== "") {
      apiurl = overrideUrl;
    }
    var data = {};
    data.url = apiurl;
    $(".config-item").each(function () {
      var config = $(this).data("config");
      data[config] = $(this).val();
    });
    data.id = $("form[data-item-id]").data("item-id");
    if (data.password && data.password === fakePassword) {
      data.password = "";
    }
    $.post("".concat(base, "test_config"), {
      data: data
    }).done(function (responseData) {
      // eslint-disable-next-line no-alert
      alert(responseData);
    }).fail(function (responseData) {
      // eslint-disable-next-line no-alert
      alert("Something went wrong: ".concat(responseData.responseText.substring(0, 100)));
    });
  });
  // Auto-select the configured default tag on load (tags mode only)
  var taglist = document.getElementById("taglist");
  if (taglist !== null) {
    var defaultTag = taglist.getAttribute("data-default-tag");
    if (typeof defaultTag === "string" && defaultTag !== "") {
      $("#taglist .tag[data-tag=\"tag-".concat(defaultTag, "\"]")).trigger("click");
    }
  }
  $("#pinlist").on("click", "a", function (e) {
    e.preventDefault();
    var current = $(this);
    var id = current.data("id");
    var tag = current.data("tag");
    $.get("".concat(base, "items/pintoggle/").concat(id, "/true/").concat(tag), function (data) {
      var inner = $(data).filter("#sortable").html();
      $("#sortable").html(inner);
      current.toggleClass("active");
    });
  });
  $("#itemform").on("submit", function () {
    var passwordField = $('input[name="config[password]"]').first();
    if (passwordField.length > 0) {
      if (passwordField.attr("value") === fakePassword) {
        passwordField.attr("value", "");
      }
    }
  });
});
var focusSearch = function focusSearch(event) {
  var searchInput = document.querySelector('input[name="q"]');
  if (searchInput) {
    event.preventDefault();
    searchInput.focus();
  }
};
var openFirstNonHiddenItem = function openFirstNonHiddenItem(event) {
  if (event.target !== document.querySelector('input[name="q"]')) {
    return;
  }
  var providerSelect = document.querySelector("#search-container select[name=provider]");
  if (providerSelect.value !== "tiles") {
    return;
  }
  var item = document.querySelector('#sortable section.item-container:not([style="display: none;"]) a');
  if ("href" in item) {
    event.preventDefault();
    window.open(item.href);
  }
};
var KEY_BINDINGS = {
  "/": focusSearch,
  Enter: openFirstNonHiddenItem
};
document.addEventListener("keydown", function (event) {
  try {
    if (event.key in KEY_BINDINGS) {
      KEY_BINDINGS[event.key](event);
    }
  } catch (e) {
    // Nothing to do
  }
});
var EXPORT_FILE_NAME = "HeimdallExport.json";
var EXPORT_API_URL = "api/item";

/**
 *
 * @param {string} fileName
 * @param {string} data
 */
function triggerFileDownload(fileName, data) {
  var a = document.createElement("a");
  var file = new Blob([data], {
    type: "text/plain"
  });
  a.href = URL.createObjectURL(file);
  a.download = EXPORT_FILE_NAME;
  a.click();
}

/**
 *
 * @param {Event} event
 */
var exportItems = function exportItems(event) {
  event.preventDefault();
  fetch(EXPORT_API_URL).then(function (response) {
    if (response.status !== 200) {
      // eslint-disable-next-line no-alert
      window.alert("An error occurred while exporting...");
    }
    return response.json();
  }).then(function (data) {
    var exportedJson = JSON.stringify(data, null, 2);
    triggerFileDownload(EXPORT_FILE_NAME, exportedJson);
  });
};
var exportButton = document.querySelector("#item-export");
if (exportButton) {
  exportButton.addEventListener("click", exportItems);
}
var IMPORT_API_URL = "api/item";
var APP_LOAD_URL = "appload";

/**
 *
 * @param {object} item
 * @param {array} errors
 */
var updateStatus = function updateStatus(_ref) {
  var item = _ref.item,
    errors = _ref.errors;
  // eslint-disable-next-line no-console
  console.log(item, errors);
  var statusLine;
  if (errors.length === 0) {
    statusLine = "<li class=\"success\"><i class=\"fas fa-circle-check\"></i> Imported: ".concat(item.title, " </li>");
  } else {
    statusLine = "<li class=\"fail\"><i class=\"fas fa-circle-xmark\"></i> Failed: ".concat(item.title, " - ").concat(errors[0], " </li>");
  }
  document.querySelector(".import-status").innerHTML += statusLine;
};

/**
 *
 */
function clearStatus() {
  var statusContainer = document.querySelector(".import-status");
  statusContainer.innerHTML = "";
}

/**
 *
 * @param {object} data
 * @param {string} csrfToken
 */
var postToApi = function postToApi(data, csrfToken) {
  return fetch(IMPORT_API_URL, {
    method: "POST",
    cache: "no-cache",
    redirect: "follow",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": csrfToken
    },
    body: JSON.stringify(data)
  });
};

/**
 *
 * @returns {string}
 */
var getCSRFToken = function getCSRFToken() {
  var tokenSelector = 'input[name="_token"]';
  return document.querySelector(tokenSelector).value;
};

/**
 *
 * @param {object} item
 * @param {object} appDetails
 * @returns {object}
 */
var mergeItemWithAppDetails = function mergeItemWithAppDetails(item, appDetails) {
  return {
    pinned: 1,
    tags: Array.isArray(item.tags) && item.tags.length ? item.tags : [0],
    appid: item.appid,
    title: item.title,
    colour: item.colour,
    url: item.url,
    appdescription: item.appdescription ? item.appdescription : appDetails.description,
    website: appDetails.website,
    icon: appDetails.iconview,
    config: item.description ? JSON.parse(item.description) : null
  };
};

/**
 *
 * @param {string|null} appId
 * @returns {Promise<{}>|Promise<any>}
 */
var fetchAppDetails = function fetchAppDetails(appId) {
  if (appId === null || appId === "null") {
    return Promise.resolve({});
  }
  return fetch(APP_LOAD_URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      app: appId
    })
  }).then(function (response) {
    if (!response.ok) {
      return Promise.reject(new Error("Failed to find app id: ".concat(appId)));
    }
    return response.json();
  });
};

/**
 *
 * @param {array} items
 */
var importItems = function importItems(items) {
  items.forEach(function (item) {
    var errors = [];
    fetchAppDetails(item.appid)["catch"](function () {
      return errors.push(new Error("Failed to find app id: ".concat(item.appid)));
    }).then(function (appDetails) {
      var itemWithAppDetails = mergeItemWithAppDetails(item, appDetails);
      var csrfToken = getCSRFToken();
      return postToApi(itemWithAppDetails, csrfToken);
    })["catch"](function () {
      return errors.push(new Error("Failed to create item: ".concat(item.title)));
    })["finally"](function () {
      updateStatus({
        item: item,
        errors: errors
      });
    });
  });
};

/**
 *
 * @param {Blob} file
 * @returns {Promise<unknown>}
 */
var readJSON = function readJSON(file) {
  return new Promise(function (resolve, reject) {
    try {
      var reader = new FileReader();
      reader.onload = function (event) {
        var contents = event.target.result;
        resolve(JSON.parse(contents));
      };
      reader.readAsText(file);
    } catch (e) {
      reject(new Error("Unable to read file"));
    }
  });
};

/**
 *
 * @param {Blob} file
 */
var openFileForImport = function openFileForImport(file) {
  clearStatus();
  return readJSON(file)["catch"](function (error) {
    // eslint-disable-next-line no-console
    console.error(error);
  }).then(importItems);
};
var fileInput = document.querySelector("input[name='import']");
var importButtons = document.querySelectorAll(".import-button");
if (fileInput && importButtons) {
  importButtons.forEach(function (importButton) {
    importButton.addEventListener("click", function () {
      var file = fileInput.files[0];
      if (!file) {
        return;
      }
      openFileForImport(file);
    });
  });
  fileInput.addEventListener("change", openFileForImport, false);
}
var REFRESH_INTERVAL_SMALL = 5000;
var REFRESH_INTERVAL_BIG = 30000;
var QUEUE_PROCESSING_INTERVAL = 1000;
var CONTAINER_SELECTOR = ".livestats-container";

/**
 * @returns {*[]}
 */
function createQueue() {
  var queue = [];
  var suspended = false;
  function processQueue() {
    if (queue.length === 0 || suspended === true) {
      return;
    }
    var next = queue.shift();
    next();
  }
  document.addEventListener("visibilitychange", function () {
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
  if (dataOnly) {
    return REFRESH_INTERVAL_BIG;
  }
  if (active) {
    return REFRESH_INTERVAL_SMALL;
  }
  return REFRESH_INTERVAL_BIG;
}

/**
 * @param {HTMLElement} container
 * @param {array} queue
 * @returns {function(): Promise<Response>}
 */
function createUpdateJob(container, queue) {
  var id = container.getAttribute("data-id");
  // Data only attribute seems to indicate that the item should not be updated that often
  var isDataOnly = container.getAttribute("data-dataonly") === "1";
  return function () {
    return fetch("get_stats/".concat(id)).then(function (response) {
      if (response.ok) {
        return response.json();
      }
      throw new Error("Network response was not ok: ".concat(response.status));
    }).then(function (data) {
      // eslint-disable-next-line no-param-reassign
      container.innerHTML = data.html;
      var isActive = data.status === "active";
      if (queue) {
        setTimeout(function () {
          queue.push(createUpdateJob(container, queue));
        }, getQueueInterval(isDataOnly, isActive));
      }
    })["catch"](function (error) {
      // eslint-disable-next-line no-console
      console.error(error);
    });
  };
}
var livestatContainers = getContainers();
if (livestatContainers.length > 0) {
  var myQueue = createQueue();
  livestatContainers.forEach(function (container) {
    createUpdateJob(container, myQueue)();
  });
}

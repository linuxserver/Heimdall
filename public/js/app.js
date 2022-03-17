function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

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
$.when($.ready).then(function () {
  var base = (document.querySelector('base') || {}).href;

  if ($('.message-container').length) {
    setTimeout(function () {
      $('.message-container').fadeOut();
    }, 3500);
  } // from https://developer.mozilla.org/en-US/docs/Web/API/Page_Visibility_API
  // Set the name of the hidden property and the change event for visibility


  var hidden, visibilityChange;

  if (typeof document.hidden !== "undefined") {
    // Opera 12.10 and Firefox 18 and later support 
    hidden = "hidden";
    visibilityChange = "visibilitychange";
  } else if (typeof document.msHidden !== "undefined") {
    hidden = "msHidden";
    visibilityChange = "msvisibilitychange";
  } else if (typeof document.webkitHidden !== "undefined") {
    hidden = "webkitHidden";
    visibilityChange = "webkitvisibilitychange";
  }

  var livestatsRefreshTimeouts = [];
  var livestatsFuncs = [];
  var livestatsContainers = $('.livestats-container');

  function stopLivestatsRefresh() {
    for (var _i = 0, _livestatsRefreshTime = livestatsRefreshTimeouts; _i < _livestatsRefreshTime.length; _i++) {
      var timeoutId = _livestatsRefreshTime[_i];
      window.clearTimeout(timeoutId);
    }
  }

  function startLivestatsRefresh() {
    for (var _i2 = 0, _livestatsFuncs = livestatsFuncs; _i2 < _livestatsFuncs.length; _i2++) {
      var fun = _livestatsFuncs[_i2];
      fun();
    }
  }

  if (livestatsContainers.length > 0) {
    if (typeof document.addEventListener === "undefined" || hidden === undefined) {
      console.log("This browser does not support visibilityChange");
    } else {
      document.addEventListener(visibilityChange, function () {
        if (document[hidden]) {
          stopLivestatsRefresh();
        } else {
          startLivestatsRefresh();
        }
      }, false);
    }

    livestatsContainers.each(function (index) {
      var id = $(this).data('id');
      var dataonly = $(this).data('dataonly');
      var increaseby = dataonly == 1 ? 20000 : 1000;
      var container = $(this);
      var max_timer = 30000;
      var timer = 5000;

      var fun = function worker() {
        $.ajax({
          url: base + 'get_stats/' + id,
          dataType: 'json',
          success: function success(data) {
            container.html(data.html);
            if (data.status == 'active') timer = increaseby;else {
              if (timer < max_timer) timer += 2000;
            }
          },
          complete: function complete() {
            // Schedule the next request when the current one's complete
            livestatsRefreshTimeouts[index] = window.setTimeout(worker, timer);
          }
        });
      };

      livestatsFuncs[index] = fun;
      fun();
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
      $.post(base + 'order', {
        order: idsInOrder
      });
    }
  });
  $('#sortable').sortable('disable');
  $('#sortable').on('mouseenter', '.item', function () {
    $(this).siblings('.tooltip').addClass('active');
    $('.refresh', this).addClass('active');
  }).on('mouseleave', '.item', function () {
    $(this).siblings('.tooltip').removeClass('active');
    $('.refresh', this).removeClass('active');
  });
  $('#search-container').on('input', 'input[name=q]', function () {
    var search = this.value;
    var items = $('#sortable').children('.item-container');

    if ($('#search-container select[name=provider]').val() === 'tiles') {
      if (search.length > 0) {
        items.hide();
        items.filter(function () {
          var name = $(this).data('name').toLowerCase();
          return name.includes(search.toLowerCase());
        }).show();
      } else {
        items.show();
      }
    } else {
      items.show();
    }
  }).on('change', 'select[name=provider]', function () {
    var items = $('#sortable').children('.item-container');

    if ($(this).val() === 'tiles') {
      $('#search-container button').hide();
      var search = $('#search-container input[name=q]').val();

      if (search.length > 0) {
        items.hide();
        items.filter(function () {
          var name = $(this).data('name').toLowerCase();
          return name.includes(search.toLowerCase());
        }).show();
      } else {
        items.show();
      }
    } else {
      $('#search-container button').show();
      items.show();
    }
  });
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
    var override_url = $('#sapconfig input[name="config[override_url]"]').val();

    if (override_url.length && override_url != '') {
      apiurl = override_url;
    }

    var data = {};
    data['url'] = apiurl;
    $('.config-item').each(function (index) {
      var config = $(this).data('config');
      data[config] = $(this).val();
    });
    $.post(base + 'test_config', {
      data: data
    }, function (data) {
      alert(data);
    });
  });
  $('#pinlist').on('click', 'a', function (e) {
    e.preventDefault();
    var current = $(this);
    var id = current.data('id');
    var tag = current.data('tag');
    $.get(base + 'items/pintoggle/' + id + '/true/' + tag, function (data) {
      var inner = $(data).filter('#sortable').html();
      $('#sortable').html(inner);
      current.toggleClass('active');
    });
  });
});

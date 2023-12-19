/* @license MIT https://raw.githubusercontent.com/jquery/jquery/3.7.0/LICENSE.txt */
/*! jQuery v3.7.0 | (c) OpenJS Foundation and other contributors | jquery.org/license */
!(function (e, t) {
  "use strict";
  "object" == typeof module && "object" == typeof module.exports
    ? (module.exports = e.document
        ? t(e, !0)
        : function (e) {
            if (!e.document)
              throw new Error("jQuery requires a window with a document");
            return t(e);
          })
    : t(e);
})("undefined" != typeof window ? window : this, function (ie, e) {
  "use strict";
  var oe = [],
    r = Object.getPrototypeOf,
    ae = oe.slice,
    g = oe.flat
      ? function (e) {
          return oe.flat.call(e);
        }
      : function (e) {
          return oe.concat.apply([], e);
        },
    s = oe.push,
    se = oe.indexOf,
    n = {},
    i = n.toString,
    ue = n.hasOwnProperty,
    o = ue.toString,
    a = o.call(Object),
    le = {},
    v = function (e) {
      return (
        "function" == typeof e &&
        "number" != typeof e.nodeType &&
        "function" != typeof e.item
      );
    },
    y = function (e) {
      return null != e && e === e.window;
    },
    C = ie.document,
    u = { type: !0, src: !0, nonce: !0, noModule: !0 };
  function m(e, t, n) {
    var r,
      i,
      o = (n = n || C).createElement("script");
    if (((o.text = e), t))
      for (r in u)
        (i = t[r] || (t.getAttribute && t.getAttribute(r))) &&
          o.setAttribute(r, i);
    n.head.appendChild(o).parentNode.removeChild(o);
  }
  function x(e) {
    return null == e
      ? e + ""
      : "object" == typeof e || "function" == typeof e
      ? n[i.call(e)] || "object"
      : typeof e;
  }
  var t = "3.7.0",
    l = /HTML$/i,
    ce = function (e, t) {
      return new ce.fn.init(e, t);
    };
  function c(e) {
    var t = !!e && "length" in e && e.length,
      n = x(e);
    return (
      !v(e) &&
      !y(e) &&
      ("array" === n ||
        0 === t ||
        ("number" == typeof t && 0 < t && t - 1 in e))
    );
  }
  function fe(e, t) {
    return e.nodeName && e.nodeName.toLowerCase() === t.toLowerCase();
  }
  (ce.fn = ce.prototype =
    {
      jquery: t,
      constructor: ce,
      length: 0,
      toArray: function () {
        return ae.call(this);
      },
      get: function (e) {
        return null == e
          ? ae.call(this)
          : e < 0
          ? this[e + this.length]
          : this[e];
      },
      pushStack: function (e) {
        var t = ce.merge(this.constructor(), e);
        return (t.prevObject = this), t;
      },
      each: function (e) {
        return ce.each(this, e);
      },
      map: function (n) {
        return this.pushStack(
          ce.map(this, function (e, t) {
            return n.call(e, t, e);
          })
        );
      },
      slice: function () {
        return this.pushStack(ae.apply(this, arguments));
      },
      first: function () {
        return this.eq(0);
      },
      last: function () {
        return this.eq(-1);
      },
      even: function () {
        return this.pushStack(
          ce.grep(this, function (e, t) {
            return (t + 1) % 2;
          })
        );
      },
      odd: function () {
        return this.pushStack(
          ce.grep(this, function (e, t) {
            return t % 2;
          })
        );
      },
      eq: function (e) {
        var t = this.length,
          n = +e + (e < 0 ? t : 0);
        return this.pushStack(0 <= n && n < t ? [this[n]] : []);
      },
      end: function () {
        return this.prevObject || this.constructor();
      },
      push: s,
      sort: oe.sort,
      splice: oe.splice,
    }),
    (ce.extend = ce.fn.extend =
      function () {
        var e,
          t,
          n,
          r,
          i,
          o,
          a = arguments[0] || {},
          s = 1,
          u = arguments.length,
          l = !1;
        for (
          "boolean" == typeof a && ((l = a), (a = arguments[s] || {}), s++),
            "object" == typeof a || v(a) || (a = {}),
            s === u && ((a = this), s--);
          s < u;
          s++
        )
          if (null != (e = arguments[s]))
            for (t in e)
              (r = e[t]),
                "__proto__" !== t &&
                  a !== r &&
                  (l && r && (ce.isPlainObject(r) || (i = Array.isArray(r)))
                    ? ((n = a[t]),
                      (o =
                        i && !Array.isArray(n)
                          ? []
                          : i || ce.isPlainObject(n)
                          ? n
                          : {}),
                      (i = !1),
                      (a[t] = ce.extend(l, o, r)))
                    : void 0 !== r && (a[t] = r));
        return a;
      }),
    ce.extend({
      expando: "jQuery" + (t + Math.random()).replace(/\D/g, ""),
      isReady: !0,
      error: function (e) {
        throw new Error(e);
      },
      noop: function () {},
      isPlainObject: function (e) {
        var t, n;
        return (
          !(!e || "[object Object]" !== i.call(e)) &&
          (!(t = r(e)) ||
            ("function" ==
              typeof (n = ue.call(t, "constructor") && t.constructor) &&
              o.call(n) === a))
        );
      },
      isEmptyObject: function (e) {
        var t;
        for (t in e) return !1;
        return !0;
      },
      globalEval: function (e, t, n) {
        m(e, { nonce: t && t.nonce }, n);
      },
      each: function (e, t) {
        var n,
          r = 0;
        if (c(e)) {
          for (n = e.length; r < n; r++)
            if (!1 === t.call(e[r], r, e[r])) break;
        } else for (r in e) if (!1 === t.call(e[r], r, e[r])) break;
        return e;
      },
      text: function (e) {
        var t,
          n = "",
          r = 0,
          i = e.nodeType;
        if (i) {
          if (1 === i || 9 === i || 11 === i) return e.textContent;
          if (3 === i || 4 === i) return e.nodeValue;
        } else while ((t = e[r++])) n += ce.text(t);
        return n;
      },
      makeArray: function (e, t) {
        var n = t || [];
        return (
          null != e &&
            (c(Object(e))
              ? ce.merge(n, "string" == typeof e ? [e] : e)
              : s.call(n, e)),
          n
        );
      },
      inArray: function (e, t, n) {
        return null == t ? -1 : se.call(t, e, n);
      },
      isXMLDoc: function (e) {
        var t = e && e.namespaceURI,
          n = e && (e.ownerDocument || e).documentElement;
        return !l.test(t || (n && n.nodeName) || "HTML");
      },
      merge: function (e, t) {
        for (var n = +t.length, r = 0, i = e.length; r < n; r++) e[i++] = t[r];
        return (e.length = i), e;
      },
      grep: function (e, t, n) {
        for (var r = [], i = 0, o = e.length, a = !n; i < o; i++)
          !t(e[i], i) !== a && r.push(e[i]);
        return r;
      },
      map: function (e, t, n) {
        var r,
          i,
          o = 0,
          a = [];
        if (c(e))
          for (r = e.length; o < r; o++)
            null != (i = t(e[o], o, n)) && a.push(i);
        else for (o in e) null != (i = t(e[o], o, n)) && a.push(i);
        return g(a);
      },
      guid: 1,
      support: le,
    }),
    "function" == typeof Symbol &&
      (ce.fn[Symbol.iterator] = oe[Symbol.iterator]),
    ce.each(
      "Boolean Number String Function Array Date RegExp Object Error Symbol".split(
        " "
      ),
      function (e, t) {
        n["[object " + t + "]"] = t.toLowerCase();
      }
    );
  var pe = oe.pop,
    de = oe.sort,
    he = oe.splice,
    ge = "[\\x20\\t\\r\\n\\f]",
    ve = new RegExp("^" + ge + "+|((?:^|[^\\\\])(?:\\\\.)*)" + ge + "+$", "g");
  ce.contains = function (e, t) {
    var n = t && t.parentNode;
    return (
      e === n ||
      !(
        !n ||
        1 !== n.nodeType ||
        !(e.contains
          ? e.contains(n)
          : e.compareDocumentPosition && 16 & e.compareDocumentPosition(n))
      )
    );
  };
  var f = /([\0-\x1f\x7f]|^-?\d)|^-$|[^\x80-\uFFFF\w-]/g;
  function p(e, t) {
    return t
      ? "\0" === e
        ? "\ufffd"
        : e.slice(0, -1) + "\\" + e.charCodeAt(e.length - 1).toString(16) + " "
      : "\\" + e;
  }
  ce.escapeSelector = function (e) {
    return (e + "").replace(f, p);
  };
  var ye = C,
    me = s;
  !(function () {
    var e,
      b,
      w,
      o,
      a,
      T,
      r,
      C,
      d,
      i,
      k = me,
      S = ce.expando,
      E = 0,
      n = 0,
      s = W(),
      c = W(),
      u = W(),
      h = W(),
      l = function (e, t) {
        return e === t && (a = !0), 0;
      },
      f =
        "checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",
      t =
        "(?:\\\\[\\da-fA-F]{1,6}" +
        ge +
        "?|\\\\[^\\r\\n\\f]|[\\w-]|[^\0-\\x7f])+",
      p =
        "\\[" +
        ge +
        "*(" +
        t +
        ")(?:" +
        ge +
        "*([*^$|!~]?=)" +
        ge +
        "*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|(" +
        t +
        "))|)" +
        ge +
        "*\\]",
      g =
        ":(" +
        t +
        ")(?:\\((('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|((?:\\\\.|[^\\\\()[\\]]|" +
        p +
        ")*)|.*)\\)|)",
      v = new RegExp(ge + "+", "g"),
      y = new RegExp("^" + ge + "*," + ge + "*"),
      m = new RegExp("^" + ge + "*([>+~]|" + ge + ")" + ge + "*"),
      x = new RegExp(ge + "|>"),
      j = new RegExp(g),
      A = new RegExp("^" + t + "$"),
      D = {
        ID: new RegExp("^#(" + t + ")"),
        CLASS: new RegExp("^\\.(" + t + ")"),
        TAG: new RegExp("^(" + t + "|[*])"),
        ATTR: new RegExp("^" + p),
        PSEUDO: new RegExp("^" + g),
        CHILD: new RegExp(
          "^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" +
            ge +
            "*(even|odd|(([+-]|)(\\d*)n|)" +
            ge +
            "*(?:([+-]|)" +
            ge +
            "*(\\d+)|))" +
            ge +
            "*\\)|)",
          "i"
        ),
        bool: new RegExp("^(?:" + f + ")$", "i"),
        needsContext: new RegExp(
          "^" +
            ge +
            "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" +
            ge +
            "*((?:-\\d)?\\d*)" +
            ge +
            "*\\)|)(?=[^-]|$)",
          "i"
        ),
      },
      N = /^(?:input|select|textarea|button)$/i,
      q = /^h\d$/i,
      L = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,
      H = /[+~]/,
      O = new RegExp("\\\\[\\da-fA-F]{1,6}" + ge + "?|\\\\([^\\r\\n\\f])", "g"),
      P = function (e, t) {
        var n = "0x" + e.slice(1) - 65536;
        return (
          t ||
          (n < 0
            ? String.fromCharCode(n + 65536)
            : String.fromCharCode((n >> 10) | 55296, (1023 & n) | 56320))
        );
      },
      R = function () {
        V();
      },
      M = J(
        function (e) {
          return !0 === e.disabled && fe(e, "fieldset");
        },
        { dir: "parentNode", next: "legend" }
      );
    try {
      k.apply((oe = ae.call(ye.childNodes)), ye.childNodes),
        oe[ye.childNodes.length].nodeType;
    } catch (e) {
      k = {
        apply: function (e, t) {
          me.apply(e, ae.call(t));
        },
        call: function (e) {
          me.apply(e, ae.call(arguments, 1));
        },
      };
    }
    function I(t, e, n, r) {
      var i,
        o,
        a,
        s,
        u,
        l,
        c,
        f = e && e.ownerDocument,
        p = e ? e.nodeType : 9;
      if (
        ((n = n || []),
        "string" != typeof t || !t || (1 !== p && 9 !== p && 11 !== p))
      )
        return n;
      if (!r && (V(e), (e = e || T), C)) {
        if (11 !== p && (u = L.exec(t)))
          if ((i = u[1])) {
            if (9 === p) {
              if (!(a = e.getElementById(i))) return n;
              if (a.id === i) return k.call(n, a), n;
            } else if (
              f &&
              (a = f.getElementById(i)) &&
              I.contains(e, a) &&
              a.id === i
            )
              return k.call(n, a), n;
          } else {
            if (u[2]) return k.apply(n, e.getElementsByTagName(t)), n;
            if ((i = u[3]) && e.getElementsByClassName)
              return k.apply(n, e.getElementsByClassName(i)), n;
          }
        if (!(h[t + " "] || (d && d.test(t)))) {
          if (((c = t), (f = e), 1 === p && (x.test(t) || m.test(t)))) {
            ((f = (H.test(t) && z(e.parentNode)) || e) == e && le.scope) ||
              ((s = e.getAttribute("id"))
                ? (s = ce.escapeSelector(s))
                : e.setAttribute("id", (s = S))),
              (o = (l = Y(t)).length);
            while (o--) l[o] = (s ? "#" + s : ":scope") + " " + Q(l[o]);
            c = l.join(",");
          }
          try {
            return k.apply(n, f.querySelectorAll(c)), n;
          } catch (e) {
            h(t, !0);
          } finally {
            s === S && e.removeAttribute("id");
          }
        }
      }
      return re(t.replace(ve, "$1"), e, n, r);
    }
    function W() {
      var r = [];
      return function e(t, n) {
        return (
          r.push(t + " ") > b.cacheLength && delete e[r.shift()],
          (e[t + " "] = n)
        );
      };
    }
    function F(e) {
      return (e[S] = !0), e;
    }
    function $(e) {
      var t = T.createElement("fieldset");
      try {
        return !!e(t);
      } catch (e) {
        return !1;
      } finally {
        t.parentNode && t.parentNode.removeChild(t), (t = null);
      }
    }
    function B(t) {
      return function (e) {
        return fe(e, "input") && e.type === t;
      };
    }
    function _(t) {
      return function (e) {
        return (fe(e, "input") || fe(e, "button")) && e.type === t;
      };
    }
    function X(t) {
      return function (e) {
        return "form" in e
          ? e.parentNode && !1 === e.disabled
            ? "label" in e
              ? "label" in e.parentNode
                ? e.parentNode.disabled === t
                : e.disabled === t
              : e.isDisabled === t || (e.isDisabled !== !t && M(e) === t)
            : e.disabled === t
          : "label" in e && e.disabled === t;
      };
    }
    function U(a) {
      return F(function (o) {
        return (
          (o = +o),
          F(function (e, t) {
            var n,
              r = a([], e.length, o),
              i = r.length;
            while (i--) e[(n = r[i])] && (e[n] = !(t[n] = e[n]));
          })
        );
      });
    }
    function z(e) {
      return e && "undefined" != typeof e.getElementsByTagName && e;
    }
    function V(e) {
      var t,
        n = e ? e.ownerDocument || e : ye;
      return (
        n != T &&
          9 === n.nodeType &&
          n.documentElement &&
          ((r = (T = n).documentElement),
          (C = !ce.isXMLDoc(T)),
          (i = r.matches || r.webkitMatchesSelector || r.msMatchesSelector),
          ye != T &&
            (t = T.defaultView) &&
            t.top !== t &&
            t.addEventListener("unload", R),
          (le.getById = $(function (e) {
            return (
              (r.appendChild(e).id = ce.expando),
              !T.getElementsByName || !T.getElementsByName(ce.expando).length
            );
          })),
          (le.disconnectedMatch = $(function (e) {
            return i.call(e, "*");
          })),
          (le.scope = $(function () {
            return T.querySelectorAll(":scope");
          })),
          (le.cssHas = $(function () {
            try {
              return T.querySelector(":has(*,:jqfake)"), !1;
            } catch (e) {
              return !0;
            }
          })),
          le.getById
            ? ((b.filter.ID = function (e) {
                var t = e.replace(O, P);
                return function (e) {
                  return e.getAttribute("id") === t;
                };
              }),
              (b.find.ID = function (e, t) {
                if ("undefined" != typeof t.getElementById && C) {
                  var n = t.getElementById(e);
                  return n ? [n] : [];
                }
              }))
            : ((b.filter.ID = function (e) {
                var n = e.replace(O, P);
                return function (e) {
                  var t =
                    "undefined" != typeof e.getAttributeNode &&
                    e.getAttributeNode("id");
                  return t && t.value === n;
                };
              }),
              (b.find.ID = function (e, t) {
                if ("undefined" != typeof t.getElementById && C) {
                  var n,
                    r,
                    i,
                    o = t.getElementById(e);
                  if (o) {
                    if ((n = o.getAttributeNode("id")) && n.value === e)
                      return [o];
                    (i = t.getElementsByName(e)), (r = 0);
                    while ((o = i[r++]))
                      if ((n = o.getAttributeNode("id")) && n.value === e)
                        return [o];
                  }
                  return [];
                }
              })),
          (b.find.TAG = function (e, t) {
            return "undefined" != typeof t.getElementsByTagName
              ? t.getElementsByTagName(e)
              : t.querySelectorAll(e);
          }),
          (b.find.CLASS = function (e, t) {
            if ("undefined" != typeof t.getElementsByClassName && C)
              return t.getElementsByClassName(e);
          }),
          (d = []),
          $(function (e) {
            var t;
            (r.appendChild(e).innerHTML =
              "<a id='" +
              S +
              "' href='' disabled='disabled'></a><select id='" +
              S +
              "-\r\\' disabled='disabled'><option selected=''></option></select>"),
              e.querySelectorAll("[selected]").length ||
                d.push("\\[" + ge + "*(?:value|" + f + ")"),
              e.querySelectorAll("[id~=" + S + "-]").length || d.push("~="),
              e.querySelectorAll("a#" + S + "+*").length || d.push(".#.+[+~]"),
              e.querySelectorAll(":checked").length || d.push(":checked"),
              (t = T.createElement("input")).setAttribute("type", "hidden"),
              e.appendChild(t).setAttribute("name", "D"),
              (r.appendChild(e).disabled = !0),
              2 !== e.querySelectorAll(":disabled").length &&
                d.push(":enabled", ":disabled"),
              (t = T.createElement("input")).setAttribute("name", ""),
              e.appendChild(t),
              e.querySelectorAll("[name='']").length ||
                d.push("\\[" + ge + "*name" + ge + "*=" + ge + "*(?:''|\"\")");
          }),
          le.cssHas || d.push(":has"),
          (d = d.length && new RegExp(d.join("|"))),
          (l = function (e, t) {
            if (e === t) return (a = !0), 0;
            var n = !e.compareDocumentPosition - !t.compareDocumentPosition;
            return (
              n ||
              (1 &
                (n =
                  (e.ownerDocument || e) == (t.ownerDocument || t)
                    ? e.compareDocumentPosition(t)
                    : 1) ||
              (!le.sortDetached && t.compareDocumentPosition(e) === n)
                ? e === T || (e.ownerDocument == ye && I.contains(ye, e))
                  ? -1
                  : t === T || (t.ownerDocument == ye && I.contains(ye, t))
                  ? 1
                  : o
                  ? se.call(o, e) - se.call(o, t)
                  : 0
                : 4 & n
                ? -1
                : 1)
            );
          })),
        T
      );
    }
    for (e in ((I.matches = function (e, t) {
      return I(e, null, null, t);
    }),
    (I.matchesSelector = function (e, t) {
      if ((V(e), C && !h[t + " "] && (!d || !d.test(t))))
        try {
          var n = i.call(e, t);
          if (
            n ||
            le.disconnectedMatch ||
            (e.document && 11 !== e.document.nodeType)
          )
            return n;
        } catch (e) {
          h(t, !0);
        }
      return 0 < I(t, T, null, [e]).length;
    }),
    (I.contains = function (e, t) {
      return (e.ownerDocument || e) != T && V(e), ce.contains(e, t);
    }),
    (I.attr = function (e, t) {
      (e.ownerDocument || e) != T && V(e);
      var n = b.attrHandle[t.toLowerCase()],
        r = n && ue.call(b.attrHandle, t.toLowerCase()) ? n(e, t, !C) : void 0;
      return void 0 !== r ? r : e.getAttribute(t);
    }),
    (I.error = function (e) {
      throw new Error("Syntax error, unrecognized expression: " + e);
    }),
    (ce.uniqueSort = function (e) {
      var t,
        n = [],
        r = 0,
        i = 0;
      if (
        ((a = !le.sortStable),
        (o = !le.sortStable && ae.call(e, 0)),
        de.call(e, l),
        a)
      ) {
        while ((t = e[i++])) t === e[i] && (r = n.push(i));
        while (r--) he.call(e, n[r], 1);
      }
      return (o = null), e;
    }),
    (ce.fn.uniqueSort = function () {
      return this.pushStack(ce.uniqueSort(ae.apply(this)));
    }),
    ((b = ce.expr =
      {
        cacheLength: 50,
        createPseudo: F,
        match: D,
        attrHandle: {},
        find: {},
        relative: {
          ">": { dir: "parentNode", first: !0 },
          " ": { dir: "parentNode" },
          "+": { dir: "previousSibling", first: !0 },
          "~": { dir: "previousSibling" },
        },
        preFilter: {
          ATTR: function (e) {
            return (
              (e[1] = e[1].replace(O, P)),
              (e[3] = (e[3] || e[4] || e[5] || "").replace(O, P)),
              "~=" === e[2] && (e[3] = " " + e[3] + " "),
              e.slice(0, 4)
            );
          },
          CHILD: function (e) {
            return (
              (e[1] = e[1].toLowerCase()),
              "nth" === e[1].slice(0, 3)
                ? (e[3] || I.error(e[0]),
                  (e[4] = +(e[4]
                    ? e[5] + (e[6] || 1)
                    : 2 * ("even" === e[3] || "odd" === e[3]))),
                  (e[5] = +(e[7] + e[8] || "odd" === e[3])))
                : e[3] && I.error(e[0]),
              e
            );
          },
          PSEUDO: function (e) {
            var t,
              n = !e[6] && e[2];
            return D.CHILD.test(e[0])
              ? null
              : (e[3]
                  ? (e[2] = e[4] || e[5] || "")
                  : n &&
                    j.test(n) &&
                    (t = Y(n, !0)) &&
                    (t = n.indexOf(")", n.length - t) - n.length) &&
                    ((e[0] = e[0].slice(0, t)), (e[2] = n.slice(0, t))),
                e.slice(0, 3));
          },
        },
        filter: {
          TAG: function (e) {
            var t = e.replace(O, P).toLowerCase();
            return "*" === e
              ? function () {
                  return !0;
                }
              : function (e) {
                  return fe(e, t);
                };
          },
          CLASS: function (e) {
            var t = s[e + " "];
            return (
              t ||
              ((t = new RegExp("(^|" + ge + ")" + e + "(" + ge + "|$)")) &&
                s(e, function (e) {
                  return t.test(
                    ("string" == typeof e.className && e.className) ||
                      ("undefined" != typeof e.getAttribute &&
                        e.getAttribute("class")) ||
                      ""
                  );
                }))
            );
          },
          ATTR: function (n, r, i) {
            return function (e) {
              var t = I.attr(e, n);
              return null == t
                ? "!=" === r
                : !r ||
                    ((t += ""),
                    "=" === r
                      ? t === i
                      : "!=" === r
                      ? t !== i
                      : "^=" === r
                      ? i && 0 === t.indexOf(i)
                      : "*=" === r
                      ? i && -1 < t.indexOf(i)
                      : "$=" === r
                      ? i && t.slice(-i.length) === i
                      : "~=" === r
                      ? -1 < (" " + t.replace(v, " ") + " ").indexOf(i)
                      : "|=" === r &&
                        (t === i || t.slice(0, i.length + 1) === i + "-"));
            };
          },
          CHILD: function (d, e, t, h, g) {
            var v = "nth" !== d.slice(0, 3),
              y = "last" !== d.slice(-4),
              m = "of-type" === e;
            return 1 === h && 0 === g
              ? function (e) {
                  return !!e.parentNode;
                }
              : function (e, t, n) {
                  var r,
                    i,
                    o,
                    a,
                    s,
                    u = v !== y ? "nextSibling" : "previousSibling",
                    l = e.parentNode,
                    c = m && e.nodeName.toLowerCase(),
                    f = !n && !m,
                    p = !1;
                  if (l) {
                    if (v) {
                      while (u) {
                        o = e;
                        while ((o = o[u]))
                          if (m ? fe(o, c) : 1 === o.nodeType) return !1;
                        s = u = "only" === d && !s && "nextSibling";
                      }
                      return !0;
                    }
                    if (((s = [y ? l.firstChild : l.lastChild]), y && f)) {
                      (p =
                        (a =
                          (r = (i = l[S] || (l[S] = {}))[d] || [])[0] === E &&
                          r[1]) && r[2]),
                        (o = a && l.childNodes[a]);
                      while ((o = (++a && o && o[u]) || (p = a = 0) || s.pop()))
                        if (1 === o.nodeType && ++p && o === e) {
                          i[d] = [E, a, p];
                          break;
                        }
                    } else if (
                      (f &&
                        (p = a =
                          (r = (i = e[S] || (e[S] = {}))[d] || [])[0] === E &&
                          r[1]),
                      !1 === p)
                    )
                      while ((o = (++a && o && o[u]) || (p = a = 0) || s.pop()))
                        if (
                          (m ? fe(o, c) : 1 === o.nodeType) &&
                          ++p &&
                          (f && ((i = o[S] || (o[S] = {}))[d] = [E, p]),
                          o === e)
                        )
                          break;
                    return (p -= g) === h || (p % h == 0 && 0 <= p / h);
                  }
                };
          },
          PSEUDO: function (e, o) {
            var t,
              a =
                b.pseudos[e] ||
                b.setFilters[e.toLowerCase()] ||
                I.error("unsupported pseudo: " + e);
            return a[S]
              ? a(o)
              : 1 < a.length
              ? ((t = [e, e, "", o]),
                b.setFilters.hasOwnProperty(e.toLowerCase())
                  ? F(function (e, t) {
                      var n,
                        r = a(e, o),
                        i = r.length;
                      while (i--) e[(n = se.call(e, r[i]))] = !(t[n] = r[i]);
                    })
                  : function (e) {
                      return a(e, 0, t);
                    })
              : a;
          },
        },
        pseudos: {
          not: F(function (e) {
            var r = [],
              i = [],
              s = ne(e.replace(ve, "$1"));
            return s[S]
              ? F(function (e, t, n, r) {
                  var i,
                    o = s(e, null, r, []),
                    a = e.length;
                  while (a--) (i = o[a]) && (e[a] = !(t[a] = i));
                })
              : function (e, t, n) {
                  return (r[0] = e), s(r, null, n, i), (r[0] = null), !i.pop();
                };
          }),
          has: F(function (t) {
            return function (e) {
              return 0 < I(t, e).length;
            };
          }),
          contains: F(function (t) {
            return (
              (t = t.replace(O, P)),
              function (e) {
                return -1 < (e.textContent || ce.text(e)).indexOf(t);
              }
            );
          }),
          lang: F(function (n) {
            return (
              A.test(n || "") || I.error("unsupported lang: " + n),
              (n = n.replace(O, P).toLowerCase()),
              function (e) {
                var t;
                do {
                  if (
                    (t = C
                      ? e.lang
                      : e.getAttribute("xml:lang") || e.getAttribute("lang"))
                  )
                    return (
                      (t = t.toLowerCase()) === n || 0 === t.indexOf(n + "-")
                    );
                } while ((e = e.parentNode) && 1 === e.nodeType);
                return !1;
              }
            );
          }),
          target: function (e) {
            var t = ie.location && ie.location.hash;
            return t && t.slice(1) === e.id;
          },
          root: function (e) {
            return e === r;
          },
          focus: function (e) {
            return (
              e ===
                (function () {
                  try {
                    return T.activeElement;
                  } catch (e) {}
                })() &&
              T.hasFocus() &&
              !!(e.type || e.href || ~e.tabIndex)
            );
          },
          enabled: X(!1),
          disabled: X(!0),
          checked: function (e) {
            return (
              (fe(e, "input") && !!e.checked) ||
              (fe(e, "option") && !!e.selected)
            );
          },
          selected: function (e) {
            return (
              e.parentNode && e.parentNode.selectedIndex, !0 === e.selected
            );
          },
          empty: function (e) {
            for (e = e.firstChild; e; e = e.nextSibling)
              if (e.nodeType < 6) return !1;
            return !0;
          },
          parent: function (e) {
            return !b.pseudos.empty(e);
          },
          header: function (e) {
            return q.test(e.nodeName);
          },
          input: function (e) {
            return N.test(e.nodeName);
          },
          button: function (e) {
            return (fe(e, "input") && "button" === e.type) || fe(e, "button");
          },
          text: function (e) {
            var t;
            return (
              fe(e, "input") &&
              "text" === e.type &&
              (null == (t = e.getAttribute("type")) ||
                "text" === t.toLowerCase())
            );
          },
          first: U(function () {
            return [0];
          }),
          last: U(function (e, t) {
            return [t - 1];
          }),
          eq: U(function (e, t, n) {
            return [n < 0 ? n + t : n];
          }),
          even: U(function (e, t) {
            for (var n = 0; n < t; n += 2) e.push(n);
            return e;
          }),
          odd: U(function (e, t) {
            for (var n = 1; n < t; n += 2) e.push(n);
            return e;
          }),
          lt: U(function (e, t, n) {
            var r;
            for (r = n < 0 ? n + t : t < n ? t : n; 0 <= --r; ) e.push(r);
            return e;
          }),
          gt: U(function (e, t, n) {
            for (var r = n < 0 ? n + t : n; ++r < t; ) e.push(r);
            return e;
          }),
        },
      }).pseudos.nth = b.pseudos.eq),
    { radio: !0, checkbox: !0, file: !0, password: !0, image: !0 }))
      b.pseudos[e] = B(e);
    for (e in { submit: !0, reset: !0 }) b.pseudos[e] = _(e);
    function G() {}
    function Y(e, t) {
      var n,
        r,
        i,
        o,
        a,
        s,
        u,
        l = c[e + " "];
      if (l) return t ? 0 : l.slice(0);
      (a = e), (s = []), (u = b.preFilter);
      while (a) {
        for (o in ((n && !(r = y.exec(a))) ||
          (r && (a = a.slice(r[0].length) || a), s.push((i = []))),
        (n = !1),
        (r = m.exec(a)) &&
          ((n = r.shift()),
          i.push({ value: n, type: r[0].replace(ve, " ") }),
          (a = a.slice(n.length))),
        b.filter))
          !(r = D[o].exec(a)) ||
            (u[o] && !(r = u[o](r))) ||
            ((n = r.shift()),
            i.push({ value: n, type: o, matches: r }),
            (a = a.slice(n.length)));
        if (!n) break;
      }
      return t ? a.length : a ? I.error(e) : c(e, s).slice(0);
    }
    function Q(e) {
      for (var t = 0, n = e.length, r = ""; t < n; t++) r += e[t].value;
      return r;
    }
    function J(a, e, t) {
      var s = e.dir,
        u = e.next,
        l = u || s,
        c = t && "parentNode" === l,
        f = n++;
      return e.first
        ? function (e, t, n) {
            while ((e = e[s])) if (1 === e.nodeType || c) return a(e, t, n);
            return !1;
          }
        : function (e, t, n) {
            var r,
              i,
              o = [E, f];
            if (n) {
              while ((e = e[s]))
                if ((1 === e.nodeType || c) && a(e, t, n)) return !0;
            } else
              while ((e = e[s]))
                if (1 === e.nodeType || c)
                  if (((i = e[S] || (e[S] = {})), u && fe(e, u))) e = e[s] || e;
                  else {
                    if ((r = i[l]) && r[0] === E && r[1] === f)
                      return (o[2] = r[2]);
                    if (((i[l] = o)[2] = a(e, t, n))) return !0;
                  }
            return !1;
          };
    }
    function K(i) {
      return 1 < i.length
        ? function (e, t, n) {
            var r = i.length;
            while (r--) if (!i[r](e, t, n)) return !1;
            return !0;
          }
        : i[0];
    }
    function Z(e, t, n, r, i) {
      for (var o, a = [], s = 0, u = e.length, l = null != t; s < u; s++)
        (o = e[s]) && ((n && !n(o, r, i)) || (a.push(o), l && t.push(s)));
      return a;
    }
    function ee(d, h, g, v, y, e) {
      return (
        v && !v[S] && (v = ee(v)),
        y && !y[S] && (y = ee(y, e)),
        F(function (e, t, n, r) {
          var i,
            o,
            a,
            s,
            u = [],
            l = [],
            c = t.length,
            f =
              e ||
              (function (e, t, n) {
                for (var r = 0, i = t.length; r < i; r++) I(e, t[r], n);
                return n;
              })(h || "*", n.nodeType ? [n] : n, []),
            p = !d || (!e && h) ? f : Z(f, u, d, n, r);
          if (
            (g ? g(p, (s = y || (e ? d : c || v) ? [] : t), n, r) : (s = p), v)
          ) {
            (i = Z(s, l)), v(i, [], n, r), (o = i.length);
            while (o--) (a = i[o]) && (s[l[o]] = !(p[l[o]] = a));
          }
          if (e) {
            if (y || d) {
              if (y) {
                (i = []), (o = s.length);
                while (o--) (a = s[o]) && i.push((p[o] = a));
                y(null, (s = []), i, r);
              }
              o = s.length;
              while (o--)
                (a = s[o]) &&
                  -1 < (i = y ? se.call(e, a) : u[o]) &&
                  (e[i] = !(t[i] = a));
            }
          } else (s = Z(s === t ? s.splice(c, s.length) : s)), y ? y(null, t, s, r) : k.apply(t, s);
        })
      );
    }
    function te(e) {
      for (
        var i,
          t,
          n,
          r = e.length,
          o = b.relative[e[0].type],
          a = o || b.relative[" "],
          s = o ? 1 : 0,
          u = J(
            function (e) {
              return e === i;
            },
            a,
            !0
          ),
          l = J(
            function (e) {
              return -1 < se.call(i, e);
            },
            a,
            !0
          ),
          c = [
            function (e, t, n) {
              var r =
                (!o && (n || t != w)) ||
                ((i = t).nodeType ? u(e, t, n) : l(e, t, n));
              return (i = null), r;
            },
          ];
        s < r;
        s++
      )
        if ((t = b.relative[e[s].type])) c = [J(K(c), t)];
        else {
          if ((t = b.filter[e[s].type].apply(null, e[s].matches))[S]) {
            for (n = ++s; n < r; n++) if (b.relative[e[n].type]) break;
            return ee(
              1 < s && K(c),
              1 < s &&
                Q(
                  e
                    .slice(0, s - 1)
                    .concat({ value: " " === e[s - 2].type ? "*" : "" })
                ).replace(ve, "$1"),
              t,
              s < n && te(e.slice(s, n)),
              n < r && te((e = e.slice(n))),
              n < r && Q(e)
            );
          }
          c.push(t);
        }
      return K(c);
    }
    function ne(e, t) {
      var n,
        v,
        y,
        m,
        x,
        r,
        i = [],
        o = [],
        a = u[e + " "];
      if (!a) {
        t || (t = Y(e)), (n = t.length);
        while (n--) (a = te(t[n]))[S] ? i.push(a) : o.push(a);
        (a = u(
          e,
          ((v = o),
          (m = 0 < (y = i).length),
          (x = 0 < v.length),
          (r = function (e, t, n, r, i) {
            var o,
              a,
              s,
              u = 0,
              l = "0",
              c = e && [],
              f = [],
              p = w,
              d = e || (x && b.find.TAG("*", i)),
              h = (E += null == p ? 1 : Math.random() || 0.1),
              g = d.length;
            for (
              i && (w = t == T || t || i);
              l !== g && null != (o = d[l]);
              l++
            ) {
              if (x && o) {
                (a = 0), t || o.ownerDocument == T || (V(o), (n = !C));
                while ((s = v[a++]))
                  if (s(o, t || T, n)) {
                    k.call(r, o);
                    break;
                  }
                i && (E = h);
              }
              m && ((o = !s && o) && u--, e && c.push(o));
            }
            if (((u += l), m && l !== u)) {
              a = 0;
              while ((s = y[a++])) s(c, f, t, n);
              if (e) {
                if (0 < u) while (l--) c[l] || f[l] || (f[l] = pe.call(r));
                f = Z(f);
              }
              k.apply(r, f),
                i && !e && 0 < f.length && 1 < u + y.length && ce.uniqueSort(r);
            }
            return i && ((E = h), (w = p)), c;
          }),
          m ? F(r) : r)
        )).selector = e;
      }
      return a;
    }
    function re(e, t, n, r) {
      var i,
        o,
        a,
        s,
        u,
        l = "function" == typeof e && e,
        c = !r && Y((e = l.selector || e));
      if (((n = n || []), 1 === c.length)) {
        if (
          2 < (o = c[0] = c[0].slice(0)).length &&
          "ID" === (a = o[0]).type &&
          9 === t.nodeType &&
          C &&
          b.relative[o[1].type]
        ) {
          if (!(t = (b.find.ID(a.matches[0].replace(O, P), t) || [])[0]))
            return n;
          l && (t = t.parentNode), (e = e.slice(o.shift().value.length));
        }
        i = D.needsContext.test(e) ? 0 : o.length;
        while (i--) {
          if (((a = o[i]), b.relative[(s = a.type)])) break;
          if (
            (u = b.find[s]) &&
            (r = u(
              a.matches[0].replace(O, P),
              (H.test(o[0].type) && z(t.parentNode)) || t
            ))
          ) {
            if ((o.splice(i, 1), !(e = r.length && Q(o))))
              return k.apply(n, r), n;
            break;
          }
        }
      }
      return (
        (l || ne(e, c))(r, t, !C, n, !t || (H.test(e) && z(t.parentNode)) || t),
        n
      );
    }
    (G.prototype = b.filters = b.pseudos),
      (b.setFilters = new G()),
      (le.sortStable = S.split("").sort(l).join("") === S),
      V(),
      (le.sortDetached = $(function (e) {
        return 1 & e.compareDocumentPosition(T.createElement("fieldset"));
      })),
      (ce.find = I),
      (ce.expr[":"] = ce.expr.pseudos),
      (ce.unique = ce.uniqueSort),
      (I.compile = ne),
      (I.select = re),
      (I.setDocument = V),
      (I.escape = ce.escapeSelector),
      (I.getText = ce.text),
      (I.isXML = ce.isXMLDoc),
      (I.selectors = ce.expr),
      (I.support = ce.support),
      (I.uniqueSort = ce.uniqueSort);
  })();
  var d = function (e, t, n) {
      var r = [],
        i = void 0 !== n;
      while ((e = e[t]) && 9 !== e.nodeType)
        if (1 === e.nodeType) {
          if (i && ce(e).is(n)) break;
          r.push(e);
        }
      return r;
    },
    h = function (e, t) {
      for (var n = []; e; e = e.nextSibling)
        1 === e.nodeType && e !== t && n.push(e);
      return n;
    },
    b = ce.expr.match.needsContext,
    w = /^<([a-z][^\/\0>:\x20\t\r\n\f]*)[\x20\t\r\n\f]*\/?>(?:<\/\1>|)$/i;
  function T(e, n, r) {
    return v(n)
      ? ce.grep(e, function (e, t) {
          return !!n.call(e, t, e) !== r;
        })
      : n.nodeType
      ? ce.grep(e, function (e) {
          return (e === n) !== r;
        })
      : "string" != typeof n
      ? ce.grep(e, function (e) {
          return -1 < se.call(n, e) !== r;
        })
      : ce.filter(n, e, r);
  }
  (ce.filter = function (e, t, n) {
    var r = t[0];
    return (
      n && (e = ":not(" + e + ")"),
      1 === t.length && 1 === r.nodeType
        ? ce.find.matchesSelector(r, e)
          ? [r]
          : []
        : ce.find.matches(
            e,
            ce.grep(t, function (e) {
              return 1 === e.nodeType;
            })
          )
    );
  }),
    ce.fn.extend({
      find: function (e) {
        var t,
          n,
          r = this.length,
          i = this;
        if ("string" != typeof e)
          return this.pushStack(
            ce(e).filter(function () {
              for (t = 0; t < r; t++) if (ce.contains(i[t], this)) return !0;
            })
          );
        for (n = this.pushStack([]), t = 0; t < r; t++) ce.find(e, i[t], n);
        return 1 < r ? ce.uniqueSort(n) : n;
      },
      filter: function (e) {
        return this.pushStack(T(this, e || [], !1));
      },
      not: function (e) {
        return this.pushStack(T(this, e || [], !0));
      },
      is: function (e) {
        return !!T(
          this,
          "string" == typeof e && b.test(e) ? ce(e) : e || [],
          !1
        ).length;
      },
    });
  var k,
    S = /^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]+))$/;
  ((ce.fn.init = function (e, t, n) {
    var r, i;
    if (!e) return this;
    if (((n = n || k), "string" == typeof e)) {
      if (
        !(r =
          "<" === e[0] && ">" === e[e.length - 1] && 3 <= e.length
            ? [null, e, null]
            : S.exec(e)) ||
        (!r[1] && t)
      )
        return !t || t.jquery ? (t || n).find(e) : this.constructor(t).find(e);
      if (r[1]) {
        if (
          ((t = t instanceof ce ? t[0] : t),
          ce.merge(
            this,
            ce.parseHTML(r[1], t && t.nodeType ? t.ownerDocument || t : C, !0)
          ),
          w.test(r[1]) && ce.isPlainObject(t))
        )
          for (r in t) v(this[r]) ? this[r](t[r]) : this.attr(r, t[r]);
        return this;
      }
      return (
        (i = C.getElementById(r[2])) && ((this[0] = i), (this.length = 1)), this
      );
    }
    return e.nodeType
      ? ((this[0] = e), (this.length = 1), this)
      : v(e)
      ? void 0 !== n.ready
        ? n.ready(e)
        : e(ce)
      : ce.makeArray(e, this);
  }).prototype = ce.fn),
    (k = ce(C));
  var E = /^(?:parents|prev(?:Until|All))/,
    j = { children: !0, contents: !0, next: !0, prev: !0 };
  function A(e, t) {
    while ((e = e[t]) && 1 !== e.nodeType);
    return e;
  }
  ce.fn.extend({
    has: function (e) {
      var t = ce(e, this),
        n = t.length;
      return this.filter(function () {
        for (var e = 0; e < n; e++) if (ce.contains(this, t[e])) return !0;
      });
    },
    closest: function (e, t) {
      var n,
        r = 0,
        i = this.length,
        o = [],
        a = "string" != typeof e && ce(e);
      if (!b.test(e))
        for (; r < i; r++)
          for (n = this[r]; n && n !== t; n = n.parentNode)
            if (
              n.nodeType < 11 &&
              (a
                ? -1 < a.index(n)
                : 1 === n.nodeType && ce.find.matchesSelector(n, e))
            ) {
              o.push(n);
              break;
            }
      return this.pushStack(1 < o.length ? ce.uniqueSort(o) : o);
    },
    index: function (e) {
      return e
        ? "string" == typeof e
          ? se.call(ce(e), this[0])
          : se.call(this, e.jquery ? e[0] : e)
        : this[0] && this[0].parentNode
        ? this.first().prevAll().length
        : -1;
    },
    add: function (e, t) {
      return this.pushStack(ce.uniqueSort(ce.merge(this.get(), ce(e, t))));
    },
    addBack: function (e) {
      return this.add(null == e ? this.prevObject : this.prevObject.filter(e));
    },
  }),
    ce.each(
      {
        parent: function (e) {
          var t = e.parentNode;
          return t && 11 !== t.nodeType ? t : null;
        },
        parents: function (e) {
          return d(e, "parentNode");
        },
        parentsUntil: function (e, t, n) {
          return d(e, "parentNode", n);
        },
        next: function (e) {
          return A(e, "nextSibling");
        },
        prev: function (e) {
          return A(e, "previousSibling");
        },
        nextAll: function (e) {
          return d(e, "nextSibling");
        },
        prevAll: function (e) {
          return d(e, "previousSibling");
        },
        nextUntil: function (e, t, n) {
          return d(e, "nextSibling", n);
        },
        prevUntil: function (e, t, n) {
          return d(e, "previousSibling", n);
        },
        siblings: function (e) {
          return h((e.parentNode || {}).firstChild, e);
        },
        children: function (e) {
          return h(e.firstChild);
        },
        contents: function (e) {
          return null != e.contentDocument && r(e.contentDocument)
            ? e.contentDocument
            : (fe(e, "template") && (e = e.content || e),
              ce.merge([], e.childNodes));
        },
      },
      function (r, i) {
        ce.fn[r] = function (e, t) {
          var n = ce.map(this, i, e);
          return (
            "Until" !== r.slice(-5) && (t = e),
            t && "string" == typeof t && (n = ce.filter(t, n)),
            1 < this.length &&
              (j[r] || ce.uniqueSort(n), E.test(r) && n.reverse()),
            this.pushStack(n)
          );
        };
      }
    );
  var D = /[^\x20\t\r\n\f]+/g;
  function N(e) {
    return e;
  }
  function q(e) {
    throw e;
  }
  function L(e, t, n, r) {
    var i;
    try {
      e && v((i = e.promise))
        ? i.call(e).done(t).fail(n)
        : e && v((i = e.then))
        ? i.call(e, t, n)
        : t.apply(void 0, [e].slice(r));
    } catch (e) {
      n.apply(void 0, [e]);
    }
  }
  (ce.Callbacks = function (r) {
    var e, n;
    r =
      "string" == typeof r
        ? ((e = r),
          (n = {}),
          ce.each(e.match(D) || [], function (e, t) {
            n[t] = !0;
          }),
          n)
        : ce.extend({}, r);
    var i,
      t,
      o,
      a,
      s = [],
      u = [],
      l = -1,
      c = function () {
        for (a = a || r.once, o = i = !0; u.length; l = -1) {
          t = u.shift();
          while (++l < s.length)
            !1 === s[l].apply(t[0], t[1]) &&
              r.stopOnFalse &&
              ((l = s.length), (t = !1));
        }
        r.memory || (t = !1), (i = !1), a && (s = t ? [] : "");
      },
      f = {
        add: function () {
          return (
            s &&
              (t && !i && ((l = s.length - 1), u.push(t)),
              (function n(e) {
                ce.each(e, function (e, t) {
                  v(t)
                    ? (r.unique && f.has(t)) || s.push(t)
                    : t && t.length && "string" !== x(t) && n(t);
                });
              })(arguments),
              t && !i && c()),
            this
          );
        },
        remove: function () {
          return (
            ce.each(arguments, function (e, t) {
              var n;
              while (-1 < (n = ce.inArray(t, s, n)))
                s.splice(n, 1), n <= l && l--;
            }),
            this
          );
        },
        has: function (e) {
          return e ? -1 < ce.inArray(e, s) : 0 < s.length;
        },
        empty: function () {
          return s && (s = []), this;
        },
        disable: function () {
          return (a = u = []), (s = t = ""), this;
        },
        disabled: function () {
          return !s;
        },
        lock: function () {
          return (a = u = []), t || i || (s = t = ""), this;
        },
        locked: function () {
          return !!a;
        },
        fireWith: function (e, t) {
          return (
            a ||
              ((t = [e, (t = t || []).slice ? t.slice() : t]),
              u.push(t),
              i || c()),
            this
          );
        },
        fire: function () {
          return f.fireWith(this, arguments), this;
        },
        fired: function () {
          return !!o;
        },
      };
    return f;
  }),
    ce.extend({
      Deferred: function (e) {
        var o = [
            [
              "notify",
              "progress",
              ce.Callbacks("memory"),
              ce.Callbacks("memory"),
              2,
            ],
            [
              "resolve",
              "done",
              ce.Callbacks("once memory"),
              ce.Callbacks("once memory"),
              0,
              "resolved",
            ],
            [
              "reject",
              "fail",
              ce.Callbacks("once memory"),
              ce.Callbacks("once memory"),
              1,
              "rejected",
            ],
          ],
          i = "pending",
          a = {
            state: function () {
              return i;
            },
            always: function () {
              return s.done(arguments).fail(arguments), this;
            },
            catch: function (e) {
              return a.then(null, e);
            },
            pipe: function () {
              var i = arguments;
              return ce
                .Deferred(function (r) {
                  ce.each(o, function (e, t) {
                    var n = v(i[t[4]]) && i[t[4]];
                    s[t[1]](function () {
                      var e = n && n.apply(this, arguments);
                      e && v(e.promise)
                        ? e
                            .promise()
                            .progress(r.notify)
                            .done(r.resolve)
                            .fail(r.reject)
                        : r[t[0] + "With"](this, n ? [e] : arguments);
                    });
                  }),
                    (i = null);
                })
                .promise();
            },
            then: function (t, n, r) {
              var u = 0;
              function l(i, o, a, s) {
                return function () {
                  var n = this,
                    r = arguments,
                    e = function () {
                      var e, t;
                      if (!(i < u)) {
                        if ((e = a.apply(n, r)) === o.promise())
                          throw new TypeError("Thenable self-resolution");
                        (t =
                          e &&
                          ("object" == typeof e || "function" == typeof e) &&
                          e.then),
                          v(t)
                            ? s
                              ? t.call(e, l(u, o, N, s), l(u, o, q, s))
                              : (u++,
                                t.call(
                                  e,
                                  l(u, o, N, s),
                                  l(u, o, q, s),
                                  l(u, o, N, o.notifyWith)
                                ))
                            : (a !== N && ((n = void 0), (r = [e])),
                              (s || o.resolveWith)(n, r));
                      }
                    },
                    t = s
                      ? e
                      : function () {
                          try {
                            e();
                          } catch (e) {
                            ce.Deferred.exceptionHook &&
                              ce.Deferred.exceptionHook(e, t.error),
                              u <= i + 1 &&
                                (a !== q && ((n = void 0), (r = [e])),
                                o.rejectWith(n, r));
                          }
                        };
                  i
                    ? t()
                    : (ce.Deferred.getErrorHook
                        ? (t.error = ce.Deferred.getErrorHook())
                        : ce.Deferred.getStackHook &&
                          (t.error = ce.Deferred.getStackHook()),
                      ie.setTimeout(t));
                };
              }
              return ce
                .Deferred(function (e) {
                  o[0][3].add(l(0, e, v(r) ? r : N, e.notifyWith)),
                    o[1][3].add(l(0, e, v(t) ? t : N)),
                    o[2][3].add(l(0, e, v(n) ? n : q));
                })
                .promise();
            },
            promise: function (e) {
              return null != e ? ce.extend(e, a) : a;
            },
          },
          s = {};
        return (
          ce.each(o, function (e, t) {
            var n = t[2],
              r = t[5];
            (a[t[1]] = n.add),
              r &&
                n.add(
                  function () {
                    i = r;
                  },
                  o[3 - e][2].disable,
                  o[3 - e][3].disable,
                  o[0][2].lock,
                  o[0][3].lock
                ),
              n.add(t[3].fire),
              (s[t[0]] = function () {
                return (
                  s[t[0] + "With"](this === s ? void 0 : this, arguments), this
                );
              }),
              (s[t[0] + "With"] = n.fireWith);
          }),
          a.promise(s),
          e && e.call(s, s),
          s
        );
      },
      when: function (e) {
        var n = arguments.length,
          t = n,
          r = Array(t),
          i = ae.call(arguments),
          o = ce.Deferred(),
          a = function (t) {
            return function (e) {
              (r[t] = this),
                (i[t] = 1 < arguments.length ? ae.call(arguments) : e),
                --n || o.resolveWith(r, i);
            };
          };
        if (
          n <= 1 &&
          (L(e, o.done(a(t)).resolve, o.reject, !n),
          "pending" === o.state() || v(i[t] && i[t].then))
        )
          return o.then();
        while (t--) L(i[t], a(t), o.reject);
        return o.promise();
      },
    });
  var H = /^(Eval|Internal|Range|Reference|Syntax|Type|URI)Error$/;
  (ce.Deferred.exceptionHook = function (e, t) {
    ie.console &&
      ie.console.warn &&
      e &&
      H.test(e.name) &&
      ie.console.warn("jQuery.Deferred exception: " + e.message, e.stack, t);
  }),
    (ce.readyException = function (e) {
      ie.setTimeout(function () {
        throw e;
      });
    });
  var O = ce.Deferred();
  function P() {
    C.removeEventListener("DOMContentLoaded", P),
      ie.removeEventListener("load", P),
      ce.ready();
  }
  (ce.fn.ready = function (e) {
    return (
      O.then(e)["catch"](function (e) {
        ce.readyException(e);
      }),
      this
    );
  }),
    ce.extend({
      isReady: !1,
      readyWait: 1,
      ready: function (e) {
        (!0 === e ? --ce.readyWait : ce.isReady) ||
          ((ce.isReady = !0) !== e && 0 < --ce.readyWait) ||
          O.resolveWith(C, [ce]);
      },
    }),
    (ce.ready.then = O.then),
    "complete" === C.readyState ||
    ("loading" !== C.readyState && !C.documentElement.doScroll)
      ? ie.setTimeout(ce.ready)
      : (C.addEventListener("DOMContentLoaded", P),
        ie.addEventListener("load", P));
  var R = function (e, t, n, r, i, o, a) {
      var s = 0,
        u = e.length,
        l = null == n;
      if ("object" === x(n))
        for (s in ((i = !0), n)) R(e, t, s, n[s], !0, o, a);
      else if (
        void 0 !== r &&
        ((i = !0),
        v(r) || (a = !0),
        l &&
          (a
            ? (t.call(e, r), (t = null))
            : ((l = t),
              (t = function (e, t, n) {
                return l.call(ce(e), n);
              }))),
        t)
      )
        for (; s < u; s++) t(e[s], n, a ? r : r.call(e[s], s, t(e[s], n)));
      return i ? e : l ? t.call(e) : u ? t(e[0], n) : o;
    },
    M = /^-ms-/,
    I = /-([a-z])/g;
  function W(e, t) {
    return t.toUpperCase();
  }
  function F(e) {
    return e.replace(M, "ms-").replace(I, W);
  }
  var $ = function (e) {
    return 1 === e.nodeType || 9 === e.nodeType || !+e.nodeType;
  };
  function B() {
    this.expando = ce.expando + B.uid++;
  }
  (B.uid = 1),
    (B.prototype = {
      cache: function (e) {
        var t = e[this.expando];
        return (
          t ||
            ((t = {}),
            $(e) &&
              (e.nodeType
                ? (e[this.expando] = t)
                : Object.defineProperty(e, this.expando, {
                    value: t,
                    configurable: !0,
                  }))),
          t
        );
      },
      set: function (e, t, n) {
        var r,
          i = this.cache(e);
        if ("string" == typeof t) i[F(t)] = n;
        else for (r in t) i[F(r)] = t[r];
        return i;
      },
      get: function (e, t) {
        return void 0 === t
          ? this.cache(e)
          : e[this.expando] && e[this.expando][F(t)];
      },
      access: function (e, t, n) {
        return void 0 === t || (t && "string" == typeof t && void 0 === n)
          ? this.get(e, t)
          : (this.set(e, t, n), void 0 !== n ? n : t);
      },
      remove: function (e, t) {
        var n,
          r = e[this.expando];
        if (void 0 !== r) {
          if (void 0 !== t) {
            n = (t = Array.isArray(t)
              ? t.map(F)
              : (t = F(t)) in r
              ? [t]
              : t.match(D) || []).length;
            while (n--) delete r[t[n]];
          }
          (void 0 === t || ce.isEmptyObject(r)) &&
            (e.nodeType ? (e[this.expando] = void 0) : delete e[this.expando]);
        }
      },
      hasData: function (e) {
        var t = e[this.expando];
        return void 0 !== t && !ce.isEmptyObject(t);
      },
    });
  var _ = new B(),
    X = new B(),
    U = /^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,
    z = /[A-Z]/g;
  function V(e, t, n) {
    var r, i;
    if (void 0 === n && 1 === e.nodeType)
      if (
        ((r = "data-" + t.replace(z, "-$&").toLowerCase()),
        "string" == typeof (n = e.getAttribute(r)))
      ) {
        try {
          n =
            "true" === (i = n) ||
            ("false" !== i &&
              ("null" === i
                ? null
                : i === +i + ""
                ? +i
                : U.test(i)
                ? JSON.parse(i)
                : i));
        } catch (e) {}
        X.set(e, t, n);
      } else n = void 0;
    return n;
  }
  ce.extend({
    hasData: function (e) {
      return X.hasData(e) || _.hasData(e);
    },
    data: function (e, t, n) {
      return X.access(e, t, n);
    },
    removeData: function (e, t) {
      X.remove(e, t);
    },
    _data: function (e, t, n) {
      return _.access(e, t, n);
    },
    _removeData: function (e, t) {
      _.remove(e, t);
    },
  }),
    ce.fn.extend({
      data: function (n, e) {
        var t,
          r,
          i,
          o = this[0],
          a = o && o.attributes;
        if (void 0 === n) {
          if (
            this.length &&
            ((i = X.get(o)), 1 === o.nodeType && !_.get(o, "hasDataAttrs"))
          ) {
            t = a.length;
            while (t--)
              a[t] &&
                0 === (r = a[t].name).indexOf("data-") &&
                ((r = F(r.slice(5))), V(o, r, i[r]));
            _.set(o, "hasDataAttrs", !0);
          }
          return i;
        }
        return "object" == typeof n
          ? this.each(function () {
              X.set(this, n);
            })
          : R(
              this,
              function (e) {
                var t;
                if (o && void 0 === e)
                  return void 0 !== (t = X.get(o, n))
                    ? t
                    : void 0 !== (t = V(o, n))
                    ? t
                    : void 0;
                this.each(function () {
                  X.set(this, n, e);
                });
              },
              null,
              e,
              1 < arguments.length,
              null,
              !0
            );
      },
      removeData: function (e) {
        return this.each(function () {
          X.remove(this, e);
        });
      },
    }),
    ce.extend({
      queue: function (e, t, n) {
        var r;
        if (e)
          return (
            (t = (t || "fx") + "queue"),
            (r = _.get(e, t)),
            n &&
              (!r || Array.isArray(n)
                ? (r = _.access(e, t, ce.makeArray(n)))
                : r.push(n)),
            r || []
          );
      },
      dequeue: function (e, t) {
        t = t || "fx";
        var n = ce.queue(e, t),
          r = n.length,
          i = n.shift(),
          o = ce._queueHooks(e, t);
        "inprogress" === i && ((i = n.shift()), r--),
          i &&
            ("fx" === t && n.unshift("inprogress"),
            delete o.stop,
            i.call(
              e,
              function () {
                ce.dequeue(e, t);
              },
              o
            )),
          !r && o && o.empty.fire();
      },
      _queueHooks: function (e, t) {
        var n = t + "queueHooks";
        return (
          _.get(e, n) ||
          _.access(e, n, {
            empty: ce.Callbacks("once memory").add(function () {
              _.remove(e, [t + "queue", n]);
            }),
          })
        );
      },
    }),
    ce.fn.extend({
      queue: function (t, n) {
        var e = 2;
        return (
          "string" != typeof t && ((n = t), (t = "fx"), e--),
          arguments.length < e
            ? ce.queue(this[0], t)
            : void 0 === n
            ? this
            : this.each(function () {
                var e = ce.queue(this, t, n);
                ce._queueHooks(this, t),
                  "fx" === t && "inprogress" !== e[0] && ce.dequeue(this, t);
              })
        );
      },
      dequeue: function (e) {
        return this.each(function () {
          ce.dequeue(this, e);
        });
      },
      clearQueue: function (e) {
        return this.queue(e || "fx", []);
      },
      promise: function (e, t) {
        var n,
          r = 1,
          i = ce.Deferred(),
          o = this,
          a = this.length,
          s = function () {
            --r || i.resolveWith(o, [o]);
          };
        "string" != typeof e && ((t = e), (e = void 0)), (e = e || "fx");
        while (a--)
          (n = _.get(o[a], e + "queueHooks")) &&
            n.empty &&
            (r++, n.empty.add(s));
        return s(), i.promise(t);
      },
    });
  var G = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,
    Y = new RegExp("^(?:([+-])=|)(" + G + ")([a-z%]*)$", "i"),
    Q = ["Top", "Right", "Bottom", "Left"],
    J = C.documentElement,
    K = function (e) {
      return ce.contains(e.ownerDocument, e);
    },
    Z = { composed: !0 };
  J.getRootNode &&
    (K = function (e) {
      return (
        ce.contains(e.ownerDocument, e) || e.getRootNode(Z) === e.ownerDocument
      );
    });
  var ee = function (e, t) {
    return (
      "none" === (e = t || e).style.display ||
      ("" === e.style.display && K(e) && "none" === ce.css(e, "display"))
    );
  };
  function te(e, t, n, r) {
    var i,
      o,
      a = 20,
      s = r
        ? function () {
            return r.cur();
          }
        : function () {
            return ce.css(e, t, "");
          },
      u = s(),
      l = (n && n[3]) || (ce.cssNumber[t] ? "" : "px"),
      c =
        e.nodeType &&
        (ce.cssNumber[t] || ("px" !== l && +u)) &&
        Y.exec(ce.css(e, t));
    if (c && c[3] !== l) {
      (u /= 2), (l = l || c[3]), (c = +u || 1);
      while (a--)
        ce.style(e, t, c + l),
          (1 - o) * (1 - (o = s() / u || 0.5)) <= 0 && (a = 0),
          (c /= o);
      (c *= 2), ce.style(e, t, c + l), (n = n || []);
    }
    return (
      n &&
        ((c = +c || +u || 0),
        (i = n[1] ? c + (n[1] + 1) * n[2] : +n[2]),
        r && ((r.unit = l), (r.start = c), (r.end = i))),
      i
    );
  }
  var ne = {};
  function re(e, t) {
    for (var n, r, i, o, a, s, u, l = [], c = 0, f = e.length; c < f; c++)
      (r = e[c]).style &&
        ((n = r.style.display),
        t
          ? ("none" === n &&
              ((l[c] = _.get(r, "display") || null),
              l[c] || (r.style.display = "")),
            "" === r.style.display &&
              ee(r) &&
              (l[c] =
                ((u = a = o = void 0),
                (a = (i = r).ownerDocument),
                (s = i.nodeName),
                (u = ne[s]) ||
                  ((o = a.body.appendChild(a.createElement(s))),
                  (u = ce.css(o, "display")),
                  o.parentNode.removeChild(o),
                  "none" === u && (u = "block"),
                  (ne[s] = u)))))
          : "none" !== n && ((l[c] = "none"), _.set(r, "display", n)));
    for (c = 0; c < f; c++) null != l[c] && (e[c].style.display = l[c]);
    return e;
  }
  ce.fn.extend({
    show: function () {
      return re(this, !0);
    },
    hide: function () {
      return re(this);
    },
    toggle: function (e) {
      return "boolean" == typeof e
        ? e
          ? this.show()
          : this.hide()
        : this.each(function () {
            ee(this) ? ce(this).show() : ce(this).hide();
          });
    },
  });
  var xe,
    be,
    we = /^(?:checkbox|radio)$/i,
    Te = /<([a-z][^\/\0>\x20\t\r\n\f]*)/i,
    Ce = /^$|^module$|\/(?:java|ecma)script/i;
  (xe = C.createDocumentFragment().appendChild(C.createElement("div"))),
    (be = C.createElement("input")).setAttribute("type", "radio"),
    be.setAttribute("checked", "checked"),
    be.setAttribute("name", "t"),
    xe.appendChild(be),
    (le.checkClone = xe.cloneNode(!0).cloneNode(!0).lastChild.checked),
    (xe.innerHTML = "<textarea>x</textarea>"),
    (le.noCloneChecked = !!xe.cloneNode(!0).lastChild.defaultValue),
    (xe.innerHTML = "<option></option>"),
    (le.option = !!xe.lastChild);
  var ke = {
    thead: [1, "<table>", "</table>"],
    col: [2, "<table><colgroup>", "</colgroup></table>"],
    tr: [2, "<table><tbody>", "</tbody></table>"],
    td: [3, "<table><tbody><tr>", "</tr></tbody></table>"],
    _default: [0, "", ""],
  };
  function Se(e, t) {
    var n;
    return (
      (n =
        "undefined" != typeof e.getElementsByTagName
          ? e.getElementsByTagName(t || "*")
          : "undefined" != typeof e.querySelectorAll
          ? e.querySelectorAll(t || "*")
          : []),
      void 0 === t || (t && fe(e, t)) ? ce.merge([e], n) : n
    );
  }
  function Ee(e, t) {
    for (var n = 0, r = e.length; n < r; n++)
      _.set(e[n], "globalEval", !t || _.get(t[n], "globalEval"));
  }
  (ke.tbody = ke.tfoot = ke.colgroup = ke.caption = ke.thead),
    (ke.th = ke.td),
    le.option ||
      (ke.optgroup = ke.option =
        [1, "<select multiple='multiple'>", "</select>"]);
  var je = /<|&#?\w+;/;
  function Ae(e, t, n, r, i) {
    for (
      var o,
        a,
        s,
        u,
        l,
        c,
        f = t.createDocumentFragment(),
        p = [],
        d = 0,
        h = e.length;
      d < h;
      d++
    )
      if ((o = e[d]) || 0 === o)
        if ("object" === x(o)) ce.merge(p, o.nodeType ? [o] : o);
        else if (je.test(o)) {
          (a = a || f.appendChild(t.createElement("div"))),
            (s = (Te.exec(o) || ["", ""])[1].toLowerCase()),
            (u = ke[s] || ke._default),
            (a.innerHTML = u[1] + ce.htmlPrefilter(o) + u[2]),
            (c = u[0]);
          while (c--) a = a.lastChild;
          ce.merge(p, a.childNodes), ((a = f.firstChild).textContent = "");
        } else p.push(t.createTextNode(o));
    (f.textContent = ""), (d = 0);
    while ((o = p[d++]))
      if (r && -1 < ce.inArray(o, r)) i && i.push(o);
      else if (
        ((l = K(o)), (a = Se(f.appendChild(o), "script")), l && Ee(a), n)
      ) {
        c = 0;
        while ((o = a[c++])) Ce.test(o.type || "") && n.push(o);
      }
    return f;
  }
  var De = /^([^.]*)(?:\.(.+)|)/;
  function Ne() {
    return !0;
  }
  function qe() {
    return !1;
  }
  function Le(e, t, n, r, i, o) {
    var a, s;
    if ("object" == typeof t) {
      for (s in ("string" != typeof n && ((r = r || n), (n = void 0)), t))
        Le(e, s, n, r, t[s], o);
      return e;
    }
    if (
      (null == r && null == i
        ? ((i = n), (r = n = void 0))
        : null == i &&
          ("string" == typeof n
            ? ((i = r), (r = void 0))
            : ((i = r), (r = n), (n = void 0))),
      !1 === i)
    )
      i = qe;
    else if (!i) return e;
    return (
      1 === o &&
        ((a = i),
        ((i = function (e) {
          return ce().off(e), a.apply(this, arguments);
        }).guid = a.guid || (a.guid = ce.guid++))),
      e.each(function () {
        ce.event.add(this, t, i, r, n);
      })
    );
  }
  function He(e, r, t) {
    t
      ? (_.set(e, r, !1),
        ce.event.add(e, r, {
          namespace: !1,
          handler: function (e) {
            var t,
              n = _.get(this, r);
            if (1 & e.isTrigger && this[r]) {
              if (n)
                (ce.event.special[r] || {}).delegateType && e.stopPropagation();
              else if (
                ((n = ae.call(arguments)),
                _.set(this, r, n),
                this[r](),
                (t = _.get(this, r)),
                _.set(this, r, !1),
                n !== t)
              )
                return e.stopImmediatePropagation(), e.preventDefault(), t;
            } else
              n &&
                (_.set(this, r, ce.event.trigger(n[0], n.slice(1), this)),
                e.stopPropagation(),
                (e.isImmediatePropagationStopped = Ne));
          },
        }))
      : void 0 === _.get(e, r) && ce.event.add(e, r, Ne);
  }
  (ce.event = {
    global: {},
    add: function (t, e, n, r, i) {
      var o,
        a,
        s,
        u,
        l,
        c,
        f,
        p,
        d,
        h,
        g,
        v = _.get(t);
      if ($(t)) {
        n.handler && ((n = (o = n).handler), (i = o.selector)),
          i && ce.find.matchesSelector(J, i),
          n.guid || (n.guid = ce.guid++),
          (u = v.events) || (u = v.events = Object.create(null)),
          (a = v.handle) ||
            (a = v.handle =
              function (e) {
                return "undefined" != typeof ce && ce.event.triggered !== e.type
                  ? ce.event.dispatch.apply(t, arguments)
                  : void 0;
              }),
          (l = (e = (e || "").match(D) || [""]).length);
        while (l--)
          (d = g = (s = De.exec(e[l]) || [])[1]),
            (h = (s[2] || "").split(".").sort()),
            d &&
              ((f = ce.event.special[d] || {}),
              (d = (i ? f.delegateType : f.bindType) || d),
              (f = ce.event.special[d] || {}),
              (c = ce.extend(
                {
                  type: d,
                  origType: g,
                  data: r,
                  handler: n,
                  guid: n.guid,
                  selector: i,
                  needsContext: i && ce.expr.match.needsContext.test(i),
                  namespace: h.join("."),
                },
                o
              )),
              (p = u[d]) ||
                (((p = u[d] = []).delegateCount = 0),
                (f.setup && !1 !== f.setup.call(t, r, h, a)) ||
                  (t.addEventListener && t.addEventListener(d, a))),
              f.add &&
                (f.add.call(t, c), c.handler.guid || (c.handler.guid = n.guid)),
              i ? p.splice(p.delegateCount++, 0, c) : p.push(c),
              (ce.event.global[d] = !0));
      }
    },
    remove: function (e, t, n, r, i) {
      var o,
        a,
        s,
        u,
        l,
        c,
        f,
        p,
        d,
        h,
        g,
        v = _.hasData(e) && _.get(e);
      if (v && (u = v.events)) {
        l = (t = (t || "").match(D) || [""]).length;
        while (l--)
          if (
            ((d = g = (s = De.exec(t[l]) || [])[1]),
            (h = (s[2] || "").split(".").sort()),
            d)
          ) {
            (f = ce.event.special[d] || {}),
              (p = u[(d = (r ? f.delegateType : f.bindType) || d)] || []),
              (s =
                s[2] &&
                new RegExp("(^|\\.)" + h.join("\\.(?:.*\\.|)") + "(\\.|$)")),
              (a = o = p.length);
            while (o--)
              (c = p[o]),
                (!i && g !== c.origType) ||
                  (n && n.guid !== c.guid) ||
                  (s && !s.test(c.namespace)) ||
                  (r && r !== c.selector && ("**" !== r || !c.selector)) ||
                  (p.splice(o, 1),
                  c.selector && p.delegateCount--,
                  f.remove && f.remove.call(e, c));
            a &&
              !p.length &&
              ((f.teardown && !1 !== f.teardown.call(e, h, v.handle)) ||
                ce.removeEvent(e, d, v.handle),
              delete u[d]);
          } else for (d in u) ce.event.remove(e, d + t[l], n, r, !0);
        ce.isEmptyObject(u) && _.remove(e, "handle events");
      }
    },
    dispatch: function (e) {
      var t,
        n,
        r,
        i,
        o,
        a,
        s = new Array(arguments.length),
        u = ce.event.fix(e),
        l = (_.get(this, "events") || Object.create(null))[u.type] || [],
        c = ce.event.special[u.type] || {};
      for (s[0] = u, t = 1; t < arguments.length; t++) s[t] = arguments[t];
      if (
        ((u.delegateTarget = this),
        !c.preDispatch || !1 !== c.preDispatch.call(this, u))
      ) {
        (a = ce.event.handlers.call(this, u, l)), (t = 0);
        while ((i = a[t++]) && !u.isPropagationStopped()) {
          (u.currentTarget = i.elem), (n = 0);
          while ((o = i.handlers[n++]) && !u.isImmediatePropagationStopped())
            (u.rnamespace &&
              !1 !== o.namespace &&
              !u.rnamespace.test(o.namespace)) ||
              ((u.handleObj = o),
              (u.data = o.data),
              void 0 !==
                (r = (
                  (ce.event.special[o.origType] || {}).handle || o.handler
                ).apply(i.elem, s)) &&
                !1 === (u.result = r) &&
                (u.preventDefault(), u.stopPropagation()));
        }
        return c.postDispatch && c.postDispatch.call(this, u), u.result;
      }
    },
    handlers: function (e, t) {
      var n,
        r,
        i,
        o,
        a,
        s = [],
        u = t.delegateCount,
        l = e.target;
      if (u && l.nodeType && !("click" === e.type && 1 <= e.button))
        for (; l !== this; l = l.parentNode || this)
          if (1 === l.nodeType && ("click" !== e.type || !0 !== l.disabled)) {
            for (o = [], a = {}, n = 0; n < u; n++)
              void 0 === a[(i = (r = t[n]).selector + " ")] &&
                (a[i] = r.needsContext
                  ? -1 < ce(i, this).index(l)
                  : ce.find(i, this, null, [l]).length),
                a[i] && o.push(r);
            o.length && s.push({ elem: l, handlers: o });
          }
      return (
        (l = this), u < t.length && s.push({ elem: l, handlers: t.slice(u) }), s
      );
    },
    addProp: function (t, e) {
      Object.defineProperty(ce.Event.prototype, t, {
        enumerable: !0,
        configurable: !0,
        get: v(e)
          ? function () {
              if (this.originalEvent) return e(this.originalEvent);
            }
          : function () {
              if (this.originalEvent) return this.originalEvent[t];
            },
        set: function (e) {
          Object.defineProperty(this, t, {
            enumerable: !0,
            configurable: !0,
            writable: !0,
            value: e,
          });
        },
      });
    },
    fix: function (e) {
      return e[ce.expando] ? e : new ce.Event(e);
    },
    special: {
      load: { noBubble: !0 },
      click: {
        setup: function (e) {
          var t = this || e;
          return (
            we.test(t.type) && t.click && fe(t, "input") && He(t, "click", !0),
            !1
          );
        },
        trigger: function (e) {
          var t = this || e;
          return (
            we.test(t.type) && t.click && fe(t, "input") && He(t, "click"), !0
          );
        },
        _default: function (e) {
          var t = e.target;
          return (
            (we.test(t.type) &&
              t.click &&
              fe(t, "input") &&
              _.get(t, "click")) ||
            fe(t, "a")
          );
        },
      },
      beforeunload: {
        postDispatch: function (e) {
          void 0 !== e.result &&
            e.originalEvent &&
            (e.originalEvent.returnValue = e.result);
        },
      },
    },
  }),
    (ce.removeEvent = function (e, t, n) {
      e.removeEventListener && e.removeEventListener(t, n);
    }),
    (ce.Event = function (e, t) {
      if (!(this instanceof ce.Event)) return new ce.Event(e, t);
      e && e.type
        ? ((this.originalEvent = e),
          (this.type = e.type),
          (this.isDefaultPrevented =
            e.defaultPrevented ||
            (void 0 === e.defaultPrevented && !1 === e.returnValue)
              ? Ne
              : qe),
          (this.target =
            e.target && 3 === e.target.nodeType
              ? e.target.parentNode
              : e.target),
          (this.currentTarget = e.currentTarget),
          (this.relatedTarget = e.relatedTarget))
        : (this.type = e),
        t && ce.extend(this, t),
        (this.timeStamp = (e && e.timeStamp) || Date.now()),
        (this[ce.expando] = !0);
    }),
    (ce.Event.prototype = {
      constructor: ce.Event,
      isDefaultPrevented: qe,
      isPropagationStopped: qe,
      isImmediatePropagationStopped: qe,
      isSimulated: !1,
      preventDefault: function () {
        var e = this.originalEvent;
        (this.isDefaultPrevented = Ne),
          e && !this.isSimulated && e.preventDefault();
      },
      stopPropagation: function () {
        var e = this.originalEvent;
        (this.isPropagationStopped = Ne),
          e && !this.isSimulated && e.stopPropagation();
      },
      stopImmediatePropagation: function () {
        var e = this.originalEvent;
        (this.isImmediatePropagationStopped = Ne),
          e && !this.isSimulated && e.stopImmediatePropagation(),
          this.stopPropagation();
      },
    }),
    ce.each(
      {
        altKey: !0,
        bubbles: !0,
        cancelable: !0,
        changedTouches: !0,
        ctrlKey: !0,
        detail: !0,
        eventPhase: !0,
        metaKey: !0,
        pageX: !0,
        pageY: !0,
        shiftKey: !0,
        view: !0,
        char: !0,
        code: !0,
        charCode: !0,
        key: !0,
        keyCode: !0,
        button: !0,
        buttons: !0,
        clientX: !0,
        clientY: !0,
        offsetX: !0,
        offsetY: !0,
        pointerId: !0,
        pointerType: !0,
        screenX: !0,
        screenY: !0,
        targetTouches: !0,
        toElement: !0,
        touches: !0,
        which: !0,
      },
      ce.event.addProp
    ),
    ce.each({ focus: "focusin", blur: "focusout" }, function (r, i) {
      function o(e) {
        if (C.documentMode) {
          var t = _.get(this, "handle"),
            n = ce.event.fix(e);
          (n.type = "focusin" === e.type ? "focus" : "blur"),
            (n.isSimulated = !0),
            t(e),
            n.target === n.currentTarget && t(n);
        } else ce.event.simulate(i, e.target, ce.event.fix(e));
      }
      (ce.event.special[r] = {
        setup: function () {
          var e;
          if ((He(this, r, !0), !C.documentMode)) return !1;
          (e = _.get(this, i)) || this.addEventListener(i, o),
            _.set(this, i, (e || 0) + 1);
        },
        trigger: function () {
          return He(this, r), !0;
        },
        teardown: function () {
          var e;
          if (!C.documentMode) return !1;
          (e = _.get(this, i) - 1)
            ? _.set(this, i, e)
            : (this.removeEventListener(i, o), _.remove(this, i));
        },
        _default: function (e) {
          return _.get(e.target, r);
        },
        delegateType: i,
      }),
        (ce.event.special[i] = {
          setup: function () {
            var e = this.ownerDocument || this.document || this,
              t = C.documentMode ? this : e,
              n = _.get(t, i);
            n ||
              (C.documentMode
                ? this.addEventListener(i, o)
                : e.addEventListener(r, o, !0)),
              _.set(t, i, (n || 0) + 1);
          },
          teardown: function () {
            var e = this.ownerDocument || this.document || this,
              t = C.documentMode ? this : e,
              n = _.get(t, i) - 1;
            n
              ? _.set(t, i, n)
              : (C.documentMode
                  ? this.removeEventListener(i, o)
                  : e.removeEventListener(r, o, !0),
                _.remove(t, i));
          },
        });
    }),
    ce.each(
      {
        mouseenter: "mouseover",
        mouseleave: "mouseout",
        pointerenter: "pointerover",
        pointerleave: "pointerout",
      },
      function (e, i) {
        ce.event.special[e] = {
          delegateType: i,
          bindType: i,
          handle: function (e) {
            var t,
              n = e.relatedTarget,
              r = e.handleObj;
            return (
              (n && (n === this || ce.contains(this, n))) ||
                ((e.type = r.origType),
                (t = r.handler.apply(this, arguments)),
                (e.type = i)),
              t
            );
          },
        };
      }
    ),
    ce.fn.extend({
      on: function (e, t, n, r) {
        return Le(this, e, t, n, r);
      },
      one: function (e, t, n, r) {
        return Le(this, e, t, n, r, 1);
      },
      off: function (e, t, n) {
        var r, i;
        if (e && e.preventDefault && e.handleObj)
          return (
            (r = e.handleObj),
            ce(e.delegateTarget).off(
              r.namespace ? r.origType + "." + r.namespace : r.origType,
              r.selector,
              r.handler
            ),
            this
          );
        if ("object" == typeof e) {
          for (i in e) this.off(i, t, e[i]);
          return this;
        }
        return (
          (!1 !== t && "function" != typeof t) || ((n = t), (t = void 0)),
          !1 === n && (n = qe),
          this.each(function () {
            ce.event.remove(this, e, n, t);
          })
        );
      },
    });
  var Oe = /<script|<style|<link/i,
    Pe = /checked\s*(?:[^=]|=\s*.checked.)/i,
    Re = /^\s*<!\[CDATA\[|\]\]>\s*$/g;
  function Me(e, t) {
    return (
      (fe(e, "table") &&
        fe(11 !== t.nodeType ? t : t.firstChild, "tr") &&
        ce(e).children("tbody")[0]) ||
      e
    );
  }
  function Ie(e) {
    return (e.type = (null !== e.getAttribute("type")) + "/" + e.type), e;
  }
  function We(e) {
    return (
      "true/" === (e.type || "").slice(0, 5)
        ? (e.type = e.type.slice(5))
        : e.removeAttribute("type"),
      e
    );
  }
  function Fe(e, t) {
    var n, r, i, o, a, s;
    if (1 === t.nodeType) {
      if (_.hasData(e) && (s = _.get(e).events))
        for (i in (_.remove(t, "handle events"), s))
          for (n = 0, r = s[i].length; n < r; n++) ce.event.add(t, i, s[i][n]);
      X.hasData(e) && ((o = X.access(e)), (a = ce.extend({}, o)), X.set(t, a));
    }
  }
  function $e(n, r, i, o) {
    r = g(r);
    var e,
      t,
      a,
      s,
      u,
      l,
      c = 0,
      f = n.length,
      p = f - 1,
      d = r[0],
      h = v(d);
    if (h || (1 < f && "string" == typeof d && !le.checkClone && Pe.test(d)))
      return n.each(function (e) {
        var t = n.eq(e);
        h && (r[0] = d.call(this, e, t.html())), $e(t, r, i, o);
      });
    if (
      f &&
      ((t = (e = Ae(r, n[0].ownerDocument, !1, n, o)).firstChild),
      1 === e.childNodes.length && (e = t),
      t || o)
    ) {
      for (s = (a = ce.map(Se(e, "script"), Ie)).length; c < f; c++)
        (u = e),
          c !== p &&
            ((u = ce.clone(u, !0, !0)), s && ce.merge(a, Se(u, "script"))),
          i.call(n[c], u, c);
      if (s)
        for (
          l = a[a.length - 1].ownerDocument, ce.map(a, We), c = 0;
          c < s;
          c++
        )
          (u = a[c]),
            Ce.test(u.type || "") &&
              !_.access(u, "globalEval") &&
              ce.contains(l, u) &&
              (u.src && "module" !== (u.type || "").toLowerCase()
                ? ce._evalUrl &&
                  !u.noModule &&
                  ce._evalUrl(
                    u.src,
                    { nonce: u.nonce || u.getAttribute("nonce") },
                    l
                  )
                : m(u.textContent.replace(Re, ""), u, l));
    }
    return n;
  }
  function Be(e, t, n) {
    for (var r, i = t ? ce.filter(t, e) : e, o = 0; null != (r = i[o]); o++)
      n || 1 !== r.nodeType || ce.cleanData(Se(r)),
        r.parentNode &&
          (n && K(r) && Ee(Se(r, "script")), r.parentNode.removeChild(r));
    return e;
  }
  ce.extend({
    htmlPrefilter: function (e) {
      return e;
    },
    clone: function (e, t, n) {
      var r,
        i,
        o,
        a,
        s,
        u,
        l,
        c = e.cloneNode(!0),
        f = K(e);
      if (
        !(
          le.noCloneChecked ||
          (1 !== e.nodeType && 11 !== e.nodeType) ||
          ce.isXMLDoc(e)
        )
      )
        for (a = Se(c), r = 0, i = (o = Se(e)).length; r < i; r++)
          (s = o[r]),
            (u = a[r]),
            void 0,
            "input" === (l = u.nodeName.toLowerCase()) && we.test(s.type)
              ? (u.checked = s.checked)
              : ("input" !== l && "textarea" !== l) ||
                (u.defaultValue = s.defaultValue);
      if (t)
        if (n)
          for (o = o || Se(e), a = a || Se(c), r = 0, i = o.length; r < i; r++)
            Fe(o[r], a[r]);
        else Fe(e, c);
      return (
        0 < (a = Se(c, "script")).length && Ee(a, !f && Se(e, "script")), c
      );
    },
    cleanData: function (e) {
      for (var t, n, r, i = ce.event.special, o = 0; void 0 !== (n = e[o]); o++)
        if ($(n)) {
          if ((t = n[_.expando])) {
            if (t.events)
              for (r in t.events)
                i[r] ? ce.event.remove(n, r) : ce.removeEvent(n, r, t.handle);
            n[_.expando] = void 0;
          }
          n[X.expando] && (n[X.expando] = void 0);
        }
    },
  }),
    ce.fn.extend({
      detach: function (e) {
        return Be(this, e, !0);
      },
      remove: function (e) {
        return Be(this, e);
      },
      text: function (e) {
        return R(
          this,
          function (e) {
            return void 0 === e
              ? ce.text(this)
              : this.empty().each(function () {
                  (1 !== this.nodeType &&
                    11 !== this.nodeType &&
                    9 !== this.nodeType) ||
                    (this.textContent = e);
                });
          },
          null,
          e,
          arguments.length
        );
      },
      append: function () {
        return $e(this, arguments, function (e) {
          (1 !== this.nodeType &&
            11 !== this.nodeType &&
            9 !== this.nodeType) ||
            Me(this, e).appendChild(e);
        });
      },
      prepend: function () {
        return $e(this, arguments, function (e) {
          if (
            1 === this.nodeType ||
            11 === this.nodeType ||
            9 === this.nodeType
          ) {
            var t = Me(this, e);
            t.insertBefore(e, t.firstChild);
          }
        });
      },
      before: function () {
        return $e(this, arguments, function (e) {
          this.parentNode && this.parentNode.insertBefore(e, this);
        });
      },
      after: function () {
        return $e(this, arguments, function (e) {
          this.parentNode && this.parentNode.insertBefore(e, this.nextSibling);
        });
      },
      empty: function () {
        for (var e, t = 0; null != (e = this[t]); t++)
          1 === e.nodeType && (ce.cleanData(Se(e, !1)), (e.textContent = ""));
        return this;
      },
      clone: function (e, t) {
        return (
          (e = null != e && e),
          (t = null == t ? e : t),
          this.map(function () {
            return ce.clone(this, e, t);
          })
        );
      },
      html: function (e) {
        return R(
          this,
          function (e) {
            var t = this[0] || {},
              n = 0,
              r = this.length;
            if (void 0 === e && 1 === t.nodeType) return t.innerHTML;
            if (
              "string" == typeof e &&
              !Oe.test(e) &&
              !ke[(Te.exec(e) || ["", ""])[1].toLowerCase()]
            ) {
              e = ce.htmlPrefilter(e);
              try {
                for (; n < r; n++)
                  1 === (t = this[n] || {}).nodeType &&
                    (ce.cleanData(Se(t, !1)), (t.innerHTML = e));
                t = 0;
              } catch (e) {}
            }
            t && this.empty().append(e);
          },
          null,
          e,
          arguments.length
        );
      },
      replaceWith: function () {
        var n = [];
        return $e(
          this,
          arguments,
          function (e) {
            var t = this.parentNode;
            ce.inArray(this, n) < 0 &&
              (ce.cleanData(Se(this)), t && t.replaceChild(e, this));
          },
          n
        );
      },
    }),
    ce.each(
      {
        appendTo: "append",
        prependTo: "prepend",
        insertBefore: "before",
        insertAfter: "after",
        replaceAll: "replaceWith",
      },
      function (e, a) {
        ce.fn[e] = function (e) {
          for (var t, n = [], r = ce(e), i = r.length - 1, o = 0; o <= i; o++)
            (t = o === i ? this : this.clone(!0)),
              ce(r[o])[a](t),
              s.apply(n, t.get());
          return this.pushStack(n);
        };
      }
    );
  var _e = new RegExp("^(" + G + ")(?!px)[a-z%]+$", "i"),
    Xe = /^--/,
    Ue = function (e) {
      var t = e.ownerDocument.defaultView;
      return (t && t.opener) || (t = ie), t.getComputedStyle(e);
    },
    ze = function (e, t, n) {
      var r,
        i,
        o = {};
      for (i in t) (o[i] = e.style[i]), (e.style[i] = t[i]);
      for (i in ((r = n.call(e)), t)) e.style[i] = o[i];
      return r;
    },
    Ve = new RegExp(Q.join("|"), "i");
  function Ge(e, t, n) {
    var r,
      i,
      o,
      a,
      s = Xe.test(t),
      u = e.style;
    return (
      (n = n || Ue(e)) &&
        ((a = n.getPropertyValue(t) || n[t]),
        s && a && (a = a.replace(ve, "$1") || void 0),
        "" !== a || K(e) || (a = ce.style(e, t)),
        !le.pixelBoxStyles() &&
          _e.test(a) &&
          Ve.test(t) &&
          ((r = u.width),
          (i = u.minWidth),
          (o = u.maxWidth),
          (u.minWidth = u.maxWidth = u.width = a),
          (a = n.width),
          (u.width = r),
          (u.minWidth = i),
          (u.maxWidth = o))),
      void 0 !== a ? a + "" : a
    );
  }
  function Ye(e, t) {
    return {
      get: function () {
        if (!e()) return (this.get = t).apply(this, arguments);
        delete this.get;
      },
    };
  }
  !(function () {
    function e() {
      if (l) {
        (u.style.cssText =
          "position:absolute;left:-11111px;width:60px;margin-top:1px;padding:0;border:0"),
          (l.style.cssText =
            "position:relative;display:block;box-sizing:border-box;overflow:scroll;margin:auto;border:1px;padding:1px;width:60%;top:1%"),
          J.appendChild(u).appendChild(l);
        var e = ie.getComputedStyle(l);
        (n = "1%" !== e.top),
          (s = 12 === t(e.marginLeft)),
          (l.style.right = "60%"),
          (o = 36 === t(e.right)),
          (r = 36 === t(e.width)),
          (l.style.position = "absolute"),
          (i = 12 === t(l.offsetWidth / 3)),
          J.removeChild(u),
          (l = null);
      }
    }
    function t(e) {
      return Math.round(parseFloat(e));
    }
    var n,
      r,
      i,
      o,
      a,
      s,
      u = C.createElement("div"),
      l = C.createElement("div");
    l.style &&
      ((l.style.backgroundClip = "content-box"),
      (l.cloneNode(!0).style.backgroundClip = ""),
      (le.clearCloneStyle = "content-box" === l.style.backgroundClip),
      ce.extend(le, {
        boxSizingReliable: function () {
          return e(), r;
        },
        pixelBoxStyles: function () {
          return e(), o;
        },
        pixelPosition: function () {
          return e(), n;
        },
        reliableMarginLeft: function () {
          return e(), s;
        },
        scrollboxSize: function () {
          return e(), i;
        },
        reliableTrDimensions: function () {
          var e, t, n, r;
          return (
            null == a &&
              ((e = C.createElement("table")),
              (t = C.createElement("tr")),
              (n = C.createElement("div")),
              (e.style.cssText =
                "position:absolute;left:-11111px;border-collapse:separate"),
              (t.style.cssText = "border:1px solid"),
              (t.style.height = "1px"),
              (n.style.height = "9px"),
              (n.style.display = "block"),
              J.appendChild(e).appendChild(t).appendChild(n),
              (r = ie.getComputedStyle(t)),
              (a =
                parseInt(r.height, 10) +
                  parseInt(r.borderTopWidth, 10) +
                  parseInt(r.borderBottomWidth, 10) ===
                t.offsetHeight),
              J.removeChild(e)),
            a
          );
        },
      }));
  })();
  var Qe = ["Webkit", "Moz", "ms"],
    Je = C.createElement("div").style,
    Ke = {};
  function Ze(e) {
    var t = ce.cssProps[e] || Ke[e];
    return (
      t ||
      (e in Je
        ? e
        : (Ke[e] =
            (function (e) {
              var t = e[0].toUpperCase() + e.slice(1),
                n = Qe.length;
              while (n--) if ((e = Qe[n] + t) in Je) return e;
            })(e) || e))
    );
  }
  var et = /^(none|table(?!-c[ea]).+)/,
    tt = { position: "absolute", visibility: "hidden", display: "block" },
    nt = { letterSpacing: "0", fontWeight: "400" };
  function rt(e, t, n) {
    var r = Y.exec(t);
    return r ? Math.max(0, r[2] - (n || 0)) + (r[3] || "px") : t;
  }
  function it(e, t, n, r, i, o) {
    var a = "width" === t ? 1 : 0,
      s = 0,
      u = 0,
      l = 0;
    if (n === (r ? "border" : "content")) return 0;
    for (; a < 4; a += 2)
      "margin" === n && (l += ce.css(e, n + Q[a], !0, i)),
        r
          ? ("content" === n && (u -= ce.css(e, "padding" + Q[a], !0, i)),
            "margin" !== n &&
              (u -= ce.css(e, "border" + Q[a] + "Width", !0, i)))
          : ((u += ce.css(e, "padding" + Q[a], !0, i)),
            "padding" !== n
              ? (u += ce.css(e, "border" + Q[a] + "Width", !0, i))
              : (s += ce.css(e, "border" + Q[a] + "Width", !0, i)));
    return (
      !r &&
        0 <= o &&
        (u +=
          Math.max(
            0,
            Math.ceil(
              e["offset" + t[0].toUpperCase() + t.slice(1)] - o - u - s - 0.5
            )
          ) || 0),
      u + l
    );
  }
  function ot(e, t, n) {
    var r = Ue(e),
      i =
        (!le.boxSizingReliable() || n) &&
        "border-box" === ce.css(e, "boxSizing", !1, r),
      o = i,
      a = Ge(e, t, r),
      s = "offset" + t[0].toUpperCase() + t.slice(1);
    if (_e.test(a)) {
      if (!n) return a;
      a = "auto";
    }
    return (
      ((!le.boxSizingReliable() && i) ||
        (!le.reliableTrDimensions() && fe(e, "tr")) ||
        "auto" === a ||
        (!parseFloat(a) && "inline" === ce.css(e, "display", !1, r))) &&
        e.getClientRects().length &&
        ((i = "border-box" === ce.css(e, "boxSizing", !1, r)),
        (o = s in e) && (a = e[s])),
      (a = parseFloat(a) || 0) +
        it(e, t, n || (i ? "border" : "content"), o, r, a) +
        "px"
    );
  }
  function at(e, t, n, r, i) {
    return new at.prototype.init(e, t, n, r, i);
  }
  ce.extend({
    cssHooks: {
      opacity: {
        get: function (e, t) {
          if (t) {
            var n = Ge(e, "opacity");
            return "" === n ? "1" : n;
          }
        },
      },
    },
    cssNumber: {
      animationIterationCount: !0,
      aspectRatio: !0,
      borderImageSlice: !0,
      columnCount: !0,
      flexGrow: !0,
      flexShrink: !0,
      fontWeight: !0,
      gridArea: !0,
      gridColumn: !0,
      gridColumnEnd: !0,
      gridColumnStart: !0,
      gridRow: !0,
      gridRowEnd: !0,
      gridRowStart: !0,
      lineHeight: !0,
      opacity: !0,
      order: !0,
      orphans: !0,
      scale: !0,
      widows: !0,
      zIndex: !0,
      zoom: !0,
      fillOpacity: !0,
      floodOpacity: !0,
      stopOpacity: !0,
      strokeMiterlimit: !0,
      strokeOpacity: !0,
    },
    cssProps: {},
    style: function (e, t, n, r) {
      if (e && 3 !== e.nodeType && 8 !== e.nodeType && e.style) {
        var i,
          o,
          a,
          s = F(t),
          u = Xe.test(t),
          l = e.style;
        if (
          (u || (t = Ze(s)),
          (a = ce.cssHooks[t] || ce.cssHooks[s]),
          void 0 === n)
        )
          return a && "get" in a && void 0 !== (i = a.get(e, !1, r)) ? i : l[t];
        "string" === (o = typeof n) &&
          (i = Y.exec(n)) &&
          i[1] &&
          ((n = te(e, t, i)), (o = "number")),
          null != n &&
            n == n &&
            ("number" !== o ||
              u ||
              (n += (i && i[3]) || (ce.cssNumber[s] ? "" : "px")),
            le.clearCloneStyle ||
              "" !== n ||
              0 !== t.indexOf("background") ||
              (l[t] = "inherit"),
            (a && "set" in a && void 0 === (n = a.set(e, n, r))) ||
              (u ? l.setProperty(t, n) : (l[t] = n)));
      }
    },
    css: function (e, t, n, r) {
      var i,
        o,
        a,
        s = F(t);
      return (
        Xe.test(t) || (t = Ze(s)),
        (a = ce.cssHooks[t] || ce.cssHooks[s]) &&
          "get" in a &&
          (i = a.get(e, !0, n)),
        void 0 === i && (i = Ge(e, t, r)),
        "normal" === i && t in nt && (i = nt[t]),
        "" === n || n
          ? ((o = parseFloat(i)), !0 === n || isFinite(o) ? o || 0 : i)
          : i
      );
    },
  }),
    ce.each(["height", "width"], function (e, u) {
      ce.cssHooks[u] = {
        get: function (e, t, n) {
          if (t)
            return !et.test(ce.css(e, "display")) ||
              (e.getClientRects().length && e.getBoundingClientRect().width)
              ? ot(e, u, n)
              : ze(e, tt, function () {
                  return ot(e, u, n);
                });
        },
        set: function (e, t, n) {
          var r,
            i = Ue(e),
            o = !le.scrollboxSize() && "absolute" === i.position,
            a = (o || n) && "border-box" === ce.css(e, "boxSizing", !1, i),
            s = n ? it(e, u, n, a, i) : 0;
          return (
            a &&
              o &&
              (s -= Math.ceil(
                e["offset" + u[0].toUpperCase() + u.slice(1)] -
                  parseFloat(i[u]) -
                  it(e, u, "border", !1, i) -
                  0.5
              )),
            s &&
              (r = Y.exec(t)) &&
              "px" !== (r[3] || "px") &&
              ((e.style[u] = t), (t = ce.css(e, u))),
            rt(0, t, s)
          );
        },
      };
    }),
    (ce.cssHooks.marginLeft = Ye(le.reliableMarginLeft, function (e, t) {
      if (t)
        return (
          (parseFloat(Ge(e, "marginLeft")) ||
            e.getBoundingClientRect().left -
              ze(e, { marginLeft: 0 }, function () {
                return e.getBoundingClientRect().left;
              })) + "px"
        );
    })),
    ce.each({ margin: "", padding: "", border: "Width" }, function (i, o) {
      (ce.cssHooks[i + o] = {
        expand: function (e) {
          for (
            var t = 0, n = {}, r = "string" == typeof e ? e.split(" ") : [e];
            t < 4;
            t++
          )
            n[i + Q[t] + o] = r[t] || r[t - 2] || r[0];
          return n;
        },
      }),
        "margin" !== i && (ce.cssHooks[i + o].set = rt);
    }),
    ce.fn.extend({
      css: function (e, t) {
        return R(
          this,
          function (e, t, n) {
            var r,
              i,
              o = {},
              a = 0;
            if (Array.isArray(t)) {
              for (r = Ue(e), i = t.length; a < i; a++)
                o[t[a]] = ce.css(e, t[a], !1, r);
              return o;
            }
            return void 0 !== n ? ce.style(e, t, n) : ce.css(e, t);
          },
          e,
          t,
          1 < arguments.length
        );
      },
    }),
    (((ce.Tween = at).prototype = {
      constructor: at,
      init: function (e, t, n, r, i, o) {
        (this.elem = e),
          (this.prop = n),
          (this.easing = i || ce.easing._default),
          (this.options = t),
          (this.start = this.now = this.cur()),
          (this.end = r),
          (this.unit = o || (ce.cssNumber[n] ? "" : "px"));
      },
      cur: function () {
        var e = at.propHooks[this.prop];
        return e && e.get ? e.get(this) : at.propHooks._default.get(this);
      },
      run: function (e) {
        var t,
          n = at.propHooks[this.prop];
        return (
          this.options.duration
            ? (this.pos = t =
                ce.easing[this.easing](
                  e,
                  this.options.duration * e,
                  0,
                  1,
                  this.options.duration
                ))
            : (this.pos = t = e),
          (this.now = (this.end - this.start) * t + this.start),
          this.options.step &&
            this.options.step.call(this.elem, this.now, this),
          n && n.set ? n.set(this) : at.propHooks._default.set(this),
          this
        );
      },
    }).init.prototype = at.prototype),
    ((at.propHooks = {
      _default: {
        get: function (e) {
          var t;
          return 1 !== e.elem.nodeType ||
            (null != e.elem[e.prop] && null == e.elem.style[e.prop])
            ? e.elem[e.prop]
            : (t = ce.css(e.elem, e.prop, "")) && "auto" !== t
            ? t
            : 0;
        },
        set: function (e) {
          ce.fx.step[e.prop]
            ? ce.fx.step[e.prop](e)
            : 1 !== e.elem.nodeType ||
              (!ce.cssHooks[e.prop] && null == e.elem.style[Ze(e.prop)])
            ? (e.elem[e.prop] = e.now)
            : ce.style(e.elem, e.prop, e.now + e.unit);
        },
      },
    }).scrollTop = at.propHooks.scrollLeft =
      {
        set: function (e) {
          e.elem.nodeType && e.elem.parentNode && (e.elem[e.prop] = e.now);
        },
      }),
    (ce.easing = {
      linear: function (e) {
        return e;
      },
      swing: function (e) {
        return 0.5 - Math.cos(e * Math.PI) / 2;
      },
      _default: "swing",
    }),
    (ce.fx = at.prototype.init),
    (ce.fx.step = {});
  var st,
    ut,
    lt,
    ct,
    ft = /^(?:toggle|show|hide)$/,
    pt = /queueHooks$/;
  function dt() {
    ut &&
      (!1 === C.hidden && ie.requestAnimationFrame
        ? ie.requestAnimationFrame(dt)
        : ie.setTimeout(dt, ce.fx.interval),
      ce.fx.tick());
  }
  function ht() {
    return (
      ie.setTimeout(function () {
        st = void 0;
      }),
      (st = Date.now())
    );
  }
  function gt(e, t) {
    var n,
      r = 0,
      i = { height: e };
    for (t = t ? 1 : 0; r < 4; r += 2 - t)
      i["margin" + (n = Q[r])] = i["padding" + n] = e;
    return t && (i.opacity = i.width = e), i;
  }
  function vt(e, t, n) {
    for (
      var r,
        i = (yt.tweeners[t] || []).concat(yt.tweeners["*"]),
        o = 0,
        a = i.length;
      o < a;
      o++
    )
      if ((r = i[o].call(n, t, e))) return r;
  }
  function yt(o, e, t) {
    var n,
      a,
      r = 0,
      i = yt.prefilters.length,
      s = ce.Deferred().always(function () {
        delete u.elem;
      }),
      u = function () {
        if (a) return !1;
        for (
          var e = st || ht(),
            t = Math.max(0, l.startTime + l.duration - e),
            n = 1 - (t / l.duration || 0),
            r = 0,
            i = l.tweens.length;
          r < i;
          r++
        )
          l.tweens[r].run(n);
        return (
          s.notifyWith(o, [l, n, t]),
          n < 1 && i
            ? t
            : (i || s.notifyWith(o, [l, 1, 0]), s.resolveWith(o, [l]), !1)
        );
      },
      l = s.promise({
        elem: o,
        props: ce.extend({}, e),
        opts: ce.extend(
          !0,
          { specialEasing: {}, easing: ce.easing._default },
          t
        ),
        originalProperties: e,
        originalOptions: t,
        startTime: st || ht(),
        duration: t.duration,
        tweens: [],
        createTween: function (e, t) {
          var n = ce.Tween(
            o,
            l.opts,
            e,
            t,
            l.opts.specialEasing[e] || l.opts.easing
          );
          return l.tweens.push(n), n;
        },
        stop: function (e) {
          var t = 0,
            n = e ? l.tweens.length : 0;
          if (a) return this;
          for (a = !0; t < n; t++) l.tweens[t].run(1);
          return (
            e
              ? (s.notifyWith(o, [l, 1, 0]), s.resolveWith(o, [l, e]))
              : s.rejectWith(o, [l, e]),
            this
          );
        },
      }),
      c = l.props;
    for (
      !(function (e, t) {
        var n, r, i, o, a;
        for (n in e)
          if (
            ((i = t[(r = F(n))]),
            (o = e[n]),
            Array.isArray(o) && ((i = o[1]), (o = e[n] = o[0])),
            n !== r && ((e[r] = o), delete e[n]),
            (a = ce.cssHooks[r]) && ("expand" in a))
          )
            for (n in ((o = a.expand(o)), delete e[r], o))
              (n in e) || ((e[n] = o[n]), (t[n] = i));
          else t[r] = i;
      })(c, l.opts.specialEasing);
      r < i;
      r++
    )
      if ((n = yt.prefilters[r].call(l, o, c, l.opts)))
        return (
          v(n.stop) &&
            (ce._queueHooks(l.elem, l.opts.queue).stop = n.stop.bind(n)),
          n
        );
    return (
      ce.map(c, vt, l),
      v(l.opts.start) && l.opts.start.call(o, l),
      l
        .progress(l.opts.progress)
        .done(l.opts.done, l.opts.complete)
        .fail(l.opts.fail)
        .always(l.opts.always),
      ce.fx.timer(ce.extend(u, { elem: o, anim: l, queue: l.opts.queue })),
      l
    );
  }
  (ce.Animation = ce.extend(yt, {
    tweeners: {
      "*": [
        function (e, t) {
          var n = this.createTween(e, t);
          return te(n.elem, e, Y.exec(t), n), n;
        },
      ],
    },
    tweener: function (e, t) {
      v(e) ? ((t = e), (e = ["*"])) : (e = e.match(D));
      for (var n, r = 0, i = e.length; r < i; r++)
        (n = e[r]),
          (yt.tweeners[n] = yt.tweeners[n] || []),
          yt.tweeners[n].unshift(t);
    },
    prefilters: [
      function (e, t, n) {
        var r,
          i,
          o,
          a,
          s,
          u,
          l,
          c,
          f = "width" in t || "height" in t,
          p = this,
          d = {},
          h = e.style,
          g = e.nodeType && ee(e),
          v = _.get(e, "fxshow");
        for (r in (n.queue ||
          (null == (a = ce._queueHooks(e, "fx")).unqueued &&
            ((a.unqueued = 0),
            (s = a.empty.fire),
            (a.empty.fire = function () {
              a.unqueued || s();
            })),
          a.unqueued++,
          p.always(function () {
            p.always(function () {
              a.unqueued--, ce.queue(e, "fx").length || a.empty.fire();
            });
          })),
        t))
          if (((i = t[r]), ft.test(i))) {
            if (
              (delete t[r],
              (o = o || "toggle" === i),
              i === (g ? "hide" : "show"))
            ) {
              if ("show" !== i || !v || void 0 === v[r]) continue;
              g = !0;
            }
            d[r] = (v && v[r]) || ce.style(e, r);
          }
        if ((u = !ce.isEmptyObject(t)) || !ce.isEmptyObject(d))
          for (r in (f &&
            1 === e.nodeType &&
            ((n.overflow = [h.overflow, h.overflowX, h.overflowY]),
            null == (l = v && v.display) && (l = _.get(e, "display")),
            "none" === (c = ce.css(e, "display")) &&
              (l
                ? (c = l)
                : (re([e], !0),
                  (l = e.style.display || l),
                  (c = ce.css(e, "display")),
                  re([e]))),
            ("inline" === c || ("inline-block" === c && null != l)) &&
              "none" === ce.css(e, "float") &&
              (u ||
                (p.done(function () {
                  h.display = l;
                }),
                null == l && ((c = h.display), (l = "none" === c ? "" : c))),
              (h.display = "inline-block"))),
          n.overflow &&
            ((h.overflow = "hidden"),
            p.always(function () {
              (h.overflow = n.overflow[0]),
                (h.overflowX = n.overflow[1]),
                (h.overflowY = n.overflow[2]);
            })),
          (u = !1),
          d))
            u ||
              (v
                ? "hidden" in v && (g = v.hidden)
                : (v = _.access(e, "fxshow", { display: l })),
              o && (v.hidden = !g),
              g && re([e], !0),
              p.done(function () {
                for (r in (g || re([e]), _.remove(e, "fxshow"), d))
                  ce.style(e, r, d[r]);
              })),
              (u = vt(g ? v[r] : 0, r, p)),
              r in v ||
                ((v[r] = u.start), g && ((u.end = u.start), (u.start = 0)));
      },
    ],
    prefilter: function (e, t) {
      t ? yt.prefilters.unshift(e) : yt.prefilters.push(e);
    },
  })),
    (ce.speed = function (e, t, n) {
      var r =
        e && "object" == typeof e
          ? ce.extend({}, e)
          : {
              complete: n || (!n && t) || (v(e) && e),
              duration: e,
              easing: (n && t) || (t && !v(t) && t),
            };
      return (
        ce.fx.off
          ? (r.duration = 0)
          : "number" != typeof r.duration &&
            (r.duration in ce.fx.speeds
              ? (r.duration = ce.fx.speeds[r.duration])
              : (r.duration = ce.fx.speeds._default)),
        (null != r.queue && !0 !== r.queue) || (r.queue = "fx"),
        (r.old = r.complete),
        (r.complete = function () {
          v(r.old) && r.old.call(this), r.queue && ce.dequeue(this, r.queue);
        }),
        r
      );
    }),
    ce.fn.extend({
      fadeTo: function (e, t, n, r) {
        return this.filter(ee)
          .css("opacity", 0)
          .show()
          .end()
          .animate({ opacity: t }, e, n, r);
      },
      animate: function (t, e, n, r) {
        var i = ce.isEmptyObject(t),
          o = ce.speed(e, n, r),
          a = function () {
            var e = yt(this, ce.extend({}, t), o);
            (i || _.get(this, "finish")) && e.stop(!0);
          };
        return (
          (a.finish = a),
          i || !1 === o.queue ? this.each(a) : this.queue(o.queue, a)
        );
      },
      stop: function (i, e, o) {
        var a = function (e) {
          var t = e.stop;
          delete e.stop, t(o);
        };
        return (
          "string" != typeof i && ((o = e), (e = i), (i = void 0)),
          e && this.queue(i || "fx", []),
          this.each(function () {
            var e = !0,
              t = null != i && i + "queueHooks",
              n = ce.timers,
              r = _.get(this);
            if (t) r[t] && r[t].stop && a(r[t]);
            else for (t in r) r[t] && r[t].stop && pt.test(t) && a(r[t]);
            for (t = n.length; t--; )
              n[t].elem !== this ||
                (null != i && n[t].queue !== i) ||
                (n[t].anim.stop(o), (e = !1), n.splice(t, 1));
            (!e && o) || ce.dequeue(this, i);
          })
        );
      },
      finish: function (a) {
        return (
          !1 !== a && (a = a || "fx"),
          this.each(function () {
            var e,
              t = _.get(this),
              n = t[a + "queue"],
              r = t[a + "queueHooks"],
              i = ce.timers,
              o = n ? n.length : 0;
            for (
              t.finish = !0,
                ce.queue(this, a, []),
                r && r.stop && r.stop.call(this, !0),
                e = i.length;
              e--;

            )
              i[e].elem === this &&
                i[e].queue === a &&
                (i[e].anim.stop(!0), i.splice(e, 1));
            for (e = 0; e < o; e++)
              n[e] && n[e].finish && n[e].finish.call(this);
            delete t.finish;
          })
        );
      },
    }),
    ce.each(["toggle", "show", "hide"], function (e, r) {
      var i = ce.fn[r];
      ce.fn[r] = function (e, t, n) {
        return null == e || "boolean" == typeof e
          ? i.apply(this, arguments)
          : this.animate(gt(r, !0), e, t, n);
      };
    }),
    ce.each(
      {
        slideDown: gt("show"),
        slideUp: gt("hide"),
        slideToggle: gt("toggle"),
        fadeIn: { opacity: "show" },
        fadeOut: { opacity: "hide" },
        fadeToggle: { opacity: "toggle" },
      },
      function (e, r) {
        ce.fn[e] = function (e, t, n) {
          return this.animate(r, e, t, n);
        };
      }
    ),
    (ce.timers = []),
    (ce.fx.tick = function () {
      var e,
        t = 0,
        n = ce.timers;
      for (st = Date.now(); t < n.length; t++)
        (e = n[t])() || n[t] !== e || n.splice(t--, 1);
      n.length || ce.fx.stop(), (st = void 0);
    }),
    (ce.fx.timer = function (e) {
      ce.timers.push(e), ce.fx.start();
    }),
    (ce.fx.interval = 13),
    (ce.fx.start = function () {
      ut || ((ut = !0), dt());
    }),
    (ce.fx.stop = function () {
      ut = null;
    }),
    (ce.fx.speeds = { slow: 600, fast: 200, _default: 400 }),
    (ce.fn.delay = function (r, e) {
      return (
        (r = (ce.fx && ce.fx.speeds[r]) || r),
        (e = e || "fx"),
        this.queue(e, function (e, t) {
          var n = ie.setTimeout(e, r);
          t.stop = function () {
            ie.clearTimeout(n);
          };
        })
      );
    }),
    (lt = C.createElement("input")),
    (ct = C.createElement("select").appendChild(C.createElement("option"))),
    (lt.type = "checkbox"),
    (le.checkOn = "" !== lt.value),
    (le.optSelected = ct.selected),
    ((lt = C.createElement("input")).value = "t"),
    (lt.type = "radio"),
    (le.radioValue = "t" === lt.value);
  var mt,
    xt = ce.expr.attrHandle;
  ce.fn.extend({
    attr: function (e, t) {
      return R(this, ce.attr, e, t, 1 < arguments.length);
    },
    removeAttr: function (e) {
      return this.each(function () {
        ce.removeAttr(this, e);
      });
    },
  }),
    ce.extend({
      attr: function (e, t, n) {
        var r,
          i,
          o = e.nodeType;
        if (3 !== o && 8 !== o && 2 !== o)
          return "undefined" == typeof e.getAttribute
            ? ce.prop(e, t, n)
            : ((1 === o && ce.isXMLDoc(e)) ||
                (i =
                  ce.attrHooks[t.toLowerCase()] ||
                  (ce.expr.match.bool.test(t) ? mt : void 0)),
              void 0 !== n
                ? null === n
                  ? void ce.removeAttr(e, t)
                  : i && "set" in i && void 0 !== (r = i.set(e, n, t))
                  ? r
                  : (e.setAttribute(t, n + ""), n)
                : i && "get" in i && null !== (r = i.get(e, t))
                ? r
                : null == (r = ce.find.attr(e, t))
                ? void 0
                : r);
      },
      attrHooks: {
        type: {
          set: function (e, t) {
            if (!le.radioValue && "radio" === t && fe(e, "input")) {
              var n = e.value;
              return e.setAttribute("type", t), n && (e.value = n), t;
            }
          },
        },
      },
      removeAttr: function (e, t) {
        var n,
          r = 0,
          i = t && t.match(D);
        if (i && 1 === e.nodeType) while ((n = i[r++])) e.removeAttribute(n);
      },
    }),
    (mt = {
      set: function (e, t, n) {
        return !1 === t ? ce.removeAttr(e, n) : e.setAttribute(n, n), n;
      },
    }),
    ce.each(ce.expr.match.bool.source.match(/\w+/g), function (e, t) {
      var a = xt[t] || ce.find.attr;
      xt[t] = function (e, t, n) {
        var r,
          i,
          o = t.toLowerCase();
        return (
          n ||
            ((i = xt[o]),
            (xt[o] = r),
            (r = null != a(e, t, n) ? o : null),
            (xt[o] = i)),
          r
        );
      };
    });
  var bt = /^(?:input|select|textarea|button)$/i,
    wt = /^(?:a|area)$/i;
  function Tt(e) {
    return (e.match(D) || []).join(" ");
  }
  function Ct(e) {
    return (e.getAttribute && e.getAttribute("class")) || "";
  }
  function kt(e) {
    return Array.isArray(e) ? e : ("string" == typeof e && e.match(D)) || [];
  }
  ce.fn.extend({
    prop: function (e, t) {
      return R(this, ce.prop, e, t, 1 < arguments.length);
    },
    removeProp: function (e) {
      return this.each(function () {
        delete this[ce.propFix[e] || e];
      });
    },
  }),
    ce.extend({
      prop: function (e, t, n) {
        var r,
          i,
          o = e.nodeType;
        if (3 !== o && 8 !== o && 2 !== o)
          return (
            (1 === o && ce.isXMLDoc(e)) ||
              ((t = ce.propFix[t] || t), (i = ce.propHooks[t])),
            void 0 !== n
              ? i && "set" in i && void 0 !== (r = i.set(e, n, t))
                ? r
                : (e[t] = n)
              : i && "get" in i && null !== (r = i.get(e, t))
              ? r
              : e[t]
          );
      },
      propHooks: {
        tabIndex: {
          get: function (e) {
            var t = ce.find.attr(e, "tabindex");
            return t
              ? parseInt(t, 10)
              : bt.test(e.nodeName) || (wt.test(e.nodeName) && e.href)
              ? 0
              : -1;
          },
        },
      },
      propFix: { for: "htmlFor", class: "className" },
    }),
    le.optSelected ||
      (ce.propHooks.selected = {
        get: function (e) {
          var t = e.parentNode;
          return t && t.parentNode && t.parentNode.selectedIndex, null;
        },
        set: function (e) {
          var t = e.parentNode;
          t && (t.selectedIndex, t.parentNode && t.parentNode.selectedIndex);
        },
      }),
    ce.each(
      [
        "tabIndex",
        "readOnly",
        "maxLength",
        "cellSpacing",
        "cellPadding",
        "rowSpan",
        "colSpan",
        "useMap",
        "frameBorder",
        "contentEditable",
      ],
      function () {
        ce.propFix[this.toLowerCase()] = this;
      }
    ),
    ce.fn.extend({
      addClass: function (t) {
        var e, n, r, i, o, a;
        return v(t)
          ? this.each(function (e) {
              ce(this).addClass(t.call(this, e, Ct(this)));
            })
          : (e = kt(t)).length
          ? this.each(function () {
              if (
                ((r = Ct(this)), (n = 1 === this.nodeType && " " + Tt(r) + " "))
              ) {
                for (o = 0; o < e.length; o++)
                  (i = e[o]), n.indexOf(" " + i + " ") < 0 && (n += i + " ");
                (a = Tt(n)), r !== a && this.setAttribute("class", a);
              }
            })
          : this;
      },
      removeClass: function (t) {
        var e, n, r, i, o, a;
        return v(t)
          ? this.each(function (e) {
              ce(this).removeClass(t.call(this, e, Ct(this)));
            })
          : arguments.length
          ? (e = kt(t)).length
            ? this.each(function () {
                if (
                  ((r = Ct(this)),
                  (n = 1 === this.nodeType && " " + Tt(r) + " "))
                ) {
                  for (o = 0; o < e.length; o++) {
                    i = e[o];
                    while (-1 < n.indexOf(" " + i + " "))
                      n = n.replace(" " + i + " ", " ");
                  }
                  (a = Tt(n)), r !== a && this.setAttribute("class", a);
                }
              })
            : this
          : this.attr("class", "");
      },
      toggleClass: function (t, n) {
        var e,
          r,
          i,
          o,
          a = typeof t,
          s = "string" === a || Array.isArray(t);
        return v(t)
          ? this.each(function (e) {
              ce(this).toggleClass(t.call(this, e, Ct(this), n), n);
            })
          : "boolean" == typeof n && s
          ? n
            ? this.addClass(t)
            : this.removeClass(t)
          : ((e = kt(t)),
            this.each(function () {
              if (s)
                for (o = ce(this), i = 0; i < e.length; i++)
                  (r = e[i]), o.hasClass(r) ? o.removeClass(r) : o.addClass(r);
              else
                (void 0 !== t && "boolean" !== a) ||
                  ((r = Ct(this)) && _.set(this, "__className__", r),
                  this.setAttribute &&
                    this.setAttribute(
                      "class",
                      r || !1 === t ? "" : _.get(this, "__className__") || ""
                    ));
            }));
      },
      hasClass: function (e) {
        var t,
          n,
          r = 0;
        t = " " + e + " ";
        while ((n = this[r++]))
          if (1 === n.nodeType && -1 < (" " + Tt(Ct(n)) + " ").indexOf(t))
            return !0;
        return !1;
      },
    });
  var St = /\r/g;
  ce.fn.extend({
    val: function (n) {
      var r,
        e,
        i,
        t = this[0];
      return arguments.length
        ? ((i = v(n)),
          this.each(function (e) {
            var t;
            1 === this.nodeType &&
              (null == (t = i ? n.call(this, e, ce(this).val()) : n)
                ? (t = "")
                : "number" == typeof t
                ? (t += "")
                : Array.isArray(t) &&
                  (t = ce.map(t, function (e) {
                    return null == e ? "" : e + "";
                  })),
              ((r =
                ce.valHooks[this.type] ||
                ce.valHooks[this.nodeName.toLowerCase()]) &&
                "set" in r &&
                void 0 !== r.set(this, t, "value")) ||
                (this.value = t));
          }))
        : t
        ? (r = ce.valHooks[t.type] || ce.valHooks[t.nodeName.toLowerCase()]) &&
          "get" in r &&
          void 0 !== (e = r.get(t, "value"))
          ? e
          : "string" == typeof (e = t.value)
          ? e.replace(St, "")
          : null == e
          ? ""
          : e
        : void 0;
    },
  }),
    ce.extend({
      valHooks: {
        option: {
          get: function (e) {
            var t = ce.find.attr(e, "value");
            return null != t ? t : Tt(ce.text(e));
          },
        },
        select: {
          get: function (e) {
            var t,
              n,
              r,
              i = e.options,
              o = e.selectedIndex,
              a = "select-one" === e.type,
              s = a ? null : [],
              u = a ? o + 1 : i.length;
            for (r = o < 0 ? u : a ? o : 0; r < u; r++)
              if (
                ((n = i[r]).selected || r === o) &&
                !n.disabled &&
                (!n.parentNode.disabled || !fe(n.parentNode, "optgroup"))
              ) {
                if (((t = ce(n).val()), a)) return t;
                s.push(t);
              }
            return s;
          },
          set: function (e, t) {
            var n,
              r,
              i = e.options,
              o = ce.makeArray(t),
              a = i.length;
            while (a--)
              ((r = i[a]).selected =
                -1 < ce.inArray(ce.valHooks.option.get(r), o)) && (n = !0);
            return n || (e.selectedIndex = -1), o;
          },
        },
      },
    }),
    ce.each(["radio", "checkbox"], function () {
      (ce.valHooks[this] = {
        set: function (e, t) {
          if (Array.isArray(t))
            return (e.checked = -1 < ce.inArray(ce(e).val(), t));
        },
      }),
        le.checkOn ||
          (ce.valHooks[this].get = function (e) {
            return null === e.getAttribute("value") ? "on" : e.value;
          });
    });
  var Et = ie.location,
    jt = { guid: Date.now() },
    At = /\?/;
  ce.parseXML = function (e) {
    var t, n;
    if (!e || "string" != typeof e) return null;
    try {
      t = new ie.DOMParser().parseFromString(e, "text/xml");
    } catch (e) {}
    return (
      (n = t && t.getElementsByTagName("parsererror")[0]),
      (t && !n) ||
        ce.error(
          "Invalid XML: " +
            (n
              ? ce
                  .map(n.childNodes, function (e) {
                    return e.textContent;
                  })
                  .join("\n")
              : e)
        ),
      t
    );
  };
  var Dt = /^(?:focusinfocus|focusoutblur)$/,
    Nt = function (e) {
      e.stopPropagation();
    };
  ce.extend(ce.event, {
    trigger: function (e, t, n, r) {
      var i,
        o,
        a,
        s,
        u,
        l,
        c,
        f,
        p = [n || C],
        d = ue.call(e, "type") ? e.type : e,
        h = ue.call(e, "namespace") ? e.namespace.split(".") : [];
      if (
        ((o = f = a = n = n || C),
        3 !== n.nodeType &&
          8 !== n.nodeType &&
          !Dt.test(d + ce.event.triggered) &&
          (-1 < d.indexOf(".") && ((d = (h = d.split(".")).shift()), h.sort()),
          (u = d.indexOf(":") < 0 && "on" + d),
          ((e = e[ce.expando]
            ? e
            : new ce.Event(d, "object" == typeof e && e)).isTrigger = r
            ? 2
            : 3),
          (e.namespace = h.join(".")),
          (e.rnamespace = e.namespace
            ? new RegExp("(^|\\.)" + h.join("\\.(?:.*\\.|)") + "(\\.|$)")
            : null),
          (e.result = void 0),
          e.target || (e.target = n),
          (t = null == t ? [e] : ce.makeArray(t, [e])),
          (c = ce.event.special[d] || {}),
          r || !c.trigger || !1 !== c.trigger.apply(n, t)))
      ) {
        if (!r && !c.noBubble && !y(n)) {
          for (
            s = c.delegateType || d, Dt.test(s + d) || (o = o.parentNode);
            o;
            o = o.parentNode
          )
            p.push(o), (a = o);
          a === (n.ownerDocument || C) &&
            p.push(a.defaultView || a.parentWindow || ie);
        }
        i = 0;
        while ((o = p[i++]) && !e.isPropagationStopped())
          (f = o),
            (e.type = 1 < i ? s : c.bindType || d),
            (l =
              (_.get(o, "events") || Object.create(null))[e.type] &&
              _.get(o, "handle")) && l.apply(o, t),
            (l = u && o[u]) &&
              l.apply &&
              $(o) &&
              ((e.result = l.apply(o, t)),
              !1 === e.result && e.preventDefault());
        return (
          (e.type = d),
          r ||
            e.isDefaultPrevented() ||
            (c._default && !1 !== c._default.apply(p.pop(), t)) ||
            !$(n) ||
            (u &&
              v(n[d]) &&
              !y(n) &&
              ((a = n[u]) && (n[u] = null),
              (ce.event.triggered = d),
              e.isPropagationStopped() && f.addEventListener(d, Nt),
              n[d](),
              e.isPropagationStopped() && f.removeEventListener(d, Nt),
              (ce.event.triggered = void 0),
              a && (n[u] = a))),
          e.result
        );
      }
    },
    simulate: function (e, t, n) {
      var r = ce.extend(new ce.Event(), n, { type: e, isSimulated: !0 });
      ce.event.trigger(r, null, t);
    },
  }),
    ce.fn.extend({
      trigger: function (e, t) {
        return this.each(function () {
          ce.event.trigger(e, t, this);
        });
      },
      triggerHandler: function (e, t) {
        var n = this[0];
        if (n) return ce.event.trigger(e, t, n, !0);
      },
    });
  var qt = /\[\]$/,
    Lt = /\r?\n/g,
    Ht = /^(?:submit|button|image|reset|file)$/i,
    Ot = /^(?:input|select|textarea|keygen)/i;
  function Pt(n, e, r, i) {
    var t;
    if (Array.isArray(e))
      ce.each(e, function (e, t) {
        r || qt.test(n)
          ? i(n, t)
          : Pt(
              n + "[" + ("object" == typeof t && null != t ? e : "") + "]",
              t,
              r,
              i
            );
      });
    else if (r || "object" !== x(e)) i(n, e);
    else for (t in e) Pt(n + "[" + t + "]", e[t], r, i);
  }
  (ce.param = function (e, t) {
    var n,
      r = [],
      i = function (e, t) {
        var n = v(t) ? t() : t;
        r[r.length] =
          encodeURIComponent(e) + "=" + encodeURIComponent(null == n ? "" : n);
      };
    if (null == e) return "";
    if (Array.isArray(e) || (e.jquery && !ce.isPlainObject(e)))
      ce.each(e, function () {
        i(this.name, this.value);
      });
    else for (n in e) Pt(n, e[n], t, i);
    return r.join("&");
  }),
    ce.fn.extend({
      serialize: function () {
        return ce.param(this.serializeArray());
      },
      serializeArray: function () {
        return this.map(function () {
          var e = ce.prop(this, "elements");
          return e ? ce.makeArray(e) : this;
        })
          .filter(function () {
            var e = this.type;
            return (
              this.name &&
              !ce(this).is(":disabled") &&
              Ot.test(this.nodeName) &&
              !Ht.test(e) &&
              (this.checked || !we.test(e))
            );
          })
          .map(function (e, t) {
            var n = ce(this).val();
            return null == n
              ? null
              : Array.isArray(n)
              ? ce.map(n, function (e) {
                  return { name: t.name, value: e.replace(Lt, "\r\n") };
                })
              : { name: t.name, value: n.replace(Lt, "\r\n") };
          })
          .get();
      },
    });
  var Rt = /%20/g,
    Mt = /#.*$/,
    It = /([?&])_=[^&]*/,
    Wt = /^(.*?):[ \t]*([^\r\n]*)$/gm,
    Ft = /^(?:GET|HEAD)$/,
    $t = /^\/\//,
    Bt = {},
    _t = {},
    Xt = "*/".concat("*"),
    Ut = C.createElement("a");
  function zt(o) {
    return function (e, t) {
      "string" != typeof e && ((t = e), (e = "*"));
      var n,
        r = 0,
        i = e.toLowerCase().match(D) || [];
      if (v(t))
        while ((n = i[r++]))
          "+" === n[0]
            ? ((n = n.slice(1) || "*"), (o[n] = o[n] || []).unshift(t))
            : (o[n] = o[n] || []).push(t);
    };
  }
  function Vt(t, i, o, a) {
    var s = {},
      u = t === _t;
    function l(e) {
      var r;
      return (
        (s[e] = !0),
        ce.each(t[e] || [], function (e, t) {
          var n = t(i, o, a);
          return "string" != typeof n || u || s[n]
            ? u
              ? !(r = n)
              : void 0
            : (i.dataTypes.unshift(n), l(n), !1);
        }),
        r
      );
    }
    return l(i.dataTypes[0]) || (!s["*"] && l("*"));
  }
  function Gt(e, t) {
    var n,
      r,
      i = ce.ajaxSettings.flatOptions || {};
    for (n in t) void 0 !== t[n] && ((i[n] ? e : r || (r = {}))[n] = t[n]);
    return r && ce.extend(!0, e, r), e;
  }
  (Ut.href = Et.href),
    ce.extend({
      active: 0,
      lastModified: {},
      etag: {},
      ajaxSettings: {
        url: Et.href,
        type: "GET",
        isLocal:
          /^(?:about|app|app-storage|.+-extension|file|res|widget):$/.test(
            Et.protocol
          ),
        global: !0,
        processData: !0,
        async: !0,
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        accepts: {
          "*": Xt,
          text: "text/plain",
          html: "text/html",
          xml: "application/xml, text/xml",
          json: "application/json, text/javascript",
        },
        contents: { xml: /\bxml\b/, html: /\bhtml/, json: /\bjson\b/ },
        responseFields: {
          xml: "responseXML",
          text: "responseText",
          json: "responseJSON",
        },
        converters: {
          "* text": String,
          "text html": !0,
          "text json": JSON.parse,
          "text xml": ce.parseXML,
        },
        flatOptions: { url: !0, context: !0 },
      },
      ajaxSetup: function (e, t) {
        return t ? Gt(Gt(e, ce.ajaxSettings), t) : Gt(ce.ajaxSettings, e);
      },
      ajaxPrefilter: zt(Bt),
      ajaxTransport: zt(_t),
      ajax: function (e, t) {
        "object" == typeof e && ((t = e), (e = void 0)), (t = t || {});
        var c,
          f,
          p,
          n,
          d,
          r,
          h,
          g,
          i,
          o,
          v = ce.ajaxSetup({}, t),
          y = v.context || v,
          m = v.context && (y.nodeType || y.jquery) ? ce(y) : ce.event,
          x = ce.Deferred(),
          b = ce.Callbacks("once memory"),
          w = v.statusCode || {},
          a = {},
          s = {},
          u = "canceled",
          T = {
            readyState: 0,
            getResponseHeader: function (e) {
              var t;
              if (h) {
                if (!n) {
                  n = {};
                  while ((t = Wt.exec(p)))
                    n[t[1].toLowerCase() + " "] = (
                      n[t[1].toLowerCase() + " "] || []
                    ).concat(t[2]);
                }
                t = n[e.toLowerCase() + " "];
              }
              return null == t ? null : t.join(", ");
            },
            getAllResponseHeaders: function () {
              return h ? p : null;
            },
            setRequestHeader: function (e, t) {
              return (
                null == h &&
                  ((e = s[e.toLowerCase()] = s[e.toLowerCase()] || e),
                  (a[e] = t)),
                this
              );
            },
            overrideMimeType: function (e) {
              return null == h && (v.mimeType = e), this;
            },
            statusCode: function (e) {
              var t;
              if (e)
                if (h) T.always(e[T.status]);
                else for (t in e) w[t] = [w[t], e[t]];
              return this;
            },
            abort: function (e) {
              var t = e || u;
              return c && c.abort(t), l(0, t), this;
            },
          };
        if (
          (x.promise(T),
          (v.url = ((e || v.url || Et.href) + "").replace(
            $t,
            Et.protocol + "//"
          )),
          (v.type = t.method || t.type || v.method || v.type),
          (v.dataTypes = (v.dataType || "*").toLowerCase().match(D) || [""]),
          null == v.crossDomain)
        ) {
          r = C.createElement("a");
          try {
            (r.href = v.url),
              (r.href = r.href),
              (v.crossDomain =
                Ut.protocol + "//" + Ut.host != r.protocol + "//" + r.host);
          } catch (e) {
            v.crossDomain = !0;
          }
        }
        if (
          (v.data &&
            v.processData &&
            "string" != typeof v.data &&
            (v.data = ce.param(v.data, v.traditional)),
          Vt(Bt, v, t, T),
          h)
        )
          return T;
        for (i in ((g = ce.event && v.global) &&
          0 == ce.active++ &&
          ce.event.trigger("ajaxStart"),
        (v.type = v.type.toUpperCase()),
        (v.hasContent = !Ft.test(v.type)),
        (f = v.url.replace(Mt, "")),
        v.hasContent
          ? v.data &&
            v.processData &&
            0 ===
              (v.contentType || "").indexOf(
                "application/x-www-form-urlencoded"
              ) &&
            (v.data = v.data.replace(Rt, "+"))
          : ((o = v.url.slice(f.length)),
            v.data &&
              (v.processData || "string" == typeof v.data) &&
              ((f += (At.test(f) ? "&" : "?") + v.data), delete v.data),
            !1 === v.cache &&
              ((f = f.replace(It, "$1")),
              (o = (At.test(f) ? "&" : "?") + "_=" + jt.guid++ + o)),
            (v.url = f + o)),
        v.ifModified &&
          (ce.lastModified[f] &&
            T.setRequestHeader("If-Modified-Since", ce.lastModified[f]),
          ce.etag[f] && T.setRequestHeader("If-None-Match", ce.etag[f])),
        ((v.data && v.hasContent && !1 !== v.contentType) || t.contentType) &&
          T.setRequestHeader("Content-Type", v.contentType),
        T.setRequestHeader(
          "Accept",
          v.dataTypes[0] && v.accepts[v.dataTypes[0]]
            ? v.accepts[v.dataTypes[0]] +
                ("*" !== v.dataTypes[0] ? ", " + Xt + "; q=0.01" : "")
            : v.accepts["*"]
        ),
        v.headers))
          T.setRequestHeader(i, v.headers[i]);
        if (v.beforeSend && (!1 === v.beforeSend.call(y, T, v) || h))
          return T.abort();
        if (
          ((u = "abort"),
          b.add(v.complete),
          T.done(v.success),
          T.fail(v.error),
          (c = Vt(_t, v, t, T)))
        ) {
          if (((T.readyState = 1), g && m.trigger("ajaxSend", [T, v]), h))
            return T;
          v.async &&
            0 < v.timeout &&
            (d = ie.setTimeout(function () {
              T.abort("timeout");
            }, v.timeout));
          try {
            (h = !1), c.send(a, l);
          } catch (e) {
            if (h) throw e;
            l(-1, e);
          }
        } else l(-1, "No Transport");
        function l(e, t, n, r) {
          var i,
            o,
            a,
            s,
            u,
            l = t;
          h ||
            ((h = !0),
            d && ie.clearTimeout(d),
            (c = void 0),
            (p = r || ""),
            (T.readyState = 0 < e ? 4 : 0),
            (i = (200 <= e && e < 300) || 304 === e),
            n &&
              (s = (function (e, t, n) {
                var r,
                  i,
                  o,
                  a,
                  s = e.contents,
                  u = e.dataTypes;
                while ("*" === u[0])
                  u.shift(),
                    void 0 === r &&
                      (r = e.mimeType || t.getResponseHeader("Content-Type"));
                if (r)
                  for (i in s)
                    if (s[i] && s[i].test(r)) {
                      u.unshift(i);
                      break;
                    }
                if (u[0] in n) o = u[0];
                else {
                  for (i in n) {
                    if (!u[0] || e.converters[i + " " + u[0]]) {
                      o = i;
                      break;
                    }
                    a || (a = i);
                  }
                  o = o || a;
                }
                if (o) return o !== u[0] && u.unshift(o), n[o];
              })(v, T, n)),
            !i &&
              -1 < ce.inArray("script", v.dataTypes) &&
              ce.inArray("json", v.dataTypes) < 0 &&
              (v.converters["text script"] = function () {}),
            (s = (function (e, t, n, r) {
              var i,
                o,
                a,
                s,
                u,
                l = {},
                c = e.dataTypes.slice();
              if (c[1])
                for (a in e.converters) l[a.toLowerCase()] = e.converters[a];
              o = c.shift();
              while (o)
                if (
                  (e.responseFields[o] && (n[e.responseFields[o]] = t),
                  !u && r && e.dataFilter && (t = e.dataFilter(t, e.dataType)),
                  (u = o),
                  (o = c.shift()))
                )
                  if ("*" === o) o = u;
                  else if ("*" !== u && u !== o) {
                    if (!(a = l[u + " " + o] || l["* " + o]))
                      for (i in l)
                        if (
                          (s = i.split(" "))[1] === o &&
                          (a = l[u + " " + s[0]] || l["* " + s[0]])
                        ) {
                          !0 === a
                            ? (a = l[i])
                            : !0 !== l[i] && ((o = s[0]), c.unshift(s[1]));
                          break;
                        }
                    if (!0 !== a)
                      if (a && e["throws"]) t = a(t);
                      else
                        try {
                          t = a(t);
                        } catch (e) {
                          return {
                            state: "parsererror",
                            error: a
                              ? e
                              : "No conversion from " + u + " to " + o,
                          };
                        }
                  }
              return { state: "success", data: t };
            })(v, s, T, i)),
            i
              ? (v.ifModified &&
                  ((u = T.getResponseHeader("Last-Modified")) &&
                    (ce.lastModified[f] = u),
                  (u = T.getResponseHeader("etag")) && (ce.etag[f] = u)),
                204 === e || "HEAD" === v.type
                  ? (l = "nocontent")
                  : 304 === e
                  ? (l = "notmodified")
                  : ((l = s.state), (o = s.data), (i = !(a = s.error))))
              : ((a = l), (!e && l) || ((l = "error"), e < 0 && (e = 0))),
            (T.status = e),
            (T.statusText = (t || l) + ""),
            i ? x.resolveWith(y, [o, l, T]) : x.rejectWith(y, [T, l, a]),
            T.statusCode(w),
            (w = void 0),
            g && m.trigger(i ? "ajaxSuccess" : "ajaxError", [T, v, i ? o : a]),
            b.fireWith(y, [T, l]),
            g &&
              (m.trigger("ajaxComplete", [T, v]),
              --ce.active || ce.event.trigger("ajaxStop")));
        }
        return T;
      },
      getJSON: function (e, t, n) {
        return ce.get(e, t, n, "json");
      },
      getScript: function (e, t) {
        return ce.get(e, void 0, t, "script");
      },
    }),
    ce.each(["get", "post"], function (e, i) {
      ce[i] = function (e, t, n, r) {
        return (
          v(t) && ((r = r || n), (n = t), (t = void 0)),
          ce.ajax(
            ce.extend(
              { url: e, type: i, dataType: r, data: t, success: n },
              ce.isPlainObject(e) && e
            )
          )
        );
      };
    }),
    ce.ajaxPrefilter(function (e) {
      var t;
      for (t in e.headers)
        "content-type" === t.toLowerCase() &&
          (e.contentType = e.headers[t] || "");
    }),
    (ce._evalUrl = function (e, t, n) {
      return ce.ajax({
        url: e,
        type: "GET",
        dataType: "script",
        cache: !0,
        async: !1,
        global: !1,
        converters: { "text script": function () {} },
        dataFilter: function (e) {
          ce.globalEval(e, t, n);
        },
      });
    }),
    ce.fn.extend({
      wrapAll: function (e) {
        var t;
        return (
          this[0] &&
            (v(e) && (e = e.call(this[0])),
            (t = ce(e, this[0].ownerDocument).eq(0).clone(!0)),
            this[0].parentNode && t.insertBefore(this[0]),
            t
              .map(function () {
                var e = this;
                while (e.firstElementChild) e = e.firstElementChild;
                return e;
              })
              .append(this)),
          this
        );
      },
      wrapInner: function (n) {
        return v(n)
          ? this.each(function (e) {
              ce(this).wrapInner(n.call(this, e));
            })
          : this.each(function () {
              var e = ce(this),
                t = e.contents();
              t.length ? t.wrapAll(n) : e.append(n);
            });
      },
      wrap: function (t) {
        var n = v(t);
        return this.each(function (e) {
          ce(this).wrapAll(n ? t.call(this, e) : t);
        });
      },
      unwrap: function (e) {
        return (
          this.parent(e)
            .not("body")
            .each(function () {
              ce(this).replaceWith(this.childNodes);
            }),
          this
        );
      },
    }),
    (ce.expr.pseudos.hidden = function (e) {
      return !ce.expr.pseudos.visible(e);
    }),
    (ce.expr.pseudos.visible = function (e) {
      return !!(e.offsetWidth || e.offsetHeight || e.getClientRects().length);
    }),
    (ce.ajaxSettings.xhr = function () {
      try {
        return new ie.XMLHttpRequest();
      } catch (e) {}
    });
  var Yt = { 0: 200, 1223: 204 },
    Qt = ce.ajaxSettings.xhr();
  (le.cors = !!Qt && "withCredentials" in Qt),
    (le.ajax = Qt = !!Qt),
    ce.ajaxTransport(function (i) {
      var o, a;
      if (le.cors || (Qt && !i.crossDomain))
        return {
          send: function (e, t) {
            var n,
              r = i.xhr();
            if (
              (r.open(i.type, i.url, i.async, i.username, i.password),
              i.xhrFields)
            )
              for (n in i.xhrFields) r[n] = i.xhrFields[n];
            for (n in (i.mimeType &&
              r.overrideMimeType &&
              r.overrideMimeType(i.mimeType),
            i.crossDomain ||
              e["X-Requested-With"] ||
              (e["X-Requested-With"] = "XMLHttpRequest"),
            e))
              r.setRequestHeader(n, e[n]);
            (o = function (e) {
              return function () {
                o &&
                  ((o =
                    a =
                    r.onload =
                    r.onerror =
                    r.onabort =
                    r.ontimeout =
                    r.onreadystatechange =
                      null),
                  "abort" === e
                    ? r.abort()
                    : "error" === e
                    ? "number" != typeof r.status
                      ? t(0, "error")
                      : t(r.status, r.statusText)
                    : t(
                        Yt[r.status] || r.status,
                        r.statusText,
                        "text" !== (r.responseType || "text") ||
                          "string" != typeof r.responseText
                          ? { binary: r.response }
                          : { text: r.responseText },
                        r.getAllResponseHeaders()
                      ));
              };
            }),
              (r.onload = o()),
              (a = r.onerror = r.ontimeout = o("error")),
              void 0 !== r.onabort
                ? (r.onabort = a)
                : (r.onreadystatechange = function () {
                    4 === r.readyState &&
                      ie.setTimeout(function () {
                        o && a();
                      });
                  }),
              (o = o("abort"));
            try {
              r.send((i.hasContent && i.data) || null);
            } catch (e) {
              if (o) throw e;
            }
          },
          abort: function () {
            o && o();
          },
        };
    }),
    ce.ajaxPrefilter(function (e) {
      e.crossDomain && (e.contents.script = !1);
    }),
    ce.ajaxSetup({
      accepts: {
        script:
          "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript",
      },
      contents: { script: /\b(?:java|ecma)script\b/ },
      converters: {
        "text script": function (e) {
          return ce.globalEval(e), e;
        },
      },
    }),
    ce.ajaxPrefilter("script", function (e) {
      void 0 === e.cache && (e.cache = !1), e.crossDomain && (e.type = "GET");
    }),
    ce.ajaxTransport("script", function (n) {
      var r, i;
      if (n.crossDomain || n.scriptAttrs)
        return {
          send: function (e, t) {
            (r = ce("<script>")
              .attr(n.scriptAttrs || {})
              .prop({ charset: n.scriptCharset, src: n.url })
              .on(
                "load error",
                (i = function (e) {
                  r.remove(),
                    (i = null),
                    e && t("error" === e.type ? 404 : 200, e.type);
                })
              )),
              C.head.appendChild(r[0]);
          },
          abort: function () {
            i && i();
          },
        };
    });
  var Jt,
    Kt = [],
    Zt = /(=)\?(?=&|$)|\?\?/;
  ce.ajaxSetup({
    jsonp: "callback",
    jsonpCallback: function () {
      var e = Kt.pop() || ce.expando + "_" + jt.guid++;
      return (this[e] = !0), e;
    },
  }),
    ce.ajaxPrefilter("json jsonp", function (e, t, n) {
      var r,
        i,
        o,
        a =
          !1 !== e.jsonp &&
          (Zt.test(e.url)
            ? "url"
            : "string" == typeof e.data &&
              0 ===
                (e.contentType || "").indexOf(
                  "application/x-www-form-urlencoded"
                ) &&
              Zt.test(e.data) &&
              "data");
      if (a || "jsonp" === e.dataTypes[0])
        return (
          (r = e.jsonpCallback =
            v(e.jsonpCallback) ? e.jsonpCallback() : e.jsonpCallback),
          a
            ? (e[a] = e[a].replace(Zt, "$1" + r))
            : !1 !== e.jsonp &&
              (e.url += (At.test(e.url) ? "&" : "?") + e.jsonp + "=" + r),
          (e.converters["script json"] = function () {
            return o || ce.error(r + " was not called"), o[0];
          }),
          (e.dataTypes[0] = "json"),
          (i = ie[r]),
          (ie[r] = function () {
            o = arguments;
          }),
          n.always(function () {
            void 0 === i ? ce(ie).removeProp(r) : (ie[r] = i),
              e[r] && ((e.jsonpCallback = t.jsonpCallback), Kt.push(r)),
              o && v(i) && i(o[0]),
              (o = i = void 0);
          }),
          "script"
        );
    }),
    (le.createHTMLDocument =
      (((Jt = C.implementation.createHTMLDocument("").body).innerHTML =
        "<form></form><form></form>"),
      2 === Jt.childNodes.length)),
    (ce.parseHTML = function (e, t, n) {
      return "string" != typeof e
        ? []
        : ("boolean" == typeof t && ((n = t), (t = !1)),
          t ||
            (le.createHTMLDocument
              ? (((r = (t =
                  C.implementation.createHTMLDocument("")).createElement(
                  "base"
                )).href = C.location.href),
                t.head.appendChild(r))
              : (t = C)),
          (o = !n && []),
          (i = w.exec(e))
            ? [t.createElement(i[1])]
            : ((i = Ae([e], t, o)),
              o && o.length && ce(o).remove(),
              ce.merge([], i.childNodes)));
      var r, i, o;
    }),
    (ce.fn.load = function (e, t, n) {
      var r,
        i,
        o,
        a = this,
        s = e.indexOf(" ");
      return (
        -1 < s && ((r = Tt(e.slice(s))), (e = e.slice(0, s))),
        v(t)
          ? ((n = t), (t = void 0))
          : t && "object" == typeof t && (i = "POST"),
        0 < a.length &&
          ce
            .ajax({ url: e, type: i || "GET", dataType: "html", data: t })
            .done(function (e) {
              (o = arguments),
                a.html(r ? ce("<div>").append(ce.parseHTML(e)).find(r) : e);
            })
            .always(
              n &&
                function (e, t) {
                  a.each(function () {
                    n.apply(this, o || [e.responseText, t, e]);
                  });
                }
            ),
        this
      );
    }),
    (ce.expr.pseudos.animated = function (t) {
      return ce.grep(ce.timers, function (e) {
        return t === e.elem;
      }).length;
    }),
    (ce.offset = {
      setOffset: function (e, t, n) {
        var r,
          i,
          o,
          a,
          s,
          u,
          l = ce.css(e, "position"),
          c = ce(e),
          f = {};
        "static" === l && (e.style.position = "relative"),
          (s = c.offset()),
          (o = ce.css(e, "top")),
          (u = ce.css(e, "left")),
          ("absolute" === l || "fixed" === l) && -1 < (o + u).indexOf("auto")
            ? ((a = (r = c.position()).top), (i = r.left))
            : ((a = parseFloat(o) || 0), (i = parseFloat(u) || 0)),
          v(t) && (t = t.call(e, n, ce.extend({}, s))),
          null != t.top && (f.top = t.top - s.top + a),
          null != t.left && (f.left = t.left - s.left + i),
          "using" in t ? t.using.call(e, f) : c.css(f);
      },
    }),
    ce.fn.extend({
      offset: function (t) {
        if (arguments.length)
          return void 0 === t
            ? this
            : this.each(function (e) {
                ce.offset.setOffset(this, t, e);
              });
        var e,
          n,
          r = this[0];
        return r
          ? r.getClientRects().length
            ? ((e = r.getBoundingClientRect()),
              (n = r.ownerDocument.defaultView),
              { top: e.top + n.pageYOffset, left: e.left + n.pageXOffset })
            : { top: 0, left: 0 }
          : void 0;
      },
      position: function () {
        if (this[0]) {
          var e,
            t,
            n,
            r = this[0],
            i = { top: 0, left: 0 };
          if ("fixed" === ce.css(r, "position")) t = r.getBoundingClientRect();
          else {
            (t = this.offset()),
              (n = r.ownerDocument),
              (e = r.offsetParent || n.documentElement);
            while (
              e &&
              (e === n.body || e === n.documentElement) &&
              "static" === ce.css(e, "position")
            )
              e = e.parentNode;
            e &&
              e !== r &&
              1 === e.nodeType &&
              (((i = ce(e).offset()).top += ce.css(e, "borderTopWidth", !0)),
              (i.left += ce.css(e, "borderLeftWidth", !0)));
          }
          return {
            top: t.top - i.top - ce.css(r, "marginTop", !0),
            left: t.left - i.left - ce.css(r, "marginLeft", !0),
          };
        }
      },
      offsetParent: function () {
        return this.map(function () {
          var e = this.offsetParent;
          while (e && "static" === ce.css(e, "position")) e = e.offsetParent;
          return e || J;
        });
      },
    }),
    ce.each(
      { scrollLeft: "pageXOffset", scrollTop: "pageYOffset" },
      function (t, i) {
        var o = "pageYOffset" === i;
        ce.fn[t] = function (e) {
          return R(
            this,
            function (e, t, n) {
              var r;
              if (
                (y(e) ? (r = e) : 9 === e.nodeType && (r = e.defaultView),
                void 0 === n)
              )
                return r ? r[i] : e[t];
              r
                ? r.scrollTo(o ? r.pageXOffset : n, o ? n : r.pageYOffset)
                : (e[t] = n);
            },
            t,
            e,
            arguments.length
          );
        };
      }
    ),
    ce.each(["top", "left"], function (e, n) {
      ce.cssHooks[n] = Ye(le.pixelPosition, function (e, t) {
        if (t)
          return (t = Ge(e, n)), _e.test(t) ? ce(e).position()[n] + "px" : t;
      });
    }),
    ce.each({ Height: "height", Width: "width" }, function (a, s) {
      ce.each(
        { padding: "inner" + a, content: s, "": "outer" + a },
        function (r, o) {
          ce.fn[o] = function (e, t) {
            var n = arguments.length && (r || "boolean" != typeof e),
              i = r || (!0 === e || !0 === t ? "margin" : "border");
            return R(
              this,
              function (e, t, n) {
                var r;
                return y(e)
                  ? 0 === o.indexOf("outer")
                    ? e["inner" + a]
                    : e.document.documentElement["client" + a]
                  : 9 === e.nodeType
                  ? ((r = e.documentElement),
                    Math.max(
                      e.body["scroll" + a],
                      r["scroll" + a],
                      e.body["offset" + a],
                      r["offset" + a],
                      r["client" + a]
                    ))
                  : void 0 === n
                  ? ce.css(e, t, i)
                  : ce.style(e, t, n, i);
              },
              s,
              n ? e : void 0,
              n
            );
          };
        }
      );
    }),
    ce.each(
      [
        "ajaxStart",
        "ajaxStop",
        "ajaxComplete",
        "ajaxError",
        "ajaxSuccess",
        "ajaxSend",
      ],
      function (e, t) {
        ce.fn[t] = function (e) {
          return this.on(t, e);
        };
      }
    ),
    ce.fn.extend({
      bind: function (e, t, n) {
        return this.on(e, null, t, n);
      },
      unbind: function (e, t) {
        return this.off(e, null, t);
      },
      delegate: function (e, t, n, r) {
        return this.on(t, e, n, r);
      },
      undelegate: function (e, t, n) {
        return 1 === arguments.length
          ? this.off(e, "**")
          : this.off(t, e || "**", n);
      },
      hover: function (e, t) {
        return this.mouseenter(e).mouseleave(t || e);
      },
    }),
    ce.each(
      "blur focus focusin focusout resize scroll click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup contextmenu".split(
        " "
      ),
      function (e, n) {
        ce.fn[n] = function (e, t) {
          return 0 < arguments.length
            ? this.on(n, null, e, t)
            : this.trigger(n);
        };
      }
    );
  var en = /^[\s\uFEFF\xA0]+|([^\s\uFEFF\xA0])[\s\uFEFF\xA0]+$/g;
  (ce.proxy = function (e, t) {
    var n, r, i;
    if (("string" == typeof t && ((n = e[t]), (t = e), (e = n)), v(e)))
      return (
        (r = ae.call(arguments, 2)),
        ((i = function () {
          return e.apply(t || this, r.concat(ae.call(arguments)));
        }).guid = e.guid =
          e.guid || ce.guid++),
        i
      );
  }),
    (ce.holdReady = function (e) {
      e ? ce.readyWait++ : ce.ready(!0);
    }),
    (ce.isArray = Array.isArray),
    (ce.parseJSON = JSON.parse),
    (ce.nodeName = fe),
    (ce.isFunction = v),
    (ce.isWindow = y),
    (ce.camelCase = F),
    (ce.type = x),
    (ce.now = Date.now),
    (ce.isNumeric = function (e) {
      var t = ce.type(e);
      return ("number" === t || "string" === t) && !isNaN(e - parseFloat(e));
    }),
    (ce.trim = function (e) {
      return null == e ? "" : (e + "").replace(en, "$1");
    }),
    "function" == typeof define &&
      define.amd &&
      define("jquery", [], function () {
        return ce;
      });
  var tn = ie.jQuery,
    nn = ie.$;
  return (
    (ce.noConflict = function (e) {
      return (
        ie.$ === ce && (ie.$ = nn),
        e && ie.jQuery === ce && (ie.jQuery = tn),
        ce
      );
    }),
    "undefined" == typeof e && (ie.jQuery = ie.$ = ce),
    ce
  );
});
/* @license MIT https://raw.githubusercontent.com/jashkenas/underscore/1.13.6/LICENSE */
!(function (n, r) {
  "object" == typeof exports && "undefined" != typeof module
    ? (module.exports = r())
    : "function" == typeof define && define.amd
    ? define("underscore", r)
    : ((n = "undefined" != typeof globalThis ? globalThis : n || self),
      (function () {
        var t = n._,
          e = (n._ = r());
        e.noConflict = function () {
          return (n._ = t), e;
        };
      })());
})(this, function () {
  //     Underscore.js 1.13.6
  //     https://underscorejs.org
  //     (c) 2009-2022 Jeremy Ashkenas, Julian Gonggrijp, and DocumentCloud and Investigative Reporters & Editors
  //     Underscore may be freely distributed under the MIT license.
  var n = "1.13.6",
    r =
      ("object" == typeof self && self.self === self && self) ||
      ("object" == typeof global && global.global === global && global) ||
      Function("return this")() ||
      {},
    t = Array.prototype,
    e = Object.prototype,
    u = "undefined" != typeof Symbol ? Symbol.prototype : null,
    o = t.push,
    i = t.slice,
    a = e.toString,
    f = e.hasOwnProperty,
    c = "undefined" != typeof ArrayBuffer,
    l = "undefined" != typeof DataView,
    s = Array.isArray,
    p = Object.keys,
    v = Object.create,
    h = c && ArrayBuffer.isView,
    y = isNaN,
    d = isFinite,
    g = !{ toString: null }.propertyIsEnumerable("toString"),
    b = [
      "valueOf",
      "isPrototypeOf",
      "toString",
      "propertyIsEnumerable",
      "hasOwnProperty",
      "toLocaleString",
    ],
    m = Math.pow(2, 53) - 1;
  function j(n, r) {
    return (
      (r = null == r ? n.length - 1 : +r),
      function () {
        for (
          var t = Math.max(arguments.length - r, 0), e = Array(t), u = 0;
          u < t;
          u++
        )
          e[u] = arguments[u + r];
        switch (r) {
          case 0:
            return n.call(this, e);
          case 1:
            return n.call(this, arguments[0], e);
          case 2:
            return n.call(this, arguments[0], arguments[1], e);
        }
        var o = Array(r + 1);
        for (u = 0; u < r; u++) o[u] = arguments[u];
        return (o[r] = e), n.apply(this, o);
      }
    );
  }
  function _(n) {
    var r = typeof n;
    return "function" === r || ("object" === r && !!n);
  }
  function w(n) {
    return void 0 === n;
  }
  function A(n) {
    return !0 === n || !1 === n || "[object Boolean]" === a.call(n);
  }
  function x(n) {
    var r = "[object " + n + "]";
    return function (n) {
      return a.call(n) === r;
    };
  }
  var S = x("String"),
    O = x("Number"),
    M = x("Date"),
    E = x("RegExp"),
    B = x("Error"),
    N = x("Symbol"),
    I = x("ArrayBuffer"),
    T = x("Function"),
    k = r.document && r.document.childNodes;
  "function" != typeof /./ &&
    "object" != typeof Int8Array &&
    "function" != typeof k &&
    (T = function (n) {
      return "function" == typeof n || !1;
    });
  var D = T,
    R = x("Object"),
    F = l && R(new DataView(new ArrayBuffer(8))),
    V = "undefined" != typeof Map && R(new Map()),
    P = x("DataView");
  var q = F
      ? function (n) {
          return null != n && D(n.getInt8) && I(n.buffer);
        }
      : P,
    U = s || x("Array");
  function W(n, r) {
    return null != n && f.call(n, r);
  }
  var z = x("Arguments");
  !(function () {
    z(arguments) ||
      (z = function (n) {
        return W(n, "callee");
      });
  })();
  var L = z;
  function $(n) {
    return O(n) && y(n);
  }
  function C(n) {
    return function () {
      return n;
    };
  }
  function K(n) {
    return function (r) {
      var t = n(r);
      return "number" == typeof t && t >= 0 && t <= m;
    };
  }
  function J(n) {
    return function (r) {
      return null == r ? void 0 : r[n];
    };
  }
  var G = J("byteLength"),
    H = K(G),
    Q =
      /\[object ((I|Ui)nt(8|16|32)|Float(32|64)|Uint8Clamped|Big(I|Ui)nt64)Array\]/;
  var X = c
      ? function (n) {
          return h ? h(n) && !q(n) : H(n) && Q.test(a.call(n));
        }
      : C(!1),
    Y = J("length");
  function Z(n, r) {
    r = (function (n) {
      for (var r = {}, t = n.length, e = 0; e < t; ++e) r[n[e]] = !0;
      return {
        contains: function (n) {
          return !0 === r[n];
        },
        push: function (t) {
          return (r[t] = !0), n.push(t);
        },
      };
    })(r);
    var t = b.length,
      u = n.constructor,
      o = (D(u) && u.prototype) || e,
      i = "constructor";
    for (W(n, i) && !r.contains(i) && r.push(i); t--; )
      (i = b[t]) in n && n[i] !== o[i] && !r.contains(i) && r.push(i);
  }
  function nn(n) {
    if (!_(n)) return [];
    if (p) return p(n);
    var r = [];
    for (var t in n) W(n, t) && r.push(t);
    return g && Z(n, r), r;
  }
  function rn(n, r) {
    var t = nn(r),
      e = t.length;
    if (null == n) return !e;
    for (var u = Object(n), o = 0; o < e; o++) {
      var i = t[o];
      if (r[i] !== u[i] || !(i in u)) return !1;
    }
    return !0;
  }
  function tn(n) {
    return n instanceof tn
      ? n
      : this instanceof tn
      ? void (this._wrapped = n)
      : new tn(n);
  }
  function en(n) {
    return new Uint8Array(n.buffer || n, n.byteOffset || 0, G(n));
  }
  (tn.VERSION = n),
    (tn.prototype.value = function () {
      return this._wrapped;
    }),
    (tn.prototype.valueOf = tn.prototype.toJSON = tn.prototype.value),
    (tn.prototype.toString = function () {
      return String(this._wrapped);
    });
  var un = "[object DataView]";
  function on(n, r, t, e) {
    if (n === r) return 0 !== n || 1 / n == 1 / r;
    if (null == n || null == r) return !1;
    if (n != n) return r != r;
    var o = typeof n;
    return (
      ("function" === o || "object" === o || "object" == typeof r) &&
      (function n(r, t, e, o) {
        r instanceof tn && (r = r._wrapped);
        t instanceof tn && (t = t._wrapped);
        var i = a.call(r);
        if (i !== a.call(t)) return !1;
        if (F && "[object Object]" == i && q(r)) {
          if (!q(t)) return !1;
          i = un;
        }
        switch (i) {
          case "[object RegExp]":
          case "[object String]":
            return "" + r == "" + t;
          case "[object Number]":
            return +r != +r ? +t != +t : 0 == +r ? 1 / +r == 1 / t : +r == +t;
          case "[object Date]":
          case "[object Boolean]":
            return +r == +t;
          case "[object Symbol]":
            return u.valueOf.call(r) === u.valueOf.call(t);
          case "[object ArrayBuffer]":
          case un:
            return n(en(r), en(t), e, o);
        }
        var f = "[object Array]" === i;
        if (!f && X(r)) {
          if (G(r) !== G(t)) return !1;
          if (r.buffer === t.buffer && r.byteOffset === t.byteOffset) return !0;
          f = !0;
        }
        if (!f) {
          if ("object" != typeof r || "object" != typeof t) return !1;
          var c = r.constructor,
            l = t.constructor;
          if (
            c !== l &&
            !(D(c) && c instanceof c && D(l) && l instanceof l) &&
            "constructor" in r &&
            "constructor" in t
          )
            return !1;
        }
        o = o || [];
        var s = (e = e || []).length;
        for (; s--; ) if (e[s] === r) return o[s] === t;
        if ((e.push(r), o.push(t), f)) {
          if ((s = r.length) !== t.length) return !1;
          for (; s--; ) if (!on(r[s], t[s], e, o)) return !1;
        } else {
          var p,
            v = nn(r);
          if (((s = v.length), nn(t).length !== s)) return !1;
          for (; s--; )
            if (((p = v[s]), !W(t, p) || !on(r[p], t[p], e, o))) return !1;
        }
        return e.pop(), o.pop(), !0;
      })(n, r, t, e)
    );
  }
  function an(n) {
    if (!_(n)) return [];
    var r = [];
    for (var t in n) r.push(t);
    return g && Z(n, r), r;
  }
  function fn(n) {
    var r = Y(n);
    return function (t) {
      if (null == t) return !1;
      var e = an(t);
      if (Y(e)) return !1;
      for (var u = 0; u < r; u++) if (!D(t[n[u]])) return !1;
      return n !== hn || !D(t[cn]);
    };
  }
  var cn = "forEach",
    ln = "has",
    sn = ["clear", "delete"],
    pn = ["get", ln, "set"],
    vn = sn.concat(cn, pn),
    hn = sn.concat(pn),
    yn = ["add"].concat(sn, cn, ln),
    dn = V ? fn(vn) : x("Map"),
    gn = V ? fn(hn) : x("WeakMap"),
    bn = V ? fn(yn) : x("Set"),
    mn = x("WeakSet");
  function jn(n) {
    for (var r = nn(n), t = r.length, e = Array(t), u = 0; u < t; u++)
      e[u] = n[r[u]];
    return e;
  }
  function _n(n) {
    for (var r = {}, t = nn(n), e = 0, u = t.length; e < u; e++)
      r[n[t[e]]] = t[e];
    return r;
  }
  function wn(n) {
    var r = [];
    for (var t in n) D(n[t]) && r.push(t);
    return r.sort();
  }
  function An(n, r) {
    return function (t) {
      var e = arguments.length;
      if ((r && (t = Object(t)), e < 2 || null == t)) return t;
      for (var u = 1; u < e; u++)
        for (var o = arguments[u], i = n(o), a = i.length, f = 0; f < a; f++) {
          var c = i[f];
          (r && void 0 !== t[c]) || (t[c] = o[c]);
        }
      return t;
    };
  }
  var xn = An(an),
    Sn = An(nn),
    On = An(an, !0);
  function Mn(n) {
    if (!_(n)) return {};
    if (v) return v(n);
    var r = function () {};
    r.prototype = n;
    var t = new r();
    return (r.prototype = null), t;
  }
  function En(n) {
    return U(n) ? n : [n];
  }
  function Bn(n) {
    return tn.toPath(n);
  }
  function Nn(n, r) {
    for (var t = r.length, e = 0; e < t; e++) {
      if (null == n) return;
      n = n[r[e]];
    }
    return t ? n : void 0;
  }
  function In(n, r, t) {
    var e = Nn(n, Bn(r));
    return w(e) ? t : e;
  }
  function Tn(n) {
    return n;
  }
  function kn(n) {
    return (
      (n = Sn({}, n)),
      function (r) {
        return rn(r, n);
      }
    );
  }
  function Dn(n) {
    return (
      (n = Bn(n)),
      function (r) {
        return Nn(r, n);
      }
    );
  }
  function Rn(n, r, t) {
    if (void 0 === r) return n;
    switch (null == t ? 3 : t) {
      case 1:
        return function (t) {
          return n.call(r, t);
        };
      case 3:
        return function (t, e, u) {
          return n.call(r, t, e, u);
        };
      case 4:
        return function (t, e, u, o) {
          return n.call(r, t, e, u, o);
        };
    }
    return function () {
      return n.apply(r, arguments);
    };
  }
  function Fn(n, r, t) {
    return null == n ? Tn : D(n) ? Rn(n, r, t) : _(n) && !U(n) ? kn(n) : Dn(n);
  }
  function Vn(n, r) {
    return Fn(n, r, 1 / 0);
  }
  function Pn(n, r, t) {
    return tn.iteratee !== Vn ? tn.iteratee(n, r) : Fn(n, r, t);
  }
  function qn() {}
  function Un(n, r) {
    return (
      null == r && ((r = n), (n = 0)),
      n + Math.floor(Math.random() * (r - n + 1))
    );
  }
  (tn.toPath = En), (tn.iteratee = Vn);
  var Wn =
    Date.now ||
    function () {
      return new Date().getTime();
    };
  function zn(n) {
    var r = function (r) {
        return n[r];
      },
      t = "(?:" + nn(n).join("|") + ")",
      e = RegExp(t),
      u = RegExp(t, "g");
    return function (n) {
      return (n = null == n ? "" : "" + n), e.test(n) ? n.replace(u, r) : n;
    };
  }
  var Ln = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#x27;",
      "`": "&#x60;",
    },
    $n = zn(Ln),
    Cn = zn(_n(Ln)),
    Kn = (tn.templateSettings = {
      evaluate: /<%([\s\S]+?)%>/g,
      interpolate: /<%=([\s\S]+?)%>/g,
      escape: /<%-([\s\S]+?)%>/g,
    }),
    Jn = /(.)^/,
    Gn = {
      "'": "'",
      "\\": "\\",
      "\r": "r",
      "\n": "n",
      "\u2028": "u2028",
      "\u2029": "u2029",
    },
    Hn = /\\|'|\r|\n|\u2028|\u2029/g;
  function Qn(n) {
    return "\\" + Gn[n];
  }
  var Xn = /^\s*(\w|\$)+\s*$/;
  var Yn = 0;
  function Zn(n, r, t, e, u) {
    if (!(e instanceof r)) return n.apply(t, u);
    var o = Mn(n.prototype),
      i = n.apply(o, u);
    return _(i) ? i : o;
  }
  var nr = j(function (n, r) {
    var t = nr.placeholder,
      e = function () {
        for (var u = 0, o = r.length, i = Array(o), a = 0; a < o; a++)
          i[a] = r[a] === t ? arguments[u++] : r[a];
        for (; u < arguments.length; ) i.push(arguments[u++]);
        return Zn(n, e, this, this, i);
      };
    return e;
  });
  nr.placeholder = tn;
  var rr = j(function (n, r, t) {
      if (!D(n)) throw new TypeError("Bind must be called on a function");
      var e = j(function (u) {
        return Zn(n, e, r, this, t.concat(u));
      });
      return e;
    }),
    tr = K(Y);
  function er(n, r, t, e) {
    if (((e = e || []), r || 0 === r)) {
      if (r <= 0) return e.concat(n);
    } else r = 1 / 0;
    for (var u = e.length, o = 0, i = Y(n); o < i; o++) {
      var a = n[o];
      if (tr(a) && (U(a) || L(a)))
        if (r > 1) er(a, r - 1, t, e), (u = e.length);
        else for (var f = 0, c = a.length; f < c; ) e[u++] = a[f++];
      else t || (e[u++] = a);
    }
    return e;
  }
  var ur = j(function (n, r) {
    var t = (r = er(r, !1, !1)).length;
    if (t < 1) throw new Error("bindAll must be passed function names");
    for (; t--; ) {
      var e = r[t];
      n[e] = rr(n[e], n);
    }
    return n;
  });
  var or = j(function (n, r, t) {
      return setTimeout(function () {
        return n.apply(null, t);
      }, r);
    }),
    ir = nr(or, tn, 1);
  function ar(n) {
    return function () {
      return !n.apply(this, arguments);
    };
  }
  function fr(n, r) {
    var t;
    return function () {
      return --n > 0 && (t = r.apply(this, arguments)), n <= 1 && (r = null), t;
    };
  }
  var cr = nr(fr, 2);
  function lr(n, r, t) {
    r = Pn(r, t);
    for (var e, u = nn(n), o = 0, i = u.length; o < i; o++)
      if (r(n[(e = u[o])], e, n)) return e;
  }
  function sr(n) {
    return function (r, t, e) {
      t = Pn(t, e);
      for (var u = Y(r), o = n > 0 ? 0 : u - 1; o >= 0 && o < u; o += n)
        if (t(r[o], o, r)) return o;
      return -1;
    };
  }
  var pr = sr(1),
    vr = sr(-1);
  function hr(n, r, t, e) {
    for (var u = (t = Pn(t, e, 1))(r), o = 0, i = Y(n); o < i; ) {
      var a = Math.floor((o + i) / 2);
      t(n[a]) < u ? (o = a + 1) : (i = a);
    }
    return o;
  }
  function yr(n, r, t) {
    return function (e, u, o) {
      var a = 0,
        f = Y(e);
      if ("number" == typeof o)
        n > 0
          ? (a = o >= 0 ? o : Math.max(o + f, a))
          : (f = o >= 0 ? Math.min(o + 1, f) : o + f + 1);
      else if (t && o && f) return e[(o = t(e, u))] === u ? o : -1;
      if (u != u) return (o = r(i.call(e, a, f), $)) >= 0 ? o + a : -1;
      for (o = n > 0 ? a : f - 1; o >= 0 && o < f; o += n)
        if (e[o] === u) return o;
      return -1;
    };
  }
  var dr = yr(1, pr, hr),
    gr = yr(-1, vr);
  function br(n, r, t) {
    var e = (tr(n) ? pr : lr)(n, r, t);
    if (void 0 !== e && -1 !== e) return n[e];
  }
  function mr(n, r, t) {
    var e, u;
    if (((r = Rn(r, t)), tr(n)))
      for (e = 0, u = n.length; e < u; e++) r(n[e], e, n);
    else {
      var o = nn(n);
      for (e = 0, u = o.length; e < u; e++) r(n[o[e]], o[e], n);
    }
    return n;
  }
  function jr(n, r, t) {
    r = Pn(r, t);
    for (
      var e = !tr(n) && nn(n), u = (e || n).length, o = Array(u), i = 0;
      i < u;
      i++
    ) {
      var a = e ? e[i] : i;
      o[i] = r(n[a], a, n);
    }
    return o;
  }
  function _r(n) {
    var r = function (r, t, e, u) {
      var o = !tr(r) && nn(r),
        i = (o || r).length,
        a = n > 0 ? 0 : i - 1;
      for (u || ((e = r[o ? o[a] : a]), (a += n)); a >= 0 && a < i; a += n) {
        var f = o ? o[a] : a;
        e = t(e, r[f], f, r);
      }
      return e;
    };
    return function (n, t, e, u) {
      var o = arguments.length >= 3;
      return r(n, Rn(t, u, 4), e, o);
    };
  }
  var wr = _r(1),
    Ar = _r(-1);
  function xr(n, r, t) {
    var e = [];
    return (
      (r = Pn(r, t)),
      mr(n, function (n, t, u) {
        r(n, t, u) && e.push(n);
      }),
      e
    );
  }
  function Sr(n, r, t) {
    r = Pn(r, t);
    for (var e = !tr(n) && nn(n), u = (e || n).length, o = 0; o < u; o++) {
      var i = e ? e[o] : o;
      if (!r(n[i], i, n)) return !1;
    }
    return !0;
  }
  function Or(n, r, t) {
    r = Pn(r, t);
    for (var e = !tr(n) && nn(n), u = (e || n).length, o = 0; o < u; o++) {
      var i = e ? e[o] : o;
      if (r(n[i], i, n)) return !0;
    }
    return !1;
  }
  function Mr(n, r, t, e) {
    return (
      tr(n) || (n = jn(n)),
      ("number" != typeof t || e) && (t = 0),
      dr(n, r, t) >= 0
    );
  }
  var Er = j(function (n, r, t) {
    var e, u;
    return (
      D(r)
        ? (u = r)
        : ((r = Bn(r)), (e = r.slice(0, -1)), (r = r[r.length - 1])),
      jr(n, function (n) {
        var o = u;
        if (!o) {
          if ((e && e.length && (n = Nn(n, e)), null == n)) return;
          o = n[r];
        }
        return null == o ? o : o.apply(n, t);
      })
    );
  });
  function Br(n, r) {
    return jr(n, Dn(r));
  }
  function Nr(n, r, t) {
    var e,
      u,
      o = -1 / 0,
      i = -1 / 0;
    if (
      null == r ||
      ("number" == typeof r && "object" != typeof n[0] && null != n)
    )
      for (var a = 0, f = (n = tr(n) ? n : jn(n)).length; a < f; a++)
        null != (e = n[a]) && e > o && (o = e);
    else
      (r = Pn(r, t)),
        mr(n, function (n, t, e) {
          ((u = r(n, t, e)) > i || (u === -1 / 0 && o === -1 / 0)) &&
            ((o = n), (i = u));
        });
    return o;
  }
  var Ir = /[^\ud800-\udfff]|[\ud800-\udbff][\udc00-\udfff]|[\ud800-\udfff]/g;
  function Tr(n) {
    return n
      ? U(n)
        ? i.call(n)
        : S(n)
        ? n.match(Ir)
        : tr(n)
        ? jr(n, Tn)
        : jn(n)
      : [];
  }
  function kr(n, r, t) {
    if (null == r || t) return tr(n) || (n = jn(n)), n[Un(n.length - 1)];
    var e = Tr(n),
      u = Y(e);
    r = Math.max(Math.min(r, u), 0);
    for (var o = u - 1, i = 0; i < r; i++) {
      var a = Un(i, o),
        f = e[i];
      (e[i] = e[a]), (e[a] = f);
    }
    return e.slice(0, r);
  }
  function Dr(n, r) {
    return function (t, e, u) {
      var o = r ? [[], []] : {};
      return (
        (e = Pn(e, u)),
        mr(t, function (r, u) {
          var i = e(r, u, t);
          n(o, r, i);
        }),
        o
      );
    };
  }
  var Rr = Dr(function (n, r, t) {
      W(n, t) ? n[t].push(r) : (n[t] = [r]);
    }),
    Fr = Dr(function (n, r, t) {
      n[t] = r;
    }),
    Vr = Dr(function (n, r, t) {
      W(n, t) ? n[t]++ : (n[t] = 1);
    }),
    Pr = Dr(function (n, r, t) {
      n[t ? 0 : 1].push(r);
    }, !0);
  function qr(n, r, t) {
    return r in t;
  }
  var Ur = j(function (n, r) {
      var t = {},
        e = r[0];
      if (null == n) return t;
      D(e)
        ? (r.length > 1 && (e = Rn(e, r[1])), (r = an(n)))
        : ((e = qr), (r = er(r, !1, !1)), (n = Object(n)));
      for (var u = 0, o = r.length; u < o; u++) {
        var i = r[u],
          a = n[i];
        e(a, i, n) && (t[i] = a);
      }
      return t;
    }),
    Wr = j(function (n, r) {
      var t,
        e = r[0];
      return (
        D(e)
          ? ((e = ar(e)), r.length > 1 && (t = r[1]))
          : ((r = jr(er(r, !1, !1), String)),
            (e = function (n, t) {
              return !Mr(r, t);
            })),
        Ur(n, e, t)
      );
    });
  function zr(n, r, t) {
    return i.call(n, 0, Math.max(0, n.length - (null == r || t ? 1 : r)));
  }
  function Lr(n, r, t) {
    return null == n || n.length < 1
      ? null == r || t
        ? void 0
        : []
      : null == r || t
      ? n[0]
      : zr(n, n.length - r);
  }
  function $r(n, r, t) {
    return i.call(n, null == r || t ? 1 : r);
  }
  var Cr = j(function (n, r) {
      return (
        (r = er(r, !0, !0)),
        xr(n, function (n) {
          return !Mr(r, n);
        })
      );
    }),
    Kr = j(function (n, r) {
      return Cr(n, r);
    });
  function Jr(n, r, t, e) {
    A(r) || ((e = t), (t = r), (r = !1)), null != t && (t = Pn(t, e));
    for (var u = [], o = [], i = 0, a = Y(n); i < a; i++) {
      var f = n[i],
        c = t ? t(f, i, n) : f;
      r && !t
        ? ((i && o === c) || u.push(f), (o = c))
        : t
        ? Mr(o, c) || (o.push(c), u.push(f))
        : Mr(u, f) || u.push(f);
    }
    return u;
  }
  var Gr = j(function (n) {
    return Jr(er(n, !0, !0));
  });
  function Hr(n) {
    for (var r = (n && Nr(n, Y).length) || 0, t = Array(r), e = 0; e < r; e++)
      t[e] = Br(n, e);
    return t;
  }
  var Qr = j(Hr);
  function Xr(n, r) {
    return n._chain ? tn(r).chain() : r;
  }
  function Yr(n) {
    return (
      mr(wn(n), function (r) {
        var t = (tn[r] = n[r]);
        tn.prototype[r] = function () {
          var n = [this._wrapped];
          return o.apply(n, arguments), Xr(this, t.apply(tn, n));
        };
      }),
      tn
    );
  }
  mr(
    ["pop", "push", "reverse", "shift", "sort", "splice", "unshift"],
    function (n) {
      var r = t[n];
      tn.prototype[n] = function () {
        var t = this._wrapped;
        return (
          null != t &&
            (r.apply(t, arguments),
            ("shift" !== n && "splice" !== n) || 0 !== t.length || delete t[0]),
          Xr(this, t)
        );
      };
    }
  ),
    mr(["concat", "join", "slice"], function (n) {
      var r = t[n];
      tn.prototype[n] = function () {
        var n = this._wrapped;
        return null != n && (n = r.apply(n, arguments)), Xr(this, n);
      };
    });
  var Zr = Yr({
    __proto__: null,
    VERSION: n,
    restArguments: j,
    isObject: _,
    isNull: function (n) {
      return null === n;
    },
    isUndefined: w,
    isBoolean: A,
    isElement: function (n) {
      return !(!n || 1 !== n.nodeType);
    },
    isString: S,
    isNumber: O,
    isDate: M,
    isRegExp: E,
    isError: B,
    isSymbol: N,
    isArrayBuffer: I,
    isDataView: q,
    isArray: U,
    isFunction: D,
    isArguments: L,
    isFinite: function (n) {
      return !N(n) && d(n) && !isNaN(parseFloat(n));
    },
    isNaN: $,
    isTypedArray: X,
    isEmpty: function (n) {
      if (null == n) return !0;
      var r = Y(n);
      return "number" == typeof r && (U(n) || S(n) || L(n))
        ? 0 === r
        : 0 === Y(nn(n));
    },
    isMatch: rn,
    isEqual: function (n, r) {
      return on(n, r);
    },
    isMap: dn,
    isWeakMap: gn,
    isSet: bn,
    isWeakSet: mn,
    keys: nn,
    allKeys: an,
    values: jn,
    pairs: function (n) {
      for (var r = nn(n), t = r.length, e = Array(t), u = 0; u < t; u++)
        e[u] = [r[u], n[r[u]]];
      return e;
    },
    invert: _n,
    functions: wn,
    methods: wn,
    extend: xn,
    extendOwn: Sn,
    assign: Sn,
    defaults: On,
    create: function (n, r) {
      var t = Mn(n);
      return r && Sn(t, r), t;
    },
    clone: function (n) {
      return _(n) ? (U(n) ? n.slice() : xn({}, n)) : n;
    },
    tap: function (n, r) {
      return r(n), n;
    },
    get: In,
    has: function (n, r) {
      for (var t = (r = Bn(r)).length, e = 0; e < t; e++) {
        var u = r[e];
        if (!W(n, u)) return !1;
        n = n[u];
      }
      return !!t;
    },
    mapObject: function (n, r, t) {
      r = Pn(r, t);
      for (var e = nn(n), u = e.length, o = {}, i = 0; i < u; i++) {
        var a = e[i];
        o[a] = r(n[a], a, n);
      }
      return o;
    },
    identity: Tn,
    constant: C,
    noop: qn,
    toPath: En,
    property: Dn,
    propertyOf: function (n) {
      return null == n
        ? qn
        : function (r) {
            return In(n, r);
          };
    },
    matcher: kn,
    matches: kn,
    times: function (n, r, t) {
      var e = Array(Math.max(0, n));
      r = Rn(r, t, 1);
      for (var u = 0; u < n; u++) e[u] = r(u);
      return e;
    },
    random: Un,
    now: Wn,
    escape: $n,
    unescape: Cn,
    templateSettings: Kn,
    template: function (n, r, t) {
      !r && t && (r = t), (r = On({}, r, tn.templateSettings));
      var e = RegExp(
          [
            (r.escape || Jn).source,
            (r.interpolate || Jn).source,
            (r.evaluate || Jn).source,
          ].join("|") + "|$",
          "g"
        ),
        u = 0,
        o = "__p+='";
      n.replace(e, function (r, t, e, i, a) {
        return (
          (o += n.slice(u, a).replace(Hn, Qn)),
          (u = a + r.length),
          t
            ? (o += "'+\n((__t=(" + t + "))==null?'':_.escape(__t))+\n'")
            : e
            ? (o += "'+\n((__t=(" + e + "))==null?'':__t)+\n'")
            : i && (o += "';\n" + i + "\n__p+='"),
          r
        );
      }),
        (o += "';\n");
      var i,
        a = r.variable;
      if (a) {
        if (!Xn.test(a))
          throw new Error("variable is not a bare identifier: " + a);
      } else (o = "with(obj||{}){\n" + o + "}\n"), (a = "obj");
      o =
        "var __t,__p='',__j=Array.prototype.join," +
        "print=function(){__p+=__j.call(arguments,'');};\n" +
        o +
        "return __p;\n";
      try {
        i = new Function(a, "_", o);
      } catch (n) {
        throw ((n.source = o), n);
      }
      var f = function (n) {
        return i.call(this, n, tn);
      };
      return (f.source = "function(" + a + "){\n" + o + "}"), f;
    },
    result: function (n, r, t) {
      var e = (r = Bn(r)).length;
      if (!e) return D(t) ? t.call(n) : t;
      for (var u = 0; u < e; u++) {
        var o = null == n ? void 0 : n[r[u]];
        void 0 === o && ((o = t), (u = e)), (n = D(o) ? o.call(n) : o);
      }
      return n;
    },
    uniqueId: function (n) {
      var r = ++Yn + "";
      return n ? n + r : r;
    },
    chain: function (n) {
      var r = tn(n);
      return (r._chain = !0), r;
    },
    iteratee: Vn,
    partial: nr,
    bind: rr,
    bindAll: ur,
    memoize: function (n, r) {
      var t = function (e) {
        var u = t.cache,
          o = "" + (r ? r.apply(this, arguments) : e);
        return W(u, o) || (u[o] = n.apply(this, arguments)), u[o];
      };
      return (t.cache = {}), t;
    },
    delay: or,
    defer: ir,
    throttle: function (n, r, t) {
      var e,
        u,
        o,
        i,
        a = 0;
      t || (t = {});
      var f = function () {
          (a = !1 === t.leading ? 0 : Wn()),
            (e = null),
            (i = n.apply(u, o)),
            e || (u = o = null);
        },
        c = function () {
          var c = Wn();
          a || !1 !== t.leading || (a = c);
          var l = r - (c - a);
          return (
            (u = this),
            (o = arguments),
            l <= 0 || l > r
              ? (e && (clearTimeout(e), (e = null)),
                (a = c),
                (i = n.apply(u, o)),
                e || (u = o = null))
              : e || !1 === t.trailing || (e = setTimeout(f, l)),
            i
          );
        };
      return (
        (c.cancel = function () {
          clearTimeout(e), (a = 0), (e = u = o = null);
        }),
        c
      );
    },
    debounce: function (n, r, t) {
      var e,
        u,
        o,
        i,
        a,
        f = function () {
          var c = Wn() - u;
          r > c
            ? (e = setTimeout(f, r - c))
            : ((e = null), t || (i = n.apply(a, o)), e || (o = a = null));
        },
        c = j(function (c) {
          return (
            (a = this),
            (o = c),
            (u = Wn()),
            e || ((e = setTimeout(f, r)), t && (i = n.apply(a, o))),
            i
          );
        });
      return (
        (c.cancel = function () {
          clearTimeout(e), (e = o = a = null);
        }),
        c
      );
    },
    wrap: function (n, r) {
      return nr(r, n);
    },
    negate: ar,
    compose: function () {
      var n = arguments,
        r = n.length - 1;
      return function () {
        for (var t = r, e = n[r].apply(this, arguments); t--; )
          e = n[t].call(this, e);
        return e;
      };
    },
    after: function (n, r) {
      return function () {
        if (--n < 1) return r.apply(this, arguments);
      };
    },
    before: fr,
    once: cr,
    findKey: lr,
    findIndex: pr,
    findLastIndex: vr,
    sortedIndex: hr,
    indexOf: dr,
    lastIndexOf: gr,
    find: br,
    detect: br,
    findWhere: function (n, r) {
      return br(n, kn(r));
    },
    each: mr,
    forEach: mr,
    map: jr,
    collect: jr,
    reduce: wr,
    foldl: wr,
    inject: wr,
    reduceRight: Ar,
    foldr: Ar,
    filter: xr,
    select: xr,
    reject: function (n, r, t) {
      return xr(n, ar(Pn(r)), t);
    },
    every: Sr,
    all: Sr,
    some: Or,
    any: Or,
    contains: Mr,
    includes: Mr,
    include: Mr,
    invoke: Er,
    pluck: Br,
    where: function (n, r) {
      return xr(n, kn(r));
    },
    max: Nr,
    min: function (n, r, t) {
      var e,
        u,
        o = 1 / 0,
        i = 1 / 0;
      if (
        null == r ||
        ("number" == typeof r && "object" != typeof n[0] && null != n)
      )
        for (var a = 0, f = (n = tr(n) ? n : jn(n)).length; a < f; a++)
          null != (e = n[a]) && e < o && (o = e);
      else
        (r = Pn(r, t)),
          mr(n, function (n, t, e) {
            ((u = r(n, t, e)) < i || (u === 1 / 0 && o === 1 / 0)) &&
              ((o = n), (i = u));
          });
      return o;
    },
    shuffle: function (n) {
      return kr(n, 1 / 0);
    },
    sample: kr,
    sortBy: function (n, r, t) {
      var e = 0;
      return (
        (r = Pn(r, t)),
        Br(
          jr(n, function (n, t, u) {
            return { value: n, index: e++, criteria: r(n, t, u) };
          }).sort(function (n, r) {
            var t = n.criteria,
              e = r.criteria;
            if (t !== e) {
              if (t > e || void 0 === t) return 1;
              if (t < e || void 0 === e) return -1;
            }
            return n.index - r.index;
          }),
          "value"
        )
      );
    },
    groupBy: Rr,
    indexBy: Fr,
    countBy: Vr,
    partition: Pr,
    toArray: Tr,
    size: function (n) {
      return null == n ? 0 : tr(n) ? n.length : nn(n).length;
    },
    pick: Ur,
    omit: Wr,
    first: Lr,
    head: Lr,
    take: Lr,
    initial: zr,
    last: function (n, r, t) {
      return null == n || n.length < 1
        ? null == r || t
          ? void 0
          : []
        : null == r || t
        ? n[n.length - 1]
        : $r(n, Math.max(0, n.length - r));
    },
    rest: $r,
    tail: $r,
    drop: $r,
    compact: function (n) {
      return xr(n, Boolean);
    },
    flatten: function (n, r) {
      return er(n, r, !1);
    },
    without: Kr,
    uniq: Jr,
    unique: Jr,
    union: Gr,
    intersection: function (n) {
      for (var r = [], t = arguments.length, e = 0, u = Y(n); e < u; e++) {
        var o = n[e];
        if (!Mr(r, o)) {
          var i;
          for (i = 1; i < t && Mr(arguments[i], o); i++);
          i === t && r.push(o);
        }
      }
      return r;
    },
    difference: Cr,
    unzip: Hr,
    transpose: Hr,
    zip: Qr,
    object: function (n, r) {
      for (var t = {}, e = 0, u = Y(n); e < u; e++)
        r ? (t[n[e]] = r[e]) : (t[n[e][0]] = n[e][1]);
      return t;
    },
    range: function (n, r, t) {
      null == r && ((r = n || 0), (n = 0)), t || (t = r < n ? -1 : 1);
      for (
        var e = Math.max(Math.ceil((r - n) / t), 0), u = Array(e), o = 0;
        o < e;
        o++, n += t
      )
        u[o] = n;
      return u;
    },
    chunk: function (n, r) {
      if (null == r || r < 1) return [];
      for (var t = [], e = 0, u = n.length; e < u; )
        t.push(i.call(n, e, (e += r)));
      return t;
    },
    mixin: Yr,
    default: tn,
  });
  return (Zr._ = Zr), Zr;
});
/* @license GNU-GPL-2.0-or-later https://git.drupalcode.org/project/once/-/raw/v1.0.1/LICENSE.md */
/*! @drupal/once - v1.0.1 - 2021-06-12 */
var once = (function () {
  "use strict";
  var n = /[\11\12\14\15\40]+/,
    e = "data-once",
    t = document;
  function r(n, t, r) {
    return n[t + "Attribute"](e, r);
  }
  function o(e) {
    if ("string" != typeof e) throw new TypeError("once ID must be a string");
    if ("" === e || n.test(e))
      throw new RangeError("once ID must not be empty or contain spaces");
    return '[data-once~="' + e + '"]';
  }
  function u(n) {
    if (!(n instanceof Element))
      throw new TypeError("The element must be an instance of Element");
    return !0;
  }
  function i(n, e) {
    void 0 === e && (e = t);
    var r = n;
    if (null === n) r = [];
    else {
      if (!n) throw new TypeError("Selector must not be empty");
      "string" != typeof n || (e !== t && !u(e))
        ? n instanceof Element && (r = [n])
        : (r = e.querySelectorAll(n));
    }
    return Array.prototype.slice.call(r);
  }
  function c(n, e, t) {
    return e.filter(function (e) {
      var r = u(e) && e.matches(n);
      return r && t && t(e), r;
    });
  }
  function f(e, t) {
    var o = t.add,
      u = t.remove,
      i = [];
    r(e, "has") &&
      r(e, "get")
        .trim()
        .split(n)
        .forEach(function (n) {
          i.indexOf(n) < 0 && n !== u && i.push(n);
        }),
      o && i.push(o);
    var c = i.join(" ");
    r(e, "" === c ? "remove" : "set", c);
  }
  function a(n, e, t) {
    return c(":not(" + o(n) + ")", i(e, t), function (e) {
      return f(e, { add: n });
    });
  }
  return (
    (a.remove = function (n, e, t) {
      return c(o(n), i(e, t), function (e) {
        return f(e, { remove: n });
      });
    }),
    (a.filter = function (n, e, t) {
      return c(o(n), i(e, t));
    }),
    (a.find = function (n, e) {
      return i(n ? o(n) : "[data-once]", e);
    }),
    a
  );
})();

/* @license GNU-GPL-2.0-or-later https://www.drupal.org/licensing/faq */
(function () {
  const settingsElement = document.querySelector(
    'head > script[type="application/json"][data-drupal-selector="drupal-settings-json"], body > script[type="application/json"][data-drupal-selector="drupal-settings-json"]'
  );
  window.drupalSettings = {};
  if (settingsElement !== null)
    window.drupalSettings = JSON.parse(settingsElement.textContent);
})();
window.Drupal = { behaviors: {}, locale: {} };
(function (
  Drupal,
  drupalSettings,
  drupalTranslations,
  console,
  Proxy,
  Reflect
) {
  Drupal.throwError = function (error) {
    setTimeout(() => {
      throw error;
    }, 0);
  };
  Drupal.attachBehaviors = function (context, settings) {
    context = context || document;
    settings = settings || drupalSettings;
    const behaviors = Drupal.behaviors;
    Object.keys(behaviors || {}).forEach((i) => {
      if (typeof behaviors[i].attach === "function")
        try {
          behaviors[i].attach(context, settings);
        } catch (e) {
          Drupal.throwError(e);
        }
    });
  };
  Drupal.detachBehaviors = function (context, settings, trigger) {
    context = context || document;
    settings = settings || drupalSettings;
    trigger = trigger || "unload";
    const behaviors = Drupal.behaviors;
    Object.keys(behaviors || {}).forEach((i) => {
      if (typeof behaviors[i].detach === "function")
        try {
          behaviors[i].detach(context, settings, trigger);
        } catch (e) {
          Drupal.throwError(e);
        }
    });
  };
  Drupal.checkPlain = function (str) {
    str = str
      .toString()
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
    return str;
  };
  Drupal.formatString = function (str, args) {
    const processedArgs = {};
    Object.keys(args || {}).forEach((key) => {
      switch (key.charAt(0)) {
        case "@":
          processedArgs[key] = Drupal.checkPlain(args[key]);
          break;
        case "!":
          processedArgs[key] = args[key];
          break;
        default:
          processedArgs[key] = Drupal.theme("placeholder", args[key]);
          break;
      }
    });
    return Drupal.stringReplace(str, processedArgs, null);
  };
  Drupal.stringReplace = function (str, args, keys) {
    if (str.length === 0) return str;
    if (!Array.isArray(keys)) {
      keys = Object.keys(args || {});
      keys.sort((a, b) => a.length - b.length);
    }
    if (keys.length === 0) return str;
    const key = keys.pop();
    const fragments = str.split(key);
    if (keys.length) {
      for (let i = 0; i < fragments.length; i++)
        fragments[i] = Drupal.stringReplace(fragments[i], args, keys.slice(0));
    }
    return fragments.join(args[key]);
  };
  Drupal.t = function (str, args, options) {
    options = options || {};
    options.context = options.context || "";
    if (
      typeof drupalTranslations !== "undefined" &&
      drupalTranslations.strings &&
      drupalTranslations.strings[options.context] &&
      drupalTranslations.strings[options.context][str]
    )
      str = drupalTranslations.strings[options.context][str];
    if (args) str = Drupal.formatString(str, args);
    return str;
  };
  Drupal.url = function (path) {
    return (
      drupalSettings?.path?.baseUrl + drupalSettings?.path?.pathPrefix + path
    );
  };
  Drupal.url.toAbsolute = function (url) {
    const urlParsingNode = document.createElement("a");
    try {
      url = decodeURIComponent(url);
    } catch (e) {}
    urlParsingNode.setAttribute("href", url);
    return urlParsingNode.cloneNode(false).href;
  };
  Drupal.url.isLocal = function (url) {
    let absoluteUrl = Drupal.url.toAbsolute(url);
    let { protocol } = window.location;
    if (protocol === "http:" && absoluteUrl.indexOf("https:") === 0)
      protocol = "https:";
    let baseUrl = `${protocol}//${
      window.location.host
    }${drupalSettings?.path?.baseUrl?.slice(0, -1)}`;
    try {
      absoluteUrl = decodeURIComponent(absoluteUrl);
    } catch (e) {}
    try {
      baseUrl = decodeURIComponent(baseUrl);
    } catch (e) {}
    return absoluteUrl === baseUrl || absoluteUrl.indexOf(`${baseUrl}/`) === 0;
  };
  Drupal.formatPlural = function (count, singular, plural, args, options) {
    args = args || {};
    args["@count"] = count;
    const pluralDelimiter = drupalSettings.pluralDelimiter;
    const translations = Drupal.t(
      singular + pluralDelimiter + plural,
      args,
      options
    ).split(pluralDelimiter);
    let index = 0;
    if (
      typeof drupalTranslations !== "undefined" &&
      drupalTranslations.pluralFormula
    )
      index =
        count in drupalTranslations.pluralFormula
          ? drupalTranslations.pluralFormula[count]
          : drupalTranslations.pluralFormula.default;
    else {
      if (args["@count"] !== 1) index = 1;
    }
    return translations[index];
  };
  Drupal.encodePath = function (item) {
    return window.encodeURIComponent(item).replace(/%2F/g, "/");
  };
  Drupal.deprecationError = ({ message }) => {
    if (
      drupalSettings.suppressDeprecationErrors === false &&
      typeof console !== "undefined" &&
      console.warn
    )
      console.warn(`[Deprecation] ${message}`);
  };
  Drupal.deprecatedProperty = ({ target, deprecatedProperty, message }) => {
    if (!Proxy || !Reflect) return target;
    return new Proxy(target, {
      get: (target, key, ...rest) => {
        if (key === deprecatedProperty) Drupal.deprecationError({ message });
        return Reflect.get(target, key, ...rest);
      },
    });
  };
  Drupal.theme = function (func, ...args) {
    if (func in Drupal.theme) return Drupal.theme[func](...args);
  };
  Drupal.theme.placeholder = function (str) {
    return `<em class="placeholder">${Drupal.checkPlain(str)}</em>`;
  };
})(
  Drupal,
  window.drupalSettings,
  window.drupalTranslations,
  window.console,
  window.Proxy,
  window.Reflect
);
if (window.jQuery) jQuery.noConflict();
document.documentElement.className += " js";
(function (Drupal, drupalSettings) {
  const domReady = (callback) => {
    const listener = () => {
      callback();
      document.removeEventListener("DOMContentLoaded", listener);
    };
    if (document.readyState !== "loading") setTimeout(callback, 0);
    else document.addEventListener("DOMContentLoaded", listener);
  };
  domReady(() => {
    Drupal.attachBehaviors(document, drupalSettings);
  });
})(Drupal, window.drupalSettings);
(function ($, Drupal, drupalSettings) {
  "use strict";
  Drupal.google_analytics = {};
  $(document).ready(function () {
    $(document.body).on("mousedown keyup touchstart", function (event) {
      $(event.target)
        .closest("a,area")
        .each(function () {
          if (Drupal.google_analytics.isInternal(this.href))
            if (
              $(this).is(".colorbox") &&
              drupalSettings?.google_analytics?.trackColorbox
            ) {
            } else if (
              drupalSettings.google_analytics.trackDownload &&
              Drupal.google_analytics.isDownload(this.href)
            )
              gtag(
                "event",
                Drupal.google_analytics
                  .getDownloadExtension(this.href)
                  .toUpperCase(),
                {
                  event_category: "Downloads",
                  event_label: Drupal.google_analytics.getPageUrl(this.href),
                  transport_type: "beacon",
                }
              );
            else {
              if (Drupal.google_analytics.isInternalSpecial(this.href))
                gtag("config", drupalSettings.google_analytics.account, {
                  page_path: Drupal.google_analytics.getPageUrl(this.href),
                  transport_type: "beacon",
                });
            }
          else if (
            drupalSettings.google_analytics.trackMailto &&
            $(this).is("a[href^='mailto:'],area[href^='mailto:']")
          )
            gtag("event", "Click", {
              event_category: "Mails",
              event_label: this.href.substring(7),
              transport_type: "beacon",
            });
          else if (
            drupalSettings.google_analytics.trackTel &&
            $(this).is("a[href^='tel:'],area[href^='tel:']")
          )
            gtag("event", "Click", {
              event_category: "Telephone calls",
              event_label: this.href.substring(4),
              transport_type: "beacon",
            });
          else {
            if (
              drupalSettings.google_analytics.trackOutbound &&
              this.href.match(/^\w+:\/\//i)
            )
              if (
                drupalSettings.google_analytics.trackDomainMode !== 2 ||
                (drupalSettings.google_analytics.trackDomainMode === 2 &&
                  !Drupal.google_analytics.isCrossDomain(
                    this.hostname,
                    drupalSettings.google_analytics.trackCrossDomains
                  ))
              )
                gtag("event", "Click", {
                  event_category: "Outbound links",
                  event_label: this.href,
                  transport_type: "beacon",
                });
          }
        });
    });
    if (drupalSettings?.google_analytics?.trackUrlFragments)
      window.onhashchange = function () {
        gtag("config", drupalSettings.google_analytics.account, {
          page_path: location.pathname + location.search + location.hash,
        });
      };
    if (drupalSettings?.google_analytics?.trackColorbox)
      $(document).on("cbox_complete", function () {
        var href = $.colorbox.element().attr("href");
        if (href)
          gtag("config", drupalSettings.google_analytics.account, {
            page_path: Drupal.google_analytics.getPageUrl(href),
          });
      });
  });
  Drupal.google_analytics.isCrossDomain = function (hostname, crossDomains) {
    return $.inArray(hostname, crossDomains) > -1 ? true : false;
  };
  Drupal.google_analytics.isDownload = function (url) {
    var isDownload = new RegExp(
      "\\.(" +
        drupalSettings.google_analytics.trackDownloadExtensions +
        ")([?#].*)?$",
      "i"
    );
    return isDownload.test(url);
  };
  Drupal.google_analytics.isInternal = function (url) {
    var isInternal = new RegExp("^(https?)://" + window.location.host, "i");
    return isInternal.test(url);
  };
  Drupal.google_analytics.isInternalSpecial = function (url) {
    var isInternalSpecial = new RegExp("(/go/.*)$", "i");
    return isInternalSpecial.test(url);
  };
  Drupal.google_analytics.getPageUrl = function (url) {
    var extractInternalUrl = new RegExp(
      "^(https?)://" + window.location.host,
      "i"
    );
    return url.replace(extractInternalUrl, "");
  };
  Drupal.google_analytics.getDownloadExtension = function (url) {
    var extractDownloadextension = new RegExp(
      "\\.(" +
        drupalSettings.google_analytics.trackDownloadExtensions +
        ")([?#].*)?$",
      "i"
    );
    var extension = extractDownloadextension.exec(url);
    return extension === null ? "" : extension[1];
  };
})(jQuery, Drupal, drupalSettings);
(function ($) {
  let trigger = $(".official-website-banner__trigger");
  trigger.addClass("content-collapsed");
  trigger.click(function () {
    $(".official-website-banner__content").slideToggle(250);
    if (trigger.hasClass("content-collapsed"))
      trigger.removeClass("content-collapsed").addClass("content-expanded");
    else {
      if (trigger.hasClass("content-expanded"))
        trigger.removeClass("content-expanded").addClass("content-collapsed");
    }
  });
})(jQuery);
(function ($, Drupal, drupalSettings) {
  "use strict";
  Drupal.extlink = Drupal.extlink || {};
  Drupal.extlink.attach = function (context, drupalSettings) {
    if (
      typeof drupalSettings.data === "undefined" ||
      !drupalSettings.data.hasOwnProperty("extlink")
    )
      return;
    var extIconPlacement = "append";
    if (
      drupalSettings.data.extlink.extIconPlacement &&
      drupalSettings.data.extlink.extIconPlacement != "0"
    )
      extIconPlacement = drupalSettings.data.extlink.extIconPlacement;
    var pattern =
      /^(([^\/:]+?\.)*)([^\.:]{1,})((\.[a-z0-9]{1,253})*)(:[0-9]{1,5})?$/;
    var host = window.location.host.replace(pattern, "$2$3$6");
    var subdomain = window.location.host.replace(host, "");
    var subdomains;
    if (drupalSettings.data.extlink.extSubdomains) subdomains = "([^/]*\\.)?";
    else if (subdomain === "www." || subdomain === "") subdomains = "(www\\.)?";
    else subdomains = subdomain.replace(".", "\\.");
    var whitelistedDomains = false;
    if (drupalSettings.data.extlink.whitelistedDomains) {
      whitelistedDomains = [];
      for (
        var i = 0;
        i < drupalSettings.data.extlink.whitelistedDomains.length;
        i++
      )
        whitelistedDomains.push(
          new RegExp(
            "^https?:\\/\\/" +
              drupalSettings.data.extlink.whitelistedDomains[i].replace(
                /(\r\n|\n|\r)/gm,
                ""
              ) +
              ".*$",
            "i"
          )
        );
    }
    var internal_link = new RegExp(
      "^https?://([^@]*@)?" + subdomains + host,
      "i"
    );
    var extInclude = false;
    if (drupalSettings.data.extlink.extInclude)
      extInclude = new RegExp(
        drupalSettings.data.extlink.extInclude.replace(/\\/, "\\"),
        "i"
      );
    var extExclude = false;
    if (drupalSettings.data.extlink.extExclude)
      extExclude = new RegExp(
        drupalSettings.data.extlink.extExclude.replace(/\\/, "\\"),
        "i"
      );
    var extCssExclude = false;
    if (drupalSettings.data.extlink.extCssExclude)
      extCssExclude = drupalSettings.data.extlink.extCssExclude;
    var extCssExplicit = false;
    if (drupalSettings.data.extlink.extCssExplicit)
      extCssExplicit = drupalSettings.data.extlink.extCssExplicit;
    var external_links = [];
    var mailto_links = [];
    $("a:not([data-extlink]), area:not([data-extlink])", context).each(
      function (el) {
        try {
          var url = "";
          if (typeof this.href == "string") url = this.href.toLowerCase();
          else {
            if (typeof this.href == "object") url = this.href.baseVal;
          }
          if (
            url.indexOf("http") === 0 &&
            ((!internal_link.test(url) &&
              !(extExclude && extExclude.test(url))) ||
              (extInclude && extInclude.test(url))) &&
            !(extCssExclude && $(this).is(extCssExclude)) &&
            !(extCssExclude && $(this).parents(extCssExclude).length > 0) &&
            !(extCssExplicit && $(this).parents(extCssExplicit).length < 1)
          ) {
            var match = false;
            if (whitelistedDomains)
              for (var i = 0; i < whitelistedDomains.length; i++)
                if (whitelistedDomains[i].test(url)) {
                  match = true;
                  break;
                }
            if (!match) external_links.push(this);
          } else {
            if (
              this.tagName !== "AREA" &&
              url.indexOf("mailto:") === 0 &&
              !(extCssExclude && $(this).parents(extCssExclude).length > 0) &&
              !(extCssExplicit && $(this).parents(extCssExplicit).length < 1)
            )
              mailto_links.push(this);
          }
        } catch (error) {
          return false;
        }
      }
    );
    if (
      drupalSettings.data.extlink.extClass !== "0" &&
      drupalSettings.data.extlink.extClass !== ""
    )
      Drupal.extlink.applyClassAndSpan(
        external_links,
        drupalSettings.data.extlink.extClass,
        extIconPlacement
      );
    if (
      drupalSettings.data.extlink.mailtoClass !== "0" &&
      drupalSettings.data.extlink.mailtoClass !== ""
    )
      Drupal.extlink.applyClassAndSpan(
        mailto_links,
        drupalSettings.data.extlink.mailtoClass,
        extIconPlacement
      );
    if (drupalSettings.data.extlink.extTarget) {
      $(external_links)
        .filter(function () {
          return !(
            drupalSettings.data.extlink.extTargetNoOverride &&
            $(this).is("a[target]")
          );
        })
        .attr({ target: "_blank" });
      $(external_links).attr("rel", function (i, val) {
        if (val === null || typeof val === "undefined") return "noopener";
        if (val.indexOf("noopener") > -1)
          if (val.indexOf("noopener") === -1) return val + " noopener";
          else return val;
        else return val + " noopener";
      });
    }
    if (drupalSettings.data.extlink.extNofollow)
      $(external_links).attr("rel", function (i, val) {
        if (val === null || typeof val === "undefined") return "nofollow";
        var target = "nofollow";
        if (drupalSettings.data.extlink.extFollowNoOverride) target = "follow";
        if (val.indexOf(target) === -1) return val + " nofollow";
        return val;
      });
    if (drupalSettings.data.extlink.extNoreferrer)
      $(external_links).attr("rel", function (i, val) {
        if (val === null || typeof val === "undefined") return "noreferrer";
        if (val.indexOf("noreferrer") === -1) return val + " noreferrer";
        return val;
      });
    Drupal.extlink = Drupal.extlink || {};
    Drupal.extlink.popupClickHandler =
      Drupal.extlink.popupClickHandler ||
      function () {
        if (drupalSettings.data.extlink.extAlert)
          return confirm(drupalSettings.data.extlink.extAlertText);
      };
    $(external_links).off("click.extlink");
    $(external_links).on("click.extlink", function (e) {
      return Drupal.extlink.popupClickHandler(e, this);
    });
  };
  Drupal.extlink.applyClassAndSpan = function (
    links,
    class_name,
    icon_placement
  ) {
    var $links_to_process;
    if (drupalSettings.data.extlink.extImgClass) $links_to_process = $(links);
    else {
      var links_with_images = $(links).find("img, svg").parents("a");
      $links_to_process = $(links).not(links_with_images);
    }
    if (class_name !== "0") $links_to_process.addClass(class_name);
    $links_to_process.attr("data-extlink", "");
    var i;
    var length = $links_to_process.length;
    for (i = 0; i < length; i++) {
      var $link = $($links_to_process[i]);
      if (drupalSettings.data.extlink.extUseFontAwesome)
        if (class_name === drupalSettings.data.extlink.mailtoClass)
          $link[icon_placement](
            '<span class="fa-' +
              class_name +
              ' extlink"><span class="' +
              drupalSettings.data.extlink.extFaMailtoClasses +
              '" aria-label="' +
              drupalSettings.data.extlink.mailtoLabel +
              '"></span></span>'
          );
        else
          $link[icon_placement](
            '<span class="fa-' +
              class_name +
              ' extlink"><span class="' +
              drupalSettings.data.extlink.extFaLinkClasses +
              '" aria-label="' +
              drupalSettings.data.extlink.extLabel +
              '"></span></span>'
          );
      else if (class_name === drupalSettings.data.extlink.mailtoClass)
        $link[icon_placement](
          '<svg focusable="false" class="' +
            class_name +
            '" role="img" aria-label="' +
            drupalSettings.data.extlink.mailtoLabel +
            '" xmlns="http://www.w3.org/2000/svg" viewBox="0 10 70 20"><metadata><sfw xmlns="http://ns.adobe.com/SaveForWeb/1.0/"><sliceSourceBounds y="-8160" x="-8165" width="16389" height="16384" bottomLeftOrigin="true"/><optimizationSettings><targetSettings targetSettingsID="0" fileFormat="PNG24Format"><PNG24Format transparency="true" filtered="false" interlaced="false" noMatteColor="false" matteColor="#FFFFFF"/></targetSettings></optimizationSettings></sfw></metadata><title>' +
            drupalSettings.data.extlink.mailtoLabel +
            '</title><path d="M56 14H8c-1.1 0-2 0.9-2 2v32c0 1.1 0.9 2 2 2h48c1.1 0 2-0.9 2-2V16C58 14.9 57.1 14 56 14zM50.5 18L32 33.4 13.5 18H50.5zM10 46V20.3l20.7 17.3C31.1 37.8 31.5 38 32 38s0.9-0.2 1.3-0.5L54 20.3V46H10z"/></svg>'
        );
      else
        $link[icon_placement](
          '<svg focusable="false" class="' +
            class_name +
            '" role="img" aria-label="' +
            drupalSettings.data.extlink.extLabel +
            '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 40"><metadata><sfw xmlns="http://ns.adobe.com/SaveForWeb/1.0/"><sliceSourceBounds y="-8160" x="-8165" width="16389" height="16384" bottomLeftOrigin="true"/><optimizationSettings><targetSettings targetSettingsID="0" fileFormat="PNG24Format"><PNG24Format transparency="true" filtered="false" interlaced="false" noMatteColor="false" matteColor="#FFFFFF"/></targetSettings></optimizationSettings></sfw></metadata><title>' +
            drupalSettings.data.extlink.extLabel +
            '</title><path d="M48 26c-1.1 0-2 0.9-2 2v26H10V18h26c1.1 0 2-0.9 2-2s-0.9-2-2-2H8c-1.1 0-2 0.9-2 2v40c0 1.1 0.9 2 2 2h40c1.1 0 2-0.9 2-2V28C50 26.9 49.1 26 48 26z"/><path d="M56 6H44c-1.1 0-2 0.9-2 2s0.9 2 2 2h7.2L30.6 30.6c-0.8 0.8-0.8 2 0 2.8C31 33.8 31.5 34 32 34s1-0.2 1.4-0.6L54 12.8V20c0 1.1 0.9 2 2 2s2-0.9 2-2V8C58 6.9 57.1 6 56 6z"/></svg>'
        );
    }
  };
  Drupal.behaviors.extlink = Drupal.behaviors.extlink || {};
  Drupal.behaviors.extlink.attach = function (context, drupalSettings) {
    if (typeof extlinkAttach === "function") extlinkAttach(context);
    else Drupal.extlink.attach(context, drupalSettings);
  };
})(jQuery, Drupal, drupalSettings);
+(function ($) {
  "use strict";
  var Affix = function (element, options) {
    this.options = $.extend({}, Affix.DEFAULTS, options);
    var target =
      this.options.target === Affix.DEFAULTS.target
        ? $(this.options.target)
        : $(document).find(this.options.target);
    this.$target = target
      .on("scroll.bs.affix.data-api", $.proxy(this.checkPosition, this))
      .on(
        "click.bs.affix.data-api",
        $.proxy(this.checkPositionWithEventLoop, this)
      );
    this.$element = $(element);
    this.affixed = null;
    this.unpin = null;
    this.pinnedOffset = null;
    this.checkPosition();
  };
  Affix.VERSION = "3.4.1";
  Affix.RESET = "affix affix-top affix-bottom";
  Affix.DEFAULTS = { offset: 0, target: window };
  Affix.prototype.getState = function (
    scrollHeight,
    height,
    offsetTop,
    offsetBottom
  ) {
    var scrollTop = this.$target.scrollTop();
    var position = this.$element.offset();
    var targetHeight = this.$target.height();
    if (offsetTop != null && this.affixed == "top")
      return scrollTop < offsetTop ? "top" : false;
    if (this.affixed == "bottom") {
      if (offsetTop != null)
        return scrollTop + this.unpin <= position.top ? false : "bottom";
      return scrollTop + targetHeight <= scrollHeight - offsetBottom
        ? false
        : "bottom";
    }
    var initializing = this.affixed == null;
    var colliderTop = initializing ? scrollTop : position.top;
    var colliderHeight = initializing ? targetHeight : height;
    if (offsetTop != null && scrollTop <= offsetTop) return "top";
    if (
      offsetBottom != null &&
      colliderTop + colliderHeight >= scrollHeight - offsetBottom
    )
      return "bottom";
    return false;
  };
  Affix.prototype.getPinnedOffset = function () {
    if (this.pinnedOffset) return this.pinnedOffset;
    this.$element.removeClass(Affix.RESET).addClass("affix");
    var scrollTop = this.$target.scrollTop();
    var position = this.$element.offset();
    return (this.pinnedOffset = position.top - scrollTop);
  };
  Affix.prototype.checkPositionWithEventLoop = function () {
    setTimeout($.proxy(this.checkPosition, this), 1);
  };
  Affix.prototype.checkPosition = function () {
    if (!this.$element.is(":visible")) return;
    var height = this.$element.height();
    var offset = this.options.offset;
    var offsetTop = offset.top;
    var offsetBottom = offset.bottom;
    var scrollHeight = Math.max(
      $(document).height(),
      $(document.body).height()
    );
    if (typeof offset != "object") offsetBottom = offsetTop = offset;
    if (typeof offsetTop == "function") offsetTop = offset.top(this.$element);
    if (typeof offsetBottom == "function")
      offsetBottom = offset.bottom(this.$element);
    var affix = this.getState(scrollHeight, height, offsetTop, offsetBottom);
    if (this.affixed != affix) {
      if (this.unpin != null) this.$element.css("top", "");
      var affixType = "affix" + (affix ? "-" + affix : "");
      var e = $.Event(affixType + ".bs.affix");
      this.$element.trigger(e);
      if (e.isDefaultPrevented()) return;
      this.affixed = affix;
      this.unpin = affix == "bottom" ? this.getPinnedOffset() : null;
      this.$element
        .removeClass(Affix.RESET)
        .addClass(affixType)
        .trigger(affixType.replace("affix", "affixed") + ".bs.affix");
    }
    if (affix == "bottom")
      this.$element.offset({ top: scrollHeight - height - offsetBottom });
  };
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.affix");
      var options = typeof option == "object" && option;
      if (!data) $this.data("bs.affix", (data = new Affix(this, options)));
      if (typeof option == "string") data[option]();
    });
  }
  var old = $.fn.affix;
  $.fn.affix = Plugin;
  $.fn.affix.Constructor = Affix;
  $.fn.affix.noConflict = function () {
    $.fn.affix = old;
    return this;
  };
  $(window).on("load", function () {
    $('[data-spy="affix"]').each(function () {
      var $spy = $(this);
      var data = $spy.data();
      data.offset = data.offset || {};
      if (data.offsetBottom != null) data.offset.bottom = data.offsetBottom;
      if (data.offsetTop != null) data.offset.top = data.offsetTop;
      Plugin.call($spy, data);
    });
  });
})(jQuery);
+(function ($) {
  "use strict";
  var dismiss = '[data-dismiss="alert"]';
  var Alert = function (el) {
    $(el).on("click", dismiss, this.close);
  };
  Alert.VERSION = "3.4.1";
  Alert.TRANSITION_DURATION = 150;
  Alert.prototype.close = function (e) {
    var $this = $(this);
    var selector = $this.attr("data-target");
    if (!selector) {
      selector = $this.attr("href");
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, "");
    }
    selector = selector === "#" ? [] : selector;
    var $parent = $(document).find(selector);
    if (e) e.preventDefault();
    if (!$parent.length) $parent = $this.closest(".alert");
    $parent.trigger((e = $.Event("close.bs.alert")));
    if (e.isDefaultPrevented()) return;
    $parent.removeClass("in");
    function removeElement() {
      $parent.detach().trigger("closed.bs.alert").remove();
    }
    $.support.transition && $parent.hasClass("fade")
      ? $parent
          .one("bsTransitionEnd", removeElement)
          .emulateTransitionEnd(Alert.TRANSITION_DURATION)
      : removeElement();
  };
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.alert");
      if (!data) $this.data("bs.alert", (data = new Alert(this)));
      if (typeof option == "string") data[option].call($this);
    });
  }
  var old = $.fn.alert;
  $.fn.alert = Plugin;
  $.fn.alert.Constructor = Alert;
  $.fn.alert.noConflict = function () {
    $.fn.alert = old;
    return this;
  };
  $(document).on("click.bs.alert.data-api", dismiss, Alert.prototype.close);
})(jQuery);
+(function ($) {
  "use strict";
  var Carousel = function (element, options) {
    this.$element = $(element);
    this.$indicators = this.$element.find(".carousel-indicators");
    this.options = options;
    this.paused = null;
    this.sliding = null;
    this.interval = null;
    this.$active = null;
    this.$items = null;
    this.options.keyboard &&
      this.$element.on("keydown.bs.carousel", $.proxy(this.keydown, this));
    this.options.pause == "hover" &&
      !("ontouchstart" in document.documentElement) &&
      this.$element
        .on("mouseenter.bs.carousel", $.proxy(this.pause, this))
        .on("mouseleave.bs.carousel", $.proxy(this.cycle, this));
  };
  Carousel.VERSION = "3.4.1";
  Carousel.TRANSITION_DURATION = 600;
  Carousel.DEFAULTS = {
    interval: 5000,
    pause: "hover",
    wrap: true,
    keyboard: true,
  };
  Carousel.prototype.keydown = function (e) {
    if (/input|textarea/i.test(e.target.tagName)) return;
    switch (e.which) {
      case 37:
        this.prev();
        break;
      case 39:
        this.next();
        break;
      default:
        return;
    }
    e.preventDefault();
  };
  Carousel.prototype.cycle = function (e) {
    e || (this.paused = false);
    this.interval && clearInterval(this.interval);
    this.options.interval &&
      !this.paused &&
      (this.interval = setInterval(
        $.proxy(this.next, this),
        this.options.interval
      ));
    return this;
  };
  Carousel.prototype.getItemIndex = function (item) {
    this.$items = item.parent().children(".item");
    return this.$items.index(item || this.$active);
  };
  Carousel.prototype.getItemForDirection = function (direction, active) {
    var activeIndex = this.getItemIndex(active);
    var willWrap =
      (direction == "prev" && activeIndex === 0) ||
      (direction == "next" && activeIndex == this.$items.length - 1);
    if (willWrap && !this.options.wrap) return active;
    var delta = direction == "prev" ? -1 : 1;
    var itemIndex = (activeIndex + delta) % this.$items.length;
    return this.$items.eq(itemIndex);
  };
  Carousel.prototype.to = function (pos) {
    var that = this;
    var activeIndex = this.getItemIndex(
      (this.$active = this.$element.find(".item.active"))
    );
    if (pos > this.$items.length - 1 || pos < 0) return;
    if (this.sliding)
      return this.$element.one("slid.bs.carousel", function () {
        that.to(pos);
      });
    if (activeIndex == pos) return this.pause().cycle();
    return this.slide(pos > activeIndex ? "next" : "prev", this.$items.eq(pos));
  };
  Carousel.prototype.pause = function (e) {
    e || (this.paused = true);
    if (this.$element.find(".next, .prev").length && $.support.transition) {
      this.$element.trigger($.support.transition.end);
      this.cycle(true);
    }
    this.interval = clearInterval(this.interval);
    return this;
  };
  Carousel.prototype.next = function () {
    if (this.sliding) return;
    return this.slide("next");
  };
  Carousel.prototype.prev = function () {
    if (this.sliding) return;
    return this.slide("prev");
  };
  Carousel.prototype.slide = function (type, next) {
    var $active = this.$element.find(".item.active");
    var $next = next || this.getItemForDirection(type, $active);
    var isCycling = this.interval;
    var direction = type == "next" ? "left" : "right";
    var that = this;
    if ($next.hasClass("active")) return (this.sliding = false);
    var relatedTarget = $next[0];
    var slideEvent = $.Event("slide.bs.carousel", { relatedTarget, direction });
    this.$element.trigger(slideEvent);
    if (slideEvent.isDefaultPrevented()) return;
    this.sliding = true;
    isCycling && this.pause();
    if (this.$indicators.length) {
      this.$indicators.find(".active").removeClass("active");
      var $nextIndicator = $(
        this.$indicators.children()[this.getItemIndex($next)]
      );
      $nextIndicator && $nextIndicator.addClass("active");
    }
    var slidEvent = $.Event("slid.bs.carousel", { relatedTarget, direction });
    if ($.support.transition && this.$element.hasClass("slide")) {
      $next.addClass(type);
      if (typeof $next === "object" && $next.length) $next[0].offsetWidth;
      $active.addClass(direction);
      $next.addClass(direction);
      $active
        .one("bsTransitionEnd", function () {
          $next.removeClass([type, direction].join(" ")).addClass("active");
          $active.removeClass(["active", direction].join(" "));
          that.sliding = false;
          setTimeout(function () {
            that.$element.trigger(slidEvent);
          }, 0);
        })
        .emulateTransitionEnd(Carousel.TRANSITION_DURATION);
    } else {
      $active.removeClass("active");
      $next.addClass("active");
      this.sliding = false;
      this.$element.trigger(slidEvent);
    }
    isCycling && this.cycle();
    return this;
  };
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.carousel");
      var options = $.extend(
        {},
        Carousel.DEFAULTS,
        $this.data(),
        typeof option == "object" && option
      );
      var action = typeof option == "string" ? option : options.slide;
      if (!data)
        $this.data("bs.carousel", (data = new Carousel(this, options)));
      if (typeof option == "number") data.to(option);
      else if (action) data[action]();
      else {
        if (options.interval) data.pause().cycle();
      }
    });
  }
  var old = $.fn.carousel;
  $.fn.carousel = Plugin;
  $.fn.carousel.Constructor = Carousel;
  $.fn.carousel.noConflict = function () {
    $.fn.carousel = old;
    return this;
  };
  var clickHandler = function (e) {
    var $this = $(this);
    var href = $this.attr("href");
    if (href) href = href.replace(/.*(?=#[^\s]+$)/, "");
    var target = $this.attr("data-target") || href;
    var $target = $(document).find(target);
    if (!$target.hasClass("carousel")) return;
    var options = $.extend({}, $target.data(), $this.data());
    var slideIndex = $this.attr("data-slide-to");
    if (slideIndex) options.interval = false;
    Plugin.call($target, options);
    if (slideIndex) $target.data("bs.carousel").to(slideIndex);
    e.preventDefault();
  };
  $(document)
    .on("click.bs.carousel.data-api", "[data-slide]", clickHandler)
    .on("click.bs.carousel.data-api", "[data-slide-to]", clickHandler);
  $(window).on("load", function () {
    $('[data-ride="carousel"]').each(function () {
      var $carousel = $(this);
      Plugin.call($carousel, $carousel.data());
    });
  });
})(jQuery);
+(function ($) {
  "use strict";
  var Collapse = function (element, options) {
    this.$element = $(element);
    this.options = $.extend({}, Collapse.DEFAULTS, options);
    this.$trigger = $(
      '[data-toggle="collapse"][href="#' +
        element.id +
        '"],' +
        '[data-toggle="collapse"][data-target="#' +
        element.id +
        '"]'
    );
    this.transitioning = null;
    if (this.options.parent) this.$parent = this.getParent();
    else this.addAriaAndCollapsedClass(this.$element, this.$trigger);
    if (this.options.toggle) this.toggle();
  };
  Collapse.VERSION = "3.4.1";
  Collapse.TRANSITION_DURATION = 350;
  Collapse.DEFAULTS = { toggle: true };
  Collapse.prototype.dimension = function () {
    var hasWidth = this.$element.hasClass("width");
    return hasWidth ? "width" : "height";
  };
  Collapse.prototype.show = function () {
    if (this.transitioning || this.$element.hasClass("in")) return;
    var activesData;
    var actives =
      this.$parent &&
      this.$parent.children(".panel").children(".in, .collapsing");
    if (actives && actives.length) {
      activesData = actives.data("bs.collapse");
      if (activesData && activesData.transitioning) return;
    }
    var startEvent = $.Event("show.bs.collapse");
    this.$element.trigger(startEvent);
    if (startEvent.isDefaultPrevented()) return;
    if (actives && actives.length) {
      Plugin.call(actives, "hide");
      activesData || actives.data("bs.collapse", null);
    }
    var dimension = this.dimension();
    this.$element
      .removeClass("collapse")
      .addClass("collapsing")
      [dimension](0)
      .attr("aria-expanded", true);
    this.$trigger.removeClass("collapsed").attr("aria-expanded", true);
    this.transitioning = 1;
    var complete = function () {
      this.$element
        .removeClass("collapsing")
        .addClass("collapse in")
        [dimension]("");
      this.transitioning = 0;
      this.$element.trigger("shown.bs.collapse");
    };
    if (!$.support.transition) return complete.call(this);
    var scrollSize = $.camelCase(["scroll", dimension].join("-"));
    this.$element
      .one("bsTransitionEnd", $.proxy(complete, this))
      .emulateTransitionEnd(Collapse.TRANSITION_DURATION)
      [dimension](this.$element[0][scrollSize]);
  };
  Collapse.prototype.hide = function () {
    if (this.transitioning || !this.$element.hasClass("in")) return;
    var startEvent = $.Event("hide.bs.collapse");
    this.$element.trigger(startEvent);
    if (startEvent.isDefaultPrevented()) return;
    var dimension = this.dimension();
    this.$element[dimension](this.$element[dimension]())[0].offsetHeight;
    this.$element
      .addClass("collapsing")
      .removeClass("collapse in")
      .attr("aria-expanded", false);
    this.$trigger.addClass("collapsed").attr("aria-expanded", false);
    this.transitioning = 1;
    var complete = function () {
      this.transitioning = 0;
      this.$element
        .removeClass("collapsing")
        .addClass("collapse")
        .trigger("hidden.bs.collapse");
    };
    if (!$.support.transition) return complete.call(this);
    this.$element[dimension](0)
      .one("bsTransitionEnd", $.proxy(complete, this))
      .emulateTransitionEnd(Collapse.TRANSITION_DURATION);
  };
  Collapse.prototype.toggle = function () {
    this[this.$element.hasClass("in") ? "hide" : "show"]();
  };
  Collapse.prototype.getParent = function () {
    return $(document)
      .find(this.options.parent)
      .find(
        '[data-toggle="collapse"][data-parent="' + this.options.parent + '"]'
      )
      .each(
        $.proxy(function (i, element) {
          var $element = $(element);
          this.addAriaAndCollapsedClass(
            getTargetFromTrigger($element),
            $element
          );
        }, this)
      )
      .end();
  };
  Collapse.prototype.addAriaAndCollapsedClass = function ($element, $trigger) {
    var isOpen = $element.hasClass("in");
    $element.attr("aria-expanded", isOpen);
    $trigger.toggleClass("collapsed", !isOpen).attr("aria-expanded", isOpen);
  };
  function getTargetFromTrigger($trigger) {
    var href;
    var target =
      $trigger.attr("data-target") ||
      ((href = $trigger.attr("href")) && href.replace(/.*(?=#[^\s]+$)/, ""));
    return $(document).find(target);
  }
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.collapse");
      var options = $.extend(
        {},
        Collapse.DEFAULTS,
        $this.data(),
        typeof option == "object" && option
      );
      if (!data && options.toggle && /show|hide/.test(option))
        options.toggle = false;
      if (!data)
        $this.data("bs.collapse", (data = new Collapse(this, options)));
      if (typeof option == "string") data[option]();
    });
  }
  var old = $.fn.collapse;
  $.fn.collapse = Plugin;
  $.fn.collapse.Constructor = Collapse;
  $.fn.collapse.noConflict = function () {
    $.fn.collapse = old;
    return this;
  };
  $(document).on(
    "click.bs.collapse.data-api",
    '[data-toggle="collapse"]',
    function (e) {
      var $this = $(this);
      if (!$this.attr("data-target")) e.preventDefault();
      var $target = getTargetFromTrigger($this);
      var data = $target.data("bs.collapse");
      var option = data ? "toggle" : $this.data();
      Plugin.call($target, option);
    }
  );
})(jQuery);
+(function ($) {
  "use strict";
  var backdrop = ".dropdown-backdrop";
  var toggle = '[data-toggle="dropdown"]';
  var Dropdown = function (element) {
    $(element).on("click.bs.dropdown", this.toggle);
  };
  Dropdown.VERSION = "3.4.1";
  function getParent($this) {
    var selector = $this.attr("data-target");
    if (!selector) {
      selector = $this.attr("href");
      selector =
        selector &&
        /#[A-Za-z]/.test(selector) &&
        selector.replace(/.*(?=#[^\s]*$)/, "");
    }
    var $parent = selector !== "#" ? $(document).find(selector) : null;
    return $parent && $parent.length ? $parent : $this.parent();
  }
  function clearMenus(e) {
    if (e && e.which === 3) return;
    $(backdrop).remove();
    $(toggle).each(function () {
      var $this = $(this);
      var $parent = getParent($this);
      var relatedTarget = { relatedTarget: this };
      if (!$parent.hasClass("open")) return;
      if (
        e &&
        e.type == "click" &&
        /input|textarea/i.test(e.target.tagName) &&
        $.contains($parent[0], e.target)
      )
        return;
      $parent.trigger((e = $.Event("hide.bs.dropdown", relatedTarget)));
      if (e.isDefaultPrevented()) return;
      $this.attr("aria-expanded", "false");
      $parent
        .removeClass("open")
        .trigger($.Event("hidden.bs.dropdown", relatedTarget));
    });
  }
  Dropdown.prototype.toggle = function (e) {
    var $this = $(this);
    if ($this.is(".disabled, :disabled")) return;
    var $parent = getParent($this);
    var isActive = $parent.hasClass("open");
    clearMenus();
    if (!isActive) {
      if (
        "ontouchstart" in document.documentElement &&
        !$parent.closest(".navbar-nav").length
      )
        $(document.createElement("div"))
          .addClass("dropdown-backdrop")
          .insertAfter($(this))
          .on("click", clearMenus);
      var relatedTarget = { relatedTarget: this };
      $parent.trigger((e = $.Event("show.bs.dropdown", relatedTarget)));
      if (e.isDefaultPrevented()) return;
      $this.trigger("focus").attr("aria-expanded", "true");
      $parent
        .toggleClass("open")
        .trigger($.Event("shown.bs.dropdown", relatedTarget));
    }
    return false;
  };
  Dropdown.prototype.keydown = function (e) {
    if (
      !/(38|40|27|32)/.test(e.which) ||
      /input|textarea/i.test(e.target.tagName)
    )
      return;
    var $this = $(this);
    e.preventDefault();
    e.stopPropagation();
    if ($this.is(".disabled, :disabled")) return;
    var $parent = getParent($this);
    var isActive = $parent.hasClass("open");
    if ((!isActive && e.which != 27) || (isActive && e.which == 27)) {
      if (e.which == 27) $parent.find(toggle).trigger("focus");
      return $this.trigger("click");
    }
    var desc = " li:not(.disabled):visible a";
    var $items = $parent.find(".dropdown-menu" + desc);
    if (!$items.length) return;
    var index = $items.index(e.target);
    if (e.which == 38 && index > 0) index--;
    if (e.which == 40 && index < $items.length - 1) index++;
    if (!~index) index = 0;
    $items.eq(index).trigger("focus");
  };
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.dropdown");
      if (!data) $this.data("bs.dropdown", (data = new Dropdown(this)));
      if (typeof option == "string") data[option].call($this);
    });
  }
  var old = $.fn.dropdown;
  $.fn.dropdown = Plugin;
  $.fn.dropdown.Constructor = Dropdown;
  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old;
    return this;
  };
  $(document)
    .on("click.bs.dropdown.data-api", clearMenus)
    .on("click.bs.dropdown.data-api", ".dropdown form", function (e) {
      e.stopPropagation();
    })
    .on("click.bs.dropdown.data-api", toggle, Dropdown.prototype.toggle)
    .on("keydown.bs.dropdown.data-api", toggle, Dropdown.prototype.keydown)
    .on(
      "keydown.bs.dropdown.data-api",
      ".dropdown-menu",
      Dropdown.prototype.keydown
    );
})(jQuery);
+(function ($) {
  "use strict";
  var Modal = function (element, options) {
    this.options = options;
    this.$body = $(document.body);
    this.$element = $(element);
    this.$dialog = this.$element.find(".modal-dialog");
    this.$backdrop = null;
    this.isShown = null;
    this.originalBodyPad = null;
    this.scrollbarWidth = 0;
    this.ignoreBackdropClick = false;
    this.fixedContent = ".navbar-fixed-top, .navbar-fixed-bottom";
    if (this.options.remote)
      this.$element.find(".modal-content").load(
        this.options.remote,
        $.proxy(function () {
          this.$element.trigger("loaded.bs.modal");
        }, this)
      );
  };
  Modal.VERSION = "3.4.1";
  Modal.TRANSITION_DURATION = 300;
  Modal.BACKDROP_TRANSITION_DURATION = 150;
  Modal.DEFAULTS = { backdrop: true, keyboard: true, show: true };
  Modal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget);
  };
  Modal.prototype.show = function (_relatedTarget) {
    var that = this;
    var e = $.Event("show.bs.modal", { relatedTarget: _relatedTarget });
    this.$element.trigger(e);
    if (this.isShown || e.isDefaultPrevented()) return;
    this.isShown = true;
    this.checkScrollbar();
    this.setScrollbar();
    this.$body.addClass("modal-open");
    this.escape();
    this.resize();
    this.$element.on(
      "click.dismiss.bs.modal",
      '[data-dismiss="modal"]',
      $.proxy(this.hide, this)
    );
    this.$dialog.on("mousedown.dismiss.bs.modal", function () {
      that.$element.one("mouseup.dismiss.bs.modal", function (e) {
        if ($(e.target).is(that.$element)) that.ignoreBackdropClick = true;
      });
    });
    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass("fade");
      if (!that.$element.parent().length) that.$element.appendTo(that.$body);
      that.$element.show().scrollTop(0);
      that.adjustDialog();
      if (transition) that.$element[0].offsetWidth;
      that.$element.addClass("in");
      that.enforceFocus();
      var e = $.Event("shown.bs.modal", { relatedTarget: _relatedTarget });
      transition
        ? that.$dialog
            .one("bsTransitionEnd", function () {
              that.$element.trigger("focus").trigger(e);
            })
            .emulateTransitionEnd(Modal.TRANSITION_DURATION)
        : that.$element.trigger("focus").trigger(e);
    });
  };
  Modal.prototype.hide = function (e) {
    if (e) e.preventDefault();
    e = $.Event("hide.bs.modal");
    this.$element.trigger(e);
    if (!this.isShown || e.isDefaultPrevented()) return;
    this.isShown = false;
    this.escape();
    this.resize();
    $(document).off("focusin.bs.modal");
    this.$element
      .removeClass("in")
      .off("click.dismiss.bs.modal")
      .off("mouseup.dismiss.bs.modal");
    this.$dialog.off("mousedown.dismiss.bs.modal");
    $.support.transition && this.$element.hasClass("fade")
      ? this.$element
          .one("bsTransitionEnd", $.proxy(this.hideModal, this))
          .emulateTransitionEnd(Modal.TRANSITION_DURATION)
      : this.hideModal();
  };
  Modal.prototype.enforceFocus = function () {
    $(document)
      .off("focusin.bs.modal")
      .on(
        "focusin.bs.modal",
        $.proxy(function (e) {
          if (
            document !== e.target &&
            this.$element[0] !== e.target &&
            !this.$element.has(e.target).length
          )
            this.$element.trigger("focus");
        }, this)
      );
  };
  Modal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard)
      this.$element.on(
        "keydown.dismiss.bs.modal",
        $.proxy(function (e) {
          e.which == 27 && this.hide();
        }, this)
      );
    else {
      if (!this.isShown) this.$element.off("keydown.dismiss.bs.modal");
    }
  };
  Modal.prototype.resize = function () {
    if (this.isShown)
      $(window).on("resize.bs.modal", $.proxy(this.handleUpdate, this));
    else $(window).off("resize.bs.modal");
  };
  Modal.prototype.hideModal = function () {
    var that = this;
    this.$element.hide();
    this.backdrop(function () {
      that.$body.removeClass("modal-open");
      that.resetAdjustments();
      that.resetScrollbar();
      that.$element.trigger("hidden.bs.modal");
    });
  };
  Modal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove();
    this.$backdrop = null;
  };
  Modal.prototype.backdrop = function (callback) {
    var that = this;
    var animate = this.$element.hasClass("fade") ? "fade" : "";
    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate;
      this.$backdrop = $(document.createElement("div"))
        .addClass("modal-backdrop " + animate)
        .appendTo(this.$body);
      this.$element.on(
        "click.dismiss.bs.modal",
        $.proxy(function (e) {
          if (this.ignoreBackdropClick) {
            this.ignoreBackdropClick = false;
            return;
          }
          if (e.target !== e.currentTarget) return;
          this.options.backdrop == "static"
            ? this.$element[0].focus()
            : this.hide();
        }, this)
      );
      if (doAnimate) this.$backdrop[0].offsetWidth;
      this.$backdrop.addClass("in");
      if (!callback) return;
      doAnimate
        ? this.$backdrop
            .one("bsTransitionEnd", callback)
            .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION)
        : callback();
    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass("in");
      var callbackRemove = function () {
        that.removeBackdrop();
        callback && callback();
      };
      $.support.transition && this.$element.hasClass("fade")
        ? this.$backdrop
            .one("bsTransitionEnd", callbackRemove)
            .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION)
        : callbackRemove();
    } else {
      if (callback) callback();
    }
  };
  Modal.prototype.handleUpdate = function () {
    this.adjustDialog();
  };
  Modal.prototype.adjustDialog = function () {
    var modalIsOverflowing =
      this.$element[0].scrollHeight > document.documentElement.clientHeight;
    this.$element.css({
      paddingLeft:
        !this.bodyIsOverflowing && modalIsOverflowing
          ? this.scrollbarWidth
          : "",
      paddingRight:
        this.bodyIsOverflowing && !modalIsOverflowing
          ? this.scrollbarWidth
          : "",
    });
  };
  Modal.prototype.resetAdjustments = function () {
    this.$element.css({ paddingLeft: "", paddingRight: "" });
  };
  Modal.prototype.checkScrollbar = function () {
    var fullWindowWidth = window.innerWidth;
    if (!fullWindowWidth) {
      var documentElementRect =
        document.documentElement.getBoundingClientRect();
      fullWindowWidth =
        documentElementRect.right - Math.abs(documentElementRect.left);
    }
    this.bodyIsOverflowing = document.body.clientWidth < fullWindowWidth;
    this.scrollbarWidth = this.measureScrollbar();
  };
  Modal.prototype.setScrollbar = function () {
    var bodyPad = parseInt(this.$body.css("padding-right") || 0, 10);
    this.originalBodyPad = document.body.style.paddingRight || "";
    var scrollbarWidth = this.scrollbarWidth;
    if (this.bodyIsOverflowing) {
      this.$body.css("padding-right", bodyPad + scrollbarWidth);
      $(this.fixedContent).each(function (index, element) {
        var actualPadding = element.style.paddingRight;
        var calculatedPadding = $(element).css("padding-right");
        $(element)
          .data("padding-right", actualPadding)
          .css(
            "padding-right",
            parseFloat(calculatedPadding) + scrollbarWidth + "px"
          );
      });
    }
  };
  Modal.prototype.resetScrollbar = function () {
    this.$body.css("padding-right", this.originalBodyPad);
    $(this.fixedContent).each(function (index, element) {
      var padding = $(element).data("padding-right");
      $(element).removeData("padding-right");
      element.style.paddingRight = padding ? padding : "";
    });
  };
  Modal.prototype.measureScrollbar = function () {
    var scrollDiv = document.createElement("div");
    scrollDiv.className = "modal-scrollbar-measure";
    this.$body.append(scrollDiv);
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
    this.$body[0].removeChild(scrollDiv);
    return scrollbarWidth;
  };
  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.modal");
      var options = $.extend(
        {},
        Modal.DEFAULTS,
        $this.data(),
        typeof option == "object" && option
      );
      if (!data) $this.data("bs.modal", (data = new Modal(this, options)));
      if (typeof option == "string") data[option](_relatedTarget);
      else {
        if (options.show) data.show(_relatedTarget);
      }
    });
  }
  var old = $.fn.modal;
  $.fn.modal = Plugin;
  $.fn.modal.Constructor = Modal;
  $.fn.modal.noConflict = function () {
    $.fn.modal = old;
    return this;
  };
  $(document).on(
    "click.bs.modal.data-api",
    '[data-toggle="modal"]',
    function (e) {
      var $this = $(this);
      var href = $this.attr("href");
      var target =
        $this.attr("data-target") ||
        (href && href.replace(/.*(?=#[^\s]+$)/, ""));
      var $target = $(document).find(target);
      var option = $target.data("bs.modal")
        ? "toggle"
        : $.extend(
            { remote: !/#/.test(href) && href },
            $target.data(),
            $this.data()
          );
      if ($this.is("a")) e.preventDefault();
      $target.one("show.bs.modal", function (showEvent) {
        if (showEvent.isDefaultPrevented()) return;
        $target.one("hidden.bs.modal", function () {
          $this.is(":visible") && $this.trigger("focus");
        });
      });
      Plugin.call($target, option, this);
    }
  );
})(jQuery);
+(function ($) {
  "use strict";
  var DISALLOWED_ATTRIBUTES = ["sanitize", "whiteList", "sanitizeFn"];
  var uriAttrs = [
    "background",
    "cite",
    "href",
    "itemtype",
    "longdesc",
    "poster",
    "src",
    "xlink:href",
  ];
  var ARIA_ATTRIBUTE_PATTERN = /^aria-[\w-]*$/i;
  var DefaultWhitelist = {
    "*": ["class", "dir", "id", "lang", "role", ARIA_ATTRIBUTE_PATTERN],
    a: ["target", "href", "title", "rel"],
    area: [],
    b: [],
    br: [],
    col: [],
    code: [],
    div: [],
    em: [],
    hr: [],
    h1: [],
    h2: [],
    h3: [],
    h4: [],
    h5: [],
    h6: [],
    i: [],
    img: ["src", "alt", "title", "width", "height"],
    li: [],
    ol: [],
    p: [],
    pre: [],
    s: [],
    small: [],
    span: [],
    sub: [],
    sup: [],
    strong: [],
    u: [],
    ul: [],
  };
  var SAFE_URL_PATTERN =
    /^(?:(?:https?|mailto|ftp|tel|file):|[^&:/?#]*(?:[/?#]|$))/gi;
  var DATA_URL_PATTERN =
    /^data:(?:image\/(?:bmp|gif|jpeg|jpg|png|tiff|webp)|video\/(?:mpeg|mp4|ogg|webm)|audio\/(?:mp3|oga|ogg|opus));base64,[a-z0-9+/]+=*$/i;
  function allowedAttribute(attr, allowedAttributeList) {
    var attrName = attr.nodeName.toLowerCase();
    if ($.inArray(attrName, allowedAttributeList) !== -1) {
      if ($.inArray(attrName, uriAttrs) !== -1)
        return Boolean(
          attr.nodeValue.match(SAFE_URL_PATTERN) ||
            attr.nodeValue.match(DATA_URL_PATTERN)
        );
      return true;
    }
    var regExp = $(allowedAttributeList).filter(function (index, value) {
      return value instanceof RegExp;
    });
    for (var i = 0, l = regExp.length; i < l; i++)
      if (attrName.match(regExp[i])) return true;
    return false;
  }
  function sanitizeHtml(unsafeHtml, whiteList, sanitizeFn) {
    if (unsafeHtml.length === 0) return unsafeHtml;
    if (sanitizeFn && typeof sanitizeFn === "function")
      return sanitizeFn(unsafeHtml);
    if (!document.implementation || !document.implementation.createHTMLDocument)
      return unsafeHtml;
    var createdDocument =
      document.implementation.createHTMLDocument("sanitization");
    createdDocument.body.innerHTML = unsafeHtml;
    var whitelistKeys = $.map(whiteList, function (el, i) {
      return i;
    });
    var elements = $(createdDocument.body).find("*");
    for (var i = 0, len = elements.length; i < len; i++) {
      var el = elements[i];
      var elName = el.nodeName.toLowerCase();
      if ($.inArray(elName, whitelistKeys) === -1) {
        el.parentNode.removeChild(el);
        continue;
      }
      var attributeList = $.map(el.attributes, function (el) {
        return el;
      });
      var whitelistedAttributes = [].concat(
        whiteList["*"] || [],
        whiteList[elName] || []
      );
      for (var j = 0, len2 = attributeList.length; j < len2; j++)
        if (!allowedAttribute(attributeList[j], whitelistedAttributes))
          el.removeAttribute(attributeList[j].nodeName);
    }
    return createdDocument.body.innerHTML;
  }
  var Tooltip = function (element, options) {
    this.type = null;
    this.options = null;
    this.enabled = null;
    this.timeout = null;
    this.hoverState = null;
    this.$element = null;
    this.inState = null;
    this.init("tooltip", element, options);
  };
  Tooltip.VERSION = "3.4.1";
  Tooltip.TRANSITION_DURATION = 150;
  Tooltip.DEFAULTS = {
    animation: true,
    placement: "top",
    selector: false,
    template:
      '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
    trigger: "hover focus",
    title: "",
    delay: 0,
    html: false,
    container: false,
    viewport: { selector: "body", padding: 0 },
    sanitize: true,
    sanitizeFn: null,
    whiteList: DefaultWhitelist,
  };
  Tooltip.prototype.init = function (type, element, options) {
    this.enabled = true;
    this.type = type;
    this.$element = $(element);
    this.options = this.getOptions(options);
    this.$viewport =
      this.options.viewport &&
      $(document).find(
        $.isFunction(this.options.viewport)
          ? this.options.viewport.call(this, this.$element)
          : this.options.viewport.selector || this.options.viewport
      );
    this.inState = { click: false, hover: false, focus: false };
    if (
      this.$element[0] instanceof document.constructor &&
      !this.options.selector
    )
      throw new Error(
        "`selector` option must be specified when initializing " +
          this.type +
          " on the window.document object!"
      );
    var triggers = this.options.trigger.split(" ");
    for (var i = triggers.length; i--; ) {
      var trigger = triggers[i];
      if (trigger == "click")
        this.$element.on(
          "click." + this.type,
          this.options.selector,
          $.proxy(this.toggle, this)
        );
      else {
        if (trigger != "manual") {
          var eventIn = trigger == "hover" ? "mouseenter" : "focusin";
          var eventOut = trigger == "hover" ? "mouseleave" : "focusout";
          this.$element.on(
            eventIn + "." + this.type,
            this.options.selector,
            $.proxy(this.enter, this)
          );
          this.$element.on(
            eventOut + "." + this.type,
            this.options.selector,
            $.proxy(this.leave, this)
          );
        }
      }
    }
    this.options.selector
      ? (this._options = $.extend({}, this.options, {
          trigger: "manual",
          selector: "",
        }))
      : this.fixTitle();
  };
  Tooltip.prototype.getDefaults = function () {
    return Tooltip.DEFAULTS;
  };
  Tooltip.prototype.getOptions = function (options) {
    var dataAttributes = this.$element.data();
    for (var dataAttr in dataAttributes)
      if (
        dataAttributes.hasOwnProperty(dataAttr) &&
        $.inArray(dataAttr, DISALLOWED_ATTRIBUTES) !== -1
      )
        delete dataAttributes[dataAttr];
    options = $.extend({}, this.getDefaults(), dataAttributes, options);
    if (options.delay && typeof options.delay == "number")
      options.delay = { show: options.delay, hide: options.delay };
    if (options.sanitize)
      options.template = sanitizeHtml(
        options.template,
        options.whiteList,
        options.sanitizeFn
      );
    return options;
  };
  Tooltip.prototype.getDelegateOptions = function () {
    var options = {};
    var defaults = this.getDefaults();
    this._options &&
      $.each(this._options, function (key, value) {
        if (defaults[key] != value) options[key] = value;
      });
    return options;
  };
  Tooltip.prototype.enter = function (obj) {
    var self =
      obj instanceof this.constructor
        ? obj
        : $(obj.currentTarget).data("bs." + this.type);
    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions());
      $(obj.currentTarget).data("bs." + this.type, self);
    }
    if (obj instanceof $.Event)
      self.inState[obj.type == "focusin" ? "focus" : "hover"] = true;
    if (self.tip().hasClass("in") || self.hoverState == "in") {
      self.hoverState = "in";
      return;
    }
    clearTimeout(self.timeout);
    self.hoverState = "in";
    if (!self.options.delay || !self.options.delay.show) return self.show();
    self.timeout = setTimeout(function () {
      if (self.hoverState == "in") self.show();
    }, self.options.delay.show);
  };
  Tooltip.prototype.isInStateTrue = function () {
    for (var key in this.inState) if (this.inState[key]) return true;
    return false;
  };
  Tooltip.prototype.leave = function (obj) {
    var self =
      obj instanceof this.constructor
        ? obj
        : $(obj.currentTarget).data("bs." + this.type);
    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions());
      $(obj.currentTarget).data("bs." + this.type, self);
    }
    if (obj instanceof $.Event)
      self.inState[obj.type == "focusout" ? "focus" : "hover"] = false;
    if (self.isInStateTrue()) return;
    clearTimeout(self.timeout);
    self.hoverState = "out";
    if (!self.options.delay || !self.options.delay.hide) return self.hide();
    self.timeout = setTimeout(function () {
      if (self.hoverState == "out") self.hide();
    }, self.options.delay.hide);
  };
  Tooltip.prototype.show = function () {
    var e = $.Event("show.bs." + this.type);
    if (this.hasContent() && this.enabled) {
      this.$element.trigger(e);
      var inDom = $.contains(
        this.$element[0].ownerDocument.documentElement,
        this.$element[0]
      );
      if (e.isDefaultPrevented() || !inDom) return;
      var that = this;
      var $tip = this.tip();
      var tipId = this.getUID(this.type);
      this.setContent();
      $tip.attr("id", tipId);
      this.$element.attr("aria-describedby", tipId);
      if (this.options.animation) $tip.addClass("fade");
      var placement =
        typeof this.options.placement == "function"
          ? this.options.placement.call(this, $tip[0], this.$element[0])
          : this.options.placement;
      var autoToken = /\s?auto?\s?/i;
      var autoPlace = autoToken.test(placement);
      if (autoPlace) placement = placement.replace(autoToken, "") || "top";
      $tip
        .detach()
        .css({ top: 0, left: 0, display: "block" })
        .addClass(placement)
        .data("bs." + this.type, this);
      this.options.container
        ? $tip.appendTo($(document).find(this.options.container))
        : $tip.insertAfter(this.$element);
      this.$element.trigger("inserted.bs." + this.type);
      var pos = this.getPosition();
      var actualWidth = $tip[0].offsetWidth;
      var actualHeight = $tip[0].offsetHeight;
      if (autoPlace) {
        var orgPlacement = placement;
        var viewportDim = this.getPosition(this.$viewport);
        placement =
          placement == "bottom" &&
          pos.bottom + actualHeight > viewportDim.bottom
            ? "top"
            : placement == "top" && pos.top - actualHeight < viewportDim.top
            ? "bottom"
            : placement == "right" &&
              pos.right + actualWidth > viewportDim.width
            ? "left"
            : placement == "left" && pos.left - actualWidth < viewportDim.left
            ? "right"
            : placement;
        $tip.removeClass(orgPlacement).addClass(placement);
      }
      var calculatedOffset = this.getCalculatedOffset(
        placement,
        pos,
        actualWidth,
        actualHeight
      );
      this.applyPlacement(calculatedOffset, placement);
      var complete = function () {
        var prevHoverState = that.hoverState;
        that.$element.trigger("shown.bs." + that.type);
        that.hoverState = null;
        if (prevHoverState == "out") that.leave(that);
      };
      $.support.transition && this.$tip.hasClass("fade")
        ? $tip
            .one("bsTransitionEnd", complete)
            .emulateTransitionEnd(Tooltip.TRANSITION_DURATION)
        : complete();
    }
  };
  Tooltip.prototype.applyPlacement = function (offset, placement) {
    var $tip = this.tip();
    var width = $tip[0].offsetWidth;
    var height = $tip[0].offsetHeight;
    var marginTop = parseInt($tip.css("margin-top"), 10);
    var marginLeft = parseInt($tip.css("margin-left"), 10);
    if (isNaN(marginTop)) marginTop = 0;
    if (isNaN(marginLeft)) marginLeft = 0;
    offset.top += marginTop;
    offset.left += marginLeft;
    $.offset.setOffset(
      $tip[0],
      $.extend(
        {
          using: function (props) {
            $tip.css({
              top: Math.round(props.top),
              left: Math.round(props.left),
            });
          },
        },
        offset
      ),
      0
    );
    $tip.addClass("in");
    var actualWidth = $tip[0].offsetWidth;
    var actualHeight = $tip[0].offsetHeight;
    if (placement == "top" && actualHeight != height)
      offset.top = offset.top + height - actualHeight;
    var delta = this.getViewportAdjustedDelta(
      placement,
      offset,
      actualWidth,
      actualHeight
    );
    if (delta.left) offset.left += delta.left;
    else offset.top += delta.top;
    var isVertical = /top|bottom/.test(placement);
    var arrowDelta = isVertical
      ? delta.left * 2 - width + actualWidth
      : delta.top * 2 - height + actualHeight;
    var arrowOffsetPosition = isVertical ? "offsetWidth" : "offsetHeight";
    $tip.offset(offset);
    this.replaceArrow(arrowDelta, $tip[0][arrowOffsetPosition], isVertical);
  };
  Tooltip.prototype.replaceArrow = function (delta, dimension, isVertical) {
    this.arrow()
      .css(isVertical ? "left" : "top", 50 * (1 - delta / dimension) + "%")
      .css(isVertical ? "top" : "left", "");
  };
  Tooltip.prototype.setContent = function () {
    var $tip = this.tip();
    var title = this.getTitle();
    if (this.options.html) {
      if (this.options.sanitize)
        title = sanitizeHtml(
          title,
          this.options.whiteList,
          this.options.sanitizeFn
        );
      $tip.find(".tooltip-inner").html(title);
    } else $tip.find(".tooltip-inner").text(title);
    $tip.removeClass("fade in top bottom left right");
  };
  Tooltip.prototype.hide = function (callback) {
    var that = this;
    var $tip = $(this.$tip);
    var e = $.Event("hide.bs." + this.type);
    function complete() {
      if (that.hoverState != "in") $tip.detach();
      if (that.$element)
        that.$element
          .removeAttr("aria-describedby")
          .trigger("hidden.bs." + that.type);
      callback && callback();
    }
    this.$element.trigger(e);
    if (e.isDefaultPrevented()) return;
    $tip.removeClass("in");
    $.support.transition && $tip.hasClass("fade")
      ? $tip
          .one("bsTransitionEnd", complete)
          .emulateTransitionEnd(Tooltip.TRANSITION_DURATION)
      : complete();
    this.hoverState = null;
    return this;
  };
  Tooltip.prototype.fixTitle = function () {
    var $e = this.$element;
    if ($e.attr("title") || typeof $e.attr("data-original-title") != "string")
      $e.attr("data-original-title", $e.attr("title") || "").attr("title", "");
  };
  Tooltip.prototype.hasContent = function () {
    return this.getTitle();
  };
  Tooltip.prototype.getPosition = function ($element) {
    $element = $element || this.$element;
    var el = $element[0];
    var isBody = el.tagName == "BODY";
    var elRect = el.getBoundingClientRect();
    if (elRect.width == null)
      elRect = $.extend({}, elRect, {
        width: elRect.right - elRect.left,
        height: elRect.bottom - elRect.top,
      });
    var isSvg = window.SVGElement && el instanceof window.SVGElement;
    var elOffset = isBody
      ? { top: 0, left: 0 }
      : isSvg
      ? null
      : $element.offset();
    var scroll = {
      scroll: isBody
        ? document.documentElement.scrollTop || document.body.scrollTop
        : $element.scrollTop(),
    };
    var outerDims = isBody
      ? { width: $(window).width(), height: $(window).height() }
      : null;
    return $.extend({}, elRect, scroll, outerDims, elOffset);
  };
  Tooltip.prototype.getCalculatedOffset = function (
    placement,
    pos,
    actualWidth,
    actualHeight
  ) {
    return placement == "bottom"
      ? {
          top: pos.top + pos.height,
          left: pos.left + pos.width / 2 - actualWidth / 2,
        }
      : placement == "top"
      ? {
          top: pos.top - actualHeight,
          left: pos.left + pos.width / 2 - actualWidth / 2,
        }
      : placement == "left"
      ? {
          top: pos.top + pos.height / 2 - actualHeight / 2,
          left: pos.left - actualWidth,
        }
      : {
          top: pos.top + pos.height / 2 - actualHeight / 2,
          left: pos.left + pos.width,
        };
  };
  Tooltip.prototype.getViewportAdjustedDelta = function (
    placement,
    pos,
    actualWidth,
    actualHeight
  ) {
    var delta = { top: 0, left: 0 };
    if (!this.$viewport) return delta;
    var viewportPadding =
      (this.options.viewport && this.options.viewport.padding) || 0;
    var viewportDimensions = this.getPosition(this.$viewport);
    if (/right|left/.test(placement)) {
      var topEdgeOffset = pos.top - viewportPadding - viewportDimensions.scroll;
      var bottomEdgeOffset =
        pos.top + viewportPadding - viewportDimensions.scroll + actualHeight;
      if (topEdgeOffset < viewportDimensions.top)
        delta.top = viewportDimensions.top - topEdgeOffset;
      else {
        if (
          bottomEdgeOffset >
          viewportDimensions.top + viewportDimensions.height
        )
          delta.top =
            viewportDimensions.top +
            viewportDimensions.height -
            bottomEdgeOffset;
      }
    } else {
      var leftEdgeOffset = pos.left - viewportPadding;
      var rightEdgeOffset = pos.left + viewportPadding + actualWidth;
      if (leftEdgeOffset < viewportDimensions.left)
        delta.left = viewportDimensions.left - leftEdgeOffset;
      else {
        if (rightEdgeOffset > viewportDimensions.right)
          delta.left =
            viewportDimensions.left +
            viewportDimensions.width -
            rightEdgeOffset;
      }
    }
    return delta;
  };
  Tooltip.prototype.getTitle = function () {
    var title;
    var $e = this.$element;
    var o = this.options;
    title =
      $e.attr("data-original-title") ||
      (typeof o.title == "function" ? o.title.call($e[0]) : o.title);
    return title;
  };
  Tooltip.prototype.getUID = function (prefix) {
    do prefix += ~~(Math.random() * 1000000);
    while (document.getElementById(prefix));
    return prefix;
  };
  Tooltip.prototype.tip = function () {
    if (!this.$tip) {
      this.$tip = $(this.options.template);
      if (this.$tip.length != 1)
        throw new Error(
          this.type +
            " `template` option must consist of exactly 1 top-level element!"
        );
    }
    return this.$tip;
  };
  Tooltip.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find(".tooltip-arrow"));
  };
  Tooltip.prototype.enable = function () {
    this.enabled = true;
  };
  Tooltip.prototype.disable = function () {
    this.enabled = false;
  };
  Tooltip.prototype.toggleEnabled = function () {
    this.enabled = !this.enabled;
  };
  Tooltip.prototype.toggle = function (e) {
    var self = this;
    if (e) {
      self = $(e.currentTarget).data("bs." + this.type);
      if (!self) {
        self = new this.constructor(e.currentTarget, this.getDelegateOptions());
        $(e.currentTarget).data("bs." + this.type, self);
      }
    }
    if (e) {
      self.inState.click = !self.inState.click;
      if (self.isInStateTrue()) self.enter(self);
      else self.leave(self);
    } else self.tip().hasClass("in") ? self.leave(self) : self.enter(self);
  };
  Tooltip.prototype.destroy = function () {
    var that = this;
    clearTimeout(this.timeout);
    this.hide(function () {
      that.$element.off("." + that.type).removeData("bs." + that.type);
      if (that.$tip) that.$tip.detach();
      that.$tip = null;
      that.$arrow = null;
      that.$viewport = null;
      that.$element = null;
    });
  };
  Tooltip.prototype.sanitizeHtml = function (unsafeHtml) {
    return sanitizeHtml(
      unsafeHtml,
      this.options.whiteList,
      this.options.sanitizeFn
    );
  };
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.tooltip");
      var options = typeof option == "object" && option;
      if (!data && /destroy|hide/.test(option)) return;
      if (!data) $this.data("bs.tooltip", (data = new Tooltip(this, options)));
      if (typeof option == "string") data[option]();
    });
  }
  var old = $.fn.tooltip;
  $.fn.tooltip = Plugin;
  $.fn.tooltip.Constructor = Tooltip;
  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old;
    return this;
  };
})(jQuery);
+(function ($) {
  "use strict";
  var Popover = function (element, options) {
    this.init("popover", element, options);
  };
  if (!$.fn.tooltip) throw new Error("Popover requires tooltip.js");
  Popover.VERSION = "3.4.1";
  Popover.DEFAULTS = $.extend({}, $.fn.tooltip.Constructor.DEFAULTS, {
    placement: "right",
    trigger: "click",
    content: "",
    template:
      '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
  });
  Popover.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype);
  Popover.prototype.constructor = Popover;
  Popover.prototype.getDefaults = function () {
    return Popover.DEFAULTS;
  };
  Popover.prototype.setContent = function () {
    var $tip = this.tip();
    var title = this.getTitle();
    var content = this.getContent();
    if (this.options.html) {
      var typeContent = typeof content;
      if (this.options.sanitize) {
        title = this.sanitizeHtml(title);
        if (typeContent === "string") content = this.sanitizeHtml(content);
      }
      $tip.find(".popover-title").html(title);
      $tip
        .find(".popover-content")
        .children()
        .detach()
        .end()
        [typeContent === "string" ? "html" : "append"](content);
    } else {
      $tip.find(".popover-title").text(title);
      $tip.find(".popover-content").children().detach().end().text(content);
    }
    $tip.removeClass("fade top bottom left right in");
    if (!$tip.find(".popover-title").html()) $tip.find(".popover-title").hide();
  };
  Popover.prototype.hasContent = function () {
    return this.getTitle() || this.getContent();
  };
  Popover.prototype.getContent = function () {
    var $e = this.$element;
    var o = this.options;
    return (
      $e.attr("data-content") ||
      (typeof o.content == "function" ? o.content.call($e[0]) : o.content)
    );
  };
  Popover.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find(".arrow"));
  };
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.popover");
      var options = typeof option == "object" && option;
      if (!data && /destroy|hide/.test(option)) return;
      if (!data) $this.data("bs.popover", (data = new Popover(this, options)));
      if (typeof option == "string") data[option]();
    });
  }
  var old = $.fn.popover;
  $.fn.popover = Plugin;
  $.fn.popover.Constructor = Popover;
  $.fn.popover.noConflict = function () {
    $.fn.popover = old;
    return this;
  };
})(jQuery);
+(function ($) {
  "use strict";
  function ScrollSpy(element, options) {
    this.$body = $(document.body);
    this.$scrollElement = $(element).is(document.body) ? $(window) : $(element);
    this.options = $.extend({}, ScrollSpy.DEFAULTS, options);
    this.selector = (this.options.target || "") + " .nav li > a";
    this.offsets = [];
    this.targets = [];
    this.activeTarget = null;
    this.scrollHeight = 0;
    this.$scrollElement.on("scroll.bs.scrollspy", $.proxy(this.process, this));
    this.refresh();
    this.process();
  }
  ScrollSpy.VERSION = "3.4.1";
  ScrollSpy.DEFAULTS = { offset: 10 };
  ScrollSpy.prototype.getScrollHeight = function () {
    return (
      this.$scrollElement[0].scrollHeight ||
      Math.max(
        this.$body[0].scrollHeight,
        document.documentElement.scrollHeight
      )
    );
  };
  ScrollSpy.prototype.refresh = function () {
    var that = this;
    var offsetMethod = "offset";
    var offsetBase = 0;
    this.offsets = [];
    this.targets = [];
    this.scrollHeight = this.getScrollHeight();
    if (!$.isWindow(this.$scrollElement[0])) {
      offsetMethod = "position";
      offsetBase = this.$scrollElement.scrollTop();
    }
    this.$body
      .find(this.selector)
      .map(function () {
        var $el = $(this);
        var href = $el.data("target") || $el.attr("href");
        var $href = /^#./.test(href) && $(href);
        return (
          ($href &&
            $href.length &&
            $href.is(":visible") && [
              [$href[offsetMethod]().top + offsetBase, href],
            ]) ||
          null
        );
      })
      .sort(function (a, b) {
        return a[0] - b[0];
      })
      .each(function () {
        that.offsets.push(this[0]);
        that.targets.push(this[1]);
      });
  };
  ScrollSpy.prototype.process = function () {
    var scrollTop = this.$scrollElement.scrollTop() + this.options.offset;
    var scrollHeight = this.getScrollHeight();
    var maxScroll =
      this.options.offset + scrollHeight - this.$scrollElement.height();
    var offsets = this.offsets;
    var targets = this.targets;
    var activeTarget = this.activeTarget;
    var i;
    if (this.scrollHeight != scrollHeight) this.refresh();
    if (scrollTop >= maxScroll)
      return (
        activeTarget != (i = targets[targets.length - 1]) && this.activate(i)
      );
    if (activeTarget && scrollTop < offsets[0]) {
      this.activeTarget = null;
      return this.clear();
    }
    for (i = offsets.length; i--; )
      activeTarget != targets[i] &&
        scrollTop >= offsets[i] &&
        (offsets[i + 1] === undefined || scrollTop < offsets[i + 1]) &&
        this.activate(targets[i]);
  };
  ScrollSpy.prototype.activate = function (target) {
    this.activeTarget = target;
    this.clear();
    var selector =
      this.selector +
      '[data-target="' +
      target +
      '"],' +
      this.selector +
      '[href="' +
      target +
      '"]';
    var active = $(selector).parents("li").addClass("active");
    if (active.parent(".dropdown-menu").length)
      active = active.closest("li.dropdown").addClass("active");
    active.trigger("activate.bs.scrollspy");
  };
  ScrollSpy.prototype.clear = function () {
    $(this.selector)
      .parentsUntil(this.options.target, ".active")
      .removeClass("active");
  };
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.scrollspy");
      var options = typeof option == "object" && option;
      if (!data)
        $this.data("bs.scrollspy", (data = new ScrollSpy(this, options)));
      if (typeof option == "string") data[option]();
    });
  }
  var old = $.fn.scrollspy;
  $.fn.scrollspy = Plugin;
  $.fn.scrollspy.Constructor = ScrollSpy;
  $.fn.scrollspy.noConflict = function () {
    $.fn.scrollspy = old;
    return this;
  };
  $(window).on("load.bs.scrollspy.data-api", function () {
    $('[data-spy="scroll"]').each(function () {
      var $spy = $(this);
      Plugin.call($spy, $spy.data());
    });
  });
})(jQuery);
+(function ($) {
  "use strict";
  var Tab = function (element) {
    this.element = $(element);
  };
  Tab.VERSION = "3.4.1";
  Tab.TRANSITION_DURATION = 150;
  Tab.prototype.show = function () {
    var $this = this.element;
    var $ul = $this.closest("ul:not(.dropdown-menu)");
    var selector = $this.data("target");
    if (!selector) {
      selector = $this.attr("href");
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, "");
    }
    if ($this.parent("li").hasClass("active")) return;
    var $previous = $ul.find(".active:last a");
    var hideEvent = $.Event("hide.bs.tab", { relatedTarget: $this[0] });
    var showEvent = $.Event("show.bs.tab", { relatedTarget: $previous[0] });
    $previous.trigger(hideEvent);
    $this.trigger(showEvent);
    if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented())
      return;
    var $target = $(document).find(selector);
    this.activate($this.closest("li"), $ul);
    this.activate($target, $target.parent(), function () {
      $previous.trigger({ type: "hidden.bs.tab", relatedTarget: $this[0] });
      $this.trigger({ type: "shown.bs.tab", relatedTarget: $previous[0] });
    });
  };
  Tab.prototype.activate = function (element, container, callback) {
    var $active = container.find("> .active");
    var transition =
      callback &&
      $.support.transition &&
      (($active.length && $active.hasClass("fade")) ||
        !!container.find("> .fade").length);
    function next() {
      $active
        .removeClass("active")
        .find("> .dropdown-menu > .active")
        .removeClass("active")
        .end()
        .find('[data-toggle="tab"]')
        .attr("aria-expanded", false);
      element
        .addClass("active")
        .find('[data-toggle="tab"]')
        .attr("aria-expanded", true);
      if (transition) {
        element[0].offsetWidth;
        element.addClass("in");
      } else element.removeClass("fade");
      if (element.parent(".dropdown-menu").length)
        element
          .closest("li.dropdown")
          .addClass("active")
          .end()
          .find('[data-toggle="tab"]')
          .attr("aria-expanded", true);
      callback && callback();
    }
    $active.length && transition
      ? $active
          .one("bsTransitionEnd", next)
          .emulateTransitionEnd(Tab.TRANSITION_DURATION)
      : next();
    $active.removeClass("in");
  };
  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data("bs.tab");
      if (!data) $this.data("bs.tab", (data = new Tab(this)));
      if (typeof option == "string") data[option]();
    });
  }
  var old = $.fn.tab;
  $.fn.tab = Plugin;
  $.fn.tab.Constructor = Tab;
  $.fn.tab.noConflict = function () {
    $.fn.tab = old;
    return this;
  };
  var clickHandler = function (e) {
    e.preventDefault();
    Plugin.call($(this), "show");
  };
  $(document)
    .on("click.bs.tab.data-api", '[data-toggle="tab"]', clickHandler)
    .on("click.bs.tab.data-api", '[data-toggle="pill"]', clickHandler);
})(jQuery);
+(function ($) {
  "use strict";
  function transitionEnd() {
    var el = document.createElement("bootstrap");
    var transEndEventNames = {
      WebkitTransition: "webkitTransitionEnd",
      MozTransition: "transitionend",
      OTransition: "oTransitionEnd otransitionend",
      transition: "transitionend",
    };
    for (var name in transEndEventNames)
      if (el.style[name] !== undefined)
        return { end: transEndEventNames[name] };
    return false;
  }
  $.fn.emulateTransitionEnd = function (duration) {
    var called = false;
    var $el = this;
    $(this).one("bsTransitionEnd", function () {
      called = true;
    });
    var callback = function () {
      if (!called) $($el).trigger($.support.transition.end);
    };
    setTimeout(callback, duration);
    return this;
  };
  $(function () {
    $.support.transition = transitionEnd();
    if (!$.support.transition) return;
    $.event.special.bsTransitionEnd = {
      bindType: $.support.transition.end,
      delegateType: $.support.transition.end,
      handle: function (e) {
        if ($(e.target).is(this))
          return e.handleObj.handler.apply(this, arguments);
      },
    };
  });
})(jQuery);
(function (_, $, Drupal, drupalSettings) {
  "use strict";
  var Bootstrap = {
    processedOnce: {},
    settings: drupalSettings.bootstrap || {},
  };
  Bootstrap.checkPlain = function (str) {
    return (str && Drupal.checkPlain(str)) || "";
  };
  Bootstrap.createPlugin = function (id, plugin, noConflict) {
    if ($.fn[id] !== void 0)
      return this.fatal(
        "Specified jQuery plugin identifier already exists: @id. Use Drupal.bootstrap.replacePlugin() instead.",
        { "@id": id }
      );
    if (typeof plugin !== "function")
      return this.fatal(
        'You must provide a constructor function to create a jQuery plugin "@id": @plugin',
        { "@id": id, "@plugin": plugin }
      );
    this.pluginNoConflict(id, plugin, noConflict);
    $.fn[id] = plugin;
  };
  Bootstrap.diffObjects = function (objects) {
    var args = Array.prototype.slice.call(arguments);
    return _.pick(
      args[0],
      _.difference.apply(
        _,
        _.map(args, function (obj) {
          return Object.keys(obj);
        })
      )
    );
  };
  Bootstrap.eventMap = {
    Event:
      /^(?:load|unload|abort|error|select|change|submit|reset|focus|blur|resize|scroll)$/,
    MouseEvent:
      /^(?:click|dblclick|mouse(?:down|enter|leave|up|over|move|out))$/,
    KeyboardEvent: /^(?:key(?:down|press|up))$/,
    TouchEvent: /^(?:touch(?:start|end|move|cancel))$/,
  };
  Bootstrap.extendPlugin = function (id, callback) {
    if (typeof $.fn[id] !== "function")
      return this.fatal(
        "Specified jQuery plugin identifier does not exist: @id",
        { "@id": id }
      );
    if (typeof callback !== "function")
      return this.fatal(
        'You must provide a callback function to extend the jQuery plugin "@id": @callback',
        { "@id": id, "@callback": callback }
      );
    var constructor = ($.fn[id] && $.fn[id].Constructor) || $.fn[id];
    var plugin = callback.apply(constructor, [this.settings]);
    if (!$.isPlainObject(plugin))
      return this.fatal(
        'Returned value from callback is not a plain object that can be used to extend the jQuery plugin "@id": @obj',
        { "@obj": plugin }
      );
    this.wrapPluginConstructor(constructor, plugin, true);
    return $.fn[id];
  };
  Bootstrap.superWrapper = function (parent, fn) {
    return function () {
      var previousSuper = this.super;
      this.super = parent;
      var ret = fn.apply(this, arguments);
      if (previousSuper) this.super = previousSuper;
      else delete this.super;
      return ret;
    };
  };
  Bootstrap.fatal = function (message, args) {
    if (this.settings.dev && console.warn) {
      for (var name in args)
        if (args.hasOwnProperty(name) && typeof args[name] === "object")
          args[name] = JSON.stringify(args[name]);
      Drupal.throwError(new Error(Drupal.formatString(message, args)));
    }
    return false;
  };
  Bootstrap.intersectObjects = function (objects) {
    var args = Array.prototype.slice.call(arguments);
    return _.pick(
      args[0],
      _.intersection.apply(
        _,
        _.map(args, function (obj) {
          return Object.keys(obj);
        })
      )
    );
  };
  Bootstrap.normalizeObject = function (obj) {
    if (!$.isPlainObject(obj)) return obj;
    for (var k in obj)
      if (typeof obj[k] === "string")
        if (obj[k] === "true") obj[k] = true;
        else if (obj[k] === "false") obj[k] = false;
        else {
          if (obj[k].match(/^[\d-.]$/)) obj[k] = parseFloat(obj[k]);
        }
      else {
        if ($.isPlainObject(obj[k])) obj[k] = Bootstrap.normalizeObject(obj[k]);
      }
    return obj;
  };
  Bootstrap.once = function (id, callback) {
    if (this.processedOnce[id]) return this;
    callback.call(this, this.settings);
    this.processedOnce[id] = true;
    return this;
  };
  Bootstrap.option = function (key, value) {
    var options = $.isPlainObject(key) ? $.extend({}, key) : {};
    if (arguments.length === 0) return $.extend({}, this.options);
    if (typeof key === "string") {
      var parts = key.split(".");
      key = parts.shift();
      var obj = options;
      if (parts.length) {
        for (var i = 0; i < parts.length - 1; i++) {
          obj[parts[i]] = obj[parts[i]] || {};
          obj = obj[parts[i]];
        }
        key = parts.pop();
      }
      if (arguments.length === 1) return obj[key] === void 0 ? null : obj[key];
      obj[key] = value;
    }
    $.extend(true, this.options, options);
  };
  Bootstrap.pluginNoConflict = function (id, plugin, noConflict) {
    if (plugin.noConflict === void 0 && (noConflict === void 0 || noConflict)) {
      var old = $.fn[id];
      plugin.noConflict = function () {
        $.fn[id] = old;
        return this;
      };
    }
  };
  Bootstrap.relayEvent = function (target, name, stopPropagation) {
    return function (e) {
      if (stopPropagation === void 0 || stopPropagation) e.stopPropagation();
      var $target = $(target);
      var parts = name.split(".").filter(Boolean);
      var type = parts.shift();
      e.target = $target[0];
      e.currentTarget = $target[0];
      e.namespace = parts.join(".");
      e.type = type;
      $target.trigger(e);
    };
  };
  Bootstrap.replacePlugin = function (id, callback, noConflict) {
    if (typeof $.fn[id] !== "function")
      return this.fatal(
        "Specified jQuery plugin identifier does not exist: @id",
        { "@id": id }
      );
    if (typeof callback !== "function")
      return this.fatal(
        "You must provide a valid callback function to replace a jQuery plugin: @callback",
        { "@callback": callback }
      );
    var constructor = ($.fn[id] && $.fn[id].Constructor) || $.fn[id];
    var plugin = callback.apply(constructor, [this.settings]);
    if (typeof plugin !== "function")
      return this.fatal(
        'Returned value from callback is not a usable function to replace a jQuery plugin "@id": @plugin',
        { "@id": id, "@plugin": plugin }
      );
    this.wrapPluginConstructor(constructor, plugin);
    this.pluginNoConflict(id, plugin, noConflict);
    $.fn[id] = plugin;
  };
  Bootstrap.simulate = function (element, type, options) {
    var ret = true;
    if (element instanceof $) {
      element.each(function () {
        if (!Bootstrap.simulate(this, type, options)) ret = false;
      });
      return ret;
    }
    if (!(element instanceof HTMLElement))
      this.fatal(
        'Passed element must be an instance of HTMLElement, got "@type" instead.',
        { "@type": typeof element }
      );
    if (typeof $.simulate === "function") {
      new $.simulate(element, type, options);
      return true;
    }
    var event;
    var ctor;
    var types = [].concat(type);
    for (var i = 0, l = types.length; i < l; i++) {
      type = types[i];
      for (var name in this.eventMap)
        if (this.eventMap[name].test(type)) {
          ctor = name;
          break;
        }
      if (!ctor)
        throw new SyntaxError(
          "Only rudimentary HTMLEvents, KeyboardEvents and MouseEvents are supported: " +
            type
        );
      var opts = { bubbles: true, cancelable: true };
      if (ctor === "KeyboardEvent" || ctor === "MouseEvent")
        $.extend(opts, { ctrlKey: !1, altKey: !1, shiftKey: !1, metaKey: !1 });
      if (ctor === "MouseEvent")
        $.extend(opts, { button: 0, pointerX: 0, pointerY: 0, view: window });
      if (options) $.extend(opts, options);
      if (typeof window[ctor] === "function") {
        event = new window[ctor](type, opts);
        if (!element.dispatchEvent(event)) ret = false;
      } else if (document.createEvent) {
        event = document.createEvent(ctor);
        event.initEvent(type, opts.bubbles, opts.cancelable);
        if (!element.dispatchEvent(event)) ret = false;
      } else if (typeof element.fireEvent === "function") {
        event = $.extend(document.createEventObject(), opts);
        if (!element.fireEvent("on" + type, event)) ret = false;
      } else {
        if (typeof element[type]) element[type]();
      }
    }
    return ret;
  };
  Bootstrap.stripHtml = function (html) {
    if (html instanceof $) html = html.html();
    else {
      if (html instanceof Element) html = html.innerHTML;
    }
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return (tmp.textContent || tmp.innerText || "").replace(
      /^[\s\n\t]*|[\s\n\t]*$/,
      ""
    );
  };
  Bootstrap.unsupported = function (type, name, value) {
    Bootstrap.warn("Unsupported by Drupal Bootstrap: (@type) @name -> @value", {
      "@type": type,
      "@name": name,
      "@value": typeof value === "object" ? JSON.stringify(value) : value,
    });
  };
  Bootstrap.warn = function (message, args) {
    if (this.settings.dev && console.warn)
      console.warn(Drupal.formatString(message, args));
  };
  Bootstrap.wrapPluginConstructor = function (constructor, plugin, extend) {
    var proto = constructor.prototype;
    var option = this.option;
    if (proto.option === void 0)
      proto.option = function () {
        return option.apply(this, arguments);
      };
    if (extend) {
      if (plugin.prototype !== void 0)
        for (var key in plugin.prototype) {
          if (!plugin.prototype.hasOwnProperty(key)) continue;
          var value = plugin.prototype[key];
          if (typeof value === "function")
            proto[key] = this.superWrapper(proto[key] || function () {}, value);
          else
            proto[key] = $.isPlainObject(value)
              ? $.extend(true, {}, proto[key], value)
              : value;
        }
      delete plugin.prototype;
      for (key in plugin) {
        if (!plugin.hasOwnProperty(key)) continue;
        value = plugin[key];
        if (typeof value === "function")
          constructor[key] = this.superWrapper(
            constructor[key] || function () {},
            value
          );
        else
          constructor[key] = $.isPlainObject(value)
            ? $.extend(true, {}, constructor[key], value)
            : value;
      }
    }
  };
  Drupal.bootstrap = Drupal.bootstrap || Bootstrap;
})(window._, window.jQuery, window.Drupal, window.drupalSettings);
(function ($, _) {
  var Attributes = function (attributes) {
    this.data = {};
    this.data["class"] = [];
    this.merge(attributes);
  };
  Attributes.prototype.toString = function () {
    var output = "";
    var name, value;
    var checkPlain = function (str) {
      return (
        (str &&
          str
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")) ||
        ""
      );
    };
    var data = this.getData();
    for (name in data) {
      if (!data.hasOwnProperty(name)) continue;
      value = data[name];
      if (_.isFunction(value)) value = value();
      if (_.isObject(value)) value = _.values(value);
      if (_.isArray(value)) value = value.join(" ");
      output += " " + checkPlain(name) + '="' + checkPlain(value) + '"';
    }
    return output;
  };
  Attributes.prototype.toPlainObject = function () {
    var object = {};
    var name, value;
    var data = this.getData();
    for (name in data) {
      if (!data.hasOwnProperty(name)) continue;
      value = data[name];
      if (_.isFunction(value)) value = value();
      if (_.isObject(value)) value = _.values(value);
      if (_.isArray(value)) value = value.join(" ");
      object[name] = value;
    }
    return object;
  };
  Attributes.prototype.addClass = function (value) {
    var args = Array.prototype.slice.call(arguments);
    this.data["class"] = this.sanitizeClasses(this.data["class"].concat(args));
    return this;
  };
  Attributes.prototype.exists = function (name) {
    return this.data[name] !== void 0 && this.data[name] !== null;
  };
  Attributes.prototype.get = function (name, defaultValue) {
    if (!this.exists(name)) this.data[name] = defaultValue;
    return this.data[name];
  };
  Attributes.prototype.getData = function () {
    return _.extend({}, this.data);
  };
  Attributes.prototype.getClasses = function () {
    return this.get("class", []);
  };
  Attributes.prototype.hasClass = function (className) {
    className = this.sanitizeClasses(Array.prototype.slice.call(arguments));
    var classes = this.getClasses();
    for (var i = 0, l = className.length; i < l; i++)
      if (_.indexOf(classes, className[i]) === -1) return false;
    return true;
  };
  Attributes.prototype.merge = function (object, recursive) {
    if (!object) return this;
    if (object instanceof $) object = object[0];
    if (object instanceof Node)
      object = Array.prototype.slice
        .call(object.attributes)
        .reduce(function (attributes, attribute) {
          attributes[attribute.name] = attribute.value;
          return attributes;
        }, {});
    else if (object instanceof Attributes) object = object.getData();
    else object = _.extend({}, object);
    if (!$.isPlainObject(object)) {
      setTimeout(function () {
        throw new Error("Passed object is not supported: " + object);
      });
      return this;
    }
    if (object && object["class"] !== void 0) {
      this.addClass(object["class"]);
      delete object["class"];
    }
    if (recursive === void 0 || recursive)
      this.data = $.extend(true, {}, this.data, object);
    else this.data = $.extend({}, this.data, object);
    return this;
  };
  Attributes.prototype.remove = function (name) {
    if (this.exists(name)) delete this.data[name];
    return this;
  };
  Attributes.prototype.removeClass = function (className) {
    var remove = this.sanitizeClasses(Array.prototype.slice.apply(arguments));
    this.data["class"] = _.without(this.getClasses(), remove);
    return this;
  };
  Attributes.prototype.replaceClass = function (oldValue, newValue) {
    var classes = this.getClasses();
    var i = _.indexOf(this.sanitizeClasses(oldValue), classes);
    if (i >= 0) {
      classes[i] = newValue;
      this.set("class", classes);
    }
    return this;
  };
  Attributes.prototype.sanitizeClasses = function (classes) {
    return _.chain(Array.prototype.slice.call(arguments))
      .flatten()
      .map(function (string) {
        return string.split(" ");
      })
      .flatten()
      .filter()
      .map(function (value) {
        return Attributes.cleanClass(value);
      })
      .uniq()
      .value();
  };
  Attributes.prototype.set = function (name, value) {
    var obj = $.isPlainObject(name) ? name : {};
    if (typeof name === "string") obj[name] = value;
    return this.merge(obj);
  };
  Attributes.cleanClass = function (identifier, filter) {
    filter = filter || { " ": "-", _: "-", "/": "-", "[": "-", "]": "" };
    identifier = identifier.toLowerCase();
    if (filter["__"] === void 0)
      identifier = identifier.replace("__", "#DOUBLE_UNDERSCORE#");
    identifier = identifier.replace(
      Object.keys(filter),
      Object.keys(filter).map(function (key) {
        return filter[key];
      })
    );
    if (filter["__"] === void 0)
      identifier = identifier.replace("#DOUBLE_UNDERSCORE#", "__");
    identifier = identifier.replace(
      /[^\u002D\u0030-\u0039\u0041-\u005A\u005F\u0061-\u007A\u00A1-\uFFFF]/g,
      ""
    );
    identifier = identifier.replace(
      ["/^[0-9]/", "/^(-[0-9])|^(--)/"],
      ["_", "__"]
    );
    return identifier;
  };
  Attributes.create = function (attributes) {
    return new Attributes(attributes);
  };
  window.Attributes = Attributes;
})(window.jQuery, window._);
(function ($, Drupal, Bootstrap, Attributes) {
  if (!Drupal.icon) Drupal.icon = { bundles: {} };
  if (!Drupal.theme.icon || Drupal.theme.prototype.icon)
    $.extend(Drupal.theme, {
      icon: function (bundle, icon, attributes) {
        if (!Drupal.icon.bundles[bundle]) return "";
        attributes = Attributes.create(attributes)
          .addClass("icon")
          .set("aria-hidden", "true");
        icon = Drupal.icon.bundles[bundle](icon, attributes);
        return "<span" + attributes + "></span>";
      },
    });
  Drupal.icon.bundles.bootstrap = function (icon, attributes) {
    attributes.addClass(["glyphicon", "glyphicon-" + icon]);
  };
  $.extend(Drupal.theme, {
    ajaxThrobber: function () {
      return Drupal.theme("bootstrapIcon", "refresh", {
        class: ["ajax-throbber", "glyphicon-spin"],
      });
    },
    button: function (attributes) {
      attributes = Attributes.create(attributes).addClass("btn");
      var context = attributes.get("context", "default");
      var label = attributes.get("value", "");
      attributes.remove("context").remove("value");
      if (
        !attributes.hasClass([
          "btn-default",
          "btn-primary",
          "btn-success",
          "btn-info",
          "btn-warning",
          "btn-danger",
          "btn-link",
        ])
      )
        attributes.addClass("btn-" + Bootstrap.checkPlain(context));
      if (!attributes.exists("type"))
        attributes.set(
          "type",
          attributes.hasClass("form-submit") ? "submit" : "button"
        );
      return "<button" + attributes + ">" + label + "</button>";
    },
    btn: function (attributes) {
      return Drupal.theme("button", attributes);
    },
    "btn-block": function (attributes) {
      return Drupal.theme(
        "button",
        Attributes.create(attributes).addClass("btn-block")
      );
    },
    "btn-lg": function (attributes) {
      return Drupal.theme(
        "button",
        Attributes.create(attributes).addClass("btn-lg")
      );
    },
    "btn-sm": function (attributes) {
      return Drupal.theme(
        "button",
        Attributes.create(attributes).addClass("btn-sm")
      );
    },
    "btn-xs": function (attributes) {
      return Drupal.theme(
        "button",
        Attributes.create(attributes).addClass("btn-xs")
      );
    },
    bootstrapIcon: function (name, attributes) {
      return Drupal.theme("icon", "bootstrap", name, attributes);
    },
  });
})(window.jQuery, window.Drupal, window.Drupal.bootstrap, window.Attributes);
var Drupal = Drupal || {};
(function ($, Drupal, Bootstrap) {
  "use strict";
  var $document = $(document);
  Bootstrap.extendPlugin("popover", function (settings) {
    return {
      DEFAULTS: {
        animation: !!settings.popover_animation,
        autoClose: !!settings.popover_auto_close,
        enabled: settings.popover_enabled,
        html: !!settings.popover_html,
        placement: settings.popover_placement,
        selector: settings.popover_selector,
        trigger: settings.popover_trigger,
        title: settings.popover_title,
        content: settings.popover_content,
        delay: parseInt(settings.popover_delay, 10),
        container: settings.popover_container,
      },
    };
  });
  Drupal.behaviors.bootstrapPopovers = {
    $activePopover: null,
    attach: function (context) {
      if (!$.fn.popover || !$.fn.popover.Constructor.DEFAULTS.enabled) return;
      var _this = this;
      $document
        .on("show.bs.popover", "[data-toggle=popover]", function () {
          var $trigger = $(this);
          var popover = $trigger.data("bs.popover");
          if (popover.options.originalTrigger === "click") {
            if (
              _this.$activePopover &&
              _this.getOption("autoClose") &&
              !_this.$activePopover.is($trigger)
            )
              _this.$activePopover.popover("hide");
            _this.$activePopover = $trigger;
          }
        })
        .on("focus.bs.popover", ":visible", function (e) {
          var $target = $(e.target);
          if (
            _this.$activePopover &&
            _this.getOption("autoClose") &&
            !_this.$activePopover.is($target) &&
            !$target.closest(".popover.in")[0]
          ) {
            _this.$activePopover.popover("hide");
            _this.$activePopover = null;
          }
        })
        .on("click.bs.popover", function (e) {
          var $target = $(e.target);
          if (
            _this.$activePopover &&
            _this.getOption("autoClose") &&
            !$target.is("[data-toggle=popover]") &&
            !$target.closest(".popover.in")[0]
          ) {
            _this.$activePopover.popover("hide");
            _this.$activePopover = null;
          }
        })
        .on("keyup.bs.popover", function (e) {
          if (
            _this.$activePopover &&
            _this.getOption("autoClose") &&
            e.which === 27
          ) {
            _this.$activePopover.popover("hide");
            _this.$activePopover = null;
          }
        });
      var elements = $(context).find("[data-toggle=popover]").toArray();
      for (var i = 0; i < elements.length; i++) {
        var $element = $(elements[i]);
        var options = $.extend(
          {},
          $.fn.popover.Constructor.DEFAULTS,
          $element.data()
        );
        options.originalTrigger = options.trigger;
        if (options.trigger === "click") options.trigger = "manual";
        var target =
          options.target ||
          ($element.is('a[href^="#"]') && $element.attr("href"));
        var $target = $document.find(target).clone();
        if (!options.content && $target[0]) {
          $target
            .removeClass("visually-hidden hidden")
            .removeAttr("aria-hidden");
          options.content =
            $target.wrap("<div/>").parent()[options.html ? "html" : "text"]() ||
            "";
        }
        $element.popover(options);
        if (options.originalTrigger === "click") {
          $element
            .off("click.drupal.bootstrap.popover")
            .on("click.drupal.bootstrap.popover", function (e) {
              $(this).popover("toggle");
              e.preventDefault();
              e.stopPropagation();
            });
        }
      }
    },
    detach: function (context) {
      if (!$.fn.popover || !$.fn.popover.Constructor.DEFAULTS.enabled) return;
      $(context)
        .find('[data-toggle="popover"]')
        .off("click.drupal.bootstrap.popover")
        .popover("destroy");
    },
    getOption: function (name, defaultValue, element) {
      var $element = element ? $(element) : this.$activePopover;
      var options = $.extend(
        true,
        {},
        $.fn.popover.Constructor.DEFAULTS,
        (($element && $element.data("bs.popover")) || {}).options
      );
      if (options[name] !== void 0) return options[name];
      return defaultValue !== void 0 ? defaultValue : void 0;
    },
  };
})(window.jQuery, window.Drupal, window.Drupal.bootstrap);
(function (Drupal, drupalSettings, once) {
  const sitewideAlertsSelector = "[data-sitewide-alert]";
  const shouldShowOnThisPage = (pages = [], negate = true) => {
    if (pages.length === 0) return true;
    let pagePathMatches = false;
    const currentPath = window.location.pathname;
    for (let i = 0; i < pages.length; i++) {
      const baseUrl = drupalSettings?.path?.baseUrl?.slice(0, -1);
      const page = baseUrl + pages[i];
      if (page.charAt(page.length - 1) === "*") {
        if (currentPath.startsWith(page.substring(0, page.length - 1))) {
          pagePathMatches = true;
          break;
        }
      } else {
        if (page === currentPath) {
          pagePathMatches = true;
          break;
        }
      }
    }
    return negate ? !pagePathMatches : pagePathMatches;
  };
  const alertWasDismissed = (alert) => {
    if (!(`alert-dismissed-${alert.uuid}` in window.localStorage)) return false;
    const dismissedAtTimestamp = Number(
      window.localStorage.getItem(`alert-dismissed-${alert.uuid}`)
    );
    return dismissedAtTimestamp >= alert.dismissalIgnoreBefore;
  };
  const dismissAlert = (alert) => {
    window.localStorage.setItem(
      `alert-dismissed-${alert.uuid}`,
      String(Math.round(new Date().getTime() / 1000))
    );
    document
      .querySelectorAll(`[data-uuid="${alert.uuid}"]`)
      .forEach((alert) => {
        alert.dispatchEvent(
          new CustomEvent("sitewide-alert-dismissed", {
            bubbles: true,
            composed: true,
          })
        );
        removeAlert(alert);
      });
  };
  const buildAlertElement = (alert) => {
    const alertElement = document.createElement("div");
    alertElement.innerHTML = alert.renderedAlert;
    if (alert.dismissible) {
      const dismissButtons =
        alertElement.getElementsByClassName("js-dismiss-button");
      for (let i = 0; i < dismissButtons.length; i++)
        dismissButtons[i].addEventListener("click", () => dismissAlert(alert));
    }
    return alertElement.firstElementChild;
  };
  const removeAlert = (alert) => {
    alert.dispatchEvent(
      new CustomEvent("sitewide-alert-removed", {
        bubbles: true,
        composed: true,
      })
    );
    alert.remove();
  };
  const fetchAlerts = () => {
    return fetch(
      `${
        window.location.origin +
        drupalSettings?.path?.baseUrl +
        drupalSettings?.path?.pathPrefix
      }sitewide_alert/load`
    )
      .then((res) => res.json())
      .then(
        (result) => result.sitewideAlerts,
        (error) => {
          console.error(error);
        }
      );
  };
  const removeStaleAlerts = (alerts) => {
    const roots = document.querySelectorAll(sitewideAlertsSelector);
    roots.forEach((root) => {
      const existingAlerts = root.querySelectorAll("[data-uuid]");
      const alertsToBeRemoved = Array.from(existingAlerts).filter(
        (alert) => !alerts.includes(alert.getAttribute("data-uuid"))
      );
      alertsToBeRemoved.forEach((alert) => removeAlert(alert));
    });
  };
  const initAlerts = () => {
    const roots = document.querySelectorAll(sitewideAlertsSelector);
    // fetchAlerts().then((alerts) => {
    //   removeStaleAlerts(alerts);
    //   alerts.forEach((alert) => {
    //     const dismissed = alertWasDismissed(alert);
    //     const showOnThisPage = shouldShowOnThisPage(
    //       alert.showOnPages,
    //       alert.negateShowOnPages
    //     );
    //     roots.forEach((root) => {
    //       const existingAlertElement = root.querySelector(
    //         `[data-uuid="${alert.uuid}"]`
    //       );
    //       if (showOnThisPage && !dismissed) {
    //         const renderableAlertElement = buildAlertElement(alert);
    //         existingAlertElement
    //           ? root.replaceChild(renderableAlertElement, existingAlertElement)
    //           : root.appendChild(renderableAlertElement);
    //         renderableAlertElement.dispatchEvent(
    //           new CustomEvent("sitewide-alert-rendered", {
    //             bubbles: true,
    //             composed: true,
    //           })
    //         );
    //         return;
    //       }
    //       if ((dismissed || !showOnThisPage) && existingAlertElement)
    //         removeAlert(existingAlertElement);
    //     });
    //   });
    // });
  };
  const supportsHistoryPushState = () => {
    return (
      "pushState" in window.history && window.history["pushState"] !== null
    );
  };
  const supportsHistoryReplaceState = () => {
    return (
      "replaceState" in window.history &&
      window.history["replaceState"] !== null
    );
  };
  const proxyPushState = () => {
    if (supportsHistoryPushState())
      window.history.pushState = new Proxy(window.history.pushState, {
        apply(target, thisArg, argArray) {
          triggerHistoryEvent(thisArg, argArray);
          return target.apply(thisArg, argArray);
        },
      });
    if (supportsHistoryReplaceState())
      window.history.replaceState = new Proxy(window.history.replaceState, {
        apply(target, thisArg, argArray) {
          triggerHistoryEvent(thisArg, argArray);
          return target.apply(thisArg, argArray);
        },
      });
  };
  const triggerHistoryEvent = (thisArg, argArray) => {
    const event = new CustomEvent("sitewidealerts.popstate", {
      detail: { state: thisArg, options: argArray },
    });
    window.dispatchEvent(event);
  };
  const historyListener = () => {
    window.addEventListener("sitewidealerts.popstate", () => initAlerts());
    window.addEventListener("popstate", () => initAlerts());
  };
  Drupal.behaviors.sitewide_alert_init = {
    attach: (context, settings) => {
      once("sitewide_alerts_init", "html", context).forEach((element) => {
        initAlerts();
        proxyPushState();
        historyListener();
        if (drupalSettings?.sitewideAlert?.automaticRefresh === true) {
          const interval = setInterval(
            () => initAlerts(),
            drupalSettings.sitewideAlert.refreshInterval < 1000
              ? 1000
              : drupalSettings.sitewideAlert.refreshInterval
          );
          if (!drupalSettings?.sitewideAlert?.automaticRefresh)
            clearInterval(interval);
        }
      });
    },
  };
})(Drupal, drupalSettings, once);
Drupal.debounce = function (func, wait, immediate) {
  let timeout;
  let result;
  return function (...args) {
    const context = this;
    const later = function () {
      timeout = null;
      if (!immediate) result = func.apply(context, args);
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) result = func.apply(context, args);
    return result;
  };
};
(function ($, Drupal, debounce) {
  $.fn.drupalGetSummary = function () {
    const callback = this.data("summaryCallback");
    return this[0] && callback ? callback(this[0]).trim() : "";
  };
  $.fn.drupalSetSummary = function (callback) {
    const self = this;
    if (typeof callback !== "function") {
      const val = callback;
      callback = function () {
        return val;
      };
    }
    return this.data("summaryCallback", callback)
      .off("formUpdated.summary")
      .on("formUpdated.summary", () => {
        self.trigger("summaryUpdated");
      })
      .trigger("summaryUpdated");
  };
  Drupal.behaviors.formSingleSubmit = {
    attach() {
      function onFormSubmit(e) {
        const $form = $(e.currentTarget);
        const formValues = $form.serialize();
        const previousValues = $form.attr("data-drupal-form-submit-last");
        if (previousValues === formValues) e.preventDefault();
        else $form.attr("data-drupal-form-submit-last", formValues);
      }
      $(once("form-single-submit", "body")).on(
        "submit.singleSubmit",
        'form:not([method~="GET"])',
        onFormSubmit
      );
    },
  };
  function triggerFormUpdated(element) {
    $(element).trigger("formUpdated");
  }
  function fieldsList(form) {
    return [].map.call(form.querySelectorAll("[name][id]"), (el) => el.id);
  }
  Drupal.behaviors.formUpdated = {
    attach(context) {
      const $context = $(context);
      const contextIsForm = $context.is("form");
      const $forms = $(
        once("form-updated", contextIsForm ? $context : $context.find("form"))
      );
      let formFields;
      if ($forms.length)
        $.makeArray($forms).forEach((form) => {
          const events = "change.formUpdated input.formUpdated ";
          const eventHandler = debounce((event) => {
            triggerFormUpdated(event.target);
          }, 300);
          formFields = fieldsList(form).join(",");
          form.setAttribute("data-drupal-form-fields", formFields);
          $(form).on(events, eventHandler);
        });
      if (contextIsForm) {
        formFields = fieldsList(context).join(",");
        const currentFields = $(context).attr("data-drupal-form-fields");
        if (formFields !== currentFields) triggerFormUpdated(context);
      }
    },
    detach(context, settings, trigger) {
      const $context = $(context);
      const contextIsForm = $context.is("form");
      if (trigger === "unload")
        once
          .remove(
            "form-updated",
            contextIsForm ? $context : $context.find("form")
          )
          .forEach((form) => {
            form.removeAttribute("data-drupal-form-fields");
            $(form).off(".formUpdated");
          });
    },
  };
  Drupal.behaviors.fillUserInfoFromBrowser = {
    attach(context, settings) {
      const userInfo = ["name", "mail", "homepage"];
      const $forms = $(
        once("user-info-from-browser", "[data-user-info-from-browser]")
      );
      if ($forms.length)
        userInfo.forEach((info) => {
          const $element = $forms.find(`[name=${info}]`);
          const browserData = localStorage.getItem(`Drupal.visitor.${info}`);
          if (!$element.length) return;
          const emptyValue = $element[0].value === "";
          const defaultValue =
            $element.attr("data-drupal-default-value") === $element[0].value;
          if (browserData && (emptyValue || defaultValue))
            $element.each(function (index, item) {
              item.value = browserData;
            });
        });
      $forms.on("submit", () => {
        userInfo.forEach((info) => {
          const $element = $forms.find(`[name=${info}]`);
          if ($element.length)
            localStorage.setItem(`Drupal.visitor.${info}`, $element[0].value);
        });
      });
    },
  };
  const handleFragmentLinkClickOrHashChange = (e) => {
    let url;
    if (e.type === "click")
      url = e.currentTarget.location
        ? e.currentTarget.location
        : e.currentTarget;
    else url = window.location;
    const hash = url.hash.substr(1);
    if (hash) {
      const $target = $(`#${hash}`);
      $("body").trigger("formFragmentLinkClickOrHashChange", [$target]);
      setTimeout(() => $target.trigger("focus"), 300);
    }
  };
  const debouncedHandleFragmentLinkClickOrHashChange = debounce(
    handleFragmentLinkClickOrHashChange,
    300,
    true
  );
  $(window).on(
    "hashchange.form-fragment",
    debouncedHandleFragmentLinkClickOrHashChange
  );
  $(document).on(
    "click.form-fragment",
    'a[href*="#"]',
    debouncedHandleFragmentLinkClickOrHashChange
  );
})(jQuery, Drupal, Drupal.debounce);
(function ($, window, Drupal, drupalSettings, once) {
  Drupal.behaviors.bootstrapForm = {
    attach: function (context) {
      if (
        drupalSettings.bootstrap &&
        drupalSettings.bootstrap.forms_has_error_value_toggle
      ) {
        var $context = $(context);
        $(
          once(
            "error",
            ".form-item.has-error:not(.form-type-password.has-feedback)",
            context
          )
        ).each(function () {
          var $formItem = $(this);
          var $input = $formItem.find(":input");
          $input.on("keyup focus blur", function () {
            if (this.defaultValue !== void 0) {
              $formItem[
                this.defaultValue !== this.value ? "removeClass" : "addClass"
              ]("has-error");
              $input[
                this.defaultValue !== this.value ? "removeClass" : "addClass"
              ]("error");
            }
          });
        });
      }
    },
  };
})(jQuery, this, Drupal, drupalSettings, once);
(function ($, Drupal) {
  "use strict";
  var isChrome = /chrom(e|ium)/.test(window.navigator.userAgent.toLowerCase());
  if (isChrome) {
    var backButton = false;
    if (window.performance) {
      var navEntries = window.performance.getEntriesByType("navigation");
      if (navEntries.length > 0 && navEntries[0].type === "back_forward")
        backButton = true;
      else {
        if (
          window.performance.navigation &&
          window.performance.navigation.type ===
            window.performance.navigation.TYPE_BACK_FORWARD
        )
          backButton = true;
      }
    }
    if (backButton && Drupal) {
      var attachBehaviors = Drupal?.attachBehaviors;
      Drupal.attachBehaviors = function (context, settings) {
        setTimeout(function (context, settings) {
          attachBehaviors(context, settings);
        }, 300);
      };
    }
  }
})(jQuery, Drupal);
(function ($, Drupal) {
  const states = { postponed: [] };
  Drupal.states = states;
  function invert(a, invertState) {
    return invertState && typeof a !== "undefined" ? !a : a;
  }
  function compare(a, b) {
    if (a === b) return typeof a === "undefined" ? a : true;
    return typeof a === "undefined" || typeof b === "undefined";
  }
  function ternary(a, b) {
    if (typeof a === "undefined") return b;
    if (typeof b === "undefined") return a;
    return a && b;
  }
  Drupal.behaviors.states = {
    attach(context, settings) {
      const $states = $(context).find("[data-drupal-states]");
      const il = $states.length;
      for (let i = 0; i < il; i++) {
        const config = JSON.parse(
          $states[i].getAttribute("data-drupal-states")
        );
        Object.keys(config || {}).forEach((state) => {
          new states.Dependent({
            element: $($states[i]),
            state: states.State.sanitize(state),
            constraints: config[state],
          });
        });
      }
      while (states.postponed.length) states.postponed.shift()();
    },
  };
  states.Dependent = function (args) {
    $.extend(this, { values: {}, oldValue: null }, args);
    this.dependees = this.getDependees();
    Object.keys(this.dependees || {}).forEach((selector) => {
      this.initializeDependee(selector, this.dependees[selector]);
    });
  };
  states.Dependent.comparisons = {
    RegExp(reference, value) {
      return reference.test(value);
    },
    Function(reference, value) {
      return reference(value);
    },
    Number(reference, value) {
      return typeof value === "string"
        ? compare(reference.toString(), value)
        : compare(reference, value);
    },
  };
  states.Dependent.prototype = {
    initializeDependee(selector, dependeeStates) {
      this.values[selector] = {};
      Object.keys(dependeeStates).forEach((i) => {
        let state = dependeeStates[i];
        if ($.inArray(state, dependeeStates) === -1) return;
        state = states.State.sanitize(state);
        this.values[selector][state.name] = null;
        $(selector).on(`state:${state}`, { selector, state }, (e) => {
          this.update(e.data.selector, e.data.state, e.value);
        });
        new states.Trigger({ selector, state });
      });
    },
    compare(reference, selector, state) {
      const value = this.values[selector][state.name];
      if (reference.constructor.name in states.Dependent.comparisons)
        return states.Dependent.comparisons[reference.constructor.name](
          reference,
          value
        );
      return compare(reference, value);
    },
    update(selector, state, value) {
      if (value !== this.values[selector][state.name]) {
        this.values[selector][state.name] = value;
        this.reevaluate();
      }
    },
    reevaluate() {
      let value = this.verifyConstraints(this.constraints);
      if (value !== this.oldValue) {
        this.oldValue = value;
        value = invert(value, this.state.invert);
        this.element.trigger({
          type: `state:${this.state}`,
          value,
          trigger: true,
        });
      }
    },
    verifyConstraints(constraints, selector) {
      let result;
      if ($.isArray(constraints)) {
        const hasXor = $.inArray("xor", constraints) === -1;
        const len = constraints.length;
        for (let i = 0; i < len; i++)
          if (constraints[i] !== "xor") {
            const constraint = this.checkConstraints(
              constraints[i],
              selector,
              i
            );
            if (constraint && (hasXor || result)) return hasXor;
            result = result || constraint;
          }
      } else {
        if ($.isPlainObject(constraints)) {
          for (const n in constraints)
            if (constraints.hasOwnProperty(n)) {
              result = ternary(
                result,
                this.checkConstraints(constraints[n], selector, n)
              );
              if (result === false) return false;
            }
        }
      }
      return result;
    },
    checkConstraints(value, selector, state) {
      if (typeof state !== "string" || /[0-9]/.test(state[0])) state = null;
      else {
        if (typeof selector === "undefined") {
          selector = state;
          state = null;
        }
      }
      if (state !== null) {
        state = states.State.sanitize(state);
        return invert(this.compare(value, selector, state), state.invert);
      }
      return this.verifyConstraints(value, selector);
    },
    getDependees() {
      const cache = {};
      const _compare = this.compare;
      this.compare = function (reference, selector, state) {
        (cache[selector] || (cache[selector] = [])).push(state.name);
      };
      this.verifyConstraints(this.constraints);
      this.compare = _compare;
      return cache;
    },
  };
  states.Trigger = function (args) {
    $.extend(this, args);
    if (this.state in states.Trigger.states) {
      this.element = $(this.selector);
      if (!this.element.data(`trigger:${this.state}`)) this.initialize();
    }
  };
  states.Trigger.prototype = {
    initialize() {
      const trigger = states.Trigger.states[this.state];
      if (typeof trigger === "function") trigger.call(window, this.element);
      else
        Object.keys(trigger || {}).forEach((event) => {
          this.defaultTrigger(event, trigger[event]);
        });
      this.element.data(`trigger:${this.state}`, true);
    },
    defaultTrigger(event, valueFn) {
      let oldValue = valueFn.call(this.element);
      this.element.on(
        event,
        $.proxy(function (e) {
          const value = valueFn.call(this.element, e);
          if (oldValue !== value) {
            this.element.trigger({
              type: `state:${this.state}`,
              value,
              oldValue,
            });
            oldValue = value;
          }
        }, this)
      );
      states.postponed.push(
        $.proxy(function () {
          this.element.trigger({
            type: `state:${this.state}`,
            value: oldValue,
            oldValue: null,
          });
        }, this)
      );
    },
  };
  states.Trigger.states = {
    empty: {
      keyup() {
        return this.val() === "";
      },
      change() {
        return this.val() === "";
      },
    },
    checked: {
      change() {
        let checked = false;
        this.each(function () {
          checked = $(this).prop("checked");
          return !checked;
        });
        return checked;
      },
    },
    value: {
      keyup() {
        if (this.length > 1) return this.filter(":checked").val() || false;
        return this.val();
      },
      change() {
        if (this.length > 1) return this.filter(":checked").val() || false;
        return this.val();
      },
    },
    collapsed: {
      collapsed(e) {
        return typeof e !== "undefined" && "value" in e
          ? e.value
          : !this.is("[open]");
      },
    },
  };
  states.State = function (state) {
    this.pristine = state;
    this.name = state;
    let process = true;
    do {
      while (this.name.charAt(0) === "!") {
        this.name = this.name.substring(1);
        this.invert = !this.invert;
      }
      if (this.name in states.State.aliases)
        this.name = states.State.aliases[this.name];
      else process = false;
    } while (process);
  };
  states.State.sanitize = function (state) {
    if (state instanceof states.State) return state;
    return new states.State(state);
  };
  states.State.aliases = {
    enabled: "!disabled",
    invisible: "!visible",
    invalid: "!valid",
    untouched: "!touched",
    optional: "!required",
    filled: "!empty",
    unchecked: "!checked",
    irrelevant: "!relevant",
    expanded: "!collapsed",
    open: "!collapsed",
    closed: "collapsed",
    readwrite: "!readonly",
  };
  states.State.prototype = {
    invert: false,
    toString() {
      return this.name;
    },
  };
  const $document = $(document);
  $document.on("state:disabled", (e) => {
    if (e.trigger)
      $(e.target)
        .closest(".js-form-item, .js-form-submit, .js-form-wrapper")
        .toggleClass("form-disabled", e.value)
        .find("select, input, textarea")
        .prop("disabled", e.value);
  });
  $document.on("state:readonly", (e) => {
    if (e.trigger)
      $(e.target)
        .closest(".js-form-item, .js-form-submit, .js-form-wrapper")
        .toggleClass("form-readonly", e.value)
        .find("input, textarea")
        .prop("readonly", e.value);
  });
  $document.on("state:required", (e) => {
    if (e.trigger)
      if (e.value) {
        const label = `label${e.target.id ? `[for=${e.target.id}]` : ""}`;
        const $label = $(e.target)
          .attr({ required: "required", "aria-required": "true" })
          .closest(".js-form-item, .js-form-wrapper")
          .find(label);
        if (!$label.hasClass("js-form-required").length)
          $label.addClass("js-form-required form-required");
      } else
        $(e.target)
          .removeAttr("required aria-required")
          .closest(".js-form-item, .js-form-wrapper")
          .find("label.js-form-required")
          .removeClass("js-form-required form-required");
  });
  $document.on("state:visible", (e) => {
    if (e.trigger)
      $(e.target)
        .closest(".js-form-item, .js-form-submit, .js-form-wrapper")
        .toggle(e.value);
  });
  $document.on("state:checked", (e) => {
    if (e.trigger)
      $(e.target)
        .closest(".js-form-item, .js-form-wrapper")
        .find("input")
        .prop("checked", e.value)
        .trigger("change");
  });
  $document.on("state:collapsed", (e) => {
    if (e.trigger)
      if ($(e.target).is("[open]") === e.value)
        $(e.target).find("> summary").trigger("click");
  });
})(jQuery, Drupal);
(function ($) {
  $(document).unbind("state:disabled");
  $(document).bind("state:disabled", function (e) {
    if (e.trigger)
      $(e.target)
        .attr("disabled", e.value)
        .closest(".form-item, .form-submit, .form-wrapper")
        .toggleClass("form-disabled", e.value)
        .find(":input")
        .attr("disabled", e.value);
  });
})(jQuery);
(function ($, Drupal, once) {
  "use strict";
  Drupal.webform = Drupal?.webform || {};
  Drupal.webform.states = Drupal?.webform.states || {};
  Drupal.webform.states.slideDown = Drupal?.webform.states.slideDown || {};
  Drupal.webform.states.slideDown.duration = "slow";
  Drupal.webform.states.slideUp = Drupal?.webform.states.slideUp || {};
  Drupal.webform.states.slideUp.duration = "fast";
  $.fn.hasData = function (data) {
    return typeof this.data(data) !== "undefined";
  };
  $.fn.isWebform = function () {
    return $(this).closest(
      'form.webform-submission-form, form[id^="webform"], form[data-is-webform]'
    ).length
      ? true
      : false;
  };
  $.fn.isWebformElement = function () {
    return $(this).isWebform() ||
      $(this).closest("[data-is-webform-element]").length
      ? true
      : false;
  };
  Drupal.states.Trigger.states.empty.change = function change() {
    return this.val() === "";
  };
  var states = Drupal.states;
  Drupal.states.Dependent.prototype.compare = function compare(
    reference,
    selector,
    state
  ) {
    var value = this.values[selector][state.name];
    var name = reference.constructor.name;
    if (!name) {
      name = $.type(reference);
      name = name.charAt(0).toUpperCase() + name.slice(1);
    }
    if (name in states.Dependent.comparisons)
      return states.Dependent.comparisons[name](reference, value);
    if (reference.constructor.name in states.Dependent.comparisons)
      return states.Dependent.comparisons[reference.constructor.name](
        reference,
        value
      );
    return _compare2(reference, value);
  };
  function _compare2(a, b) {
    if (a === b) return typeof a === "undefined" ? a : true;
    return typeof a === "undefined" || typeof b === "undefined";
  }
  Drupal.states.Dependent.comparisons.Object = function (reference, value) {
    if ("pattern" in reference)
      return new RegExp(reference["pattern"]).test(value);
    else if ("!pattern" in reference)
      return !new RegExp(reference["!pattern"]).test(value);
    else if ("less" in reference)
      return value !== "" && parseFloat(reference["less"]) > parseFloat(value);
    else if ("less_equal" in reference)
      return (
        value !== "" && parseFloat(reference["less_equal"]) >= parseFloat(value)
      );
    else if ("greater" in reference)
      return (
        value !== "" && parseFloat(reference["greater"]) < parseFloat(value)
      );
    else if ("greater_equal" in reference)
      return (
        value !== "" &&
        parseFloat(reference["greater_equal"]) <= parseFloat(value)
      );
    else if ("between" in reference || "!between" in reference) {
      if (value === "") return false;
      var between = reference["between"] || reference["!between"];
      var betweenParts = between.split(":");
      var greater = betweenParts[0];
      var less =
        typeof betweenParts[1] !== "undefined" ? betweenParts[1] : null;
      var isGreaterThan =
        greater === null ||
        greater === "" ||
        parseFloat(value) >= parseFloat(greater);
      var isLessThan =
        less === null || less === "" || parseFloat(value) <= parseFloat(less);
      var result = isGreaterThan && isLessThan;
      return reference["!between"] ? !result : result;
    } else return reference.indexOf(value) !== false;
  };
  var $document = $(document);
  $document.on("state:required", function (e) {
    if (e.trigger && $(e.target).isWebformElement()) {
      var $target = $(e.target);
      toggleRequired($target.find('input[type="file"]'), e.value);
      if (
        $target.is(
          ".js-form-type-radios, .js-form-type-webform-radios-other, .js-webform-type-radios, .js-webform-type-webform-radios-other, .js-webform-type-webform-entity-radios, .webform-likert-table"
        )
      ) {
        $target.toggleClass("required", e.value);
        toggleRequired($target.find('input[type="radio"]'), e.value);
      }
      if (
        $target.is(
          ".js-form-type-checkboxes, .js-form-type-webform-checkboxes-other, .js-webform-type-checkboxes, .js-webform-type-webform-checkboxes-other"
        )
      ) {
        $target.toggleClass("required", e.value);
        var $checkboxes = $target.find('input[type="checkbox"]');
        if (e.value) {
          $checkboxes.on("click", statesCheckboxesRequiredEventHandler);
          checkboxesRequired($target);
        } else {
          $checkboxes.off("click", statesCheckboxesRequiredEventHandler);
          toggleRequired($checkboxes, false);
        }
      }
      if ($target.is(".js-webform-tableselect")) {
        $target.toggleClass("required", e.value);
        var isMultiple = $target.is("[multiple]");
        if (isMultiple) {
          var $tbody = $target.find("tbody");
          var $checkboxes = $tbody.find('input[type="checkbox"]');
          copyRequireMessage($target, $checkboxes);
          if (e.value) {
            $checkboxes.on(
              "click change",
              statesCheckboxesRequiredEventHandler
            );
            checkboxesRequired($tbody);
          } else {
            $checkboxes.off(
              "click change ",
              statesCheckboxesRequiredEventHandler
            );
            toggleRequired($tbody, false);
          }
        } else {
          var $radios = $target.find('input[type="radio"]');
          copyRequireMessage($target, $radios);
          toggleRequired($radios, e.value);
        }
      }
      if (
        $target.is(
          ".js-form-type-webform-select-other, .js-webform-type-webform-select-other"
        )
      ) {
        var $select = $target.find("select");
        toggleRequired($select, e.value);
        copyRequireMessage($target, $select);
      }
      if ($target.find("> label:not([for])").length)
        $target
          .find("> label")
          .toggleClass("js-form-required form-required", e.value);
      if (
        $target.is(
          ".js-webform-type-radios, .js-webform-type-checkboxes, fieldset"
        )
      )
        $target
          .find("legend span.fieldset-legend:not(.visually-hidden)")
          .toggleClass("js-form-required form-required", e.value);
      if ($target.is("fieldset")) $target.removeAttr("required aria-required");
    }
  });
  $document.on("state:checked", function (e) {
    if (e.trigger) $(e.target).trigger("change");
  });
  $document.on("state:readonly", function (e) {
    if (e.trigger && $(e.target).isWebformElement()) {
      $(e.target)
        .prop("readonly", e.value)
        .closest(".js-form-item, .js-form-wrapper")
        .toggleClass("webform-readonly", e.value)
        .find("input, textarea")
        .prop("readonly", e.value);
      $(e.target)
        .trigger("webform:readonly")
        .find("select, input, textarea, button")
        .trigger("webform:readonly");
    }
  });
  $document.on("state:visible state:visible-slide", function (e) {
    if (e.trigger && $(e.target).isWebformElement())
      if (e.value)
        $(":input", e.target)
          .addBack()
          .each(function () {
            restoreValueAndRequired(this);
            triggerEventHandlers(this);
          });
      else
        $(":input", e.target)
          .addBack()
          .each(function () {
            backupValueAndRequired(this);
            clearValueAndRequired(this);
            triggerEventHandlers(this);
          });
  });
  $document.on("state:visible-slide", function (e) {
    if (e.trigger && $(e.target).isWebformElement()) {
      var effect = e.value ? "slideDown" : "slideUp";
      var duration = Drupal?.websform?.states[effect].duration;
      $(e.target)
        .closest(".js-form-item, .js-form-submit, .js-form-wrapper")
        [effect](duration);
    }
  });
  Drupal.states.State.aliases["invisible-slide"] = "!visible-slide";
  $document.on("state:disabled", function (e) {
    if (e.trigger && $(e.target).isWebformElement()) {
      $(e.target)
        .prop("disabled", e.value)
        .closest(".js-form-item, .js-form-submit, .js-form-wrapper")
        .toggleClass("form-disabled", e.value)
        .find("select, input, textarea, button")
        .prop("disabled", e.value);
      var fileElements = $(e.target).find(
        ':input[type="hidden"][name$="[fids]"]'
      );
      if (fileElements.length) {
        if ($(e.target).is("fieldset")) $(e.target).prop("disabled", false);
        fileElements.removeAttr("disabled");
      }
      $(e.target)
        .trigger("webform:disabled")
        .find("select, input, textarea, button")
        .trigger("webform:disabled");
    }
  });
  Drupal.behaviors.webformCheckboxesRequired = {
    attach: function (context) {
      $(
        once(
          "webform-checkboxes-required",
          ".js-form-type-checkboxes.required, .js-form-type-webform-checkboxes-other.required, .js-webform-type-checkboxes.required, .js-webform-type-webform-checkboxes-other.required, .js-webform-type-webform-radios-other.checkboxes",
          context
        )
      ).each(function () {
        var $element = $(this);
        $element
          .find('input[type="checkbox"]')
          .on("click", statesCheckboxesRequiredEventHandler);
        setTimeout(function () {
          checkboxesRequired($element);
        });
      });
    },
  };
  Drupal.behaviors.webformRadiosRequired = {
    attach: function (context) {
      $(
        once(
          "webform-radios-required",
          ".js-form-type-radios, .js-form-type-webform-radios-other, .js-webform-type-radios, .js-webform-type-webform-radios-other, .js-webform-type-webform-entity-radios, .js-webform-type-webform-scale",
          context
        )
      ).each(function () {
        var $element = $(this);
        setTimeout(function () {
          radiosRequired($element);
        });
      });
    },
  };
  Drupal.behaviors.webformTableSelectRequired = {
    attach: function (context) {
      $(
        once(
          "webform-tableselect-required",
          ".js-webform-tableselect.required",
          context
        )
      ).each(function () {
        var $element = $(this);
        var $tbody = $element.find("tbody");
        var isMultiple = $element.is("[multiple]");
        if (isMultiple)
          $tbody.find('input[type="checkbox"]').on("click change", function () {
            checkboxesRequired($tbody);
          });
        setTimeout(function () {
          isMultiple ? checkboxesRequired($tbody) : radiosRequired($element);
        });
      });
    },
  };
  function checkboxesRequired($element) {
    var $firstCheckbox = $element.find('input[type="checkbox"]').first();
    var isChecked = $element.find('input[type="checkbox"]').is(":checked");
    toggleRequired($firstCheckbox, !isChecked);
    copyRequireMessage($element, $firstCheckbox);
  }
  function radiosRequired($element) {
    var $radios = $element.find('input[type="radio"]');
    var isRequired = $element.hasClass("required");
    toggleRequired($radios, isRequired);
    copyRequireMessage($element, $radios);
  }
  function statesCheckboxesRequiredEventHandler() {
    var $element = $(this).closest(
      ".js-webform-type-checkboxes, .js-webform-type-webform-checkboxes-other"
    );
    checkboxesRequired($element);
  }
  function triggerEventHandlers(input) {
    var $input = $(input);
    var type = input.type;
    var tag = input.tagName.toLowerCase();
    var extraParameters = ["webform.states"];
    if (type === "checkbox" || type === "radio")
      $input
        .trigger("change", extraParameters)
        .trigger("blur", extraParameters);
    else if (tag === "select") {
      if ($input.closest(".webform-type-address").length) {
        if (
          !$input.data("webform-states-address-initialized") &&
          $input.attr("autocomplete") === "country" &&
          $input.val() === $input.find("option[selected]").attr("value")
        )
          return;
        $input.data("webform-states-address-initialized", true);
      }
      $input
        .trigger("change", extraParameters)
        .trigger("blur", extraParameters);
    } else {
      if (type !== "submit" && type !== "button" && type !== "file") {
        var hasInputMask =
          $.fn.inputmask && $input.hasClass("js-webform-input-mask");
        hasInputMask && $input.inputmask("remove");
        $input
          .trigger("input", extraParameters)
          .trigger("change", extraParameters)
          .trigger("keydown", extraParameters)
          .trigger("keyup", extraParameters)
          .trigger("blur", extraParameters);
        hasInputMask && $input.inputmask();
      }
    }
  }
  function backupValueAndRequired(input) {
    var $input = $(input);
    var type = input.type;
    var tag = input.tagName.toLowerCase();
    if ($input.prop("required") && !$input.hasData("webform-required"))
      $input.data("webform-required", true);
    if (!$input.hasData("webform-value"))
      if (type === "checkbox" || type === "radio")
        $input.data("webform-value", $input.prop("checked"));
      else if (tag === "select") {
        var values = [];
        $input.find("option:selected").each(function (i, option) {
          values[i] = option.value;
        });
        $input.data("webform-value", values);
      } else {
        if (type !== "submit" && type !== "button")
          $input.data("webform-value", input.value);
      }
  }
  function restoreValueAndRequired(input) {
    var $input = $(input);
    var value = $input.data("webform-value");
    if (typeof value !== "undefined") {
      var type = input.type;
      var tag = input.tagName.toLowerCase();
      if (type === "checkbox" || type === "radio")
        $input.prop("checked", value);
      else if (tag === "select")
        $.each(value, function (i, option_value) {
          option_value = option_value.replace(/'/g, "\\'");
          $input
            .find("option[value='" + option_value + "']")
            .prop("selected", true);
        });
      else {
        if (type !== "submit" && type !== "button") input.value = value;
      }
      $input.removeData("webform-value");
    }
    var required = $input.data("webform-required");
    if (typeof required !== "undefined") {
      if (required) $input.prop("required", true);
      $input.removeData("webform-required");
    }
  }
  function clearValueAndRequired(input) {
    var $input = $(input);
    if ($input.closest("[data-webform-states-no-clear]").length) return;
    var type = input.type;
    var tag = input.tagName.toLowerCase();
    if (type === "checkbox" || type === "radio") $input.prop("checked", false);
    else if (tag === "select")
      if ($input.find('option[value=""]').length) $input.val("");
      else input.selectedIndex = -1;
    else {
      if (type !== "submit" && type !== "button")
        input.value = type === "color" ? "#000000" : "";
    }
    $input.prop("required", false);
  }
  function toggleRequired($input, required) {
    var isCheckboxOrRadio =
      $input.attr("type") === "radio" || $input.attr("type") === "checkbox";
    if (required)
      if (isCheckboxOrRadio) $input.attr({ required: "required" });
      else $input.attr({ required: "required", "aria-required": "true" });
    else if (isCheckboxOrRadio) $input.removeAttr("required");
    else $input.removeAttr("required aria-required");
  }
  function copyRequireMessage($source, $destination) {
    if ($source.attr("data-msg-required"))
      $destination.attr("data-msg-required", $source.attr("data-msg-required"));
  }
})(jQuery, Drupal, once);
(function ($, Drupal, once) {
  "use strict";
  Drupal.behaviors.webformRemoveFormSingleSubmit = {
    attach: function attach() {
      function onFormSubmit(e) {
        var $form = $(e.currentTarget);
        $form.removeAttr("data-drupal-form-submit-last");
      }
      $(once("webform-single-submit", "body")).on(
        "submit.singleSubmit",
        "form.webform-remove-single-submit",
        onFormSubmit
      );
    },
  };
  Drupal.behaviors.webformDisableAutoSubmit = {
    attach: function (context) {
      $(
        once(
          "webform-disable-autosubmit",
          $(".js-webform-disable-autosubmit input").not(
            ":button, :submit, :reset, :image, :file"
          )
        )
      ).on("keyup keypress", function (e) {
        if (e.which === 13) {
          e.preventDefault();
          return false;
        }
      });
    },
  };
  Drupal.behaviors.webformRequiredError = {
    attach: function (context) {
      $(
        once(
          "webform-required-error",
          $(context).find(
            ":input[data-webform-required-error], :input[data-webform-pattern-error]"
          )
        )
      )
        .on("invalid", function () {
          this.setCustomValidity("");
          if (this.valid) return;
          if (
            this.validity.patternMismatch &&
            $(this).attr("data-webform-pattern-error")
          )
            this.setCustomValidity($(this).attr("data-webform-pattern-error"));
          else {
            if (
              this.validity.valueMissing &&
              $(this).attr("data-webform-required-error")
            )
              this.setCustomValidity(
                $(this).attr("data-webform-required-error")
              );
          }
        })
        .on("input change", function () {
          var name = $(this).attr("name");
          $(this.form)
            .find(':input[name="' + name + '"]')
            .each(function () {
              this.setCustomValidity("");
            });
        });
    },
  };
  $(document).on("state:required", function (e) {
    $(e.target)
      .filter(":input[data-webform-required-error]")
      .each(function () {
        this.setCustomValidity("");
      });
  });
})(jQuery, Drupal, once);

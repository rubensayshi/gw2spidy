/* C:\Projects\Cobalt\Source\Curse.Ascalon.Web\Content\js\Libs\htmldiff.js */
(function () {
    window.HTMLDiff = (function () {
        function a(c, d) {
            this.a = c;
            this.b = d;
        }
        a.prototype.diff = function () {
            var b;
            b = this.diff_list(this.tokenize(this.a), this.tokenize(this.b));
            this.update(this.a, b.filter(function (e) {
                var d, c;
                d = e[0], c = e[1];
                return d !== "+";
            }));
            return this.update(this.b, b.filter(function (e) {
                var d, c;
                d = e[0], c = e[1];
                return d !== "-";
            }));
        };
        a.prototype.parseTextNodes = function (c, d) {
            var b;
            b = function (m) {
                if (m === null) {
                    return false;
                }
                var i, o, j, e, h, g, k, f, l;
                if (m.nodeType === 3) {
                    if (!/^\s*$/.test(m.nodeValue)) {
                        return d(m);
                    }
                } else {
                    l = (function () {
                        var n, p, r, q;
                        r = m.childNodes;
                        q = [];
                        for (n = 0, p = r.length; n < p; n++) {
                            i = r[n];
                            q.push(i);
                        }
                        return q;
                    })();
                    for (h = 0, k = l.length; h < k; h++) {
                        e = l[h];
                        j = b(e);
                        if (j) {
                            for (g = 0, f = j.length; g < f; g++) {
                                o = j[g];
                                m.insertBefore(o, e);
                            }
                            m.removeChild(e);
                        }
                    }
                    return false;
                }
            };
            return b(c);
        };
        a.prototype.tokenize = function (c) {
            var b;
            b = [];
            this.parseTextNodes(c, function (d) {
                b = b.concat(d.nodeValue.split(" "));
                return false;
            });
            return b;
        };
        a.prototype.update = function (d, c) {
            var b;
            b = 0;
            return this.parseTextNodes(d, function (e) {
                var k, n, g, l, i, q, m, o, f, p, h, j;
                m = b;
                k = b + (e.nodeValue.split(" ")).length;
                b = k;
                i = (function () {
                    var r, s, v, t, u;
                    v = c.slice(m, k);
                    u = [];
                    for (r = 0, s = v.length; r < s; r++) {
                        t = v[r], o = t[0], f = t[1];
                        if (o === "=") {
                            u.push(f);
                        } else {
                            u.push("<ins>" + f + "</ins>");
                        }
                    }
                    return u;
                })();
                i = i.join(" ").replace(/<\/ins> <ins>/g, " ").replace(/<ins> /g, " <ins>").replace(/[ ]<\/ins>/g, "</ins> ").replace(/<ins><\/ins>/g, "");
                l = [];
                g = document.createTextNode("");
                l.push(g);
                j = i.split(/(<\/?ins>)/);
                for (p = 0, h = j.length; p < h; p++) {
                    q = j[p];
                    switch (q) {
                    case "<ins>":
                        n = document.createElement("ins");
                        l.push(n);
                        g = document.createTextNode("");
                        n.appendChild(g);
                        break;
                    case "</ins>":
                        g = document.createTextNode("");
                        l.push(g);
                        break;
                    default:
                        g.nodeValue = q;
                    }
                }
                return l.filter(function (r) {
                    return !(r.nodeType === 3 && r.nodeValue === "");
                });
            });
        };
        a.prototype.diff_list = function (n, l) {
            var h, o, p, q, c, s, d, e, t, m, v, u, r, b, g, f;
            c = {};
            for (h = 0, u = n.length; h < u; h++) {
                m = n[h];
                if (!(m in c)) {
                    c[m] = [];
                }
                c[m].push(h);
            }
            q = (function () {
                var j, i;
                i = [];
                for (h = 0, j = n.length; 0 <= j ? h < j : h > j; 0 <= j ? h++ : h--) {
                    i.push(0);
                }
                return i;
            })();
            e = d = s = 0;
            for (o = 0, r = l.length; o < r; o++) {
                m = l[o];
                t = (function () {
                    var j, i;
                    i = [];
                    for (h = 0, j = n.length; 0 <= j ? h < j : h > j; 0 <= j ? h++ : h--) {
                        i.push(0);
                    }
                    return i;
                })();
                f = (g = c[m]) !== null ? g : [];
                for (v = 0, b = f.length; v < b; v++) {
                    p = f[v];
                    t[p] = (p && q[p - 1] ? 1 : 0) + 1;
                    if (t[p] > s) {
                        s = t[p];
                        e = p - s + 1;
                        d = o - s + 1;
                    }
                }
                q = t;
            }
            if (s === 0) {
                return [].concat((function () {
                    var i, k, j;
                    j = [];
                    for (i = 0, k = n.length; i < k; i++) {
                        m = n[i];
                        j.push(["-", m]);
                    }
                    return j;
                })(), (function () {
                    var i, k, j;
                    j = [];
                    for (i = 0, k = l.length; i < k; i++) {
                        m = l[i];
                        j.push(["+", m]);
                    }
                    return j;
                })());
            } else {
                return [].concat(this.diff_list(n.slice(0, e), l.slice(0, d)), (function () {
                    var j, w, i, k;
                    i = l.slice(d, (d + s));
                    k = [];
                    for (j = 0, w = i.length; j < w; j++) {
                        m = i[j];
                        k.push(["=", m]);
                    }
                    return k;
                })(), this.diff_list(n.slice(e + s), l.slice(d + s)));
            }
        };
        return a;
    })();
}).call(this);
jQuery(document).ready(function () {
    jQuery.each(jQuery(".change"), function (a, d) {
        var c = jQuery(d).find(".old .db-tooltip");
        var b = jQuery(d).find(".new .db-tooltip");
        if ((c.length) && (b.length)) {
            var e = new HTMLDiff(c[0], b[0]);
            e.diff();
        }
    });
});

/* C:\Projects\Cobalt\Source\Curse.Ascalon.Web\Content\js\Libs\jquery.dbTooltip.js */
(function (i) {
    var p = {}, q, l, m, f = i.browser.msie && /MSIE\s(5\.5|6\.)/.test(navigator.userAgent),
        b = true,
        c = 0,
        g = 0;
    i.dbTooltip = {
        blocked: false,
        defaults: {
            delay: 200,
            fade: false,
            showURL: false,
            track: true,
            extraClass: "",
            top: 15,
            left: 15,
            AdvancedTooltips: true,
            id: "db-tooltip-container"
        },
        block: function () {
            i.dbTooltip.blocked = !i.dbTooltip.blocked;
        },
        change_html: function (s) {
            p.body.html(s);
        }
    };
    i.fn.extend({
        dbTooltip: function (s) {
            if (typeof (WowDbSettings) !== "undefined") {
                i.each(WowDbSettings, function (u, t) {
                    i.dbTooltip.defaults[u] = t;
                });
            }
            s = i.extend({}, i.dbTooltip.defaults, s);
            a(s);
            return this.each(function () {
                i.data(this, "dbTooltip", s);
                this.tOpacity = p.parent.css("opacity");
                this.tooltipText = this.title;
                i(this).removeAttr("title");
                this.alt = "";
            }).mouseover(o).mouseout(j).click(j);
        },
        hideWhenEmpty: function () {
            return this.each(function () {
                i(this)[i(this).html() ? "show" : "hide"]();
            });
        },
        url: function () {
            return this.attr("href") || this.attr("src");
        }
    });

    function a(s) {
        if (p.parent) {
            return;
        }
        p.parent = i('<div id="' + s.id + '"><h3></h3><div class="body"></div><div class="url"></div></div>').appendTo(document.body).hide();
        if (i.fn.bgiframe) {
            p.parent.bgiframe();
        }
        p.title = i("h3", p.parent);
        p.body = i("div.body", p.parent);
        p.url = i("div.url", p.parent);
    }

    function k(s) {
        return i.data(s, "dbTooltip");
    }

    function n(s) {
        if (k(this).delay) {
            m = setTimeout(r, k(this).delay);
        } else {
            r();
        }
        b = !! k(this).track;
        i(document.body).bind("mousemove", d);
        d(s);
    }

    function o(B, x) {
        if (i.dbTooltip.blocked || (this === q && !x) || (!this.tooltipText && !k(this).bodyHandler)) {
            return;
        }
        q = this;
        l = this.tooltipText;
        if (k(this).bodyHandler) {
            p.title.hide();
            var u = k(this).bodyHandler.call(this);
            var t = null;
            if (!u) {
                u = k(this).loadingText;
                if (k(this).wowTooltip) {
                    var C = k(this).wowTooltip.call(this);
                    WP_Tooltips[C.Type + "-" + C.Id + "-" + C.OldBuild + "-" + C.NewBuild] = k(this).loadingText;
                    var y = "tooltip";
                    if (C.OldBuild && C.NewBuild) {
                        y = "dual-tooltip/" + C.OldBuild + "/" + C.NewBuild;
                    }
                    var A = C.HostName;
                    var s = A + "/" + C.Type + "/" + C.Id + "/" + y + "?x";
                    if ((k(this).AdvancedTooltips || C.SimpleOrAdvanced === "advanced") && C.SimpleOrAdvanced !== "simple") {
                        s += "&advanced=1";
                    }
                    if (A === location.protocol + "//" + location.host) {
                        i.get(s, {
                            callback: "WP_OnTooltipLoaded"
                        }, function (D) {
                            WP_OnTooltipLoaded(D);
                        });
                    } else {
                        t = document.createElement("script");
                        t.type = "text/javascript";
                        t.src = s + "&callback=WP_OnTooltipLoaded";
                    }
                }
            }
            if (u.nodeType || u.jquery) {
                p.body.empty().append(u);
            } else {
                p.body.html(u);
            }
            p.body.show();
            r();
            if (t !== null) {
                i("head").append(t);
            }
        } else {
            if (k(this).showBody) {
                var z = l.split(k(this).showBody);
                p.title.html(z.shift()).show();
                p.body.empty();
                for (var v = 0, w;
                    (w = z[v]); v++) {
                    if (v > 0) {
                        p.body.append("<br/>");
                    }
                    p.body.append(w);
                }
                p.body.hideWhenEmpty();
            } else {
                p.title.html(l).show();
                p.body.hide();
            }
        } if (k(this).showURL && i(this).url()) {
            p.url.html(i(this).url().replace("http://", "")).show();
        } else {
            p.url.hide();
        }
        p.parent.addClass(k(this).extraClass);
        n.apply(this, arguments);
    }

    function r() {
        m = null;
        if ((!f || !i.fn.bgiframe) && k(q) && k(q).fade) {
            if (p.parent.is(":animated")) {
                p.parent.stop().show().fadeTo(k(q).fade, q.tOpacity);
            } else {
                p.parent.is(":visible") ? p.parent.fadeTo(k(q).fade, q.tOpacity) : p.parent.fadeIn(k(q).fade);
            }
        } else {
            p.parent.show();
        }
        d();
    }

    function d(y) {
        if (y) {
            if (y.pageX === c && y.pageY === g) {
                return;
            }
            if (!y.pageX) {
                y.pageX = c;
            } else {
                c = y.pageX;
            } if (!y.pageY) {
                y.pageY = g;
            } else {
                g = y.pageY;
            }
        }
        if (i.dbTooltip.blocked) {
            return;
        }
        if (y && y.target.tagName === "OPTION") {
            return;
        }
        if (!b && p.parent.is(":visible")) {
            i(document.body).unbind("mousemove", d)
        }
        if (q === null) {
            i(document.body).unbind("mousemove", d);
            return;
        }
        p.parent.removeClass("viewport-right").removeClass("viewport-bottom");
        var x = p.parent[0].offsetLeft;
        var z = p.parent[0].offsetTop;
        var w = k(q);
        if (!w) {
            p.parent.hide();
            i(document.body).unbind("mousemove", d);
            return
        }
        if (y) {
            x = y.pageX + w.left + 5;
            z = y.pageY - w.top - p.parent.height() + 5;
            var t = "auto";
            if (w.positionLeft) {
                t = i(window).width() - x;
                x = "auto"
            }
            p.parent.css({
                left: x,
                right: t,
                top: z
            })
        }
        var A = h,
            s = p.parent[0];
        if (A.x + A.cx < s.offsetLeft + s.offsetWidth) {
            x -= s.offsetWidth + 20 + w.left;
            p.parent.css({
                left: x + "px"
            }).addClass("viewport-right")
        }
        if (y && (z - i(window).scrollTop()) < 0) {
            z = y.pageY + w.top;
            var B = A.y + A.cy - 15;
            var u = z + p.parent.height();
            if (u > B) {
                z -= (u - B)
            }
            p.parent.css({
                top: z
            })
        }
    }
    var h = {
        x: 0,
        y: 0,
        cx: 0,
        cy: 0
    };

    function e() {
        h.x = window.scrollX;
        h.y = window.scrollY;
        h.cx = window.innerWidth;
        h.cy = window.innerHeight
    }
    e();
    i(window).resize(function () {
        e()
    });
    i(window).scroll(function () {
        e()
    });

    function j(u) {
        if (i.dbTooltip.blocked) {
            return
        }
        if (m) {
            clearTimeout(m)
        }
        q = null;
        var t = k(this);

        function s() {
            p.parent.removeClass(t.extraClass).hide().css("opacity", "")
        }
        if ((!f || !i.fn.bgiframe) && t.fade) {
            if (p.parent.is(":animated")) {
                p.parent.stop().fadeTo(t.fade, 0, s)
            } else {
                p.parent.stop().fadeOut(t.fade, s)
            }
        } else {
            s()
        }
    }
})(jQuery);

/* C:\Projects\Cobalt\Source\Curse.Ascalon.Web\Content\js\Ascalon\Ascalon.Tooltip.js */

function ImportCss(b) {
    if (document.createStyleSheet) {
        document.createStyleSheet(b)
    } else {
        var c = b;
        var a = document.createElement("link");
        a.rel = "stylesheet";
        a.type = "text/css";
        a.href = c;
        document.getElementsByTagName("head")[0].appendChild(a)
    }
}

function WP_LoadCss() {
    var a = null;
    jQuery("script").each(function (c, d) {
        var b = jQuery(d).attr("src");
        if (b != null && (b.indexOf("tt.js") != -1 || b.indexOf("Ascalon.Tooltip.js") != -1)) {
            a = b
        }
    });
    if (a != null) {
        a = a.substring(0, a.indexOf("/js/"));
        ImportCss(a + "/skins/Ascalon/css/tooltip.css")
    }
}

function WP_LoadTooltips(a) {
    if (a) {
        WP_LoadTooltipsElements(a.find("a, *[data-id]"))
    } else {
        WP_LoadTooltipsElements(jQuery("a, *[data-id]"))
    }
}

function WP_LoadTooltipsElements(a) {
    var b = /(.*?)?\/(skills|tasks|traits|items|recipes|achievements|creatures|boons|conditions|guildupgrades)\/([0-9]+)[\/a-z0-9\-]*(\?(simple|advanced))?(#([0-9]+)-([0-9]+))?/i;
    a.each(function () {
        var c = jQuery(this).attr("href") || jQuery(this).attr("data-id");
        if (!c) {
            return
        }
        c = c.split("?")[0];
        if (!c || c == location.href || (location.protocol + "//" + location.host + c) == location.href) {
            return
        }
        if (c.substr(0, 11) == "javascript:") {
            return
        }
        var j = c.match(b);
        if (!j) {
            if (jQuery(this).attr("href") && jQuery(this).attr("data-tooltip-href")) {
                c = jQuery(this).attr("data-tooltip-href");
                c = c.split("?")[0];
                if (!c || c == location.href || (location.protocol + "//" + location.host + c) == location.href) {
                    return
                }
                if (c.substr(0, 11) == "javascript:") {
                    return
                }
                j = c.match(b)
            }
            if (!j) {
                return
            }
        }
        var e = j[1] || ("http://" + window.location.host);
        var g = j[2];
        var h = j[3];
        var d = j[5];
        var i = j[7];
        var f = j[8];
        jQuery(this).dbTooltip({
            bodyHandler: function () {
                var k = g + "-" + h + "-" + i + "-" + f;
                WP_ActiveTooltip = k;
                return WP_Tooltips[k]
            },
            wowTooltip: function () {
                return {
                    HostName: e,
                    Type: g,
                    Id: h,
                    OldBuild: i,
                    NewBuild: f,
                    SimpleOrAdvanced: d
                }
            },
            loadingText: '<div class="db-tooltip"><div class="db-description" style="width: auto">Loading..</div></div>'
        })
    })
}

function WP_OnTooltipLoaded(b) {
    var e = b.Type + "-" + b.Id + "-" + b.OldBuild + "-" + b.NewBuild;
    if (typeof (b.OldBuild) != "undefined" && typeof (b.NewBuild) != "undefined") {
        var d = jQuery("<div/>").attr("id", "temp").html(b.Tooltip);
        var a = d.find(".db-tooltip");
        if (a.length == 3) {
            var c = new HTMLDiff(a[1], a[2]);
            c.diff();
            b.Tooltip = d.html()
        }
    }
    WP_Tooltips[e] = b.Tooltip;
    if (WP_ActiveTooltip == e) {
        jQuery.dbTooltip.change_html(b.Tooltip)
    }
    jQuery(document.body).trigger("mousemove")
}

function WP_Initialize() {
    WP_Tooltips = {};
    if (typeof (Cobalt) != "undefined") {
        Cobalt.runOnHtmlInsert(WP_LoadTooltips)
    } else {
        WP_LoadCss();
        WP_LoadTooltips()
    }
}
jQuery(document).ready(function () {
    WP_Initialize()
});
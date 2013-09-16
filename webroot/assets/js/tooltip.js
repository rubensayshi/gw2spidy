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
                    var s = A + "/" + C.Type + "/" + C.Id;
                    if (A === location.protocol + "//" + location.host) {
                        i.get(s, {}, function (D) {
                            WP_OnTooltipLoaded(D.result);
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

function WP_LoadTooltips(a) {
    if (a) {
        WP_LoadTooltipsElements(a.find("a, *[data-tooltip-id], *[data-tooltip-recipe]"))
    } else {
        WP_LoadTooltipsElements(jQuery("a, *[data-tooltip-id], *[data-tooltip-recipe]"))
    }
}

function WP_LoadTooltipsElements(a) {
    a.each(function () {
        var c = jQuery(this).attr("data-tooltip-id");
        if (!c) {
            var c = jQuery(this).attr("data-tooltip-recipe")
            if (!c) {
                return
            }
            else {
               var g = 'api/v0.9/json/recipe-tooltip';
            }
        }
        else {
            var g = 'api/v0.9/json/item-tooltip';
        }
        var j = "".split("?");
        var e = "http://" + window.location.host;
        var h = c;
        var d = null;
        var i = j[3];
        var f = j[2];
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
        WP_LoadTooltips()
    }
}
jQuery(document).ready(function () {
    WP_Initialize()
});
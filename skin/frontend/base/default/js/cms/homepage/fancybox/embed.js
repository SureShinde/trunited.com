/**
 * Created by anthony on 5/18/17.
 */
!function (e) {
    function t(n) {
        if (i[n])return i[n].exports;
        var o = i[n] = {exports: {}, id: n, loaded: !1};
        return e[n].call(o.exports, o, o.exports, t), o.loaded = !0, o.exports
    }

    var i = {};
    return t.m = e, t.c = i, t.p = "", t(0)
}([function (e, t, i) {
    function n() {
        window.videopath_lightbox || (window.videopath_lightbox = {
            initialized: !0,
            showVideo: r
        }, window.addEventListener("resize", d, !1), setInterval(o, 500), o())
    }

    function o() {
        for (var e = document.querySelectorAll(".vp_thumbnail"), t = function (e) {
            for (var t = 0; t < m.length; t++)if (e === m[t])return;
            m.push(e);
            var i = e.getAttribute("data-videopath-id");
            u.trackThumbnailLoad(i), e.addEventListener("click", function () {
                a(e)
            }, !1)
        }, i = 0; i < e.length; i++)t(e[i])
    }

    function r(e) {
        if (!v && e.id) {
            var t;
            for (t in h)t in e || (e[t] = h[t]);
            v = {config: e};
            var i = A.replace("{{video_id}}", e.id);
            e.custom_url && (i = e.custom_url), u.trackThumbnailClick(e.id), s.windowWidth() < b || g ? (window.location.href = i, v = null) : l(i)
        }
    }

    function a(e) {
        var t = {
            id: e.getAttribute("data-videopath-id"),
            retain_aspect: e.getAttribute("data-videopath-retain-aspect")
        };
        r(t)
    }

    function c() {
        if (v) {
            var e = v.element;
            e.className = "", setTimeout(function () {
                document.body.removeChild(e), v = null
            }, 300)
        }
    }

    function l(e) {
        var t = document.createElement("div"), i = p.replace("{{frame_src}}", e);
        t.id = "vp_lightbox", t.innerHTML = i, v.element = t, document.body.appendChild(t), d(), setTimeout(function () {
            t.className = "vp_show"
        }, 16), t.addEventListener("click", function () {
            c()
        }, !1);
        var n = t.getElementsByTagName("iframe")[0];
        if (n) {
            var o = function () {
                try {
                    n.focus(), n.contentWindow.focus(), n.contentWindow.document.body.focus()
                } catch (e) {
                }
            };
            setTimeout(o, 100), n.addEventListener("load", o, !1)
        }
    }

    function d() {
        if (v && v.element) {
            var e = s.windowWidth(), t = s.windowHeight(), i = v.element;
            i.style.height = t + "px";
            var n = v.config.retain_aspect;
            if (n) {
                var o, r, a = v.config.lightbox_margin / 100, c = 0, l = 0, d = e / t;
                d > n ? (o = n / d, r = Math.floor((1 - o) * e * .5), l = r) : (o = d / n, r = Math.floor((1 - o) * t * .5), c = r), c += a * t, l += a * e, c = Math.floor(c) + "px", l = Math.floor(l) + "px";
                var p = i.querySelector(".vp_frame_wrapper");
                p.style.top = p.style.bottom = c, p.style.left = p.style.right = l
            }
        }
    }

    i(1), i(2);
    var p = i(9), u = i(10), s = i(11), g = /Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent), b = 640, A = "http://player.videopath.com/{{video_id}}/index.html?autoplay=1", h = {
        id: !1,
        retain_aspect: 1.77,
        lightbox_margin: "3"
    }, v = null, m = [];
    s.onDocumentLoad(n)
}, function (e, t) {
    !window.addEventListener && function (e, t, i, n, o, r, a) {
        e[n] = t[n] = i[n] = function (e, t) {
            var i = this;
            a.unshift([i, e, t, function (e) {
                e.currentTarget = i, e.preventDefault = function () {
                    e.returnValue = !1
                }, e.stopPropagation = function () {
                    e.cancelBubble = !0
                }, e.target = e.srcElement || i, t.call(i, e)
            }]), this.attachEvent("on" + e, a[0][3])
        }, e[o] = t[o] = i[o] = function (e, t) {
            for (var i, n = 0; i = a[n]; ++n)if (i[0] == this && i[1] == e && i[2] == t)return this.detachEvent("on" + e, a.splice(n, 1)[0][3])
        }, e[r] = t[r] = i[r] = function (e) {
            return this.fireEvent("on" + e.type, e)
        }
    }(Window.prototype, HTMLDocument.prototype, Element.prototype, "addEventListener", "removeEventListener", "dispatchEvent", [])
}, function (e, t, i) {
    var n = i(3);
    "string" == typeof n && (n = [[e.id, n, ""]]);
    i(8)(n, {});
    n.locals && (e.exports = n.locals)
}, function (e, t, i) {
    t = e.exports = i(4)(), t.push([e.id, "#vp_lightbox .vp_close_button,.vp_thumbnail{background-position:center;background-repeat:no-repeat;cursor:pointer}.vp_thumbnail{position:relative;background-size:cover}.vp_thumbnail .vp_thumbnail_inner{position:relative;padding-bottom:56.25%;padding-top:25px}.vp_thumbnail .vp_play_button{opacity:.8;position:absolute;left:50%;top:50%;width:70px;height:50px;margin-top:-25px;margin-left:-35px;background-image:url(" + i(5) + ");background-size:25px 25px;background-repeat:no-repeat;background-position:center;border-radius:2px;background-color:red}.vp_thumbnail .vp_play_button.vp_dark{background-image:url(" + i(6) + ")}.vp_thumbnail:hover .vp_play_button{opacity:1}#vp_lightbox{z-index:9999;position:fixed;background-color:rgba(0,0,0,.7);top:0;left:0;width:100%;filter:alpha(Opacity=0);opacity:0;-moz-transition:opacity,.3s;-o-transition:opacity,.3s;-webkit-transition:opacity,.3s;transition:opacity,.3s}#vp_lightbox.vp_show{filter:alpha(enabled=false);opacity:1}#vp_lightbox .vp_frame_wrapper{-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box;position:absolute;left:5%;top:5%;right:5%;bottom:5%;border:3px solid #d3d3d3;background-color:#000;overflow:none}#vp_lightbox .vp_close_button{position:absolute;width:20px;height:20px;right:-11px;top:-11px;background-image:url(" + i(7) + ");z-index:1000;background-color:gray;background-size:8px 8px;border-radius:12px;border:2px solid #fff}#vp_lightbox .vp_close_button:hover,#vp_lightbox iframe{background-color:#000}#vp_lightbox iframe{margin:0;padding:0}", ""])
}, function (e, t) {
    e.exports = function () {
        var e = [];
        return e.toString = function () {
            for (var e = [], t = 0; t < this.length; t++) {
                var i = this[t];
                i[2] ? e.push("@media " + i[2] + "{" + i[1] + "}") : e.push(i[1])
            }
            return e.join("")
        }, e.i = function (t, i) {
            "string" == typeof t && (t = [[null, t, ""]]);
            for (var n = {}, o = 0; o < this.length; o++) {
                var r = this[o][0];
                "number" == typeof r && (n[r] = !0)
            }
            for (o = 0; o < t.length; o++) {
                var a = t[o];
                "number" == typeof a[0] && n[a[0]] || (i && !a[2] ? a[2] = i : i && (a[2] = "(" + a[2] + ") and (" + i + ")"), e.push(a))
            }
        }, e
    }
}, function (e, t) {
    e.exports = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCODczQkVFQTcwMDkxMUU1QTgzNTgzQ0VGNzY1QUIyNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCODczQkVFQjcwMDkxMUU1QTgzNTgzQ0VGNzY1QUIyNiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkI4NzNCRUU4NzAwOTExRTVBODM1ODNDRUY3NjVBQjI2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkI4NzNCRUU5NzAwOTExRTVBODM1ODNDRUY3NjVBQjI2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+RqwDuwAAAlFJREFUeNrUmksoBVEYx+dyKStsJFmIlPIo2XkkpSgLZEOkrIhSWEh5iyxQilgpJVkod4MiFigWlLyfxcKCElYUNf6n+51ium7X3HtnzvevX3cWZ87069wzZ74z49B1XTMkGWSDXXClcYkQ+UEGeNDdeQX9IMzQRklCDF41II6OI0EXuADVqg+IUSTcQ5tEMAc2QQ4XkU8vbQvADpj+MWrKiviSenAK2k2er4yISBQYBoegjLOITDpYAqt0zFZEphgcgXEaLbYiMs3gEjRxFxGJARNgn0aKrYhMFs2dRZDCWUSmApyAIXpaYCsiEgo6SKiOs4hMPJgBWyCXs4hMHtgGsyCBs4hMLTgDnSCMs4hIBBgA56CKs4hMEpgH62bKBZVEZAqpXJgEsZxFZBpp/rQAB2cRkWgw5ku5oLqITAaVC66/ygUuIjKlVC60AidnEZlRzb3jw15E5I27yD0opznDVkSUAmk06X/FyURAFGf94PivBqqLHNID5TLXBfGFbrGZvkioOiJToA88/ucklUQ26G+0Z+ZkFURuSWDBn07sFPmi26nYQ373tzO7REQB1QuuA9Wh1SLi/98D1gLdsVUiT7SgTQbrAlaIjNPmwnMwLxJMkRXQDQ6sGPJgrOzitUIlKLFKItAj8kHzYETz/lJVaZFZup3e2bUo+SsiNqO76NfWmJ0jD5r7NXW+ChJmRkSnOTBorJlVEwn30tZFq/KRigWM08Odx5hjWg9cSteShs+FUsENfeb0DNo4fOIkcHj48KwBFNED3pgda4KZfAswAINOlqWYba/XAAAAAElFTkSuQmCC"
}, function (e, t) {
    e.exports = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCODczQkVFRTcwMDkxMUU1QTgzNTgzQ0VGNzY1QUIyNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCODczQkVFRjcwMDkxMUU1QTgzNTgzQ0VGNzY1QUIyNiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkI4NzNCRUVDNzAwOTExRTVBODM1ODNDRUY3NjVBQjI2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkI4NzNCRUVENzAwOTExRTVBODM1ODNDRUY3NjVBQjI2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+yKLsWgAAAmpJREFUeNrUmU9IFFEcx9/WKnTSLiHRQRKhgwbSnrKQQDDwkNIlMYROQoFgCBG09keMDhUoLXYSVyI8CO6lhMSIFIxYYdlts5IgDx4UKjwpGEzfH/t7sg27yzazM/N+X/iwAzszy4e3v3nv9yZkWZbSiUQi9NEIzoIV8E0ZnGQyeXB8yPbdafAOTIGP4AGoUgJiF7kKjvNxDYiCL6BXmkh1gXNOghfgLWiVIrJf4twLYBk8zxs1Y0XKST/IglsOrzdGhFILHoEU6JIsotMM5sA8H4sV0bkI0mCMR0usiM4A+ApuSBehHAPPaOLlkRIronOGa2cWnJIsonMZfAIPebUgVoRyGNxmoWuSRXROgEnwHpyTLKJzHiyBOKiXLKLTBz6DO27bBRPWSkfACFgDPZJFdBrAS7DgpF0wSUSnnduFGKiTLKJznetnEIQki1COgqfltAumi+RvilC7kCjWLkgR0bnE7cJNEJYsovNE5XZ8xItQdqSLbIBurhmxItQKNHHR/5OwEAFqzmj7NlPsBNNFUrygfCV1QvzNj9iWciRMHZEJcB9s/c9FJoks8t/og5OLTRD5zgIzbm4SpMgffpzSHvKu25sFJUIN1D2wXqkb+i1C//+74E2lb+yXyDZPaDGvfsAPkTHeXPjp5Y94KfIaDINVP4bci5mdXitcAZ1+SVR6RPa4Dh6r0i9VjRaJ8+P0R1CTklsR2oyO8megcVojmyr3mrrNBAknI2JxDYzae2bTRKpLnJvgWTltYgMTLvDksSfD80HC5FbSXiPTvKym/AJDKrfLZ7REoRHJcg108AJvXMjmhPorwAD4w2t9WE3TLQAAAABJRU5ErkJggg=="
}, function (e, t) {
    e.exports = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAOCAYAAADwikbvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0ODUxMzkwMzcwMDgxMUU1QTgzNTgzQ0VGNzY1QUIyNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0ODUxMzkwNDcwMDgxMUU1QTgzNTgzQ0VGNzY1QUIyNiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQ4NTEzOTAxNzAwODExRTVBODM1ODNDRUY3NjVBQjI2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQ4NTEzOTAyNzAwODExRTVBODM1ODNDRUY3NjVBQjI2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+1QpsLQAAAPNJREFUeNpi/P//fxIDA4MPEKsD8Qkg3gLE6xlQgScQWwNxPBDzAfEaIN7FANS87j8qWAzEPkDMAMVuQNyCpuYfEDeyAE0QRrMlBkr/BeKvQOwIxBUMmOAXSPNeILbDYgBI80sgLsOicSkQX2OAOhHdWfgAyFt+IC8h+6sdi8J/+MKDASlgnAm4YCnUErgeJgbc4D8U41GB39n/CTnbh0iNGAEGiiodHPG4EIhfAHE5lmgEeYcZpNkdi8YlQLwSmkj+YzEcZMBNkOb3ODRuh/I5oAmmGk0dGws0I4BM1wDi40C8FSoGA7tATgTiFrSMcQ0gwAAHjXIAoSc03QAAAABJRU5ErkJggg=="
}, function (e, t, i) {
    function n(e, t) {
        for (var i = 0; i < e.length; i++) {
            var n = e[i], o = u[n.id];
            if (o) {
                o.refs++;
                for (var r = 0; r < o.parts.length; r++)o.parts[r](n.parts[r]);
                for (; r < n.parts.length; r++)o.parts.push(c(n.parts[r], t))
            } else {
                for (var a = [], r = 0; r < n.parts.length; r++)a.push(c(n.parts[r], t));
                u[n.id] = {id: n.id, refs: 1, parts: a}
            }
        }
    }

    function o(e) {
        for (var t = [], i = {}, n = 0; n < e.length; n++) {
            var o = e[n], r = o[0], a = o[1], c = o[2], l = o[3], d = {css: a, media: c, sourceMap: l};
            i[r] ? i[r].parts.push(d) : t.push(i[r] = {id: r, parts: [d]})
        }
        return t
    }

    function r() {
        var e = document.createElement("style"), t = b();
        return e.type = "text/css", t.appendChild(e), e
    }

    function a() {
        var e = document.createElement("link"), t = b();
        return e.rel = "stylesheet", t.appendChild(e), e
    }

    function c(e, t) {
        var i, n, o;
        if (t.singleton) {
            var c = h++;
            i = A || (A = r()), n = l.bind(null, i, c, !1), o = l.bind(null, i, c, !0)
        } else e.sourceMap && "function" == typeof URL && "function" == typeof URL.createObjectURL && "function" == typeof URL.revokeObjectURL && "function" == typeof Blob && "function" == typeof btoa ? (i = a(), n = p.bind(null, i), o = function () {
            i.parentNode.removeChild(i), i.href && URL.revokeObjectURL(i.href)
        }) : (i = r(), n = d.bind(null, i), o = function () {
            i.parentNode.removeChild(i)
        });
        return n(e), function (t) {
            if (t) {
                if (t.css === e.css && t.media === e.media && t.sourceMap === e.sourceMap)return;
                n(e = t)
            } else o()
        }
    }

    function l(e, t, i, n) {
        var o = i ? "" : n.css;
        if (e.styleSheet)e.styleSheet.cssText = v(t, o); else {
            var r = document.createTextNode(o), a = e.childNodes;
            a[t] && e.removeChild(a[t]), a.length ? e.insertBefore(r, a[t]) : e.appendChild(r)
        }
    }

    function d(e, t) {
        var i = t.css, n = t.media;
        t.sourceMap;
        if (n && e.setAttribute("media", n), e.styleSheet)e.styleSheet.cssText = i; else {
            for (; e.firstChild;)e.removeChild(e.firstChild);
            e.appendChild(document.createTextNode(i))
        }
    }

    function p(e, t) {
        var i = t.css, n = (t.media, t.sourceMap);
        n && (i += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(n)))) + " */");
        var o = new Blob([i], {type: "text/css"}), r = e.href;
        e.href = URL.createObjectURL(o), r && URL.revokeObjectURL(r)
    }

    var u = {}, s = function (e) {
        var t;
        return function () {
            return "undefined" == typeof t && (t = e.apply(this, arguments)), t
        }
    }, g = s(function () {
        return /msie [6-9]\b/.test(window.navigator.userAgent.toLowerCase())
    }), b = s(function () {
        return document.head || document.getElementsByTagName("head")[0]
    }), A = null, h = 0;
    e.exports = function (e, t) {
        t = t || {}, "undefined" == typeof t.singleton && (t.singleton = g());
        var i = o(e);
        return n(i, t), function (e) {
            for (var r = [], a = 0; a < i.length; a++) {
                var c = i[a], l = u[c.id];
                l.refs--, r.push(l)
            }
            if (e) {
                var d = o(e);
                n(d, t)
            }
            for (var a = 0; a < r.length; a++) {
                var l = r[a];
                if (0 === l.refs) {
                    for (var p = 0; p < l.parts.length; p++)l.parts[p]();
                    delete u[l.id]
                }
            }
        }
    };
    var v = function () {
        var e = [];
        return function (t, i) {
            return e[t] = i, e.filter(Boolean).join("\n")
        }
    }()
}, function (e, t) {
    e.exports = "<div class=vp_frame_wrapper><div class=vp_close_button></div><iframe allowtransparency=true width=100% height=100% frameborder=0 allowfullscreen onmousewheel=event.preventDefault() src={{frame_src}}></iframe></div>"
}, 

function (e, t) {
    function i(e, t) {
        var i, n = {
            v: "1",
            tid: "UA-46402960-4",
            t: "event",
            cid: a,
            cd1: t,
            z: Math.random(),
            aip: "1",
            dl: (document.location + "").substring(0, 2e3),
            ec: "embed actions",
            ea: e
        }, o = r;
        for (i in n)o += i + "=" + encodeURIComponent(n[i]) + "&";
        "https:" == document.location.protocol && (o = o.replace("www", "ssl"));
        var c = document.createElement("img");
        c.width = 1, c.height = 1, c.src = o
    }

    function n(e) {
        try {
            //i("thumbnail clicked", e)
        } catch (t) {
        }
    }

    function o(e) {
        //i("thumbnail loaded", e)
    }

    var r = "//www.google-analytics.com/collect?", a = Math.round(1e6 * Math.random());
    e.exports = {trackThumbnailLoad: o, trackThumbnailClick: n}
}, 

function (e, t) {
    function i() {
        return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth
    }

    function n() {
        return window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight
    }

    function o(e) {
        document.addEventListener("DOMContentLoaded", e), document.onreadystatechange = function () {
            "complete" == document.readyState && e()
        }, "complete" == document.readyState && e()
    }

    e.exports = {windowWidth: i, windowHeight: n, onDocumentLoad: o}
}]);

/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunk"] = self["webpackChunk"] || []).push([["vendor/cookiejar"],{

/***/ "./node_modules/cookiejar/cookiejar.js":
/*!*********************************************!*\
  !*** ./node_modules/cookiejar/cookiejar.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, exports) => {

eval("/* jshint node: true */\n(function () {\n    \"use strict\";\n\n    function CookieAccessInfo(domain, path, secure, script) {\n        if (this instanceof CookieAccessInfo) {\n            this.domain = domain || undefined;\n            this.path = path || \"/\";\n            this.secure = !!secure;\n            this.script = !!script;\n            return this;\n        }\n        return new CookieAccessInfo(domain, path, secure, script);\n    }\n    CookieAccessInfo.All = Object.freeze(Object.create(null));\n    exports.CookieAccessInfo = CookieAccessInfo;\n\n    function Cookie(cookiestr, request_domain, request_path) {\n        if (cookiestr instanceof Cookie) {\n            return cookiestr;\n        }\n        if (this instanceof Cookie) {\n            this.name = null;\n            this.value = null;\n            this.expiration_date = Infinity;\n            this.path = String(request_path || \"/\");\n            this.explicit_path = false;\n            this.domain = request_domain || null;\n            this.explicit_domain = false;\n            this.secure = false; //how to define default?\n            this.noscript = false; //httponly\n            if (cookiestr) {\n                this.parse(cookiestr, request_domain, request_path);\n            }\n            return this;\n        }\n        return new Cookie(cookiestr, request_domain, request_path);\n    }\n    exports.Cookie = Cookie;\n\n    Cookie.prototype.toString = function toString() {\n        var str = [this.name + \"=\" + this.value];\n        if (this.expiration_date !== Infinity) {\n            str.push(\"expires=\" + (new Date(this.expiration_date)).toGMTString());\n        }\n        if (this.domain) {\n            str.push(\"domain=\" + this.domain);\n        }\n        if (this.path) {\n            str.push(\"path=\" + this.path);\n        }\n        if (this.secure) {\n            str.push(\"secure\");\n        }\n        if (this.noscript) {\n            str.push(\"httponly\");\n        }\n        return str.join(\"; \");\n    };\n\n    Cookie.prototype.toValueString = function toValueString() {\n        return this.name + \"=\" + this.value;\n    };\n\n    var cookie_str_splitter = /[:](?=\\s*[a-zA-Z0-9_\\-]+\\s*[=])/g;\n    Cookie.prototype.parse = function parse(str, request_domain, request_path) {\n        if (this instanceof Cookie) {\n            var parts = str.split(\";\").filter(function (value) {\n                    return !!value;\n                });\n            var i;\n\n            var pair = parts[0].match(/([^=]+)=([\\s\\S]*)/);\n            if (!pair) {\n                console.warn(\"Invalid cookie header encountered. Header: '\"+str+\"'\");\n                return;\n            }\n\n            var key = pair[1];\n            var value = pair[2];\n            if ( typeof key !== 'string' || key.length === 0 || typeof value !== 'string' ) {\n                console.warn(\"Unable to extract values from cookie header. Cookie: '\"+str+\"'\");\n                return;\n            }\n\n            this.name = key;\n            this.value = value;\n\n            for (i = 1; i < parts.length; i += 1) {\n                pair = parts[i].match(/([^=]+)(?:=([\\s\\S]*))?/);\n                key = pair[1].trim().toLowerCase();\n                value = pair[2];\n                switch (key) {\n                case \"httponly\":\n                    this.noscript = true;\n                    break;\n                case \"expires\":\n                    this.expiration_date = value ?\n                            Number(Date.parse(value)) :\n                            Infinity;\n                    break;\n                case \"path\":\n                    this.path = value ?\n                            value.trim() :\n                            \"\";\n                    this.explicit_path = true;\n                    break;\n                case \"domain\":\n                    this.domain = value ?\n                            value.trim() :\n                            \"\";\n                    this.explicit_domain = !!this.domain;\n                    break;\n                case \"secure\":\n                    this.secure = true;\n                    break;\n                }\n            }\n\n            if (!this.explicit_path) {\n               this.path = request_path || \"/\";\n            }\n            if (!this.explicit_domain) {\n               this.domain = request_domain;\n            }\n\n            return this;\n        }\n        return new Cookie().parse(str, request_domain, request_path);\n    };\n\n    Cookie.prototype.matches = function matches(access_info) {\n        if (access_info === CookieAccessInfo.All) {\n          return true;\n        }\n        if (this.noscript && access_info.script ||\n                this.secure && !access_info.secure ||\n                !this.collidesWith(access_info)) {\n            return false;\n        }\n        return true;\n    };\n\n    Cookie.prototype.collidesWith = function collidesWith(access_info) {\n        if ((this.path && !access_info.path) || (this.domain && !access_info.domain)) {\n            return false;\n        }\n        if (this.path && access_info.path.indexOf(this.path) !== 0) {\n            return false;\n        }\n        if (this.explicit_path && access_info.path.indexOf( this.path ) !== 0) {\n           return false;\n        }\n        var access_domain = access_info.domain && access_info.domain.replace(/^[\\.]/,'');\n        var cookie_domain = this.domain && this.domain.replace(/^[\\.]/,'');\n        if (cookie_domain === access_domain) {\n            return true;\n        }\n        if (cookie_domain) {\n            if (!this.explicit_domain) {\n                return false; // we already checked if the domains were exactly the same\n            }\n            var wildcard = access_domain.indexOf(cookie_domain);\n            if (wildcard === -1 || wildcard !== access_domain.length - cookie_domain.length) {\n                return false;\n            }\n            return true;\n        }\n        return true;\n    };\n\n    function CookieJar() {\n        var cookies, cookies_list, collidable_cookie;\n        if (this instanceof CookieJar) {\n            cookies = Object.create(null); //name: [Cookie]\n\n            this.setCookie = function setCookie(cookie, request_domain, request_path) {\n                var remove, i;\n                cookie = new Cookie(cookie, request_domain, request_path);\n                //Delete the cookie if the set is past the current time\n                remove = cookie.expiration_date <= Date.now();\n                if (cookies[cookie.name] !== undefined) {\n                    cookies_list = cookies[cookie.name];\n                    for (i = 0; i < cookies_list.length; i += 1) {\n                        collidable_cookie = cookies_list[i];\n                        if (collidable_cookie.collidesWith(cookie)) {\n                            if (remove) {\n                                cookies_list.splice(i, 1);\n                                if (cookies_list.length === 0) {\n                                    delete cookies[cookie.name];\n                                }\n                                return false;\n                            }\n                            cookies_list[i] = cookie;\n                            return cookie;\n                        }\n                    }\n                    if (remove) {\n                        return false;\n                    }\n                    cookies_list.push(cookie);\n                    return cookie;\n                }\n                if (remove) {\n                    return false;\n                }\n                cookies[cookie.name] = [cookie];\n                return cookies[cookie.name];\n            };\n            //returns a cookie\n            this.getCookie = function getCookie(cookie_name, access_info) {\n                var cookie, i;\n                cookies_list = cookies[cookie_name];\n                if (!cookies_list) {\n                    return;\n                }\n                for (i = 0; i < cookies_list.length; i += 1) {\n                    cookie = cookies_list[i];\n                    if (cookie.expiration_date <= Date.now()) {\n                        if (cookies_list.length === 0) {\n                            delete cookies[cookie.name];\n                        }\n                        continue;\n                    }\n\n                    if (cookie.matches(access_info)) {\n                        return cookie;\n                    }\n                }\n            };\n            //returns a list of cookies\n            this.getCookies = function getCookies(access_info) {\n                var matches = [], cookie_name, cookie;\n                for (cookie_name in cookies) {\n                    cookie = this.getCookie(cookie_name, access_info);\n                    if (cookie) {\n                        matches.push(cookie);\n                    }\n                }\n                matches.toString = function toString() {\n                    return matches.join(\":\");\n                };\n                matches.toValueString = function toValueString() {\n                    return matches.map(function (c) {\n                        return c.toValueString();\n                    }).join(';');\n                };\n                return matches;\n            };\n\n            return this;\n        }\n        return new CookieJar();\n    }\n    exports.CookieJar = CookieJar;\n\n    //returns list of cookies that were set correctly. Cookies that are expired and removed are not returned.\n    CookieJar.prototype.setCookies = function setCookies(cookies, request_domain, request_path) {\n        cookies = Array.isArray(cookies) ?\n                cookies :\n                cookies.split(cookie_str_splitter);\n        var successful = [],\n            i,\n            cookie;\n        cookies = cookies.map(function(item){\n            return new Cookie(item, request_domain, request_path);\n        });\n        for (i = 0; i < cookies.length; i += 1) {\n            cookie = cookies[i];\n            if (this.setCookie(cookie, request_domain, request_path)) {\n                successful.push(cookie);\n            }\n        }\n        return successful;\n    };\n}());\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29va2llamFyL2Nvb2tpZWphci5qcz9hNmNhIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsSUFBSSx3QkFBd0I7O0FBRTVCO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGdDQUFnQztBQUNoQyxrQ0FBa0M7QUFDbEM7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxJQUFJLGNBQWM7O0FBRWxCO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSwwQkFBMEI7QUFDMUI7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG9DQUFvQztBQUNwQztBQUNBLGlCQUFpQjtBQUNqQjs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBLHVCQUF1QixrQkFBa0I7QUFDekM7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsNkJBQTZCO0FBQzdCO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSwwQ0FBMEM7O0FBRTFDO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsK0JBQStCLHlCQUF5QjtBQUN4RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsMkJBQTJCLHlCQUF5QjtBQUNwRDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EscUJBQXFCLFNBQVM7QUFDOUI7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsSUFBSSxpQkFBaUI7O0FBRXJCO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsU0FBUztBQUNULG1CQUFtQixvQkFBb0I7QUFDdkM7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDIiwiZmlsZSI6Ii4vbm9kZV9tb2R1bGVzL2Nvb2tpZWphci9jb29raWVqYXIuanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiBqc2hpbnQgbm9kZTogdHJ1ZSAqL1xuKGZ1bmN0aW9uICgpIHtcbiAgICBcInVzZSBzdHJpY3RcIjtcblxuICAgIGZ1bmN0aW9uIENvb2tpZUFjY2Vzc0luZm8oZG9tYWluLCBwYXRoLCBzZWN1cmUsIHNjcmlwdCkge1xuICAgICAgICBpZiAodGhpcyBpbnN0YW5jZW9mIENvb2tpZUFjY2Vzc0luZm8pIHtcbiAgICAgICAgICAgIHRoaXMuZG9tYWluID0gZG9tYWluIHx8IHVuZGVmaW5lZDtcbiAgICAgICAgICAgIHRoaXMucGF0aCA9IHBhdGggfHwgXCIvXCI7XG4gICAgICAgICAgICB0aGlzLnNlY3VyZSA9ICEhc2VjdXJlO1xuICAgICAgICAgICAgdGhpcy5zY3JpcHQgPSAhIXNjcmlwdDtcbiAgICAgICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBuZXcgQ29va2llQWNjZXNzSW5mbyhkb21haW4sIHBhdGgsIHNlY3VyZSwgc2NyaXB0KTtcbiAgICB9XG4gICAgQ29va2llQWNjZXNzSW5mby5BbGwgPSBPYmplY3QuZnJlZXplKE9iamVjdC5jcmVhdGUobnVsbCkpO1xuICAgIGV4cG9ydHMuQ29va2llQWNjZXNzSW5mbyA9IENvb2tpZUFjY2Vzc0luZm87XG5cbiAgICBmdW5jdGlvbiBDb29raWUoY29va2llc3RyLCByZXF1ZXN0X2RvbWFpbiwgcmVxdWVzdF9wYXRoKSB7XG4gICAgICAgIGlmIChjb29raWVzdHIgaW5zdGFuY2VvZiBDb29raWUpIHtcbiAgICAgICAgICAgIHJldHVybiBjb29raWVzdHI7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRoaXMgaW5zdGFuY2VvZiBDb29raWUpIHtcbiAgICAgICAgICAgIHRoaXMubmFtZSA9IG51bGw7XG4gICAgICAgICAgICB0aGlzLnZhbHVlID0gbnVsbDtcbiAgICAgICAgICAgIHRoaXMuZXhwaXJhdGlvbl9kYXRlID0gSW5maW5pdHk7XG4gICAgICAgICAgICB0aGlzLnBhdGggPSBTdHJpbmcocmVxdWVzdF9wYXRoIHx8IFwiL1wiKTtcbiAgICAgICAgICAgIHRoaXMuZXhwbGljaXRfcGF0aCA9IGZhbHNlO1xuICAgICAgICAgICAgdGhpcy5kb21haW4gPSByZXF1ZXN0X2RvbWFpbiB8fCBudWxsO1xuICAgICAgICAgICAgdGhpcy5leHBsaWNpdF9kb21haW4gPSBmYWxzZTtcbiAgICAgICAgICAgIHRoaXMuc2VjdXJlID0gZmFsc2U7IC8vaG93IHRvIGRlZmluZSBkZWZhdWx0P1xuICAgICAgICAgICAgdGhpcy5ub3NjcmlwdCA9IGZhbHNlOyAvL2h0dHBvbmx5XG4gICAgICAgICAgICBpZiAoY29va2llc3RyKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5wYXJzZShjb29raWVzdHIsIHJlcXVlc3RfZG9tYWluLCByZXF1ZXN0X3BhdGgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIG5ldyBDb29raWUoY29va2llc3RyLCByZXF1ZXN0X2RvbWFpbiwgcmVxdWVzdF9wYXRoKTtcbiAgICB9XG4gICAgZXhwb3J0cy5Db29raWUgPSBDb29raWU7XG5cbiAgICBDb29raWUucHJvdG90eXBlLnRvU3RyaW5nID0gZnVuY3Rpb24gdG9TdHJpbmcoKSB7XG4gICAgICAgIHZhciBzdHIgPSBbdGhpcy5uYW1lICsgXCI9XCIgKyB0aGlzLnZhbHVlXTtcbiAgICAgICAgaWYgKHRoaXMuZXhwaXJhdGlvbl9kYXRlICE9PSBJbmZpbml0eSkge1xuICAgICAgICAgICAgc3RyLnB1c2goXCJleHBpcmVzPVwiICsgKG5ldyBEYXRlKHRoaXMuZXhwaXJhdGlvbl9kYXRlKSkudG9HTVRTdHJpbmcoKSk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRoaXMuZG9tYWluKSB7XG4gICAgICAgICAgICBzdHIucHVzaChcImRvbWFpbj1cIiArIHRoaXMuZG9tYWluKTtcbiAgICAgICAgfVxuICAgICAgICBpZiAodGhpcy5wYXRoKSB7XG4gICAgICAgICAgICBzdHIucHVzaChcInBhdGg9XCIgKyB0aGlzLnBhdGgpO1xuICAgICAgICB9XG4gICAgICAgIGlmICh0aGlzLnNlY3VyZSkge1xuICAgICAgICAgICAgc3RyLnB1c2goXCJzZWN1cmVcIik7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRoaXMubm9zY3JpcHQpIHtcbiAgICAgICAgICAgIHN0ci5wdXNoKFwiaHR0cG9ubHlcIik7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHN0ci5qb2luKFwiOyBcIik7XG4gICAgfTtcblxuICAgIENvb2tpZS5wcm90b3R5cGUudG9WYWx1ZVN0cmluZyA9IGZ1bmN0aW9uIHRvVmFsdWVTdHJpbmcoKSB7XG4gICAgICAgIHJldHVybiB0aGlzLm5hbWUgKyBcIj1cIiArIHRoaXMudmFsdWU7XG4gICAgfTtcblxuICAgIHZhciBjb29raWVfc3RyX3NwbGl0dGVyID0gL1s6XSg/PVxccypbYS16QS1aMC05X1xcLV0rXFxzKls9XSkvZztcbiAgICBDb29raWUucHJvdG90eXBlLnBhcnNlID0gZnVuY3Rpb24gcGFyc2Uoc3RyLCByZXF1ZXN0X2RvbWFpbiwgcmVxdWVzdF9wYXRoKSB7XG4gICAgICAgIGlmICh0aGlzIGluc3RhbmNlb2YgQ29va2llKSB7XG4gICAgICAgICAgICB2YXIgcGFydHMgPSBzdHIuc3BsaXQoXCI7XCIpLmZpbHRlcihmdW5jdGlvbiAodmFsdWUpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuICEhdmFsdWU7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB2YXIgaTtcblxuICAgICAgICAgICAgdmFyIHBhaXIgPSBwYXJ0c1swXS5tYXRjaCgvKFtePV0rKT0oW1xcc1xcU10qKS8pO1xuICAgICAgICAgICAgaWYgKCFwYWlyKSB7XG4gICAgICAgICAgICAgICAgY29uc29sZS53YXJuKFwiSW52YWxpZCBjb29raWUgaGVhZGVyIGVuY291bnRlcmVkLiBIZWFkZXI6ICdcIitzdHIrXCInXCIpO1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyIGtleSA9IHBhaXJbMV07XG4gICAgICAgICAgICB2YXIgdmFsdWUgPSBwYWlyWzJdO1xuICAgICAgICAgICAgaWYgKCB0eXBlb2Yga2V5ICE9PSAnc3RyaW5nJyB8fCBrZXkubGVuZ3RoID09PSAwIHx8IHR5cGVvZiB2YWx1ZSAhPT0gJ3N0cmluZycgKSB7XG4gICAgICAgICAgICAgICAgY29uc29sZS53YXJuKFwiVW5hYmxlIHRvIGV4dHJhY3QgdmFsdWVzIGZyb20gY29va2llIGhlYWRlci4gQ29va2llOiAnXCIrc3RyK1wiJ1wiKTtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMubmFtZSA9IGtleTtcbiAgICAgICAgICAgIHRoaXMudmFsdWUgPSB2YWx1ZTtcblxuICAgICAgICAgICAgZm9yIChpID0gMTsgaSA8IHBhcnRzLmxlbmd0aDsgaSArPSAxKSB7XG4gICAgICAgICAgICAgICAgcGFpciA9IHBhcnRzW2ldLm1hdGNoKC8oW149XSspKD86PShbXFxzXFxTXSopKT8vKTtcbiAgICAgICAgICAgICAgICBrZXkgPSBwYWlyWzFdLnRyaW0oKS50b0xvd2VyQ2FzZSgpO1xuICAgICAgICAgICAgICAgIHZhbHVlID0gcGFpclsyXTtcbiAgICAgICAgICAgICAgICBzd2l0Y2ggKGtleSkge1xuICAgICAgICAgICAgICAgIGNhc2UgXCJodHRwb25seVwiOlxuICAgICAgICAgICAgICAgICAgICB0aGlzLm5vc2NyaXB0ID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSBcImV4cGlyZXNcIjpcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5leHBpcmF0aW9uX2RhdGUgPSB2YWx1ZSA/XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgTnVtYmVyKERhdGUucGFyc2UodmFsdWUpKSA6XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgSW5maW5pdHk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgXCJwYXRoXCI6XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucGF0aCA9IHZhbHVlID9cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZS50cmltKCkgOlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIFwiXCI7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZXhwbGljaXRfcGF0aCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgXCJkb21haW5cIjpcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5kb21haW4gPSB2YWx1ZSA/XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWUudHJpbSgpIDpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBcIlwiO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmV4cGxpY2l0X2RvbWFpbiA9ICEhdGhpcy5kb21haW47XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgXCJzZWN1cmVcIjpcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZWN1cmUgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICghdGhpcy5leHBsaWNpdF9wYXRoKSB7XG4gICAgICAgICAgICAgICB0aGlzLnBhdGggPSByZXF1ZXN0X3BhdGggfHwgXCIvXCI7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAoIXRoaXMuZXhwbGljaXRfZG9tYWluKSB7XG4gICAgICAgICAgICAgICB0aGlzLmRvbWFpbiA9IHJlcXVlc3RfZG9tYWluO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gbmV3IENvb2tpZSgpLnBhcnNlKHN0ciwgcmVxdWVzdF9kb21haW4sIHJlcXVlc3RfcGF0aCk7XG4gICAgfTtcblxuICAgIENvb2tpZS5wcm90b3R5cGUubWF0Y2hlcyA9IGZ1bmN0aW9uIG1hdGNoZXMoYWNjZXNzX2luZm8pIHtcbiAgICAgICAgaWYgKGFjY2Vzc19pbmZvID09PSBDb29raWVBY2Nlc3NJbmZvLkFsbCkge1xuICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIGlmICh0aGlzLm5vc2NyaXB0ICYmIGFjY2Vzc19pbmZvLnNjcmlwdCB8fFxuICAgICAgICAgICAgICAgIHRoaXMuc2VjdXJlICYmICFhY2Nlc3NfaW5mby5zZWN1cmUgfHxcbiAgICAgICAgICAgICAgICAhdGhpcy5jb2xsaWRlc1dpdGgoYWNjZXNzX2luZm8pKSB7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgfTtcblxuICAgIENvb2tpZS5wcm90b3R5cGUuY29sbGlkZXNXaXRoID0gZnVuY3Rpb24gY29sbGlkZXNXaXRoKGFjY2Vzc19pbmZvKSB7XG4gICAgICAgIGlmICgodGhpcy5wYXRoICYmICFhY2Nlc3NfaW5mby5wYXRoKSB8fCAodGhpcy5kb21haW4gJiYgIWFjY2Vzc19pbmZvLmRvbWFpbikpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuICAgICAgICBpZiAodGhpcy5wYXRoICYmIGFjY2Vzc19pbmZvLnBhdGguaW5kZXhPZih0aGlzLnBhdGgpICE9PSAwKSB7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHRoaXMuZXhwbGljaXRfcGF0aCAmJiBhY2Nlc3NfaW5mby5wYXRoLmluZGV4T2YoIHRoaXMucGF0aCApICE9PSAwKSB7XG4gICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuICAgICAgICB2YXIgYWNjZXNzX2RvbWFpbiA9IGFjY2Vzc19pbmZvLmRvbWFpbiAmJiBhY2Nlc3NfaW5mby5kb21haW4ucmVwbGFjZSgvXltcXC5dLywnJyk7XG4gICAgICAgIHZhciBjb29raWVfZG9tYWluID0gdGhpcy5kb21haW4gJiYgdGhpcy5kb21haW4ucmVwbGFjZSgvXltcXC5dLywnJyk7XG4gICAgICAgIGlmIChjb29raWVfZG9tYWluID09PSBhY2Nlc3NfZG9tYWluKSB7XG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoY29va2llX2RvbWFpbikge1xuICAgICAgICAgICAgaWYgKCF0aGlzLmV4cGxpY2l0X2RvbWFpbikge1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTsgLy8gd2UgYWxyZWFkeSBjaGVja2VkIGlmIHRoZSBkb21haW5zIHdlcmUgZXhhY3RseSB0aGUgc2FtZVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgdmFyIHdpbGRjYXJkID0gYWNjZXNzX2RvbWFpbi5pbmRleE9mKGNvb2tpZV9kb21haW4pO1xuICAgICAgICAgICAgaWYgKHdpbGRjYXJkID09PSAtMSB8fCB3aWxkY2FyZCAhPT0gYWNjZXNzX2RvbWFpbi5sZW5ndGggLSBjb29raWVfZG9tYWluLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgIH07XG5cbiAgICBmdW5jdGlvbiBDb29raWVKYXIoKSB7XG4gICAgICAgIHZhciBjb29raWVzLCBjb29raWVzX2xpc3QsIGNvbGxpZGFibGVfY29va2llO1xuICAgICAgICBpZiAodGhpcyBpbnN0YW5jZW9mIENvb2tpZUphcikge1xuICAgICAgICAgICAgY29va2llcyA9IE9iamVjdC5jcmVhdGUobnVsbCk7IC8vbmFtZTogW0Nvb2tpZV1cblxuICAgICAgICAgICAgdGhpcy5zZXRDb29raWUgPSBmdW5jdGlvbiBzZXRDb29raWUoY29va2llLCByZXF1ZXN0X2RvbWFpbiwgcmVxdWVzdF9wYXRoKSB7XG4gICAgICAgICAgICAgICAgdmFyIHJlbW92ZSwgaTtcbiAgICAgICAgICAgICAgICBjb29raWUgPSBuZXcgQ29va2llKGNvb2tpZSwgcmVxdWVzdF9kb21haW4sIHJlcXVlc3RfcGF0aCk7XG4gICAgICAgICAgICAgICAgLy9EZWxldGUgdGhlIGNvb2tpZSBpZiB0aGUgc2V0IGlzIHBhc3QgdGhlIGN1cnJlbnQgdGltZVxuICAgICAgICAgICAgICAgIHJlbW92ZSA9IGNvb2tpZS5leHBpcmF0aW9uX2RhdGUgPD0gRGF0ZS5ub3coKTtcbiAgICAgICAgICAgICAgICBpZiAoY29va2llc1tjb29raWUubmFtZV0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICBjb29raWVzX2xpc3QgPSBjb29raWVzW2Nvb2tpZS5uYW1lXTtcbiAgICAgICAgICAgICAgICAgICAgZm9yIChpID0gMDsgaSA8IGNvb2tpZXNfbGlzdC5sZW5ndGg7IGkgKz0gMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29sbGlkYWJsZV9jb29raWUgPSBjb29raWVzX2xpc3RbaV07XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoY29sbGlkYWJsZV9jb29raWUuY29sbGlkZXNXaXRoKGNvb2tpZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAocmVtb3ZlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvb2tpZXNfbGlzdC5zcGxpY2UoaSwgMSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb29raWVzX2xpc3QubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkZWxldGUgY29va2llc1tjb29raWUubmFtZV07XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb29raWVzX2xpc3RbaV0gPSBjb29raWU7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvb2tpZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBpZiAocmVtb3ZlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgY29va2llc19saXN0LnB1c2goY29va2llKTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvb2tpZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaWYgKHJlbW92ZSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGNvb2tpZXNbY29va2llLm5hbWVdID0gW2Nvb2tpZV07XG4gICAgICAgICAgICAgICAgcmV0dXJuIGNvb2tpZXNbY29va2llLm5hbWVdO1xuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIC8vcmV0dXJucyBhIGNvb2tpZVxuICAgICAgICAgICAgdGhpcy5nZXRDb29raWUgPSBmdW5jdGlvbiBnZXRDb29raWUoY29va2llX25hbWUsIGFjY2Vzc19pbmZvKSB7XG4gICAgICAgICAgICAgICAgdmFyIGNvb2tpZSwgaTtcbiAgICAgICAgICAgICAgICBjb29raWVzX2xpc3QgPSBjb29raWVzW2Nvb2tpZV9uYW1lXTtcbiAgICAgICAgICAgICAgICBpZiAoIWNvb2tpZXNfbGlzdCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGZvciAoaSA9IDA7IGkgPCBjb29raWVzX2xpc3QubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgICAgICAgICAgY29va2llID0gY29va2llc19saXN0W2ldO1xuICAgICAgICAgICAgICAgICAgICBpZiAoY29va2llLmV4cGlyYXRpb25fZGF0ZSA8PSBEYXRlLm5vdygpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoY29va2llc19saXN0Lmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRlbGV0ZSBjb29raWVzW2Nvb2tpZS5uYW1lXTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGNvb2tpZS5tYXRjaGVzKGFjY2Vzc19pbmZvKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvb2tpZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG4gICAgICAgICAgICAvL3JldHVybnMgYSBsaXN0IG9mIGNvb2tpZXNcbiAgICAgICAgICAgIHRoaXMuZ2V0Q29va2llcyA9IGZ1bmN0aW9uIGdldENvb2tpZXMoYWNjZXNzX2luZm8pIHtcbiAgICAgICAgICAgICAgICB2YXIgbWF0Y2hlcyA9IFtdLCBjb29raWVfbmFtZSwgY29va2llO1xuICAgICAgICAgICAgICAgIGZvciAoY29va2llX25hbWUgaW4gY29va2llcykge1xuICAgICAgICAgICAgICAgICAgICBjb29raWUgPSB0aGlzLmdldENvb2tpZShjb29raWVfbmFtZSwgYWNjZXNzX2luZm8pO1xuICAgICAgICAgICAgICAgICAgICBpZiAoY29va2llKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBtYXRjaGVzLnB1c2goY29va2llKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBtYXRjaGVzLnRvU3RyaW5nID0gZnVuY3Rpb24gdG9TdHJpbmcoKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBtYXRjaGVzLmpvaW4oXCI6XCIpO1xuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgbWF0Y2hlcy50b1ZhbHVlU3RyaW5nID0gZnVuY3Rpb24gdG9WYWx1ZVN0cmluZygpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG1hdGNoZXMubWFwKGZ1bmN0aW9uIChjKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gYy50b1ZhbHVlU3RyaW5nKCk7XG4gICAgICAgICAgICAgICAgICAgIH0pLmpvaW4oJzsnKTtcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgIHJldHVybiBtYXRjaGVzO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIG5ldyBDb29raWVKYXIoKTtcbiAgICB9XG4gICAgZXhwb3J0cy5Db29raWVKYXIgPSBDb29raWVKYXI7XG5cbiAgICAvL3JldHVybnMgbGlzdCBvZiBjb29raWVzIHRoYXQgd2VyZSBzZXQgY29ycmVjdGx5LiBDb29raWVzIHRoYXQgYXJlIGV4cGlyZWQgYW5kIHJlbW92ZWQgYXJlIG5vdCByZXR1cm5lZC5cbiAgICBDb29raWVKYXIucHJvdG90eXBlLnNldENvb2tpZXMgPSBmdW5jdGlvbiBzZXRDb29raWVzKGNvb2tpZXMsIHJlcXVlc3RfZG9tYWluLCByZXF1ZXN0X3BhdGgpIHtcbiAgICAgICAgY29va2llcyA9IEFycmF5LmlzQXJyYXkoY29va2llcykgP1xuICAgICAgICAgICAgICAgIGNvb2tpZXMgOlxuICAgICAgICAgICAgICAgIGNvb2tpZXMuc3BsaXQoY29va2llX3N0cl9zcGxpdHRlcik7XG4gICAgICAgIHZhciBzdWNjZXNzZnVsID0gW10sXG4gICAgICAgICAgICBpLFxuICAgICAgICAgICAgY29va2llO1xuICAgICAgICBjb29raWVzID0gY29va2llcy5tYXAoZnVuY3Rpb24oaXRlbSl7XG4gICAgICAgICAgICByZXR1cm4gbmV3IENvb2tpZShpdGVtLCByZXF1ZXN0X2RvbWFpbiwgcmVxdWVzdF9wYXRoKTtcbiAgICAgICAgfSk7XG4gICAgICAgIGZvciAoaSA9IDA7IGkgPCBjb29raWVzLmxlbmd0aDsgaSArPSAxKSB7XG4gICAgICAgICAgICBjb29raWUgPSBjb29raWVzW2ldO1xuICAgICAgICAgICAgaWYgKHRoaXMuc2V0Q29va2llKGNvb2tpZSwgcmVxdWVzdF9kb21haW4sIHJlcXVlc3RfcGF0aCkpIHtcbiAgICAgICAgICAgICAgICBzdWNjZXNzZnVsLnB1c2goY29va2llKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gc3VjY2Vzc2Z1bDtcbiAgICB9O1xufSgpKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./node_modules/cookiejar/cookiejar.js\n");

/***/ })

}]);
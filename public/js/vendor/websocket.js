/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunk"] = self["webpackChunk"] || []).push([["vendor/websocket"],{

/***/ "./node_modules/websocket/lib/browser.js":
/*!***********************************************!*\
  !*** ./node_modules/websocket/lib/browser.js ***!
  \***********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

eval("var _globalThis;\ntry {\n\t_globalThis = __webpack_require__(/*! es5-ext/global */ \"./node_modules/es5-ext/global.js\");\n} catch (error) {\n} finally {\n\tif (!_globalThis && typeof window !== 'undefined') { _globalThis = window; }\n\tif (!_globalThis) { throw new Error('Could not determine global this'); }\n}\n\nvar NativeWebSocket = _globalThis.WebSocket || _globalThis.MozWebSocket;\nvar websocket_version = __webpack_require__(/*! ./version */ \"./node_modules/websocket/lib/version.js\");\n\n\n/**\n * Expose a W3C WebSocket class with just one or two arguments.\n */\nfunction W3CWebSocket(uri, protocols) {\n\tvar native_instance;\n\n\tif (protocols) {\n\t\tnative_instance = new NativeWebSocket(uri, protocols);\n\t}\n\telse {\n\t\tnative_instance = new NativeWebSocket(uri);\n\t}\n\n\t/**\n\t * 'native_instance' is an instance of nativeWebSocket (the browser's WebSocket\n\t * class). Since it is an Object it will be returned as it is when creating an\n\t * instance of W3CWebSocket via 'new W3CWebSocket()'.\n\t *\n\t * ECMAScript 5: http://bclary.com/2004/11/07/#a-13.2.2\n\t */\n\treturn native_instance;\n}\nif (NativeWebSocket) {\n\t['CONNECTING', 'OPEN', 'CLOSING', 'CLOSED'].forEach(function(prop) {\n\t\tObject.defineProperty(W3CWebSocket, prop, {\n\t\t\tget: function() { return NativeWebSocket[prop]; }\n\t\t});\n\t});\n}\n\n/**\n * Module exports.\n */\nmodule.exports = {\n    'w3cwebsocket' : NativeWebSocket ? W3CWebSocket : null,\n    'version'      : websocket_version\n};\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvd2Vic29ja2V0L2xpYi9icm93c2VyLmpzPzdlM2MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQTtBQUNBLGVBQWUsbUJBQU8sQ0FBQyx3REFBZ0I7QUFDdkMsQ0FBQztBQUNELENBQUM7QUFDRCxxREFBcUQsc0JBQXNCO0FBQzNFLG9CQUFvQixvREFBb0Q7QUFDeEU7O0FBRUE7QUFDQSx3QkFBd0IsbUJBQU8sQ0FBQywwREFBVzs7O0FBRzNDO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLG9CQUFvQiw4QkFBOEI7QUFDbEQsR0FBRztBQUNILEVBQUU7QUFDRjs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiIuL25vZGVfbW9kdWxlcy93ZWJzb2NrZXQvbGliL2Jyb3dzZXIuanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyJ2YXIgX2dsb2JhbFRoaXM7XG50cnkge1xuXHRfZ2xvYmFsVGhpcyA9IHJlcXVpcmUoJ2VzNS1leHQvZ2xvYmFsJyk7XG59IGNhdGNoIChlcnJvcikge1xufSBmaW5hbGx5IHtcblx0aWYgKCFfZ2xvYmFsVGhpcyAmJiB0eXBlb2Ygd2luZG93ICE9PSAndW5kZWZpbmVkJykgeyBfZ2xvYmFsVGhpcyA9IHdpbmRvdzsgfVxuXHRpZiAoIV9nbG9iYWxUaGlzKSB7IHRocm93IG5ldyBFcnJvcignQ291bGQgbm90IGRldGVybWluZSBnbG9iYWwgdGhpcycpOyB9XG59XG5cbnZhciBOYXRpdmVXZWJTb2NrZXQgPSBfZ2xvYmFsVGhpcy5XZWJTb2NrZXQgfHwgX2dsb2JhbFRoaXMuTW96V2ViU29ja2V0O1xudmFyIHdlYnNvY2tldF92ZXJzaW9uID0gcmVxdWlyZSgnLi92ZXJzaW9uJyk7XG5cblxuLyoqXG4gKiBFeHBvc2UgYSBXM0MgV2ViU29ja2V0IGNsYXNzIHdpdGgganVzdCBvbmUgb3IgdHdvIGFyZ3VtZW50cy5cbiAqL1xuZnVuY3Rpb24gVzNDV2ViU29ja2V0KHVyaSwgcHJvdG9jb2xzKSB7XG5cdHZhciBuYXRpdmVfaW5zdGFuY2U7XG5cblx0aWYgKHByb3RvY29scykge1xuXHRcdG5hdGl2ZV9pbnN0YW5jZSA9IG5ldyBOYXRpdmVXZWJTb2NrZXQodXJpLCBwcm90b2NvbHMpO1xuXHR9XG5cdGVsc2Uge1xuXHRcdG5hdGl2ZV9pbnN0YW5jZSA9IG5ldyBOYXRpdmVXZWJTb2NrZXQodXJpKTtcblx0fVxuXG5cdC8qKlxuXHQgKiAnbmF0aXZlX2luc3RhbmNlJyBpcyBhbiBpbnN0YW5jZSBvZiBuYXRpdmVXZWJTb2NrZXQgKHRoZSBicm93c2VyJ3MgV2ViU29ja2V0XG5cdCAqIGNsYXNzKS4gU2luY2UgaXQgaXMgYW4gT2JqZWN0IGl0IHdpbGwgYmUgcmV0dXJuZWQgYXMgaXQgaXMgd2hlbiBjcmVhdGluZyBhblxuXHQgKiBpbnN0YW5jZSBvZiBXM0NXZWJTb2NrZXQgdmlhICduZXcgVzNDV2ViU29ja2V0KCknLlxuXHQgKlxuXHQgKiBFQ01BU2NyaXB0IDU6IGh0dHA6Ly9iY2xhcnkuY29tLzIwMDQvMTEvMDcvI2EtMTMuMi4yXG5cdCAqL1xuXHRyZXR1cm4gbmF0aXZlX2luc3RhbmNlO1xufVxuaWYgKE5hdGl2ZVdlYlNvY2tldCkge1xuXHRbJ0NPTk5FQ1RJTkcnLCAnT1BFTicsICdDTE9TSU5HJywgJ0NMT1NFRCddLmZvckVhY2goZnVuY3Rpb24ocHJvcCkge1xuXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShXM0NXZWJTb2NrZXQsIHByb3AsIHtcblx0XHRcdGdldDogZnVuY3Rpb24oKSB7IHJldHVybiBOYXRpdmVXZWJTb2NrZXRbcHJvcF07IH1cblx0XHR9KTtcblx0fSk7XG59XG5cbi8qKlxuICogTW9kdWxlIGV4cG9ydHMuXG4gKi9cbm1vZHVsZS5leHBvcnRzID0ge1xuICAgICd3M2N3ZWJzb2NrZXQnIDogTmF0aXZlV2ViU29ja2V0ID8gVzNDV2ViU29ja2V0IDogbnVsbCxcbiAgICAndmVyc2lvbicgICAgICA6IHdlYnNvY2tldF92ZXJzaW9uXG59O1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./node_modules/websocket/lib/browser.js\n");

/***/ }),

/***/ "./node_modules/websocket/lib/version.js":
/*!***********************************************!*\
  !*** ./node_modules/websocket/lib/version.js ***!
  \***********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

eval("module.exports = __webpack_require__(/*! ../package.json */ \"./node_modules/websocket/package.json\").version;\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvd2Vic29ja2V0L2xpYi92ZXJzaW9uLmpzPzExNjgiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUEsNEdBQW1EIiwiZmlsZSI6Ii4vbm9kZV9tb2R1bGVzL3dlYnNvY2tldC9saWIvdmVyc2lvbi5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi4vcGFja2FnZS5qc29uJykudmVyc2lvbjtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./node_modules/websocket/lib/version.js\n");

/***/ }),

/***/ "./node_modules/websocket/package.json":
/*!*********************************************!*\
  !*** ./node_modules/websocket/package.json ***!
  \*********************************************/
/***/ ((module) => {

"use strict";
module.exports = JSON.parse('{"name":"websocket","description":"Websocket Client & Server Library implementing the WebSocket protocol as specified in RFC 6455.","keywords":["websocket","websockets","socket","networking","comet","push","RFC-6455","realtime","server","client"],"author":"Brian McKelvey <theturtle32@gmail.com> (https://github.com/theturtle32)","contributors":["Iñaki Baz Castillo <ibc@aliax.net> (http://dev.sipdoc.net)"],"version":"1.0.33","repository":{"type":"git","url":"https://github.com/theturtle32/WebSocket-Node.git"},"homepage":"https://github.com/theturtle32/WebSocket-Node","engines":{"node":">=4.0.0"},"dependencies":{"bufferutil":"^4.0.1","debug":"^2.2.0","es5-ext":"^0.10.50","typedarray-to-buffer":"^3.1.5","utf-8-validate":"^5.0.2","yaeti":"^0.0.6"},"devDependencies":{"buffer-equal":"^1.0.0","gulp":"^4.0.2","gulp-jshint":"^2.0.4","jshint-stylish":"^2.2.1","jshint":"^2.0.0","tape":"^4.9.1"},"config":{"verbose":false},"scripts":{"test":"tape test/unit/*.js","gulp":"gulp"},"main":"index","directories":{"lib":"./lib"},"browser":"lib/browser.js","license":"Apache-2.0"}');

/***/ })

}]);
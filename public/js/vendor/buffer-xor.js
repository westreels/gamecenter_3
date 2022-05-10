/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunk"] = self["webpackChunk"] || []).push([["vendor/buffer-xor"],{

/***/ "./node_modules/buffer-xor/index.js":
/*!******************************************!*\
  !*** ./node_modules/buffer-xor/index.js ***!
  \******************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

eval("/* provided dependency */ var Buffer = __webpack_require__(/*! buffer */ \"./node_modules/buffer/index.js\")[\"Buffer\"];\nmodule.exports = function xor (a, b) {\n  var length = Math.min(a.length, b.length)\n  var buffer = new Buffer(length)\n\n  for (var i = 0; i < length; ++i) {\n    buffer[i] = a[i] ^ b[i]\n  }\n\n  return buffer\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvYnVmZmVyLXhvci9pbmRleC5qcz84YzhhIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7QUFBQTtBQUNBO0FBQ0EsbUJBQW1CLE1BQU07O0FBRXpCLGlCQUFpQixZQUFZO0FBQzdCO0FBQ0E7O0FBRUE7QUFDQSIsImZpbGUiOiIuL25vZGVfbW9kdWxlcy9idWZmZXIteG9yL2luZGV4LmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbiB4b3IgKGEsIGIpIHtcbiAgdmFyIGxlbmd0aCA9IE1hdGgubWluKGEubGVuZ3RoLCBiLmxlbmd0aClcbiAgdmFyIGJ1ZmZlciA9IG5ldyBCdWZmZXIobGVuZ3RoKVxuXG4gIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuZ3RoOyArK2kpIHtcbiAgICBidWZmZXJbaV0gPSBhW2ldIF4gYltpXVxuICB9XG5cbiAgcmV0dXJuIGJ1ZmZlclxufVxuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./node_modules/buffer-xor/index.js\n");

/***/ })

}]);
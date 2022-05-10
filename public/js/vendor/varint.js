/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunk"] = self["webpackChunk"] || []).push([["vendor/varint"],{

/***/ "./node_modules/varint/decode.js":
/*!***************************************!*\
  !*** ./node_modules/varint/decode.js ***!
  \***************************************/
/***/ ((module) => {

eval("module.exports = read\n\nvar MSB = 0x80\n  , REST = 0x7F\n\nfunction read(buf, offset) {\n  var res    = 0\n    , offset = offset || 0\n    , shift  = 0\n    , counter = offset\n    , b\n    , l = buf.length\n\n  do {\n    if (counter >= l) {\n      read.bytes = 0\n      throw new RangeError('Could not decode varint')\n    }\n    b = buf[counter++]\n    res += shift < 28\n      ? (b & REST) << shift\n      : (b & REST) * Math.pow(2, shift)\n    shift += 7\n  } while (b >= MSB)\n\n  read.bytes = counter - offset\n\n  return res\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvdmFyaW50L2RlY29kZS5qcz9mYTQ1Il0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHOztBQUVIOztBQUVBO0FBQ0EiLCJmaWxlIjoiLi9ub2RlX21vZHVsZXMvdmFyaW50L2RlY29kZS5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIm1vZHVsZS5leHBvcnRzID0gcmVhZFxuXG52YXIgTVNCID0gMHg4MFxuICAsIFJFU1QgPSAweDdGXG5cbmZ1bmN0aW9uIHJlYWQoYnVmLCBvZmZzZXQpIHtcbiAgdmFyIHJlcyAgICA9IDBcbiAgICAsIG9mZnNldCA9IG9mZnNldCB8fCAwXG4gICAgLCBzaGlmdCAgPSAwXG4gICAgLCBjb3VudGVyID0gb2Zmc2V0XG4gICAgLCBiXG4gICAgLCBsID0gYnVmLmxlbmd0aFxuXG4gIGRvIHtcbiAgICBpZiAoY291bnRlciA+PSBsKSB7XG4gICAgICByZWFkLmJ5dGVzID0gMFxuICAgICAgdGhyb3cgbmV3IFJhbmdlRXJyb3IoJ0NvdWxkIG5vdCBkZWNvZGUgdmFyaW50JylcbiAgICB9XG4gICAgYiA9IGJ1Zltjb3VudGVyKytdXG4gICAgcmVzICs9IHNoaWZ0IDwgMjhcbiAgICAgID8gKGIgJiBSRVNUKSA8PCBzaGlmdFxuICAgICAgOiAoYiAmIFJFU1QpICogTWF0aC5wb3coMiwgc2hpZnQpXG4gICAgc2hpZnQgKz0gN1xuICB9IHdoaWxlIChiID49IE1TQilcblxuICByZWFkLmJ5dGVzID0gY291bnRlciAtIG9mZnNldFxuXG4gIHJldHVybiByZXNcbn1cbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./node_modules/varint/decode.js\n");

/***/ }),

/***/ "./node_modules/varint/encode.js":
/*!***************************************!*\
  !*** ./node_modules/varint/encode.js ***!
  \***************************************/
/***/ ((module) => {

eval("module.exports = encode\n\nvar MSB = 0x80\n  , REST = 0x7F\n  , MSBALL = ~REST\n  , INT = Math.pow(2, 31)\n\nfunction encode(num, out, offset) {\n  out = out || []\n  offset = offset || 0\n  var oldOffset = offset\n\n  while(num >= INT) {\n    out[offset++] = (num & 0xFF) | MSB\n    num /= 128\n  }\n  while(num & MSBALL) {\n    out[offset++] = (num & 0xFF) | MSB\n    num >>>= 7\n  }\n  out[offset] = num | 0\n  \n  encode.bytes = offset - oldOffset + 1\n  \n  return out\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvdmFyaW50L2VuY29kZS5qcz9hZjNhIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOztBQUVBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTs7QUFFQTtBQUNBIiwiZmlsZSI6Ii4vbm9kZV9tb2R1bGVzL3ZhcmludC9lbmNvZGUuanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyJtb2R1bGUuZXhwb3J0cyA9IGVuY29kZVxuXG52YXIgTVNCID0gMHg4MFxuICAsIFJFU1QgPSAweDdGXG4gICwgTVNCQUxMID0gflJFU1RcbiAgLCBJTlQgPSBNYXRoLnBvdygyLCAzMSlcblxuZnVuY3Rpb24gZW5jb2RlKG51bSwgb3V0LCBvZmZzZXQpIHtcbiAgb3V0ID0gb3V0IHx8IFtdXG4gIG9mZnNldCA9IG9mZnNldCB8fCAwXG4gIHZhciBvbGRPZmZzZXQgPSBvZmZzZXRcblxuICB3aGlsZShudW0gPj0gSU5UKSB7XG4gICAgb3V0W29mZnNldCsrXSA9IChudW0gJiAweEZGKSB8IE1TQlxuICAgIG51bSAvPSAxMjhcbiAgfVxuICB3aGlsZShudW0gJiBNU0JBTEwpIHtcbiAgICBvdXRbb2Zmc2V0KytdID0gKG51bSAmIDB4RkYpIHwgTVNCXG4gICAgbnVtID4+Pj0gN1xuICB9XG4gIG91dFtvZmZzZXRdID0gbnVtIHwgMFxuICBcbiAgZW5jb2RlLmJ5dGVzID0gb2Zmc2V0IC0gb2xkT2Zmc2V0ICsgMVxuICBcbiAgcmV0dXJuIG91dFxufVxuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./node_modules/varint/encode.js\n");

/***/ }),

/***/ "./node_modules/varint/index.js":
/*!**************************************!*\
  !*** ./node_modules/varint/index.js ***!
  \**************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

eval("module.exports = {\n    encode: __webpack_require__(/*! ./encode.js */ \"./node_modules/varint/encode.js\")\n  , decode: __webpack_require__(/*! ./decode.js */ \"./node_modules/varint/decode.js\")\n  , encodingLength: __webpack_require__(/*! ./length.js */ \"./node_modules/varint/length.js\")\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvdmFyaW50L2luZGV4LmpzPzI2MTIiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQSxZQUFZLG1CQUFPLENBQUMsb0RBQWE7QUFDakMsWUFBWSxtQkFBTyxDQUFDLG9EQUFhO0FBQ2pDLG9CQUFvQixtQkFBTyxDQUFDLG9EQUFhO0FBQ3pDIiwiZmlsZSI6Ii4vbm9kZV9tb2R1bGVzL3ZhcmludC9pbmRleC5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIm1vZHVsZS5leHBvcnRzID0ge1xuICAgIGVuY29kZTogcmVxdWlyZSgnLi9lbmNvZGUuanMnKVxuICAsIGRlY29kZTogcmVxdWlyZSgnLi9kZWNvZGUuanMnKVxuICAsIGVuY29kaW5nTGVuZ3RoOiByZXF1aXJlKCcuL2xlbmd0aC5qcycpXG59XG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./node_modules/varint/index.js\n");

/***/ }),

/***/ "./node_modules/varint/length.js":
/*!***************************************!*\
  !*** ./node_modules/varint/length.js ***!
  \***************************************/
/***/ ((module) => {

eval("\nvar N1 = Math.pow(2,  7)\nvar N2 = Math.pow(2, 14)\nvar N3 = Math.pow(2, 21)\nvar N4 = Math.pow(2, 28)\nvar N5 = Math.pow(2, 35)\nvar N6 = Math.pow(2, 42)\nvar N7 = Math.pow(2, 49)\nvar N8 = Math.pow(2, 56)\nvar N9 = Math.pow(2, 63)\n\nmodule.exports = function (value) {\n  return (\n    value < N1 ? 1\n  : value < N2 ? 2\n  : value < N3 ? 3\n  : value < N4 ? 4\n  : value < N5 ? 5\n  : value < N6 ? 6\n  : value < N7 ? 7\n  : value < N8 ? 8\n  : value < N9 ? 9\n  :              10\n  )\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvdmFyaW50L2xlbmd0aC5qcz82MWYxIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiIuL25vZGVfbW9kdWxlcy92YXJpbnQvbGVuZ3RoLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiXG52YXIgTjEgPSBNYXRoLnBvdygyLCAgNylcbnZhciBOMiA9IE1hdGgucG93KDIsIDE0KVxudmFyIE4zID0gTWF0aC5wb3coMiwgMjEpXG52YXIgTjQgPSBNYXRoLnBvdygyLCAyOClcbnZhciBONSA9IE1hdGgucG93KDIsIDM1KVxudmFyIE42ID0gTWF0aC5wb3coMiwgNDIpXG52YXIgTjcgPSBNYXRoLnBvdygyLCA0OSlcbnZhciBOOCA9IE1hdGgucG93KDIsIDU2KVxudmFyIE45ID0gTWF0aC5wb3coMiwgNjMpXG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24gKHZhbHVlKSB7XG4gIHJldHVybiAoXG4gICAgdmFsdWUgPCBOMSA/IDFcbiAgOiB2YWx1ZSA8IE4yID8gMlxuICA6IHZhbHVlIDwgTjMgPyAzXG4gIDogdmFsdWUgPCBONCA/IDRcbiAgOiB2YWx1ZSA8IE41ID8gNVxuICA6IHZhbHVlIDwgTjYgPyA2XG4gIDogdmFsdWUgPCBONyA/IDdcbiAgOiB2YWx1ZSA8IE44ID8gOFxuICA6IHZhbHVlIDwgTjkgPyA5XG4gIDogICAgICAgICAgICAgIDEwXG4gIClcbn1cbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./node_modules/varint/length.js\n");

/***/ })

}]);
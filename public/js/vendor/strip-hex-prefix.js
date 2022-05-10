/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunk"] = self["webpackChunk"] || []).push([["vendor/strip-hex-prefix"],{

/***/ "./node_modules/strip-hex-prefix/src/index.js":
/*!****************************************************!*\
  !*** ./node_modules/strip-hex-prefix/src/index.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

eval("var isHexPrefixed = __webpack_require__(/*! is-hex-prefixed */ \"./node_modules/is-hex-prefixed/src/index.js\");\n\n/**\n * Removes '0x' from a given `String` is present\n * @param {String} str the string value\n * @return {String|Optional} a string by pass if necessary\n */\nmodule.exports = function stripHexPrefix(str) {\n  if (typeof str !== 'string') {\n    return str;\n  }\n\n  return isHexPrefixed(str) ? str.slice(2) : str;\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvc3RyaXAtaGV4LXByZWZpeC9zcmMvaW5kZXguanM/ODViMSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSxvQkFBb0IsbUJBQU8sQ0FBQyxvRUFBaUI7O0FBRTdDO0FBQ0E7QUFDQSxXQUFXLE9BQU87QUFDbEIsWUFBWSxnQkFBZ0I7QUFDNUI7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBIiwiZmlsZSI6Ii4vbm9kZV9tb2R1bGVzL3N0cmlwLWhleC1wcmVmaXgvc3JjL2luZGV4LmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsidmFyIGlzSGV4UHJlZml4ZWQgPSByZXF1aXJlKCdpcy1oZXgtcHJlZml4ZWQnKTtcblxuLyoqXG4gKiBSZW1vdmVzICcweCcgZnJvbSBhIGdpdmVuIGBTdHJpbmdgIGlzIHByZXNlbnRcbiAqIEBwYXJhbSB7U3RyaW5nfSBzdHIgdGhlIHN0cmluZyB2YWx1ZVxuICogQHJldHVybiB7U3RyaW5nfE9wdGlvbmFsfSBhIHN0cmluZyBieSBwYXNzIGlmIG5lY2Vzc2FyeVxuICovXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uIHN0cmlwSGV4UHJlZml4KHN0cikge1xuICBpZiAodHlwZW9mIHN0ciAhPT0gJ3N0cmluZycpIHtcbiAgICByZXR1cm4gc3RyO1xuICB9XG5cbiAgcmV0dXJuIGlzSGV4UHJlZml4ZWQoc3RyKSA/IHN0ci5zbGljZSgyKSA6IHN0cjtcbn1cbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./node_modules/strip-hex-prefix/src/index.js\n");

/***/ })

}]);
/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunk"] = self["webpackChunk"] || []).push([["vendor/randomfill"],{

/***/ "./node_modules/randomfill/browser.js":
/*!********************************************!*\
  !*** ./node_modules/randomfill/browser.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
eval("/* provided dependency */ var process = __webpack_require__(/*! process/browser */ \"./node_modules/process/browser.js\");\n\n\nfunction oldBrowser () {\n  throw new Error('secure random number generation not supported by this browser\\nuse chrome, FireFox or Internet Explorer 11')\n}\nvar safeBuffer = __webpack_require__(/*! safe-buffer */ \"./node_modules/safe-buffer/index.js\")\nvar randombytes = __webpack_require__(/*! randombytes */ \"./node_modules/randombytes/browser.js\")\nvar Buffer = safeBuffer.Buffer\nvar kBufferMaxLength = safeBuffer.kMaxLength\nvar crypto = __webpack_require__.g.crypto || __webpack_require__.g.msCrypto\nvar kMaxUint32 = Math.pow(2, 32) - 1\nfunction assertOffset (offset, length) {\n  if (typeof offset !== 'number' || offset !== offset) { // eslint-disable-line no-self-compare\n    throw new TypeError('offset must be a number')\n  }\n\n  if (offset > kMaxUint32 || offset < 0) {\n    throw new TypeError('offset must be a uint32')\n  }\n\n  if (offset > kBufferMaxLength || offset > length) {\n    throw new RangeError('offset out of range')\n  }\n}\n\nfunction assertSize (size, offset, length) {\n  if (typeof size !== 'number' || size !== size) { // eslint-disable-line no-self-compare\n    throw new TypeError('size must be a number')\n  }\n\n  if (size > kMaxUint32 || size < 0) {\n    throw new TypeError('size must be a uint32')\n  }\n\n  if (size + offset > length || size > kBufferMaxLength) {\n    throw new RangeError('buffer too small')\n  }\n}\nif ((crypto && crypto.getRandomValues) || !process.browser) {\n  exports.randomFill = randomFill\n  exports.randomFillSync = randomFillSync\n} else {\n  exports.randomFill = oldBrowser\n  exports.randomFillSync = oldBrowser\n}\nfunction randomFill (buf, offset, size, cb) {\n  if (!Buffer.isBuffer(buf) && !(buf instanceof __webpack_require__.g.Uint8Array)) {\n    throw new TypeError('\"buf\" argument must be a Buffer or Uint8Array')\n  }\n\n  if (typeof offset === 'function') {\n    cb = offset\n    offset = 0\n    size = buf.length\n  } else if (typeof size === 'function') {\n    cb = size\n    size = buf.length - offset\n  } else if (typeof cb !== 'function') {\n    throw new TypeError('\"cb\" argument must be a function')\n  }\n  assertOffset(offset, buf.length)\n  assertSize(size, offset, buf.length)\n  return actualFill(buf, offset, size, cb)\n}\n\nfunction actualFill (buf, offset, size, cb) {\n  if (process.browser) {\n    var ourBuf = buf.buffer\n    var uint = new Uint8Array(ourBuf, offset, size)\n    crypto.getRandomValues(uint)\n    if (cb) {\n      process.nextTick(function () {\n        cb(null, buf)\n      })\n      return\n    }\n    return buf\n  }\n  if (cb) {\n    randombytes(size, function (err, bytes) {\n      if (err) {\n        return cb(err)\n      }\n      bytes.copy(buf, offset)\n      cb(null, buf)\n    })\n    return\n  }\n  var bytes = randombytes(size)\n  bytes.copy(buf, offset)\n  return buf\n}\nfunction randomFillSync (buf, offset, size) {\n  if (typeof offset === 'undefined') {\n    offset = 0\n  }\n  if (!Buffer.isBuffer(buf) && !(buf instanceof __webpack_require__.g.Uint8Array)) {\n    throw new TypeError('\"buf\" argument must be a Buffer or Uint8Array')\n  }\n\n  assertOffset(offset, buf.length)\n\n  if (size === undefined) size = buf.length - offset\n\n  assertSize(size, offset, buf.length)\n\n  return actualFill(buf, offset, size)\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvcmFuZG9tZmlsbC9icm93c2VyLmpzPzc1Y2MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IjtBQUFZOztBQUVaO0FBQ0E7QUFDQTtBQUNBLGlCQUFpQixtQkFBTyxDQUFDLHdEQUFhO0FBQ3RDLGtCQUFrQixtQkFBTyxDQUFDLDBEQUFhO0FBQ3ZDO0FBQ0E7QUFDQSxhQUFhLHFCQUFNLFdBQVcscUJBQU07QUFDcEM7QUFDQTtBQUNBLHdEQUF3RDtBQUN4RDtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLGtEQUFrRDtBQUNsRDtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLDJDQUEyQyxPQUFPO0FBQ2xELEVBQUUsa0JBQWtCO0FBQ3BCLEVBQUUsc0JBQXNCO0FBQ3hCLENBQUM7QUFDRCxFQUFFLGtCQUFrQjtBQUNwQixFQUFFLHNCQUFzQjtBQUN4QjtBQUNBO0FBQ0EsZ0RBQWdELHFCQUFNO0FBQ3REO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBLEdBQUc7QUFDSDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQSxNQUFNLE9BQU87QUFDYjtBQUNBO0FBQ0E7QUFDQTtBQUNBLE1BQU0sT0FBTztBQUNiO0FBQ0EsT0FBTztBQUNQO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxLQUFLO0FBQ0w7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxnREFBZ0QscUJBQU07QUFDdEQ7QUFDQTs7QUFFQTs7QUFFQTs7QUFFQTs7QUFFQTtBQUNBIiwiZmlsZSI6Ii4vbm9kZV9tb2R1bGVzL3JhbmRvbWZpbGwvYnJvd3Nlci5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIid1c2Ugc3RyaWN0J1xuXG5mdW5jdGlvbiBvbGRCcm93c2VyICgpIHtcbiAgdGhyb3cgbmV3IEVycm9yKCdzZWN1cmUgcmFuZG9tIG51bWJlciBnZW5lcmF0aW9uIG5vdCBzdXBwb3J0ZWQgYnkgdGhpcyBicm93c2VyXFxudXNlIGNocm9tZSwgRmlyZUZveCBvciBJbnRlcm5ldCBFeHBsb3JlciAxMScpXG59XG52YXIgc2FmZUJ1ZmZlciA9IHJlcXVpcmUoJ3NhZmUtYnVmZmVyJylcbnZhciByYW5kb21ieXRlcyA9IHJlcXVpcmUoJ3JhbmRvbWJ5dGVzJylcbnZhciBCdWZmZXIgPSBzYWZlQnVmZmVyLkJ1ZmZlclxudmFyIGtCdWZmZXJNYXhMZW5ndGggPSBzYWZlQnVmZmVyLmtNYXhMZW5ndGhcbnZhciBjcnlwdG8gPSBnbG9iYWwuY3J5cHRvIHx8IGdsb2JhbC5tc0NyeXB0b1xudmFyIGtNYXhVaW50MzIgPSBNYXRoLnBvdygyLCAzMikgLSAxXG5mdW5jdGlvbiBhc3NlcnRPZmZzZXQgKG9mZnNldCwgbGVuZ3RoKSB7XG4gIGlmICh0eXBlb2Ygb2Zmc2V0ICE9PSAnbnVtYmVyJyB8fCBvZmZzZXQgIT09IG9mZnNldCkgeyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLXNlbGYtY29tcGFyZVxuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ29mZnNldCBtdXN0IGJlIGEgbnVtYmVyJylcbiAgfVxuXG4gIGlmIChvZmZzZXQgPiBrTWF4VWludDMyIHx8IG9mZnNldCA8IDApIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdvZmZzZXQgbXVzdCBiZSBhIHVpbnQzMicpXG4gIH1cblxuICBpZiAob2Zmc2V0ID4ga0J1ZmZlck1heExlbmd0aCB8fCBvZmZzZXQgPiBsZW5ndGgpIHtcbiAgICB0aHJvdyBuZXcgUmFuZ2VFcnJvcignb2Zmc2V0IG91dCBvZiByYW5nZScpXG4gIH1cbn1cblxuZnVuY3Rpb24gYXNzZXJ0U2l6ZSAoc2l6ZSwgb2Zmc2V0LCBsZW5ndGgpIHtcbiAgaWYgKHR5cGVvZiBzaXplICE9PSAnbnVtYmVyJyB8fCBzaXplICE9PSBzaXplKSB7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tc2VsZi1jb21wYXJlXG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcignc2l6ZSBtdXN0IGJlIGEgbnVtYmVyJylcbiAgfVxuXG4gIGlmIChzaXplID4ga01heFVpbnQzMiB8fCBzaXplIDwgMCkge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ3NpemUgbXVzdCBiZSBhIHVpbnQzMicpXG4gIH1cblxuICBpZiAoc2l6ZSArIG9mZnNldCA+IGxlbmd0aCB8fCBzaXplID4ga0J1ZmZlck1heExlbmd0aCkge1xuICAgIHRocm93IG5ldyBSYW5nZUVycm9yKCdidWZmZXIgdG9vIHNtYWxsJylcbiAgfVxufVxuaWYgKChjcnlwdG8gJiYgY3J5cHRvLmdldFJhbmRvbVZhbHVlcykgfHwgIXByb2Nlc3MuYnJvd3Nlcikge1xuICBleHBvcnRzLnJhbmRvbUZpbGwgPSByYW5kb21GaWxsXG4gIGV4cG9ydHMucmFuZG9tRmlsbFN5bmMgPSByYW5kb21GaWxsU3luY1xufSBlbHNlIHtcbiAgZXhwb3J0cy5yYW5kb21GaWxsID0gb2xkQnJvd3NlclxuICBleHBvcnRzLnJhbmRvbUZpbGxTeW5jID0gb2xkQnJvd3NlclxufVxuZnVuY3Rpb24gcmFuZG9tRmlsbCAoYnVmLCBvZmZzZXQsIHNpemUsIGNiKSB7XG4gIGlmICghQnVmZmVyLmlzQnVmZmVyKGJ1ZikgJiYgIShidWYgaW5zdGFuY2VvZiBnbG9iYWwuVWludDhBcnJheSkpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdcImJ1ZlwiIGFyZ3VtZW50IG11c3QgYmUgYSBCdWZmZXIgb3IgVWludDhBcnJheScpXG4gIH1cblxuICBpZiAodHlwZW9mIG9mZnNldCA9PT0gJ2Z1bmN0aW9uJykge1xuICAgIGNiID0gb2Zmc2V0XG4gICAgb2Zmc2V0ID0gMFxuICAgIHNpemUgPSBidWYubGVuZ3RoXG4gIH0gZWxzZSBpZiAodHlwZW9mIHNpemUgPT09ICdmdW5jdGlvbicpIHtcbiAgICBjYiA9IHNpemVcbiAgICBzaXplID0gYnVmLmxlbmd0aCAtIG9mZnNldFxuICB9IGVsc2UgaWYgKHR5cGVvZiBjYiAhPT0gJ2Z1bmN0aW9uJykge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1wiY2JcIiBhcmd1bWVudCBtdXN0IGJlIGEgZnVuY3Rpb24nKVxuICB9XG4gIGFzc2VydE9mZnNldChvZmZzZXQsIGJ1Zi5sZW5ndGgpXG4gIGFzc2VydFNpemUoc2l6ZSwgb2Zmc2V0LCBidWYubGVuZ3RoKVxuICByZXR1cm4gYWN0dWFsRmlsbChidWYsIG9mZnNldCwgc2l6ZSwgY2IpXG59XG5cbmZ1bmN0aW9uIGFjdHVhbEZpbGwgKGJ1Ziwgb2Zmc2V0LCBzaXplLCBjYikge1xuICBpZiAocHJvY2Vzcy5icm93c2VyKSB7XG4gICAgdmFyIG91ckJ1ZiA9IGJ1Zi5idWZmZXJcbiAgICB2YXIgdWludCA9IG5ldyBVaW50OEFycmF5KG91ckJ1Ziwgb2Zmc2V0LCBzaXplKVxuICAgIGNyeXB0by5nZXRSYW5kb21WYWx1ZXModWludClcbiAgICBpZiAoY2IpIHtcbiAgICAgIHByb2Nlc3MubmV4dFRpY2soZnVuY3Rpb24gKCkge1xuICAgICAgICBjYihudWxsLCBidWYpXG4gICAgICB9KVxuICAgICAgcmV0dXJuXG4gICAgfVxuICAgIHJldHVybiBidWZcbiAgfVxuICBpZiAoY2IpIHtcbiAgICByYW5kb21ieXRlcyhzaXplLCBmdW5jdGlvbiAoZXJyLCBieXRlcykge1xuICAgICAgaWYgKGVycikge1xuICAgICAgICByZXR1cm4gY2IoZXJyKVxuICAgICAgfVxuICAgICAgYnl0ZXMuY29weShidWYsIG9mZnNldClcbiAgICAgIGNiKG51bGwsIGJ1ZilcbiAgICB9KVxuICAgIHJldHVyblxuICB9XG4gIHZhciBieXRlcyA9IHJhbmRvbWJ5dGVzKHNpemUpXG4gIGJ5dGVzLmNvcHkoYnVmLCBvZmZzZXQpXG4gIHJldHVybiBidWZcbn1cbmZ1bmN0aW9uIHJhbmRvbUZpbGxTeW5jIChidWYsIG9mZnNldCwgc2l6ZSkge1xuICBpZiAodHlwZW9mIG9mZnNldCA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICBvZmZzZXQgPSAwXG4gIH1cbiAgaWYgKCFCdWZmZXIuaXNCdWZmZXIoYnVmKSAmJiAhKGJ1ZiBpbnN0YW5jZW9mIGdsb2JhbC5VaW50OEFycmF5KSkge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1wiYnVmXCIgYXJndW1lbnQgbXVzdCBiZSBhIEJ1ZmZlciBvciBVaW50OEFycmF5JylcbiAgfVxuXG4gIGFzc2VydE9mZnNldChvZmZzZXQsIGJ1Zi5sZW5ndGgpXG5cbiAgaWYgKHNpemUgPT09IHVuZGVmaW5lZCkgc2l6ZSA9IGJ1Zi5sZW5ndGggLSBvZmZzZXRcblxuICBhc3NlcnRTaXplKHNpemUsIG9mZnNldCwgYnVmLmxlbmd0aClcblxuICByZXR1cm4gYWN0dWFsRmlsbChidWYsIG9mZnNldCwgc2l6ZSlcbn1cbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./node_modules/randomfill/browser.js\n");

/***/ })

}]);
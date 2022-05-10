/**
 * Format string almost like sprintf
 *
 * @returns {*}
 */
export function format () {
  const args = arguments
  return args[0].replace(/{(\d+)}/g, (match, number) => {
    const n = parseInt(number, 10)
    return typeof args[n + 1] !== 'undefined' ? args[n + 1] : match
  })
}

/**
 * Get nested object value by path
 * https://stackoverflow.com/a/43849204/2767324
 *
 * @param object
 * @param path
 * @param defaultValue
 * @returns {*}
 */
export function get (object, path, defaultValue = null) {
  return path.split('.').reduce((o, p) => o && typeof o[p] !== 'undefined' && o[p] != null ? o[p] : defaultValue, object)
}

/**
 * Set nested object value by path
 * https://stackoverflow.com/a/43849204/2767324
 *
 * @param object
 * @param path
 * @param value
 * @returns {*}
 */
export function set (object, path, value) {
  return path.split('.').reduce((o, p, i) => {
    o[p] = path.split('.').length === ++i ? value : o[p] || {}
  }, object)
}

/**
 * Copy DOM element content to clipboard
 *
 * @param el
 */
export function copyToClipboard (el) {
  el.select()
  try {
    document.execCommand('copy')
  } catch (err) {
    //
  }
  // clear selection
  document.getSelection().removeAllRanges()
  document.activeElement.blur()
}

/**
 * Check if variable is numeric
 *
 * @param n
 * @returns {boolean}
 */
export function isNumeric (n) {
  return !isNaN(parseFloat(n)) && isFinite(n)
}

/**
 * Check if variable is an integer
 *
 * @param n
 * @returns {boolean}
 */
export function isInteger (n) {
  return (typeof n === 'number' && n === parseInt(n, 10)) || (typeof n === 'string' && n === parseInt(n, 10).toString())
}

/**
 * Capitalize first letter of a string
 *
 * @param string
 * @returns {string}
 */
export function ucfirst (string) {
  return string[0].toUpperCase() + string.slice(1)
}

/**
 * Deep merge objects or arrays
 * Source: https://stackoverflow.com/a/49798508/2767324
 *
 * @param sources
 * @returns {}
 */
export function deepMerge (...sources) {
  let acc = {}
  for (const source of sources) {
    if (source instanceof Array) {
      if (!(acc instanceof Array)) {
        acc = []
      }
      // acc = [...acc, ...source]
      acc = [...source]
    } else if (source instanceof Object) {
      for (let [key, value] of Object.entries(source)) {
        if (value instanceof Object && key in acc) {
          value = deepMerge(acc[key], value)
        }
        acc = {
          ...acc,
          [key]: value
        }
      }
    }
  }

  return acc
}

/**
 * Sleep for N milliseconds
 * Usage: await sleep (5000)
 *
 * @param ms
 * @returns {Promise<unknown>}
 */
export function sleep (ms) {
  return new Promise(resolve => setTimeout(resolve, ms))
}

/**
 * Get website base URL
 *
 * @returns {string}
 */
export function baseUrl () {
  return window.location.origin || window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '')
}

export function lpad (string, symbol, length) {
  string = '' + string

  while (string.length < length) {
    string = symbol + string
  }

  return string
}

export function round (n, digits = 0) {
  const base = Math.pow(10, digits)
  return Math.round(n * base) / base
}

/**
 * Dynalically load an external JS script
 *
 * @param url
 * @param callback
 */
export function loadScript (url, callback) {
  var script = document.createElement('script')
  script.onload = () => {
    callback()
  }
  script.src = url
  document.body.appendChild(script)
}

/**
 * Check if given hex color is bright or not
 * https://stackoverflow.com/a/51567564/10001962
 *
 * @param color
 * @returns {boolean}
 */
export function isColorBright (color) {
  const hex = color.replace('#', '')
  const red = parseInt(hex.substr(0, 2), 16)
  const green = parseInt(hex.substr(2, 2), 16)
  const blue = parseInt(hex.substr(4, 2), 16)
  const brightness = ((red * 299) + (green * 587) + (blue * 114)) / 1000

  return brightness > 155
}

/**
 * Get UNIX timestamp
 * @returns {number}
 */
export function time () {
  return Math.floor(new Date().getTime() / 1000)
}

/**
 * Convert object to query string
 *
 * @param obj
 * @returns {string}
 */
export function toQueryString (obj) {
  return Object.keys(obj)
    .map(key => {
      const value = obj[key]
      return typeof value === 'string'
        ? key + '=' + encodeURIComponent(value)
        : value.length ? value.map(v => key + '[]=' + v).join('&') : null
    })
    .filter(v => v !== null)
    .join('&')
}

/**
 * Move array {array} element at index {currentIndex} to index {newIndex}
 *
 * @param array
 * @param currentIndex
 * @param newIndex
 */
export function moveArrayElement (array, currentIndex, newIndex) {
  const el = array[currentIndex]
  array.splice(currentIndex, 1)
  array.splice(newIndex, 0, el)
}

/**
 * Truncate a long string and add ... at the end
 *
 * @param string
 * @param maxLength
 * @returns {string|*}
 */
export function truncate (string, maxLength = 20) {
  return string.length > maxLength ? `${string.substring(0, maxLength)}...` : string
}

/**
 * Preload an image
 *
 * @param src
 * @returns {Promise<unknown>}
 */
export function preloadImage (src) {
  return new Promise(resolve => {
    const image = new Image()
    image.onload = resolve
    image.onerror = resolve
    image.src = src
  })
}

/**
 * Preload a number of images
 *
 * @param {Array} images
 * @returns {Promise<unknown[]>}
 */
export function preloadImages (images) {
  return Promise.all(images.map(src => preloadImage(src)))
}

export function unique (array) {
  return array.filter((item, index) => array.indexOf(item) === index)
}

export function union (array1, array2) {
  return unique(array1.concat(array2))
}

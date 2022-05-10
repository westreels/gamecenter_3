import numeral from 'numeral'
import { config } from './config'
import { format } from './utils'

// register custom numeral locale
numeral.register('locale', 'custom', {
  delimiters: {
    decimal: String.fromCharCode(config('settings.format.number.decimal_separator')),
    thousands: String.fromCharCode(config('settings.format.number.thousands_separator'))
  },
  abbreviations: {
    thousand: 'k',
    million: 'm',
    billion: 'b',
    trillion: 't'
  },
  currency: {
    symbol: '$'
  }
})

numeral.locale('custom')

function sign (number) {
  return number < 0 ? '-' : ''
}

// format number as integer
export function integer (number) {
  return format('{0}', numeral(number).format('0,0'))
}

// format number as decimal with fixed number of decimals
export function decimal (number, decimals = 2) {
  var num = numeral(Math.abs(number))
  var formattedNumber = num.format('0,0.' + Array(decimals + 1).join('0')) // https://stackoverflow.com/a/1877479/2767324
  if (formattedNumber === 'NaN') {
    formattedNumber = number.toFixed(decimals)
  }

  return format('{0}{1}', sign(number), formattedNumber)
}

// format number as float with variable number of decimals
export function float (number) {
  if (number <= 1e-6) {
    return decimal(number, 8)
  } else if (number <= 1e-4) {
    return decimal(number, 6)
  } else if (number <= 1e-2) {
    return decimal(number, 4)
  } else {
    return number
  }
}

// format number as 1.25k, 1.25m etc
export function short (number) {
  return number < 1000 ? integer(number) : numeral(number).format('0,0.00a')
}

// format number as decimal with percent sign
export function percentage (number) {
  return format('{0}%', decimal(number))
}

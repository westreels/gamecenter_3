import loadImage from 'image-promise'
import toHex from 'colornames'
import cloneDeep from 'clone-deep'

import imgTplNum1 from 'packages/horse-racing/resources/images/num_1.png'
import imgTplNum2 from 'packages/horse-racing/resources/images/num_2.png'
import imgTplNum3 from 'packages/horse-racing/resources/images/num_3.png'
import imgTplNum4 from 'packages/horse-racing/resources/images/num_4.png'
import imgTplNum5 from 'packages/horse-racing/resources/images/num_5.png'
import imgTplNum6 from 'packages/horse-racing/resources/images/num_6.png'
import imgTplNum7 from 'packages/horse-racing/resources/images/num_7.png'
import imgTplNum8 from 'packages/horse-racing/resources/images/num_8.png'
import imgTplNum9 from 'packages/horse-racing/resources/images/num_9.png'
import imgTplNum10 from 'packages/horse-racing/resources/images/num_10.png'
import imgTplNum11 from 'packages/horse-racing/resources/images/num_11.png'
import imgTplNum12 from 'packages/horse-racing/resources/images/num_12.png'

import imgTplShadow from 'packages/horse-racing/resources/images/shadow.png'
import imgTplAny from 'packages/horse-racing/resources/images/any.png'
import imgTplBody from 'packages/horse-racing/resources/images/body.png'
import imgTplFace from 'packages/horse-racing/resources/images/face.png'
import imgTplHead from 'packages/horse-racing/resources/images/head.png'
import imgTplHorse from 'packages/horse-racing/resources/images/horse.png'
import imgTplSaddle from 'packages/horse-racing/resources/images/saddle.png'
import imgTplTail from 'packages/horse-racing/resources/images/tail.png'

const imgTplNum = [
  imgTplNum1,
  imgTplNum2,
  imgTplNum3,
  imgTplNum4,
  imgTplNum5,
  imgTplNum6,
  imgTplNum7,
  imgTplNum8,
  imgTplNum9,
  imgTplNum10,
  imgTplNum11,
  imgTplNum12
]

function colorNormilize (color) {
  if (color[0] !== '#') color = toHex(color)
  return color
}

function HexToRGB (Hex) {
  var Long = parseInt(Hex.replace(/^#/, ''), 16)
  return {
    R: (Long >>> 16) & 0xff,
    G: (Long >>> 8) & 0xff,
    B: Long & 0xff
  }
}

function getPixels (img) {
  var canvas = document.createElement('canvas')
  var ctx = canvas.getContext('2d', { alpha: true })
  canvas.width = img.width
  canvas.height = img.height
  ctx.drawImage(img, 0, 0, img.naturalWidth, img.naturalHeight, 0, 0, img.width, img.height)
  return ctx.getImageData(0, 0, img.width, img.height)
}

function changeColor (img, color) {
  var canvas = document.createElement('canvas')
  var ctx = canvas.getContext('2d', { alpha: true })
  var newColor = HexToRGB(color)
  var pixels = getPixels(img)
  for (var I = 0, L = pixels.data.length; I < L; I += 4) {
    if (pixels.data[I + 3] > 0) {
      pixels.data[I + 0] = newColor.R * (pixels.data[I + 0] / 255) + (newColor.R < 100 && pixels.data[I + 0] > 180 ? (pixels.data[I + 0] - 180) / 75 * 50 : 0)
      if (pixels.data[I + 0] > 255) pixels.data[I + 0] = 255
      pixels.data[I + 1] = newColor.G * (pixels.data[I + 1] / 255) + (newColor.G < 100 && pixels.data[I + 1] > 180 ? (pixels.data[I + 1] - 180) / 75 * 50 : 0)
      if (pixels.data[I + 1] > 255) pixels.data[I + 1] = 255
      pixels.data[I + 2] = newColor.B * (pixels.data[I + 2] / 255) + (newColor.B < 100 && pixels.data[I + 2] > 180 ? (pixels.data[I + 2] - 180) / 75 * 50 : 0)
      if (pixels.data[I + 2] > 255) pixels.data[I + 2] = 255
    }
  }
  canvas.width = img.width
  canvas.height = img.height
  ctx.putImageData(pixels, 0, 0)
  return canvas
}

export default class CRunner {
  name = ''
  colors = {}
  frames = []
  num = 1
  framesCnt = 12
  frmW = 0
  frmH = 0
  frame = 0
  paused = true
  position = 0
  target = 0
  dir = 0
  preview = ''
  finished = false
  constructor (colors, name, num) {
    this.colors = cloneDeep(colors)
    this.num = num
    this.name = name
    this.t_last = 0
    this.t_frame = 25
    this.position_start = 0
  }

  async init () {
    var
      shadow = await loadImage(imgTplShadow)
    var horse = changeColor(await loadImage(imgTplHorse), this.colors.horse.body ? colorNormilize(this.colors.horse.body) : '#000000')
    var tail = changeColor(await loadImage(imgTplTail), this.colors.horse.tail ? colorNormilize(this.colors.horse.tail) : '#333333')
    var saddle = changeColor(await loadImage(imgTplSaddle), this.colors.horse.pad && this.colors.horse.pad.background ? colorNormilize(this.colors.horse.pad.background) : '#aaaaaa')
    var num = changeColor(await loadImage(this.num > 0 && this.num < 13 ? imgTplNum[this.num - 1] : imgTplNum1), this.colors.horse.pad && this.colors.horse.pad.text ? colorNormilize(this.colors.horse.pad.text) : '#333333')
    var body = changeColor(await loadImage(imgTplBody), this.colors.jockey.shirt ? colorNormilize(this.colors.jockey.shirt) : '#ffffff')
    var face = changeColor(await loadImage(imgTplFace), this.colors.jockey.face ? colorNormilize(this.colors.jockey.face) : ['#FFDCB1', '#E5C298', '#E4B98E', '#710200', '#E1ADA4'][Math.round(Math.random() * 4)])
    var head = changeColor(await loadImage(imgTplHead), this.colors.jockey.hat ? colorNormilize(this.colors.jockey.hat) : '#ffffff')
    var any = await loadImage(imgTplAny)
    this.frmW = shadow.width / this.framesCnt
    this.frmH = shadow.height
    this.frames = []
    while (this.frames.length < this.framesCnt) {
      var c = document.createElement('canvas')
      c.height = this.frmH
      c.width = this.frmW
      this.frames.push(c)
    }
    for (var idx in this.frames) {
      var ctx = this.frames[idx].getContext('2d', { alpha: true })
      ctx.drawImage(shadow, this.frmW * idx, 0, this.frmW, this.frmH, 0, 0, this.frmW, this.frmH)
      ctx.drawImage(horse, this.frmW * idx, 0, this.frmW, this.frmH, 0, 0, this.frmW, this.frmH)
      ctx.drawImage(tail, this.frmW * idx, 0, this.frmW, this.frmH, 0, 0, this.frmW, this.frmH)
      ctx.drawImage(saddle, this.frmW * idx, 0, this.frmW, this.frmH, 0, 0, this.frmW, this.frmH)
      ctx.drawImage(num, this.frmW * idx, 0, this.frmW, this.frmH, 0, 0, this.frmW, this.frmH)
      ctx.drawImage(body, this.frmW * idx, 0, this.frmW, this.frmH, 0, 0, this.frmW, this.frmH)
      ctx.drawImage(face, this.frmW * idx, 0, this.frmW, this.frmH, 0, 0, this.frmW, this.frmH)
      ctx.drawImage(head, this.frmW * idx, 0, this.frmW, this.frmH, 0, 0, this.frmW, this.frmH)
      ctx.drawImage(any, this.frmW * idx, 0, this.frmW, this.frmH, 0, 0, this.frmW, this.frmH)
    }
    this.paused = false
    this.t_last = (new Date()).getTime()
    this.frame = 0
    this.preview = this.frames[1].toDataURL('image/png')
  }

  getFrame (i) {
    if (typeof i !== 'undefined') {
      if (i < 0) return this.frames[0]
      if (i >= this.framesCnt) return this.frames[this.framesCnt - 1]
      return this.frames[i]
    } else if (this.paused) {
      return this.frames[this.frame]
    } else {
      var t = (new Date()).getTime()
      if (t > this.t_last + this.t_frame) {
        var cnt = Math.floor((t - this.t_last) / this.t_frame)
        this.t_last = t
        this.frame += cnt
        while (this.frame >= this.framesCnt) this.frame -= this.framesCnt
      }
      return this.frames[this.frame]
    }
  }

  pause () {
    this.paused = true
  }

  continue () {
    if (this.paused) {
      this.paused = false
      this.t_last = (new Date()).getTime()
    }
  }
}

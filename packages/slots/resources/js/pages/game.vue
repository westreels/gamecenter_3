<template>
  <div class="d-flex flex-column fill-height py-3 pt-md-3 pt-12">
    <div class="message-container d-flex justify-center align-center">
      <game-message :message="message"/>
    </div>

    <div v-if="ready" class="d-flex justify-center fill-height align-center">
      <div class="d-flex flex-md-row justify-center align-stretch flex-column">
        <div class="line-btns left d-flex flex-md-column align-center justify-space-between flex-row pa-5 pa-md-0">
          <v-btn v-for="i in line_cols[0]" :key="`lines-1-`+i" small class="pa-0" :disabled="playing" :class="{'primary':line_blink.indexOf(i)!=-1&&!animation.is_line_showig}" type="button" @mouseover="lineShow(i)" @mouseout="lineHide(i)" @click="sound('click');lines=i;linesShow();">
            {{ i }}
          </v-btn>
        </div>
        <div class="d-flex align-center justify-center">
          <canvas ref="canvas" width="1400" height="600"/>
        </div>
        <div class="line-btns right d-flex flex-md-column align-stretch justify-space-between flex-row pa-5 pa-md-0">
          <v-btn v-for="i in line_cols[1]" :key="`lines-2-`+i" small class="pa-0" :disabled="playing" :class="{'primary':line_blink.indexOf(i)!=-1&&!animation.is_line_showig}" type="button" @mouseover="lineShow(i)" @mouseout="lineHide(i)" @click="sound('click');lines=i;linesShow();">
            {{ i }}
          </v-btn>
        </div>
      </div>
    </div>
    <div v-else class="d-flex justify-center fill-height align-center">
      <block-preloader />
    </div>
    <play-controls :loading="loading" :disabled="!ready || account.balance < totalBetAmount || totalBetAmount === 0" :playing="playing" @bet-change="bet = $event" @play="play">
      <template v-slot:before-bet-input>
        <v-text-field
          v-model.number="lines"
          :label="$t('Lines')"
          dense
          :rules="[validationInteger, v => validationMin(v, 1), v => validationMax(v, 20)]"
          :disabled="playing"
          outlined
          :full-width="false"
          class="lines-input text-center mr-2"
        >
          <template v-slot:prepend-inner>
            <v-btn small text icon color="primary" @click="lines = Math.max(1, --lines)">
              <v-icon small>
                mdi-minus
              </v-icon>
            </v-btn>
          </template>
          <template v-slot:append>
            <v-btn small text icon color="primary" @click="lines = Math.min(20, ++lines)">
              <v-icon small>
                mdi-plus
              </v-icon>
            </v-btn>
          </template>
        </v-text-field>
      </template>
      <template v-slot:after-bet-input>
        <v-text-field
          :label="$t('Total bet')"
          :value="totalBetAmount"
          dense
          :readonly="!playing"
          :disabled="playing || !ready"
          outlined
          :full-width="false"
          class="total-bet-input text-center ml-2"
        />
      </template>
    </play-controls>
  </div>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import { config } from '~/plugins/config'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import { sleep } from '~/plugins/utils'
import clickSound from '~/../audio/common/click.wav'
import winSound from 'packages/slots/resources/audio/win.wav'
import loseSound from 'packages/slots/resources/audio/lose.wav'
import spinSound from 'packages/slots/resources/audio/spin.wav'
import startSound from 'packages/slots/resources/audio/start.wav'
import stopSound from 'packages/slots/resources/audio/stop.wav'
import animationImage from 'packages/slots/resources/images/animation.png'
import PlayControls from '~/components/Games/PlayControls'
import GameMessage from '~/components/Games/GameMessage'
import BlockPreloader from '~/components/BlockPreloader'

export default {
  name: 'Slots',

  components: { BlockPreloader, GameMessage, PlayControls },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      loading: false,
      playing: false,
      ready: false,
      message: null,
      bet: 0,
      lines: 0,
      line_cols: [
        [4, 2, 6, 9, 10, 1, 8, 7, 3, 5],
        [11, 13, 16, 12, 15, 17, 19, 14, 18, 20]
      ],
      line_blink: [],
      win: {},
      win_scatters: [],
      win_lines: {},
      win_loop: [],
      result: null,
      animation: {}
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    variations () {
      return config(`${this.gamePackageId}`).variations
    },
    variation () {
      return this.variations && this.variations.length ? this.variations.findIndex(o => o.slug === this.$route.params.slug) : null
    },
    config () {
      return this.variation !== null ? this.variations[this.variation] : {}
    },
    totalBetAmount () {
      return this.bet * this.lines
    }
  },

  watch: {
    variation (n, o) {
      // don't call init() when component is initiated, because it's called in the created() hook
      if (o !== null) {
        this.init()
      }
    }
  },

  beforeDestroy () {
    clearTimeout(this.show_demo_t)
    clearInterval(this.i)
    this.show_demo_t = null
    this.i = null
    this.playing = false
    this.soundStop(spinSound)
  },

  created () {
    // wait until next tick to ensure all computed properties are available (important to set lines property)
    this.$nextTick(() => {
      this.init()
    })
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    init () {
      clearInterval(this.i)
      this.i = null
      this.loading = false
      this.playing = false
      this.ready = false
      this.message = null
      this.lines = this.config.default_lines
      this.line_blink = []
      this.win = {}
      this.win_scatters = []
      this.win_lines = {}
      this.win_loop = []
      this.result = null
      this.animation = {
        i: null,
        wheel_angle: 0,
        reels: [],
        sym: [],
        lines: [],
        animation: null,
        t: 0,
        t_show_demo: 0,
        t_hide: 0,
        is_show_demo: false,
        is_line_showig: false,
        lines_show_demo: -1,
        lines_show_c: 0,
        lines_show_demo_t: 1,
        is_spinned: false,
        is_render_planned: false,
        win_line_demo: 0,
        speed_max: 5000,
        animation_time: 30,
        animation_frames: 30,
        animation_framre_size: 200,
        // speed_max: this.options.config.speed_max,
        // animation_time: this.options.config.animation_time,
        requestAnimationFrame: e => setTimeout(e, 1),
        requestAnimationFrame_get: () => {
          var _raf = window.requestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            window.oRequestAnimationFrame
          this.animation.requestAnimationFrame = _raf ? _raf.bind(window) : null
        },
        lines_indexes: [
          [1, 1, 1, 1, 1],
          [0, 0, 0, 0, 0],
          [2, 2, 2, 2, 2],

          [1, 1, 0, 1, 2],
          [1, 1, 2, 1, 0],
          [1, 0, 1, 2, 1],
          [1, 0, 1, 2, 2],
          [1, 0, 0, 1, 2],
          [1, 2, 1, 0, 1],
          [1, 2, 2, 1, 0],
          [1, 2, 1, 0, 0],

          [0, 1, 2, 1, 0],
          [0, 1, 1, 1, 2],
          [0, 0, 1, 2, 2],
          [0, 0, 1, 2, 1],
          [0, 0, 0, 1, 2],

          [2, 1, 0, 1, 2],
          [2, 1, 1, 1, 0],
          [2, 2, 1, 0, 0],
          [2, 2, 1, 0, 1]
        ]
      }
      document.addEventListener('visibilitychange', this.visibilityCnage)
      if ('hidden' in document) {
        document.addEventListener('visibilitychange', this.changeWindowState)
      } else if ('mozHidden' in document) {
        document.addEventListener('mozvisibilitychange', this.changeWindowState)
      } else if ('webkitHidden' in document) {
        document.addEventListener('webkitvisibilitychange', this.changeWindowState)
      } else if ('msHidden' in document) {
        document.addEventListener('msvisibilitychange', this.changeWindowState)
      }
      var c = 0
      const fin = () => {
        c--
        if (c === 0) {
          this.ready = true
          setTimeout(this.startAnimation, 10)
          window.game = this
        }
      }
      const fnx = () => {
        this.config.symbols.forEach(symFile => {
          c++
          var sym = {
            source: null,
            speed_frames: [],
            animation: []
          }
          sym.source = new Image()
          sym.source.onload = () => {
            var i, x, y
            var c, ctx
            var animationFrame = 0
            for (i = 0; i <= this.animation.speed_max / 1000 * 2; i++) {
              x = Math.round(Math.pow(i, 5) * 0.001)
              c = document.createElement('canvas')
              ctx = c.getContext('2d')
              c.width = 200
              c.height = 200 + x
              ctx.globalAlpha = 1 / (x + 1)
              for (y = 0; y < x + 1; ++y) { ctx.drawImage(sym.source, (200 - sym.source.width) / 2, y + (200 - sym.source.height) / 2) }
              sym.speed_frames[i] = c
            }
            for (i = 0; i < this.animation.animation_time; i++) {
              const z = Math.cos(i * 4 * Math.PI / this.animation.animation_time) * 0.1 + 0.9
              c = document.createElement('canvas')
              c.width = c.height = 200
              ctx = c.getContext('2d')
              ctx.drawImage(
                this.animation.animation,
                this.animation.animation.height * animationFrame,
                0,
                this.animation.animation.height,
                this.animation.animation.height,
                0,
                0,
                200,
                200)
              ctx.drawImage(
                sym.source,
                (200 - sym.source.width * z) / 2,
                (200 - sym.source.height * z) / 2,
                sym.source.width * z,
                sym.source.height * z)
              sym.animation[i] = c
              animationFrame++
              if (animationFrame >= this.animation.animation_frames) animationFrame = 0
            }
            fin()
          }
          sym.source.src = symFile.image
          this.animation.sym.push(sym)
        })
      }
      this.animation.animation = new Image()
      this.animation.animation.onload = fnx
      this.animation.animation.src = animationImage
    },
    async play (bet) {
      this.sound(clickSound)
      this.loading = true
      this.playing = true
      this.win = 0
      this.message = null
      clearTimeout(this.show_demo_t)
      this.show_demo_t = 0
      this.animation.lines_show_demo = -1
      this.animation.is_line_showig = false
      this.animation.is_show_demo = false
      this.animation.win_line_demo = 0
      this.result = null
      this.animation.spinStart()
      // update user balance
      this.updateUserAccountBalance(this.account.balance - bet * this.lines)
      await sleep(100)
      // API request params
      const endpoint = this.getRoute('play')
      const requestParams = { hash: this.provablyFairGame.hash, bet, lines: this.lines, variation: this.variation }
      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)
      this.loading = false
      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })
      // display banker score and result for each hand
      // game.gameable.reels.forEach((el, i) => game.gameable.reels[i]--)
      if (this.playing) {
        this.result = {
          balance: game.account.balance,
          reels: game.gameable.reels,
          win_titles: game.gameable.win_titles,
          wins: game.gameable.wins,
          win_lines: game.gameable.win_lines,
          win_scatters: game.gameable.win_scatters,
          total_win: game.win
        }
      }
    },
    visibilityCnage () {
      if (document.hidden !== undefined) {
        if (document.hidden) { this.animation.t_hide = Date.now() } else {
          var r = Date.now() - this.animation.t_hide
          this.animation.t += r
          this.animation.lines.forEach(el => { el.t += r })
          this.animation.reels.forEach(el => { el.t += r })
        }
      }
    },
    changeWindowState (e) {
      this.animation.requestAnimationFrame_get()
      this.animation.requestAnimationFrame(() => {
        this.animation.canvas.style.display = 'none'
        this.animation.requestAnimationFrame(() => { this.animation.canvas.style.display = 'block' })
      })
    },
    showResult () {
      this.updateUserAccountBalance(this.result.balance)
      this.animation.lines.forEach(el => el.hide())
      this.line_blink = []
      this.win_loop = []
      if (this.result.total_win > 0) {
        this.sound(winSound)
        var k = 0
        const titleKeys = Object.keys(this.result.win_titles)
        this.message = this.$t('You won') + ' ' + this.result.total_win
        if (Object.keys(this.result.win_lines).length) {
          Object.keys(this.result.win_lines).forEach(winLineIdx => {
            const winLine = this.result.win_lines[winLineIdx]
            this.win_loop.push({
              type: 'line',
              idx: winLineIdx,
              text: titleKeys[k] + ' ' + this.$t('pays') + ' ' + this.result.win_titles[titleKeys[k]]
            })
            k++
            this.line_blink.push(winLineIdx)
            winLine.forEach((symbolIdx, reelIdx) => {
              if (symbolIdx !== null) this.animation.reels[reelIdx].win_add(symbolIdx)
            })
          })
        }
        if (this.result.win_scatters.length) {
          this.win_loop.unshift({
            type: 'scatter',
            text: titleKeys[k] + ' ' + this.$t('pay') + ' ' + this.result.win_titles[titleKeys[k]]
          })
          this.result.win_scatters.forEach((scatters, reelIdx) => scatters.forEach(scatter => this.animation.reels[reelIdx].win_add(scatter)))
        }
        this.win_lines = this.result.win_lines
        this.win_scatters = this.result.win_scatters
      } else {
        this.sound(loseSound)
      }
      window.result = this.result
      this.result = null
      this.playing = false
      this.show_demo_t = setTimeout(this.showResultDemo, 2000)
    },
    showResultDemo () {
      this.show_demo_t = 0
      this.animation.reels.forEach(el => el.win_clear())
      this.animation.t_show_demo = -1
      this.animation.is_show_demo = true
    },
    linesShow () {
      this.animation.lines.forEach(el => el.hide())
      this.animation.lines_show_demo = this.lines
      this.animation.lines_show_c = 0
      this.animation.lines_show_demo_t = 1
    },
    lineShow (line) {
      this.animation.is_line_showig = line
      this.animation.lines.forEach(line => line.hide())
      if (this.animation.lines[line - 1]) this.animation.lines[line - 1].hide()
      if (this.animation.lines[line - 1]) this.animation.lines[line - 1].show()
    },
    lineHide (line) {
      this.animation.is_line_showig = false
      if (this.animation.lines[line - 1] && this.animation.win_line_demo !== line) { this.animation.lines[line - 1].hide() }
      if (this.animation.win_line_demo && this.animation.win_line_demo !== line) { this.animation.lines[this.animation.win_line_demo - 1].show() }
    },
    render () {
      if (this.playing && this.animation.is_spinned && this.result) this.animation.spinStop()
      this.animation.is_render_planned = false
      if (this.animation.is_show_demo && this.win_loop.length > 0) {
        while (this.animation.t_show_demo === -1 || this.animation.t_show_demo > 2000) {
          const line = this.win_loop.shift()
          this.win_loop.push(line)
          this.animation.t_show_demo -= 2000
          if (this.animation.t_show_demo < 0) this.animation.t_show_demo = 0
          this.animation.reels.forEach(el => el.win_clear())
          if (!this.animation.is_line_showig) {
            this.line_blink = []
            this.animation.lines.forEach(el => el.hide())
          }
          if (line.type === 'scatter') {
            this.animation.win_line_demo = 0
            this.message = line.text
            this.win_scatters.forEach((scatters, reelIdx) => {
              scatters.forEach(scatter => this.animation.reels[reelIdx].win_add(scatter))
            })
          } else {
            this.animation.win_line_demo = parseInt(line.idx) + 1
            this.message = line.text
            if (!this.animation.is_line_showig) {
              this.line_blink = [parseInt(line.idx) + 1]
              this.animation.lines[parseInt(line.idx)].show()
            }
            const winLine = this.win_lines[line.idx]
            this.animation.reels.forEach((el, x) => {
              if (winLine[x] !== undefined && winLine[x] >= 0) { el.win_add(winLine[x]) }
            })
          }
        }
        this.animation.t_show_demo += Date.now() - this.animation.t
      }
      if (this.animation.lines_show_demo !== -1) {
        if (this.animation.lines_show_demo_t > 0) {
          if (this.animation.lines_show_c !== 0) this.animation.lines[this.animation.lines_show_c - 1].hide()
          if (this.animation.lines_show_c === this.animation.lines_show_demo) {
            if (this.animation.is_line_showig) { this.animation.lines[this.animation.is_line_showig - 1].show() }
            this.animation.lines_show_demo = -1
            this.animation.lines_show_c = 0
          } else {
            this.animation.lines[this.animation.lines_show_c].show()
            this.animation.lines_show_c++
            this.animation.lines_show_demo_t -= 50
          }
        }
        this.animation.lines_show_demo_t += Date.now() - this.animation.t
      }
      var ctx = this.animation.canvas.getContext('2d')
      ctx.clearRect(0, 0, this.animation.canvas.width, this.animation.canvas.height)
      this.animation.t = Date.now()

      this.animation.reels.forEach(el => el.draw())
      if (!this.animation.is_line_showig && this.animation.lines_show_demo === -1) this.animation.lines.forEach(el => el.draw())
      this.animation.reels.forEach(el => el.drawWin())
      if (this.animation.is_line_showig || this.animation.lines_show_demo !== -1) this.animation.lines.forEach(el => el.draw())

      this.animation.lines.forEach(el => el.calculate())
      this.animation.reels.forEach(el => el.calculate())
    },
    startAnimation () {
      this.animation.canvas = this.$refs.canvas
      const GameSlotsLine = function (data, game, color) {
        var self = this
        var mx = 245 // move x total
        var my = 100 // move y total
        var stx = 228 // step x
        var sty = 200 // step y
        if (color === undefined) color = 'white'
        this.self = this
        this.data = data
        this.game = game
        this.color = color
        this.alpha = 0
        this.shown_p = 0
        this.t = Date.now()
        this.display = false
        this.show = function () {
          self.display = true
        }
        this.hide = function () {
          self.display = false
        }
        this.draw = function () {
          if (self.shown_p === 0) return
          var ctx = self.game.canvas.getContext('2d')
          ctx.strokeStyle = self.color
          ctx.lineWidth = 4
          ctx.shadowOffsetX = 0
          ctx.shadowOffsetY = 0
          ctx.shadowColor = self.color
          ctx.lineCap = 'round'
          ctx.globalAlpha = self.alpha
          var la = [
            self.alpha * 5,
            self.alpha * 15,
            self.alpha * 30,
            self.alpha * 60,
            self.alpha * 100
          ]
          for (var i in la) {
            ctx.shadowBlur = la[i]
            ctx.beginPath()
            ctx.moveTo(mx - 150, my + sty * self.data[0])
            for (i in self.data) { ctx.lineTo(mx + stx * i, my + sty * self.data[i]) }
            ctx.lineTo(mx + stx * i + 150, my + sty * self.data[i])
            ctx.stroke()
          }
          ctx.globalAlpha = 1
          ctx.strokeStyle = self.color
          ctx.lineWidth = 0
          ctx.shadowOffsetX = 0
          ctx.shadowOffsetY = 0
          ctx.shadowBlur = 0
          ctx.shadowColor = self.color
          ctx.lineCap = 'round'
        }
        this.calculate = function () {
          if (self.display && self.shown_p < 1) {
            self.shown_p += (self.game.t - self.t) / 300
            if (self.shown_p > 1) self.shown_p = 1
            self.alpha = self.shown_p
          }

          if (!self.display && self.shown_p > 0) {
            self.shown_p -= (self.game.t - self.t) / 100
            if (self.shown_p < 0) self.shown_p = 0
            self.alpha = self.shown_p
          }
          if (!self.display && self.shown_p === 0 && self.alpha !== 0) { self.alpha = 1 }
          self.t = self.game.t
        }
        return this
      }
      const GameSlotsReel = function (idx, reelIds, game) {
        var self = this
        var mx = 245 // move x total
        var my = 100 // move y total
        var stx = 228 // step x
        var sty = 200 // step y
        this.idx = idx
        this.self = this
        this.ids = reelIds
        this.game = game
        this.speed_frame = 0
        this.animation_frame = 0
        this.speed = 0
        this.speed_max = game.speed_max
        this.delta = 0
        this.delta_stop = 0
        this.stop_to = 0
        this.position = 0
        this.t = Date.now()
        this.is_spin = false
        this.is_stopping = false
        this.is_spinning = false
        this.delta_pos_stop = 4
        this.delta_stop_max = 200 * this.delta_pos_stop
        this.syms_win = []
        this.startSpin = function () {
          self.is_spin = true
          self.is_stopping = false
          self.is_spinning = true
        }
        this.stopSpin = function (x) {
          if (typeof (x) === 'undefined') x = 0
          self.stop_to = x
          self.is_spin = false
        }
        this.onspinned = null
        this.onstopped = null
        this.win = function (syms) {
          self.syms_win = syms
        }
        this.win_add = function (sym) {
          self.syms_win.push(sym)
        }
        this.win_clear = function () {
          self.syms_win = []
        }
        this.getReelPosition = function (pos) {
          if (pos < 0) {
            return self.getReelPosition(self.ids.length + pos)
          } else if (pos >= self.ids.length) {
            return self.getReelPosition(pos - self.ids.length)
          }
          return pos
        }
        this.draw = function () {
          var ctx = self.game.canvas.getContext('2d')
          var dFrom = 0
          var dTo = 0
          var i
          var frame = null

          if (self.speed > 0) {
            while (my + sty * (-dFrom) + Math.round(self.game.sym[self.ids[self.getReelPosition(self.position - 1 - dFrom)]].speed_frames[self.speed_frame].height * 0.5) + self.delta > 0) dFrom++
            while (my + sty * (dTo) - Math.round(self.game.sym[self.ids[self.getReelPosition(self.position - 1 - dFrom)]].speed_frames[self.speed_frame].height * 0.5) + self.delta < self.game.canvas.height) dTo++
          }

          for (i = -dFrom; i < 3 + dTo; i++) {
            if (self.speed === 0 && self.syms_win.indexOf(self.getReelPosition(self.position + i)) !== -1) { frame = null } else { frame = self.game.sym[self.ids[self.getReelPosition(self.position + i)]].speed_frames[self.speed_frame] }
            if (frame) { ctx.drawImage(frame, mx + stx * self.idx - Math.round(frame.width * 0.5), my + sty * i - Math.round(frame.height * 0.5) + self.delta) }
          }

          if (self.speed > 0) {
            ctx.strokeStyle = 'white'
            ctx.lineWidth = 5
            ctx.shadowOffsetX = 0
            ctx.shadowOffsetY = 10
            ctx.shadowColor = 'white'
            ctx.lineCap = 'round'

            var la = [5, 15, 30]
            var a = 1 - Math.abs((self.delta * 2 - sty) / sty)

            if (self.speed < self.speed_max / 2) { ctx.globalAlpha = a } else { ctx.globalAlpha = (2 - a) / 2 }

            for (i in la) {
              ctx.shadowBlur = la[i]
              ctx.beginPath()
              ctx.moveTo((mx - 100) + stx * self.idx + (100 - 100 * a), -10)
              ctx.lineTo((mx - 100) + stx * self.idx + 200 - (100 - 100 * a), -10)
              ctx.stroke()
            }

            ctx.shadowOffsetY = -10
            for (i in la) {
              ctx.shadowBlur = la[i]
              ctx.beginPath()
              ctx.moveTo((mx - 100) + stx * self.idx + (100 - 100 * a), self.game.canvas.height + 10)
              ctx.lineTo((mx - 100) + stx * self.idx + 200 - (100 - 100 * a), self.game.canvas.height + 10)
              ctx.stroke()
            }

            ctx.globalAlpha = 1
            ctx.strokeStyle = 'white'
            ctx.lineWidth = 0
            ctx.shadowOffsetX = 0
            ctx.shadowOffsetY = 0
            ctx.shadowBlur = 0
            ctx.shadowColor = 'white'
            ctx.lineCap = 'round'
          }
        }

        this.drawWin = function () {
          if (self.speed > 0) return

          var ctx = self.game.canvas.getContext('2d')

          var frame = null
          for (var i = 0; i < 3; i++) {
            if (self.speed === 0 && self.syms_win.indexOf(self.getReelPosition(self.position + i)) !== -1) {
              frame = self.game.sym[self.ids[self.getReelPosition(self.position + i)]].animation[Math.round(self.animation_frame)]
              if (frame) { ctx.drawImage(frame, mx + stx * self.idx - Math.round(frame.width * 0.5), my + sty * i - Math.round(frame.height * 0.5) + self.delta) }
            }
          }
        }

        this.calculate = function () {
          self.delta += Math.round((self.game.t - self.t) * self.speed * 0.0005)

          if (!self.is_spin) {
            self.delta_stop -= Math.round((self.game.t - self.t) * self.speed * 0.0005)
            if (self.delta_stop < 0) self.delta_stop = 0
          }

          while (self.delta >= sty) {
            self.delta -= sty
            self.position = self.getReelPosition(self.position - 1)
            if (!self.is_spin && !self.is_stopping && self.getReelPosition(self.position - self.delta_pos_stop) === self.stop_to) {
              self.is_stopping = true
              self.delta_stop = self.delta_stop_max - self.delta
            }
          }

          while (self.delta <= -sty) {
            self.delta += sty
            self.position = self.getReelPosition(self.position + 1)
          }

          if (self.is_spin && self.speed < self.speed_max) {
            self.speed += Math.round((self.game.t - self.t) * 10000 * 0.0005)
            if (self.speed > self.speed_max) self.speed = self.speed_max

            self.speed_frame = Math.round(self.speed / 1000 * 2)
            if (self.speed_frame < 0) self.speed_frame = 0
            if (self.speed_frame > Math.round(self.speed_max / 1000 * 2)) self.speed_frame = Math.round(self.speed_max / 1000 * 2)

            if (!(self.speed < self.speed_max) && typeof (self.onspinned) === 'function') { self.onspinned(self) }
          }

          if (!self.is_spin && self.is_stopping && self.speed > 0) {
            var newSpeed = Math.round(Math.pow(self.delta_stop / self.delta_stop_max, 0.5) * self.speed_max)
            if (newSpeed < self.speed) { self.speed = newSpeed }
            Math.round((self.game.t - self.t) * 5000 * 0.0005)
            if (self.speed < 0) self.speed = 0
            if (self.speed === 0) {
              self.is_spinning = false
              self.is_stopping = false
              self.delta = 0
              if (typeof (self.onstopped) === 'function') { self.onstopped(self) }
            }
            self.speed_frame = Math.round(self.speed / 1000 * 2)
            if (self.speed_frame < 0) self.speed_frame = 0
            if (self.speed_frame > Math.round(self.speed_max / 1000 * 2)) self.speed_frame = Math.round(self.speed_max / 1000 * 2)
          }

          if (self.speed === 0 && self.syms_win.length > 0) {
            self.animation_frame += (self.game.t - self.t) * 0.03
            while (Math.round(self.animation_frame) >= self.game.animation_time) self.animation_frame -= self.game.animation_time
          }

          self.t = self.game.t
        }
        return this
      }
      this.animation.lines_indexes.forEach(el => this.animation.lines.push(new GameSlotsLine(el, this.animation, this.$vuetify.theme.isDark ? 'white' : getComputedStyle(document.body).getPropertyValue('--v-primary-lighten1').trim())))
      this.config.reels.forEach((el, idx) => this.animation.reels.push(new GameSlotsReel(idx, el, this.animation)))
      this.animation.t = Date.now()
      this.animation.spinStart = () => {
        this.animation.is_show_demo = false
        this.animation.reels.forEach(el => el.win_clear())
        this.animation.lines.forEach(el => el.hide())
        var t = 0
        this.animation.reels.forEach((el, idx) => {
          setTimeout(() => {
            el.startSpin()
            this.sound(startSound)
            if (idx === 4) this.soundLoop(spinSound)
          }, t += 250)
        })
      }
      this.animation.spinStop = () => {
        this.animation.is_spinned = false
        var t = 0
        this.animation.reels.forEach((reel, idx) => {
          setTimeout(() => reel.stopSpin(this.result.reels[idx]), t += 500)
        })
      }
      this.animation.gamestop = () => {
        this.sound(stopSound)
        if (Object.values(this.animation.reels).reduce((acc, val) => acc + val.speed, 0) === 0) {
          this.showResult()
          this.soundStop(spinSound)
        }
      }
      this.animation.reels[4].onspinned = () => { this.animation.is_spinned = true }
      this.animation.reels.forEach(el => { el.onstopped = this.animation.gamestop })
      this.animation.requestAnimationFrame_get()
      this.i = setInterval(() => {
        if (!this.animation.is_render_planned) {
          this.animation.is_render_planned = true
          this.animation.requestAnimationFrame(this.render)
        }
      }, 40)
    }
  }
}
</script>

<style lang="scss" scoped>
.lines-input {
  max-width: 120px;
}

.total-bet-input {
  max-width: 100px;
}

.message-container {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
}

@media (min-aspect-ratio: 8/5) {
  canvas {
    margin: 0;
    height: calc(100vh - 300px);
    width: auto;
  }
  .permanent-navbar {
    canvas {
      height: calc(100vh - 400px);
    }
  }
}

@media (max-height: 550px) {
  canvas {
    margin: 0;
    height: calc(100vh - 100px);
    width: auto;
  }
}

@media (max-aspect-ratio: 8/5) {
  canvas {
    margin: 0;
    width: calc(100vw - 100px);
    height: auto;
  }
  .permanent-navbar {
    canvas {
      width: calc(100vw - 350px);
    }
  }
}

@media (max-width: 960px) {
  .message-container {
    top: 12px;
  }
}

@media (max-width: 600px) {
  .line-btns button {
    min-width: 25px !important;
    font-size: 0.5rem;
  }
}
</style>

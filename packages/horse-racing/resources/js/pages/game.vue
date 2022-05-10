<template>
  <div :class="{ 'd-flex justify-center align-center': !ready, 'fill-height': !ready || !($vuetify.breakpoint.width > $vuetify.breakpoint.height)}">
    <div v-if="ready" class="d-flex justify-center align-center" :class="{'fill-height': !($vuetify.breakpoint.width > $vuetify.breakpoint.height)}">
      <div v-if="playing && $vuetify.breakpoint.width > $vuetify.breakpoint.height" class="animation-container">
        <canvas ref="animation" class="animation" :class="{shot: game.shot !== 0, paused: game.paused}" />
        <v-card class="runners px-4 py-2" flat>
          <transition-group name="flip-list" tag="ul" class="pl-0">
            <div v-for="runner in runners_sorted" :key="runner.num" class="body-1 py-1">
              <v-chip class="mr-4" :color="runner.colors.horse.pad.background" :text-color="runner.colors.horse.pad.text" label x-small>
                {{ runner.num }}
              </v-chip>
              {{ runner.name }}
            </div>
          </transition-group>
        </v-card>
        <v-progress-linear v-model="progress" class="progress" />
        <transition name="scale">
          <v-card v-if="game.step_finish === 4 && game.shot === 0" class="results px-16 pt-4 pb-4">
            <div class="align-center">
              <v-alert
                v-show="message"
                dense
                outlined
                text
                color="primary"
                class="text-center"
              >
                {{ message }}
              </v-alert>
              <v-btn color="primary" :loading="loading" :disabled="loading || totalBetAmount > account.balance" @click="play">
                {{ $t('Play again') }}
              </v-btn>
              <v-btn color="primary" outlined :disabled="loading" @click="gameStop">
                {{ $t('Change bet') }}
              </v-btn>
            </div>
          </v-card>
        </transition>
      </div>
      <v-container v-else-if="$vuetify.breakpoint.width > $vuetify.breakpoint.height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" md="8">
            <v-card>
              <v-toolbar>
                <v-toolbar-title>
                  {{ $t('Place your bet') }}
                </v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-form v-model="formIsValid">
                  <v-row>
                    <v-col v-for="(runner, index) in game.runners" :key="index" sm="12" md="6">
                      <v-row>
                        <v-col cols="6">
                          <h3 class="heading text-center mb-3">
                            <v-chip
                              class="ma-2"
                              :color="runner.colors.horse.pad.background"
                              :text-color="runner.colors.horse.pad.text"
                              label
                              small
                            >
                              {{ runner.num }}
                            </v-chip>
                            {{ runner.name }}
                          </h3>
                          <v-card height="100" flat class="d-flex align-center justify-center">
                            <v-img :src="runner.preview" contain height="180" />
                          </v-card>
                        </v-col>
                        <v-col>
                          <template v-for="(title, type) in betTypes">
                            <v-text-field
                              :key="type"
                              v-model.number="inputBets[type][index]"
                              dense
                              outlined
                              class="text-center mb-2"
                              :label="title"
                              :disabled="loading"
                              :rules="[validationInteger, v => validationMin(v, minBet), v => validationMax(v, Math.min(Math.floor(account.balance), maxBet))]"
                              :hide-details="true"
                              prepend-inner-icon="mdi-minus"
                              append-icon="mdi-plus"
                            >
                              <template v-slot:prepend-inner>
                                <v-btn small text icon color="primary" @click="minimizeBet(type, index)">
                                  <v-icon small>
                                    mdi-arrow-down
                                  </v-icon>
                                </v-btn>
                                <v-btn small text icon color="primary" @click="decrementBet(type, index)">
                                  <v-icon small>
                                    mdi-minus
                                  </v-icon>
                                </v-btn>
                              </template>
                              <template v-slot:append>
                                <v-btn small text icon color="primary" @click="incrementBet(type, index)">
                                  <v-icon small>
                                    mdi-plus
                                  </v-icon>
                                </v-btn>
                                <v-btn small text icon color="primary" @click="maximizeBet(type, index)">
                                  <v-icon small>
                                    mdi-arrow-up
                                  </v-icon>
                                </v-btn>
                              </template>
                              <template v-slot:append-outer>
                                <v-tooltip bottom>
                                  <template v-slot:activator="{ on }">
                                    <v-chip
                                      color="primary"
                                      label
                                      class="payout-chip justify-center mt-n1"
                                      v-on="on"
                                    >
                                      x {{ paytable[type][index] }}
                                    </v-chip>
                                  </template>
                                  <span>{{ $t('Payout') }} {{ $t('bet x {0}', [paytable[type][index]]) }}</span>
                                </v-tooltip>
                              </template>
                            </v-text-field>
                          </template>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
              <v-card-actions class="justify-center pb-5">
                <v-btn
                  outlined
                  color="primary"
                  :disabled="loading || !bets.length"
                  @click="reset"
                >
                  {{ $t('Reset') }}
                </v-btn>
                <v-btn
                  color="primary"
                  :loading="loading"
                  :disabled="isPlayDisabled"
                  class="ml-2"
                  @click="play"
                >
                  {{ $t('Play') }}
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
      <div v-else class="d-flex fill-height align-center justify-center">
        <v-alert
          dense
          outlined
          text
          color="primary"
          class="text-center"
        >
          {{ $t('Please use landscape (horizontal) orientation.') }}
        </v-alert>
      </div>
    </div>
    <block-preloader v-else />
  </div>
</template>

<script>
import loadImage from 'image-promise'
import CRunner from './helpers/runner'

import axios from 'axios'

import { mapState, mapActions } from 'vuex'
import { config } from '~/plugins/config'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'

import clickSound from '~/../audio/common/click.wav'
import winSound from 'packages/horse-racing/resources/audio/win.wav'
import loseSound from 'packages/horse-racing/resources/audio/lose.wav'
import flashSound from 'packages/horse-racing/resources/audio/flash.wav'
import gallopSound from 'packages/horse-racing/resources/audio/gallop.wav'
import BlockPreloader from '~/components/BlockPreloader'

import imgBg from 'packages/horse-racing/resources/images/background.png'
import imgForest from 'packages/horse-racing/resources/images/forest.png'
import imgMeadow from 'packages/horse-racing/resources/images/meadow.png'
import imgGrass from 'packages/horse-racing/resources/images/grass.jpg'
import imgSand from 'packages/horse-racing/resources/images/sand.jpg'
import imgFence from 'packages/horse-racing/resources/images/fence.png'
import imgFencePost from 'packages/horse-racing/resources/images/fence_post.png'

export default {
  name: 'HorseRacing',

  components: { BlockPreloader },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      loading: false,
      playing: false,
      ready: false,
      message: null,
      inputBets: [],
      positions: [],
      progress: 0,
      runners_sorted: [],
      game: {
        p: 0,
        runners: [],
        positions: [],
        shot: 0,
        static: {
          bg: null,
          bg_pos: 0,
          bg_speed: 1,
          forest: null,
          forest_pos: 0,
          forest_speed: 2,
          meadow: null,
          meadow_pos: 0,
          meadow_speed: 3,
          grass: null,
          grass_pos: 0,
          grass_speed: 6,
          sand: null,
          sand_pos: 0,
          sand_speed: 8,
          fence: null,
          fence_pos: 0,
          fence_speed: 8,
          fence_post: null,
          fence_post_pos: 0
        },
        buf: null,
        buf_ctx: null,
        ctx: null,
        width: 0,
        height: 0,
        t_frame: 5,
        t_last: 0,
        t_repos: 0,
        paused: false,
        complete: 0
      },
      betTypes: [
        this.$t('Win'),
        this.$t('Place'),
        this.$t('Show')
      ]
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    config () {
      return config('horse-racing') || {}
    },
    defaultBet () {
      return parseInt(this.config.default_bet_amount, 10)
    },
    minBet () {
      return parseInt(this.config.min_bet, 10)
    },
    maxBet () {
      return parseInt(this.config.max_bet, 10)
    },
    betStep () {
      return parseInt(this.config.bet_change_amount, 10)
    },
    paytable () {
      return this.config.paytable
    },
    isPlayDisabled () {
      return !this.provablyFairGame.hash || !this.formIsValid || !this.bets.length || this.playing || this.totalBetAmount > this.account.balance
    },
    bets () {
      const bets = []

      this.inputBets.forEach((positionBets, type) => {
        positionBets.forEach((bet, position) => {
          if (bet > 0) {
            bets.push({ type, positions: [position], bet })
          }
        })
      })

      return bets
    },
    totalBetAmount () {
      return this.bets.reduce((a, item) => a + item.bet, 0)
    }
  },

  beforeDestroy () {
    this.gameStop()
  },

  created () {
    // wait until next tick to ensure all computed properties are available (important to set lines property)
    window.addEventListener('resize', this.resize)
    this.$nextTick(() => {
      this.init()
    })
  },
  destroyed () {
    window.removeEventListener('resize', this.resize)
  },
  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    async init () {
      // init bets object
      this.betTypes.forEach(betType => {
        // this.$set(this.inputBets, betType, Array(this.config.runners.length).fill(0))
        this.inputBets.push(Array(this.config.runners.length).fill(0))
      })
      this.game.runners = []
      for (var i in this.config.runners) {
        var runner = new CRunner(this.config.runners[i].colors, this.config.runners[i].name, parseInt(i) + 1)
        await runner.init()
        this.game.runners.push(runner)
      }
      this.game.static.bg = await loadImage(imgBg)
      this.game.static.bg_pos = 0
      this.game.static.forest = await loadImage(imgForest)
      this.game.static.forest_pos = 0
      this.game.static.meadow = await loadImage(imgMeadow)
      this.game.static.meadow_pos = 0
      this.game.static.grass = await loadImage(imgGrass)
      this.game.static.grass_pos = 0
      this.game.static.sand = await loadImage(imgSand)
      this.game.static.sand_pos = 0
      this.game.static.fence = await loadImage(imgFence)
      this.game.static.fence_pos = 0
      this.game.static.fence_post = await loadImage(imgFencePost)

      this.game.buf = document.createElement('canvas')
      this.game.buf_ctx = this.game.buf.getContext('2d')

      this.requestAnimationFrameGet()

      this.ready = true
    },
    requestAnimationFrameGet () {
      var _raf = window.requestAnimationFrame ||
                          window.mozRequestAnimationFrame ||
                          window.webkitRequestAnimationFrame ||
                          window.msRequestAnimationFrame ||
                          window.oRequestAnimationFrame

      this.requestAnimationFrame = _raf ? _raf.bind(window) : null
    },
    drawBg (frames) {
      var a, p, w, h

      a = this.game.height * 0.5 / this.game.static.bg.height
      p = this.game.static.bg_pos * a
      w = this.game.static.bg.width * a
      h = this.game.static.bg.height * a
      while (p < this.game.width) {
        this.game.buf_ctx.drawImage(this.game.static.bg, p, 0, w, h)
        p += w
      }
      this.game.static.bg_pos -= frames * this.game.static.bg_speed
      if (this.game.static.bg_pos < -this.game.static.bg.width) { this.game.static.bg_pos += this.game.static.bg.width }

      a = this.game.height * 0.5 / this.game.static.forest.height
      p = this.game.static.forest_pos * a
      w = this.game.static.forest.width * a
      h = this.game.static.forest.height * a
      while (p < this.game.width) {
        this.game.buf_ctx.drawImage(this.game.static.forest, p, 0, w, h)
        p += w
      }
      this.game.static.forest_pos -= frames * this.game.static.forest_speed
      if (this.game.static.forest_pos < -this.game.static.forest.width) { this.game.static.forest_pos += this.game.static.forest.width }

      a = this.game.height * 0.5 / this.game.static.meadow.height
      p = this.game.static.meadow_pos * a
      w = this.game.static.meadow.width * a
      h = this.game.static.meadow.height * a
      while (p < this.game.width) {
        this.game.buf_ctx.drawImage(this.game.static.meadow, p, 0, w, h)
        p += w
      }
      this.game.static.meadow_pos -= frames * this.game.static.meadow_speed
      if (this.game.static.meadow_pos < -this.game.static.meadow.width) { this.game.static.meadow_pos += this.game.static.meadow.width }
    },
    drawTrack (frames) {
      var a, p, w, hG, hO, hS, y

      y = this.game.height * 0.49
      a = this.game.height * 0.5 / this.game.static.bg.height

      p = this.game.static.grass_pos * a
      w = this.game.static.grass.width * a
      hG = this.game.static.grass.height * a
      while (p < this.game.width) {
        this.game.buf_ctx.drawImage(this.game.static.grass, p, y, w, hG)
        this.game.buf_ctx.drawImage(this.game.static.grass, p, y + hG, w, hG)
        p += w
      }
      this.game.static.grass_pos -= frames * this.game.static.grass_speed
      if (this.game.static.grass_pos < -this.game.static.grass.width) { this.game.static.grass_pos += this.game.static.grass.width }

      p = this.game.static.sand_pos * a
      w = this.game.static.sand.width * a
      hS = this.game.static.sand.height * a
      while (p < this.game.width) {
        for (var dy = y + hG * 2; dy < this.game.height; dy += hS) { this.game.buf_ctx.drawImage(this.game.static.sand, p, dy, w, hS) }
        p += w
      }
      this.game.static.sand_pos -= frames * this.game.static.sand_speed
      if (this.game.static.sand_pos < -this.game.static.sand.width) { this.game.static.sand_pos += this.game.static.sand.width }

      p = this.game.static.fence_pos * a
      w = this.game.static.fence.width * a
      hO = this.game.static.fence.height * a
      while (p < this.game.width) {
        if (
          !this.game.step_finish ||
                !(
                  (p < this.game.static.fence_post_pos * a && this.game.static.fence_post_pos * a < p + this.game.static.fence.width * a) ||
                  (p < this.game.static.fence_post_pos * a + this.game.static.fence_post.width * a && this.game.static.fence_post_pos * a + this.game.static.fence_post.width * a < p + this.game.static.fence.width * a)
                )
        ) { this.game.buf_ctx.drawImage(this.game.static.fence, p, y + hG * 2 - hO + 18 * a, w, hO) }
        p += w
      }
      this.game.static.fence_pos -= frames * this.game.static.fence_speed
      if (this.game.static.fence_pos < -this.game.static.fence.width) { this.game.static.fence_pos += this.game.static.fence.width }

      if (this.game.step_finish) {
        this.game.buf_ctx.drawImage(
          this.game.static.fence_post,
          this.game.static.fence_post_pos * a,
          y + hG * 2 - this.game.static.fence_post.height * a + 25 * a,
          this.game.static.fence_post.width * a,
          this.game.static.fence_post.height * a)

        this.game.static.fence_post_pos -= frames * this.game.static.fence_speed
      }
    },
    animation () {
      if (!this.playing) return
      var t = (new Date()).getTime(); var frames = Math.floor((t - this.game.t_last) / this.game.t_frame)
      if (!(this.$vuetify.breakpoint.width > this.$vuetify.breakpoint.height)) {
        this.game.t_start += t - this.game.t_last
        this.game.t_last = t
        this.requestAnimationFrame(this.animation)
        return
      }
      if (this.game.shot && this.game.shot + 300 < t) {
        this.soundLoop(gallopSound)
        this.game.shot = 0
        this.requestAnimationFrame(this.animation)
        this.game.t_last = t
        this.game.runners.forEach(runner => runner.continue())
        if (this.game.step_finish === 4) this.game.endFunc()
        return
      } else if (this.game.shot) {
        this.requestAnimationFrame(this.animation)
        return
      }

      this.game.t_last += this.game.t_frame * frames
      this.drawBg(frames)
      this.drawTrack(frames)

      var a = this.game.height * 0.5 / this.game.static.bg.height
      var aH = a * 4
      var pos = this.game.height * 0.5 + 300 * a
      var posStep = (this.game.height - 20 - pos) / (this.game.runners.length - 1)

      var isRepos = false
      for (var i in this.game.runners) {
        this.game.buf_ctx.drawImage(
          this.game.runners[i].getFrame(),
          (this.game.width - 100 * a - this.game.runners[i].frmW * aH) * (this.game.runners[i].position * 0.01),
          pos - this.game.runners[i].frmH * aH + 40 * aH,
          this.game.runners[i].frmW * aH,
          this.game.runners[i].frmH * aH)
        pos += posStep
        if (frames > 0 && !this.game.step_finish) {
          var d = this.config.animation.overtake_multiplier * ((1 - this.game.runners[i].p / 100 - Math.random())) * 0.5
          this.game.runners[i].dir += d
          this.game.runners[i].p += this.game.runners[i].dir * frames
          this.game.runners[i].position =
            this.game.runners[i].p * (50 - Math.abs(this.game.complete - 50)) * 2 / 100 +
            this.game.runners[i].target * (this.game.complete > this.game.runners[i].nature ? ((this.game.complete - this.game.runners[i].nature) / (100 - this.game.runners[i].nature)) : 0)
        } else if (
          this.game.step_finish &&
                      this.game.step_finish < 4 &&
                      !this.game.runners[i].finished &&
                      (this.game.width - 100 * a - this.game.runners[i].frmW * aH) * (this.game.runners[i].position * 0.01) + this.game.runners[i].frmW * aH - 40 * aH >
                      this.game.static.fence_post_pos * a + this.game.static.fence_post.width * a * 0.5
        ) {
          this.game.runners[i].finished = true
          this.game.runners.forEach(runner => runner.pause())
          this.game.shot = t
          this.game.step_finish++
          this.sound(flashSound)
          this.soundStop(gallopSound)
        }
        if ((t - this.game.t_repos) > 250) {
          var rpos = this.game.runners.reduce((acc, runner) => (acc + (runner.position > this.game.runners[i].position ? 1 : 0)), 0)
          if (this.game.positions[i].pos !== rpos) {
            this.game.positions[i].pos = rpos
            isRepos = true
          }
        }
      }
      if (isRepos) this.game.t_repos = t
      if (frames > 0) {
        this.game.complete = (t - this.game.t_start) / this.game.length * 100
        if (this.game.complete > 100) {
          this.game.complete = 100
          if (this.game.step_finish === 0) {
            this.game.step_finish = 1
            this.game.static.fence_post_pos = this.game.width / a + this.game.static.fence_post.width / a
          }
        }
        if (Math.round(this.game.complete) !== this.progress) this.progress = Math.round(this.game.complete)
      }

      if (this.runners_sorted.reduce((acc, item) => acc + item.num, '') !== Object.values(this.game.positions).sort((a, b) => (a.pos === b.pos ? (a.num > b.num ? -1 : 1) : (a.pos > b.pos ? 1 : -1))).reduce((acc, item) => acc + item.num, '')) {
        this.runners_sorted = Object.values(this.game.positions).sort((a, b) => (a.pos === b.pos ? (a.num > b.num ? -1 : 1) : (a.pos > b.pos ? 1 : -1)))
        // this.sound(clickSound)
      }

      this.game.ctx.drawImage(this.game.buf, 0, 0)

      if (this.game.step_finish && this.game.static.fence_post_pos + this.game.static.fence_post.width * a < 0) {
        this.game.paused = true
        this.soundStop(gallopSound)
      } else { this.requestAnimationFrame(this.animation) }
    },
    gameStop () {
      this.soundStop(gallopSound)
      this.playing = false
    },
    gameAnimation () {
      return new Promise((resolve, reject) => {
        this.playing = true
        this.$nextTick(() => {
          this.resize()
          this.game.ctx = this.$refs.animation.getContext('2d')
          this.game.length = this.config.animation.length * 1000

          this.game.positions = []
          var i
          for (i = 0; i < this.game.runners.length; i++) {
            this.game.positions.push({
              name: this.game.runners[i].name,
              num: this.game.runners[i].num,
              pos: i,
              colors: this.game.runners[i].colors
            })
            this.game.runners[i].finished = false
            this.game.runners[i].position = 0
            this.game.runners[i].p = 50
            this.game.runners[i].nature = Math.random() * 50 + 25
            // this.game.runners[i].position_start = 15 + 70 * Math.random()
            // this.game.runners[i].dir = 0.015 // 0.05 + Math.random() * 0.05
            this.game.runners[i].dir = 0 // 0.05 + Math.random() * 0.05
          }

          var last = 100 - (40 * Math.random())
          for (i = 0; i < this.game.runners.length; i++) {
            var runner = this.positions[i]
            // var runner = this.positions.indexOf(i)
            // if (runner !== -1) {
            if (i === 0) {
              this.game.runners[runner].target = last
            } else {
              var max = last
              var min = (this.game.runners.length - 1 - i) * (2 + 3 * Math.random())
              min += (max - min - 1) * Math.random()
              last -= Math.random() * (max - min)
              this.game.runners[runner].target = last
            }
            last--
            // }
          }

          this.runners_sorted = Object.values(this.game.positions).sort((a, b) => (a.pos === b.pos ? (a.num > b.num ? -1 : 1) : (a.pos > b.pos ? 1 : -1)))
          this.game.step_finish = 0
          this.game.complete = 0
          this.progress = 0
          this.game.t_start = (new Date()).getTime()
          this.game.paused = false
          this.game.t_last = (new Date()).getTime()
          this.game.endFunc = resolve
          this.soundLoop(gallopSound)
          this.requestAnimationFrame(this.animation)
        })
      })
    },
    resize () {
      if (this.$refs.animation) {
        this.game.width = this.$refs.animation.clientWidth
        this.game.height = this.$refs.animation.clientHeight
        this.$refs.animation.width = this.game.width
        this.$refs.animation.height = this.game.height
        this.game.buf.width = this.game.width
        this.game.buf.height = this.game.height
      }
    },
    reset () {
      this.betTypes.forEach((title, type) => {
        this.inputBets[type].forEach((bet, index) => {
          this.minimizeBet(type, index)
        })
      })
    },
    minimizeBet (type, index) {
      this.inputBets[type].splice(index, 1, this.minBet)
    },
    maximizeBet (type, index) {
      this.inputBets[type].splice(index, 1, this.maxBet)
    },
    decrementBet (type, index) {
      const bet = this.inputBets[type][index] - this.betStep
      this.inputBets[type].splice(index, 1, Math.max(this.minBet, bet))
    },
    incrementBet (type, index) {
      const bet = this.inputBets[type][index] + this.betStep
      this.inputBets[type].splice(index, 1, Math.min(this.maxBet, bet))
    },
    async play () {
      this.sound(clickSound)
      this.loading = true

      this.message = null
      this.positions = []

      // update user balance
      this.updateUserAccountBalance(this.account.balance - this.totalBetAmount)

      // API request params
      const endpoint = this.getRoute('play')
      const requestParams = { hash: this.provablyFairGame.hash, bets: this.bets }

      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)

      this.loading = false
      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })

      this.positions = game.gameable.positions.map(item => parseInt(item))

      await this.gameAnimation()

      // this.playing = false

      // update balance
      this.updateUserAccountBalance(game.account.balance)

      // play sound
      if (game.win > 0) {
        this.sound(winSound)
        this.message = this.$t('You won') + ' ' + game.win
      } else {
        this.sound(loseSound)
        this.message = this.$t('You lost')
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.payout-chip {
  width: 60px;
}

@keyframes shotani {
  0% {
    filter: grayscale(0) brightness(1);
  }
  25% {
    filter: grayscale(1) brightness(1.5);
  }
  50% {
    filter: grayscale(0) brightness(1);
  }
  75% {
    filter: grayscale(1) brightness(1.5);
  }
  100% {
    filter: grayscale(0) brightness(1);
  }
}
@keyframes pauseani {
  0% {
    filter: grayscale(0) brightness(1);
  }
  100% {
    filter: grayscale(1) brightness(1.5);
  }
}
.animation-container {
  position:relative;
  width: 100%;
  height: calc(100vh - 112px);
  .progress {
    position: absolute;
    bottom:0;left:0;right:0;
  }
  .runners {
    top: 20px;
    left: 20px;
    background: rgba(0,0,0,0.3);
    position: absolute;
  }
  .animation {
    width: 100%;
    height: 100%;
    &.shot {
      animation: shotani  0.3s infinite ease-in-out;
    }
    &.paused {
      filter: grayscale(1) brightness(1.5);
      animation: pauseani  0.1s;
    }
  }
  .results {
    position: absolute;
    top:20px;
    left: 50%;
    transform:translateX(-50%);
  }
}
.flip-list-move {
  transition: transform 0.1s;
}
</style>

<template>
  <div class="fill-height">
    <multiplayer-game-event
      @game-init="onGameInit($event)"
      @event="onEvent($event)"
    />
    <div v-if="!ready" class="d-flex flex-column fill-height py-3 align-center justify-center">
      <block-preloader />
    </div>
    <div v-else-if="landscape" class="d-flex flex-column fill-height justify-space-between">
      <div class="game fill-height d-flex flex-row justify-center py-4">
        <div class="game-view flex-shrink-0">
          <div class="game-info">
            <div class="last-crashed">
              <transition-group name="staggered-fade" tag="ul" class="pa-0 d-flex flex-row-reverse justify-start" :css="false" @before-enter="beforeEnterC" @enter="enterC" @leave="leaveC">
                <li v-for="item in crashedAt" :key="`crashed-${item.id}`" class="d-flex justify-center overflow-hidden">
                  <div class="px-2">
                    {{ item.payout }}
                  </div>
                </li>
              </transition-group>
            </div>
          </div>
          <div class="game-status text-center">
            <template v-if="betting">
              <div class="text-uppercase">
                {{ $t('Place your bets') }}
              </div>
              <div class="game-time text-h2 text--secondary">
                <countdown-timer v-if="multiplayerGame.end_time_unix" :end-date="(multiplayerGame.end_time_unix - timeDiff) / 1000" class="text-no-wrap" />
              </div>
            </template>
            <template v-else-if="playing">
              <div>&nbsp;</div>
              <div>
                <span class="text-h4">x</span>
                <span class="text-h2">{{ currentPayout.toFixed(2) }}</span>
              </div>
            </template>
            <template v-else-if="!betting && !playing">
              <div v-if="crashedAt.length !== 0" class="error--text">
                <div class="text-uppercase">
                  {{ $t('Crashed at') }}
                </div>
                <div>
                  <span class="text-h4">x</span>
                  <span class="text-h2">{{ crashedAt[0].payout.toFixed(2) }}</span>
                </div>
              </div>
              <div v-if="crashedAt.length === 0" class="text-uppercase">
                {{ $t('Waiting next game') }}
              </div>
            </template>
          </div>
          <div ref="animation" class="animation">
            <svg key="planet" class="planet" width="90" height="90"><image :xlink:href="animation.sprites.planet" width="90" height="90" /></svg>
            <div class="subanimation">
              <svg v-for="i in cloudsCount" :key="`cloud-${i}`" :ref="`cloud-${i - 1}`" class="cloud" width="130" height="130">
                <image :xlink:href="animation.sprites.clouds" width="130" height="130"/>
              </svg>
              <svg v-for="i in airplanesCount" :key="`airplane-${i}`" :ref="`airplane-${i - 1}`" class="airplane" width="50" height="50">
                <image :xlink:href="animation.sprites.airplane" width="50" height="50"/>
              </svg>
            </div>
            <canvas ref="bgCanvas" width="1280" :height="height" />
            <svg v-if="playing" key="rocket" ref="rocket" class="rocket" width="70" height="70"><image :xlink:href="animation.sprites.rocket" width="70" height="70" /></svg>
            <svg v-if="!animation.explode && !playing" key="rocket-stay" ref="rocketStay" class="rocket-stay" width="70" height="70"><image :xlink:href="animation.sprites.rocketStay" width="70" height="70" /></svg>
            <svg v-if="animation.explode" ref="explosion" key="explosion" class="explosion" width="90" height="90"><image :xlink:href="animation.sprites.explosion" width="90" height="90" /></svg>
          </div>
        </div>
        <div class="players-container flex-grow-1">
          <v-card class="fill-height">
            <v-card-text>
              <div class="d-flex flex-row justify-space-between align-center text--secondary font-weight-bold text-uppercase">
                <div>{{ $t('Player') }}</div>
                <div>{{ $t('Bet') }} / {{ $t('Win') }}</div>
              </div>
              <div class="mt-3 pr-3 list" :class="`${$vuetify.theme.dark ? 'dark' : 'light'}-scrollbar`">
                <transition-group name="staggered-fade" tag="ul" class="pa-0" :css="false" @before-enter="beforeEnter" @enter="enter" @leave="leave">
                  <li v-for="(item, index) in playersGame" :key="`player-${item.id}`" :data-index="index" class="d-flex mb-2">
                    <div class="d-flex align-center justify-space-between flex-grow-1">
                      <div class="d-flex flex-row align-center text-no-wrap overflow-hidden">
                        <user-avatar :user="item" :size="24" class="mr-2" />
                        <user-profile-modal :user="item" />
                      </div>
                      <div class="d-flex justify-end">
                        <animated-number :number="item.bet" />
                        <div class="mx-1">/</div>
                        <div v-if="item.payout">
                          <animated-number :number="item.win" class="success--text" />
                        </div>
                        <div v-else-if="!betting && !playing" class="red--text">
                          0
                        </div>
                        <div v-else>
                          &mdash;
                        </div>
                      </div>
                    </div>
                  </li>
                </transition-group>
              </div>
            </v-card-text>
          </v-card>
        </div>
      </div>
      <play-controls :play-label="$t('Bet')" :loading="loading" :playing="!betting" :provable="false" @play="makeBet">
        <template v-slot:after-play-button>
          <v-btn
            color="primary"
            :loading="loading"
            :disabled="!playing || userBet === 0 || userWin > 0"
            class="ml-2"
            @click="cashOut"
          >
            {{ $t('Cash out') }}
          </v-btn>
        </template>
      </play-controls>
    </div>
    <div v-else class="d-flex fill-height align-center justify-center">
      <v-alert dense outlined text color="primary" class="text-center">
        {{ $t('Please use landscape (horizontal) orientation.') }}
      </v-alert>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import { get } from '~/plugins/utils'
import MultiplayerGameEvent from '~/components/MultiplayerGameEvent'
import PlayControls from '~/components/Games/PlayControls'
import AnimatedNumber from '~/components/AnimatedNumber'
import BlockPreloader from '~/components/BlockPreloader'
import anime from 'animejs'
import UserAvatar from '~/components/UserAvatar'
import UserProfileModal from '~/components/UserProfileModal'
import CountdownTimer from '~/components/CountdownTimer'
import clickSound from '~/../audio/common/click.wav'
import winSound from 'packages/crash/resources/audio/win.wav'
import explodeSound from 'packages/crash/resources/audio/bang.wav'
import launchSound from 'packages/crash/resources/audio/rocket.mp4'
import planetSprite from 'packages/crash/resources/animations/planet.svg'
import rocketSprite from 'packages/crash/resources/animations/rocket.svg'
import rocketStaySprite from 'packages/crash/resources/animations/rocket-stay.svg'
import airplaneSprite from 'packages/crash/resources/animations/airplane.svg'
import cloudsSprite from 'packages/crash/resources/animations/clouds.svg'
import explosionSprite from 'packages/crash/resources/animations/explosion.svg'

export default {
  name: 'Crash',

  components: { CountdownTimer, MultiplayerGameEvent, AnimatedNumber, BlockPreloader, UserProfileModal, UserAvatar, PlayControls },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      ready: false,
      loading: false,
      gameable: {},
      multiplayerGame: {},
      players: {}, // all players including current
      playersOld: undefined,
      betting: false,
      playing: false,
      message: null,
      currentPayout: 0,
      crashedAt: [],
      timeDiff: 0,
      currentPayoutInterval: null,
      active: true,
      height: 720,
      animation: {
        airplanesVMpSpeed: 0, // vertical airplane speed
        cloudsVMpSpeed: 0, // vertical clouds speed
        airplanesMaxSpeed: 0.15, // max airplane speed
        cloudsMaxSpeed: 0.03, // max cloud speed
        airplanesMinDelay: 0, // min delay in ms until a new airplane appears
        airplanesMaxDelay: 2000, // max delay in ms until a new airplane appears
        cloudsMinDelay: 0, // min delay in ms until a new cloud appears
        cloudsMaxDelay: 2000, // max delay in ms until a new cloud appears
        airplaneLastDelay: 0,
        cloudLastDelay: 0,
        sprites: {
          planet: planetSprite,
          rocket: rocketSprite,
          rocketStay: rocketStaySprite,
          airplane: airplaneSprite,
          clouds: cloudsSprite,
          explosion: explosionSprite
        },
        airplanes: [],
        clouds: [],
        pos: 0,
        explode: 0,
        exploded: 0,
        opacity: 1
      }
    }
  },

  computed: {
    ...mapState('auth', ['account', 'user']),
    ...mapState('online', ['users']),
    animationTimeLimit () {
      return this.config.animation.time_limit
    },
    airplanesCount () {
      return this.config.animation.airplanes_count
    },
    cloudsCount () {
      return this.config.animation.clouds_count
    },
    baseNumber () {
      if (this.active) return parseFloat(this.config.base_number)
      else return 1
    },
    userBet () {
      return get(this.players, `${this.user.id}.bet`, 0)
    },
    userWin () {
      return get(this.players, `${this.user.id}.win`, 0)
    },
    landscape () {
      return this.$vuetify.breakpoint.width > this.$vuetify.breakpoint.height
    },
    playersGame () {
      return Object.values(this.playersOld !== undefined ? this.playersOld : this.players).sort(
        (a, b) =>
          a.payout > 0 && b.payout > 0
            ? (a.payout > b.payout ? -1 : 1)
            : (a.name < b.name ? -1 : 1)
      )
    }
  },

  watch: {
    playing (isPlaying, wasPlaying) {
      if (!wasPlaying && isPlaying && this.active) {
        this.currentPayoutInterval = setInterval(() => {
          this.currentPayout = this.calculatePayout(get(this.multiplayerGame, 'gameable.start_time_unix'), this.getCurrentTime())
        }, 50)
      } else if ((wasPlaying && !isPlaying) || !this.active) {
        clearInterval(this.currentPayoutInterval)
        this.currentPayoutInterval = null
        this.currentPayout = 0
      }
    }
  },

  beforeDestroy () {
    this.soundStop(launchSound)
    this.active = false
    if (this.tb) clearTimeout(this.tb)
    if (this.tg) clearTimeout(this.tg)
    window.removeEventListener('resize', () => this.onResize)
  },

  created () {
    this.init()
    window.addEventListener('resize', () => this.onResize)
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance'
    }),
    async init () {
      const loadImage = (src) => new Promise((resolve) => {
        const i = new Image()
        i.onload = () => {
          resolve(i)
        }
        i.src = src
      })

      await loadImage(planetSprite)
      await loadImage(rocketSprite)
      await loadImage(rocketStaySprite)
      await loadImage(airplaneSprite)
      await loadImage(cloudsSprite)
      await loadImage(explosionSprite)
      this.ready = true
      this.$nextTick(this.onAnimation)
    },
    async makeBet (bet) {
      this.loading = true
      this.sound(clickSound)
      this.updateUserAccountBalance(this.account.balance - bet)
      try {
        const { data: game } = await axios.post(this.getRoute('bet').replace('{multiplayerGame}', `${this.multiplayerGame.id}`), { bet })
      } catch (e) {}
      this.loading = false
    },
    async cashOut () {
      this.loading = true
      this.sound(clickSound)
      try {
        const { data: game } = await axios.post(this.getRoute('cash-out').replace('{multiplayerGame}', `${this.multiplayerGame.id}`))
        this.sound(winSound)
      } catch (e) {}
      this.loading = false
    },
    initStatiSprites () {
      this.animation.airplaneLastDelay = 0
      this.animation.cloudLastDelay = 0
      for (let i = 0; i < this.airplanesCount; i++) {
        if (this.animation.airplaneLastDelay === 0) {
          this.animation.airplaneLastDelay = this.getCurrentTime() + Math.round(this.animation.airplanesMaxDelay * Math.random())
        } else {
          this.animation.airplaneLastDelay += Math.max(this.animation.airplanesMinDelay, Math.round(this.animation.airplanesMaxDelay * Math.random()))
        }
        this.animation.airplanes.push({
          ref: `airplane-${i}`,
          shown: false,
          showAfter: this.animation.airplaneLastDelay,
          d: 0,
          x: 0,
          y: 0,
          leftSide: false
        })
      }
      for (let i = 0; i < this.cloudsCount; i++) {
        if (this.animation.cloudLastDelay === 0) {
          this.animation.cloudLastDelay = this.getCurrentTime() + Math.round(this.animation.cloudsMaxDelay * Math.random())
        } else {
          this.animation.cloudLastDelay += Math.max(this.animation.cloudsMinDelay, Math.round(this.animation.cloudsMaxDelay * Math.random()))
        }
        this.animation.clouds.push({
          ref: `cloud-${i}`,
          shown: false,
          showAfter: this.animation.cloudLastDelay,
          d: 0,
          x: 0,
          y: 0,
          leftSide: false
        })
      }
    },
    beforeEnter (el) {
      el.style.opacity = 0
      el.style.height = 0
    },
    enter (el, done) {
      const delay = el.dataset.index * 150
      setTimeout(function () {
        anime({
          targets: el,
          easing: 'easeOutExpo',
          opacity: 1,
          height: '100%'
        }).finished.then(done)
      }, delay)
    },
    leave (el, done) {
      const delay = el.dataset.index * 150
      setTimeout(function () {
        anime({
          targets: el,
          easing: 'easeOutExpo',
          opacity: 0,
          height: 0
        }).finished.then(done)
      }, delay)
    },
    beforeEnterC (el) {
      el.style.opacity = 0
      el.style.width = 0
    },
    enterC (el, done) {
      el.style.opacity = ''
      setTimeout(function () {
        anime({
          targets: el,
          easing: 'easeOutExpo',
          width: (el.children[0].getBoundingClientRect().width + 8) + 'px'
        }).finished.then(done)
      }, 300)
    },
    leaveC (el, done) {
      el.style.opacity = 0
      setTimeout(function () {
        anime({
          targets: el,
          easing: 'easeOutExpo',
          width: 0
        }).finished.then(done)
      }, 300)
    },
    futurePlanBetting (t) {
      if (this.tb) clearTimeout(this.tb)
      this.tb = setTimeout(this.onBettingStart, t)
    },
    futurePlanGame (t) {
      if (this.tg) clearTimeout(this.tg)
      this.tg = setTimeout(this.onGameStart, t)
    },
    onGameInit (event) {
      // console.log('onGameInit', event)

      this.timeDiff = event.time - Date.now()
      this.multiplayerGame = event.multiplayer_game
      this.players = { ...event.players }

      const timeUntilBettungStart = Math.max(0, this.multiplayerGame.start_time_unix - this.getCurrentTime())
      this.futurePlanBetting(timeUntilBettungStart)

      const timeUntilGameStart = Math.max(0, get(this.multiplayerGame, 'gameable.start_time_unix') - this.getCurrentTime())
      this.futurePlanGame(timeUntilGameStart)
    },
    onBettingStart () {
      this.betting = true
      this.playing = false
      this.playersOld = undefined
    },
    onGameStart () {
      this.sound(launchSound)
      this.initStatiSprites()
      this.betting = false
      this.playing = true
      this.animation.exploded = 0
      this.animation.explode = 0
      this.animation.opacity = 1
    },
    onEvent (event) {
      if (!this.active) return
      // console.log('onEvent', event)

      this.timeDiff = event.time - Date.now()
      this.players = { ...this.players, ...event.players }

      if (event.action === 'complete') {
        // console.log('payout', this.currentPayout)
        this.playing = false
        this.playersOld = this.players
        this.players = {}
        this.crashedAt.unshift({
          id: this.crashedAt.reduce((acc, item) => Math.max(acc, item.id), 0) + 1,
          payout: event.multiplayer_game.gameable.max_payout
        })
        while (this.crashedAt.length > 5) {
          this.crashedAt.pop()
        }
        this.multiplayerGame = event.multiplayer_game.next
        const timeUntilBettungStart = Math.max(0, this.multiplayerGame.start_time_unix - this.getCurrentTime())
        this.futurePlanBetting(timeUntilBettungStart)

        this.explode()

        const timeUntilGameStart = Math.max(0, get(this.multiplayerGame, 'gameable.start_time_unix') - this.getCurrentTime())
        this.futurePlanGame(timeUntilGameStart)
      } else if (event.action === 'cash-out' && event.user.id === this.user.id) {
        this.multiplayerGame = event.multiplayer_game
        this.updateUserAccountBalance(this.user.account.balance + get(this.players, `${this.user.id}.win`, 0))
      }
    },
    calculatePayout (startTime, endTime) {
      const duration = (endTime - startTime) / 1000
      return Math.round(Math.pow(this.baseNumber, duration) * 100) / 100
    },
    calculatePayoutPrecisely (startTime, endTime) {
      const duration = (endTime - startTime) / 1000
      return Math.pow(this.baseNumber, duration)
    },
    getCurrentTime () {
      return Date.now() + this.timeDiff
    },
    explode () {
      if (!this.animation.explode && !this.animation.exploded) {
        this.soundStop(launchSound)
        this.sound(explodeSound)
        this.animation.explode = 1
        this.animation.exploded = 1
        this.animation.opacity = 1
        for (const airplane of this.animation.airplanes) {
          const dom = this.$refs[airplane.ref] && this.$refs[airplane.ref][0] ? this.$refs[airplane.ref][0] : null
          if (dom) {
            dom.style.opacity = 0
          }
        }
        while (this.animation.airplanes.length) this.animation.airplanes.pop()
        for (const cloud of this.animation.clouds) {
          const dom = this.$refs[cloud.ref] && this.$refs[cloud.ref][0] ? this.$refs[cloud.ref][0] : null
          if (dom) {
            dom.style.opacity = 0
          }
        }
        while (this.animation.clouds.length) this.animation.clouds.pop()
      }
    },
    onAnimation () {
      if (!this.active) return
      if (this.$refs.bgCanvas) {
        const context = this.$refs.bgCanvas.getContext('2d')
        const w = this.$refs.bgCanvas.width
        const h = this.$refs.bgCanvas.height

        if (this.playing) {
          const payout = this.calculatePayoutPrecisely(get(this.multiplayerGame, 'gameable.start_time_unix'), this.getCurrentTime())
          this.animation.pos = Math.min((payout - 0.8) / (this.animationTimeLimit - 0.8), 1) * 3.75
        }

        const tx = this.animation.pos
        context.clearRect(0, 0, w, h)
        if (this.playing || this.animation.opacity > 0) {
          const wx = w / 3.75
          const hx = (h - 10) / 41.521082
          let ix = 0
          const xs = 3.75 / w
          while (ix <= tx) {
            let a = 1
            if (tx > 0.5) {
              if (tx - ix < 0.5) {
                a = Math.min(1, 0.1 + (ix - (tx - 0.5)) / 0.5)
              } else {
                a = 0.1
              }
            } else {
              a = Math.min(1, ix / tx + 0.1)
            }
            a *= this.animation.opacity
            context.globalAlpha = a / 6
            context.fillStyle = '#aaaaaa'
            context.beginPath()
            const y = Math.exp(ix) - 1
            context.arc(ix * wx, h - 5 - y * hx, 16 * a + 2, 0, 2 * Math.PI)
            context.fill()
            ix += xs
          }
          const x = tx * wx * (this.$refs.bgCanvas.offsetWidth / w)
          const y = (h - 5 - (Math.exp(tx) - 1) * hx) * (this.$refs.bgCanvas.offsetHeight / h)
          if (this.$refs.rocket) {
            const x0 = (tx - xs * 4) * wx * (this.$refs.bgCanvas.offsetWidth / w)
            const y0 = (h - 5 - (Math.exp(tx - xs * 4) - 1) * hx) * (this.$refs.bgCanvas.offsetHeight / h)
            const c = (Math.atan2(y - y0, x - x0) + Math.PI / 4).toFixed(6)
            this.$refs.rocket.style.transform = `translate(-50%, -50%) rotate(${c}rad)`
            this.$refs.rocket.style.left = x.toFixed(4) + 'px'
            this.$refs.rocket.style.top = y.toFixed(4) + 'px'
          }
          if (this.$refs.explosion) {
            this.$refs.explosion.style.left = x.toFixed(2) + 'px'
            this.$refs.explosion.style.top = y.toFixed(2) + 'px'
            this.$refs.explosion.style.opacity = this.animation.opacity.toFixed(2)
          }
          if (!this.playing) {
            this.animation.opacity -= 0.02
            if (this.animation.opacity <= 0) {
              this.animation.opacity = 0
              this.animation.explode = 0
            }
          }
        }
        if (this.$refs.rocketStay) {
          const xs = 3.75 / w
          const wx = w / 3.75
          const hx = (h - 10) / 41.521082
          const tx = 0.2 / (this.animationTimeLimit - 0.8) * 3.75
          const x = tx * wx * (this.$refs.bgCanvas.offsetWidth / w)
          const y = (h - 5 - (Math.exp(tx) - 1) * hx) * (this.$refs.bgCanvas.offsetHeight / h)
          const x0 = (tx - xs * 4) * wx * (this.$refs.bgCanvas.offsetWidth / w)
          const y0 = (h - 5 - (Math.exp(tx - xs * 4) - 1) * hx) * (this.$refs.bgCanvas.offsetHeight / h)
          const c = (Math.atan2(y - y0, x - x0) + Math.PI / 4).toFixed(6)
          this.$refs.rocketStay.style.transform = `translate(-50%, -50%) rotate(${c}rad)`
          this.$refs.rocketStay.style.left = x.toFixed(4) + 'px'
          this.$refs.rocketStay.style.top = y.toFixed(4) + 'px'
        }
        // airplanes animation here
        for (const airplane of this.animation.airplanes) {
          const dom = this.$refs[airplane.ref] && this.$refs[airplane.ref][0] ? this.$refs[airplane.ref][0] : null
          if (dom) {
            if (!airplane.shown && this.getCurrentTime() > airplane.showAfter) {
              dom.style.opacity = 1
              airplane.shown = true
              airplane.leftSide = Math.random() > 0.5
              airplane.tm = this.getCurrentTime()
              airplane.speed = (Math.random() * 0.5 + 0.5) * this.animation.airplanesMaxSpeed
              airplane.y = Math.random() * 0.5 + 0.25
              airplane.d = 0
              if (airplane.leftSide) {
                airplane.x = -0.1
                dom.style.transform = 'translate(-50%,-50%) scale(1,1)'
              } else {
                airplane.x = 1.1
                dom.style.transform = 'translate(-50%,-50%) scale(-1,1)'
              }
            }
            if (airplane.shown || airplane.d <= 0.3) {
              const tm = this.getCurrentTime()
              const td = (tm - airplane.tm) * 0.001
              airplane.tm = tm
              airplane.d += td
              if (airplane.leftSide) {
                airplane.x += airplane.speed * td
              } else {
                airplane.x -= airplane.speed * td
              }
              if (this.playing) {
                airplane.y += td * this.currentPayout * this.animation.airplanesVMpSpeed
              }
              dom.style.top = `${airplane.y * this.$refs.bgCanvas.offsetHeight}px`
              dom.style.left = `${airplane.x * this.$refs.bgCanvas.offsetWidth}px`
              if (airplane.shown && (airplane.x > 1.1 || airplane.x < -0.1 || airplane.y > 1.1)) {
                airplane.shown = false
                airplane.d = 0
                this.animation.airplaneLastDelay = Math.max(this.getCurrentTime(), this.animation.airplaneLastDelay) + Math.max(this.animation.airplanesMinDelay, Math.round(this.animation.airplanesMaxDelay * Math.random()))
                airplane.showAfter = this.animation.airplaneLastDelay
                dom.style.opacity = 0
              }
            }
          }
        }
        // clouds animation here
        for (const cloud of this.animation.clouds) {
          const dom = this.$refs[cloud.ref] && this.$refs[cloud.ref][0] ? this.$refs[cloud.ref][0] : null
          if (dom) {
            if (!cloud.shown && this.getCurrentTime() > cloud.showAfter) {
              dom.style.opacity = 1
              cloud.shown = true
              cloud.leftSide = Math.random() > 0.5
              cloud.tm = this.getCurrentTime()
              cloud.speed = (Math.random() * 0.5 + 0.5) * this.animation.cloudsMaxSpeed
              cloud.y = Math.random() * 0.5 + 0.25
              cloud.d = 0
              if (cloud.leftSide) {
                cloud.x = -0.1
              } else {
                cloud.x = 1.1
              }
              if (Math.random() > 0.5) {
                dom.style.transform = 'translate(-50%,-50%) scale(1,1)'
              } else {
                dom.style.transform = 'translate(-50%,-50%) scale(-1,1)'
              }
            }
            if (cloud.shown || cloud.d <= 0.3) {
              const tm = this.getCurrentTime()
              const td = (tm - cloud.tm) * 0.001
              cloud.tm = tm
              cloud.d += td
              if (cloud.leftSide) {
                cloud.x += cloud.speed * td
              } else {
                cloud.x -= cloud.speed * td
              }
              if (this.playing) {
                cloud.y += td * this.currentPayout * this.animation.cloudsVMpSpeed
              }
              dom.style.top = `${cloud.y * this.$refs.bgCanvas.offsetHeight}px`
              dom.style.left = `${cloud.x * this.$refs.bgCanvas.offsetWidth}px`
              if (cloud.shown && (cloud.x > 1.1 || cloud.x < -0.1 || cloud.y > 1.1)) {
                cloud.shown = false
                cloud.d = 0
                this.animation.cloudLastDelay = Math.max(this.getCurrentTime(), this.animation.cloudLastDelay) + Math.max(this.animation.cloudsMinDelay, Math.round(this.animation.cloudsMaxDelay * Math.random()))
                cloud.showAfter = this.animation.cloudLastDelay
                dom.style.opacity = 0
              }
            }
          }
        }
      }
      requestAnimationFrame(this.onAnimation)
    },
    onResize () {
      this.height = window.innerWidth < 1200 ? 500 : 720
    }
  }
}
</script>

<style lang="scss" scoped>
  .game-container {
      position: relative;
      max-width: 1200px;
      margin: 0 auto;
  }

  .game-view {
      position: relative;
      max-width: 900px;

      @media (max-width: 1200px) {
          max-width: calc(100vw - 300px - 32px - 8px);
          padding-bottom: 10px;
      }

      .last-crashed {
          position: absolute;
          font-size: 0.9em;
          color: var(--v-accent-base);
          li {
              opacity: 1;
              transition: opacity .3s;
              position: relative;

              div {
                  margin: 0 2px;
              }

              &:after {
                  content: ' ';
                  position: absolute;
                  top: 0;
                  left: 1px;
                  right: 1px;
                  bottom: 0;
                  background: var(--v-accent-base);
                  opacity: 0.1;
              }
          }

          li:nth-child(2) {
              opacity: 0.8;
          }

          li:nth-child(3) {
              opacity: 0.7;
          }

          li:nth-child(4) {
              opacity: 0.6;
          }

          li:nth-child(5) {
              opacity: 0.5;
          }
      }

      .game-status {
          position: absolute;
          left: 50%;
          top: 50%;
          z-index: 2;
          transform: translate(-50%, -50%);
      }

      .animation {
        width: 100%;
        position: relative;

        .subanimation {
          position: absolute;
          top: 0;
          bottom: 0;
          left: 0;
          right: 0;
          overflow: hidden;
        }

        canvas {
              width: 100%;
              height: auto;
          }

          .planet {
              position: absolute;
              top: 0;
              right: 0;
              @media (max-width: 1200px) {
                  width: 40px;
                  height: 40px;
                  image {
                      width: 40px;
                      height: 40px;
                  }
              }
          }

          .rocket {
              position: absolute;
              @media (max-width: 1200px) {
                  width: 40px;
                  height: 40px;
                  image {
                      width: 40px;
                      height: 40px;
                  }
              }
          }

          .rocket-stay {
              position: absolute;
              transform: translate(-50%, -50%) rotate(45deg);
              @media (max-width: 1200px) {
                  width: 40px;
                  height: 40px;
                  image {
                      width: 40px;
                      height: 40px;
                  }
              }
          }

          @keyframes explosion-animation {
              0% {
                  transform: translate(-50%, -50%) scale(1);
              }

              50% {
                  transform: translate(-50%, -50%) scale(1.5);
              }

              100% {
                  transform: translate(-50%, -50%) scale(0.5);
              }
          }

          .explosion {
              position: absolute;
              transform: translate(-50%, -50%) scale(0.5);
              transform-origin: center;
              animation-name: explosion-animation;
              animation-duration: 1s;
              animation-iteration-count: 1;
              @media (max-width: 1200px) {
                  width: 40px;
                  height: 40px;
                  image {
                      width: 40px;
                      height: 40px;
                  }
              }
          }

          .airplane,
          .cloud {
              opacity: 0;
              position: absolute;
              transition: opacity 0.3s;
          }
          @media (max-width: 1200px) {
            .cloud {
              width: 40px;
              height: 40px;
              image {
                  width: 40px;
                  height: 40px;
              }
            }
            .airplane {
              width: 20px;
              height: 20px;
              image {
                  width: 20px;
                  height: 20px;
              }
            }
          }
      }
  }
  .players-container {
      .list {
          overflow: hidden;
          overflow-y: overlay;
          max-height: calc(100vh - 50px);

          &::-webkit-scrollbar {
            width: 5px;
          }
      }
  }
</style>

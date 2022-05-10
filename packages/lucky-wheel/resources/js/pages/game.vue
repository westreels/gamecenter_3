<template>
  <div class="d-flex flex-column fill-height py-3 pt-md-3 pt-12">
    <div class="message-container d-flex justify-center align-center">
      <game-message :message="message" />
    </div>

    <div v-if="ready" class="d-flex justify-center fill-height align-center">
      <div class="wheel">
        <object>
          <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="2600" height="2600" viewBox="0 0 2600 2600">
            <defs>
              <filter id="spin_blur" x="0" y="0">
                <feGaussianBlur in="SourceGraphic" :stdDeviation="animation.spinBlur" />
              </filter>
              <filter id="l_blur" x="0" y="0">
                <feGaussianBlur in="SourceGraphic" :stdDeviation="Math.round(animation.spinBlur/5)" />
              </filter>
              <clipPath id="wheel_clip">
                <circle r="1035" cx="0" cy="0" />
              </clipPath>
              <filter id="ledBrightness">
                <feComponentTransfer>
                  <feFuncR type="linear" slope="4" />
                  <feFuncG type="linear" slope="4" />
                  <feFuncB type="linear" slope="4" />
                </feComponentTransfer>
              </filter>
              <filter id="winBrightness">
                <feComponentTransfer>
                  <feFuncR type="linear" slope="2" />
                  <feFuncG type="linear" slope="2" />
                  <feFuncB type="linear" slope="2" />
                </feComponentTransfer>
              </filter>
            </defs>
            <g :transform="'translate(1300,1300) rotate('+animation.spinAngle+')'" clip-path="url(#wheel_clip)">
              <g v-for="(section,idx) in config.sections" :ref="idx" class="section" :transform="'rotate('+(idx*360/config.sections.length)+')'">
                <path
                  class="section"
                  :class="animation.index!=null&&(animation.stage==0||animation.stage>=4)&&animation.index==idx?'win':''"
                  :d="'M0 00 -'+sectorX.toFixed(2)+' -'+sectorY.toFixed(2)+' A1000 1000 0 0 0-'+sectorX.toFixed(2)+' '+sectorY.toFixed(2)+'Z'" :fill="section.backgroundColor"
                />
                <text class="label" :x="getLabelX(section)" :y="getLabelY(section)" :font-size="getLabelSize(section)" :fill="section.fontColor ? section.fontColor : '#000000'">
                  <template v-if="typeof(section.title)=='string' && section.title.split('\n').length>1">
                    <tspan v-for="line in section.title.split('\n')" :x="getLabelX(section)" :dy="getLabelSpanDY(section)">{{ line }}</tspan>
                  </template>
                  <template v-else>{{ section.title }}</template>
                </text>
              </g>
              <g v-for="(section,idx) in config.sections" class="section" :transform="'rotate('+(idx*360/config.sections.length)+')'">
                <path class="border" :d="'M'+sectorBX.toFixed(2)+' -'+sectorBY.toFixed(2)+' -'+sectorBX.toFixed(2)+' '+sectorBY.toFixed(2)+' -'+sectorX.toFixed(2)+' -'+sectorY.toFixed(2)+'Z'" />
              </g>
              <circle cx="0" cy="0" r="200" class="circle-inner" stroke-width="35" />
              <circle cx="0" cy="0" r="50" class="circle-inner-2" stroke-width="0" />
              <radialGradient id="wheelDarken" cx="0" cy="0" r="1000" gradientUnits="userSpaceOnUse">
                <stop offset="0" style="stop-color: transparent;" />
                <stop offset="0.85" style="stop-color: transparent;" />
                <stop offset="1" style="stop-color: rgba(0, 0, 0,0.25);" />
              </radialGradient>
              <circle cx="0" cy="0" r="1000" stroke-width="70" fill="url(#wheelDarken)" class="circle-outer" />

              <circle v-for="idx in config.sections.length"
                      :cx="Math.cos( 360/config.sections.length*Math.PI/180/2 + Math.PI + 360/config.sections.length*Math.PI/180*idx)*1000"
                      :cy="Math.sin( 360/config.sections.length*Math.PI/180/2 + Math.PI + 360/config.sections.length*Math.PI/180*idx)*1000"
                      r="20" stroke-width="0" class="segment-pin"
              />

            </g>
            <g transform="translate(1300,1300)">

              <circle cx="0" cy="0" r="1200" stroke-width="140" fill="transparent" class="circle-outer" />

              <radialGradient v-for="(section, idx) in config.sections" :id="'LOUTER'+idx" :key="idx " cx="0" cy="0" r="100" gradientUnits="userSpaceOnUse">
                <stop offset="0" :style="'stop-color: '+section.backgroundColor+';'" />
                <stop offset="0.025" :style="'stop-color: '+section.backgroundColor+'aa;'" />
                <stop offset="1" :style="'stop-color: '+section.backgroundColor+'00;'" />
              </radialGradient>

              <g v-for="idx in config.sections.length * 2 - 1" :transform="'rotate('+(360/(config.sections.length*2)*idx ).toFixed(2)+') translate(-1200,0)'">
                <circle cx="0" cy="0" r="40" stroke-width="0" class="led-outer" filter="url(#ledBrightness)" :fill="getLedOuterFill(idx)" />
                <circle cx="0" cy="0" r="100" stroke-width="0" class="led-outer-light" filter="url(#ledBrightness)" :fill="'url(#LOUTER'+((idx+animation.ledPanelOffset-1) % config.sections.length+1)+')'" />
              </g>
              <g v-for="idx in config.sections.length*2" :transform="'rotate('+(360/(config.sections.length*2)*idx - 360/(config.sections.length*2)/2).toFixed(2)+') translate(-1200,0)'">
                <path stroke-width="0" class="outer-pin" d="M 20 0 6 6 0 20 -6 6 -20 0 -6 -6 0 -20 6 -6Z" />
              </g>

              <g :transform="'translate(-1150,0) rotate(-'+animation.lAngle+')'">
                <path filter="url(#l_blur)" class="stopper-body" fill="white" stroke-width="0" stroke="black" d="
                              M178 -5
                              28 -70
                              A75 75 0 1 0 28 70
                              L 178 5
                              A18 10 0 0 0 178 -5
                              Z"
                />
                <circle class="stopper-pin" cx="0" cy="0" r="60" stroke-width="0" fill="#ff0000" />
                <circle class="stopper-pin-inner" cx="0" cy="0" r="20" stroke-width="0" fill="#00ff00" />
              </g>

            </g>
          </svg>
        </object>
      </div>
    </div>
    <div v-else class="d-flex justify-center fill-height align-center">
      <block-preloader />
    </div>
    <play-controls :loading="loading" :playing="playing" @play="play" />
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
  import clackSound from 'packages/lucky-wheel/resources/audio/clack.wav'
  import loseSound from 'packages/lucky-wheel/resources/audio/lose.wav'
  import startSound from 'packages/lucky-wheel/resources/audio/start.wav'
  import winSound from 'packages/lucky-wheel/resources/audio/win.wav'
  import PlayControls from '~/components/Games/PlayControls'
  import GameMessage from '~/components/Games/GameMessage'
  import BlockPreloader from '~/components/BlockPreloader'

  export default {
    name: 'LuckyWheel',

    components: { BlockPreloader, GameMessage, PlayControls },

    mixins: [FormMixin, GameMixin, SoundMixin],

    data () {
      return {
        loading: false,
        playing: false,
        ready: false,
        message: null,
        animation: {
            planned: false,
            t: 0,
            aClack: 0,
            spinBlur: 0,
            spinAngle: 0,
            breakM: 0,
            breakAngle: 0,
            breakAngleFrom: 0,
            ledAngle: 0,
            lPosAngle: 0,
            lAngle: 0,
            speed: 0,
            speedLedMin: 50,
            speedLed: 50,
            speedReverseMax: 15,
            speedMax: 600,
            speedMin: 0.1,
            speedNoise: 400,
            breakSpins: 4,
            ledPanelOffset: 0,
            stage: 0, // 0 - stopped, 1 - acceleration, 2 - spin, 3 - deceleration, 4 - reverse
            index: null,
            requestAnimationFrame: null
        },
        interval: null
      }
    },

    computed: {
      ...mapState('auth', ['account']),
      variations () {
        return this.gamePackageId ? config(`${this.gamePackageId}`).variations : null
      },
      variation () {
        return this.variations && this.variations.length ? this.variations.findIndex(o => o.slug === this.$route.params.slug) : null
      },
      config () {
        return this.variation !== null ? this.variations[this.variation] : {}
      },
      sectorX () {
          return Math.cos(360 / this.config.sections.length * Math.PI / 180 / 2) * 1000
      },
      sectorY () {
          return Math.sin(360 / this.config.sections.length * Math.PI / 180 / 2) * 1000
      },
      sectorBX () {
          return Math.abs(Math.cos(360 / this.config.sections.length * Math.PI / 180 / 2 + Math.PI / 2) * 20)
      },
      sectorBY () {
          return Math.abs(Math.sin(360 / this.config.sections.length * Math.PI / 180 / 2 + Math.PI / 2) * 20)
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
    created () {
      document.addEventListener('visibilitychange', this.visibilityCnage)
    },
    beforeDestroy () {
      this.playing = false
      clearInterval(this.interval)
      document.removeEventListener('visibilitychange', this.visibilityCnage)
    },
    mounted () {
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
        this.ready = false
        this.animation.lPosAngle = 360 / this.config.sections.length / 2
        this.requestAnimationFrameGet()
        this.animation.t = Date.now()
			  this.interval = setInterval(() => {
          if (!this.animation.planned) {
              this.animation.planned = true
              this.animation.requestAnimationFrame(this.animationCalculate)
          }
        }, 25)
        setTimeout(() => { this.ready = true }, 1000)
      },
      async play (bet) {
        this.loading = true
        this.playing = true
        this.message = null
        this.sound(startSound)
        this.startSpin()

        // update user balance
        this.updateUserAccountBalance(this.account.balance - bet)

        await sleep(500)

        // API request params
        const endpoint = this.getRoute('play')
        const requestParams = { hash: this.provablyFairGame.hash, bet, variation: this.variation }

        // execute the action
        const { data: game } = await axios.post(endpoint, requestParams)

        this.loading = false

        this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })

        this.respnoseData = {
            position: game.gameable.position,
            balance: game.account.balance,
            win: game.win,
            bet: game.bet
        }
        setTimeout(() => this.stopSpin(this.respnoseData.position), 1000)
      },
      getLabelSize (segment) {
          if (!isNaN(segment.font)) { return segment.font } else { return ((typeof (segment.title) === 'string' ? (segment.title.split('\n').reduce((v, el) => { if (el.length > v)v = el.length; return v }, 0)) : segment.title.toString().length) > 6 ? ((12 / (typeof (segment.title) === 'string' ? (segment.title.split('\n').reduce((v, el) => { if (el.length > v)v = el.length; return v }, 0)) : segment.title.toString().length) * 70)) : 160) }
      },
      getLabelX (segment) {
          if (typeof (segment.title) === 'string' && segment.title.split('\n').length > 1) { return -600 } else if ((typeof (segment.title) === 'string' ? (segment.title.split('\n').reduce((v, el) => { if (el.length > v)v = el.length; return v }, 0)) : segment.title.toString().length) > 6) { return -600 } else { return -650 }
      },
      getLabelY (segment) {
          if (typeof (segment.title) === 'string' && segment.title.split('\n').length > 1) { return -this.getLabelSize(segment) * 1.6 * segment.title.split('\n').length / 2 } else { return 0 }
      },
      getLabelSpanDY (segment) {
          return this.getLabelSize(segment) * 1.2
      },
      getLedOuterFill (idx) {
        // console.log(((idx+this.animation.ledPanelOffset-1) % this.config.sections.length), this.config.sections.length)
        return this.config.sections[((idx + this.animation.ledPanelOffset - 1) % this.config.sections.length)].backgroundColor
      },
      visibilityCnage () {
				if (document.hidden !== undefined) {
					if (document.hidden) {
            this.animation.t_hide = Date.now()
					} else {
						var r = Date.now() - this.animation.t_hide
						this.animation.t += r
					}
				}
			},
      requestAnimationFrameGet () {
          var _raf = window.requestAnimationFrame ||
                          window.mozRequestAnimationFrame ||
                          window.webkitRequestAnimationFrame ||
                          window.msRequestAnimationFrame ||
                          window.oRequestAnimationFrame

          this.animation.requestAnimationFrame = _raf ? _raf.bind(window) : null
      },
      onStopSpin () {
          if (!this.respnoseData) return
          this.updateUserAccountBalance(this.respnoseData.balance)
          if (this.respnoseData.win > 0) {
              this.message = this.$t('You won') + ' ' + this.respnoseData.win
              this.sound(winSound)
          } else {
              this.sound(loseSound)
          }

          this.playing = false
          this.respnoseData = null
      },
      animationCalculate () {
          this.animation.planned = false
          var tc = Date.now() - this.animation.t
          this.animation.t = Date.now()
          switch (this.animation.stage) {
              case 1:
                  this.animation.speed += this.animation.speedMax / 1000 * tc
                  this.animation.spinBlur = Math.round(this.animation.speed / 10) - 10
                  if (this.animation.spinBlur < 0) this.animation.spinBlur = 0
                  this.animation.speedLed = Math.round(this.animation.speed)
                  if (this.animation.speed >= this.animation.speedMax) {
                      this.animation.speed = this.animation.speedMax
                      this.animation.stage++
                  }
                  break
              case 2:
                  if (this.animation.index != null) {
                      this.animation.breakAngle =

                                  360 * this.animation.breakSpins - this.animation.spinAngle - this.animation.index * 360 / this.config.sections.length +
                                    360 / this.config.sections.length / 2 + 4

                      this.animation.breakAngleFrom = this.animation.breakAngle
                      this.animation.breakM = this.animation.speedMax / Math.sqrt(this.animation.breakAngleFrom)
                      this.animation.stage++
                  }
                  break
              case 3:
                  if (this.animation.breakAngle > 0) {
                      this.animation.speed = this.animation.breakM * Math.sqrt(this.animation.breakAngle / this.animation.breakAngleFrom * this.animation.speedMax)
                      // this.animation.breakAngle/this.animation.breakAngleFrom * this.animation.speedMax;
                      // this.animation.speed = this.animation.breakAngle/this.animation.breakAngleFrom * this.animation.speedMax;
                      if (this.animation.speed < this.animation.speedMin) this.animation.speed = this.animation.speedMin
                      this.animation.spinBlur = Math.round(this.animation.speed / 10) - 10
                      if (this.animation.spinBlur < 0) this.animation.spinBlur = 0
                      this.animation.speedLed = Math.round(this.animation.speed)
                      break
                  } else if (this.animation.breakAngle <= 0) {
                      this.animation.breakAngle = 0
                      // this.animation.speed=-this.animation.speedMin;
                      this.animation.spinBlur = 0
                      this.animation.speedLed = this.animation.speedLedMin
                      this.animation.breakM = this.animation.speedReverseMax / Math.sqrt(4 + 360 / this.config.sections.length / 2)
                      this.onStopSpin()
                      this.animation.stage++
                  } else { break }
              case 4:
                  this.animation.speed =
                      -this.animation.breakM * Math.sqrt(4 + 360 / this.config.sections.length - this.animation.breakAngle)
                  if (this.animation.speed > -this.animation.speedMin) this.animation.speed = -this.animation.speedMin
                  if (this.animation.breakAngle >= 4 + 360 / this.config.sections.length / 2) {
                      this.animation.speed = 0
                      this.animation.stage = 0
                  }
                  break
          }
          if (this.animation.stage === 3 || this.animation.stage === 4) this.animation.breakAngle -= this.animation.speed / 1000 * tc
          this.animation.spinAngle += this.animation.speed / 1000 * tc
          this.animation.lPosAngle += this.animation.speed / 1000 * tc
          this.animation.aClack += this.animation.speed / 1000 * tc
          this.animation.ledAngle += this.animation.speedLed / 1000 * tc
          while (this.animation.spinAngle > 360) this.animation.spinAngle -= 360
          while (this.animation.ledAngle > 360 * 2) this.animation.ledAngle -= 360 * 2
          while (this.animation.ledAngle < 0) this.animation.ledAngle += 360
          this.animation.ledPanelOffset = Math.round(this.animation.ledAngle / (360 / (this.config.sections.length * 2)))

          if (
              this.animation.lPosAngle >= 360 / this.config.sections.length - 3 &&
              this.animation.lPosAngle <= 360 / this.config.sections.length + 8
              ) {
              while (this.animation.lPosAngle > 360 / this.config.sections.length + 8) this.animation.lPosAngle -= 360 / this.config.sections.length
              this.animation.lAngle = (
                  (11 - (360 / this.config.sections.length + 8 - this.animation.lPosAngle)) / 11 * 45
              ).toFixed(1)
              if (this.animation.lAngle > 45) this.animation.lAngle = 45
              if (this.animation.lAngle < 0) this.animation.lAngle = 0
          } else if (this.animation.lAngle > 0) {
              if (this.animation.lPosAngle > 360 / this.config.sections.length) { this.animation.lAngle = 45 }
              while (this.animation.lPosAngle > 360 / this.config.sections.length) this.animation.lPosAngle -= 360 / this.config.sections.length
              this.animation.lAngle -= 360 / 1000 * tc
              if (this.animation.lAngle < 0) this.animation.lAngle = 0
          } else if (this.animation.lPosAngle > 360 / this.config.sections.length) {
              this.animation.lAngle = 45
          }
          if (this.animation.aClack > 360 / this.config.sections.length) {
              this.sound(clackSound)
              while (this.animation.aClack > 360 / this.config.sections.length) this.animation.aClack -= 360 / this.config.sections.length
          }
      },
      startSpin () {
        this.animation.index = null
        this.animation.stage = 1
      },
      stopSpin (index) {
        this.animation.index = index
      }
    }
  }
</script>

<style lang="scss" scoped>
  .wheel {
    max-width:70%;
    width:100%;
    @media (min-width:600px) {
      &{
        width:400px;
      }
    }
    svg {
      width:100%;height:auto;
    }
    .segment-pin{
      @media (min-width:1200px){
        filter: url(#spin_blur);
      }
    }
    g path.border {
        @media (min-width:1200px){
          filter: url(#spin_blur);
        }
        stroke-width:0;
        fill: var(--v-primary-base);
    }
    g path.segment {
        @media (min-width:1200px){
          filter: url(#spin_blur);
        }
        stroke-width:0;
        &.win {
          filter: url(#winBrightness);
        }
    }
    .label {
        // font-size:160px;
        @media (min-width:1200px){
          //filter: url(#spin_blur);
        }
        stroke-width:0;
        alignment-baseline: central;
        dominant-baseline: central;
        text-anchor: middle;
    }

    .circle-inner{
      stroke: var(--v-primary-base);
      fill: var(--v-primary-darken1);
    }
    .circle-inner-2{
      fill: var(--v-primary-lighten1);
    }
    .circle-outer{
      stroke: var(--v-primary-darken3);
    }
    .stroke-pin {
      stroke:black;
      fill:red;
    }
    .outer-pin {
      fill: var(--v-primary-darken1);
    }
    .segment-pin {
      fill: var(--v-primary-darken1);
    }
    .led-outer {
      transition:.3s;
    }
    .led-outer-light {
      transition:.3s;
    }
    .stopper-body {
      fill: var(--v-primary-lighten1);
    }
    .stopper-pin {
      fill: var(--v-primary-darken1);
    }
    .stopper-pin-inner {
      fill: var(--v-primary-darken2);
    }
  }
  .message-container {
    height:0;
    padding: 25px 0 20px 0;
  }
</style>

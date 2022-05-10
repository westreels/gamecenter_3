<template>
  <fullscreen v-model="fullscreen">
    <div v-if="ready" ref="container" class="card-game-container" :class="{ 'system-bar-enabled': systemBarEnabled, 'modal': modalPaytable || modalInfo || modalProvably }" :style="`height: ${containerHeight}px;`">
      <div ref="table" class="card-game-table" @click="e => $emit('click', e)">
        <img :src="`${imageBaseUrl}/table-${deck}.jpg`">
        <img class="logo" :src="logoUrl">
        <div class="paytable-min">
          {{ $t('Min') }}: {{ paytableMin }}
        </div>
        <div class="paytable-max">
          {{ $t('Max') }}: {{ paytableMax }}
        </div>
        <div class="paytable-link" @click="modalPaytable = !modalPaytable" />
        <div class="cards">
          <slot />
        </div>
        <div class="control-panel">
          <slot name="control-panel" />
        </div>
        <chips class="chips" :class="{ disabled: !betMode }" :bet-min="paytableMin" :bet-max="paytableMax" :bet="bet" @bet="event => placeBet(event.bet, event.pos)" />
        <slot name="bet-panel" :chips="chips" :unbet="unbet" :dealer="dealer" :animated="chipsAnimated" />
        <div class="button-mini game-info" @click="modalInfo = true">
          <img :src="`${imageBaseUrl}/info.png`">
        </div>
        <div class="button-mini provably" @click="modalProvably = true">
          <img :src="`${imageBaseUrl}/provably.png`">
        </div>
        <div class="button-mini full" @click="fullscreen = !fullscreen">
          <img :src="`${imageBaseUrl}/full.png`">
        </div>
        <slot name="bottom" />
      </div>
      <modal-info v-model="modalPaytable">
        <slot name="paytable" />
      </modal-info>
      <modal-info v-model="modalInfo">
        <slot name="info" />
      </modal-info>
      <modal-info v-model="modalProvably">
        <div class="provably-fair-form">
          <h5>
            {{ $t('Provably fair') }}
          </h5>
          <form @submit.prevent="createProvablyFairGame">
            <label>{{ $t('Client seed') }}</label>
            <input v-model="provablyFairGame.client_seed">
            <label>{{ $t('Server hash') }}</label>
            <div class="input-btn">
              <input
                ref="hash"
                v-model="provablyFairGame.hash"
                readonly
              >
              <div class="btn-icon" @click="copyToClipboard($refs.hash)">
                <object>
                  <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clone" class="svg-inline--fa fa-clone fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M464 0H144c-26.51 0-48 21.49-48 48v48H48c-26.51 0-48 21.49-48 48v320c0 26.51 21.49 48 48 48h320c26.51 0 48-21.49 48-48v-48h48c26.51 0 48-21.49 48-48V48c0-26.51-21.49-48-48-48zM362 464H54a6 6 0 0 1-6-6V150a6 6 0 0 1 6-6h42v224c0 26.51 21.49 48 48 48h224v42a6 6 0 0 1-6 6zm96-96H150a6 6 0 0 1-6-6V54a6 6 0 0 1 6-6h308a6 6 0 0 1 6 6v308a6 6 0 0 1-6 6z"></path></svg>
                </object>
              </div>
            </div>
            <button
              type="submit"
              :disabled="!provablyFairGame || !provablyFairGame.client_seed || !(provablyFairGame.client_seed.length > 0 && provablyFairGame.client_seed.length < 32)"
            >
              {{ $t('Generate') }}
            </button>
          </form>
        </div>
      </modal-info>
      <div class="portrait-warn">
        {{ $t('Please rotate your device to landscape orientation') }}
      </div>
    </div>
    <preloader v-else :progress="loadingProgress" />
  </fullscreen>
</template>

<script>
import VueFullscreen from 'vue-fullscreen'
import Vue from 'vue'
import { sleep, copyToClipboard, preloadImage } from '~/plugins/utils'
import GameMixin from '~/mixins/Game'
import Chips from './Chips'
import ChipValues from './ChipValues'
import ModalInfo from './ModalInfo'
import { config } from '~/plugins/config'
import Preloader from '~/components/Games/CardGame/Preloader'

Vue.use(VueFullscreen)

export default {
  components: { Chips, ModalInfo, Preloader },
  mixins: [GameMixin],
  props: {
    actions: {
      type: Array,
      required: true
    },
    betMode: {
      type: String,
      default: null
    },
    chipsBet: {
      type: Array,
      default: () => ['ante', 'call']
    }
  },
  data () {
    return {
      imageBaseUrl: '/images/games/card-game-ui',
      ready: false,
      loadingProgress: 0,
      chips: {},
      resolvers: {},
      dealer: false,
      modalPaytable: false,
      modalInfo: false,
      modalProvably: false,
      fullscreen: false,
      containerHeight: 50
    }
  },
  computed: {
    bet () {
      return Object.keys(this.chips).reduce((ak, k) => ak + this.chips[k].reduce((acc, item) => acc + item.bet, 0), 0)
    },
    deck () {
      return config('settings.games.playing_cards.deck')
    },
    systemBarEnabled () {
      return config('settings.interface.system_bar.enabled')
    },
    logoUrl () {
      return `/images/games/${this.gamePackageId}/logo.png`
    },
    paytableMin () {
      return this.config.min_bet
    },
    paytableMax () {
      return this.config.max_bet
    }
  },
  created () {
    this.chipsBet.forEach(el => this.$set(this.chips, el, []))
  },
  mounted () {
    this.$nextTick(this.init)
  },
  beforeDestroy () {
    window.removeEventListener('resize', this.resize)
  },
  methods: {
    copyToClipboard,
    async init () {
      await this.loadCardDeck()
      this.loadingProgress += 15
      await preloadImage(this.logoUrl)
      this.loadingProgress += 5
      await this.preloadGameImages(this.actions, progress => {
        this.loadingProgress = Math.min(25 + Math.round(progress / 100 * 80), 100)
      })
      this.ready = true
      await this.$nextTick()
      this.resize()
      window.addEventListener('resize', this.resize)
    },
    createProvablyFairGame () {
      this.$store.dispatch('provably-fair/create', { key: this.gamePackageId, seed: this.provablyFairGame.client_seed })
    },
    reset () {
      Object.keys(this.chips).forEach(k => {
        this.$set(this.chips, k, [])
      })
    },
    async resize () {
      // 64 40
      // this.$refs.table.style.transform = `scale(${window.outerWidth / 3184})`
      this.containerHeight = 50
      await this.$nextTick()
      this.containerHeight = this.$el.offsetHeight - 1
      if (3184 / 1440 <= window.innerWidth / this.containerHeight) {
      // if (3184 / 1440 <= window.innerWidth / (window.innerHeight - 64 - (window.innerWidth < 1250 ? 64 : 40))) {
        this.$refs.table.style.transform = `scale(${this.containerHeight / 1440}) translateX(-50%)`
      } else {
        this.$refs.table.style.transform = `scale(${window.innerWidth / 3184}) translateX(-50%)`
      }
    },
    placeBet (bet, pos, mode, source) {
      if (ChipValues.indexOf(bet) === -1) {
        let a = 0
        while (bet > 0) {
          const chip = ChipValues.reduce((acc, val) => bet >= val ? val : acc, 1)
          bet -= chip
          setTimeout(() => this.placeBet(chip, pos, mode, source), a += 100)
        }
      } else {
        if (mode === undefined) {
          mode = this.betMode
        }
        this.$emit('bet', bet, mode)
        if (!pos) {
          if (source === 'dealer') {
            pos = {
              x: 3184 / 2,
              y: 0
            }
          } else {
            pos = {
              x: 3184 / 2,
              y: 1520
            }
          }
        }
        if (mode && Array.isArray(this.chips[mode])) {
          this.chips[mode].push({ bet, pos })
        }
      }
    },
    unbet (mode) {
      if (mode === undefined) {
        mode = this.betMode
      }
      if (mode && Array.isArray(this.chips[mode]) && this.chips[mode].length) {
        this.$emit('unbet', this.chips[mode].pop().bet, mode)
      }
    },
    betWin (mode) {
      if (mode === undefined) {
        mode = this.betMode
      }
      return new Promise(resolve => {
        this.resolvers[mode] = () => {
          resolve()
        }
        this.$nextTick(() => {
          this.$set(this.chips, mode, [])
        })
      })
    },
    betLoss (mode) {
      return new Promise(resolve => {
        this.dealer = true
        this.resolvers[mode] = () => {
          resolve()
          this.dealer = false
        }
        this.$nextTick(() => {
          this.$set(this.chips, mode, [])
        })
      })
    },
    demoLose () {
      return new Promise(resolve => {
        this.dealer = true
        const lookupMode = Object.keys(this.chips).filter(k => this.chips[k].length)[0]
        this.resolvers[lookupMode] = () => {
          resolve()
          this.dealer = false
        }
        this.$nextTick(() => {
          Object.keys(this.chips).forEach(k => {
            this.$set(this.chips, k, [])
          })
        })
      })
    },
    async demoWin (wins) {
      const winsChip = {}
      if (typeof wins !== 'object') {
        const v = wins
        wins = {}
        wins[Object.keys(this.chips)[0]] = v
      }
      for (const k in wins) {
        let bet = wins[k]
        const chipsWin = []
        while (bet > 0) {
          const chip = ChipValues.reduce((acc, val) => bet >= val ? val : acc, 1)
          bet -= chip
          bet = parseFloat(bet.toFixed(6))
          chipsWin.push({
            bet: chip,
            pos: {
              x: 3184 / 2,
              y: 0
            }
          })
        }
        if (chipsWin.length) winsChip[k] = chipsWin
      }
      if (Object.keys(winsChip).length > 0) {
        this.dealer = true
        await this.$nextTick()
        for (const k in winsChip) {
          await new Promise(resolve => {
            this.resolvers[k] = resolve
            this.chips[k].push(...(winsChip[k]))
          })
        }
      }
      await sleep(2000)
      this.dealer = false
      await this.$nextTick()
      await new Promise(resolve => {
        const lookupMode = Object.keys(this.chips).filter(k => this.chips[k].length)[0]
        this.resolvers[lookupMode] = resolve
        Object.keys(this.chips).forEach(k => {
          this.$set(this.chips, k, [])
        })
      })
    },
    demoPush () {
      return new Promise(resolve => {
        const lookupMode = Object.keys(this.chips).filter(k => this.chips[k].length)[0]
        this.resolvers[lookupMode] = () => {
          resolve()
        }
        this.$nextTick(() => {
          Object.keys(this.chips).forEach(k => {
            this.$set(this.chips, k, [])
          })
        })
      })
    },
    chipsAnimated (chips) {
      if (typeof this.resolvers[chips] === 'function') {
        this.resolvers[chips]()
        delete this.resolvers[chips]
      }
    },
    async preloadGameImages (actions, onUpdate) {
      const buttons = []
      actions.forEach(button => {
        [null, 'active', 'disabled', 'hover'].forEach(state => {
          buttons.push(`${this.imageBaseUrl}/${button}${state ? '-' + state : ''}.png`)
        })
      })
      const images = [
        `${this.imageBaseUrl}/table-${this.deck}.jpg`,
        `${this.imageBaseUrl}/info.png`,
        `${this.imageBaseUrl}/provably.png`,
        `${this.imageBaseUrl}/full.png`,
        `${this.imageBaseUrl}/popup-bg.png`,
        ...ChipValues.map(chip => `${this.imageBaseUrl}/playing-chips/${chip}.png`),
        ...ChipValues.map(chip => `${this.imageBaseUrl}/bet-chips/${chip}.png`),
        ...buttons
      ]
      for (const image of images) {
        await preloadImage(image)
        if (typeof onUpdate === 'function') onUpdate(images.indexOf(image) / images.length * 100)
      }
    }
  }
}
</script>

<style scoped lang="scss">
@import 'scss/card-game-fonts.scss';

.card-game-container {
  width: 100%;
  height: 50px;
  // height: calc(100vw * 1440 / 3184);
  overflow: hidden;
  user-select: none;
  position: relative;
  background: #160008;
  &.modal {
    z-index: 6;
  }
  height: calc(100vh - 64px - 40px);
  &.system-bar-enabled {
    // height: calc(100vh - 64px - 40px - 20px);
  }
  @media (max-width: 1250px) {
    height: calc(100vh - 64px - 64px);
  }
}
.card-game-table {
  transform-origin: top left;
  position: relative;
  width: 3184px;
  height: 1440px;
  overflow: hidden;
  transform-style: preserve-3d;
  perspective: 1000px;
  left: 50%;
}
.logo {
  position: absolute;
  left: calc(3184px / 2);
  top: 415px;
  transform: translate(-50%, -50%);
  //opacity: 70%;
  //mix-blend-mode: color-dodge;
}
.paytable-min {
  font-family: impact;
  font-size: 15pt;
  text-transform: uppercase;
  position: absolute;
  top: 430px;
  left: 686px;
  transform: rotate(-24deg);
  color: #d2b782;
}
.paytable-max {
  font-family: impact;
  font-size: 15pt;
  text-transform: uppercase;
  position: absolute;
  top: 451px;
  left: 696px;
  transform: rotate(-24deg);
  color: #d2b782;
}
.paytable-link {
  position: absolute;
      top: 333px;
    left: 588px;
    width: 300px;
    height: 150px;
    transform: rotate(-24deg);
  cursor: pointer;
}
.chips {
  position: absolute;
  bottom: 70px;
  left: 360px;
  display: flex;
  transition: bottom 0.3s;
  &.disabled {
    bottom: -190px;
  }
}
.control-panel {
  position: absolute;
  bottom: 22px;
  left: 50%;
  transform: translate(-50%, 0);
  display: flex;
  justify-content: center;
  align-items: flex-end;
}
.cards {
  position: absolute;
  top: 637px;
  width: 100%;
  // transform: matrix3d(1,0,0.00,0,0.00,0.91,0.42,-0.0003,0,-0.42,0.91,0,0,0,0,1);
  transform: rotateX(13deg);
  transform-style: preserve-3d;
}
.button-mini {
  position: absolute;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  &.game-info {
    left: 364px;
    top: 30px;
  }
  &.provably {
    left: 445px;
    top: 30px;
  }
  &.full {
    right: 394px;
    top: 30px;
  }
  img {
    z-index: 0;
  }
  &:hover {
    img {
      filter: brightness(2);
    }
  }
  &:active {
    img {
      filter: brightness(4);
    }
  }
}
.provably-fair-form {
  min-width: 300px;
  label {
    margin-top: 15px;
    display: block;
    font-family: 'impact', sans-serif;
    font-size: 1.2em;
    font-weight: normal;
    color: #be9243;
  }
  input {
    color: #d3c9b7;
    border: 3px solid #ffb438;
    border-radius: 7px;
    padding: 3px 5px;
    margin: 5px 0;
    display: block;
    width: 100%;
  }
  .input-btn {
    display: flex;
    align-items: center;
    .btn-icon {
      cursor: pointer;
      padding: 0;
      width: 38px;
      height: 38px;
      margin-left: 8px;
      display: flex;
      justify-content: center;
      align-items: center;
      background: rgba(191 140 42 / 0.25);
      box-sizing: border-box;
      border-radius: 8px;
      flex-shrink: 0;
      transition: background 0.2s;
      &:hover {
        background: rgba(191 140 42 / 0.45);
      }
      &:active {
        background: rgba(191 140 42 / 0.65);
      }
      object {
        width: 20px;
        height: 20px;
      }
    }
  }
  button {
    background: rgba(191 140 42 / 0.25);
    box-sizing: border-box;
    border-radius: 8px;
    margin-top: 8px;
    padding: 8px 16px;
    transition: background 0.2s;
    &:hover {
      background: rgba(191 140 42 / 0.45);
    }
    &:active {
      background: rgba(191 140 42 / 0.65);
    }
  }
}

.portrait-warn {
  display: none;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-size: 25px;
  z-index: 100;
  background: black;
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  @media (orientation: portrait) {
    display: flex;
  }
}
</style>

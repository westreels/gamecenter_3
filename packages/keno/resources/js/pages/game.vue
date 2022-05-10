<template>
  <div v-if="!ready" class="d-flex flex-column fill-height py-3 align-center justify-center">
    <block-preloader />
  </div>
  <div v-else class="d-flex flex-column fill-height py-3 align-center justify-center">
    <div class="message">
      <game-message :message="message" />
    </div>
    <div :class="{light: !$vuetify.theme.isDark}">
      <div v-for="y in rows_count" :key="`row-${y}`" class="d-flex">
        <div v-for="x in cols_count" :key="`cell-${y}-${x}`" class="keno-btn" :class="{
          normal: selectedNumbers.indexOf((y - 1) * cols_count + x) == -1 && drawnNumbers.indexOf((y - 1) * cols_count + x) == -1,
          selected: selectedNumbers.indexOf((y - 1) * cols_count + x) != -1 && drawnNumbers.indexOf((y - 1) * cols_count + x) == -1,
          win: selectedNumbers.indexOf((y - 1) * cols_count + x) != -1 && drawnNumbers.indexOf((y - 1) * cols_count + x) != -1,
          loss: selectedNumbers.indexOf((y - 1) * cols_count + x) == -1 && drawnNumbers.indexOf((y - 1) * cols_count + x) != -1
        }" @click="numClick((y - 1) * cols_count + x)"
        >
          <div class="rainbow" />
          <div class="t1">
            <span />
            <span />
            <span />
            <span />
            {{ (y - 1) * cols_count + x }}
          </div>
          <div class="t2">
            {{ (y - 1) * cols_count + x }}
          </div>
        </div>
      </div>
    </div>
    <div class="message d-flex flex-column align-center justify-center">
      {{ selectedNumbers.length == selectCount ? '' : $t('Please select %{n} numbers to play', {n: selectCount}) }}
    </div>
    <div class="d-flex flex-row align-center justify-center">
      <v-btn text color="primary" :disabled="playing || selectedNumbers.length == 0" @click="selectedNumbers = []; drawnNumbers = []">
        {{ $t('Clear') }}
      </v-btn>
      <v-btn text color="primary" :disabled="playing" @click="random">
        {{ $t('Random') }}
      </v-btn>
      <v-btn text color="primary" :disabled="playing || drawnNumbers.length == 0" @click="drawnNumbers = []">
        {{ $t('Repeat') }}
      </v-btn>
    </div>
    <div class="d-flex flex-row align-center justify-center">
      <play-controls :loading="loading" :playing="playing" :disabled="!formIsValid || selectedNumbers.length !== selectCount || drawnNumbers.length !== 0 || playing" :class="{loading: !ready}" @play="play" />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import clickSound from '~/../audio/common/click.wav'
// import bangSound from 'packages/keno/resources/audio/bang.wav'
// import punchSound from 'packages/keno/resources/audio/punch.wav'
import winSound from 'packages/keno/resources/audio/win.wav'
import loseSound from 'packages/keno/resources/audio/lose.wav'
import betSound from 'packages/keno/resources/audio/clack.wav'
import noneSound from 'packages/keno/resources/audio/none.wav'
import startSound from 'packages/keno/resources/audio/clack.wav'
import unbetSound from 'packages/keno/resources/audio/unbet.wav'
import hitWinSound from 'packages/keno/resources/audio/hit1.wav'
import hitLossSound from 'packages/keno/resources/audio/deal.wav'
import PlayControls from '~/components/Games/PlayControls'
import BlockPreloader from '~/components/BlockPreloader'
import GameMessage from '~/components/Games/GameMessage'
import arrayShuffle  from 'array-shuffle'
export default {
  name: 'Keno',

  components: { BlockPreloader, PlayControls, GameMessage },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      clickSound,
      formIsValid: true,
      loading: false,
      playing: false,
      ready: false,
      message: null,
      selectedNumbers: [],
      drawnNumbers: [],
      showDelayMS: 100
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    rows_count () {
      return parseInt(this.config.rows_count, 10)
    },
    cols_count () {
      return parseInt(this.config.cols_count, 10)
    },
    selectCount () {
      return parseInt(this.config.select_count, 10)
    }
  },

  beforeDestroy () {
    this.ready = false
  },
  mounted () {
    const root = document.documentElement
    root.style.setProperty('--keno-cols', this.config.cols_count)
    setTimeout(() => {
      this.ready = true
    }, 1000)
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    async play (bet) {
      this.sound(startSound)
      this.message = null
      this.loading = true
      this.playing = true
      this.win = 0

      // update user balance
      this.updateUserAccountBalance(this.account.balance - bet)

      // API request params
      const endpoint = this.getRoute('play')
      const requestParams = { hash: this.provablyFairGame.hash, bet, numbers: this.selectedNumbers }

      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)
      if (!this.ready) return
      this.loading = false
      const wins = arrayShuffle(game.gameable.drawn_numbers)
      this.drawnNumbers = []
      while (wins.length) {
        const i = wins.shift()
        if (this.drawnNumbers.indexOf(i) === -1) {
          this.drawnNumbers.push(i)
          if (this.selectedNumbers.indexOf(i) === -1) {
            this.sound(hitLossSound)
          } else {
            this.sound(hitWinSound)
          }
          await (() => new Promise(resolve => setTimeout(resolve, this.showDelayMS)))()
        }
        if (!this.ready) return
      }

      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })
      this.win = game.win
      this.playing = false

      // update balance
      this.updateUserAccountBalance(game.account.balance)
      if (game.win > 0) {
        this.sound(winSound)
        this.message = this.$t('You won') + ' ' + game.win
      } else {
        this.sound(loseSound)
        this.message = this.$t('No luck')
      }
    },
    numClick (x) {
      if (this.playing) {
        this.sound(noneSound)
        return
      }
      if (this.drawnNumbers.length) {
        this.drawnNumbers = []
        this.selectedNumbers = []
        this.message = ''
      }
      let i
      if ((i = this.selectedNumbers.indexOf(x)) !== -1) {
        this.selectedNumbers.splice(i, 1)
        this.sound(unbetSound)
      } else if (this.selectedNumbers.length < this.selectCount) {
        this.selectedNumbers.push(x)
        this.sound(betSound)
      } else {
        this.sound(noneSound)
      }
    },
    async random () {
      this.playing = true
      this.drawnNumbers = []
      this.selectedNumbers = []
      let nums = []
      for (let i = 1; i <= this.config.cols_count * (this.config.rows_count - 1); i++) nums.push(i)
      nums = arrayShuffle(nums).slice(0, this.selectCount)
      while (nums.length) {
        this.selectedNumbers.push(nums.shift())
        this.sound(betSound)
        await (() => new Promise(resolve => setTimeout(resolve, this.showDelayMS)))()
        if (!this.ready) return
      }
      this.playing = false
    }
  }
}
</script>

<style lang="scss" scoped>
.message {
  height: 60px;

}

@keyframes btn-neon-border-top {
  from {
    left: -100%;
  }

  to {
    left: 100%;
  }
}

@keyframes btn-neon-border-right {
  from {
    top: -100%;
  }

  to {
    top: 100%;
  }
}

@keyframes btn-neon-border-bottom {
  from {
    right: -100%;
  }

  to {
    right: 100%;
  }
}

@keyframes btn-neon-border-left {
  from {
    bottom: -100%;
  }

  to {
    bottom: 100%;
  }
}

@keyframes neon-title {

  0%,
  100% {
    // clip-path: polygon(0% 51%, 13% 48%, 26% 50%, 37% 53%, 50% 55%, 61% 58%, 76% 60%, 91% 59%, 100% 51%, 100% 100%, 0 100%);
    clip-path: polygon(0% 46%, 13% 43%, 26% 45%, 37% 48%, 50% 50%, 61% 53%, 76% 55%, 91% 54%, 100% 46%, 100% 100%, 0 100%);
  }

  50% {
    // clip-path: polygon(0 51%, 9% 57%, 21% 62%, 36% 63%, 49% 57%, 60% 50%, 75% 46%, 91% 44%, 100% 42%, 100% 100%, 0 100%);
    clip-path: polygon(0 46%, 9% 52%, 21% 57%, 36% 58%, 49% 52%, 60% 45%, 75% 41%, 91% 39%, 100% 37%, 100% 100%, 0 100%);
  }
}

@keyframes neon-block {
  from {
    filter: hue-rotate(0deg);
  }
  to {
    filter: hue-rotate(360deg);
  }
}

.keno-btn {
  width: 40px;
  height: 40px;
  cursor: pointer;
  margin: 6px 12px;
  position: relative;
  padding: 0;
  color: #f2f2f2;
  transition: .5s;
  @media (max-width: 750px) {
        margin: 6px 3px;
        width: calc((100vw - 16px) / var(--keno-cols) - 6px);
    }
  &:before,
  &:after {
    content: '';
    position: absolute;
    top: -2px;
    right: -2px;
    bottom: -2px;
    left: -2px;
    z-index: 2;
    transition: .2s;
    opacity: 1;
  }

  &:after {
    z-index: 1;
    filter: blur(7px);
  }

  &:before,
  &:after {
    background: linear-gradient(235deg, var(--v-primary-base), var(--v-accent-base));
  }
  .t1,
  .t2 {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 3;
    transition: .2s;
  }

  .t2 {
    z-index: 4;
  }

  .t1 {
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--v-secondary-darken2);
    font-size: 16px;
  }

  .t2 {
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--v-secondary-darken2);
    font-size: 16px;
    opacity: 0;
  }

  .rainbow {
    position: absolute;
    top: -4px;
    right: -4px;
    bottom: -4px;
    left: -4px;
    filter: blur(15px);
    z-index: 1;

    &:after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background: linear-gradient(315deg, var(--v-primary-base), var(--v-accent-base));
    }

    &:after {
      animation: neon-block 1.5s linear infinite;
    }
    opacity: 0;
  }
  &.normal:hover {
    .t1 {
      border-radius: 5px;
      background: transparent;
      overflow: hidden;

      span {
        position: absolute;
        transition: all .25s;
        animation-duration: 3s;
        animation-iteration-count: infinite;

        &:nth-child(1) {
          top: 0;
          left: -100%;
          width: 100%;
          height: 2px;
          background: linear-gradient(90deg, transparent 30%, var(--v-primary-base));
          animation-name: btn-neon-border-top;
        }

        &:nth-child(2) {
          right: 0;
          top: -100%;
          width: 2px;
          height: 100%;
          background: linear-gradient(180deg, transparent 30%, var(--v-primary-base));
          animation-name: btn-neon-border-right;
          animation-delay: calc(3s / 4);
        }

        &:nth-child(3) {
          right: -100%;
          bottom: 0;
          width: 100%;
          height: 2px;
          background: linear-gradient(270deg, transparent 30%, var(--v-primary-base));
          animation-name: btn-neon-border-bottom;
          animation-delay: calc(3s / 4 * 2);
        }

        &:nth-child(4) {
          bottom: -100%;
          left: 0;
          width: 2px;
          height: 100%;
          background: linear-gradient(360deg, transparent 30%, var(--v-primary-base));
          animation-name: btn-neon-border-left;
          animation-delay: calc(3s / 4 * 3);
        }
      }
    }

    &:before,
    &:after {
      opacity: 0;
    }
  }

  &.selected {
    .t1 {
      border-radius: 5px;
      background: transparent;
      overflow: hidden;

      span {
        position: absolute;
        transition: all .25s;
        animation-duration: 3s;
        animation-iteration-count: infinite;
      }

      background: var(--v-primary-base);
        color: #151515;
        box-shadow: 0 0 10px var(--v-primary-base),
          0 0 30px var(--v-primary-base),
          0 0 50px var(--v-primary-base);
    }

    &:before,
    &:after {
      opacity: 0;
    }

    &:hover {
      .t1 {
        background: var(--v-primary-lighten1);
        color: #151515;
        box-shadow: 0 0 10px var(--v-primary-lighten1),
          0 0 30px var(--v-primary-lighten1),
          0 0 50px var(--v-primary-lighten1);
      }
    }
  }

  &.loss {
    &:before,
    &:after {
      opacity: 0;
    }
    .t1 {
      border-radius: 5px;
      overflow: hidden;
      background: var(--v-accent-lighten1);
      color: #151515;
      box-shadow: 0 0 10px var(--v-accent-lighten1),
        0 0 30px var(--v-accent-lighten1),
        0 0 50px var(--v-accent-lighten1);
    }
  }
  &.win {
    transform: scale(1.2);
    &:before {
      opacity: 0;
    }
    .t1,
    .t2 {
      font-size: 25px;
      font-weight: bolder;
      text-transform: uppercase;
      color: transparent;
      text-shadow: 0 0 15px var(--v-accent-lighten1);
      -webkit-text-stroke: 0.25px var(--v-accent-lighten1);
      opacity: 1
    }
    .t2 {
      color: var(--v-accent-lighten1);
      animation: neon-title 3s ease-in-out infinite;
    }
    .t1 {
      background: linear-gradient(45deg, #272325 40%, #020104);
    }
    &:after {
      background: linear-gradient(45deg, var(--v-primary-base), var(--v-accent-base));
      animation: neon-block 1.5s linear infinite;
    }
    .rainbow {
      opacity: 1;
    }
  }
}
.light .keno-btn {
  color: #333333;
  .t1 {
    background: white;
  }
  .t2 {
    background: var(--v-primary-lighten4);
  }
  &:before,
  &:after {
    background: linear-gradient(235deg, var(--v-primary-base), var(--v-accent-base));
  }
  &.loss {
    .t1 {
      background: var(--v-accent-lighten1);
    }
  }
  &.win {
    .t1,
    .t2 {
      text-shadow: 0 0 15px var(--v-accent-darken4);
      -webkit-text-stroke: 0.5px var(--v-accent-darken4);
      opacity: 1
    }
    .t2 {
      color: var(--v-primary-darken4);
    }
    .t1 {
      background: linear-gradient(45deg, var(--v-primary-lighten4) 40%, var(--v-primary-lighten3));
    }
  }
}
</style>

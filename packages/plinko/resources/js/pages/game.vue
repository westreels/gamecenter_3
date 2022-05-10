<template>
  <div class="d-flex flex-column fill-height py-3 align-center justify-center">
    <div class="message-container" :class="{loading: !ready}">
      <game-message class="message" :message="message" />
    </div>
    <div ref="game" class="game d-flex justify-center align-center" :class="{light: !$vuetify.theme.dark, loading: !ready}"></div>
    <play-controls :loading="loading" :playing="playing" :disabled="!formIsValid" :class="{loading: !ready}" @play="play" />
    <block-preloader v-if="!ready" />
  </div>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import clickSound from '~/../audio/common/click.wav'
import bangSound from 'packages/plinko/resources/audio/bang.wav'
import punchSound from 'packages/plinko/resources/audio/punch.wav'
import winSound from 'packages/plinko/resources/audio/win.wav'
import loseSound from 'packages/plinko/resources/audio/lose.wav'
import PlayControls from '~/components/Games/PlayControls'
import BlockPreloader from '~/components/BlockPreloader'
import GameMessage from '~/components/Games/GameMessage'

import Plinko from './helpers/plinko.js'

export default {
  name: 'Plinko',

  components: { BlockPreloader, PlayControls, GameMessage },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      clickSound,
      formIsValid: true,
      loading: false,
      playing: false,
      plinko: null,
      ready: false,
      message: null
    }
  },

  computed: {
    ...mapState('auth', ['account'])
  },

  created () {
    //
  },

  mounted () {
    this.$nextTick(async () => {
      this.plinko = new Plinko()
      this.plinko.ballColor = this.$vuetify.theme.currentTheme.primary
      this.plinko.pegColor = this.$vuetify.theme.currentTheme.accent
      this.plinko.punch = () => this.sound(punchSound)
      this.plinko.bang = () => this.sound(bangSound)
      this.plinko.light = !this.$vuetify.theme.dark
      await this.plinko.setup(this.$refs.game, this.config.paytable)
      this.ready = true
    })
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    async play (bet) {
      this.message = null
      this.plinko.bucket = -1
      this.loading = true
      this.playing = true
      this.win = 0

      // update user balance
      this.updateUserAccountBalance(this.account.balance - bet)

      // API request params
      const endpoint = this.getRoute('play')
      const requestParams = { hash: this.provablyFairGame.hash, bet }

      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)
      this.loading = false

      await (path => new Promise(resolve => this.plinko.pull(path, game.gameable.bucket, resolve)))(game.gameable.path)

      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })
      this.win = game.win
      this.playing = false

      // update balance
      this.updateUserAccountBalance(game.account.balance)
      // play sound
      setTimeout(() => {
        if (game.win > 0) {
          this.sound(winSound)
          this.message = this.$t('You won') + ' ' + game.win
        } else {
          this.sound(loseSound)
        }
      }, 500)
    }
  }
}
</script>

<style lang="scss" scoped>
  .game {
    width: 100%;
  }
  .game::v-deep canvas {
      @media (orientation: landscape) and (min-height: 500px) {
        max-height: calc(100vh - 250px);
      }
      @media (orientation: landscape) and (max-height: 499px) {
        max-height: calc(100vh - 50px);
      }
      @media (orientation: portrait) {
        width: 100%;
      }
      background: none !important;
  }
  .game.light::v-deep {
    canvas {
      background: none !important;
      position: relative;
      z-index:2
    }
    position: relative;
  }
  .loading {
    display: none !important;
  }
  .message-container {
    height: 0;
    margin-top: -20px;
    margin-bottom: 20px;
    position: relative;
    z-index: 100;
  }
</style>

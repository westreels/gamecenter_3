<template>
  <card-table
    ref="cardTable"
    class="fill-height"
    :actions="['deal', 'rebet', 'newbet']"
    :bet-mode="betMode"
    :chips-bet="['player', 'tie', 'banker']"
    @bet="placeBet"
    @unbet="onUnbet"
    @click="resultModal = false"
  >
    <template #bet-panel="{ chips, unbet, dealer, animated }">
      <bet
        :chips="chips.player"
        :label="$t('Player')"
        :position="-1.85"
        :dealer="dealer"
        :blink="!playing && !loading && gameBetType == 0"
        @click="() => (!playing && !loading) ? gameBetType = 0 : null"
        @unbet="() => onUnbetClick(0, unbet)"
        @animated="e => animated('player')"
      />
      <bet
        :chips="chips.tie"
        :label="$t('Tie')"
        :position="0"
        :dealer="dealer"
        :blink="!playing && !loading && gameBetType == 1"
        @click="() => (!playing && !loading) ? gameBetType = 1 : null"
        @unbet="() => onUnbetClick(1, unbet)"
        @animated="e => animated('tie')"
      />
      <bet
        :chips="chips.banker"
        :label="$t('Bank')"
        :position="1.85"
        :dealer="dealer"
        :blink="!playing && !loading && gameBetType == 2"
        @click="() => (!playing && !loading) ? gameBetType = 2 : null"
        @unbet="() => onUnbetClick(2, unbet)"
        @animated="e => animated('banker')"
      />
    </template>
    <template #control-panel>
      <transition name="slide-down">
        <div v-if="!playing && !loading" class="btns-panel">
          <action-button type="deal" :title="$t('Deal')" :disabled="loading || Object.values(gameBet).reduce((a, i) => a + i, 0) < config.min_bet" @click="onDealClick" />
          <action-button type="rebet" :title="$t('Rebet')" :disabled="!isRebetActive || loading" @click="onRebetClick" />
          <action-button type="newbet" :title="$t('New bet')" :disabled="loading || Object.values(gameBet).reduce((a, i) => a + i, 0) == 0" @click="onNewbetClick" />
        </div>
      </transition>
    </template>
    <template #paytable>
      <paytable />
    </template>
    <template #info>
      <info />
    </template>
    <template #bottom>
      <modal-result v-model="resultModal" :glow="win > 0 ? 'win' : win < 0 ? 'lose' : 'push'">
        <h5>{{ result }}</h5>
        <p v-if="win > 0">
          {{ win }}
        </p>
      </modal-result>
    </template>
    <hand :cards="playerCards" :cards-count="2" :position-x="-1.5" :position-y="0" :result="playerResult" :demo="playerDemo" />
    <hand :cards="dealerCards" :cards-count="2" :position-x="1.5" :position-y="0" :result="dealerResult" :demo="dealerDemo" />
  </card-table>
</template>

<script>
import CardTable from '~/components/Games/CardGame/Table'
import ActionButton from '~/components/Games/CardGame/Button'
import Hand from '~/components/Games/CardGame/Hand'
import Bet from '~/components/Games/CardGame/Bet'
import ModalResult from '~/components/Games/CardGame/ModalResult'
import Paytable from './paytable'
import Info from './info'

import swooshSound from 'packages/baccarat/resources/audio/swoosh.wav'
import winSound from 'packages/baccarat/resources/audio/win.wav'
import loseSound from 'packages/baccarat/resources/audio/lose.wav'
import pushSound from 'packages/baccarat/resources/audio/push.wav'

import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import { sleep } from '~/plugins/utils'

export default {
  name: 'Baccarat',

  components: { CardTable, ActionButton, Hand, Bet, ModalResult, Paytable, Info },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      ready: true,
      playing: false,
      loading: false,

      gameBet: {
        player: 0,
        banker: 0,
        tie: 0
      },
      gameBetType: 0,

      gameBetOld: null,

      playerCards: [],
      dealerCards: [],
      playerDemo: [],
      dealerDemo: [],
      playerResult: '',
      dealerResult: '',
      resultModal: false,
      result: '',
      win: 0
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    betMode () {
      if (this.playing || this.loading) return null
      switch (this.gameBetType) {
        case 0: return 'player'
        case 1: return 'tie'
        default: return 'banker'
      }
    },
    isRebetActive () {
      return this.gameBetOld && Object.values(this.gameBet).reduce((a, b) => a + b, 0) === 0 && this.account.balance >= Object.values(this.gameBetOld).reduce((acc, item) => acc + item, 0)
    }
  },

  beforeDestroy () {
    this.ready = false
  },
  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    placeBet (bet, mode) {
      if (!this.playing) this.gameBet[mode] += bet
    },
    onUnbet (bet, mode) {
      if (!this.playing) this.gameBet[mode] -= bet
    },
    async onUnbetClick(idx, f) {
      if (!this.playing && !this.loading) {
        if (this.gameBetType !== idx) {
          this.gameBetType = idx
          await this.$nextTick()
        }
        f()
      }
    },
    resetPlayers () {
      this.playerCards = []
      this.dealerCards = []
      this.playerDemo = []
      this.dealerDemo = []
      this.playerResult = ''
      this.dealerResult = ''
    },
    modalWin (win) {
      this.result = this.$t('You win')
      this.win = win
      this.resultModal = true
    },
    modalLose () {
      this.result = this.$t('You lose')
      this.win = -1
      this.resultModal = true
    },
    modalPush () {
      this.result = this.$t('Push')
      this.win = 0
      this.resultModal = true
    },
    async onDealClick () {
      this.loading = true
      this.playing = true

      this.resetPlayers()
      // update user balance
      this.updateUserAccountBalance(this.account.balance - this.gameBet)
      this.sound(swooshSound)
      await sleep(500)

      const endpoint = this.getRoute('play')

      const requestParams = {
        hash: this.provablyFairGame.hash,
        player_bet: this.gameBet.player === 0 ? null : this.gameBet.player,
        banker_bet: this.gameBet.banker === 0 ? null : this.gameBet.banker,
        tie_bet: this.gameBet.tie === 0 ? null : this.gameBet.tie
      }

      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)
      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })
      this.loading = false
      if (!this.ready) return
      this.playerCards.push(game.gameable.player_hand[0])
      await sleep(400)
      this.dealerCards.push(game.gameable.banker_hand[0])
      await sleep(400)
      this.playerCards.push(game.gameable.player_hand[1])
      await sleep(400)
      this.dealerCards.push(game.gameable.banker_hand[1])
      await sleep(400)

      this.playerResult = game.gameable.initial_player_score.toString()
      this.dealerResult = game.gameable.initial_banker_score.toString()

      await sleep(2000)
      if (game.gameable.player_hand.length > 2) {
        this.playerCards.push(game.gameable.player_hand[2])
        await sleep(800)
      }
      if (game.gameable.banker_hand.length > 2) {
        this.dealerCards.push(game.gameable.banker_hand[2])
        await sleep(800)
      }
      this.playerResult = game.gameable.player_score.toString()
      this.dealerResult = game.gameable.banker_score.toString()
      await sleep(1000)
      const resultDemo = async () => {
        const betWin = {}
        if (game.gameable.banker_win < game.gameable.banker_bet) {
          this.$refs.cardTable.betLoss('banker')
        } else {
          betWin.banker = game.gameable.banker_win - game.gameable.banker_bet
        }
        if (game.gameable.player_win < game.gameable.player_bet) {
          this.$refs.cardTable.betLoss('player')
        } else {
          betWin.player = game.gameable.player_win - game.gameable.player_bet
        }
        if (game.gameable.tie_win < game.gameable.tie_bet) {
          this.$refs.cardTable.betLoss('tie')
        } else {
          betWin.tie = game.gameable.tie_win - game.gameable.tie_bet
        }
        if (Object.keys(betWin).length > 0 && Object.values(betWin).reduce((acc, item) => acc + item, 0) > 0) {
          await this.$refs.cardTable.demoWin(betWin)
        }
      }
      // play sound
      if (game.win > game.bet) {
        this.sound(winSound)
        await resultDemo()
        this.modalWin(game.win)
      } else if (game.win === game.bet) {
        this.sound(pushSound)
        await resultDemo()
        this.modalPush()
      } else {
        this.sound(loseSound)
        await resultDemo()
        this.modalLose()
      }
      this.updateUserAccountBalance(game.account.balance)
      this.gameBetOld = {}
      for (const i in this.gameBet) {
        this.gameBetOld[i] = this.gameBet[i]
        this.gameBet[i] = 0
      }
      this.playing = false
      await sleep(3000)
      if (!this.playing && !this.loading) this.resetPlayers()
    },
    async onRebetClick () {
      await this.$nextTick()
      for (const i in this.gameBetOld) {
        this.$refs.cardTable.placeBet(this.gameBetOld[i], null, i)
      }
      this.resetPlayers()
      this.loading = true
      await sleep(1000)
      this.onDealClick()
    },
    onNewbetClick () {
      this.$refs.cardTable.reset()
      this.resetPlayers()
      for (const i in this.gameBet) this.gameBet[i] = 0
    }
  }
}
</script>

<style scoped lang="scss">
  .btns-panel {
      position: absolute;
      left: 50%;
      top: -104px;
      transform: translate(-50%, -50%);
      display: flex;
  }

  .slide-down-enter-active {
    transition: 0.6s;
    opacity: 0;
    transform: translate(-50%, 50%);
  }

  .slide-down-leave-active {
    transition: 0.6s;
    opacity: 1;
    transform: translate(-50%, -50%);
  }

  .slide-down-enter-to, .slide-down-leave {
    opacity: 1;
    transform: translate(-50%, -50%);
  }

  .slide-down-enter, .slide-down-leave-to {
    opacity: 0;
    transform: translate(-50%, 50%);
  }
</style>

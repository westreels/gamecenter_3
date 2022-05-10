<template>
  <card-table
    ref="cardTable"
    class="fill-height"
    :actions="['deal', 'rebet', 'newbet', 'call', 'fold']"
    :bet-mode="betMode"
    :chips-bet="['bonus', 'ante', 'call']"
    @bet="placeBet"
    @unbet="onUnbet"
    @click="resultModal = false"
  >
    <template #bet-panel="{ chips, unbet, dealer, animated }">
      <bet
        :chips="chips.bonus"
        :label="$t('Bonus')"
        :size="0.95"
        :font-size="0.7"
        :position="-1.65"
        :dealer="dealer"
        :blink="!playing && !loading && betType == 'bonus'"
        @click="onBonusClick"
        @unbet="() => !playing && !loading && betType == 'bonus' ? unbet() : onBonusClick()"
        @animated="e => animated('bonus')"
      />
      <bet
        :chips="chips.ante"
        :label="$t('Ante')"
        :dealer="dealer"
        :blink="!playing && !loading && betType == 'ante'"
        @click="onAnteClick"
        @unbet="() => !playing && !loading && betType == 'ante' ? unbet() : onAnteClick()"
        @animated="e => animated('ante')"
      />
      <bet
        :chips="chips ? chips.call : []"
        :label="$t('Call')"
        :size="0.95"
        :font-size="0.7"
        value-right
        :position="1.65"
        :dealer="dealer"
        :blink="playing && !loading"
        @animated="e => animated('call')"
      />
    </template>
    <template #control-panel>
      <transition name="slide-down">
        <div v-if="!playing && !loading" class="btns-panel">
          <action-button type="deal" :title="$t('Deal')" :disabled="isDealDisabled" @click="onDealClick" />
          <action-button type="rebet" :title="$t('Rebet')" :disabled="isRebetDisabled" @click="onRebetClick" />
          <action-button type="newbet" :title="$t('New bet')" :disabled="isNewbetDisabled" @click="onNewbetClick" />
        </div>
      </transition>
      <transition name="slide-down">
        <div v-if="playing && !loading" class="btns-panel">
          <action-button type="fold" :title="$t('Fold')" :disabled="isFoldDisabled" @click="onFoldClick" />
          <action-button type="call" :title="$t('Call')" :disabled="isCallDisabled" @click="onCallClick" />
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
    <hand :cards="dealerCards" :cards-count="2" :position-x="0" :position-y="-1.5" :result="dealerResult" :demo="dealerDemo" />
    <hand :cards="tableCards" :cards-count="5" :position-x="0" :position-y="-0.5" :demo="tableDemo" />
    <hand :cards="playerCards" :cards-count="2" :position-x="0" :position-y="0.5" :result="playerResult" :demo="playerDemo" />
  </card-table>
</template>

<script>
import CardTable from '~/components/Games/CardGame/Table'
import Bet from '~/components/Games/CardGame/Bet'
import ActionButton from '~/components/Games/CardGame/Button'
import Hand from '~/components/Games/CardGame/Hand'
import ModalResult from '~/components/Games/CardGame/ModalResult'
import Paytable from './paytable'
import Info from './info'

import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import { sleep } from '~/plugins/utils'
import swooshSound from 'packages/casino-holdem/resources/audio/swoosh.wav'
import winSound from 'packages/casino-holdem/resources/audio/win.wav'
import loseSound from 'packages/casino-holdem/resources/audio/lose.wav'
import pushSound from 'packages/casino-holdem/resources/audio/push.wav'

export default {
  name: 'CasinoHoldem',

  components: { CardTable, Bet, ActionButton, Hand, ModalResult, Paytable, Info },

  mixins: [GameMixin, SoundMixin],

  data () {
    return {
      ready: true,

      playing: false,
      loading: false,

      betType: 'ante',
      gameBet: 0,
      gameBetOld: 0,
      gameBonus: 0,
      gameBonusOld: 0,
      tableCards: [],
      tableDemo: [],
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
      if (this.playing || this.loading) {
        return null
      } else {
        return this.betType
      }
    },
    isDealDisabled () {
      return this.loading || this.gameBet < this.config.min_bet
    },
    isRebetDisabled () {
      return this.loading || this.gameBet > 0 || this.gameBonus > 0 || this.gameBetOld === 0
    },
    isNewbetDisabled () {
      return this.loading || (this.gameBet === 0 && this.gameBonus === 0)
    },
    isCallDisabled () {
      return this.loading || this.account.balance < this.gameBet * 2
    },
    isFoldDisabled () {
      return this.loading
    }
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    placeBet (bet, mode) {
      if (!this.playing && !this.loading) {
        if (mode === 'ante') {
          this.gameBet += bet
        } else {
          this.gameBonus += bet
        }
      }
    },
    onUnbet (bet, mode) {
      if (!this.playing && !this.loading) {
        if (mode === 'ante') {
          this.gameBet -= bet
        } else {
          this.gameBonus -= bet
        }
      }
    },
    resetPlayers () {
      this.playerCards = []
      this.dealerCards = []
      this.tableCards = []
      this.playerDemo = []
      this.dealerDemo = []
      this.tableDemo = []
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
    onBonusClick () {
      if (!this.playing && !this.loading) {
        this.betType = 'bonus'
      }
    },
    onAnteClick () {
      if (!this.playing && !this.loading) {
        this.betType = 'ante'
      }
    },
    async onRebetClick () {
      this.$refs.cardTable.placeBet(this.gameBetOld, null, 'ante')
      this.$refs.cardTable.placeBet(this.gameBonusOld, null, 'bonus')
      this.loading = true
      await sleep(500)
      this.resetPlayers()
      await sleep(1000)
      this.onDealClick()
    },
    onNewbetClick () {
      this.gameBet = 0
      this.gameBonus = 0
      this.betType = 'ante'
      this.$refs.cardTable.reset()
      this.resetPlayers()
    },
    async onDealClick () {
      this.loading = true
      this.playing = true
      this.betType = 'ante'
      this.updateUserAccountBalance(this.account.balance - this.gameBet - this.gameBonus)
      this.sound(swooshSound)
      const { data: game } = await axios.post(this.getRoute('play'), { hash: this.provablyFairGame.hash, bet: this.gameBet, bonus_bet: this.gameBonus })
      this.playerCards.push(game.gameable.player_cards[0])
      await sleep(400)
      this.dealerCards.push(null)
      await sleep(400)
      this.playerCards.push(game.gameable.player_cards[1])
      await sleep(400)
      this.dealerCards.push(null)
      await sleep(400)
      for (const card of game.gameable.community_cards) {
        this.tableCards.push(card)
        await sleep(400)
      }
      await sleep(1400)
      this.playerResult = game.gameable.player_hand_title
      this.playerDemo = game.gameable.player_combination_cards
      this.tableDemo = game.gameable.player_combination_cards
      await sleep(1500)
      this.playerDemo = []
      this.tableDemo = []
      this.loading = false
    },
    async onCallClick () {
      this.playerResult = ''
      this.loading = true
      this.$refs.cardTable.placeBet(this.gameBet * 2, null, 'call')
      this.updateUserAccountBalance(this.account.balance - this.gameBet * 2)
      await sleep(500)
      const { data: game } = await axios.post(this.getRoute('call'), { hash: this.provablyFairGame.hash })
      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })
      this.tableCards.push(game.gameable.community_cards[3])
      await sleep(400)
      this.tableCards.push(game.gameable.community_cards[4])
      await sleep(1000)
      this.playerResult = game.gameable.player_hand_title
      this.tableDemo = game.gameable.player_combination_cards
      this.playerDemo = game.gameable.player_combination_cards
      await sleep(2000)
      this.playerDemo = []
      this.tableDemo = []
      await sleep(400)
      this.$set(this.dealerCards, 0, game.gameable.dealer_cards[0])
      await sleep(400)
      this.$set(this.dealerCards, 1, game.gameable.dealer_cards[1])
      await sleep(1200)
      this.dealerDemo = game.gameable.dealer_combination_cards
      this.tableDemo = game.gameable.dealer_combination_cards
      this.dealerResult = game.gameable.dealer_hand_title
      await sleep(2000)
      this.dealerDemo = []
      this.tableDemo = []
      const demoResult = async () => {
        const betWin = {}
        if (game.gameable.ante_win < game.gameable.ante_bet) {
          this.$refs.cardTable.betLoss('ante')
        } else {
          betWin.ante = game.gameable.ante_win - game.gameable.ante_bet
        }
        if (game.gameable.call_win < game.gameable.call_bet) {
          this.$refs.cardTable.betLoss('call')
        } else {
          betWin.call = game.gameable.call_win - game.gameable.call_bet
        }
        if (game.gameable.bonus_win < game.gameable.bonus_bet) {
          this.$refs.cardTable.betLoss('bonus')
        } else {
          betWin.bonus = game.gameable.bonus_win - game.gameable.bonus_bet
        }
        if (Object.keys(betWin).length > 0 && Object.values(betWin).reduce((acc, item) => acc + item, 0) > 0) {
          await this.$refs.cardTable.demoWin(betWin)
        } else {
          await sleep(400)
        }
      }
      if (game.gameable.dealer_qualified) {
        if (game.gameable.call_win > game.gameable.call_bet) {
          this.sound(winSound)
          await demoResult()
          this.modalWin(game.win)
        } else if (game.gameable.call_win === game.gameable.call_bet) {
          this.sound(pushSound)
          await demoResult()
          this.modalPush()
        } else {
          this.sound(loseSound)
          await demoResult()
          this.modalLose()
        }
      } else {
        this.sound(winSound)
        await demoResult()
        this.modalWin(game.win)
      }
      this.gameBetOld = this.gameBet
      this.gameBonusOld = this.gameBonus
      this.gameBet = 0
      this.gameBonus = 0
      this.loading = false
      this.playing = false
      await sleep(3000)
      if (!this.playing && !this.loading) this.resetPlayers()
    },
    async onFoldClick () {
      this.loading = true
      const { data: game } = await axios.post(this.getRoute('fold'), { hash: this.provablyFairGame.hash })
      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })
      this.sound(loseSound)
      await this.$refs.cardTable.demoLose()
      this.modalLose()
      this.gameBetOld = this.gameBet
      this.gameBonusOld = this.gameBonus
      this.gameBet = 0
      this.gameBonus = 0
      this.loading = false
      this.playing = false
      await sleep(3000)
      if (!this.playing && !this.loading) this.resetPlayers()
    }
  }
}
</script>
<style lang="scss" scoped>
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

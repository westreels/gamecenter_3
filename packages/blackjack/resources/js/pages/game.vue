<template>
  <card-table
    ref="cardTable"
    class="fill-height"
    :actions="['deal', 'rebet', 'newbet', 'double', 'stand', 'hit', 'split']"
    :bet-mode="betMode"
    :chips-bet="['ante', 'ante2', 'insurance']"
    @bet="placeBet"
    @unbet="onUnbet"
    @click="resultModal = false"
  >
    <template #bet-panel="{ chips, unbet, dealer, animated }">
      <bet
        :chips="chips.ante"
        :label="$t('Bet')"
        :blink="!playing && !loading && gameBet === 0"
        :dealer="dealer"
        :position="isSplitted ? -0.5 : 0"
        @unbet="() => !playing && !loading ? unbet() : null"
        @animated="e => animated('ante')"
      />
      <bet
        v-if="isSplitted"
        :chips="chips.ante2"
        :label="$t('Bet')"
        value-right
        :position="0.5"
        :dealer="dealer"
        @animated="e => animated('ante2')"
      />
      <bet
        v-if="isInsurance"
        :chips="chips.insurance"
        :label="$t('Insurance')"
        value-right
        :position="1"
        :dealer="dealer"
        @animated="e => animated('insurance')"
      />
    </template>
    <template #control-panel>
      <transition name="slide-down">
        <div v-if="!playing && !loading" class="btns-panel">
          <action-button type="deal" :title="$t('Deal')" :disabled="loading || gameBet < config.min_bet" @click="onDealClick" />
          <action-button type="rebet" :title="$t('Rebet')" :disabled="!isRebetActive || loading || gameBet > 0" @click="onRebetClick" />
          <action-button type="newbet" :title="$t('New bet')" :disabled="loading || gameBet === 0" @click="onNewbetClick" />
        </div>
      </transition>
      <transition name="slide-down">
        <div v-if="playing && !loading" class="btns-panel">
          <action-button type="double" :title="$t('Double')" :disabled="loadingAction || loading || !isDouble" @click="onDoubleClick" />
          <action-button type="stand" :title="$t('Stand')" :disabled="loadingAction || loading || !isStand" @click="onStandClick" />
          <action-button type="hit" :title="$t('Hit')" :disabled="loadingAction || loading || !isHit" @click="onHitClick" />
          <action-button type="split" :title="$t('Split')" :disabled="loadingAction || loading || !isSplit" @click="onSplitClick" />
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
      <modal-result v-model="insuranceModal" :stay="true">
        <div class="insurance-form">
          <h6>
            {{ $t('Insurance') }}?
          </h6>
          <div>
            <action-button type="no" :title="$t('No')" @click="onInsuranceClick(false)" />
            <action-button type="yes" :title="$t('Yes')" @click="onInsuranceClick(true)" />
          </div>
        </div>
      </modal-result>
    </template>
    <hand ref="dealerHand" :cards="dealerCards" :cards-count="3" :position-x="0" :position-y="-0.5" :result="dealerResult" :demo="dealerDemo" :hint-view="dealerView ? [1] : []" />
    <hand ref="playerHand" :cards="playerCards" :cards-count="3" :position-x="isSplitted ? -0.5 : 0" :position-y="0.5" :result="playerResult" :demo="playerDemo" :opacity="playerHandActive == 2 ? 0.5 : 1" :fold="isSplitted" />
    <hand v-if="isSplitted" ref="playerHand2" :cards="playerCards2" :cards-count="3" :position-x="1" :position-y="0.5" :result="playerResult2" :demo="playerDemo2" :opacity="playerHandActive == 1 ? 0.5 : 1" :fold="true" />
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

import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import { sleep } from '~/plugins/utils'

import dealSound from 'packages/blackjack/resources/audio/deal.wav'
import winSound from 'packages/blackjack/resources/audio/win.wav'
import loseSound from 'packages/blackjack/resources/audio/lose.wav'
import pushSound from 'packages/blackjack/resources/audio/push.wav'

export default {
  name: 'Blackjack',

  components: { CardTable, ActionButton, Hand, Bet, ModalResult, Paytable, Info },

  mixins: [GameMixin, SoundMixin],

  data () {
    return {
      ready: true,

      playing: false,
      loading: false,
      loadingAction: false,

      game: null,
      insuranceModal: false,
      dealerView: false,
      isInsurance: false,
      isSplitted: false,
      gameBet: 0,
      gameBetOld: 0,
      playerHandActive: 0,
      playerCards: [],
      playerCards2: [],
      dealerCards: [],
      playerDemo: [],
      playerDemo2: [],
      dealerDemo: [],
      playerResult: '',
      playerResult2: '',
      dealerResult: '',
      resultModal: false,
      result: '',
      win: 0
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    betMode () {
      if (!this.playing && !this.loading) {
        return 'ante'
      } else {
        return null
      }
    },
    isRebetActive () {
      return this.account.balance >= this.gameBetOld && this.gameBetOld > 0
    },
    isDouble () {
      return this.playing && !this.loading && !this.loadingAction && this.game && this.game.gameable.actions.indexOf('double') !== -1
    },
    isStand () {
      return this.playing && !this.loading && !this.loadingAction && this.game && this.game.gameable.actions.indexOf('stand') !== -1
    },
    isHit () {
      return this.playing && !this.loading && !this.loadingAction && this.game && this.game.gameable.actions.indexOf('hit') !== -1
    },
    isSplit () {
      return this.playing && !this.loading && !this.loadingAction && this.game && this.game.gameable.actions.indexOf('split') !== -1
    }
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    placeBet (bet) {
      if (!this.playing && !this.loading) {
        this.gameBet += bet
      }
    },
    onUnbet (bet) {
      if (!this.playing && !this.loading) {
        this.gameBet -= bet
      }
    },
    resetPlayers () {
      this.isSplitted = false
      this.playerHandActive = 1
      this.playerCards = []
      this.playerCards2 = []
      this.dealerCards = []
      this.playerDemo = []
      this.playerDemo2 = []
      this.dealerDemo = []
      this.playerResult = ''
      this.playerResult2 = ''
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
    async gameOver () {
      this.playing = false
      this.loading = true
      const resultDemo = async () => {
        const betWin = {}
        if (this.game.gameable.win < this.game.gameable.bet) {
          this.$refs.cardTable.betLoss('ante')
        } else {
          betWin.ante = this.game.gameable.win - this.game.gameable.bet
        }
        if (this.isSplitted) {
          if (this.game.gameable.win2 < this.game.gameable.bet2) {
            this.$refs.cardTable.betLoss('ante2')
          } else {
            betWin.ante2 = this.game.gameable.win2 - this.game.gameable.bet2
          }
        }
        if (Object.keys(betWin).length > 0) {
          await this.$refs.cardTable.demoWin(betWin)
        }
      }
      if (this.game.gameable.insurance_win > 0) {
        this.sound(pushSound)
        await resultDemo()
        await sleep(1000)
        this.modalPush()
      } else if (this.game.win > this.game.bet) {
        this.sound(winSound)
        await resultDemo()
        await sleep(1000)
        this.modalWin(this.game.win)
      } else if (this.game.win === this.game.bet) {
        this.sound(loseSound)
        await resultDemo()
        await sleep(1000)
        this.modalPush()
      } else {
        this.sound(pushSound)
        await resultDemo()
        await sleep(1000)
        this.modalLose()
      }
      this.setProvablyFairGame({ key: this.gamePackageId, game: this.game.pf_game })
      this.updateUserAccountBalance(this.game.account.balance)
      this.game = null
      this.gameBetOld = this.gameBet
      this.gameBet = 0
      this.loading = false
      await sleep(3000)
      if (!this.playing && !this.loading) this.resetPlayers()
    },
    fillP1Result (isResult) {
      this.playerResult = this.game.gameable.player_blackjack
        ? this.$t('Blackjack')
        : (this.game.gameable.player_score > 21
            ? this.$t('Busted')
            : this.game.gameable.player_score.toString())
    },
    fillP2Result (isResult) {
      this.playerResult2 = this.game.gameable.player_score2 > 21
        ? this.$t('Busted')
        : this.game.gameable.player_score2?.toString() ?? ''
    },
    fillDResult () {
      this.dealerResult = this.game.gameable.dealer_blackjack
        ? this.$t('Blackjack')
        : (this.game.gameable.dealer_score > 21
          ? this.$t('Busted')
          : this.game.gameable.dealer_score?.toString() ?? '')
    },
    async fillCards () {
      this.playerResult = ''
      this.playerResult2 = ''
      this.dealerResult = ''
      if (this.game.gameable.player_hand && this.playerHandActive === 1) {
        if (this.playerCards.length !== this.game.gameable.player_hand.length) {
          for (let i = this.playerCards.length; i < this.game.gameable.player_hand.length; i++) {
            this.playerCards.push(this.game.gameable.player_hand[i])
            await sleep(400)
          }
          await sleep(800)
        }
      }
      this.fillP1Result()
      if (this.isSplitted && this.playerHandActive === 1 && this.game.gameable.player_hand2.length === 2) {
        this.playerHandActive = 2
        await sleep(400)
        await this.$nextTick()
      }
      if (this.game.gameable.player_hand2 && this.playerHandActive === 2) {
        if (this.playerCards2.length !== this.game.gameable.player_hand2.length) {
          for (let i = this.playerCards2.length; i < this.game.gameable.player_hand2.length; i++) {
            this.playerCards2.push(this.game.gameable.player_hand2[i])
            await sleep(400)
          }
          await sleep(800)
        }
      }
      this.fillP2Result()
      if (this.game.is_completed) {
        await sleep(400)
        await this.$nextTick()
      }
      if (this.game.gameable.dealer_hand) {
        if (this.dealerCards.length === 2 && this.dealerCards[1] == null && this.game.gameable.dealer_hand.length >= 2 && this.game.gameable.dealer_hand[1] != null) {
          this.$set(this.dealerCards, 1, this.game.gameable.dealer_hand[1])
          await sleep(400)
          await this.$nextTick()
        }
        for (let i = this.dealerCards.length; i < this.game.gameable.dealer_hand.length; i++) {
          this.dealerCards.push(this.game.gameable.dealer_hand[i])
          await sleep(400)
          await this.$nextTick()
        }
        await sleep(1200)
      }
      if (this.game.is_completed) {
        this.fillP1Result(true)
        this.fillP2Result(true)
        this.fillDResult()
      }
    },
    async onRebetClick () {
      this.$refs.cardTable.placeBet(this.gameBetOld)
      this.resetPlayers()
      this.loading = true
      await sleep(1000)
      this.onDealClick()
    },
    onNewbetClick () {
      this.gameBet = 0
      this.$refs.cardTable.reset()
      this.resetPlayers()
    },
    async onDealClick () {
      this.loading = true
      this.playing = true
      this.gameBetOld = this.gameBet
      this.resetPlayers()
      this.updateUserAccountBalance(this.account.balance - this.gameBet)
      this.sound(dealSound)
      await sleep(500)
      // API request params
      const { data: game } = await axios.post(this.getRoute('play'), { hash: this.provablyFairGame.hash, bet: this.gameBet })
      this.game = game
      this.playerCards.push(this.game.gameable.player_hand[0])
      await sleep(400)
      this.dealerCards.push(this.game.gameable.dealer_hand_draw[0])
      await sleep(400)
      this.playerCards.push(this.game.gameable.player_hand[1])
      await sleep(400)
      this.dealerCards.push(this.game.gameable.dealer_hand_draw[1])
      await sleep(400)
      if (this.game.gameable.player_score === 21 && typeof this.game.gameable.player_result === 'string' && this.game.gameable.player_result.length > 0) {
        this.playerResult = this.game.gameable.player_result
      } else {
        this.playerResult = this.game.gameable.player_score?.toString() ?? ''
      }
      if (game.gameable.dealer_check && game.gameable.actions.indexOf('insurance') === -1) {
        await sleep(400)
        this.dealerView = true
        await sleep(1500)
        this.dealerView = false
        await sleep(500)
      }
      await this.fillCards()
      if (game.is_completed) {
        this.gameOver()
      } else {
        if (game.gameable.actions.indexOf('insurance') > -1) {
          this.insuranceModal = true
        } else {
          this.loading = false
        }
      }
    },
    async onInsuranceClick (isInsurance) {
      this.insuranceModal = false
      if (isInsurance) {
        this.isInsurance = true
        await this.$nextTick()
        this.updateUserAccountBalance(this.account.balance - this.gameBet / 2)
        this.$refs.cardTable.placeBet(this.gameBet / 2, null, 'insurance')
        await sleep(800)
        if (this.game.gameable.player_score !== 21) {
          this.dealerView = true
          await sleep(1500)
        }
        const { data: game } = await axios.post(this.getRoute('insurance'), { hash: this.provablyFairGame.hash, insurance: true })
        this.game = game
        this.dealerView = false
        await sleep(500)
        if (game.is_completed) {
          await this.fillCards()
          if (game.gameable.insurance_win > 0) {
            this.$refs.cardTable.placeBet(game.gameable.insurance_win - this.gameBet / 2, null, 'insurance', 'dealer')
            await sleep(1000)
            this.$refs.cardTable.betWin('insurance')
            await sleep(1000)
          } else {
            await this.$refs.cardTable.betLoss('insurance')
          }
          this.gameOver()
        } else {
          await this.$refs.cardTable.betLoss('insurance')
          await sleep(500)
          this.loading = false
        }
        await sleep(500)
        this.isInsurance = false
      } else {
        if (this.game.gameable.player_score !== 21) {
          this.dealerView = true
          await sleep(1500)
        }
        const { data: game } = await axios.post(this.getRoute('insurance'), { hash: this.provablyFairGame.hash, insurance: false })
        this.game = game
        this.dealerView = false
        await sleep(500)
        await this.fillCards()
        if (game.is_completed) {
          this.gameOver()
        } else {
          this.loading = false
        }
      }
    },
    async onDoubleClick () {
      this.loadingAction = true
      this.updateUserAccountBalance(this.account.balance - this.gameBet)
      this.$refs.cardTable.placeBet(this.gameBet, null, 'ante')
      const { data: game } = await axios.post(this.getRoute('double'), { hash: this.provablyFairGame.hash })
      this.game = game
      await this.fillCards()
      if (game.is_completed) this.gameOver()
      this.loadingAction = false
    },
    async onStandClick () {
      this.loadingAction = true
      const params = { hash: this.provablyFairGame.hash }
      if (this.isSplitted) params.hand = this.playerHandActive
      const { data: game } = await axios.post(this.getRoute(`${this.isSplitted ? 'split-' : ''}stand`), params)
      this.game = game
      await this.fillCards()
      if (game.is_completed) {
        await this.gameOver()
      } else if (this.isSplitted && this.playerHandActive === 1) {
        this.playerHandActive = 2
      }
      this.loadingAction = false
    },
    async onHitClick () {
      this.loadingAction = true
      const params = { hash: this.provablyFairGame.hash }
      if (this.isSplitted) params.hand = this.playerHandActive
      const { data: game } = await axios.post(this.getRoute(`${this.isSplitted ? 'split-' : ''}hit`), params)
      this.game = game
      await this.fillCards()
      if (game.is_completed) await this.gameOver()
      this.loadingAction = false
    },
    async onSplitClick () {
      this.loadingAction = true
      this.playerResult = ''
      const pos = Object.assign({}, this.$refs.playerHand.getCardPosition(1))
      this.isSplitted = true
      await this.$nextTick()
      this.updateUserAccountBalance(this.account.balance - this.gameBet)
      this.$refs.cardTable.placeBet(this.gameBet, null, 'ante2')
      await this.$nextTick()
      this.$refs.playerHand.disableCardAnimation(1)
      this.$refs.playerHand2.setAnimationsSource(0, pos)
      const card = this.playerCards.pop()
      this.playerCards2.push(card)
      await sleep(400)
      const { data: game } = await axios.post(this.getRoute('split'), { hash: this.provablyFairGame.hash })
      this.game = game
      await this.fillCards()
      if (game.is_completed) await this.gameOver()
      this.loadingAction = false
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
  .insurance-form {
    div {
      display: flex;
      justify-content: center;
    }
    &::v-deep {
      .button {
        width: 120px;
        height: 130px;
        margin-bottom: 30px;
        box-sizing: border-box;
      }
      .sprite {
        width: 110px !important;
        height: 110px !important;
      }
      img {
        width: 140px;
        height: 140px;
      }
    }
  }
</style>

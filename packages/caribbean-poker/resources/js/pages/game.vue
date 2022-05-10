<template>
  <card-table
    ref="cardTable"
    class="fill-height"
    :actions="['deal', 'call', 'fold', 'rebet', 'newbet']"
    :bet-mode="!playing && !loading ? 'ante' : null"
    @bet="placeBet"
    @unbet="onUnbet"
    @click="resultModal = false"
  >
    <template #bet-panel="{ chips, unbet, dealer, animated }">
      <bet
        :chips="chips.ante"
        :label="$t('Ante')"
        :blink="!playing && !loading && !gameBet.length"
        :dealer="dealer"
        @unbet="() => !playing && !loading ? unbet() : null"
        @animated="e => animated('ante')"
      />
      <bet
        :chips="chips.call"
        :label="$t('Call')"
        :size="0.95"
        :font-size="0.7"
        value-right
        :position="1"
        :dealer="dealer"
        :blink="playing && !loading"
        @animated="e => animated('call')"
      />
    </template>
    <template #control-panel>
      <transition name="slide-down">
        <div v-if="!playing && !loading" class="btns-panel">
          <action-button type="deal" :title="$t('Deal')" :disabled="loading || gameBet.reduce((a, v) => a + v, 0) < config.min_bet" @click="onDealClick" />
          <action-button type="rebet" :title="$t('Rebet')" :disabled="!isRebetActive || loading || gameBet.length !== 0 || gameBetOld.length === 0" @click="onRebetClick" />
          <action-button type="newbet" :title="$t('New bet')" :disabled="loading || gameBet.length === 0" @click="onNewbetClick" />
        </div>
      </transition>
      <transition name="slide-down">
        <div v-if="playing && !loading" class="btns-panel">
          <action-button type="fold" :title="$t('Fold')" :disabled="loading" @click="onFoldClick" />
          <action-button type="call" :title="$t('Call')" :disabled="loading || !isCallActive" @click="onCallClick" />
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
    <hand :cards="dealerCards" :cards-count="5" :position-x="0" :position-y="-0.5" :result="dealerResult" :demo="dealerDemo" />
    <hand :cards="playerCards" :cards-count="5" :position-x="0" :position-y="0.5" :result="playerResult" :demo="playerDemo" />
  </card-table>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import Bet from '~/components/Games/CardGame/Bet'
import { sleep } from '~/plugins/utils'
import swooshSound from 'packages/caribbean-poker/resources/audio/swoosh.wav'
import winSound from 'packages/caribbean-poker/resources/audio/win.wav'
import loseSound from 'packages/caribbean-poker/resources/audio/lose.wav'
import pushSound from 'packages/caribbean-poker/resources/audio/push.wav'
import CardTable from '~/components/Games/CardGame/Table'
import ActionButton from '~/components/Games/CardGame/Button'
import Hand from '~/components/Games/CardGame/Hand'
import ModalResult from '~/components/Games/CardGame/ModalResult'
import Paytable from './paytable'
import Info from './info'
export default {
  name: 'CaribbeanPoker',

  components: { CardTable, Bet, ActionButton, Hand, Paytable, Info, ModalResult },

  mixins: [GameMixin, SoundMixin],

  data () {
    return {
      ready: true,

      loading: false,
      playing: false,

      bet: 0,
      anteBet: 0,
      gameBet: [],
      gameBetOld: [],
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
    isCallActive () {
      return this.account.balance >= this.anteBet * 2
    },
    isRebetActive () {
      return this.account.balance >= this.gameBetOld.reduce((acc, item) => acc + item, 0)
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
    placeBet (bet) {
      if (!this.playing && !this.loading) this.gameBet.push(bet)
    },
    onUnbet (bet) {
      if (!this.playing && !this.loading) this.gameBet.pop()
    },
    resetPlayers () {
      this.playerCards = []
      this.dealerCards = []
      this.playerDemo = []
      this.dealerDemo = []
      this.playerResult = ''
      this.dealerResult = ''
    },
    onDealClick () {
      const bet = this.gameBet.reduce((acc, item) => acc + item, 0)
      this.gameBetOld = this.gameBet
      this.gameBet = []
      this.anteBet = bet
      this.action('play', { bet })
    },
    async onRebetClick () {
      this.$refs.cardTable.placeBet(this.gameBetOld.reduce((acc, item) => acc + item, 0))
      this.resetPlayers()
      await sleep(1000)
      this.onDealClick()
    },
    onNewbetClick () {
      this.gameBet = []
      this.$refs.cardTable.reset()
      this.resetPlayers()
    },
    onCallClick () {
      this.action('call')
    },
    onFoldClick () {
      this.action('fold')
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
    // handle game actions (deal, hit, stand etc)
    async action (name, params = {}) {
      this.loading = true
      // update user balance
      if (name === 'call') {
        this.$refs.cardTable.placeBet(this.anteBet * 2, null, 'call')
        this.updateUserAccountBalance(this.account.balance - this.anteBet * 2)
        await sleep(500)
      }

      // clear previous game results
      if (name === 'play') {
        this.resetPlayers()
        this.updateUserAccountBalance(this.account.balance - this.anteBet)
        this.sound(swooshSound)
        await sleep(500)
      }

      // API request params
      const endpoint = this.getRoute(name)
      const requestParams = { hash: this.provablyFairGame.hash, ...params }

      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)

      let animationDelay = 0

      // initial draw
      if (!game.is_completed) {
        this.bet = game.bet

        this.playerCards = game.gameable.player_cards
        await sleep(350 * 3 + 400 * 2)
        this.dealerCards = game.gameable.dealer_cards.concat([null, null, null, null])
        await sleep(350 * 4 + 400 * 2)

        // display player hand rank
        // setTimeout(() => {
        this.playerResult = game.gameable.player_hand_title
        this.playerDemo = game.gameable.player_combination_cards
        setTimeout(() => { this.playerDemo = [] }, 2000)
        this.playing = true
        this.loading = false
        // }, animationDelay += 250)
      // game completed
      } else {
        this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })

        if (game.gameable.call_bet) {
          // show dealer cards
          game.gameable.dealer_cards.slice(1).forEach((card, i) => {
            setTimeout(() => {
              this.$set(this.dealerCards, i + 1, card)
            }, animationDelay += 500)
          })
        }

        // display dealer score and result for each hand
        setTimeout(async () => {
          if (!this.ready) return
          // player made a call bet
          if (game.gameable.call_bet) {
            this.dealerResult = game.gameable.dealer_hand_title
            // dealer qualified
            if (game.gameable.dealer_qualified) {
              this.dealerDemo = game.gameable.dealer_combination_cards
              setTimeout(async () => {
                this.dealerDemo = []
                if (game.gameable.call_win > game.gameable.call_bet) {
                  this.sound(winSound)
                  await this.$refs.cardTable.demoWin({ ante: Math.max(0, game.gameable.ante_win - this.anteBet), call: Math.max(0, game.gameable.call_win - this.anteBet * 2) })
                  this.modalWin(game.win)
                } else if (game.gameable.call_win === game.gameable.call_bet) {
                  this.sound(pushSound)
                  await this.$refs.cardTable.demoPush()
                  this.modalPush()
                } else {
                  this.sound(loseSound)
                  await this.$refs.cardTable.demoLose()
                  this.modalLose()
                }
                this.loading = false
                this.playing = false
                await sleep(3000)
                if (!this.playing && !this.loading) this.resetPlayers()
              }, 2000)
            // dealer didn't qualify
            } else {
              this.sound(winSound)
              await this.$refs.cardTable.demoWin({ ante: Math.max(0, game.gameable.ante_win - this.anteBet), call: Math.max(0, game.gameable.call_win - this.anteBet * 2) })
              this.modalWin(game.win)
              this.loading = false
              this.playing = false
              await sleep(3000)
              if (!this.playing && !this.loading) this.resetPlayers()
            }
          // fold
          } else {
            this.sound(loseSound)
            await this.$refs.cardTable.demoLose()
            this.modalLose()
            this.loading = false
            this.playing = false
            await sleep(3000)
            if (!this.playing && !this.loading) this.resetPlayers()
          }
          this.playing = false
          // update balance
          this.updateUserAccountBalance(game.account.balance)
        }, animationDelay += 500)
      }
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

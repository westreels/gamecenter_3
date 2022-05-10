<template>
  <div v-if="deckIsLoaded" class="d-flex flex-column fill-height">
    <game-room
      :playing="playing"
      @room="onRoomChange($event.room)"
      @game="onGame($event.game)"
      @event="onEvent($event.event)"
      @players="onPlayers($event.players)"
      @player-joined="onPlayerJoined($event.player)"
      @player-left="onPlayerLeft($event.player)"
      @ready="ready = $event.ready"
      @exit="onExit"
    />
    <template v-if="room">
      <div id="opponent-hands" class="d-flex justify-space-around mt-2">
        <hand
          v-for="(opponent, i) in opponents"
          :key="i"
          :cards="opponent.cards"
          :score="opponent.score"
          :result="opponent.score > 0 && !playing ? resultMessage(opponent) : opponent.result"
          :result-class="resultClass(opponent)"
          :bet="opponent.bet"
          :win="opponent.win"
        >
          <template v-slot:title>
            <div class="font-weight-thin text-center mb-2 ml-n10 ml-lg-0">
              {{ opponent.name }}
              <v-progress-circular
                v-show="isOpponentTurn(opponent)"
                :rotate="360"
                :size="25"
                :width="2"
                :value="isOpponentTurn(opponent) ? Math.round(100 * (opponent.action_end - time) / actionDuration) : 0"
                color="primary"
              >
                {{ opponent.action_end - time }}
              </v-progress-circular>
            </div>
          </template>
        </hand>
      </div>
      <div class="d-flex justify-center fill-height align-center">
        <hand
          v-if="player.cards"
          :cards="player.cards"
          :score="player.score"
          :result="player.score > 0 && !playing ? resultMessage(player) : player.result"
          :result-class="resultClass(player)"
          :bet="player.bet"
          :win="player.win"
        />
      </div>
      <div class="d-flex justify-center align-center flex-wrap">
        <v-btn
          :disabled="isActionDisabled('stand')"
          :loading="action === 'stand'"
          class="mx-1 my-2 my-lg-0 action-button"
          small
          @click="doAction('stand')"
        >
          {{ $t('Stand') }}
        </v-btn>
        <v-progress-circular
          :rotate="360"
          :size="40"
          :width="3"
          :value="isPlayerTurn ? Math.round(100 * (player.action_end - time) / actionDuration) : 0"
          :color="isPlayerTurn? (time < player.action_end - finalHitThreshold ? 'primary' : 'error') : 'secondary'"
          class="mx-2"
        >
          {{ isPlayerTurn ? player.action_end - time : 0 }}
        </v-progress-circular>
        <v-btn
          :disabled="isActionDisabled('hit')"
          :loading="action === 'hit'"
          class="mx-1 my-2 my-lg-0 action-button"
          small
          @click="doAction('hit')"
        >
          {{ $t('Hit') }}
        </v-btn>
      </div>
      <div class="d-flex justify-center my-2">
        <v-btn
          color="primary"
          :loading="action === 'play'"
          :disabled="!ready || !balanceIsSufficient || playing"
          @click="doAction('play')"
        >
          {{ $t('Play') }}
        </v-btn>
        <v-btn
          v-if="cancelAllowed"
          color="red"
          :loading="action === 'cancel'"
          :disabled="!!action"
          class="ml-3"
          @click="doAction('cancel')"
        >
          {{ $t('Cancel') }}
        </v-btn>
      </div>
    </template>
  </div>
  <div v-else class="d-flex fill-height justify-center align-center">
    <block-preloader />
  </div>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import Hand from '~/components/Games/BasicCardGame/Hand'
import { config } from '~/plugins/config'
import { get, time } from '~/plugins/utils'
import clickSound from '~/../audio/common/click.wav'
import dealSound from 'packages/multiplayer-blackjack/resources/audio/deal.wav'
import swooshSound from 'packages/multiplayer-blackjack/resources/audio/swoosh.wav'
import flipSound from 'packages/multiplayer-blackjack/resources/audio/flip.wav'
import winSound from 'packages/multiplayer-blackjack/resources/audio/win.wav'
import loseSound from 'packages/multiplayer-blackjack/resources/audio/lose.wav'
import pushSound from 'packages/multiplayer-blackjack/resources/audio/push.wav'
import GameRoom from '~/components/Games/GameRoom'
import BlockPreloader from '~/components/BlockPreloader'

export default {
  name: 'MultiplayerBlackjack',

  components: { BlockPreloader, GameRoom, Hand },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      ready: false,
      room: null,
      game: null,
      action: null,
      loading: false,
      playing: false,
      deckIsLoaded: false,
      defaultHand: {
        name: '',
        cards: [],
        score: -1,
        result: '',
        bet: 0,
        win: 0
      },
      player: {},
      opponents: {},
      time: null,
      intervalId: null,
      serverTimeDiff: 0
    }
  },

  computed: {
    ...mapState('auth', ['account', 'user']),
    actionDuration () {
      return parseInt(config('multiplayer-blackjack.action_duration'), 10)
    },
    finalHitThreshold () {
      return parseInt(config('multiplayer-blackjack.final_hit_threshold'), 10)
    },
    cancelThreshold () {
      return parseInt(config('multiplayer-blackjack.cancel_threshold'), 10)
    },
    isPlayerTurn () {
      return this.time && this.player.action_start && this.player.action_end && this.player.action_start <= this.time && this.time < this.player.action_end
    },
    bet () {
      return this.room ? this.room.parameters.bet : 0
    },
    playersCount () {
      return this.room ? parseInt(this.room.parameters.players_count, 10) : 0
    },
    balanceIsSufficient () {
      return this.account.balance >= this.bet
    },
    winnersCount () {
      return [this.player.win || 0, ...Object.keys(this.opponents).map(id => this.opponents[id].win)].filter(win => win > 0).length
    },
    cancelAllowed () {
      return this.playing
        && this.opponents
        && this.time
        && this.game
        && this.game.created < this.time - this.cancelThreshold
        && Object.keys(this.opponents).filter(id => this.opponents[id].cards[0] !== null).length < this.playersCount - 1
    }
  },

  watch: {
    time (time, prevTime) {
      if (this.playing && !this.action && time === this.player.action_end && prevTime === this.player.action_end - 1) {
        this.doAction('stand')
      }
    },
    playing (playing, wasPlaying) {
      // clear action time interval when the game is completed
      if (wasPlaying && !playing) {
        this.clearActionTimeInterval()
      }
    }
  },

  async created () {
    await this.loadCardDeck()
    this.deckIsLoaded = true
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance'
    }),
    isOpponentTurn (opponent) {
      return this.time && opponent.action_start && opponent.action_end && opponent.action_start <= this.time && this.time <= opponent.action_end
    },
    updatePlayerHand (player, values) {
      Object.keys(values).forEach(key => {
        player[key] = values[key]
      })
    },
    // handle game actions (play, hit, stand)
    async doAction (action) {
      this.action = action

      // clear previous game results
      if (action === 'play') {
        this.sound(swooshSound)
        this.playing = true
        this.updateUserAccountBalance(this.account.balance - this.bet)

        // assign default hand values (remove existing cards)
        this.player = { ...this.defaultHand }

        for (const userId in this.opponents) {
          if (this.opponents[userId].score > 0) {
            this.opponents[userId] = { ...this.defaultHand, name: this.opponents[userId].name }
            setTimeout(() => this.updatePlayerHand(this.opponents[userId], { cards: [null, null] }), 200)
          }
        }
      } else {
        this.sound(clickSound)
      }

      // execute the action
      const { data: game } = await axios.post(this.getRoute(action).replace('{room}', `${this.room.id}`))

      this.game = game

      // deal sound
      if (action === 'play' || action === 'hit') {
        this.sound(dealSound)
      }

      if (action !== 'cancel') {
        // update player hand
        this.player = { ...this.player, ...game.gameable.player_hand }
      } else {
        // assign default hand values (remove existing cards)
        this.updatePlayerHand(this.player, { ...this.defaultHand, cards: [null, null], result: this.$t('Cancelled') })
        this.updateUserAccountBalance(this.account.balance + game.gameable.player_hand.win)
      }

      // enable action buttons
      this.action = null

      // enable action time interval
      this.enableActionTimeInterval(game.server_time)
    },
    isActionDisabled (action) {
      return !!this.action
        || (action === 'hit' && this.player.score >= 21)
        || !this.isPlayerTurn
        || Object.keys(this.opponents).length < this.playersCount - 1
    },
    enableActionTimeInterval (time) {
      if (!this.intervalId) {
        this.time = Math.floor(time / 1000)

        this.intervalId = setInterval(() => {
          this.time++
        }, 1000)
      }
    },
    clearActionTimeInterval () {
      if (this.intervalId) {
        clearInterval(this.intervalId)
        this.intervalId = null
        this.time = null
      }
    },
    resultClass (player) {
      return player.score > 0 ? (player.win > 0 ? (this.winnersCount === 1 ? 'primary text--primary' : 'warning') : 'error') : 'secondary'
    },
    resultMessage (player) {
      return player.win > 0
        ? (player.score === 21 && player.cards.length === 2
          ? this.$t('Blackjack')
          : (this.winnersCount === 1
            ? this.$t('Win')
            : this.$t('Push')))
        : this.$t('Lose')
    },
    onRoomChange (room) {
      this.room = room
    },
    onPlayers (players) {
      // loop through player hands
      players.forEach(player => {
        // if the hand belongs to the current user
        if (this.user.id === player.id) {
          // set the player hand to default values
          this.player = { ...this.defaultHand }
          // update player hand to display cards
          setTimeout(() => {
            this.updatePlayerHand(
              this.player,
              this.game
                ? { ...get(this.game, 'gameable.player_hand') }
                : { cards: [null, null], result: players.length === this.playersCount ? this.$t('Click Play') : this.$t('Waiting') }
            )

            if (this.playing) {
              this.enableActionTimeInterval(Date.now() + this.serverTimeDiff)
            }
          }, 100)
        // if the hand DOES NOT belong to the current user
        } else {
          // set the opponent hand to default values
          this.$set(this.opponents, player.id, { ...this.defaultHand, name: player.name })
          // update opponent hand to display values
          setTimeout(() => this.updatePlayerHand(this.opponents[player.id],
            get(this.game, 'gameable.opponent_hands')
              ? { ...get(this.game, 'gameable.opponent_hands.' + player.id) }
              : { cards: [null, null] }
          ), 100)
        }
      })
    },
    onPlayerJoined (player) {
      // if this player didn't join earlier
      if (!this.opponents[player.id]) {
        this.$set(this.opponents, player.id, { ...this.defaultHand, name: player.name })
        setTimeout(() => this.updatePlayerHand(this.opponents[player.id], { cards: [null, null] }), 100)

        // update player message when all players joined
        if (Object.keys(this.opponents).length + 1 === this.playersCount) {
          this.updatePlayerHand(this.player, { result: this.$t('Click Play') })
        }
      // if this player already exists (probably left and joined again)
      } else {
        this.updatePlayerHand(this.opponents[player.id], { result: '' })
      }
    },
    onPlayerLeft (player) {
      // add a message when a player leaves the room (it also happens when the page is refreshed)
      this.updatePlayerHand(this.opponents[player.id], { result: this.$t('Left') })
    },
    // called when the page is refreshed
    onGame (game) {
      this.game = game
      this.serverTimeDiff = game.server_time - Date.now()

      // get time when the game should finish
      const scheduledCompletionTime = get(game, 'gameable.player_hand') && get(game, 'gameable.opponent_hands')
        ? Math.max(
          game.gameable.player_hand.action_end || 0,
          ...Object.keys(game.gameable.opponent_hands).map(id => game.gameable.opponent_hands[id].action_end)
        )
        : null

      // complete the game explicitly if this time is in the past
      if (scheduledCompletionTime && time() > scheduledCompletionTime) {
        this.doAction('complete')
      }

      if (!game.is_completed) {
        this.playing = true
      }
    },
    onEvent (event) {
      if (event.action !== 'cancel') {
        // loop through player hands
        Object.keys(event.gameable.hands).forEach(userId => {
          // if the hand belongs to the current user
          if (this.user.id === parseInt(userId, 10)) {
            this.updatePlayerHand(this.player, {
              action_start: event.gameable.hands[userId].action_start,
              action_end: event.gameable.hands[userId].action_end,
              win: event.gameable.hands[userId].win
            })
          // if the hand DOES NOT belong to the current user
          } else if (typeof this.opponents[userId] !== 'undefined') {
            // deal sound
            if (event.gameable.hands[userId].cards.length > this.opponents[userId].cards.length) {
              this.sound(dealSound)
            } else if (event.game.is_completed) {
              this.sound(flipSound)
            }
            // set the opponent hand
            this.opponents[userId] = { ...this.opponents[userId], ...event.gameable.hands[userId] }
          }
        })
      }

      // if game is completed
      if (event.game.is_completed) {
        this.playing = false

        // push
        if (this.winnersCount > 1) {
          this.sound(pushSound)
          this.updateUserAccountBalance(this.account.balance + this.player.win)
        // player wins
        } else if (this.player.win > 0) {
          this.sound(winSound)
          this.updateUserAccountBalance(this.account.balance + this.player.win)
        // player loses
        } else {
          this.sound(loseSound)
        }
      // if game is cancelled
      } else if (event.game.is_cancelled) {
        this.playing = false
      } else {
        this.enableActionTimeInterval(event.game.server_time)
      }
    },
    onExit () {
      this.game = null
      this.player = {}
      this.opponents = {}
      this.playing = false
      this.clearActionTimeInterval()
    }
  }
}
</script>

<style lang="scss" scoped>
  #opponent-hands {
    min-height: 160px;
  }

  .action-button {
    width: 80px;
  }
</style>

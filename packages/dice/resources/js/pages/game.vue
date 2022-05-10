<template>
  <div class="d-flex flex-column fill-height py-3">
    <div class="message-container d-flex justify-center align-center">
      <game-message :message="message" class="ma-0" />
    </div>
    <div class="d-flex justify-center fill-height align-center">
      <v-row justify="center">
        <v-col cols="3" md="2" lg="1" class="text-center">
          <v-card :elevation="3" :loading="playing">
            <animated-number :number="parseInt(rollDigits[0], 10)" :animating="playing" :class="animatedNumberClass" />
          </v-card>
        </v-col>
        <v-col cols="3" md="2" lg="1" class="text-center">
          <v-card :elevation="3" :loading="playing">
            <animated-number :number="parseInt(rollDigits[1], 10)" :animating="playing" :class="animatedNumberClass" />
          </v-card>
        </v-col>
        <v-col cols="3" md="2" lg="1" class="text-center">
          <v-card :elevation="3" :loading="playing">
            <animated-number :number="parseInt(rollDigits[2], 10)" :animating="playing" :class="animatedNumberClass" />
          </v-card>
        </v-col>
        <v-col cols="3" md="2" lg="1" class="text-center">
          <v-card :elevation="3" :loading="playing">
            <animated-number :number="parseInt(rollDigits[3], 10)" :animating="playing" :class="animatedNumberClass" />
          </v-card>
        </v-col>
      </v-row>
    </div>
    <div class="d-flex justify-center flex-column">
      <v-slider
        v-model="refNumber"
        thumb-label="always"
        :min="minRefNumber"
        :max="maxRefNumber"
        :step="1"
        :track-color="sliderTrackColor"
        :track-fill-color="sliderFillColor"
        class="px-5"
      >
        <template v-slot:prepend>
          <v-icon @click="decrementRefNumber">
            mdi-minus
          </v-icon>
        </template>
        <template v-slot:append>
          <v-icon @click="incrementRefNumber">
            mdi-plus
          </v-icon>
        </template>
      </v-slider>
      <div class="align-self-center">
        <v-form v-model="formIsValid" class="d-md-flex">
          <v-col cols="12" md="6" class="pa-0">
            <v-text-field
              v-model.number="winChance"
              dense
              outlined
              :full-width="false"
              class="text-center flex-grow-0 mr-0 mr-lg-2"
              :label="$t('Win chance')"
              :disabled="playing"
              :rules="[v => validateWinChance(v)]"
            >
              <template v-slot:prepend-inner>
                <v-btn small text icon color="primary" @click="winChance = minWinChance">
                  <v-icon small>mdi-arrow-down</v-icon>
                </v-btn>
                <v-btn small text icon color="primary" @click="decrementWinChance">
                  <v-icon small>mdi-minus</v-icon>
                </v-btn>
              </template>
              <template v-slot:append>
                <v-btn small text icon color="primary" @click="incrementWinChance">
                  <v-icon small>mdi-plus</v-icon>
                </v-btn>
                <v-btn small text icon color="primary" @click="winChance = maxWinChance">
                  <v-icon small>mdi-arrow-up</v-icon>
                </v-btn>
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="12" md="6" class="pa-0">
            <v-text-field
              v-model.number="payout"
              dense
              outlined
              :full-width="false"
              class="text-center flex-grow-0 ml-0 ml-lg-2"
              :label="$t('Payout')"
              :disabled="playing"
              :rules="[v => validatePayout(v)]"
            >
              <template v-slot:prepend-inner>
                <v-btn small text icon color="primary" @click="payout = minPayout">
                  <v-icon small>mdi-arrow-down</v-icon>
                </v-btn>
                <v-btn small text icon color="primary" @click="decrementPayout">
                  <v-icon small>mdi-minus</v-icon>
                </v-btn>
              </template>
              <template v-slot:append>
                <v-btn small text icon color="primary" @click="incrementPayout">
                  <v-icon small>mdi-plus</v-icon>
                </v-btn>
                <v-btn small text icon color="primary" @click="payout = maxPayout">
                  <v-icon small>mdi-arrow-up</v-icon>
                </v-btn>
              </template>
            </v-text-field>
          </v-col>
        </v-form>
      </div>
      <v-btn-toggle
        v-model="direction"
        active-class="primary--text"
        mandatory
        rounded
        group
        class="align-self-center mb-3"
      >
        <v-btn
          :disabled="playing"
          class="mx-1"
          small
          :value="-1"
          @click="sound(clickSound)"
        >
          {{ $t('Roll {0} - {1}', [minRoll, refNumber - 1]) }}
        </v-btn>
        <v-btn
          :disabled="playing"
          class="mx-1"
          small
          :value="1"
          @click="sound(clickSound)"
        >
          {{ $t('Roll {0} - {1}', [refNumber, maxRoll]) }}
        </v-btn>
      </v-btn-toggle>
    </div>
    <play-controls
      ref="controls"
      :loading="loading"
      :playing="playing"
      :disabled="!formIsValid"
      :auto-play-support="config.auto_play_enabled"
      :auto-play-interval="3"
      @play="play"
    />
  </div>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import { config } from '~/plugins/config'
import { decimal } from '~/plugins/format'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import { sleep, isNumeric, lpad, round } from '~/plugins/utils'
import clickSound from '~/../audio/common/click.wav'
import winSound from 'packages/dice/resources/audio/win.wav'
import loseSound from 'packages/dice/resources/audio/lose.wav'
import PlayControls from '~/components/Games/PlayControls'
import AnimatedNumber from '../components/AnimatedNumber'
import GameMessage from '~/components/Games/GameMessage'

export default {
  name: 'Dice',

  components: { GameMessage, PlayControls, AnimatedNumber },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      clickSound,
      formIsValid: true,
      loading: false,
      playing: false,
      message: null,
      refNumber: null,
      payout: null,
      winChance: null,
      direction: -1,
      win: 0,
      roll: 0,
      mutating: false
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    animatedNumberClass () {
      return [this.win > 0 ? (this.$vuetify.theme.isDark ? 'blur-dark' : 'blur-light') : '', 'digit']
    },
    rollDigits () {
      return lpad(this.roll, 0, 4)
    },
    minRoll () {
      return 0
    },
    maxRoll () {
      return 9999
    },
    rollsRangeLength () {
      return this.maxRoll - this.minRoll + 1
    },
    low () {
      return round(Math.max(this.minWinChance, this.refNumber))
    },
    high () {
      return 9999 - this.low
    },
    minWinChance () {
      return config('dice.min_win_chance')
    },
    maxWinChance () {
      return config('dice.max_win_chance')
    },
    minRefNumber () {
      return this.minWinChance * 100
    },
    maxRefNumber () {
      return this.maxWinChance * 100
    },
    minPayout () {
      return parseFloat(round((100 - this.houseEdge) / this.maxWinChance, 4))
    },
    maxPayout () {
      return parseFloat(round((100 - this.houseEdge) / this.minWinChance, 4))
    },
    houseEdge () {
      return config('dice.house_edge')
    },
    sliderTrackColor () {
      return this.direction === -1 ? 'grey' : 'primary'
    },
    sliderFillColor () {
      return this.direction === -1 ? 'primary' : 'grey'
    }
  },

  watch: {
    direction (direction) {
      this.mutate(() => {
        this.winChance = this.calcWinChance(direction, this.refNumber)
        this.payout = this.calcPayout(this.winChance)
      })
    },
    refNumber (refNumber) {
      this.mutate(() => {
        this.winChance = this.calcWinChance(this.direction, refNumber)
        this.payout = this.calcPayout(this.winChance)
      })
    },
    winChance (winChance) {
      this.mutate(() => {
        this.refNumber = this.calcRefNumber(this.direction, winChance)
        this.payout = this.calcPayout(winChance)
      })
    },
    payout (payout) {
      this.mutate(() => {
        this.winChance = this.calcWinChanceByPayout(payout)
        this.refNumber = this.calcRefNumber(this.direction, this.winChance)
      })
    }
  },

  created () {
    // trigger change
    this.refNumber = 5000
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    async play (bet) {
      this.message = null
      this.loading = true
      this.playing = true
      this.win = 0

      // update user balance
      this.updateUserAccountBalance(this.account.balance - bet)

      await sleep(500)

      // API request params
      const endpoint = this.getRoute('play')
      const requestParams = { hash: this.provablyFairGame.hash, bet, direction: this.direction, win_chance: this.winChance }

      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)

      this.loading = false

      // this.rotateDice(game.gameable.roll)
      this.roll = game.gameable.roll

      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })

      // display banker score and result for each hand
      setTimeout(() => {
        this.win = game.win

        this.playing = false

        // update balance
        this.updateUserAccountBalance(game.account.balance)

        // play sound
        if (game.win > 0) {
          this.sound(winSound)
          this.message = this.$t('You won')
        } else {
          this.sound(loseSound)
          this.message = this.$t('You lost')
        }

        this.$refs.controls.onGameCompleted(game)
      }, 500)
    },
    startMutation () {
      this.mutating = true
    },
    stopMutation () {
      this.$nextTick(() => {
        this.mutating = false
      })
    },
    // function that can safely change some watched properties and ensures watch functions not called repetitively
    mutate (f) {
      if (!this.mutating) {
        this.startMutation()
        f()
        this.stopMutation()
      }
    },
    validateWinChance (value) {
      return (isNumeric(value) && value >= this.minWinChance && value <= this.maxWinChance) || this.$t('min: {0}, max: {1}', [decimal(this.minWinChance), decimal(this.maxWinChance)])
    },
    validatePayout (value) {
      return (isNumeric(value) && value >= this.minPayout && value <= this.maxPayout) || this.$t('min: {0}, max: {1}', [decimal(this.minPayout, 4), decimal(this.maxPayout, 4)])
    },
    calcRefNumber (direction, winChance) {
        return direction === -1 ? winChance * 100 : (100 - winChance) * 100
    },
    calcWinChance (direction, refNumber) {
        return direction === -1
         ? round((refNumber - this.minRoll) / this.rollsRangeLength * 100, 2)
         : round((this.maxRoll - refNumber + 1) / this.rollsRangeLength * 100, 2)
    },
    calcWinChanceByPayout (payout) {
        return round((100 - this.houseEdge) / payout, 2)
    },
    calcPayout (winChance) {
      return round((100 - this.houseEdge) / winChance, 4)
    },
    decrementWinChance () {
      if (this.winChance - 1 >= this.minWinChance) {
        this.winChance -= 1
      } else {
        this.winChance = this.minWinChance
      }
    },
    incrementWinChance () {
      if (this.winChance + 1 <= this.maxWinChance) {
        this.winChance += 1
      } else {
        this.winChance = this.maxWinChance
      }
    },
    decrementRefNumber () {
      if (this.refNumber - 100 >= this.minRefNumber) {
        this.refNumber -= 100
      } else {
        this.refNumber = this.minRefNumber
      }
    },
    incrementRefNumber () {
      if (this.refNumber + 100 <= this.maxRefNumber) {
        this.refNumber += 100
      } else {
        this.refNumber = this.maxRefNumber
      }
    },
    decrementPayout () {
      if (this.payout - 1 >= this.minPayout) {
        this.payout = round(this.payout - 1, 4)
      } else {
        this.payout = this.minPayout
      }
    },
    incrementPayout () {
      if (this.payout + 1 <= this.maxPayout) {
        this.payout = round(this.payout + 1, 4)
      } else {
        this.payout = this.maxPayout
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.digit {
  font-family: monospace;
  font-size: 5em;
  line-height: 2em;
}

.blur-light {
  color: #000;
  text-transform: uppercase;
  animation: blur-light 1s ease-out 1s 3;
  /*text-shadow: 0px 0px 5px #9c9c9c, 0px 0px 5px #9c9c9c;*/
}

.blur-dark {
  color: #fff;
  text-transform: uppercase;
  animation: blur-dark 1s ease-out 1s 3;
  /*text-shadow: 0px 0px 5px #fff, 0px 0px 7px #fff;*/
}

.message-container {
  min-height: 45px;
}

@keyframes blur-light {
  from {
    text-shadow: 0px 0px 10px #9c9c9c,
    0px 0px 10px #9c9c9c,
    0px 0px 25px #9c9c9c,
    0px 0px 25px #9c9c9c,
    0px 0px 25px #9c9c9c,
    0px 0px 25px #9c9c9c,
    0px 0px 25px #9c9c9c,
    0px 0px 25px #9c9c9c
  }
}

@keyframes blur-dark {
  from {
    text-shadow: 0px 0px 10px #fff,
    0px 0px 10px #fff,
    0px 0px 25px #fff,
    0px 0px 25px #fff,
    0px 0px 25px #fff,
    0px 0px 25px #fff,
    0px 0px 25px #fff,
    0px 0px 25px #fff
  }
}
</style>

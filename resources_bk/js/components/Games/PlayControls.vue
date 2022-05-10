<template>
  <v-form ref="form" v-model="formIsValid" @submit.prevent="play">
    <div v-if="autoPlaySupport" class="d-flex justify-center">
      <v-btn-toggle
        v-model="autoPlayEnabled"
        active-class="primary--text"
        mandatory
        rounded
        group
        class="align-self-center mb-3"
      >
        <v-btn
          :disabled="playing || autoPlayInProgress"
          class="mx-1"
          small
          :value="false"
        >
          {{ $t('Manual') }}
        </v-btn>
        <v-btn
          :disabled="playing || autoPlayInProgress"
          class="mx-1"
          small
          :value="true"
        >
          {{ $t('Auto') }}
        </v-btn>
      </v-btn-toggle>
      <v-dialog
        v-model="autoPlaySettingsDialog"
        persistent
        max-width="600px"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            icon
            color="primary"
            :disabled="!autoPlayEnabled || playing || autoPlayInProgress"
            v-bind="attrs"
            v-on="on"
          >
            <v-icon>mdi-cog</v-icon>
          </v-btn>
        </template>
        <v-card>
          <v-card-title class="headline my-5">
            {{ $t('Auto play settings') }}
          </v-card-title>
          <v-card-text>
            <v-text-field
              v-model.number="autoPlay.onLoss.amount"
              dense
              outlined
              class="text-center"
              :label="$t('On loss')"
              :rules="[validationInteger]"
              :readonly="autoPlay.onLoss.reset"
              :suffix="$t('credits')"
            >
              <template v-slot:prepend-inner>
                <v-btn-toggle
                  v-model="autoPlay.onLoss.reset"
                  active-class="primary--text"
                  mandatory
                  rounded
                  group
                  class="align-self-center"
                >
                  <v-btn :value="true" small>
                    {{ $t('Reset to base') }}
                  </v-btn>
                  <v-btn :value="false" small>
                    {{ $t('Change by') }}
                  </v-btn>
                </v-btn-toggle>
              </template>
            </v-text-field>
            <v-text-field
              v-model.number="autoPlay.onWin.amount"
              dense
              outlined
              class="text-center"
              :label="$t('On win')"
              :rules="[validationInteger]"
              :readonly="autoPlay.onWin.reset"
              :suffix="$t('credits')"
            >
              <template v-slot:prepend-inner>
                <v-btn-toggle
                  v-model="autoPlay.onWin.reset"
                  active-class="primary--text"
                  mandatory
                  rounded
                  group
                  class="align-self-center"
                >
                  <v-btn :value="true" small>
                    {{ $t('Reset to base') }}
                  </v-btn>
                  <v-btn :value="false" small>
                    {{ $t('Change by') }}
                  </v-btn>
                </v-btn-toggle>
              </template>
            </v-text-field>
            <v-text-field
              v-model.number="autoPlay.maxBetsCount"
              dense
              outlined
              class="text-center"
              :label="$t('Number of games')"
              :rules="[validationNonNegativeInteger]"
            />
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="autoPlay.minBet"
                  dense
                  outlined
                  class="text-center"
                  :label="$t('Min bet')"
                  :suffix="$t('credits')"
                  :rules="[validationNonNegativeInteger]"
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="autoPlay.maxBet"
                  dense
                  outlined
                  class="text-center"
                  :label="$t('Max bet')"
                  :suffix="$t('credits')"
                  :rules="[validationNonNegativeInteger]"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="autoPlay.minBalance"
                  dense
                  outlined
                  :prefix="$t('less than')"
                  class="text-center"
                  :label="$t('Stop if balance is')"
                  :rules="[validationNonNegativeInteger]"
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="autoPlay.maxBalance"
                  dense
                  outlined
                  :prefix="$t('greater than')"
                  class="text-center"
                  :label="$t('Stop if balance is')"
                  :rules="[validationNonNegativeInteger]"
                />
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn
              color="primary"
              @click="autoPlaySettingsDialog = false"
            >
              {{ $t('Apply') }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-dialog
        v-model="autoPlayHistoryDialog"
        persistent
        max-width="600px"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            icon
            color="primary"
            :disabled="!autoPlayEnabled || playing || autoPlayInProgress"
            v-bind="attrs"
            v-on="on"
          >
            <v-icon>mdi-poll</v-icon>
          </v-btn>
        </template>
        <v-card>
          <v-card-title class="headline my-5">
            {{ $t('Auto play history') }}
          </v-card-title>
          <v-card-text>
            <v-simple-table v-if="autoPlay.games.length" dense fixed-header>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">
                      #
                    </th>
                    <th class="text-right">
                      {{ $t('Bet') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Win') }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(game, i) in autoPlay.games" :key="i">
                    <td class="text-left">
                      {{ ++i }}
                    </td>
                    <td class="text-right">
                      {{ game.bet }}
                    </td>
                    <td class="text-right">
                      {{ game.win }}
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="2" class="text-right">
                      {{ $t('Total net win') }}
                    </th>
                    <th class="text-right" :class="autoPlayNetResult > 0 ? 'green--text' : 'error--text'">
                      {{ autoPlayNetResult.toFixed(2) }}
                    </th>
                  </tr>
                </tfoot>
              </template>
            </v-simple-table>
            <p v-else>
              {{ $t('No games played in the auto mode.') }}
            </p>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn
              color="primary"
              @click="autoPlayHistoryDialog = false"
            >
              {{ $t('Close') }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
    <div class="d-flex justify-center flex-wrap mt-3">
      <slot name="before-bet-input" />
      <v-text-field
        v-model.number="bet"
        dense
        outlined
        :full-width="false"
        class="bet-input text-center"
        :label="betLabel || $t('Bet')"
        :disabled="playing"
        :rules="[validationInteger, v => validationMin(v, minBet), v => typeof account !== 'undefined' && validationMax(v, Math.min(Math.floor(account.balance), maxBet))]"
        prepend-inner-icon="mdi-minus"
        append-icon="mdi-plus"
        @click:prepend-inner="decreaseBet"
        @click:append="increaseBet"
      >
        <template v-slot:prepend-inner>
          <v-btn small text icon color="primary" @click="bet = minBet">
            <v-icon small>
              mdi-arrow-down
            </v-icon>
          </v-btn>
          <v-btn small text icon color="primary" @click="decreaseBet">
            <v-icon small>
              mdi-minus
            </v-icon>
          </v-btn>
        </template>
        <template v-slot:append>
          <v-btn small text icon color="primary" @click="increaseBet">
            <v-icon small>
              mdi-plus
            </v-icon>
          </v-btn>
          <v-btn small text icon color="primary" @click="bet = maxBet">
            <v-icon small>
              mdi-arrow-up
            </v-icon>
          </v-btn>
        </template>
      </v-text-field>
      <slot name="after-bet-input" />
      <v-btn
        v-if="autoPlayEnabled"
        color="primary"
        :disabled="!autoPlayInProgress"
        class="ml-2"
        outlined
        icon
        tile
        @click="stopAutoPlay"
      >
        <v-icon>mdi-stop</v-icon>
      </v-btn>
      <v-btn
        type="submit"
        color="primary"
        :loading="loading"
        :disabled="disabled || !formIsValid || isPlayDisabled || autoPlayInProgress"
        class="ml-2"
      >
        {{ playLabel || $t('Play') }}
      </v-btn>
      <slot name="after-play-button" />
    </div>
  </v-form>
</template>

<script>
import { mapState } from 'vuex'
import { isInteger } from '~/plugins/utils'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import clickSound from '~/../audio/common/click.wav'

export default {
  mixins: [FormMixin, GameMixin, SoundMixin],

  props: {
    betLabel: {
      type: String,
      required: false,
      default: ''
    },
    playLabel: {
      type: String,
      required: false,
      default: ''
    },
    loading: {
      type: Boolean,
      required: true
    },
    playing: {
      type: Boolean,
      required: true
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    provable: {
      type: Boolean,
      required: false,
      default: true
    },
    autoPlaySupport: {
      type: Boolean,
      required: false,
      default: false
    },
    autoPlayInterval: {
      type: Number,
      required: false,
      default: 1
    }
  },

  data () {
    return {
      bet: null,
      autoPlay: {
        baseBet: 0,
        onWin: {
          reset: true,
          amount: 0
        },
        onLoss: {
          reset: true,
          amount: 0
        },
        games: [],
        minBet: 0,
        maxBet: 0,
        betsCount: 0,
        maxBetsCount: 0,
        maxBalance: 0,
        minBalance: 0,
        winsCount: 0,
        lossesCount: 0
      },
      autoPlayInProgress: false,
      autoPlayEnabled: false,
      autoPlaySettingsDialog: false,
      autoPlayHistoryDialog: false
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    defaultBet () {
      return parseInt(this.config.default_bet_amount, 10)
    },
    minBet () {
      return parseInt(this.config.min_bet, 10)
    },
    maxBet () {
      return parseInt(this.config.max_bet, 10)
    },
    betStep () {
      return parseInt(this.config.bet_change_amount, 10)
    },
    isPlayDisabled () {
      return (this.provable && !this.provablyFairGame.hash) || this.playing || typeof this.account === 'undefined' || this.bet > this.account.balance
    },
    autoPlayNetResult () {
      return this.autoPlay.games.reduce((a, game) => a + game.win - game.bet, 0)
    }
  },

  watch: {
    bet (bet, oldBet) {
      this.$emit('bet-change', isInteger(bet) ? bet : 0)
    }
  },

  created () {
    // it's important to wait until next tick to ensure config computed property is updated
    // after switching from one game page to another.
    this.$nextTick(() => {
      this.bet = this.defaultBet
    })
  },

  methods: {
    play () {
      this.sound(clickSound)

      if (this.autoPlaySupport && this.autoPlayEnabled && !this.autoPlayInProgress) {
        this.autoPlayInProgress = true
        this.autoPlay.baseBet = this.bet
        this.autoPlay.betsCount = 0
        this.autoPlay.winsCount = 0
        this.autoPlay.lossesCount = 0
        this.autoPlay.games = []
      }

      this.$emit('play', this.bet)
    },
    decreaseBet () {
      this.sound(clickSound)
      const bet = this.bet - this.betStep
      this.bet = Math.max(this.minBet, bet)
    },
    increaseBet () {
      this.sound(clickSound)
      const bet = this.bet + this.betStep
      this.bet = Math.min(this.maxBet, bet)
    },
    onGameCompleted (game) {
      if (this.autoPlaySupport) {
        const isWin = game.win > game.bet
        this.autoPlay.betsCount++
        this.autoPlay.games.push({ bet: game.bet, win: game.win })

        // win
        if (isWin) {
          this.autoPlay.winsCount++

          if (this.autoPlay.onWin.reset) {
            this.setBet(this.autoPlay.baseBet)
          } else {
            this.setBet(this.bet + this.autoPlay.onWin.amount)
          }
          // loss
        } else {
          this.autoPlay.lossesCount++

          if (this.autoPlay.onLoss.reset) {
            this.setBet(this.autoPlay.baseBet)
          } else {
            this.setBet(this.bet + this.autoPlay.onLoss.amount)
          }
        }

        if (this.bet > this.account.balance ||
          (this.autoPlay.minBalance > 0 && this.account.balance < this.autoPlay.minBalance) ||
          (this.autoPlay.maxBalance > 0 && this.account.balance > this.autoPlay.maxBalance) ||
          (this.autoPlay.maxBetsCount > 0 && this.autoPlay.betsCount >= this.autoPlay.maxBetsCount)) {
          this.stopAutoPlay()
        }

        setTimeout(() => {
          if (this.autoPlayInProgress) {
            this.play()
          } else {
            this.stopAutoPlay()
          }
        }, parseInt(this.autoPlayInterval) * 1000)
      }
    },
    setBet (value) {
      const minValues = this.autoPlay.maxBet > 0
        ? [value, this.maxBet, this.autoPlay.maxBet]
        : [value, this.maxBet]
      const maxValues = this.autoPlay.minBet > 0
        ? [this.minBet, this.autoPlay.minBet]
        : [this.minBet]
      this.bet = Math.max(...[Math.min(...minValues), ...maxValues])
    },
    stopAutoPlay () {
      this.autoPlayInProgress = false
    }
  }
}
</script>
<style lang="scss" scoped>
.bet-input {
  max-width: 250px;
}
</style>

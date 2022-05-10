<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-combobox
              v-model="form.GAME_CASINO_HOLDEM_CATEGORIES"
              :label="$t('Categories')"
              multiple
              outlined
              chips
              small-chips
              deletable-chips
              hide-selected
              no-filter
            />

            <file-upload
              v-model="form.GAME_CASINO_HOLDEM_BANNER"
              :label="$t('Banner')"
              name="banner"
              folder="games/casino-holdem"
            />

            <file-upload
              v-model="form.GAME_CASINO_HOLDEM_BACKGROUND"
              :label="$t('Background image')"
              name="background"
              folder="games/casino-holdem"
              :clearable="true"
            />

            <v-text-field
              v-model.number="form.GAME_CASINO_HOLDEM_MIN_BET"
              :label="$t('Min bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CASINO_HOLDEM_MIN_BET')"
              :error-messages="form.errors.get('GAME_CASINO_HOLDEM_MIN_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_MIN_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_CASINO_HOLDEM_MAX_BET"
              :label="$t('Max bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CASINO_HOLDEM_MAX_BET')"
              :error-messages="form.errors.get('GAME_CASINO_HOLDEM_MAX_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_MAX_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_CASINO_HOLDEM_BET_CHANGE_AMOUNT"
              :label="$t('Bet increment / decrement amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CASINO_HOLDEM_BET_CHANGE_AMOUNT')"
              :error-messages="form.errors.get('GAME_CASINO_HOLDEM_BET_CHANGE_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_BET_CHANGE_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_CASINO_HOLDEM_DEFAULT_BET_AMOUNT"
              :label="$t('Default bet amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CASINO_HOLDEM_DEFAULT_BET_AMOUNT')"
              :error-messages="form.errors.get('GAME_CASINO_HOLDEM_DEFAULT_BET_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_DEFAULT_BET_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_CASINO_HOLDEM_MIN_BONUS_BET"
              :label="$t('Min bonus bet')"
              :rules="[validationInteger]"
              :error="form.errors.has('GAME_CASINO_HOLDEM_MIN_BONUS_BET')"
              :error-messages="form.errors.get('GAME_CASINO_HOLDEM_MIN_BONUS_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_MIN_BONUS_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_CASINO_HOLDEM_MAX_BONUS_BET"
              :label="$t('Max bonus bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CASINO_HOLDEM_MAX_BONUS_BET')"
              :error-messages="form.errors.get('GAME_CASINO_HOLDEM_MAX_BONUS_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_MAX_BONUS_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_CASINO_HOLDEM_BONUS_BET_CHANGE_AMOUNT"
              :label="$t('Bonus bet increment / decrement amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CASINO_HOLDEM_BONUS_BET_CHANGE_AMOUNT')"
              :error-messages="form.errors.get('GAME_CASINO_HOLDEM_BONUS_BET_CHANGE_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_BONUS_BET_CHANGE_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_CASINO_HOLDEM_DEFAULT_BONUS_BET_AMOUNT"
              :label="$t('Default bet amount')"
              :rules="[validationInteger]"
              :error="form.errors.has('GAME_CASINO_HOLDEM_DEFAULT_BONUS_BET_AMOUNT')"
              :error-messages="form.errors.get('GAME_CASINO_HOLDEM_DEFAULT_BONUS_BET_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_DEFAULT_BONUS_BET_AMOUNT')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Ante paytable') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <template v-for="(hand, i) in anteHands">
              <v-text-field
                v-if="i!==0"
                :key="i"
                v-model.number="form.GAME_CASINO_HOLDEM_ANTE_PAYTABLE[i]"
                :label="hand"
                :rules="[validationInteger]"
                :error="form.errors.has('GAME_CASINO_HOLDEM_ANTE_PAYTABLE')"
                :error-messages="form.errors.get('GAME_CASINO_HOLDEM_ANTE_PAYTABLE')"
                outlined
                :prefix="$t('Bet') + ' x '"
                :suffix="variables.GAME_CASINO_HOLDEM_ANTE_PAYTABLE[i] - 1 + ':1'"
                @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_ANTE_PAYTABLE')"
              />
              <input v-else :key="i" type="hidden" name="GAME_CASINO_HOLDEM_ANTE_PAYTABLE[0]" value="0">
            </template>
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Bonus paytable') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <template v-for="(hand, i) in bonusHands">
              <v-text-field
                v-if="i!==0"
                :key="i"
                v-model.number="form.GAME_CASINO_HOLDEM_BONUS_PAYTABLE[i]"
                :label="hand"
                :rules="[validationInteger]"
                :error="form.errors.has('GAME_CASINO_HOLDEM_BONUS_PAYTABLE')"
                :error-messages="form.errors.get('GAME_CASINO_HOLDEM_BONUS_PAYTABLE')"
                outlined
                :prefix="$t('Bet') + ' x '"
                :suffix="variables.GAME_CASINO_HOLDEM_BONUS_PAYTABLE[i] - 1 + ':1'"
                @keydown="clearFormErrors($event, 'GAME_CASINO_HOLDEM_BONUS_PAYTABLE')"
              />
              <input v-else :key="i" type="hidden" name="GAME_CASINO_HOLDEM_BONUS_PAYTABLE[0]" value="0">
            </template>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-card-text>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'
import FormMixin from '~/mixins/Form'
import FileUpload from '~/components/Admin/FileUpload'

export default {
  components: { FileUpload },
  mixins: [FormMixin],

  props: {
    form: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      anteHands: [
        this.$t('High card'),
        this.$t('Pair'),
        this.$t('Two pair'),
        this.$t('Three of a kind'),
        this.$t('Straight'),
        this.$t('Flush'),
        this.$t('Full house'),
        this.$t('Four of a kind'),
        this.$t('Straight flush'),
        this.$t('Royal flush')
      ],
      bonusHands: [
        this.$t('High card'),
        this.$t('Pair of aces'),
        this.$t('Two pair'),
        this.$t('Three of a kind'),
        this.$t('Straight'),
        this.$t('Flush'),
        this.$t('Full house'),
        this.$t('Four of a kind'),
        this.$t('Straight flush'),
        this.$t('Royal flush')
      ],
      variables: {
        GAME_CASINO_HOLDEM_CATEGORIES: config('casino-holdem.categories'),
        GAME_CASINO_HOLDEM_BANNER: config('casino-holdem.banner'),
        GAME_CASINO_HOLDEM_BACKGROUND: config('casino-holdem.background'),
        GAME_CASINO_HOLDEM_MIN_BET: parseInt(config('casino-holdem.min_bet'), 10),
        GAME_CASINO_HOLDEM_MIN_BONUS_BET: parseInt(config('casino-holdem.min_bonus_bet'), 10),
        GAME_CASINO_HOLDEM_MAX_BET: parseInt(config('casino-holdem.max_bet'), 10),
        GAME_CASINO_HOLDEM_MAX_BONUS_BET: parseInt(config('casino-holdem.max_bonus_bet'), 10),
        GAME_CASINO_HOLDEM_BET_CHANGE_AMOUNT: parseInt(config('casino-holdem.bet_change_amount'), 10),
        GAME_CASINO_HOLDEM_BONUS_BET_CHANGE_AMOUNT: parseInt(config('casino-holdem.bonus_bet_change_amount'), 10),
        GAME_CASINO_HOLDEM_DEFAULT_BET_AMOUNT: parseInt(config('casino-holdem.default_bet_amount'), 10),
        GAME_CASINO_HOLDEM_DEFAULT_BONUS_BET_AMOUNT: parseInt(config('casino-holdem.default_bonus_bet_amount'), 10),
        GAME_CASINO_HOLDEM_ANTE_PAYTABLE: config('casino-holdem.ante_paytable'),
        GAME_CASINO_HOLDEM_BONUS_PAYTABLE: config('casino-holdem.bonus_paytable')
      }
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-combobox
              v-model="form.GAME_BACCARAT_CATEGORIES"
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
              v-model="form.GAME_BACCARAT_BANNER"
              :label="$t('Banner')"
              name="banner"
              folder="games/baccarat"
            />

            <file-upload
              v-model="form.GAME_BACCARAT_BACKGROUND"
              :label="$t('Background image')"
              name="background"
              folder="games/baccarat"
              :clearable="true"
            />

            <v-text-field
              v-model.number="form.GAME_BACCARAT_MIN_BET"
              :label="$t('Min bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_BACCARAT_MIN_BET')"
              :error-messages="form.errors.get('GAME_BACCARAT_MIN_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_BACCARAT_MIN_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_BACCARAT_MAX_BET"
              :label="$t('Max bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_BACCARAT_MAX_BET')"
              :error-messages="form.errors.get('GAME_BACCARAT_MAX_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_BACCARAT_MAX_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_BACCARAT_BET_CHANGE_AMOUNT"
              :label="$t('Bet increment / decrement amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_BACCARAT_BET_CHANGE_AMOUNT')"
              :error-messages="form.errors.get('GAME_BACCARAT_BET_CHANGE_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_BACCARAT_BET_CHANGE_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_BACCARAT_DEFAULT_BET_AMOUNT"
              :label="$t('Default bet amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_BACCARAT_DEFAULT_BET_AMOUNT')"
              :error-messages="form.errors.get('GAME_BACCARAT_DEFAULT_BET_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_BACCARAT_DEFAULT_BET_AMOUNT')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Paytable') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model.number="form.GAME_BACCARAT_PAYOUT_PLAYER"
              :label="$t('Player bet')"
              :rules="[validationPositiveNumber]"
              :error="form.errors.has('GAME_BACCARAT_PAYOUT_PLAYER')"
              :error-messages="form.errors.get('GAME_BACCARAT_PAYOUT_PLAYER')"
              outlined
              :prefix="$t('Bet') + ' x '"
              @keydown="clearFormErrors($event, 'GAME_BACCARAT_PAYOUT_PLAYER')"
            />

            <v-text-field
              v-model.number="form.GAME_BACCARAT_PAYOUT_TIE"
              :label="$t('Tie bet')"
              :rules="[validationPositiveNumber]"
              :error="form.errors.has('GAME_BACCARAT_PAYOUT_TIE')"
              :error-messages="form.errors.get('GAME_BACCARAT_PAYOUT_TIE')"
              outlined
              :prefix="$t('Bet') + ' x '"
              @keydown="clearFormErrors($event, 'GAME_BACCARAT_PAYOUT_TIE')"
            />

            <v-text-field
              v-model.number="form.GAME_BACCARAT_PAYOUT_BANKER"
              :label="$t('Banker bet')"
              :rules="[validationPositiveNumber]"
              :error="form.errors.has('GAME_BACCARAT_PAYOUT_BANKER')"
              :error-messages="form.errors.get('GAME_BACCARAT_PAYOUT_BANKER')"
              outlined
              :prefix="$t('Bet') + ' x '"
              @keydown="clearFormErrors($event, 'GAME_BACCARAT_PAYOUT_BANKER')"
            />
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
      variables: {
        GAME_BACCARAT_CATEGORIES: config('baccarat.categories'),
        GAME_BACCARAT_BANNER: config('baccarat.banner'),
        GAME_BACCARAT_BACKGROUND: config('baccarat.background'),
        GAME_BACCARAT_MIN_BET: parseInt(config('baccarat.min_bet'), 10),
        GAME_BACCARAT_MAX_BET: parseInt(config('baccarat.max_bet'), 10),
        GAME_BACCARAT_BET_CHANGE_AMOUNT: parseInt(config('baccarat.bet_change_amount'), 10),
        GAME_BACCARAT_DEFAULT_BET_AMOUNT: parseInt(config('baccarat.default_bet_amount'), 10),
        GAME_BACCARAT_PAYOUT_PLAYER: parseFloat(config('baccarat.payouts.player')),
        GAME_BACCARAT_PAYOUT_TIE: parseFloat(config('baccarat.payouts.tie')),
        GAME_BACCARAT_PAYOUT_BANKER: parseFloat(config('baccarat.payouts.banker'))
      }
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

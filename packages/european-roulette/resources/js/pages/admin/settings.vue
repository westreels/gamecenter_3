<template>
  <v-card flat>
    <v-card-text>
      <v-combobox
        v-model="form.GAME_EUROPEAN_ROULETTE_CATEGORIES"
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
        v-model="form.GAME_EUROPEAN_ROULETTE_BANNER"
        :label="$t('Banner')"
        name="banner"
        folder="games/european-roulette"
      />

      <file-upload
        v-model="form.GAME_EUROPEAN_ROULETTE_BACKGROUND"
        :label="$t('Background image')"
        name="background"
        folder="games/european-roulette"
        :clearable="true"
      />

      <v-text-field
        v-model.number="form.GAME_EUROPEAN_ROULETTE_MIN_BET"
        :label="$t('Min bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_EUROPEAN_ROULETTE_MIN_BET')"
        :error-messages="form.errors.get('GAME_EUROPEAN_ROULETTE_MIN_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_EUROPEAN_ROULETTE_MIN_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_EUROPEAN_ROULETTE_MAX_BET"
        :label="$t('Max bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_EUROPEAN_ROULETTE_MAX_BET')"
        :error-messages="form.errors.get('GAME_EUROPEAN_ROULETTE_MAX_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_EUROPEAN_ROULETTE_MAX_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_EUROPEAN_ROULETTE_BET_CHANGE_AMOUNT"
        :label="$t('Bet increment / decrement amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_EUROPEAN_ROULETTE_BET_CHANGE_AMOUNT')"
        :error-messages="form.errors.get('GAME_EUROPEAN_ROULETTE_BET_CHANGE_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_EUROPEAN_ROULETTE_BET_CHANGE_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.GAME_EUROPEAN_ROULETTE_DEFAULT_BET_AMOUNT"
        :label="$t('Default bet amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_EUROPEAN_ROULETTE_DEFAULT_BET_AMOUNT')"
        :error-messages="form.errors.get('GAME_EUROPEAN_ROULETTE_DEFAULT_BET_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_EUROPEAN_ROULETTE_DEFAULT_BET_AMOUNT')"
      />
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
        GAME_EUROPEAN_ROULETTE_CATEGORIES: config('european-roulette.categories'),
        GAME_EUROPEAN_ROULETTE_BANNER: config('european-roulette.banner'),
        GAME_EUROPEAN_ROULETTE_BACKGROUND: config('european-roulette.background'),
        GAME_EUROPEAN_ROULETTE_MIN_BET: parseInt(config('european-roulette.min_bet'), 10),
        GAME_EUROPEAN_ROULETTE_MAX_BET: parseInt(config('european-roulette.max_bet'), 10),
        GAME_EUROPEAN_ROULETTE_BET_CHANGE_AMOUNT: parseInt(config('european-roulette.bet_change_amount'), 10),
        GAME_EUROPEAN_ROULETTE_DEFAULT_BET_AMOUNT: parseInt(config('european-roulette.default_bet_amount'), 10)
      }
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

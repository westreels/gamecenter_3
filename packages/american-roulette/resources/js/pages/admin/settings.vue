<template>
  <v-card flat>
    <v-card-text>
      <v-combobox
        v-model="form.GAME_AMERICAN_ROULETTE_CATEGORIES"
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
        v-model="form.GAME_AMERICAN_ROULETTE_BANNER"
        :label="$t('Banner')"
        name="banner"
        folder="games/american-roulette"
      />

      <file-upload
        v-model="form.GAME_AMERICAN_ROULETTE_BACKGROUND"
        :label="$t('Background image')"
        name="background"
        folder="games/american-roulette"
        :clearable="true"
      />

      <v-text-field
        v-model.number="form.GAME_AMERICAN_ROULETTE_MIN_BET"
        :label="$t('Min bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_AMERICAN_ROULETTE_MIN_BET')"
        :error-messages="form.errors.get('GAME_AMERICAN_ROULETTE_MIN_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_AMERICAN_ROULETTE_MIN_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_AMERICAN_ROULETTE_MAX_BET"
        :label="$t('Max bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_AMERICAN_ROULETTE_MAX_BET')"
        :error-messages="form.errors.get('GAME_AMERICAN_ROULETTE_MAX_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_AMERICAN_ROULETTE_MAX_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_AMERICAN_ROULETTE_BET_CHANGE_AMOUNT"
        :label="$t('Bet increment / decrement amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_AMERICAN_ROULETTE_BET_CHANGE_AMOUNT')"
        :error-messages="form.errors.get('GAME_AMERICAN_ROULETTE_BET_CHANGE_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_AMERICAN_ROULETTE_BET_CHANGE_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.GAME_AMERICAN_ROULETTE_DEFAULT_BET_AMOUNT"
        :label="$t('Default bet amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_AMERICAN_ROULETTE_DEFAULT_BET_AMOUNT')"
        :error-messages="form.errors.get('GAME_AMERICAN_ROULETTE_DEFAULT_BET_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_AMERICAN_ROULETTE_DEFAULT_BET_AMOUNT')"
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
        GAME_AMERICAN_ROULETTE_CATEGORIES: config('american-roulette.categories'),
        GAME_AMERICAN_ROULETTE_BANNER: config('american-roulette.banner'),
        GAME_AMERICAN_ROULETTE_BACKGROUND: config('american-roulette.background'),
        GAME_AMERICAN_ROULETTE_MIN_BET: parseInt(config('american-roulette.min_bet'), 10),
        GAME_AMERICAN_ROULETTE_MAX_BET: parseInt(config('american-roulette.max_bet'), 10),
        GAME_AMERICAN_ROULETTE_BET_CHANGE_AMOUNT: parseInt(config('american-roulette.bet_change_amount'), 10),
        GAME_AMERICAN_ROULETTE_DEFAULT_BET_AMOUNT: parseInt(config('american-roulette.default_bet_amount'), 10)
      }
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

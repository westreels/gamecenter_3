<template>
  <v-card flat>
    <v-card-text>
      <v-combobox
        v-model="form.GAME_SIC_BO_CATEGORIES"
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
        v-model="form.GAME_SIC_BO_BANNER"
        :label="$t('Banner')"
        name="banner"
        folder="games/sic-bo"
      />

      <file-upload
        v-model="form.GAME_SIC_BO_BACKGROUND"
        :label="$t('Background image')"
        name="background"
        folder="games/sic-bo"
        :clearable="true"
      />

      <v-text-field
        v-model.number="form.GAME_SIC_BO_MIN_BET"
        :label="$t('Min bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_SIC_BO_MIN_BET')"
        :error-messages="form.errors.get('GAME_SIC_BO_MIN_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_SIC_BO_MIN_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_SIC_BO_MAX_BET"
        :label="$t('Max bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_SIC_BO_MAX_BET')"
        :error-messages="form.errors.get('GAME_SIC_BO_MAX_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_SIC_BO_MAX_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_SIC_BO_BET_CHANGE_AMOUNT"
        :label="$t('Bet increment / decrement amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_SIC_BO_BET_CHANGE_AMOUNT')"
        :error-messages="form.errors.get('GAME_SIC_BO_BET_CHANGE_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_SIC_BO_BET_CHANGE_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.GAME_SIC_BO_DEFAULT_BET_AMOUNT"
        :label="$t('Default bet amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_SIC_BO_DEFAULT_BET_AMOUNT')"
        :error-messages="form.errors.get('GAME_SIC_BO_DEFAULT_BET_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_SIC_BO_DEFAULT_BET_AMOUNT')"
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
        GAME_SIC_BO_CATEGORIES: config('sic-bo.categories'),
        GAME_SIC_BO_BANNER: config('sic-bo.banner'),
        GAME_SIC_BO_BACKGROUND: config('sic-bo.background'),
        GAME_SIC_BO_MIN_BET: parseInt(config('sic-bo.min_bet'), 10),
        GAME_SIC_BO_MAX_BET: parseInt(config('sic-bo.max_bet'), 10),
        GAME_SIC_BO_BET_CHANGE_AMOUNT: parseInt(config('sic-bo.bet_change_amount'), 10),
        GAME_SIC_BO_DEFAULT_BET_AMOUNT: parseInt(config('sic-bo.default_bet_amount'), 10)
      }
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

<template>
  <v-card flat>
    <v-card-text>
      <v-combobox
        v-model="form.GAME_DICE_CATEGORIES"
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
        v-model="form.GAME_DICE_BANNER"
        :label="$t('Banner')"
        name="banner"
        folder="games/dice"
      />

      <file-upload
        v-model="form.GAME_DICE_BACKGROUND"
        :label="$t('Background image')"
        name="background"
        folder="games/dice"
        :clearable="true"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_MIN_BET"
        :label="$t('Min bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_MIN_BET')"
        :error-messages="form.errors.get('GAME_DICE_MIN_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_DICE_MIN_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_MAX_BET"
        :label="$t('Max bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_MAX_BET')"
        :error-messages="form.errors.get('GAME_DICE_MAX_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_DICE_MAX_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_BET_CHANGE_AMOUNT"
        :label="$t('Bet increment / decrement amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_BET_CHANGE_AMOUNT')"
        :error-messages="form.errors.get('GAME_DICE_BET_CHANGE_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_DICE_BET_CHANGE_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_DEFAULT_BET_AMOUNT"
        :label="$t('Default bet amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_DEFAULT_BET_AMOUNT')"
        :error-messages="form.errors.get('GAME_DICE_DEFAULT_BET_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_DICE_DEFAULT_BET_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_MIN_WIN_CHANCE"
        :label="$t('Min win chance')"
        :rules="[validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_MIN_WIN_CHANCE')"
        :error-messages="form.errors.get('GAME_DICE_MIN_WIN_CHANCE')"
        outlined
        suffix="%"
        @keydown="clearFormErrors($event, 'GAME_DICE_MIN_WIN_CHANCE')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_MAX_WIN_CHANCE"
        :label="$t('Max win chance')"
        :rules="[validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_MAX_WIN_CHANCE')"
        :error-messages="form.errors.get('GAME_DICE_MAX_WIN_CHANCE')"
        outlined
        suffix="%"
        @keydown="clearFormErrors($event, 'GAME_DICE_MAX_WIN_CHANCE')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_HOUSE_EDGE"
        :label="$t('House edge')"
        :rules="[validationNumeric]"
        :error="form.errors.has('GAME_DICE_HOUSE_EDGE')"
        :error-messages="form.errors.get('GAME_DICE_HOUSE_EDGE')"
        outlined
        suffix="%"
        :hint="houseEdgeHint"
        persistent-hint
        @keydown="clearFormErrors($event, 'GAME_DICE_HOUSE_EDGE')"
      />

      <v-switch
        v-model="form.GAME_DICE_AUTO_PLAY_ENABLED"
        :label="$t('Auto play')"
        color="primary"
        false-value="false"
        true-value="true"
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
        GAME_DICE_CATEGORIES: config('dice.categories'),
        GAME_DICE_BANNER: config('dice.banner'),
        GAME_DICE_BACKGROUND: config('dice.background'),
        GAME_DICE_MIN_BET: parseInt(config('dice.min_bet'), 10),
        GAME_DICE_MAX_BET: parseInt(config('dice.max_bet'), 10),
        GAME_DICE_BET_CHANGE_AMOUNT: parseInt(config('dice.bet_change_amount'), 10),
        GAME_DICE_DEFAULT_BET_AMOUNT: parseInt(config('dice.default_bet_amount'), 10),
        GAME_DICE_MIN_WIN_CHANCE: parseFloat(config('dice.min_win_chance')),
        GAME_DICE_MAX_WIN_CHANCE: parseFloat(config('dice.max_win_chance')),
        GAME_DICE_HOUSE_EDGE: parseFloat(config('dice.house_edge')),
        GAME_DICE_AUTO_PLAY_ENABLED: '' + config('dice.auto_play_enabled')
      }
    }
  },

  computed: {
    houseEdgeHint () {
      return this.$t('This parameter affects payout calculation.')
        + ' '
        + this.$t('Payout is calculated like this: {0}.', ['(100 - houseEdge) / winChance'])
        + ' '
        + this.$t('Hence the more house edge is the less payout a user will get.')
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

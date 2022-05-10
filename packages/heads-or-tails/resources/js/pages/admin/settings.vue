<template>
  <v-card flat>
    <v-card-text>
      <v-combobox
        v-model="form.GAME_HEADS_OR_TAILS_CATEGORIES"
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
        v-model="form.GAME_HEADS_OR_TAILS_BANNER"
        :label="$t('Banner')"
        name="banner"
        folder="games/heads-or-tails"
      />

      <file-upload
        v-model="form.GAME_HEADS_OR_TAILS_BACKGROUND"
        :label="$t('Background image')"
        name="background"
        folder="games/heads-or-tails"
        :clearable="true"
      />

      <v-text-field
        v-model.number="form.GAME_HEADS_OR_TAILS_MIN_BET"
        :label="$t('Min bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_HEADS_OR_TAILS_MIN_BET')"
        :error-messages="form.errors.get('GAME_HEADS_OR_TAILS_MIN_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_HEADS_OR_TAILS_MIN_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_HEADS_OR_TAILS_MAX_BET"
        :label="$t('Max bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_HEADS_OR_TAILS_MAX_BET')"
        :error-messages="form.errors.get('GAME_HEADS_OR_TAILS_MAX_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_HEADS_OR_TAILS_MAX_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_HEADS_OR_TAILS_BET_CHANGE_AMOUNT"
        :label="$t('Bet increment / decrement amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_HEADS_OR_TAILS_BET_CHANGE_AMOUNT')"
        :error-messages="form.errors.get('GAME_HEADS_OR_TAILS_BET_CHANGE_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_HEADS_OR_TAILS_BET_CHANGE_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.GAME_HEADS_OR_TAILS_DEFAULT_BET_AMOUNT"
        :label="$t('Default bet amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_HEADS_OR_TAILS_DEFAULT_BET_AMOUNT')"
        :error-messages="form.errors.get('GAME_HEADS_OR_TAILS_DEFAULT_BET_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_HEADS_OR_TAILS_DEFAULT_BET_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.GAME_HEADS_OR_TAILS_HOUSE_EDGE"
        :label="$t('House edge')"
        :rules="[validationNumeric]"
        :error="form.errors.has('GAME_HEADS_OR_TAILS_HOUSE_EDGE')"
        :error-messages="form.errors.get('GAME_HEADS_OR_TAILS_HOUSE_EDGE')"
        outlined
        suffix="%"
        :hint="houseEdgeHint"
        persistent-hint
        class="mb-5"
        @keydown="clearFormErrors($event, 'GAME_HEADS_OR_TAILS_HOUSE_EDGE')"
      />

      <file-upload
        v-model="form.GAME_HEADS_OR_TAILS_IMAGES_HEADS"
        :label="$t('Heads image texture')"
        name="heads"
        folder="games/heads-or-tails"
      />

      <file-upload
        v-model="form.GAME_HEADS_OR_TAILS_IMAGES_TAILS"
        :label="$t('Tails image texture')"
        name="tails"
        folder="games/heads-or-tails"
      />

      <file-upload
        v-model="form.GAME_HEADS_OR_TAILS_IMAGES_EDGE"
        :label="$t('Edge image texture')"
        name="edge"
        folder="games/heads-or-tails"
      />
    </v-card-text>
  </v-card>
</template>

<script>
import FormMixin from '~/mixins/Form'
import { config } from '~/plugins/config'
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
        GAME_HEADS_OR_TAILS_CATEGORIES: config('heads-or-tails.categories'),
        GAME_HEADS_OR_TAILS_BANNER: config('heads-or-tails.banner'),
        GAME_HEADS_OR_TAILS_BACKGROUND: config('heads-or-tails.background'),
        GAME_HEADS_OR_TAILS_IMAGES_HEADS: config('heads-or-tails.images.heads'),
        GAME_HEADS_OR_TAILS_IMAGES_TAILS: config('heads-or-tails.images.tails'),
        GAME_HEADS_OR_TAILS_IMAGES_EDGE: config('heads-or-tails.images.edge'),
        GAME_HEADS_OR_TAILS_MIN_BET: parseInt(config('heads-or-tails.min_bet'), 10),
        GAME_HEADS_OR_TAILS_MAX_BET: parseInt(config('heads-or-tails.max_bet'), 10),
        GAME_HEADS_OR_TAILS_BET_CHANGE_AMOUNT: parseInt(config('heads-or-tails.bet_change_amount'), 10),
        GAME_HEADS_OR_TAILS_DEFAULT_BET_AMOUNT: parseInt(config('heads-or-tails.default_bet_amount'), 10),
        GAME_HEADS_OR_TAILS_HOUSE_EDGE: parseFloat(config('heads-or-tails.house_edge'))
      }
    }
  },

  computed: {
    houseEdgeHint () {
      return this.$t('This parameter affects payout calculation.')
        + ' '
        + this.$t('Payout is calculated like this: {0}.', ['2 - houseEdge / 100'])
        + ' '
        + this.$t('Hence the more house edge is the less payout a user will get.')
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

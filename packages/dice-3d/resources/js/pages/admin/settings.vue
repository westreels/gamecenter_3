<template>
  <v-card flat>
    <v-card-text>
      <v-combobox
        v-model="form.GAME_DICE_3D_CATEGORIES"
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
        v-model="form.GAME_DICE_3D_BANNER"
        :label="$t('Banner')"
        name="banner"
        folder="games/dice-3d"
      />

      <file-upload
        v-model="form.GAME_DICE_3D_BACKGROUND"
        :label="$t('Background image')"
        name="background"
        folder="games/dice-3d"
        :clearable="true"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_3D_MIN_BET"
        :label="$t('Min bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_3D_MIN_BET')"
        :error-messages="form.errors.get('GAME_DICE_3D_MIN_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_DICE_3D_MIN_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_3D_MAX_BET"
        :label="$t('Max bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_3D_MAX_BET')"
        :error-messages="form.errors.get('GAME_DICE_3D_MAX_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_DICE_3D_MAX_BET')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_3D_BET_CHANGE_AMOUNT"
        :label="$t('Bet increment / decrement amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_3D_BET_CHANGE_AMOUNT')"
        :error-messages="form.errors.get('GAME_DICE_3D_BET_CHANGE_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_DICE_3D_BET_CHANGE_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_3D_DEFAULT_BET_AMOUNT"
        :label="$t('Default bet amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('GAME_DICE_3D_DEFAULT_BET_AMOUNT')"
        :error-messages="form.errors.get('GAME_DICE_3D_DEFAULT_BET_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'GAME_DICE_3D_DEFAULT_BET_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.GAME_DICE_3D_HOUSE_EDGE"
        :label="$t('House edge')"
        :rules="[validationNumeric]"
        :error="form.errors.has('GAME_DICE_3D_HOUSE_EDGE')"
        :error-messages="form.errors.get('GAME_DICE_3D_HOUSE_EDGE')"
        outlined
        suffix="%"
        :hint="houseEdgeHint"
        persistent-hint
        @keydown="clearFormErrors($event, 'GAME_DICE_3D_HOUSE_EDGE')"
      />

      <v-select
        v-model="form.GAME_DICE_3D_DICE"
        :items="diceTypes"
        :label="$t('Dice')"
        chips
        multiple
        hide-selected
        deletable-chips
        append-icon="mdi-plus"
        class="mt-5"
      />

      <color-picker v-model="form.GAME_DICE_3D_FILL_COLOR" :label="$t('Dice fill color')" />

      <color-picker v-model="form.GAME_DICE_3D_FONT_COLOR" :label="$t('Dice font color')" />
    </v-card-text>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'
import FormMixin from '~/mixins/Form'
import ColorPicker from '~/components/ColorPicker'
import FileUpload from '~/components/Admin/FileUpload'

export default {
  components: { FileUpload, ColorPicker },

  mixins: [FormMixin],

  props: {
    form: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      colorPickerFillColor: false,
      colorPickerFontColor: false,
      diceTypes: [
        { text: this.$t('Tetrahedron'), value: 'tetrahedron' },
        { text: this.$t('Cube'), value: 'cube' },
        { text: this.$t('Octahedron'), value: 'octahedron' },
        { text: this.$t('Dipyramid'), value: 'dipyramid' },
        { text: this.$t('Dodecahedron'), value: 'dodecahedron' },
        { text: this.$t('Icosahedron'), value: 'icosahedron' }
      ],
      variables: {
        GAME_DICE_3D_CATEGORIES: config('dice-3d.categories'),
        GAME_DICE_3D_BANNER: config('dice-3d.banner'),
        GAME_DICE_3D_BACKGROUND: config('dice-3d.background'),
        GAME_DICE_3D_MIN_BET: parseInt(config('dice-3d.min_bet'), 10),
        GAME_DICE_3D_MAX_BET: parseInt(config('dice-3d.max_bet'), 10),
        GAME_DICE_3D_BET_CHANGE_AMOUNT: parseInt(config('dice-3d.bet_change_amount'), 10),
        GAME_DICE_3D_DEFAULT_BET_AMOUNT: parseInt(config('dice-3d.default_bet_amount'), 10),
        GAME_DICE_3D_MIN_WIN_CHANCE: parseFloat(config('dice-3d.min_win_chance')),
        GAME_DICE_3D_MAX_WIN_CHANCE: parseFloat(config('dice-3d.max_win_chance')),
        GAME_DICE_3D_HOUSE_EDGE: parseFloat(config('dice-3d.house_edge')),
        GAME_DICE_3D_DICE: config('dice-3d.dice'),
        GAME_DICE_3D_FILL_COLOR: config('dice-3d.fill_color'),
        GAME_DICE_3D_FONT_COLOR: config('dice-3d.font_color')
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

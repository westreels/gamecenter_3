<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-combobox
              v-model="form.GAME_VIDEO_POKER_CATEGORIES"
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
              v-model="form.GAME_VIDEO_POKER_BANNER"
              :label="$t('Banner')"
              name="banner"
              folder="games/video-poker"
            />

            <file-upload
              v-model="form.GAME_VIDEO_POKER_BACKGROUND"
              :label="$t('Background image')"
              name="background"
              folder="games/video-poker"
              :clearable="true"
            />

            <v-text-field
              v-model.number="form.GAME_VIDEO_POKER_MIN_BET"
              :label="$t('Min bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_VIDEO_POKER_MIN_BET')"
              :error-messages="form.errors.get('GAME_VIDEO_POKER_MIN_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_VIDEO_POKER_MIN_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_VIDEO_POKER_MAX_BET"
              :label="$t('Max bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_VIDEO_POKER_MAX_BET')"
              :error-messages="form.errors.get('GAME_VIDEO_POKER_MAX_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_VIDEO_POKER_MAX_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_VIDEO_POKER_BET_CHANGE_AMOUNT"
              :label="$t('Bet increment / decrement amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_VIDEO_POKER_BET_CHANGE_AMOUNT')"
              :error-messages="form.errors.get('GAME_VIDEO_POKER_BET_CHANGE_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_VIDEO_POKER_BET_CHANGE_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_VIDEO_POKER_DEFAULT_BET_AMOUNT"
              :label="$t('Default bet amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_VIDEO_POKER_DEFAULT_BET_AMOUNT')"
              :error-messages="form.errors.get('GAME_VIDEO_POKER_DEFAULT_BET_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_VIDEO_POKER_DEFAULT_BET_AMOUNT')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Paytable') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <template v-for="(combination, i) in combinations">
              <v-text-field
                v-if="i!==0"
                :key="i"
                v-model.number="form.GAME_VIDEO_POKER_PAYTABLE[i]"
                :label="combination"
                :rules="[validationPositiveNumber]"
                :error="form.errors.has('GAME_VIDEO_POKER_PAYTABLE')"
                :error-messages="form.errors.get('GAME_VIDEO_POKER_PAYTABLE')"
                outlined
                :prefix="$t('Bet') + ' x '"
                @keydown="clearFormErrors($event, 'GAME_VIDEO_POKER_PAYTABLE')"
              />
              <input v-else type="hidden" name="GAME_VIDEO_POKER_PAYTABLE[0]" value="0" />
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
      variables: {
        GAME_VIDEO_POKER_CATEGORIES: config('video-poker.categories'),
        GAME_VIDEO_POKER_BANNER: config('video-poker.banner'),
        GAME_VIDEO_POKER_BACKGROUND: config('video-poker.background'),
        GAME_VIDEO_POKER_MIN_BET: parseInt(config('video-poker.min_bet'), 10),
        GAME_VIDEO_POKER_MAX_BET: parseInt(config('video-poker.max_bet'), 10),
        GAME_VIDEO_POKER_BET_CHANGE_AMOUNT: parseInt(config('video-poker.bet_change_amount'), 10),
        GAME_VIDEO_POKER_DEFAULT_BET_AMOUNT: parseInt(config('video-poker.default_bet_amount'), 10),
        GAME_VIDEO_POKER_PAYTABLE: config('video-poker.paytable')
      },
      combinations: [
        this.$t('Nothing'),
        this.$t('Jacks or better'),
        this.$t('Two pair'),
        this.$t('Three of a kind'),
        this.$t('Straight'),
        this.$t('Flush'),
        this.$t('Full house'),
        this.$t('Four of a kind'),
        this.$t('Straight flush'),
        this.$t('Royal flush')
      ]
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

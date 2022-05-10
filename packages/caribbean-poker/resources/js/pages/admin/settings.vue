<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-combobox
              v-model="form.GAME_CARIBBEAN_POKER_CATEGORIES"
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
              v-model="form.GAME_CARIBBEAN_POKER_BANNER"
              :label="$t('Banner')"
              name="banner"
              folder="games/caribbean-poker"
            />

            <v-text-field
              v-model.number="form.GAME_CARIBBEAN_POKER_MIN_BET"
              :label="$t('Min bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CARIBBEAN_POKER_MIN_BET')"
              :error-messages="form.errors.get('GAME_CARIBBEAN_POKER_MIN_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CARIBBEAN_POKER_MIN_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_CARIBBEAN_POKER_MAX_BET"
              :label="$t('Max bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CARIBBEAN_POKER_MAX_BET')"
              :error-messages="form.errors.get('GAME_CARIBBEAN_POKER_MAX_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CARIBBEAN_POKER_MAX_BET')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Call paytable') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <template v-for="(hand, i) in hands">
              <v-text-field
                :key="i"
                v-model.number="form.GAME_CARIBBEAN_POKER_PAYTABLE[i]"
                :label="hand"
                :rules="[validationInteger]"
                :error="form.errors.has('GAME_CARIBBEAN_POKER_PAYTABLE')"
                :error-messages="form.errors.get('GAME_CARIBBEAN_POKER_PAYTABLE')"
                outlined
                :prefix="$t('Bet') + ' x '"
                :suffix="variables.GAME_CARIBBEAN_POKER_PAYTABLE[i] - 1 + ':1'"
                @keydown="clearFormErrors($event, 'GAME_CARIBBEAN_POKER_PAYTABLE')"
              />
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
      hands: [
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
      variables: {
        GAME_CARIBBEAN_POKER_CATEGORIES: config('caribbean-poker.categories'),
        GAME_CARIBBEAN_POKER_BANNER: config('caribbean-poker.banner'),
        GAME_CARIBBEAN_POKER_MIN_BET: config('caribbean-poker.min_bet'),
        GAME_CARIBBEAN_POKER_MAX_BET: config('caribbean-poker.max_bet'),
        GAME_CARIBBEAN_POKER_PAYTABLE: config('caribbean-poker.paytable')
      }
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-combobox
              v-model="form.GAME_PLINKO_CATEGORIES"
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
              v-model="form.GAME_PLINKO_BANNER"
              :label="$t('Banner')"
              name="banner"
              folder="games/plinko"
            />

            <file-upload
              v-model="form.GAME_PLINKO_BACKGROUND"
              :label="$t('Background image')"
              name="background"
              folder="games/plinko"
              :clearable="true"
            />

            <v-text-field
              v-model.number="form.GAME_PLINKO_MIN_BET"
              :label="$t('Min bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_PLINKO_MIN_BET')"
              :error-messages="form.errors.get('GAME_PLINKO_MIN_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_PLINKO_MIN_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_PLINKO_MAX_BET"
              :label="$t('Max bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_PLINKO_MAX_BET')"
              :error-messages="form.errors.get('GAME_PLINKO_MAX_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_PLINKO_MAX_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_PLINKO_BET_CHANGE_AMOUNT"
              :label="$t('Bet increment / decrement amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_PLINKO_BET_CHANGE_AMOUNT')"
              :error-messages="form.errors.get('GAME_PLINKO_BET_CHANGE_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_PLINKO_BET_CHANGE_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_PLINKO_DEFAULT_BET_AMOUNT"
              :label="$t('Default bet amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_PLINKO_DEFAULT_BET_AMOUNT')"
              :error-messages="form.errors.get('GAME_PLINKO_DEFAULT_BET_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_PLINKO_DEFAULT_BET_AMOUNT')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>

        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Paytable') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <template v-for="(payout, i) in form.GAME_PLINKO_PAYTABLE">
              <v-text-field
                :key="i"
                v-model.number="form.GAME_PLINKO_PAYTABLE[i]"
                :label="$t('Bucket {0}', [i + 1])"
                :rules="[validationNonNegativeNumber]"
                :error="form.errors.has('GAME_PLINKO_PAYTABLE')"
                :error-messages="form.errors.get('GAME_PLINKO_PAYTABLE')"
                outlined
                :prefix="$t('Bet') + ' x '"
                :append-outer-icon="bucketsCount > 2 ? 'mdi-delete' : ''"
                @click:append-outer="removeBucket(i)"
                @keydown="clearFormErrors($event, 'GAME_PLINKO_PAYTABLE')"
              />
            </template>

            <v-row v-if="bucketsCount < 26">
              <v-col>
                <v-btn color="primary" @click="addBucket">
                  {{ $t('Add bucket') }}
                </v-btn>
              </v-col>
            </v-row>
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
        GAME_PLINKO_CATEGORIES: config('plinko.categories'),
        GAME_PLINKO_BANNER: config('plinko.banner'),
        GAME_PLINKO_BACKGROUND: config('plinko.background'),
        GAME_PLINKO_MIN_BET: parseInt(config('plinko.min_bet'), 10),
        GAME_PLINKO_MAX_BET: parseInt(config('plinko.max_bet'), 10),
        GAME_PLINKO_BET_CHANGE_AMOUNT: parseInt(config('plinko.bet_change_amount'), 10),
        GAME_PLINKO_DEFAULT_BET_AMOUNT: parseInt(config('plinko.default_bet_amount'), 10),
        GAME_PLINKO_PAYTABLE: config('plinko.paytable')
      }
    }
  },

  computed: {
    bucketsCount () {
      return this.form.GAME_PLINKO_PAYTABLE.length
    }
  },

  created () {
    this.$emit('input', this.variables)
  },

  methods: {
    addBucket () {
      this.form.GAME_PLINKO_PAYTABLE.push(0)
    },
    removeBucket (index) {
      this.form.GAME_PLINKO_PAYTABLE.splice(index, 1)
    }
  }
}
</script>

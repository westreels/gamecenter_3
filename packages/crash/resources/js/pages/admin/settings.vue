<template>
  <v-card flat>
    <v-card-text v-if="pusherEnabled">
      <v-alert
        dense
        outlined
        text
        color="warning"
      >
        {{ $t('Steps to take after saving the game settings') }}:
        <a href="/admin/help/crash#q05">{{ $t('read more') }}</a>
      </v-alert>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-combobox
              v-model="form.GAME_CRASH_CATEGORIES"
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
              v-model="form.GAME_CRASH_BANNER"
              :label="$t('Banner')"
              name="banner"
              folder="games/crash"
            />

            <file-upload
              v-model="form.GAME_CRASH_BACKGROUND"
              :label="$t('Background image')"
              name="background"
              folder="games/crash"
              :clearable="true"
            />

            <v-text-field
              v-model.number="form.GAME_CRASH_MIN_BET"
              :label="$t('Min bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CRASH_MIN_BET')"
              :error-messages="form.errors.get('GAME_CRASH_MIN_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CRASH_MIN_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_CRASH_MAX_BET"
              :label="$t('Max bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CRASH_MAX_BET')"
              :error-messages="form.errors.get('GAME_CRASH_MAX_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CRASH_MAX_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_CRASH_BET_CHANGE_AMOUNT"
              :label="$t('Bet increment / decrement amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CRASH_BET_CHANGE_AMOUNT')"
              :error-messages="form.errors.get('GAME_CRASH_BET_CHANGE_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CRASH_BET_CHANGE_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_CRASH_DEFAULT_BET_AMOUNT"
              :label="$t('Default bet amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CRASH_DEFAULT_BET_AMOUNT')"
              :error-messages="form.errors.get('GAME_CRASH_DEFAULT_BET_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_CRASH_DEFAULT_BET_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_CRASH_DURATION"
              :label="$t('Betting round duration')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CRASH_DURATION')"
              :error-messages="form.errors.get('GAME_CRASH_DURATION')"
              outlined
              :suffix="$t('seconds')"
              @keydown="clearFormErrors($event, 'GAME_CRASH_DURATION')"
            />

            <v-text-field
              v-model.number="form.GAME_CRASH_INTERVAL"
              :label="$t('Delay before next game starts')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_CRASH_INTERVAL')"
              :error-messages="form.errors.get('GAME_CRASH_INTERVAL')"
              outlined
              :suffix="$t('seconds')"
              @keydown="clearFormErrors($event, 'GAME_CRASH_INTERVAL')"
            />

            <v-text-field
              v-model.number="form.GAME_CRASH_BASE_NUMBER"
              :label="$t('Exponential function base number')"
              :rules="[v => validationMin(v, 1), validationNumeric]"
              :error="form.errors.has('GAME_CRASH_BASE_NUMBER')"
              :error-messages="form.errors.get('GAME_CRASH_BASE_NUMBER')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_CRASH_BASE_NUMBER')"
            />

            <v-switch
              v-model="form.GAME_CRASH_BOTS_ENABLED"
              :label="$t('Enable bots')"
              color="primary"
              false-value="false"
              true-value="true"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Payout intervals') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <template v-for="(maxPayout, i) in form.GAME_CRASH_PAYOUT_INTERVALS">
              <v-text-field
                :key="i"
                v-model.number="form.GAME_CRASH_PAYOUT_INTERVALS[i]"
                :label="$t('Interval {0}', [i+1])"
                :rules="[validationPositiveNumber]"
                :error="form.errors.has('GAME_CRASH_PAYOUT_INTERVALS')"
                :error-messages="form.errors.get('GAME_CRASH_PAYOUT_INTERVALS')"
                outlined
                :prefix="(i > 0 ? (form.GAME_CRASH_PAYOUT_INTERVALS[i - 1] + 0.01).toFixed(2) : 1.01) + ' - '"
                append-outer-icon="mdi-delete"
                @keydown="clearFormErrors($event, 'GAME_CRASH_PAYOUT_INTERVALS')"
                @click:append-outer="deletePayoutInterval(i)"
              />
            </template>
            <v-btn color="primary" @click="addPayoutInterval">
              {{ $t('Add payout interval') }}
            </v-btn>
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Animation') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model.number="form.GAME_CRASH_ANIMATION.time_limit"
              :label="$t('Max animation time')"
              :rules="[validationPositiveInteger]"
              :error="form.errors.has('GAME_CRASH_ANIMATION.time_limit')"
              :error-messages="form.errors.get('GAME_CRASH_ANIMATION.time_limit')"
              outlined
              :suffix="$t('seconds')"
              @keydown="clearFormErrors($event, 'GAME_CRASH_ANIMATION.time_limit')"
            />

            <v-text-field
              v-model.number="form.GAME_CRASH_ANIMATION.airplanes_count"
              :label="$t('Number of airplanes')"
              :rules="[validationNonNegativeInteger]"
              :error="form.errors.has('GAME_CRASH_ANIMATION.airplanes_count')"
              :error-messages="form.errors.get('GAME_CRASH_ANIMATION.airplanes_count')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_CRASH_ANIMATION.airplanes_count')"
            />

            <v-text-field
              v-model.number="form.GAME_CRASH_ANIMATION.clouds_count"
              :label="$t('Number of clouds')"
              :rules="[validationNonNegativeInteger]"
              :error="form.errors.has('GAME_CRASH_ANIMATION.clouds_count')"
              :error-messages="form.errors.get('GAME_CRASH_ANIMATION.clouds_count')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_CRASH_ANIMATION.clouds_count')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-card-text>
    <v-card-text v-else>
      <v-alert
        dense
        outlined
        text
        color="error"
      >
        {{ $t('This game requires Pusher.') }}
        <a href="/admin/help/app#q28">{{ $t('How to integrate Pusher?') }}</a>
      </v-alert>
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
        GAME_CRASH_CATEGORIES: config('crash.categories'),
        GAME_CRASH_BANNER: config('crash.banner'),
        GAME_CRASH_BACKGROUND: config('crash.background'),
        GAME_CRASH_MIN_BET: config('crash.min_bet'),
        GAME_CRASH_MAX_BET: config('crash.max_bet'),
        GAME_CRASH_BET_CHANGE_AMOUNT: config('crash.bet_change_amount'),
        GAME_CRASH_DEFAULT_BET_AMOUNT: config('crash.default_bet_amount'),
        GAME_CRASH_DURATION: config('crash.duration'),
        GAME_CRASH_INTERVAL: config('crash.interval'),
        GAME_CRASH_BASE_NUMBER: config('crash.base_number'),
        GAME_CRASH_BOTS_ENABLED: '' + config('crash.bots_enabled'),
        GAME_CRASH_PAYOUT_INTERVALS: config('crash.payout_intervals'),
        GAME_CRASH_ANIMATION: config('crash.animation')
      }
    }
  },

  computed: {
    pusherEnabled () {
      return this.form.PUSHER_APP_ID && this.form.PUSHER_APP_KEY && this.form.PUSHER_APP_SECRET && this.form.PUSHER_APP_CLUSTER
    }
  },

  created () {
    this.$emit('input', this.variables)
  },

  methods: {
    addPayoutInterval () {
      const intervals = this.variables.GAME_CRASH_PAYOUT_INTERVALS
      intervals.push(intervals[intervals.length - 1] + 1.00)
    },
    deletePayoutInterval (index) {
      this.variables.GAME_CRASH_PAYOUT_INTERVALS.splice(index, 1)
    }
  }
}
</script>

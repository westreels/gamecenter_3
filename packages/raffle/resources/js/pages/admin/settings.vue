<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model.number="form.DISPLAY_COUNT_HOME_PAGE"
              :label="$t('Max number of raffles on home page')"
              :rules="[validationPositiveInteger]"
              :error="form.errors.has('DISPLAY_COUNT_HOME_PAGE')"
              :error-messages="form.errors.get('DISPLAY_COUNT_HOME_PAGE')"
              outlined
              persistent-hint
              @keydown="clearFormErrors($event,'DISPLAY_COUNT_HOME_PAGE')"
            />

            <v-text-field
              v-model.number="form.RAFFLE_DISPLAY_COUNT"
              :label="$t('Display count')"
              :hint="$t('How many raffles are preloaded on the Current raffles page.')"
              :persistent-hint="true"
              :rules="[validationPositiveInteger]"
              :error="form.errors.has('RAFFLE_DISPLAY_COUNT')"
              :error-messages="form.errors.get('RAFFLE_DISPLAY_COUNT')"
              outlined
              @keydown="clearFormErrors($event,'RAFFLE_DISPLAY_COUNT')"
            />

            <v-text-field
              v-model.number="form.RAFFLE_LOAD_COUNT"
              :label="$t('Load count')"
              :hint="$t('How many raffles are loaded when \'Load more\' button is clicked.')"
              :persistent-hint="true"
              :rules="[validationPositiveInteger]"
              :error="form.errors.has('RAFFLE_LOAD_COUNT')"
              :error-messages="form.errors.get('RAFFLE_LOAD_COUNT')"
              outlined
              @keydown="clearFormErrors($event,'RAFFLE_LOAD_COUNT')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Bots') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-select
              v-model="form.RAFFLE_BOTS_FREQUENCY"
              :items="frequencies"
              :label="$t('Frequency')"
              :error="form.errors.has('RAFFLE_BOTS_FREQUENCY')"
              :error-messages="form.errors.get('RAFFLE_BOTS_FREQUENCY')"
              :hint="$t('How often bots will awake.')"
              persistent-hint
              outlined
            />

            <v-text-field
              v-model.number="form.RAFFLE_BOTS_MIN_COUNT"
              :label="$t('Min bots')"
              :rules="[v => validationNonNegativeInteger(parseInt(v, 10))]"
              :error="form.errors.has('RAFFLE_BOTS_MIN_COUNT')"
              :error-messages="form.errors.get('RAFFLE_BOTS_MIN_COUNT')"
              outlined
              :hint="$t('Minimum number of bots to buy a raffle ticket during each cycle.')"
              persistent-hint
              @keydown="clearFormErrors($event,'RAFFLE_BOTS_MIN_COUNT')"
            />

            <v-text-field
              v-model.number="form.RAFFLE_BOTS_MAX_COUNT"
              :label="$t('Max bots')"
              :rules="[v => validationNonNegativeInteger(parseInt(v, 10))]"
              :error="form.errors.has('RAFFLE_BOTS_MAX_COUNT')"
              :error-messages="form.errors.get('RAFFLE_BOTS_MAX_COUNT')"
              outlined
              :hint="$t('Maximum number of bots to buy a raffle ticket during each cycle.')"
              persistent-hint
              @keydown="clearFormErrors($event,'RAFFLE_BOTS_MAX_COUNT')"
            />

            <v-text-field
              v-model.number="form.RAFFLE_BOTS_MIN_TICKETS"
              :label="$t('Min tickets')"
              :error="form.errors.has('RAFFLE_BOTS_MIN_TICKETS')"
              :error-messages="form.errors.get('RAFFLE_BOTS_MIN_TICKETS')"
              outlined
              :hint="$t('Minimum number of tickets to buy.')"
              persistent-hint
              @keydown="clearFormErrors($event,'RAFFLE_BOTS_MIN_TICKETS')"
            />

            <v-text-field
              v-model.number="form.RAFFLE_BOTS_MAX_TICKETS"
              :label="$t('Max tickets')"
              :error="form.errors.has('RAFFLE_BOTS_MAX_TICKETS')"
              :error-messages="form.errors.get('RAFFLE_BOTS_MAX_TICKETS')"
              outlined
              :hint="$t('Maximum number of tickets to buy.')"
              persistent-hint
              @keydown="clearFormErrors($event,'RAFFLE_BOTS_MAX_TICKETS')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-card-text>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'
import FormMixin from '~/mixins/Form'

export default {
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
        DISPLAY_COUNT_HOME_PAGE: config('raffle.display_count_home_page'),
        RAFFLE_DISPLAY_COUNT: config('raffle.display_count'),
        RAFFLE_LOAD_COUNT: config('raffle.load_count'),
        RAFFLE_BOTS_FREQUENCY: config('raffle.bots.frequency'),
        RAFFLE_BOTS_MIN_COUNT: config('raffle.bots.min_count'),
        RAFFLE_BOTS_MAX_COUNT: config('raffle.bots.max_count'),
        RAFFLE_BOTS_MIN_TICKETS: config('raffle.bots.min_tickets'),
        RAFFLE_BOTS_MAX_TICKETS: config('raffle.bots.max_tickets')
      }
    }
  },

  computed: {
    frequencies () {
      return [
        { value: 'everyMinute', text: this.$t('Every minute') },
        { value: 'everyFiveMinutes', text: this.$t('Every five minutes') },
        { value: 'everyTenMinutes', text: this.$t('Every ten minutes') },
        { value: 'everyFifteenMinutes', text: this.$t('Every fifteen minutes') },
        { value: 'everyThirtyMinutes', text: this.$t('Every thirty minutes') },
        { value: 'hourly', text: this.$t('Every hour') }
      ]
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

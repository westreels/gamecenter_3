<template>
  <v-card flat>
    <v-card-text v-if="pusherEnabled">
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-combobox
              v-model="form.GAME_MULTIPLAYER_ROULETTE_CATEGORIES"
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
              v-model="form.GAME_MULTIPLAYER_ROULETTE_BANNER"
              :label="$t('Banner')"
              name="banner"
              folder="games/multiplayer-roulette"
            />

            <file-upload
              v-model="form.GAME_MULTIPLAYER_ROULETTE_BACKGROUND"
              :label="$t('Background image')"
              name="background"
              folder="games/multiplayer-roulette"
              :clearable="true"
            />

            <v-text-field
              v-model.number="form.GAME_MULTIPLAYER_ROULETTE_MIN_BET"
              :label="$t('Min bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_MULTIPLAYER_ROULETTE_MIN_BET')"
              :error-messages="form.errors.get('GAME_MULTIPLAYER_ROULETTE_MIN_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_MULTIPLAYER_ROULETTE_MIN_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_MULTIPLAYER_ROULETTE_MAX_BET"
              :label="$t('Max bet')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_MULTIPLAYER_ROULETTE_MAX_BET')"
              :error-messages="form.errors.get('GAME_MULTIPLAYER_ROULETTE_MAX_BET')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_MULTIPLAYER_ROULETTE_MAX_BET')"
            />

            <v-text-field
              v-model.number="form.GAME_MULTIPLAYER_ROULETTE_BET_CHANGE_AMOUNT"
              :label="$t('Bet increment / decrement amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_MULTIPLAYER_ROULETTE_BET_CHANGE_AMOUNT')"
              :error-messages="form.errors.get('GAME_MULTIPLAYER_ROULETTE_BET_CHANGE_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_MULTIPLAYER_ROULETTE_BET_CHANGE_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_MULTIPLAYER_ROULETTE_DEFAULT_BET_AMOUNT"
              :label="$t('Default bet amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_MULTIPLAYER_ROULETTE_DEFAULT_BET_AMOUNT')"
              :error-messages="form.errors.get('GAME_MULTIPLAYER_ROULETTE_DEFAULT_BET_AMOUNT')"
              outlined
              :suffix="$t('credits')"
              @keydown="clearFormErrors($event, 'GAME_MULTIPLAYER_ROULETTE_DEFAULT_BET_AMOUNT')"
            />

            <v-text-field
              v-model.number="form.GAME_MULTIPLAYER_ROULETTE_DURATION"
              :label="$t('Betting round duration')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_MULTIPLAYER_ROULETTE_DURATION')"
              :error-messages="form.errors.get('GAME_MULTIPLAYER_ROULETTE_DURATION')"
              outlined
              :suffix="$t('seconds')"
              @keydown="clearFormErrors($event, 'GAME_MULTIPLAYER_ROULETTE_DURATION')"
            />

            <v-text-field
              v-model.number="form.GAME_MULTIPLAYER_ROULETTE_INTERVAL"
              :label="$t('Delay before next game starts')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('GAME_MULTIPLAYER_ROULETTE_INTERVAL')"
              :error-messages="form.errors.get('GAME_MULTIPLAYER_ROULETTE_INTERVAL')"
              outlined
              :suffix="$t('seconds')"
              @keydown="clearFormErrors($event, 'GAME_MULTIPLAYER_ROULETTE_INTERVAL')"
            />

            <v-switch
              v-model="form.GAME_MULTIPLAYER_ROULETTE_BOTS_ENABLED"
              :label="$t('Enable bots')"
              color="primary"
              false-value="false"
              true-value="true"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Paytable') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model.number="form.GAME_MULTIPLAYER_ROULETTE_PAYTABLE.red"
              :label="$t('Red')"
              :rules="[validationPositiveNumber]"
              outlined
              :prefix="$t('Bet') + ' x '"
              @keydown="clearFormErrors($event, 'GAME_MULTIPLAYER_ROULETTE_PAYOUT_PLAYER.red')"
            />

            <v-text-field
              v-model.number="form.GAME_MULTIPLAYER_ROULETTE_PAYTABLE.black"
              :label="$t('Black')"
              :rules="[validationPositiveNumber]"
              outlined
              :prefix="$t('Bet') + ' x '"
              @keydown="clearFormErrors($event, 'GAME_MULTIPLAYER_ROULETTE_PAYOUT_PLAYER.black')"
            />

            <v-text-field
              v-model.number="form.GAME_MULTIPLAYER_ROULETTE_PAYTABLE.zero"
              :label="$t('Zero')"
              :rules="[validationPositiveNumber]"
              outlined
              :prefix="$t('Bet') + ' x '"
              @keydown="clearFormErrors($event, 'GAME_MULTIPLAYER_ROULETTE_PAYOUT_PLAYER.zero')"
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
        GAME_MULTIPLAYER_ROULETTE_CATEGORIES: config('multiplayer-roulette.categories'),
        GAME_MULTIPLAYER_ROULETTE_BANNER: config('multiplayer-roulette.banner'),
        GAME_MULTIPLAYER_ROULETTE_BACKGROUND: config('multiplayer-roulette.background'),
        GAME_MULTIPLAYER_ROULETTE_MIN_BET: config('multiplayer-roulette.min_bet'),
        GAME_MULTIPLAYER_ROULETTE_MAX_BET: config('multiplayer-roulette.max_bet'),
        GAME_MULTIPLAYER_ROULETTE_BET_CHANGE_AMOUNT: config('multiplayer-roulette.bet_change_amount'),
        GAME_MULTIPLAYER_ROULETTE_DEFAULT_BET_AMOUNT: config('multiplayer-roulette.default_bet_amount'),
        GAME_MULTIPLAYER_ROULETTE_DURATION: config('multiplayer-roulette.duration'),
        GAME_MULTIPLAYER_ROULETTE_INTERVAL: config('multiplayer-roulette.interval'),
        GAME_MULTIPLAYER_ROULETTE_BOTS_ENABLED: '' + config('crash.bots_enabled'),
        GAME_MULTIPLAYER_ROULETTE_PAYTABLE: config('multiplayer-roulette.paytable')
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
  }
}
</script>

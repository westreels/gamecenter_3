<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('BGaming') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model="form.GAME_PROVIDERS_BGAMING_API_ID"
              :label="$t('Casino ID')"
              :rules="[validationRequired]"
              :error="form.errors.has('GAME_PROVIDERS_BGAMING_API_ID')"
              :error-messages="form.errors.get('GAME_PROVIDERS_BGAMING_API_ID')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_PROVIDERS_BGAMING_API_ID')"
            />

            <v-text-field
              v-model="form.GAME_PROVIDERS_BGAMING_API_ENDPOINT"
              :label="$t('Endpoint')"
              :rules="[validationRequired]"
              :error="form.errors.has('GAME_PROVIDERS_BGAMING_API_ENDPOINT')"
              :error-messages="form.errors.get('GAME_PROVIDERS_BGAMING_API_ENDPOINT')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_PROVIDERS_BGAMING_API_ENDPOINT')"
            />

            <v-text-field
              v-model="form.GAME_PROVIDERS_BGAMING_API_SECRET_KEY"
              :label="$t('Auth token')"
              :rules="[validationRequired]"
              :error="form.errors.has('GAME_PROVIDERS_BGAMING_API_SECRET_KEY')"
              :error-messages="form.errors.get('GAME_PROVIDERS_BGAMING_API_SECRET_KEY')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_PROVIDERS_BGAMING_API_SECRET_KEY')"
            />

            <v-text-field
              ref="bgamingCallbackUrl"
              :value="bgamingCallbackUrl"
              :label="$t('Callback URL')"
              :persistent-hint="true"
              :hint="$t('The callback URL should be provided to BGaming support')"
              readonly
              outlined
              append-icon="mdi-content-copy"
              @click:append="copyToClipboard($refs.bgamingCallbackUrl.$el.querySelector('input'))"
            />

            <v-text-field
              v-model="form.GAME_PROVIDERS_BGAMING_CURRENCY_CODE"
              :label="$t('Currency')"
              :rules="[validationRequired]"
              :error="form.errors.has('GAME_PROVIDERS_BGAMING_CURRENCY_CODE')"
              :error-messages="form.errors.get('GAME_PROVIDERS_BGAMING_CURRENCY_CODE')"
              outlined
              class="mt-5"
              @keydown="clearFormErrors($event, 'GAME_PROVIDERS_BGAMING_CURRENCY_CODE')"
            />

            <v-text-field
              v-model.number="form.GAME_PROVIDERS_BGAMING_CURRENCY_DECIMALS"
              :label="$t('Number of decimals')"
              :rules="[validationNonNegativeInteger]"
              :error="form.errors.has('GAME_PROVIDERS_BGAMING_CURRENCY_DECIMALS')"
              :error-messages="form.errors.get('GAME_PROVIDERS_BGAMING_CURRENCY_DECIMALS')"
              :persistent-hint="true"
              :hint="$t('Most fiat currencies have 2 decimals, most cryptocurrencies have 8 decimals.')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_PROVIDERS_BGAMING_CURRENCY_DECIMALS')"
            />

            <v-text-field
              v-model.number="form.GAME_PROVIDERS_BGAMING_CURRENCY_RATE"
              :label="$t('Conversion rate')"
              type="text"
              :disabled="!form.GAME_PROVIDERS_BGAMING_CURRENCY_CODE"
              :rules="[validationRequired, validationPositiveNumber]"
              :error="form.errors.has('GAME_PROVIDERS_BGAMING_CURRENCY_RATE')"
              :error-messages="form.errors.get('GAME_PROVIDERS_BGAMING_CURRENCY_RATE')"
              :prefix="form.GAME_PROVIDERS_BGAMING_CURRENCY_CODE ? '1 ' + form.GAME_PROVIDERS_BGAMING_CURRENCY_CODE + ' = ' : ''"
              :suffix="$t('credits')"
              :hint="$t('Amount of credits per 1 unit of the reference currency')"
              :persistent-hint="true"
              outlined
              class="mt-5"
              @keydown="clearFormErrors($event,'GAME_PROVIDERS_BGAMING_CURRENCY_RATE')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Evoplay') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model="form.GAME_PROVIDERS_EVOPLAY_API_ID"
              :label="$t('Project ID')"
              :rules="[validationRequired]"
              :error="form.errors.has('GAME_PROVIDERS_EVOPLAY_API_ID')"
              :error-messages="form.errors.get('GAME_PROVIDERS_EVOPLAY_API_ID')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_PROVIDERS_EVOPLAY_API_ID')"
            />

            <v-text-field
              v-model="form.GAME_PROVIDERS_EVOPLAY_API_SECRET_KEY"
              :label="$t('Secret key')"
              :rules="[validationRequired]"
              :error="form.errors.has('GAME_PROVIDERS_EVOPLAY_API_SECRET_KEY')"
              :error-messages="form.errors.get('GAME_PROVIDERS_EVOPLAY_API_SECRET_KEY')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_PROVIDERS_EVOPLAY_API_SECRET_KEY')"
            />

            <v-text-field
              ref="evoplayCallbackUrl"
              :value="evoplayCallbackUrl"
              :label="$t('Callback URL')"
              :persistent-hint="true"
              :hint="$t('The callback URL should be inserted in Evoplay backend (Evoplay Entertainment --> Casino Accounts --> double click on an account)')"
              readonly
              outlined
              append-icon="mdi-content-copy"
              @click:append="copyToClipboard($refs.evoplayCallbackUrl.$el.querySelector('input'))"
            />

            <v-text-field
              v-model="form.GAME_PROVIDERS_EVOPLAY_CURRENCY_CODE"
              :label="$t('Currency')"
              :rules="[validationRequired]"
              :error="form.errors.has('GAME_PROVIDERS_EVOPLAY_CURRENCY_CODE')"
              :error-messages="form.errors.get('GAME_PROVIDERS_EVOPLAY_CURRENCY_CODE')"
              outlined
              class="mt-5"
              @keydown="clearFormErrors($event, 'GAME_PROVIDERS_EVOPLAY_CURRENCY_CODE')"
            />

            <v-text-field
              v-model.number="form.GAME_PROVIDERS_EVOPLAY_CURRENCY_DECIMALS"
              :label="$t('Number of decimals')"
              :rules="[validationNonNegativeInteger]"
              :error="form.errors.has('GAME_PROVIDERS_EVOPLAY_CURRENCY_DECIMALS')"
              :error-messages="form.errors.get('GAME_PROVIDERS_EVOPLAY_CURRENCY_DECIMALS')"
              :persistent-hint="true"
              :hint="$t('Most fiat currencies have 2 decimals, most cryptocurrencies have 8 decimals.')"
              outlined
              @keydown="clearFormErrors($event, 'GAME_PROVIDERS_EVOPLAY_CURRENCY_DECIMALS')"
            />

            <v-text-field
              v-model.number="form.GAME_PROVIDERS_EVOPLAY_CURRENCY_RATE"
              :label="$t('Conversion rate')"
              type="text"
              :disabled="!form.GAME_PROVIDERS_EVOPLAY_CURRENCY_CODE"
              :rules="[validationRequired, validationPositiveNumber]"
              :error="form.errors.has('GAME_PROVIDERS_EVOPLAY_CURRENCY_RATE')"
              :error-messages="form.errors.get('GAME_PROVIDERS_EVOPLAY_CURRENCY_RATE')"
              :prefix="form.GAME_PROVIDERS_EVOPLAY_CURRENCY_CODE ? '1 ' + form.GAME_PROVIDERS_EVOPLAY_CURRENCY_CODE + ' = ' : ''"
              :suffix="$t('credits')"
              :hint="$t('Amount of credits per 1 unit of the reference currency')"
              :persistent-hint="true"
              outlined
              @keydown="clearFormErrors($event,'GAME_PROVIDERS_EVOPLAY_CURRENCY_RATE')"
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
import { baseUrl, copyToClipboard } from '~/plugins/utils'

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
        GAME_PROVIDERS_BGAMING_API_ID: config('game-providers.providers.bgaming.api.id'),
        GAME_PROVIDERS_BGAMING_API_SECRET_KEY: config('game-providers.providers.bgaming.api.secret_key'),
        GAME_PROVIDERS_BGAMING_API_ENDPOINT: config('game-providers.providers.bgaming.api.endpoint'),
        GAME_PROVIDERS_BGAMING_CURRENCY_CODE: config('game-providers.providers.bgaming.currency.code'),
        GAME_PROVIDERS_BGAMING_CURRENCY_DECIMALS: config('game-providers.providers.bgaming.currency.decimals'),
        GAME_PROVIDERS_BGAMING_CURRENCY_RATE: config('game-providers.providers.bgaming.currency.rate'),
        GAME_PROVIDERS_EVOPLAY_API_ID: config('game-providers.providers.evoplay.api.id'),
        GAME_PROVIDERS_EVOPLAY_API_SECRET_KEY: config('game-providers.providers.evoplay.api.secret_key'),
        GAME_PROVIDERS_EVOPLAY_CURRENCY_CODE: config('game-providers.providers.evoplay.currency.code'),
        GAME_PROVIDERS_EVOPLAY_CURRENCY_DECIMALS: config('game-providers.providers.evoplay.currency.decimals'),
        GAME_PROVIDERS_EVOPLAY_CURRENCY_RATE: config('game-providers.providers.evoplay.currency.rate')
      }
    }
  },

  computed: {
    bgamingCallbackUrl () {
      return baseUrl() + '/api/providers/bgaming'
    },
    evoplayCallbackUrl () {
      return baseUrl() + '/api/providers/evoplay/play'
    }
  },

  created () {
    this.$emit('input', this.variables)
  },

  methods: {
    copyToClipboard
  }
}
</script>

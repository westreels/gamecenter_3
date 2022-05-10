<template>
  <v-card flat>
    <v-card-text>
      <file-upload
        v-model="form.CRYPTO_PREDICTION_BANNER"
        :label="$t('Banner')"
        name="banner"
        folder="games/crypto-prediction"
      />

      <file-upload
        v-model="form.CRYPTO_PREDICTION_BACKGROUND"
        :label="$t('Background image')"
        name="background"
        folder="games/crypto-prediction"
        :clearable="true"
      />

      <v-text-field
        v-model.number="form.CRYPTO_PREDICTION_MIN_BET"
        :label="$t('Min bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('CRYPTO_PREDICTION_MIN_BET')"
        :error-messages="form.errors.get('CRYPTO_PREDICTION_MIN_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'CRYPTO_PREDICTION_MIN_BET')"
      />

      <v-text-field
        v-model.number="form.CRYPTO_PREDICTION_MAX_BET"
        :label="$t('Max bet')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('CRYPTO_PREDICTION_MAX_BET')"
        :error-messages="form.errors.get('CRYPTO_PREDICTION_MAX_BET')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'CRYPTO_PREDICTION_MAX_BET')"
      />

      <v-text-field
        v-model.number="form.CRYPTO_PREDICTION_BET_CHANGE_AMOUNT"
        :label="$t('Bet increment / decrement amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('CRYPTO_PREDICTION_BET_CHANGE_AMOUNT')"
        :error-messages="form.errors.get('CRYPTO_PREDICTION_BET_CHANGE_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'CRYPTO_PREDICTION_BET_CHANGE_AMOUNT')"
      />

      <v-text-field
        v-model.number="form.CRYPTO_PREDICTION_DEFAULT_BET_AMOUNT"
        :label="$t('Default bet amount')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('CRYPTO_PREDICTION_DEFAULT_BET_AMOUNT')"
        :error-messages="form.errors.get('CRYPTO_PREDICTION_DEFAULT_BET_AMOUNT')"
        outlined
        :suffix="$t('credits')"
        @keydown="clearFormErrors($event, 'CRYPTO_PREDICTION_DEFAULT_BET_AMOUNT')"
      />

      <v-combobox
        v-model="form.CRYPTO_PREDICTION_DURATIONS"
        :label="$t('Durations')"
        :items="availableDurations"
        item-text="text"
        item-value="value"
        multiple
        outlined
        chips
        small-chips
        deletable-chips
        hide-selected
        no-filter
      />

      <asset-search-autocomplete
        type="crypto"
        :label="$t('Default asset')"
        :default-asset-name="variables.CRYPTO_PREDICTION_DEFAULT_ASSET_NAME"
        @change="form.CRYPTO_PREDICTION_DEFAULT_ASSET_NAME = $event.name"
      />

      <v-text-field
        v-model.number="form.CRYPTO_PREDICTION_ASSET_MAX_RANK"
        :label="$t('Asset rank limit')"
        :rules="[validationInteger, v => validationMin(v, 1), v => validationMax(v, 100)]"
        :error="form.errors.has('CRYPTO_PREDICTION_ASSET_MAX_RANK')"
        :error-messages="form.errors.get('CRYPTO_PREDICTION_ASSET_MAX_RANK')"
        outlined
        :hint="$t('Lower ranked assets will not be available for play.') + ' ' + $t('Market data provider can be selected in Services » Crypto API.')"
        persistent-hint
        class="mb-5"
        @keydown="clearFormErrors($event, 'CRYPTO_PREDICTION_ASSET_MAX_RANK')"
      />

      <v-text-field
        v-model.number="form.CRYPTO_PREDICTION_PRICE_UPDATE_INTERVAL"
        :label="$t('Price refresh interval')"
        :rules="[validationInteger, validationPositiveNumber]"
        :error="form.errors.has('CRYPTO_PREDICTION_PRICE_UPDATE_INTERVAL')"
        :error-messages="form.errors.get('CRYPTO_PREDICTION_PRICE_UPDATE_INTERVAL')"
        outlined
        :prefix="$t('Every')"
        :suffix="$t('seconds')"
        :hint="$t('Market data source: {0}', ['coincap.io'])"
        persistent-hint
        class="mb-5"
        @keydown="clearFormErrors($event, 'CRYPTO_PREDICTION_PRICE_UPDATE_INTERVAL')"
      />

      <v-row no-gutters>
        <v-col cols="12" lg="6" class="pr-lg-2">
          <v-text-field
            v-model.number="form.CRYPTO_PREDICTION_PAYTABLE[1]"
            :label="`${$t('Payout')} (${$t('Higher')})`"
            :rules="[validationPositiveNumber]"
            :error="form.errors.has('CRYPTO_PREDICTION_PAYTABLE')"
            :error-messages="form.errors.get('CRYPTO_PREDICTION_PAYTABLE')"
            outlined
            :prefix="`${$t('Bet')} x`"
            :suffix="$t('credits')"
            @keydown="clearFormErrors($event, 'CRYPTO_PREDICTION_PAYTABLE')"
          />
        </v-col>
        <v-col cols="12" lg="6" class="pl-lg-2">
          <v-text-field
            v-model.number="form.CRYPTO_PREDICTION_PAYTABLE[-1]"
            :label="`${$t('Payout')} (${$t('Lower')})`"
            :rules="[validationPositiveNumber]"
            :error="form.errors.has('CRYPTO_PREDICTION_PAYTABLE')"
            :error-messages="form.errors.get('CRYPTO_PREDICTION_PAYTABLE')"
            outlined
            :prefix="`${$t('Bet')} x`"
            :suffix="$t('credits')"
            @keydown="clearFormErrors($event, 'CRYPTO_PREDICTION_PAYTABLE')"
          />
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'
import FormMixin from '~/mixins/Form'
import FileUpload from '~/components/Admin/FileUpload'
import AssetSearchAutocomplete from '~/components/AssetSearchAutocomplete'

export default {
  components: { AssetSearchAutocomplete, FileUpload },
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
        CRYPTO_PREDICTION_BANNER: config('crypto-prediction.banner'),
        CRYPTO_PREDICTION_BACKGROUND: config('crypto-prediction.background'),
        CRYPTO_PREDICTION_MIN_BET: parseInt(config('crypto-prediction.min_bet'), 10),
        CRYPTO_PREDICTION_MAX_BET: parseInt(config('crypto-prediction.max_bet'), 10),
        CRYPTO_PREDICTION_BET_CHANGE_AMOUNT: parseInt(config('crypto-prediction.bet_change_amount'), 10),
        CRYPTO_PREDICTION_DEFAULT_BET_AMOUNT: parseInt(config('crypto-prediction.default_bet_amount'), 10),
        CRYPTO_PREDICTION_DEFAULT_ASSET_NAME: config('crypto-prediction.default_asset_name'),
        CRYPTO_PREDICTION_ASSET_MAX_RANK: parseInt(config('crypto-prediction.asset_max_rank'), 10),
        CRYPTO_PREDICTION_DURATIONS: config('crypto-prediction.durations'),
        CRYPTO_PREDICTION_PAYTABLE: config('crypto-prediction.paytable'),
        CRYPTO_PREDICTION_PRICE_UPDATE_INTERVAL: parseInt(config('crypto-prediction.price_update_interval'), 10)
      }
    }
  },

  computed: {
    availableDurations () {
      return config('crypto-prediction.available_durations')
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

<template>
  <v-card flat>
    <v-card-text>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Notifications') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-expansion-panels>
              <v-expansion-panel>
                <v-expansion-panel-header>{{ $t('Admin') }}</v-expansion-panel-header>
                <v-expansion-panel-content>
                  <v-row>
                    <v-col>
                      <v-switch
                        v-model="form.PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_ENABLED"
                        :label="$t('Notify after a user completes a deposit')"
                        color="primary"
                        false-value="false"
                        true-value="true"
                      />
                    </v-col>
                    <v-col>
                      <v-text-field
                        v-model.number="form.PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_TRESHOLD"
                        :label="$t('Deposit amount treshold')"
                        :error="form.errors.has('PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_TRESHOLD')"
                        :error-messages="form.errors.get('PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_TRESHOLD')"
                        :rules="'' + form.PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_ENABLED === 'false' ? [] : [validationNonNegativeInteger]"
                        :disabled="'' + form.PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_ENABLED === 'false'"
                        outlined
                        :suffix="$t('credits')"
                        @keydown="clearFormErrors($event,'PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_TRESHOLD')"
                      />
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col>
                      <v-switch
                        v-model="form.PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_ENABLED"
                        :label="$t('Notify after a user creates a withdrawal')"
                        color="primary"
                        false-value="false"
                        true-value="true"
                      />
                    </v-col>
                    <v-col>
                      <v-text-field
                        v-model.number="form.PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_TRESHOLD"
                        :label="$t('Deposit amount treshold')"
                        :error="form.errors.has('PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_TRESHOLD')"
                        :error-messages="form.errors.get('PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_TRESHOLD')"
                        :rules="'' + form.PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_ENABLED === 'false' ? [] : [validationNonNegativeInteger]"
                        :disabled="'' + form.PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_ENABLED === 'false'"
                        outlined
                        :suffix="$t('credits')"
                        @keydown="clearFormErrors($event,'PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_TRESHOLD')"
                      />
                    </v-col>
                  </v-row>
                </v-expansion-panel-content>
              </v-expansion-panel>
            </v-expansion-panels>
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Deposits') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model.number="form.PAYMENTS_DEPOSIT_MIN"
              :label="$t('Min deposit amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('PAYMENTS_DEPOSIT_MIN')"
              :error-messages="form.errors.get('PAYMENTS_DEPOSIT_MIN')"
              :suffix="$t('credits')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_DEPOSIT_MIN')"
            />

            <v-text-field
              v-model.number="form.PAYMENTS_DEPOSIT_MAX"
              :label="$t('Max deposit amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('PAYMENTS_DEPOSIT_MAX')"
              :error-messages="form.errors.get('PAYMENTS_DEPOSIT_MAX')"
              :suffix="$t('credits')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_DEPOSIT_MAX')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Withdrawals') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model.number="form.PAYMENTS_WITHDRAWAL_MIN"
              :label="$t('Min withdrawal amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('PAYMENTS_WITHDRAWAL_MIN')"
              :error-messages="form.errors.get('PAYMENTS_WITHDRAWAL_MIN')"
              :suffix="$t('credits')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_WITHDRAWAL_MIN')"
            />

            <v-text-field
              v-model.number="form.PAYMENTS_WITHDRAWAL_MAX"
              :label="$t('Max withdrawal amount')"
              :rules="[validationInteger, validationPositiveNumber]"
              :error="form.errors.has('PAYMENTS_WITHDRAWAL_MAX')"
              :error-messages="form.errors.get('PAYMENTS_WITHDRAWAL_MAX')"
              :suffix="$t('credits')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_WITHDRAWAL_MAX')"
            />

            <v-text-field
              v-model.number="form.PAYMENTS_WITHDRAWAL_DAILY_LIMIT"
              :label="$t('Daily withdrawal limit')"
              :rules="[validationInteger]"
              :error="form.errors.has('PAYMENTS_WITHDRAWAL_DAILY_LIMIT')"
              :error-messages="form.errors.get('PAYMENTS_WITHDRAWAL_DAILY_LIMIT')"
              :suffix="$t('credits')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_WITHDRAWAL_DAILY_LIMIT')"
            />

            <v-text-field
              v-model.number="form.PAYMENTS_WITHDRAWAL_WEEKLY_LIMIT"
              :label="$t('Weekly withdrawal limit')"
              :rules="[validationInteger]"
              :error="form.errors.has('PAYMENTS_WITHDRAWAL_WEEKLY_LIMIT')"
              :error-messages="form.errors.get('PAYMENTS_WITHDRAWAL_WEEKLY_LIMIT')"
              :suffix="$t('credits')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_WITHDRAWAL_WEEKLY_LIMIT')"
            />

            <v-text-field
              v-model.number="form.PAYMENTS_WITHDRAWAL_MONTHLY_LIMIT"
              :label="$t('Monthly withdrawal limit')"
              :rules="[validationInteger]"
              :error="form.errors.has('PAYMENTS_WITHDRAWAL_MONTHLY_LIMIT')"
              :error-messages="form.errors.get('PAYMENTS_WITHDRAWAL_MONTHLY_LIMIT')"
              :suffix="$t('credits')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_WITHDRAWAL_MONTHLY_LIMIT')"
            />

            <v-text-field
              v-model.number="form.PAYMENTS_MIN_TOTAL_DEPOSIT_TO_WITHDRAW"
              :label="$t('Min total deposit to allow withdrawals')"
              :rules="[validationInteger]"
              :error="form.errors.has('PAYMENTS_MIN_TOTAL_DEPOSIT_TO_WITHDRAW')"
              :error-messages="form.errors.get('PAYMENTS_MIN_TOTAL_DEPOSIT_TO_WITHDRAW')"
              :suffix="$t('credits')"
              :hint="minDepositAmountToWithdrawHint"
              :persistent-hint="true"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_MIN_TOTAL_DEPOSIT_TO_WITHDRAW')"
            />

            <v-switch
              v-model="form.PAYMENTS_WITHDRAWAL_ONLY_PROFITS"
              :label="$t('Allow only profit withdrawals')"
              color="primary"
              false-value="false"
              true-value="true"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Paypal') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model="form.PAYMENTS_PAYPAL_USER"
              :label="$t('API username')"
              :error="form.errors.has('PAYMENTS_PAYPAL_USER')"
              :error-messages="form.errors.get('PAYMENTS_PAYPAL_USER')"
              outlined
              :hint="$t('Please note, it is different from your regular Paypal account username')"
              persistent-hint
              @keydown="clearFormErrors($event,'PAYMENTS_PAYPAL_USER')"
            />

            <v-text-field
              v-model="form.PAYMENTS_PAYPAL_PASSWORD"
              :label="$t('API password')"
              :error="form.errors.has('PAYMENTS_PAYPAL_PASSWORD')"
              :error-messages="form.errors.get('PAYMENTS_PAYPAL_PASSWORD')"
              outlined
              :hint="$t('Please note, it is different from your regular Paypal account password')"
              persistent-hint
              @keydown="clearFormErrors($event,'PAYMENTS_PAYPAL_PASSWORD')"
            />

            <v-text-field
              v-model="form.PAYMENTS_PAYPAL_SIGNATURE"
              :label="$t('API signature')"
              :error="form.errors.has('PAYMENTS_PAYPAL_SIGNATURE')"
              :error-messages="form.errors.get('PAYMENTS_PAYPAL_SIGNATURE')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_PAYPAL_SIGNATURE')"
            />

            <v-switch
              v-model="form.PAYMENTS_PAYPAL_TEST_MODE"
              :label="$t('Test mode')"
              color="primary"
              false-value="false"
              true-value="true"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Stripe') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model="form.PAYMENTS_STRIPE_PUBLIC_KEY"
              :label="$t('Public key')"
              :error="form.errors.has('PAYMENTS_STRIPE_PUBLIC_KEY')"
              :error-messages="form.errors.get('PAYMENTS_STRIPE_PUBLIC_KEY')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_STRIPE_PUBLIC_KEY')"
            />

            <v-text-field
              v-model="form.PAYMENTS_STRIPE_SECRET_KEY"
              :label="$t('Secret key')"
              :error="form.errors.has('PAYMENTS_STRIPE_SECRET_KEY')"
              :error-messages="form.errors.get('PAYMENTS_STRIPE_SECRET_KEY')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_STRIPE_SECRET_KEY')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Coinpayments') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model="form.PAYMENTS_COINPAYMENTS_MERCHANT_ID"
              :label="$t('Merchant ID')"
              :error="form.errors.has('PAYMENTS_COINPAYMENTS_MERCHANT_ID')"
              :error-messages="form.errors.get('PAYMENTS_COINPAYMENTS_MERCHANT_ID')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_COINPAYMENTS_MERCHANT_ID')"
            />

            <v-text-field
              v-model="form.PAYMENTS_COINPAYMENTS_PUBLIC_KEY"
              :label="$t('Public key')"
              :error="form.errors.has('PAYMENTS_COINPAYMENTS_PUBLIC_KEY')"
              :error-messages="form.errors.get('PAYMENTS_COINPAYMENTS_PUBLIC_KEY')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_COINPAYMENTS_PUBLIC_KEY')"
            />

            <v-text-field
              v-model="form.PAYMENTS_COINPAYMENTS_PRIVATE_KEY"
              :label="$t('Private key')"
              :error="form.errors.has('PAYMENTS_COINPAYMENTS_PRIVATE_KEY')"
              :error-messages="form.errors.get('PAYMENTS_COINPAYMENTS_PRIVATE_KEY')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_COINPAYMENTS_PRIVATE_KEY')"
            />

            <v-text-field
              v-model="form.PAYMENTS_COINPAYMENTS_SECRET_KEY"
              :label="$t('Secret key')"
              :error="form.errors.has('PAYMENTS_COINPAYMENTS_SECRET_KEY')"
              :error-messages="form.errors.get('PAYMENTS_COINPAYMENTS_SECRET_KEY')"
              outlined
              @keydown="clearFormErrors($event,'PAYMENTS_COINPAYMENTS_SECRET_KEY')"
            />

            <v-text-field
              v-model.number="form.PAYMENTS_WITHDRAWAL_AUTO_MAX"
              :label="$t('Max auto withdrawal')"
              :rules="[validationInteger]"
              :error="form.errors.has('PAYMENTS_WITHDRAWAL_AUTO_MAX')"
              :error-messages="form.errors.get('PAYMENTS_WITHDRAWAL_AUTO_MAX')"
              :suffix="$t('credits')"
              :hint="maxAutoWithdrawalHint"
              :persistent-hint="true"
              outlined
              class="mb-5"
              @keydown="clearFormErrors($event,'PAYMENTS_WITHDRAWAL_AUTO_MAX')"
            />

            <v-switch
              v-model="form.PAYMENTS_COINPAYMENTS_AUTO_CONFIRM_WITHDRAWALS"
              :label="$t('Auto confirm withdrawals')"
              color="primary"
              false-value="false"
              true-value="true"
              :hint="autoConfirmWithdrawalsHint"
              :persistent-hint="true"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Ethereum') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model="form.PAYMENTS_ETHEREUM_ETHERSCAN_API_KEY"
              :label="$t('Etherscan.io API key')"
              :error="form.errors.has('PAYMENTS_ETHEREUM_ETHERSCAN_API_KEY')"
              :error-messages="form.errors.get('PAYMENTS_ETHEREUM_ETHERSCAN_API_KEY')"
              outlined
              :hint="$t('Required to fetch transactions info from the blockchain.')"
              :persistent-hint="true"
              class="mb-5"
              @keydown="clearFormErrors($event,'PAYMENTS_ETHEREUM_ETHERSCAN_API_KEY')"
            />

            <v-select
              v-model="form.PAYMENTS_ETHEREUM_NETWORK"
              :items="ethereumNetworks"
              :label="$t('Network')"
              :error="form.errors.has('PAYMENTS_ETHEREUM_NETWORK')"
              :error-messages="form.errors.get('PAYMENTS_ETHEREUM_NETWORK')"
              outlined
              @change="clearFormErrors($event,'PAYMENTS_ETHEREUM_NETWORK')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Binance Smart Chain') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model="form.PAYMENTS_BSC_EXPLORER_API_KEY"
              :label="$t('Bscscan.com API key')"
              :error="form.errors.has('PAYMENTS_BSC_EXPLORER_API_KEY')"
              :error-messages="form.errors.get('PAYMENTS_BSC_EXPLORER_API_KEY')"
              outlined
              :hint="$t('Required to fetch transactions info from the blockchain.')"
              :persistent-hint="true"
              class="mb-5"
              @keydown="clearFormErrors($event,'PAYMENTS_BSC_EXPLORER_API_KEY')"
            />

            <v-select
              v-model="form.PAYMENTS_BSC_NETWORK"
              :items="bscNetworks"
              :label="$t('Network')"
              :error="form.errors.has('PAYMENTS_BSC_NETWORK')"
              :error-messages="form.errors.get('PAYMENTS_BSC_NETWORK')"
              outlined
              @change="clearFormErrors($event,'PAYMENTS_BSC_NETWORK')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Polygon') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-text-field
              v-model="form.PAYMENTS_POLYGON_EXPLORER_API_KEY"
              :label="$t('Polygonscan.com API key')"
              :error="form.errors.has('PAYMENTS_POLYGON_EXPLORER_API_KEY')"
              :error-messages="form.errors.get('PAYMENTS_POLYGON_EXPLORER_API_KEY')"
              outlined
              :hint="$t('Required to fetch transactions info from the blockchain.')"
              :persistent-hint="true"
              class="mb-5"
              @keydown="clearFormErrors($event,'PAYMENTS_POLYGON_EXPLORER_API_KEY')"
            />

            <v-select
              v-model="form.PAYMENTS_POLYGON_NETWORK"
              :items="polygonNetworks"
              :label="$t('Network')"
              :error="form.errors.has('PAYMENTS_POLYGON_NETWORK')"
              :error-messages="form.errors.get('PAYMENTS_POLYGON_NETWORK')"
              outlined
              @change="clearFormErrors($event,'PAYMENTS_POLYGON_NETWORK')"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
        <v-expansion-panel>
          <v-expansion-panel-header>{{ $t('Tron') }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-select
              v-model="form.PAYMENTS_TRON_NETWORK"
              :items="tronNetworks"
              :label="$t('Network')"
              :error="form.errors.has('PAYMENTS_TRON_NETWORK')"
              :error-messages="form.errors.get('PAYMENTS_TRON_NETWORK')"
              outlined
              @change="clearFormErrors($event,'PAYMENTS_TRON_NETWORK')"
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
        PAYMENTS_DEPOSIT_MIN: parseInt(config('payments.deposit_min'), 10),
        PAYMENTS_DEPOSIT_MAX: parseInt(config('payments.deposit_max'), 10),
        PAYMENTS_WITHDRAWAL_MIN: parseInt(config('payments.withdrawal_min'), 10),
        PAYMENTS_WITHDRAWAL_MAX: parseInt(config('payments.withdrawal_max'), 10),
        PAYMENTS_WITHDRAWAL_DAILY_LIMIT: parseInt(config('payments.withdrawal_daily_limit'), 10),
        PAYMENTS_WITHDRAWAL_WEEKLY_LIMIT: parseInt(config('payments.withdrawal_weekly_limit'), 10),
        PAYMENTS_WITHDRAWAL_MONTHLY_LIMIT: parseInt(config('payments.withdrawal_monthly_limit'), 10),
        PAYMENTS_WITHDRAWAL_AUTO_MAX: parseInt(config('payments.withdrawal_auto_max'), 10),
        PAYMENTS_WITHDRAWAL_ONLY_PROFITS: '' + config('payments.withdrawal_only_profits'),
        PAYMENTS_MIN_TOTAL_DEPOSIT_TO_WITHDRAW: config('payments.min_total_deposit_to_withdraw'),
        PAYMENTS_PAYPAL_USER: config('payments.paypal.user'),
        PAYMENTS_PAYPAL_PASSWORD: config('payments.paypal.password'),
        PAYMENTS_PAYPAL_SIGNATURE: config('payments.paypal.signature'),
        PAYMENTS_PAYPAL_TEST_MODE: '' + config('payments.paypal.test_mode'),
        PAYMENTS_STRIPE_PUBLIC_KEY: config('payments.stripe.public_key'),
        PAYMENTS_STRIPE_SECRET_KEY: config('payments.stripe.secret_key'),
        PAYMENTS_COINPAYMENTS_MERCHANT_ID: config('payments.coinpayments.merchant_id'),
        PAYMENTS_COINPAYMENTS_PUBLIC_KEY: config('payments.coinpayments.public_key'),
        PAYMENTS_COINPAYMENTS_PRIVATE_KEY: config('payments.coinpayments.private_key'),
        PAYMENTS_COINPAYMENTS_SECRET_KEY: config('payments.coinpayments.secret_key'),
        PAYMENTS_COINPAYMENTS_AUTO_CONFIRM_WITHDRAWALS: '' + config('payments.coinpayments.withdrawals_auto_confirm'),
        PAYMENTS_ETHEREUM_ETHERSCAN_API_KEY: config('payments.ethereum.api_key'),
        PAYMENTS_ETHEREUM_NETWORK: config('payments.ethereum.network'),
        PAYMENTS_BSC_EXPLORER_API_KEY: config('payments.bsc.api_key'),
        PAYMENTS_BSC_NETWORK: config('payments.bsc.network'),
        PAYMENTS_POLYGON_EXPLORER_API_KEY: config('payments.polygon.api_key'),
        PAYMENTS_POLYGON_NETWORK: config('payments.polygon.network'),
        PAYMENTS_TRON_NETWORK: config('payments.tron.network'),
        PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_ENABLED: '' + config('payments.notifications.admin.deposit.enabled'),
        PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_TRESHOLD: parseInt(config('payments.notifications.admin.deposit.treshold'), 10),
        PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_ENABLED: '' + config('payments.notifications.admin.withdrawal.enabled'),
        PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_TRESHOLD: parseInt(config('payments.notifications.admin.withdrawal.treshold'), 10)
      }
    }
  },

  computed: {
    autoConfirmWithdrawalsHint () {
      return this.$t('Please note that this setting only affects whether an extra email confirmation is required for all withdrawals from your coinpayments.net account.') + ' ' +
        this.$t('For this setting to work you also need to set "Allow auto_confirm = 1 in create_withdrawal" permission in your coinpayments.net account for the given API key.')
    },
    maxAutoWithdrawalHint () {
      return this.$t('Automatically confirm and process withdrawals, which are less than or equal to the above amount.') + ' ' +
        this.$t('Please note that even though such withdrawals will be processed automatically on the application side an extra email confirmation might be required on the payments provider side (see coinpayments settings).') + ' ' +
        this.$t('Leave zero if you like to manually approve all withdrawals.')
    },
    minDepositAmountToWithdrawHint () {
      return this.$t('User will need to deposit at least this amount before being able to withdraw funds.') + ' ' +
        this.$t('Set the value to 0 if you do not want to limit withdrawals.')
    },
    ethereumNetworks () {
      return [
        { text: this.$t('Main Ethereum network'), value: 'main' },
        { text: this.$t('Kovan test network'), value: 'kovan' }
      ]
    },
    bscNetworks () {
      return [
        { text: this.$t('Main network'), value: 'bsc-main' },
        { text: this.$t('Test network'), value: 'bsc-test' }
      ]
    },
    tronNetworks () {
      return [
        { text: this.$t('Mainnet (trongrid)'), value: 'api.trongrid.io' },
        { text: this.$t('Mainnet (tronstack)'), value: 'api.tronstack.io' },
        { text: this.$t('Shasta testnet'), value: 'api.shasta.trongrid.io' }
      ]
    },
    polygonNetworks () {
      return [
        { text: this.$t('Main network'), value: 'polygon-main' },
        { text: this.$t('Mumbai test network'), value: 'polygon-test' }
      ]
    }
  },

  created () {
    this.$emit('input', this.variables)
  }
}
</script>

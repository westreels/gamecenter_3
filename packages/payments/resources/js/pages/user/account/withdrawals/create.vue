<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="6">
        <v-card>
          <v-toolbar>
            <v-btn icon @click="$router.go(-1)">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Make a withdrawal') }}
            </v-toolbar-title>
            <preloader :active="!methods" />
          </v-toolbar>
          <v-card-text>
            <template v-if="methods && methods.length > 0">
              <p v-if="method.description" class="method-description mb-5">{{ method.description }}</p>
              <v-form v-model="formIsValid" @submit.prevent="submit">
                <v-select
                  v-model="method"
                  :items="methods"
                  item-text="name"
                  :return-object="true"
                  :label="$t('Withdrawal method')"
                  :disabled="formIsProcessing"
                  outlined
                />

                <v-text-field
                  v-model.number="form.amount"
                  :label="$t('Withdrawal amount')"
                  :suffix="$t('credits')"
                  :rules="[v => validationMin(v, withdrawalMin), v => validationMax(v, withdrawalMax)]"
                  :error="form.errors.has('amount')"
                  :error-messages="form.errors.get('amount')"
                  :disabled="formIsProcessing"
                  outlined
                  @keydown="clearFormErrors($event,'amount')"
                />

                <v-text-field
                  class="input-with-button"
                  :value="paymentAmount"
                  :label="$t('Payment amount')"
                  :suffix="paymentCurrencies ? '' : currency"
                  :error="form.errors.has('payment_currency')"
                  :error-messages="form.errors.get('payment_currency')"
                  :disabled="formIsProcessing"
                  outlined
                  readonly
                >
                  <template v-slot:append>
                    <template v-if="paymentCurrencies">
                      <v-menu
                        top
                        right
                        max-height="300"
                        :disabled="formIsProcessing"
                      >
                        <template v-slot:activator="{ on }">
                          <v-btn text v-on="on">
                            {{ form.payment_currency }}
                            <v-icon>mdi-menu-down</v-icon>
                          </v-btn>
                        </template>

                        <v-list>
                          <v-list-item
                            v-for="(ccy, symbol) in paymentCurrencies"
                            :key="symbol"
                            @click="form.payment_currency = symbol"
                          >
                            <v-list-item-title>{{ symbol }} - {{ ccy.name }}</v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </template>
                  </template>
                </v-text-field>

                <template v-if="method.payment_method">
                  <form-parameter
                    v-for="parameter in method.payment_method.input_parameters"
                    :key="parameter.id"
                    v-model="form.parameters[parameter.id]"
                    :parameter="parameter"
                    :form="form"
                    form-key="parameters"
                    :disabled="formIsProcessing"
                  />
                </template>

                <template v-if="method.parameters">
                  <form-parameter
                    v-for="parameter in method.parameters"
                    :key="parameter.id"
                    v-model="form.parameters[parameter.id]"
                    :parameter="parameter"
                    :form="form"
                    form-key="parameters"
                    :disabled="formIsProcessing"
                  />
                </template>

                <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
                  {{ $t('Submit') }}
                </v-btn>
              </v-form>
            </template>
            <template v-else-if="methods && methods.length === 0">
              <p>
                {{ $t('No withdrawal methods enabled.') }}
              </p>
            </template>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import { config } from '~/plugins/config'
import { get } from '~/plugins/utils'
import { decimal } from '~/plugins/format'
import { mapState, mapActions } from 'vuex'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import Preloader from '~/components/Preloader'
import FormParameter from '~/components/FormParameter'

export default {
  components: { Preloader, FormParameter },

  middleware: ['auth', 'verified', '2fa_passed'],

  mixins: [FormMixin],

  metaInfo () {
    return { title: this.$t('Make a withdrawal') }
  },

  data () {
    return {
      method: null, // currently selected method object
      methods: null,
      formIsProcessing: false,
      form: new Form({
        amount: null,
        payment_currency: null,
        parameters: {}
      })
    }
  },

  computed: {
    ...mapState('auth', [
      'user',
      'account'
    ]),
    currency () {
      return this.method.currency
    },
    rate () {
      return this.form.payment_currency
        ? this.paymentCurrencies[this.form.payment_currency].rate_credits
        : this.method.rate
    },
    paymentAmount () {
      const gatewayCode = get(this.method, 'payment_method.gateway.code')
      const decimalPlaces = this.method.decimal_places

      return this.rate > 0 && this.form.amount > 0
        ? decimal(
          this.form.amount / this.rate,
          decimalPlaces !== null ? decimalPlaces : (['coinpayments', 'ethereum', 'bsc', 'polygon'].indexOf(gatewayCode) > -1 ? 8 : 2)
        )
        : 0
    },
    paymentCurrencies () {
      return this.method.payment_currencies
    },
    withdrawalMin () {
      return parseInt(config('payments.withdrawal_min'), 10)
    },
    withdrawalMax () {
      return parseInt(config('payments.withdrawal_max'), 10)
    }
  },

  watch: {
    method (method) {
      this.form.parameters = {}

      // Assign default values to custom parameters
      if (method.parameters) {
        method.parameters.forEach(parameter => {
          this.form.parameters[parameter.id] = parameter.default
        })
      }

      // Assign default values to built-in method parameters
      if (method.payment_method) {
        method.payment_method.input_parameters.forEach(parameter => {
          this.form.parameters[parameter.id] = parameter.default
        })
      }

      this.form.payment_currency = this.paymentCurrencies ? Object.keys(this.paymentCurrencies)[0] : null
    }
  },

  async created () {
    this.$store.dispatch('auth/fetchUser')
    this.form.amount = this.withdrawalMin

    const { data } = await axios.get('/api/withdrawal-methods')

    this.methods = data
    if (this.methods.length) {
      this.method = this.methods[0]
    }
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance'
    }),
    async submit () {
      this.formIsProcessing = true

      const { data } = await this.form.post(`/api/withdrawals/methods/${this.method.id}`)
        .catch(() => {
          return false
        })

      // data will be undefined in case of any form errors (422 HTTP code)
      if (data) {
        this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })

        if (data.success) {
          this.$router.push({ name: 'user.account.withdrawals.index' })
          this.updateUserAccountBalance(this.account.balance - this.form.amount)
        }
      }

      this.formIsProcessing = false
    }
  }
}
</script>
<style lang="scss" scoped>
  .method-description {
    white-space: pre-line;
  }
</style>

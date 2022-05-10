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
              {{ $t('Make a deposit') }}
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
                  :label="$t('Deposit method')"
                  :error="form.errors.has('method')"
                  :error-messages="form.errors.get('method')"
                  :disabled="formIsProcessing"
                  outlined
                />

                <v-text-field
                  v-model.number="form.amount"
                  :label="$t('Deposit amount')"
                  :suffix="$t('credits')"
                  :rules="[v => validationMin(v, depositMin), v => validationMax(v, depositMax)]"
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
                  :suffix="paymentCurrenciesCount > 0 ? '' : currency"
                  :error="form.errors.has('payment_currency')"
                  :error-messages="form.errors.get('payment_currency')"
                  :disabled="formIsProcessing"
                  outlined
                  readonly
                >
                  <template v-slot:append>
                    <template v-if="paymentCurrenciesCount > 0">
                      <v-menu
                        top
                        right
                        max-height="300"
                        :disabled="formIsProcessing || paymentCurrenciesCount === 1"
                      >
                        <template v-slot:activator="{ on }">
                          <v-btn text v-on="on">
                            {{ form.payment_currency }}
                            <v-icon v-if="paymentCurrenciesCount > 1">
                              mdi-menu-down
                            </v-icon>
                          </v-btn>
                        </template>

                        <v-list>
                          <v-list-item
                            v-for="(ccy, symbol) in paymentCurrencies"
                            :key="symbol"
                            @click="form.payment_currency = symbol"
                          >
                            <v-list-item-title>
                              <template v-if="ccy.name">
                                {{ symbol }} - {{ ccy.name }}
                              </template>
                              <template v-else>
                                {{ symbol }}
                              </template>
                            </v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </template>
                  </template>
                </v-text-field>

                <template v-if="get(method, 'payment_method.gateway.code') === 'stripe'">
                  <input v-model="form.parameters.payment_method_id" type="hidden" />
                  <div ref="card"></div>
                  <div ref="error" class="mb-5"></div>
                </template>

                <template v-else-if="get(method, 'payment_method.gateway.code') === 'ethereum'">
                  <v-select
                    v-model="form.parameters.address"
                    :items="ethereum.addresses"
                    :label="$t('Address')"
                    :rules="[validationRequired]"
                    :error="form.errors.has('parameters.address')"
                    :error-messages="form.errors.get('parameters.address')"
                    :disabled="formIsProcessing || isEthereumAddressConfirmed"
                    outlined
                    @change="clearFormErrors($event,'parameters.address')"
                  >
                    <template v-slot:append-outer>
                      <v-btn
                        class="mt-n2"
                        :disabled="isEthereumAddressConfirmed || !form.parameters.address || ethereum.confirmedAddresses === null || ethereum.addressConfirmationInProgress"
                        :loading="ethereum.addressConfirmationInProgress"
                        @click="confirmBlockchainAddress"
                      >
                        {{ $t('Confirm') }}
                      </v-btn>
                    </template>
                  </v-select>
                </template>

                <template v-else-if="get(method, 'payment_method.gateway.code') === 'bsc'">
                  <v-select
                    v-model="form.parameters.address"
                    :items="bsc.addresses"
                    :label="$t('Address')"
                    :rules="[validationRequired]"
                    :error="form.errors.has('parameters.address')"
                    :error-messages="form.errors.get('parameters.address')"
                    :disabled="formIsProcessing || isBscAddressConfirmed"
                    outlined
                    @change="clearFormErrors($event,'parameters.address')"
                  >
                    <template v-slot:append-outer>
                      <v-btn
                        class="mt-n2"
                        :disabled="isBscAddressConfirmed || !form.parameters.address || bsc.confirmedAddresses === null || bsc.addressConfirmationInProgress"
                        :loading="bsc.addressConfirmationInProgress"
                        @click="confirmBlockchainAddress"
                      >
                        {{ $t('Confirm') }}
                      </v-btn>
                    </template>
                  </v-select>
                </template>

                <template v-else-if="get(method, 'payment_method.gateway.code') === 'polygon'">
                  <v-select
                    v-model="form.parameters.address"
                    :items="polygon.addresses"
                    :label="$t('Address')"
                    :rules="[validationRequired]"
                    :error="form.errors.has('parameters.address')"
                    :error-messages="form.errors.get('parameters.address')"
                    :disabled="formIsProcessing || isPolygonAddressConfirmed"
                    outlined
                    @change="clearFormErrors($event,'parameters.address')"
                  >
                    <template v-slot:append-outer>
                      <v-btn
                        class="mt-n2"
                        :disabled="isPolygonAddressConfirmed || !form.parameters.address || polygon.confirmedAddresses === null || polygon.addressConfirmationInProgress"
                        :loading="polygon.addressConfirmationInProgress"
                        @click="confirmBlockchainAddress"
                      >
                        {{ $t('Confirm') }}
                      </v-btn>
                    </template>
                  </v-select>
                </template>

                <template v-else-if="get(method, 'payment_method.gateway.code') === 'tron'">
                  <v-select
                    v-model="form.parameters.address"
                    :items="tron.addresses"
                    :label="$t('Address')"
                    :rules="[validationRequired]"
                    :error="form.errors.has('parameters.address')"
                    :error-messages="form.errors.get('parameters.address')"
                    :disabled="formIsProcessing || isTronAddressConfirmed"
                    outlined
                    @change="clearFormErrors($event,'parameters.address')"
                  >
                    <template v-slot:append-outer>
                      <v-btn
                        class="mt-n2"
                        :disabled="isTronAddressConfirmed || !form.parameters.address || tron.confirmedAddresses === null || tron.addressConfirmationInProgress"
                        :loading="tron.addressConfirmationInProgress"
                        @click="confirmTronAddress"
                      >
                        {{ $t('Confirm') }}
                      </v-btn>
                    </template>
                  </v-select>
                </template>

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

                <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy || formIsProcessing" :loading="form.busy">
                  {{ submitButtonText }}
                </v-btn>
              </v-form>
            </template>
            <template v-else-if="methods && methods.length === 0">
              <p>
                {{ $t('No deposit methods enabled.') }}
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
import { get, loadScript } from '~/plugins/utils'
import { decimal } from '~/plugins/format'
import { mapState } from 'vuex'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import Preloader from '~/components/Preloader'
import Web3 from 'web3'
import FormParameter from '~/components/FormParameter'

export default {
  components: { FormParameter, Preloader },

  mixins: [FormMixin],

  middleware: ['auth', 'verified', '2fa_passed'],

  metaInfo () {
    return { title: this.$t('Make a deposit') }
  },

  data () {
    return {
      method: null, // currently selected deposit method object
      methods: null,
      formIsProcessing: false,
      submitButtonText: this.$t('Proceed'),
      form: new Form({
        amount: null,
        payment_currency: null,
        parameters: {}
      }),
      ethereum: {
        addresses: [],
        confirmedAddresses: null,
        addressConfirmationInProgress: false,
        web: null
      },
      bsc: {
        addresses: [],
        confirmedAddresses: null,
        addressConfirmationInProgress: false,
        web: null
      },
      polygon: {
        addresses: [],
        confirmedAddresses: null,
        addressConfirmationInProgress: false,
        web: null
      },
      tron: {
        addresses: [],
        confirmedAddresses: null,
        addressConfirmationInProgress: false,
        web: null
      },
      stripe: {
        instance: null,
        elements: null,
        card: null
      }
    }
  },

  computed: {
    ...mapState('auth', [
      'user'
    ]),
    currency () {
      return this.method.currency
    },
    rate () {
      return this.form.payment_currency && this.paymentCurrenciesCount > 0
        ? this.paymentCurrencies[this.form.payment_currency].rate_credits
        : this.method.rate
    },
    paymentAmount () {
      const gatewayCode = get(this.method, 'payment_method.gateway.code')
      const decimalPlaces = this.method.decimal_places

      return this.rate > 0 && this.form.amount > 0
        ? decimal(
          this.form.amount / this.rate,
          decimalPlaces || (['coinpayments', 'ethereum', 'bsc', 'polygon', 'tron'].indexOf(gatewayCode) > -1 ? 8 : 2)
        )
        : 0
    },
    paymentCurrencies () {
      return this.method.payment_currencies
    },
    paymentCurrenciesCount () {
      return Object.keys(this.paymentCurrencies).length
    },
    depositMin () {
      return parseInt(config('payments.deposit_min'), 10)
    },
    depositMax () {
      return parseInt(config('payments.deposit_max'), 10)
    },
    isEthereumAddressConfirmed () {
      return this.ethereum.confirmedAddresses && !!this.ethereum.confirmedAddresses.find(item => item.address === this.form.parameters.address)
    },
    isBscAddressConfirmed () {
      return this.bsc.confirmedAddresses && !!this.bsc.confirmedAddresses.find(item => item.address === this.form.parameters.address)
    },
    isPolygonAddressConfirmed () {
      return this.polygon.confirmedAddresses && !!this.polygon.confirmedAddresses.find(item => item.address === this.form.parameters.address)
    },
    isTronAddressConfirmed () {
      return this.tron.confirmedAddresses && !!this.tron.confirmedAddresses.find(item => item.address === this.form.parameters.address)
    }
  },

  watch: {
    method (method) {
      this.formIsValid = true
      this.form.errors.clear()

      const code = get(method, 'payment_method.gateway.code')

      // disable credentials for tron to avoid this error:
      // Response to preflight request doesn't pass access control check: The value of the 'Access-Control-Allow-Origin' header in the response must not be the wildcard '*' when the request's credentials mode is 'include'
      axios.defaults.withCredentials = code !== 'tron'

      this.form.parameters = {}
      // Assign default values to custom parameters
      if (method.parameters) {
        method.parameters.forEach(parameter => {
          this.$set(this.form.parameters, parameter.id, parameter.default)
        })
      }

      // Assign default values to built-in method parameters
      if (method.payment_method) {
        method.payment_method.input_parameters.forEach(parameter => {
          this.$set(this.form.parameters, parameter.id, parameter.default)
        })
      }

      if (code === 'stripe') {
        if (this.stripe.instance) {
          this.$nextTick(() => { this.initStripe() })
        } else {
          loadScript('https://js.stripe.com/v3/', this.initStripe)
        }
      } else if (['ethereum', 'bsc', 'polygon'].indexOf(code) > -1) {
        this.initWeb3(code)
      } else if (code === 'tron') {
        this.initTronWeb()
      }

      this.form.payment_currency = this.paymentCurrencies ? Object.keys(this.paymentCurrencies)[0] : null
    },
    isEthereumAddressConfirmed (confirmed) {
      this.checkAddress(confirmed)
    },
    isBscAddressConfirmed (confirmed) {
      this.checkAddress(confirmed)
    },
    isPolygonAddressConfirmed (confirmed) {
      this.checkAddress(confirmed)
    },
    isTronAddressConfirmed (confirmed) {
      this.checkAddress(confirmed)
    }
  },

  async created () {
    this.$store.dispatch('auth/fetchUser')
    this.form.amount = this.depositMin

    const { data } = await axios.get('/api/deposit-methods')

    this.methods = data
    if (this.methods.length) {
      this.method = this.methods[0]
    }
  },

  beforeDestroy () {
    axios.defaults.withCredentials = true
  },

  methods: {
    get,
    checkAddress (confirmed) {
      if (confirmed) {
        this.form.errors.clear('parameters.address')
      } else if (!confirmed && this.form.parameters.address) {
        this.form.errors.set('parameters.address', this.$t('Please confirm this address belongs to you by signing a random text string.'))
      }
    },
    async initWeb3 (blockchain) {
      if (!Web3.givenProvider) {
        this.$store.dispatch('message/error', { text: this.$t('Please check that a {0} compatible wallet (such as {1}) is installed and enabled in your browser.', ['Web3', 'Metamask']) })
        this.formIsValid = false
        return false
      }

      const web = new Web3(Web3.givenProvider)
      this[blockchain].web = web

      try {
        const addresses = await web.eth.requestAccounts()
        this[blockchain].addresses = addresses
        this.$set(this.form.parameters, 'address', addresses[0])
        this.fetchConfirmedBlockchainAddresses(blockchain)
      } catch (error) {
        this.$store.dispatch('message/error', {
          text: error.code === 4001
            ? this.$t('Access to your account is not authorized')
            : error.message
        })
        this.formIsValid = false
      }
    },
    async initTronWeb () {
      if (!window.tronWeb) {
        this.$store.dispatch('message/error', { text: this.$t('Please check that a {0} compatible wallet (such as {1}) is installed and enabled in your browser.', ['Tronweb', 'Tronlink']) })
        this.formIsValid = false
        return false
      }

      const web = window.tronWeb
      this.tron.web = web

      const address = web.defaultAddress.base58

      if (address !== false) {
        this.tron.addresses = [address]
        this.$set(this.form.parameters, 'address', address)
        this.fetchConfirmedBlockchainAddresses('tron')
      } else {
        this.$store.dispatch('message/error', { text: this.$t('Access to your account is not authorized') })
        this.formIsValid = false
      }
    },
    async fetchConfirmedBlockchainAddresses (blockchain) {
      if (!this[blockchain].confirmedAddresses) {
        const { data } = await axios.get(`/api/blockchains/${blockchain}/addresses`)

        this[blockchain].confirmedAddresses = data
      }
    },
    async confirmBlockchainAddress () {
      const blockchain = get(this.method, 'payment_method.gateway.code')

      this[blockchain].addressConfirmationInProgress = true

      const { data } = await axios.post(`/api/blockchains/${blockchain}/addresses`, { address: this.form.parameters.address })

      let signature
      const address = data
      const web = this[blockchain].web

      try {
        signature = await web.eth.personal.sign(address.message, this.form.parameters.address)
      } catch (error) {
        this.form.errors.set('parameters.address', error.message)
      }

      if (signature) {
        const request = await axios.post(`/api/blockchains/${blockchain}/addresses/${address.id}/verify`, { signature })

        this[blockchain].confirmedAddresses.push(request.data)
      }

      this[blockchain].addressConfirmationInProgress = false
    },
    async confirmTronAddress () {
      this.tron.addressConfirmationInProgress = true

      const { data } = await axios.post('/api/blockchains/tron/addresses', { address: this.form.parameters.address })

      let signature
      const address = data
      const web = window.tronWeb

      try {
        signature = await web.trx.sign(web.toHex(address.message).replace(/^0x/, ''))
      } catch (error) {
        this.form.errors.set('parameters.address', error.message)
      }

      if (signature) {
        const { data } = await axios.post(`/api/blockchains/tron/addresses/${address.id}/verify`, { signature })

        if (data.confirmed) {
          this.tron.confirmedAddresses.push(data)
        }
      }

      this.tron.addressConfirmationInProgress = false
    },
    initStripe () {
      this.stripe.instance = window.Stripe(config('payments.stripe.public_key'))

      const style = {
        base: {
          color: this.$vuetify.theme.isDark ? '#fff' : '#000',
          lineHeight: '20px',
          fontFamily: 'inherit',
          fontSize: '16px',
          '::placeholder': {
            color: this.$vuetify.theme.isDark ? 'rgba(255, 255, 255, 0.7)' : '#000'
          },
          fontSmoothing: 'antialiased'
        },
        invalid: {
          color: '#ff5252',
          iconColor: '#ff5252'
        }
      }

      this.stripe.elements = this.stripe.instance.elements()
      this.stripe.card = this.stripe.elements.create('card', { style })
      this.stripe.card.mount(this.$refs.card)

      // Handle real-time validation errors from the card Element.
      this.stripe.card.addEventListener('change', event => {
        this.$refs.error.textContent = event.error ? event.error.message : ''
      })
    },
    async submit () {
      this.formIsProcessing = true

      if (get(this.method, 'payment_method.gateway.code') === 'stripe') {
        const { paymentMethod } = await this.stripe.instance.createPaymentMethod(
          'card',
          this.stripe.card, {
            billing_details: {
              name: this.user.name,
              email: this.user.email
            }
          })
        this.form.parameters.payment_method_id = paymentMethod.id
      }

      const { data } = await this.form.post(`/api/deposits/methods/${this.method.id}`)
        .catch(() => {
          return false
        })

      // data will be undefined in case of any form errors (422 HTTP code)
      if (data) {
        if (data.success) {
          if (data.redirect) {
            if (data.redirect.external) {
              this.submitButtonText = this.$t('Redirecting') + '...'
              window.location.href = data.redirect.url
            } else {
              this.$router.push({ name: data.redirect.url, params: data.redirect.params })
            }
          } else {
            this.$router.push({ name: 'user.account.deposits.index', query: { status: 'complete' } })
          }
        // error
        } else {
          this.$store.dispatch('message/error', { text: data.message })
          this.formIsProcessing = false
        }
      } else {
        this.formIsProcessing = false
      }
    }
  }
}
</script>
<style lang="scss" scoped>
  .method-description {
    white-space: pre-line;
  }

  .StripeElement {
    padding: 16px 0 16px 12px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.24);

    .ElementsApp {
      .InputElement {
        font-size: 16px;
        line-height: 20px;
        padding: 8px 0 8px;
      }
    }
  }

  .StripeElement--focus {
    border-width: 2px;
    border-color: var(--v-primary-base);
  }

  .StripeElement--invalid {
    border-color: #ff5252;
  }

  .theme-light {
    .StripeElement {
      border-color: rgba(0, 0, 0, 0.42);
    }

    .StripeElement--focus {
      border-color: var(--v-primary-base);
    }
  }
</style>

<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="6">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Complete deposit') }}
            </v-toolbar-title>
            <preloader :active="!deposit" />
          </v-toolbar>
          <v-card-text>
            <template v-if="get(deposit, 'method.payment_method.gateway.code') === 'coinpayments'">
              <p>{{ $t('To complete the deposit please make the following transfer') }}</p>

              <v-text-field
                ref="amount"
                :label="$t('Amount')"
                :value="get(deposit, 'payment_amount')"
                :suffix="get(deposit, 'payment_currency')"
                outlined
                readonly
                append-icon="mdi-content-copy"
                @click:append="copyToClipboard($refs.amount)"
              />

              <v-text-field
                ref="address"
                :label="$t('Address')"
                :value="get(deposit, 'parameters.address')"
                outlined
                readonly
              >
                <template v-slot:append>
                  <v-dialog v-model="modal" width="300">
                    <template v-slot:activator="{ on }">
                      <v-icon @click="modal = true">
                        mdi-qrcode
                      </v-icon>
                    </template>
                    <v-img width="300" height="300" :src="get(deposit, 'parameters.qr_url')" />
                  </v-dialog>
                  <v-icon class="ml-3" @click="copyToClipboard($refs.address)">
                    mdi-content-copy
                  </v-icon>
                </template>
              </v-text-field>

              <v-text-field
                v-if="get(deposit, 'parameters.destination_tag')"
                ref="destination_tag"
                :label="$t('Destination tag')"
                :value="get(deposit, 'parameters.destination_tag')"
                outlined
                readonly
                append-icon="mdi-content-copy"
                @click:append="copyToClipboard($refs.destination_tag)"
              />

              <span>{{ $t('Time left') }}</span>
              <countdown-timer v-if="get(deposit, 'parameters.expiration_time')" :end-date="get(deposit, 'parameters.expiration_time')" />
            </template>
            <template v-else-if="['ethereum', 'bsc', 'polygon'].indexOf(get(deposit, 'method.payment_method.gateway.code')) > -1">
              <v-text-field
                ref="address"
                :label="$t('Address from')"
                :value="get(deposit, 'parameters.addressFrom')"
                outlined
                readonly
              />

              <v-text-field
                ref="address"
                :label="$t('Address to')"
                :value="get(deposit, 'parameters.addressTo')"
                outlined
                readonly
              />

              <v-text-field
                ref="address"
                :label="$t('Amount')"
                :value="get(deposit, 'payment_amount')"
                :suffix="get(deposit, 'payment_currency')"
                outlined
                readonly
              />

              <v-btn
                color="primary"
                :disabled="transactionInProgress"
                :loading="transactionInProgress"
                @click="createEthereumTransaction"
              >
                {{ $t('Pay') }}
              </v-btn>
            </template>
            <template v-else-if="get(deposit, 'method.payment_method.gateway.code') === 'tron'">
              <v-text-field
                ref="address"
                :label="$t('Address from')"
                :value="get(deposit, 'parameters.addressFrom')"
                outlined
                readonly
              />

              <v-text-field
                ref="address"
                :label="$t('Address to')"
                :value="get(deposit, 'parameters.addressTo')"
                outlined
                readonly
              />

              <v-text-field
                ref="address"
                :label="$t('Amount')"
                :value="get(deposit, 'payment_amount')"
                :suffix="get(deposit, 'payment_currency')"
                outlined
                readonly
              />

              <v-btn
                color="primary"
                :disabled="transactionInProgress"
                :loading="transactionInProgress"
                @click="createTronTransaction"
              >
                {{ $t('Pay') }}
              </v-btn>
            </template>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import { get, copyToClipboard } from '~/plugins/utils'
import FormMixin from '~/mixins/Form'
import Preloader from '~/components/Preloader'
import CountdownTimer from '~/components/CountdownTimer'
import Web3 from 'web3'
import ABI from 'packages/payments/resources/js/static/contract-abi' // https://github.com/ethereum/wiki/wiki/Contract-ERC20-ABI

export default {
  components: { CountdownTimer, Preloader },

  mixins: [FormMixin],

  middleware: ['auth', 'verified', '2fa_passed'],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Complete deposit') }
  },

  data () {
    return {
      modal: false,
      deposit: null,
      transactionInProgress: false
    }
  },

  async created () {
    const { data } = await axios.get(`/api/deposits/${this.id}`)

    this.deposit = data

    // disable credentials for tron to avoid this error:
    // Response to preflight request doesn't pass access control check: The value of the 'Access-Control-Allow-Origin' header in the response must not be the wildcard '*' when the request's credentials mode is 'include'
    axios.defaults.withCredentials = get(this.deposit, 'payment_method.gateway.code') !== 'tron'

    if (get(this.deposit, 'payment_method.gateway.code') === 'coinpayments') {
      const time = Math.round(new Date().getTime() / 1000)

      if (time > get(this.deposit, 'parameters.expiration_time', time - 1)) {
        this.$store.dispatch('message/error', { text: this.$t('This deposit has expired.') })
        this.$router.push({ name: 'user.account.deposits.index' })
      }
    }
  },

  beforeDestroy () {
    axios.defaults.withCredentials = true
  },

  methods: {
    get,
    async createEthereumTransaction () {
      if (!Web3.givenProvider) {
        this.$store.dispatch('message/error', { text: this.$t('Please check that a {0} compatible wallet (such as {1}) is installed and enabled in your browser.', ['Web3', 'Metamask']) })
        return false
      }

      this.transactionInProgress = true
      const web = new Web3(Web3.givenProvider)
      let addresses, transaction

      try {
        addresses = await web.eth.requestAccounts()
      } catch (error) {
        this.$store.dispatch('message/error', {
          text: error.code === 4001
            ? this.$t('Access to your account is not authorized')
            : error.message
        })
      }

      // if user authorized access to their account
      if (addresses) {
        try {
          // ERC20 / BEP20 token payment
          if (['erc20', 'bep20', 'erc20_polygon'].indexOf(this.deposit.method.payment_method.code) > -1) {
            const contract = new web.eth.Contract(ABI, this.deposit.parameters.contractAddress, {
              from: this.deposit.parameters.addressFrom
            })

            const wei = web.utils.toBN(this.deposit.payment_amount_wei)
            const data = contract.methods.transfer(this.deposit.parameters.addressTo, wei).encodeABI()

            transaction = await web.eth.sendTransaction({
              from: this.deposit.parameters.addressFrom,
              to: this.deposit.parameters.contractAddress,
              value: 0, // don't transfer ETH / BNB, just tokens
              data
            })
          // ETH / BNB / MATIC payment
          } else {
            transaction = await web.eth.sendTransaction({
              from: this.deposit.parameters.addressFrom,
              to: this.deposit.parameters.addressTo,
              value: this.deposit.payment_amount_wei
            })
          }
        } catch (error) {
          this.$store.dispatch('message/error', {
            text: error.code === 4001
              ? this.$t('Transaction is not authorized')
              : error.message
          })
        }

        if (transaction) {
          const { data } = await axios.patch(`/api/deposits/methods/${this.deposit.method.id}/${this.id}`, {
            transaction_id: transaction.transactionHash
          })

          if (data.success) {
            this.$store.dispatch('message/success', { text: this.$t('Deposit is accepted and will be reflected on your account as soon as the transaction is confirmed.') })
            this.$router.push({ name: 'user.account.deposits.index' })
          }
        }
      }

      this.transactionInProgress = false
    },
    async createTronTransaction () {
      if (!window.tronWeb) {
        this.$store.dispatch('message/error', { text: this.$t('Please check that a {0} compatible wallet (such as {1}) is installed and enabled in your browser.', ['Web3', 'Metamask']) })
        return false
      }

      this.transactionInProgress = true

      const web = window.tronWeb
      const address = web.defaultAddress.base58

      if (address !== false) {
        let success, transactionId

        try {
          if (this.deposit.method.payment_method.code === 'trc20') {
            const { abi } = await web.trx.getContract(this.deposit.parameters.contractAddress)
            const contract = web.contract(abi.entrys, this.deposit.parameters.contractAddress)

            transactionId = await contract.methods.transfer(this.deposit.parameters.addressTo, this.deposit.payment_amount_sun).send()
            if (transactionId) {
              success = true
            }
          } else {
            const transaction = await web.transactionBuilder.sendTrx(
              this.deposit.parameters.addressTo,
              this.deposit.payment_amount_sun,
              this.deposit.parameters.addressFrom
            )
            const signedTransaction = await web.trx.sign(transaction);
            // {a, b} = {a: 1, b: 2} is not valid stand-alone syntax, as the {a, b} on the left-hand side is considered a block and not an object literal.
            // However, ({a, b} = {a: 1, b: 2}) is valid, as is const {a, b} = {a: 1, b: 2}
            // Your ( ... ) expression needs to be preceded by a semicolon or it may be used to execute a function on the previous line.
            ({ result: success, txid: transactionId } = await web.trx.sendRawTransaction(signedTransaction))
          }

          if (success === true) {
            const { data } = await axios.patch(`/api/deposits/methods/${this.deposit.method.id}/${this.id}`, { transaction_id: transactionId })

            if (data.success) {
              this.$store.dispatch('message/success', { text: this.$t('Deposit is accepted and will be reflected on your account as soon as the transaction is confirmed.') })
              this.$router.push({ name: 'user.account.deposits.index' })
            }
          }
        } catch (error) {
          this.$store.dispatch('message/error', { text: error.message || error })
        }
      } else {
        this.$store.dispatch('message/error', { text: this.$t('Access to your account is not authorized') })
      }

      this.transactionInProgress = false
    },
    copyToClipboard (ref) {
      return copyToClipboard(ref.$el.querySelector('input'))
    }
  }
}
</script>

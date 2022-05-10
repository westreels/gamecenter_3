<template>
  <v-container fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Faucet') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <p>
              {{ $t('You can claim {0} credits every {1} {2}.', [amount, interval, interval === 1 ? $t('hour') : $t('hours')]) }}
            </p>
            <v-form v-model="formIsValid" class="mt-3" @submit.prevent="claimFaucet">
              <v-text-field
                :value="amount"
                :label="$t('Faucet amount')"
                :suffix="$t('credits')"
                :readonly="true"
                :error="form.errors.has('amount')"
                :error-messages="form.errors.get('amount')"
                outlined
              >
                <template v-slot:append-outer>
                  <v-skeleton-loader type="button" :loading="allowed === null">
                    <v-btn type="submit" color="primary" class="mt-n3" large :disabled="!allowed || !formIsValid || form.busy" :loading="form.busy">
                      {{ $t('Get') }}
                    </v-btn>
                  </v-skeleton-loader>
                </template>
              </v-text-field>
            </v-form>
            <template v-if="allowed === false && time">
              <span>{{ $t('Wait time for the next faucet') }}</span>
              <countdown-timer :end-date="time" class="text-no-wrap" />
            </template>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import Form from 'vform'
import { config } from '~/plugins/config'
import axios from 'axios'
import FormMixin from '~/mixins/Form'
import CountdownTimer from '~/components/CountdownTimer'

export default {
  components: { CountdownTimer },

  mixins: [FormMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'faucet'],

  metaInfo () {
    return { title: this.$t('Faucet') }
  },

  data () {
    return {
      allowed: null,
      time: null,
      form: new Form(),
      url: '/api/user/account/faucet'
    }
  },

  computed: {
    amount () {
      return parseInt(config('settings.bonuses.faucet.amount'), 10)
    },
    interval () {
      return parseInt(config('settings.bonuses.faucet.interval'), 10)
    }
  },

  async created () {
    this.checkFaucet()
  },

  methods: {
    async checkFaucet () {
      const { data: { allowed, time } } = await axios.get(this.url)

      this.allowed = allowed
      this.time = time
    },
    async claimFaucet () {
      const { data: { success, time } } = await this.form.submit('post', this.url)

      if (success) {
        this.allowed = false
        this.time = time
        this.$store.dispatch('message/success', { text: this.$t('{0} credits added to your account.', [this.amount]) })
      }
    }
  }
}
</script>

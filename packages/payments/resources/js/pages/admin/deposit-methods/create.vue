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
              {{ $t('Create deposit method') }}
            </v-toolbar-title>
            <v-spacer />
            <preloader :active="!gateways" />
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="create('deposit')">
              <v-select
                v-model="gatewayId"
                :items="gatewayItems"
                :label="$t('Payment gateway')"
                :disabled="!gateways"
                outlined
              />

              <v-select
                v-show="gatewayId"
                v-model="form.payment_method_id"
                :items="gateway.payment_methods"
                item-text="name"
                item-value="id"
                :label="$t('Payment method')"
                outlined
              />

              <v-text-field
                v-model="form.code"
                :label="$t('Code')"
                type="text"
                :rules="[validationRequired]"
                :error="form.errors.has('code')"
                :error-messages="form.errors.get('code')"
                outlined
                :disabled="!gateways"
                @keydown="clearFormErrors($event,'code')"
              />

              <v-text-field
                v-model="form.name"
                :label="$t('Name')"
                type="text"
                :rules="[validationRequired]"
                :error="form.errors.has('name')"
                :error-messages="form.errors.get('name')"
                outlined
                :disabled="!gateways"
                @keydown="clearFormErrors($event,'name')"
              />

              <v-textarea
                v-model="form.description"
                :label="$t('Description')"
                :error="form.errors.has('description')"
                :error-messages="form.errors.get('description')"
                outlined
                :disabled="!gateways"
                @keydown="clearFormErrors($event,'description')"
              />

              <v-select
                v-if="get(gatewayPaymentMethod, 'allowed_currencies.0')"
                v-model="form.currency"
                :items="gatewayPaymentMethod.id ? gatewayPaymentMethod.allowed_currencies : []"
                :label="$t('Reference currency')"
                :disabled="!gateways"
                :error="form.errors.has('currency')"
                :error-messages="form.errors.get('currency')"
                outlined
                @change="clearFormErrors($event,'currency')"
              />

              <v-text-field
                v-else
                v-model="form.currency"
                :label="$t('Reference currency')"
                type="text"
                :rules="[validationRequired]"
                :error="form.errors.has('currency')"
                :error-messages="form.errors.get('currency')"
                outlined
                :disabled="!gateways"
                @keydown="clearFormErrors($event,'currency')"
              />

              <v-text-field
                v-model.number="form.rate"
                :label="$t('Reference exchange rate')"
                type="text"
                :rules="[validationRequired, validationPositiveNumber]"
                :error="form.errors.has('rate')"
                :error-messages="form.errors.get('rate')"
                :prefix="'1 ' + form.currency + ' = '"
                :suffix="$t('credits')"
                :hint="$t('Amount of credits per 1 unit of the reference currency')"
                :persistent-hint="true"
                outlined
                :disabled="!gateways"
                @keydown="clearFormErrors($event,'rate')"
              />

              <v-text-field
                v-if="!gateway.id"
                v-model.number="form.decimal_places"
                :label="$t('Decimal places')"
                type="text"
                :rules="[validationInteger, v => validationMin(v, 0), v => validationMax(v, 8)]"
                :error="form.errors.has('decimal_places')"
                :error-messages="form.errors.get('decimal_places')"
                :hint="$t('Number of decimal places, which payment amount should be rounded to.')"
                :persistent-hint="true"
                outlined
                :disabled="!gateways"
                @keydown="clearFormErrors($event,'code')"
              />

              <div v-if="gatewayPaymentMethod.id" class="mt-5">
                <form-parameter
                  v-for="parameter in gatewayPaymentMethod.parameters"
                  :key="parameter.id"
                  v-model="form.payment_method_parameters[parameter.id]"
                  :parameter="parameter"
                  :form="form"
                  form-key="payment_method_parameters"
                />
              </div>

              <entity-parameters v-if="gateways" v-model="form.parameters" />

              <v-switch
                v-model="form.enabled"
                :label="$t('Enabled')"
                color="primary"
                :false-value="0"
                :true-value="1"
                :disabled="!gateways"
              />

              <v-skeleton-loader type="button" :loading="!gateways">
                <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
                  {{ $t('Create') }}
                </v-btn>
              </v-skeleton-loader>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { get } from '~/plugins/utils'
import FormMixin from '~/mixins/Form'
import Preloader from '~/components/Preloader'
import FormParameter from '~/components/FormParameter'
import CreateDepositWithdrawalMethod from 'packages/payments/resources/js/mixins/Admin/CreateDepositWithdrawalMethod'
import EntityParameters from '~/components/Admin/EntityParameters'

export default {
  components: { Preloader, EntityParameters, FormParameter },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  mixins: [FormMixin, CreateDepositWithdrawalMethod],

  metaInfo () {
    return { title: this.$t('Create deposit method') }
  },

  async created () {
    this.pullGateways('in')
  },

  methods: {
    get
  }
}
</script>

<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="8">
        <v-card>
          <v-toolbar>
            <v-btn icon @click="$router.go(-1)">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Deposit {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <deposit-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <key-value-table
              name="deposit"
              :api="`/api/admin/deposits/${id}`"
              :headers="headers"
              class="wrap-long-text"
              @load="deposit = $event.data"
            >
              <template v-slot:user="{ item: { account: { user } } }">
                <router-link :to="{ name: 'admin.users.show', params: { id: user.id } }">
                  {{ user.name }}
                </router-link>
              </template>
              <template v-slot:response="{ item }">
                <v-dialog v-if="item.response" v-model="modal" width="600">
                  <template v-slot:activator="{ on }">
                    <slot :on="on">
                      <a class="link" v-on="on">
                        {{ $t('View') }}
                      </a>
                    </slot>
                  </template>
                  <v-card>
                    <v-toolbar>
                      <v-toolbar-title>
                        {{ $t('API response') }}
                      </v-toolbar-title>
                      <v-spacer />
                      <v-btn icon @click="modal = false">
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                    </v-toolbar>
                    <v-card-text class="pa-4">
                      <kbd v-for="(r, i) in item.response" :key="i" class="mb-3">{{ r }}</kbd>
                    </v-card-text>
                  </v-card>
                </v-dialog>
              </template>
              <template v-slot:after="{ item: { deposit: { method, parameters } } }">
                <tr v-for="(value, key) in parameters" :key="key">
                  <td>
                    {{ getParameterAttribute('name', key, method.parameters, key) }}
                  </td>
                  <td v-if="getParameterAttribute('type', key, method.parameters) === 'switch'" class="text-right">
                    {{ value ? $t('Yes') : $t('No') }}
                  </td>
                  <td v-else class="text-right">
                    {{ value }}
                  </td>
                </tr>
              </template>
            </key-value-table>
            <v-row v-if="deposit && deposit.is_created && !deposit.external_id" class="mt-5">
              <v-col class="text-center">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-form v-model="formIsValid" class="d-inline-block" @submit.prevent="action('cancel')">
                      <v-btn type="submit" color="error" :disabled="!formIsValid || form.busy" :loading="form.busy" v-on="on">
                        {{ $t('Cancel') }}
                      </v-btn>
                    </v-form>
                  </template>
                  <span>{{ $t('Cancel deposit request.') }}</span>
                </v-tooltip>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-form v-model="formIsValid" class="d-inline-block" @submit.prevent="action('complete')">
                      <v-btn type="submit" color="success" :disabled="!formIsValid || form.busy" :loading="form.busy" v-on="on">
                        {{ $t('Complete') }}
                      </v-btn>
                    </v-form>
                  </template>
                  <span>{{ $t('Complete deposit request and credit user account by the deposit amount.') }}</span>
                </v-tooltip>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import { get } from '~/plugins/utils'
import { decimal } from '~/plugins/format'
import KeyValueTable from '~/components/KeyValueTable'
import DepositMenu from 'packages/payments/resources/js/components/Admin/DepositMenu'

export default {
  components: { KeyValueTable, DepositMenu },

  mixins: [FormMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  props: ['id'],

  data () {
    return {
      modal: false,
      deposit: null,
      form: new Form()
    }
  },

  metaInfo () {
    return { title: this.$t('Deposit {0}', [this.id]) }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('External ID'), value: 'external_id' },
        { text: this.$t('User'), value: 'user' },
        { text: this.$t('Method'), value: 'method.name' },
        { text: this.$t('Amount'), value: 'payment_amount', format: 'float' },
        { text: this.$t('Currency'), value: 'payment_currency' },
        { text: this.$t('Credits'), value: 'amount', format: 'decimal' },
        { text: this.$t('Status'), value: 'status_title' },
        { text: this.$t('API response'), value: 'response' },
        { text: this.$t('Created at'), value: 'created_at' },
        { text: this.$t('Updated at'), value: 'updated_at' }
      ]
    }
  },

  methods: {
    get,
    decimal,
    getParameterAttribute (attr, id, parameters, defaultValue = null) {
      const obj = parameters.find(o => o.id === id)
      return obj ? obj[attr] : (defaultValue || id)
    },
    async action (name) {
      const { data } = await this.form.post(`/api/admin/deposits/${this.id}/${name}`)

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })

      if (data.success) {
        this.$router.push({ name: 'admin.deposits.index' })
      }
    }
  }
}
</script>

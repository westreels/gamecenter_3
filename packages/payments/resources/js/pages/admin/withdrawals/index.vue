<template>
  <v-container>
    <data-table
      api="/api/admin/withdrawals"
      :title="$t('Withdrawals')"
      :headers="headers"
      :filters="['period', 'deposit-withdrawal-status']"
      :search="true"
      :search-placeholder="$t('Withdrawal ID, user name or email')"
    >
      <template v-slot:item.name="{ item: { account : { user } } }">
        <user-link :user="user" />
      </template>
      <template v-slot:item.status_title="{ item }">
        <span :class="{ 'green--text': item.is_completed, 'error--text': item.is_cancelled }">
          {{ item.status_title }}
        </span>
      </template>
      <template v-slot:item.actions="{ item }">
        <withdrawal-menu :id="item.id" :code="item.external_id ? get(item, 'method.payment_method.gateway.code') : null" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import { get } from '~/plugins/utils'
import DataTable from '~/components/DataTable'
import UserLink from '~/components/Admin/UserLink'
import WithdrawalMenu from 'packages/payments/resources/js/components/Admin/WithdrawalMenu'

export default {
  components: { WithdrawalMenu, DataTable, UserLink },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Withdrawals') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name', sortable: false },
        { text: this.$t('Method'), value: 'method.name', sortable: false },
        { text: this.$t('Amount'), value: 'payment_amount', align: 'right', format: 'float' },
        { text: this.$t('Currency'), value: 'payment_currency', align: 'right' },
        { text: this.$t('Credits'), value: 'amount', format: 'decimal', align: 'right' },
        { text: this.$t('Status'), value: 'status_title', align: 'right', sortable: false },
        { text: this.$t('Created'), value: 'created_ago', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  },

  methods: {
    get
  }
}
</script>

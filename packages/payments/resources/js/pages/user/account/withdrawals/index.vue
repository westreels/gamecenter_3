<template>
  <v-container>
    <data-table
      :title="$t('Withdrawals')"
      api="/api/withdrawals"
      :headers="headers"
      :filters="['period', 'deposit-withdrawal-status']"
    >
      <template v-slot:table-prepend>
        <v-btn color="primary" :to="{ name: 'user.account.withdrawals.create' }" class="mb-3">
          {{ $t('Make a withdrawal') }}
        </v-btn>
      </template>
      <template v-slot:item.status_title="{ item }">
        <span :class="{ 'green--text': item.is_completed, 'error--text': item.is_cancelled }">
          {{ item.status_title }}
        </span>
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'

export default {
  components: { DataTable },

  middleware: ['auth', 'verified', '2fa_passed'],

  metaInfo () {
    return { title: this.$t('Withdrawals') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Method'), value: 'method.name', sortable: false },
        { text: this.$t('Amount'), value: 'payment_amount', align: 'right', format: 'float' },
        { text: this.$t('Currency'), value: 'payment_currency', align: 'right' },
        { text: this.$t('Credits'), value: 'amount', format: 'integer', align: 'right' },
        { text: this.$t('Status'), value: 'status_title', align: 'right', sortable: false },
        { text: this.$t('Created at'), value: 'created_at', align: 'right' }
      ]
    }
  },

  created () {
    this.$store.dispatch('auth/fetchUser')
  }
}
</script>

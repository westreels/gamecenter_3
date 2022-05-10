<template>
  <v-container>
    <data-table
      api="/api/user/account/transactions"
      :title="$t('Account transactions')"
      :filters="['period']"
      :headers="headers"
    >
      <template v-slot:item.transactionable="{ item : { transactionable } }">
        <template v-if="transactionable">
          <template v-if="transactionable.bet >=0 && transactionable.win >= 0">
            <router-link :to="{ name: 'history.games.show', params: { id: transactionable.id} }">
              {{ transactionable.title }} #{{ transactionable.id }}
            </router-link>
          </template>
          <template v-else>
            {{ transactionable.title }}
          </template>
        </template>
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
    return { title: this.$t('Account transactions') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Type'), value: 'transactionable', sortable: false },
        { text: this.$t('Amount'), value: 'amount', format: 'decimal', align: 'right' },
        { text: this.$t('Balance'), value: 'balance', format: 'decimal', align: 'right' },
        { text: this.$t('Created'), value: 'created_ago', align: 'right' }
      ]
    }
  }
}
</script>

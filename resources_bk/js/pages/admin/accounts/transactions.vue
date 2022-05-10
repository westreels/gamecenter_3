<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="8">
        <data-table
          :api="`/api/admin/accounts/${id}/transactions`"
          :title="$t('Account {0}', [id])"
          :headers="headers"
        >
          <template v-slot:toolbar-prepend>
            <v-btn icon @click="$router.go(-1)">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </template>
          <template v-slot:toolbar-append>
            <account-menu :id="id" />
          </template>
          <template v-slot:item.transactionable="{ item : { transactionable } }">
            <template v-if="transactionable">
              <template v-if="transactionable.bet >=0 && transactionable.win >= 0">
                <router-link :to="{ name: 'admin.games.show', params: { id: transactionable.id} }">
                  {{ transactionable.title }} #{{ transactionable.id }}
                </router-link>
              </template>
              <template v-else>
                {{ transactionable.title }}
              </template>
            </template>
          </template>
        </data-table>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import AccountMenu from '~/components/Admin/AccountMenu'

export default {
  components: { DataTable, AccountMenu },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Account {0}', [this.id]) }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Type'), value: 'transactionable', sortable: false },
        { text: this.$t('Amount'), value: 'amount', format: 'decimal', align: 'right' },
        { text: this.$t('Balance'), value: 'balance', format: 'decimal', align: 'right' },
        { text: this.$t('Created at'), value: 'created_at', align: 'right' }
      ]
    }
  }
}
</script>

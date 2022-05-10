<template>
  <v-container>
    <data-table
      api="/api/admin/withdrawal-methods"
      :title="$t('Withdrawal methods')"
      :headers="headers"
    >
      <template v-slot:table-prepend>
        <v-btn color="primary" :to="{ name: 'admin.withdrawal-methods.create' }" class="mb-5">
          {{ $t('Create withdrawal method') }}
        </v-btn>
      </template>
      <template v-slot:item.status_title="{ item }">
        <span :class="item.enabled ? 'green--text' : 'error--text'">
          {{ item.status_title }}
        </span>
      </template>
      <template v-slot:item.actions="{ item }">
        <withdrawal-method-menu :id="item.id" :item="item" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import WithdrawalMethodMenu from 'packages/payments/resources/js/components/Admin/WithdrawalMethodMenu'

export default {
  components: { WithdrawalMethodMenu, DataTable },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Withdrawal methods') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name' },
        { text: this.$t('Gateway'), value: 'payment_method.gateway.name', sortable: false },
        { text: this.$t('Method'), value: 'payment_method.name', sortable: false },
        { text: this.$t('Status'), value: 'status_title', sortable: false },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>

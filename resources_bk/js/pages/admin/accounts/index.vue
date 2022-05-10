<template>
  <v-container>
    <data-table
      api="/api/admin/accounts"
      :title="$t('Accounts')"
      :headers="headers"
      :filters="['user-role']"
      :search="true"
      :search-placeholder="$t('Account ID, user name or email')"
    >
      <template v-slot:item.name="{ item }">
        <user-link :user="item.user" />
      </template>
      <template v-slot:item.actions="{ item }">
        <account-menu :id="item.id" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import UserLink from '~/components/Admin/UserLink'
import AccountMenu from '~/components/Admin/AccountMenu'

export default {
  components: { DataTable, UserLink, AccountMenu },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Accounts') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name', sortable: false },
        { text: this.$t('Balance'), value: 'balance', align: 'right', format: 'decimal' },
        { text: this.$t('Updated'), value: 'updated_ago', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>

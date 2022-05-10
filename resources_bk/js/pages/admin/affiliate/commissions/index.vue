<template>
  <v-container>
    <data-table
      api="/api/admin/affiliate/commissions"
      :title="$t('Affiliates commissions')"
      :headers="headers"
      :filters="['period', 'affiliate-commission-type', 'affiliate-commission-status']"
      :search="true"
      :search-placeholder="$t('Commission ID, user name or email')"
    >
      <template v-slot:item.name="{ item: { account: { user } } }">
        <user-link :user="user" />
      </template>
      <template v-slot:item.status="{ item }">
        <span :class="getStatusClass(item)">{{ item.status_title }}</span>
      </template>
      <template v-slot:item.actions="{ item }">
        <affiliate-commission-menu :id="item.id" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import UserLink from '~/components/Admin/UserLink'
import AffiliateCommissionMenu from '~/components/Admin/AffiliateCommissionMenu'

export default {
  components: { DataTable, UserLink, AffiliateCommissionMenu },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Affiliates commissions') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name', sortable: false },
        { text: this.$t('Tier'), value: 'tier', sortable: true },
        { text: this.$t('Type'), value: 'title', sortable: false },
        { text: this.$t('Status'), value: 'status', sortable: false },
        { text: this.$t('Amount'), value: 'amount', align: 'right', format: 'decimal' },
        { text: this.$t('Created'), value: 'created_ago', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  },

  methods: {
    getStatusClass (item) {
      if (item.status === 1) {
        return 'green--text'
      } else if (item.status === 2) {
        return 'error--text'
      }
    }
  }
}
</script>

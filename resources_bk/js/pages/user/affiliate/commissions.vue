<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" lg="8">
        <data-table
          api="/api/user/affiliate/commissions"
          :title="$t('Commissions')"
          :headers="headers"
          :filters="['period', 'affiliate-commission-type', 'affiliate-commission-status']"
        >
          <template v-slot:item.status="{ item }">
            <span :class="getStatusClass(item)">{{ item.status_title }}</span>
          </template>
        </data-table>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'

export default {
  metaInfo () {
    return { title: this.$t('Affiliate program') }
  },

  components: { DataTable },

  data () {
    return {}
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Tier'), value: 'tier' },
        { text: this.$t('Type'), value: 'title', sortable: false },
        { text: this.$t('Status'), value: 'status', sortable: false },
        { text: this.$t('Amount'), value: 'amount', align: 'right', format: 'decimal' },
        { text: this.$t('Created at'), value: 'created_at', align: 'right' }
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

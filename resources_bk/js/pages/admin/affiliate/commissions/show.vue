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
              {{ $t('Affiliate commission {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <affiliate-commission-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <key-value-table
              name="commission"
              :api="`/api/admin/affiliate/commissions/${id}`"
              :headers="headers"
            >
              <template v-slot:user="{ item: { account: { user } } }">
                <router-link :to="{ name: 'admin.users.show', params: { id: user.id } }">{{ user.name }}</router-link>
              </template>
              <template v-slot:referral="{ item: { referral_account: { user } } }">
                <router-link :to="{ name: 'admin.users.show', params: { id: user.id } }">{{ user.name }}</router-link>
              </template>
              <template v-slot:type="{ item }">
                {{ item.title }} ({{ item.commissionable.title || item.commissionable_type.replace('App\\Models\\', '') }} #{{ item.commissionable.id }})
              </template>
              <template v-slot:status="{ item }">
                <span :class="getStatusClass(item)">{{ item.status_title }}</span>
              </template>
            </key-value-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import KeyValueTable from '~/components/KeyValueTable'
import AffiliateCommissionMenu from '~/components/Admin/AffiliateCommissionMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { KeyValueTable, AffiliateCommissionMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Affiliate commission {0}', [this.id]) }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('User'), value: 'user' },
        { text: this.$t('Referral'), value: 'referral' },
        { text: this.$t('Tier'), value: 'tier' },
        { text: this.$t('Type'), value: 'type' },
        { text: this.$t('Status'), value: 'status' },
        { text: this.$t('Amount'), value: 'amount', format: 'decimal' },
        { text: this.$t('IP'), value: 'ip' },
        { text: this.$t('Created at'), value: 'created_at' },
        { text: this.$t('Updated at'), value: 'updated_at' }
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

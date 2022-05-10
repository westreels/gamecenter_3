<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="8">
        <data-table
          :api="`/api/admin/raffles/${id}/tickets`"
          :title="$t('Raffle {0} tickets', [id])"
          :headers="headers"
        >
          <template v-slot:toolbar-prepend>
            <v-btn icon @click="$router.go(-1)">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </template>
          <template v-slot:toolbar-append>
            <raffle-menu :id="id" />
          </template>
          <template v-slot:item.user="{ item: { account: { user } } }">
            <user-link :user="user" />
          </template>
        </data-table>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import RaffleMenu from 'packages/raffle/resources/js/components/Admin/RaffleMenu'
import UserLink from '~/components/Admin/UserLink'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { UserLink, RaffleMenu, DataTable },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Raffle {0} tickets', [this.id]) }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('User'), value: 'user', sortable: false },
        { text: this.$t('Purchased at'), value: 'created_at', align: 'right' }
      ]
    }
  }
}
</script>

<template>
  <v-container>
    <data-table
      api="/api/raffles?completed"
      :title="$t('Completed raffles')"
      :headers="headers"
      sort-by="updated_at"
    >
      <template v-slot:item.winner="{ item }">
        <template v-if="item.winning_ticket_id">
          <v-avatar size="25">
            <v-img :src="item.winning_ticket.account.user.avatar_url" />
          </v-avatar>
          <user-profile-modal :user="item.winning_ticket.account.user" />
        </template>
        <span v-else>&mdash;</span>
      </template>
      <template v-slot:item.tickets_sold="{ item }">
        {{ item.tickets_count }} / {{ item.total_tickets }}
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import UserProfileModal from '~/components/UserProfileModal'

export default {
  components: { UserProfileModal, DataTable },

  middleware: ['auth', 'verified', '2fa_passed'],

  metaInfo () {
    return { title: this.$t('Completed raffles') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('Title'), value: 'title' },
        { text: this.$t('Winner'), value: 'winner', sortable: false },
        { text: this.$t('Win'), value: 'win', align: 'right', format: 'integer' },
        { text: this.$t('Ticket price'), value: 'ticket_price', align: 'right' },
        { text: this.$t('Tickets sold'), value: 'tickets_sold', sortable: false },
        { text: this.$t('Completed'), value: 'updated_ago', align: 'right' }
      ]
    }
  },

  created () {
    this.$store.dispatch('auth/fetchUser')
  }
}
</script>

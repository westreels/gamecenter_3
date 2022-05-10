<template>
  <v-container>
    <data-table
      api="/api/admin/raffles"
      :title="$t('Raffles')"
      :headers="headers"
      :search="true"
      :search-placeholder="$t('ID, title, winner name or email')"
    >
      <template v-slot:table-prepend>
        <v-btn color="primary" :to="{ name: 'admin.raffles.create' }" class="mb-3">
          {{ $t('Create raffle') }}
        </v-btn>
      </template>
      <template v-slot:item.title="{ item }">
        {{ item.title }}
        <v-icon v-if="item.recurring" small>
          mdi-sync
        </v-icon>
      </template>
      <template v-slot:item.winner="{ item }">
        <user-link v-if="item.winning_ticket_id" :user="item.winning_ticket.account.user" />
        <span v-else>&mdash;</span>
      </template>
      <template v-slot:item.tickets_sold="{ item }">
        {{ item.tickets_count }} / {{ item.total_tickets }}
      </template>
      <template v-slot:item.status_title="{ item }">
        <span :class="{ 'green--text': item.is_completed }">
          {{ item.status_title }}
        </span>
      </template>
      <template v-slot:item.ends_at="{ item }">
        <span v-if="item.completion_trigger === 1">{{ item.end_date }}</span>
        <span v-else>{{ $t('After all tickets are sold') }}</span>
      </template>
      <template v-slot:item.actions="{ item }">
        <raffle-menu :id="item.id" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import { get } from '~/plugins/utils'
import DataTable from '~/components/DataTable'
import UserLink from '~/components/Admin/UserLink'
import RaffleMenu from 'packages/raffle/resources/js/components/Admin/RaffleMenu'

export default {
  components: { DataTable, UserLink, RaffleMenu },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Raffles') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Title'), value: 'title' },
        { text: this.$t('Winner'), value: 'winner', sortable: false },
        { text: this.$t('Win'), value: 'win', align: 'right', format: 'integer' },
        { text: this.$t('Fee'), value: 'fee_amount', align: 'right', format: 'integer', sortable: false },
        { text: this.$t('Tickets sold'), value: 'tickets_sold', align: 'right', sortable: false },
        { text: this.$t('Status'), value: 'status_title', sortable: false },
        { text: this.$t('Ends at'), value: 'ends_at', align: 'right', sortable: false },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  },

  methods: {
    get
  }
}
</script>

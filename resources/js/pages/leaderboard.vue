<template>
  <v-container>
    <data-table
      api="/api/leaderboard"
      :title="$t('Leaderboard')"
      :headers="headers"
      :filters="['period']"
      sort-by="bet_total"
    >
      <template v-slot:item.name="{ item: user }">
        <user-avatar :user="user" />
        <user-profile-modal :user="{ id: user.id, name: user.name }" />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import UserProfileModal from '~/components/UserProfileModal'
import UserAvatar from '../components/UserAvatar'

export default {
  components: { UserAvatar, DataTable, UserProfileModal },

  middleware: ['auth', 'verified', '2fa_passed'],

  metaInfo () {
    return { title: this.$t('Leaderboard') }
  },

  data () {
    return {
      filters: {}
    }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('Player'), value: 'name' },
        { text: this.$t('Bets'), value: 'bet_count', format: 'integer', align: 'right' },
        { text: this.$t('Wagered'), value: 'bet_total', format: 'decimal', align: 'right' },
        { text: this.$t('Profit'), value: 'profit_total', format: 'decimal', align: 'right' },
        { text: this.$t('Max profit'), value: 'profit_max', format: 'decimal', align: 'right' }
      ]
    }
  }
}
</script>

<template>
  <v-container>
    <data-table
      api="/api/history/losses"
      :title="$t('Biggest losses')"
      :headers="headers"
      hide-footer
    >
      <template v-slot:item.account.user.name="{ item : { account : { user } } }">
        <user-avatar :user="user" />
        <user-profile-modal :user="user" />
      </template>
      <template v-slot:item.actions="{ item }">
        <game-menu :id="item.id" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import GameMenu from '~/components/GameMenu'
import UserProfileModal from '~/components/UserProfileModal'
import UserAvatar from '~/components/UserAvatar'

export default {
  components: { UserAvatar, DataTable, GameMenu, UserProfileModal },

  middleware: ['auth', 'verified', '2fa_passed'],

  metaInfo () {
    return { title: this.$t('Biggest losses') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('Player'), value: 'account.user.name', sortable: false },
        { text: this.$t('Game'), value: 'title', sortable: false },
        { text: this.$t('Bet'), value: 'bet', align: 'right', format: 'decimal', sortable: false },
        { text: this.$t('Win'), value: 'win', align: 'right', format: 'decimal', sortable: false },
        { text: this.$t('Profit'), value: 'profit', align: 'right', format: 'decimal', sortable: false },
        { text: this.$t('Played'), value: 'created_ago', align: 'right', sortable: false },
        { value: 'actions', align: 'right', sortable: false }
      ]
    }
  }
}
</script>

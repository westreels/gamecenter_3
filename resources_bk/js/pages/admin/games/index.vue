<template>
  <v-container>
    <data-table
      api="/api/admin/games"
      :title="$t('Games')"
      :headers="headers"
      :filters="['period', 'game', 'user-role', 'game-result']"
      :search="true"
      :search-placeholder="$t('Game ID, user name or email')"
    >
      <template v-slot:item.name="{ item }">
        <user-link :user="item.account.user" />
      </template>
      <template v-slot:item.actions="{ item }">
        <game-menu :id="item.id" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import UserLink from '~/components/Admin/UserLink'
import GameMenu from '~/components/Admin/GameMenu'

export default {
  components: { DataTable, UserLink, GameMenu },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Games') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name', sortable: false },
        { text: this.$t('Game'), value: 'title' },
        { text: this.$t('Bet'), value: 'bet', align: 'right', format: 'decimal' },
        { text: this.$t('Win'), value: 'win', align: 'right', format: 'decimal' },
        { text: this.$t('Profit'), value: 'profit', align: 'right', format: 'decimal' },
        { text: this.$t('Played'), value: 'created_ago', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>

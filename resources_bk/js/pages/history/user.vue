<template>
  <v-container>
    <data-table
      api="/api/history/user"
      :title="$t('My games')"
      :filters="['period', 'game', 'game-result']"
      :headers="headers"
      sort-by="created_at"
    >
      <template v-slot:item.actions="{ item }">
        <game-menu :id="item.id" small />
      </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import GameMenu from '~/components/GameMenu'

export default {
  components: { DataTable, GameMenu },

  middleware: ['auth', 'verified', '2fa_passed'],

  metaInfo () {
    return { title: this.$t('My games') }
  },

  computed: {
    headers () {
      return [
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

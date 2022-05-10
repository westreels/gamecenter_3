<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account : { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:gameable.selected_numbers="{ item: { gameable: { selected_numbers } } }">
      {{ selected_numbers.join(', ') }}
    </template>
    <template v-slot:gameable.drawn_numbers="{ item: { gameable: { drawn_numbers } } }">
      {{ drawn_numbers.join(', ') }}
    </template>
  </key-value-table>
</template>

<script>
import KeyValueTable from '~/components/KeyValueTable'
import UserProfileModal from '~/components/UserProfileModal'

export default {
  components: { UserProfileModal, KeyValueTable },

  props: ['id'],

  computed: {
    headers () {
      return [
        { text: this.$t('Player'), value: 'account.user.name' },
        { text: this.$t('Game'), value: 'title' },
        { text: this.$t('Bet'), value: 'bet', format: 'decimal' },
        { text: this.$t('Win'), value: 'win', format: 'decimal' },
        { text: this.$t('Profit'), value: 'profit', format: 'decimal' },
        { text: this.$t('Matches count'), value: 'gameable.matches_count' },
        { text: this.$t('Selected numbers'), value: 'gameable.selected_numbers' },
        { text: this.$t('Drawn numbers'), value: 'gameable.drawn_numbers' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

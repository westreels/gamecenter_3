<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account : { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:gameable.bet="{ item: { gameable } }">
      <template v-if="gameable.direction === -1">0 - {{ gameable.ref_number - 1 }}</template>
      <template v-else>{{ gameable.ref_number }} - 9999</template>
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
        { text: this.$t('Bet on result'), value: 'gameable.bet' },
        { text: this.$t('Win chance'), value: 'gameable.win_chance', format: 'percentage' },
        { text: this.$t('Payout'), value: 'gameable.payout' },
        { text: this.$t('Roll'), value: 'gameable.roll' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

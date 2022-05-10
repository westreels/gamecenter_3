<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account: { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:gameable.payout="{ item: { account: { user }, gameable: { winners } } }">
      {{ (winners[user.id] || 0).toFixed(2) }}
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
        { text: this.$t('Payout'), value: 'gameable.payout' },
        { text: this.$t('Crashed at'), value: 'gameable.max_payout' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

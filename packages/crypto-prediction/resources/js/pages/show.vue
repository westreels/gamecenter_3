<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account : { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:gameable.asset="{ item: { gameable: { asset } } }">
      {{ asset.name }} ({{ asset.symbol }})
    </template>
    <template v-slot:gameable.direction="{ item: { gameable: { direction } } }">
      {{ direction > 0 ? $t('Higher') : $t('Lower') }}
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
        { text: this.$t('Asset'), value: 'gameable.asset' },
        { text: this.$t('Direction'), value: 'gameable.direction' },
        { text: this.$t('Open price'), value: 'gameable.open_price' },
        { text: this.$t('Close price'), value: 'gameable.close_price' },
        { text: this.$t('Start time'), value: 'gameable.start_time' },
        { text: this.$t('End time'), value: 'gameable.end_time' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

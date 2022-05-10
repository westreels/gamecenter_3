<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account : { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:gameable.path="{ item: { gameable: { path } } }">
      {{ path.map(i => i ? $t('Right') : $t('Left')).join(', ') }}
    </template>
    <template v-slot:gameable.bucket="{ item: { gameable: { bucket } } }">
      {{ bucket + 1 }}
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
        { text: this.$t('Path'), value: 'gameable.path' },
        { text: this.$t('Bucket'), value: 'gameable.bucket' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

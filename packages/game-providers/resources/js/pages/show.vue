<template>
  <div>
    <key-value-table
      name="game"
      :api="`/api/history/games/${id}`"
      :headers="headers"
    >
      <template v-slot:account.user.name="{ item: { account : { user } } }">
        <user-profile-modal :user="user" />
      </template>
    </key-value-table>
  </div>
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
        { text: this.$t('External ID'), value: 'gameable.external_id' },
        { text: this.$t('Player'), value: 'account.user.name' },
        { text: this.$t('Game'), value: 'title' },
        { text: this.$t('Bet'), value: 'bet', format: 'decimal' },
        { text: this.$t('Win'), value: 'win', format: 'decimal' },
        { text: this.$t('Profit'), value: 'profit', format: 'decimal' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

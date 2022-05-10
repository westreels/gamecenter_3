<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account : { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:gameable.bet_titles="{ item: { gameable } }">
      <v-badge
        v-for="(amount, type) in gameable.bet_titles"
        :key="type"
        color="primary"
        :content="amount"
        :value="true"
        inline
        class="ml-2"
      >
        {{ type }}
      </v-badge>
    </template>
    <template v-slot:gameable.win_titles="{ item: { gameable } }">
      <v-badge
        v-for="(amount, type) in gameable.win_titles"
        :key="type"
        color="primary"
        :content="amount"
        :value="true"
        inline
        class="ml-2"
      >
        {{ type }}
      </v-badge>
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
        { text: this.$t('Bets'), value: 'gameable.bet_titles' },
        { text: this.$t('Wins'), value: 'gameable.win_titles' },
        { text: this.$t('Winning number'), value: 'gameable.win_number' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

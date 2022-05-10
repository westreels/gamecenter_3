<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account : { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:gameable.variation="{ item: { gameable: { variation } } }">
      {{ variations[variation] ? (variations[variation]._title || variations[variation].title) : $t('Slots') }}
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
import { config } from '~/plugins/config'
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
        { text: this.$t('Variation'), value: 'gameable.variation' },
        { text: this.$t('Lines'), value: 'gameable.lines' },
        { text: this.$t('Wins'), value: 'gameable.win_titles' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    },
    variations () {
      return config('slots.variations')
    }
  },

  methods: {
    config
  }
}
</script>

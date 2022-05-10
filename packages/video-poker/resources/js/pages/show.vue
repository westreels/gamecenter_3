<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account : { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:deck="{ item: { gameable } }">
      <card v-for="card in gameable.deck.slice(0,15)" :key="`${card}`" :card="card" />
      ...
    </template>
    <template v-slot:initial_hand="{ item: { gameable } }">
      <card v-for="card in gameable.deck.slice(0,5)" :key="`${card}`" :card="card" />
    </template>
    <template v-slot:hold="{ item: { gameable } }">
      <card v-for="index in gameable.hold" :key="`${gameable.deck[index]}`" :card="gameable.deck[index]" />
    </template>
    <template v-slot:final_hand="{ item: { gameable } }">
      <card v-for="card in gameable.hand" :key="`${card}`" :card="card" />
    </template>
  </key-value-table>
</template>

<script>
import KeyValueTable from '~/components/KeyValueTable'
import PlayingCardAbbreviation from '~/components/Games/BasicCardGame/PlayingCardAbbreviation'
import UserProfileModal from '~/components/UserProfileModal'

export default {
  components: { UserProfileModal, KeyValueTable, Card: PlayingCardAbbreviation },

  props: ['id'],

  computed: {
    headers () {
      return [
        { text: this.$t('Player'), value: 'account.user.name' },
        { text: this.$t('Game'), value: 'title' },
        { text: this.$t('Bet'), value: 'bet', format: 'decimal' },
        { text: this.$t('Win'), value: 'win', format: 'decimal' },
        { text: this.$t('Profit'), value: 'profit', format: 'decimal' },
        { text: this.$t('Deck'), value: 'deck' },
        { text: this.$t('Initial hand'), value: 'initial_hand' },
        { text: this.$t('Hold'), value: 'hold' },
        { text: this.$t('Final hand'), value: 'final_hand' },
        { text: this.$t('Result'), value: 'gameable.result' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

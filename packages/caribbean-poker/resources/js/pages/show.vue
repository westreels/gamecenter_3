<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
  >
    <template v-slot:account.user.name="{ item: { account : { user } } }">
      <user-profile-modal :user="user" />
    </template>
    <template v-slot:gameable.deck="{ item: { gameable } }">
      <card v-for="card in gameable.deck.slice(0,15)" :key="`deck-${card}`" :card="card" />
      ...
    </template>
    <template v-slot:gameable.player_cards="{ item: { gameable } }">
      <card v-for="card in gameable.player_cards" :key="card" :card="card" />
    </template>
    <template v-slot:gameable.dealer_cards="{ item: { gameable } }">
      <template v-if="gameable.dealer_cards.length">
        <card v-for="card in gameable.dealer_cards" :key="card" :card="card" />
      </template>
      <span v-else>&mdash;</span>
    </template>
    <template v-slot:gameable.player_hand="{ item: { gameable } }">
      <card v-for="card in gameable.player_hand" :key="card" :card="card" />
      /
      <span>{{ gameable.player_hand_title }}</span>
    </template>
    <template v-slot:gameable.dealer_hand="{ item: { gameable } }">
      <template v-if="gameable.dealer_hand.length">
        <card v-for="card in gameable.dealer_hand" :key="card" :card="card" />
        /
        <span>{{ gameable.dealer_hand_title }}</span>
      </template>
      <span v-else>&mdash;</span>
    </template>
    <template v-slot:gameable.ante_bet_win="{ item: { gameable } }">
      {{ gameable.ante_bet }} / {{ gameable.ante_win }}
    </template>
    <template v-slot:gameable.call_bet_win="{ item: { gameable } }">
      {{ gameable.call_bet }} / {{ gameable.call_win }}
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
        { text: this.$t('Deck'), value: 'gameable.deck' },
        { text: this.$t('Player cards'), value: 'gameable.player_cards' },
        { text: this.$t('Dealer cards'), value: 'gameable.dealer_cards' },
        { text: this.$t('Player hand'), value: 'gameable.player_hand' },
        { text: this.$t('Dealer hand'), value: 'gameable.dealer_hand' },
        { text: this.$t('Ante bet / win'), value: 'gameable.ante_bet_win' },
        { text: this.$t('Call bet / win'), value: 'gameable.call_bet_win' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

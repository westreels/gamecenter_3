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
    <template v-slot:gameable.dealer_hand="{ item: { gameable } }">
      <card v-for="card in gameable.dealer_hand" :key="`dealer-${card}`" :card="card" />
    </template>
    <template v-slot:gameable.dealer_score="{ item: { gameable } }">
      {{ gameable.dealer_score }}
      <template v-if="gameable.dealer_blackjack">
        ({{ $t('Blackjack') }})
      </template>
    </template>
    <template v-slot:gameable.player_hand="{ item: { gameable } }">
      <card v-for="card in gameable.player_hand" :key="`player-${card}`" :card="card" />
      <template v-if="gameable.player_hand2">
        <span class="mx-1">/</span>
        <card v-for="card in gameable.player_hand2" :key="`player2-${card}`" :card="card" />
      </template>
    </template>
    <template v-slot:gameable.player_score="{ item: { gameable } }">
      {{ gameable.player_score }}
      <template v-if="gameable.player_blackjack">
        ({{ $t('Blackjack') }})
      </template>
      <template v-else-if="gameable.player_score2 > 0">
        <span>/</span>
        {{ gameable.player_score2 }}
      </template>
    </template>
    <template v-slot:gameable.bet="{ item: { gameable } }">
      {{ gameable.bet }}
      <template v-if="gameable.bet2">
        <span>/</span>
        {{ gameable.bet2 }}
      </template>
    </template>
    <template v-slot:gameable.win="{ item: { gameable } }">
      {{ gameable.win }}
      <template v-if="gameable.win2">
        <span>/</span>
        {{ gameable.win2 }}
      </template>
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
        { text: this.$t('Dealer hand'), value: 'gameable.dealer_hand' },
        { text: this.$t('Dealer score'), value: 'gameable.dealer_score' },
        { text: this.$t('Player hand'), value: 'gameable.player_hand' },
        { text: this.$t('Player score'), value: 'gameable.player_score' },
        { text: this.$t('Insurance bet'), value: 'gameable.insurance_bet', format: 'decimal' },
        { text: this.$t('Insurance win'), value: 'gameable.insurance_win', format: 'decimal' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

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
      <card v-for="card in gameable.deck.slice(0,8)" :key="`deck-${card}`" :card="card" />
      ...
    </template>
    <template v-slot:gameable.player_bet="{ item: { gameable } }">
      {{ gameable.player_bet }} / {{ gameable.player_win }}
    </template>
    <template v-slot:gameable.banker_bet="{ item: { gameable } }">
      {{ gameable.banker_bet }} / {{ gameable.banker_win }}
    </template>
    <template v-slot:gameable.tie_bet="{ item: { gameable } }">
      {{ gameable.tie_bet }} / {{ gameable.tie_win }}
    </template>
    <template v-slot:gameable.player_hand="{ item: { gameable } }">
      <card v-for="card in gameable.player_hand" :key="`player-${card}`" :card="card" />
    </template>
    <template v-slot:gameable.player_score="{ item: { gameable } }">
      {{ gameable.player_score }}
    </template>
    <template v-slot:gameable.banker_hand="{ item: { gameable } }">
      <card v-for="card in gameable.banker_hand" :key="`banker-${card}`" :card="card" />
    </template>
    <template v-slot:gameable.banker_score="{ item: { gameable } }">
      {{ gameable.banker_score }}
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
        { text: this.$t('Player bet / win'), value: 'gameable.player_bet' },
        { text: this.$t('Banker bet / win'), value: 'gameable.banker_bet' },
        { text: this.$t('Tie bet / win'), value: 'gameable.tie_bet' },
        { text: this.$t('Player hand'), value: 'gameable.player_hand' },
        { text: this.$t('Player score'), value: 'gameable.player_score' },
        { text: this.$t('Banker hand'), value: 'gameable.banker_hand' },
        { text: this.$t('Banker score'), value: 'gameable.banker_score' },
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    }
  }
}
</script>

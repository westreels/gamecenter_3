<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col>
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Poker tester') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <div v-for="(player, i) in players" :key="i" class="mb-5">
              <h5 class="text-h6 font-weight-thin mb-3">
                {{ $t('Player') }} {{ i+1 }}
              </h5>
              <v-autocomplete
                v-model="players[i].cards"
                label="Cards"
                :items="players[i].cards.length < 2 ? deck : players[i].cards"
                color="primary"
                outlined
                solo
                chips
                deletable-chips
                hide-details
                hide-selected
                multiple
                full-width
                :no-data-text="$t('You can not add more than 2 cards')"
              >
                <template v-slot:item="{ item: card }">
                  <card :card="card" />
                </template>
                <template v-slot:selection="data">
                  <v-chip
                    v-bind="data.attrs"
                    :input-value="data.selected"
                    close
                    label
                    color="secondary"
                    large
                    @click="data.select"
                    @click:close="removePlayerCard(i, data.item)"
                  >
                    <card :card="data.item" />
                  </v-chip>
                </template>
              </v-autocomplete>
              <div class="mt-2">
                <h5 v-if="result" class="text-h6 font-weight-thin">
                  <span>{{ $t('Combination') }}</span>
                  <v-chip outlined label pill>
                    {{ result[i].combination }}
                  </v-chip>
                  <span class="ml-5">{{ $t('Hand') }}</span>
                  <card v-for="card in result[i].hand" :key="`hand-${card}`" :card="card" />
                  <span class="ml-5">{{ $t('Kickers') }}</span>
                  <card v-for="card in result[i].kickers" :key="`kickers-${card}`" :card="card" />
                </h5>
                <h5 v-else class="text-h6 font-weight-thin">
                  {{ $t('Hand not evaluated') }}
                </h5>
              </div>
            </div>
            <div>
              <h4 class="text-h6 font-weight-thin mb-3">
                {{ $t('Community cards') }}
              </h4>
              <v-autocomplete
                v-model="communityCards"
                label="Cards"
                :items="communityCards.length < 5 ? deckLessPlayersCards : communityCards"
                color="primary"
                outlined
                solo
                chips
                deletable-chips
                hide-details
                hide-selected
                multiple
                full-width
                :no-data-text="$t('You can not add more than 5 cards')"
              >
                <template v-slot:item="{ item: card }">
                  <card :card="card" />
                </template>
                <template v-slot:selection="data">
                  <v-chip
                    v-bind="data.attrs"
                    :input-value="data.selected"
                    close
                    outlined
                    label
                    @click="data.select"
                    @click:close="removeCommunityCard(data.item)"
                  >
                    <card :card="data.item" />
                  </v-chip>
                </template>
              </v-autocomplete>
            </div>
            <div class="mt-5">
              <v-btn color="primary" large @click="evaluate" :loading="loading" :disabled="loading">
                {{ $t('Evaluate') }}
              </v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import PlayingCardAbbreviation from '~/components/Games/BasicCardGame/PlayingCardAbbreviation'

export default {
  components: { Card: PlayingCardAbbreviation },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Poker tester') }
  },

  data () {
    return {
      cardSuites: ['C', 'D', 'H', 'S'],
      cardValues: ['2', '3', '4', '5', '6', '7', '8', '9', 'T', 'J', 'Q', 'K', 'A'],
      players: [
        { cards: ['ST', 'DJ'] },
        { cards: ['DT', 'DA'] }
      ],
      communityCards: ['C2', 'C4', 'H6', 'HT', 'S3'],
      result: null,
      loading: false
    }
  },

  computed: {
    deck () {
      const deck = []

      this.cardSuites.forEach(suit => {
        this.cardValues.forEach(value => {
          deck.push(suit + value)
        })
      })

      return deck
    },
    deckLessPlayersCards () {
      return this.deck.filter(card => this.playersCards.indexOf(card) === -1)
    },
    playersCards () {
      return this.players.reduce((a, player) => {
        a = a.concat(player.cards)
        return a
      }, [])
    }
  },

  methods: {
    async evaluate () {
      this.loading = true

      const { data: result } = await axios.post('/api/admin/tests/poker', {
        players: this.players,
        community_cards: this.communityCards
      })

      this.loading = false
      this.result = result
    },
    removePlayerCard (playerIndex, card) {
      const cardIndex = this.players[playerIndex].cards.indexOf(card)

      if (cardIndex >= 0) {
        this.players[playerIndex].cards.splice(cardIndex, 1)
      }
    },
    removeCommunityCard (card) {
      const cardIndex = this.communityCards.indexOf(card)

      if (cardIndex >= 0) {
        this.communityCards.splice(cardIndex, 1)
      }
    }
  }
}
</script>
<style scoped lang="scss">

</style>

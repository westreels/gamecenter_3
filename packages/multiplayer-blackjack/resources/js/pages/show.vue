<template>
  <key-value-table
    name="game"
    :api="`/api/history/games/${id}`"
    :headers="headers"
    @load="game = $event.data"
  >
    <template v-slot:gameable.deck="{ item: { gameable } }">
      <card v-for="card in gameable.deck.slice(0,15)" :key="card" :card="card" />
      ...
    </template>
    <template v-for="(hand, userId) in hands" v-slot:[`gameable.player.${userId}.cards`]>
      <div :key="`user-${userId}`">
        <card v-for="card in hands[userId].cards" :key="card" :card="card" />
      </div>
    </template>
    <template v-for="(hand, userId) in hands" v-slot:[`gameable.player.${userId}.score`]>
      <span :key="userId">{{ hands[userId].score }}</span>
    </template>
    <template v-for="(hand, userId) in hands" v-slot:[`gameable.player.${userId}.win`]>
      <span :key="userId">{{ hands[userId].win }}</span>
    </template>
  </key-value-table>
</template>

<script>
import KeyValueTable from '~/components/KeyValueTable'
import PlayingCardAbbreviation from '~/components/Games/BasicCardGame/PlayingCardAbbreviation'

export default {
  components: { KeyValueTable, Card: PlayingCardAbbreviation },

  props: ['id'],

  data () {
    return {
      game: null
    }
  },

  computed: {
    hands () {
      return this.game ? this.game.gameable.hands : []
    },
    headers () {
      return [
        { text: this.$t('Deck'), value: 'gameable.deck' },
        ...this.playerHeaders,
        { text: this.$t('Played'), value: 'updated_ago' }
      ]
    },
    playerHeaders () {
      const result = []

      if (this.game) {
        Object.keys(this.game.gameable.hands).forEach((userId, i) => {
          result.push({ text: this.$t('Player {0} hand', [i + 1]), value: `gameable.player.${userId}.cards` })
          result.push({ text: this.$t('Player {0} score', [i + 1]), value: `gameable.player.${userId}.score` })
          result.push({ text: this.$t('Player {0} win', [i + 1]), value: `gameable.player.${userId}.win` })
        })
      }

      return result
    }
  }
}
</script>

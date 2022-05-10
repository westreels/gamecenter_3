<template>
  <v-container class="mt-10">
    <v-row>
      <v-col class="text-center">
        <h3 class="display-1 font-weight-thin">
          {{ $t('Recent games') }}
        </h3>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12" md="6">
        <v-list v-if="recentGames === null">
          <v-skeleton-loader v-for="(v,i) in Array(10).fill(0)" :key="i" type="list-item-avatar-three-line" />
        </v-list>
        <p v-else-if="recentGames.length === 0" class="text-center">
          {{ $t('No games found') }}
        </p>
        <v-timeline v-else dense>
          <v-timeline-item
            v-for="game in recentGames"
            :key="game.id"
            right
            large
          >
            <template v-slot:icon>
              <user-avatar :user="game.account.user" :size="50" />
            </template>
            <v-card :elevation="5" class="recent-game-card" :class="{ win: game.win >= game.bet, loss: game.win < game.bet }">
              <v-card-title class="text-h6">
                {{ game.title }}
              </v-card-title>
              <v-card-text class="text-subtitle-1 py-0">
                <template v-if="game.win > 0">
                  {{ $t('{0} bet {1} and won {2} credits', [game.account.user.name, game.bet, game.win > 999 ? decimal(game.win) : game.win]) }}
                </template>
                <template v-else>
                  {{ $t('{0} bet {1} credits and lost', [game.account.user.name, game.bet]) }}
                </template>
              </v-card-text>
              <v-card-actions class="justify-space-between">
                <span class="text--secondary ml-2">
                  <v-icon class="text--secondary mr-1">
                    mdi-clock-outline
                  </v-icon>
                  {{ game.updated_ago }}
                </span>
                <v-btn small :to="{ name: 'history.games.show', params: { id: game.id } }" :color="game.win >= game.bet ? 'success' : 'error'" class="white--text">
                  {{ $t('View') }}
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-timeline-item>
        </v-timeline>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import { decimal } from '~/plugins/format'
import { config } from '~/plugins/config'
import UserAvatar from '~/components/UserAvatar'

export default {
  components: { UserAvatar },

  data () {
    return {
      recentGames: null
    }
  },

  created () {
    this.pullRecentGames()
  },

  methods: {
    config,
    decimal,
    async pullRecentGames () {
      const { data } = await axios.get('/api/pub/games/recent')

      this.recentGames = data
    }
  }
}
</script>
<style lang="scss" scoped>
.recent-game-card {
  border-width: 1px;
  border-style: solid;

  &.win {
    border-color: var(--v-success-base);
  }
  &.loss {
    border-color: var(--v-error-base);
  }
}
</style>

<template>
  <div>
    <template v-for="(game, i) in Object.keys(games).map(k => games[k])">
      <v-hover v-slot:default="{ hover }" :key="game.id">
        <v-snackbar
          app
          left
          :dark="$vuetify.theme.isDark"
          :light="!$vuetify.theme.isDark"
          bottom
          tile
          :value="true"
          :elevation="hover ? 8 : 2"
          :timeout="timeout"
          transition="scroll-y-transition"
          :style="`margin-bottom: ${i * 80}px; opacity: ${hover ? 1 : 0.8}`"
          class="game-snackbar"
        >
          <div class="d-flex justify-space-between">
            <div class="d-flex align-center">
              <user-avatar :user="game.account.user" />
              <div class="d-flex flex-column ml-2">
                <user-profile-modal :user="game.account.user">
                  <template v-slot="{ on }">
                    <span class="link" v-on="on" :style="game.win > game.bet ? 'color: #80FF00' : (game.win < game.bet ? 'color: #FF3333' : 'color: #FFFF00')">
                      {{ truncate(game.account.user.name) }}
                    </span>
                  </template>
                </user-profile-modal>
                <div class="mt-1">
                  <game-detail-modal :game="game"/>
                </div>
              </div>
            </div>
            <div class="d-flex align-center">
              <v-chip
                label
                outlined
                small
                :color="game.win > game.bet ? 'success' : (game.win < game.bet ? 'error' : 'warning')"
              >
                {{ $t('Profit') }} {{ decimal(game.win - game.bet, 2) }}
              </v-chip>
            </div>
          </div>
        </v-snackbar>
      </v-hover>
    </template>
  </div>
</template>

<script>
import { config } from '~/plugins/config'
import { truncate } from '~/plugins/utils'
import { decimal } from '~/plugins/format'
import UserAvatar from '~/components/UserAvatar'
import UserProfileModal from '~/components/UserProfileModal'
import GameDetailModal from '~/components/GameDetailModal'
import { GAME_ADD } from '~/store/mutation-types'
import { mapState } from 'vuex'
import DeviceMixin from '~/mixins/Device'

export default {
  components: { GameDetailModal, UserProfileModal, UserAvatar },

  mixins: [DeviceMixin],

  data () {
    return {
      games: {},
      unsubscribe: () => {}
    }
  },

  computed: {
    ...mapState('auth', ['user']),
    ...mapState('settings', ['settings']),
    timeout () {
      return Math.max(1, parseInt(config('settings.interface.game_feed.timeout'), 10)) * 1000
    }
  },

  created () {
    if (!this.isMobile) {
      this.unsubscribe = this.$store.subscribe(({ type, payload: game }, state) => {
        if (this.settings.gameFeed && type === `game/${GAME_ADD}` && this.user.id !== game.account.user.id) {
          this.games = { ...this.games, [game.id]: game }

          setTimeout(() => {
            delete this.games[game.id]
          }, this.timeout)
        }
      })
    }
  },

  beforeDestroy () {
    this.unsubscribe()
  },

  methods: {
    decimal,
    truncate
  }
}
</script>

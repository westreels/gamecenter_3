<template>
  <v-parallax src="/images/home/celebration.jpg" height="400" class="mt-10">
    <div class="d-flex justify-center fill-height darken-50">
      <div class="align-self-center">
        <v-row>
          <v-col class="d-flex align-center justify-center">
            <v-icon size="50" class="mr-5" color="white">
              mdi-medal
            </v-icon>
            <h3 class="display-1">
              {{ $t('Biggest win') }}
            </h3>
          </v-col>
        </v-row>
        <v-row v-if="game" class="my-5">
          <v-col class="text-center">
            <v-chip label outlined x-large color="grey lighten-3" class="display-1 font-weight-thin pa-5">
              {{ decimal(game.win) }} {{ $t('credits ') }}
            </v-chip>
          </v-col>
        </v-row>
        <v-row v-if="game">
          <v-col class="d-flex align-center justify-center">
            <user-avatar :user="game.account.user" :size="50" class="mr-5" />
            <div class="d-flex flex-column">
              <span class="text-h5 font-weight-thin">{{ game.account.user.name }}</span>
              <span class="grey--text text--lighten-1">{{ $t('won in {0} {1}', [game.title, game.updated_ago]) }}</span>
            </div>
          </v-col>
        </v-row>
        <v-row v-if="!game">
          <v-col>
            <div class="display-1 font-weight-thin">
              {{ $t('No one won big yet') }}
            </div>
          </v-col>
        </v-row>
      </div>
    </div>
  </v-parallax>
</template>
<script>
import { mapState } from 'vuex'
import UserAvatar from '~/components/UserAvatar'
import { decimal } from '~/plugins/format'

export default {
  components: { UserAvatar },

  computed: {
    ...mapState('game', { game: 'biggestWin' })
  },

  methods: {
    decimal
  }
}
</script>
<style lang="scss" scoped>
.darken-50 {
  animation: glow 5s linear infinite;
}

@keyframes glow {
  0% {
    background: rgba(0, 0, 0, 0.4);
  }

  50% {
    background: rgba(0, 0, 0, 0.6);
  }

  100% {
    background: rgba(0, 0, 0, 0.4);
  }
}
</style>

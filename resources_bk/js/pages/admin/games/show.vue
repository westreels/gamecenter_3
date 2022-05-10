<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" lg="8">
        <v-card>
          <v-toolbar>
            <v-btn icon @click="$router.go(-1)">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Game {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <game-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <game-detail :id="id" />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import GameDetail from '~/components/GameDetail'
import GameMenu from '~/components/Admin/GameMenu'

export default {
  components: { GameMenu, GameDetail },

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  props: {
    id: {
      type: [Number, String],
      required: true
    }
  },

  metaInfo () {
    return { title: this.$t('Game {0}', [this.id]) }
  }
}
</script>

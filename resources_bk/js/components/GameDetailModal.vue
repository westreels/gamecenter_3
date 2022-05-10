<template>
  <v-dialog v-model="modal" width="500">
    <template v-slot:activator="{ on }">
      <slot :on="on">
        <span class="link" v-on="on">
          {{ game.title }} #{{ game.id }}
        </span>
      </slot>
    </template>
    <v-card>
      <v-toolbar>
        <v-toolbar-title>
          {{ $t('Game {0}', [game.id]) }}
        </v-toolbar-title>
        <v-spacer />
        <v-btn icon @click="modal = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text>
        <game-detail :id="game.id" />
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import GameDetail from '~/components/GameDetail'

export default {
  components: { GameDetail },

  props: {
    game: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      modal: false
    }
  }
}
</script>

<style lang="scss" scoped>
  .link {
    color: var(--v-anchor-base);
  }
</style>

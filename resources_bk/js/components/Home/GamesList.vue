<template>
  <v-container class="mt-10">
    <v-row>
      <v-col class="text-center">
        <h3 class="display-1 font-weight-thin">
          {{ $t('Enjoy our exciting games') }}
        </h3>
      </v-col>
    </v-row>
    <v-row v-if="categories.length > 1">
      <v-col>
        <v-chip-group
          v-model="selectedCategory"
          active-class="primary"
          mandatory
        >
          <v-chip label active @click="filterByCategory('')">
            {{ $t('All') }}
          </v-chip>
          <v-chip v-for="category in categories" :key="category" label @click="filterByCategory(category)">
            {{ category }}
          </v-chip>
        </v-chip-group>
      </v-col>
    </v-row>
    <v-row ref="games" class="justify-center">
      <template v-for="(game, i) in games">
        <v-col
          :key="i"
          cols="12"
          md="6"
          lg="3"
          :data-groups="JSON.stringify(game.categories)"
          class="game-card"
        >
          <game-card :id="game.id" :name="game.name" :banner="game.banner" :slug="game.slug" />
        </v-col>
      </template>
    </v-row>
  </v-container>
</template>

<script>
import { config } from '~/plugins/config'
import Shuffle from 'shufflejs'
import GameCard from '~/components/GameCard'
import { mapGetters } from 'vuex'

export default {
  components: { GameCard },

  data () {
    return {
      selectedCategory: null,
      shuffle: null
    }
  },

  computed: {
    ...mapGetters({ games: 'package-manager/getGames' }),
    categories () {
      const categories = this.games.reduce((a, game) => a.concat(game.categories), [])

      // remove duplicates
      return categories.filter((category, i) => category && categories.indexOf(category) === i)
    }
  },

  methods: {
    config,
    filterByCategory (category) {
      if (!this.shuffle) {
        this.shuffle = new Shuffle(this.$refs.games, { itemSelector: '.game-card' })
      }

      this.shuffle.filter(category)
    }
  }
}
</script>
<style lang="scss" scoped>
.v-chip-group::v-deep {
  .v-slide-group__content {
    justify-content: center;
  }
}
</style>

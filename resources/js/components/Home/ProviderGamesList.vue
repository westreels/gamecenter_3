<template>
  <v-container v-if="packageEnabled">
    <dynamic-games-list
      v-for="(item, i) in featuredCategoryGames"
      :key="i"
      :title="item.title"
      :games="item.games"
      :display-style="config('settings.content.home.provider_games.display_style')"
      :display-count="config('settings.content.home.provider_games.display_count')"
      :load-count="config('settings.content.home.provider_games.load_count')"
      class="mt-10"
    />
  </v-container>
</template>

<script>
import axios from 'axios'
import { config } from '~/plugins/config'
import { route } from '~/plugins/route'
import DynamicGamesList from '~/components/Home/DynamicGamesList'

export default {
  components: { DynamicGamesList },

  data () {
    return {
      games: [],
      featuredCategories: config('settings.content.home.provider_games.featured_categories')
    }
  },

  computed: {
    packageEnabled () {
      return this.$store.getters['package-manager/getById']('game-providers')
    },
    featuredCategoryGames () {
      return this.featuredCategories
        .map(item => {
          item.games = this.games
            .filter(game => item.categories.indexOf(game.category) > -1)
            .sort((a, b) => {
              return a.name < b.name ? -1 : (a.name > b.name ? 1 : 0)
            })
          return item
        })
    }
  },

  async created () {
    const { data } = await axios.get(route('game-providers.games.all'))
    this.games = data
  },

  methods: {
    config
  }
}
</script>

<template>
  <v-container class="mt-5">
    <dynamic-games-list
      ref="games"
      :title="$t('{0} games', [ucfirst(provider)])"
      :games="games"
      :display-style="config('settings.content.home.provider_games.display_style')"
      :filter="true"
      class="mt-10"
    />
  </v-container>
</template>

<script>
import axios from 'axios'
import { config } from '~/plugins/config'
import { route } from '~/plugins/route'
import { ucfirst, unique } from '~/plugins/utils'
import DynamicGamesList from '~/components/Home/DynamicGamesList'

export default {
  components: { DynamicGamesList },

  middleware: [],

  metaInfo () {
    return { title: this.$t('{0} games', [this.provider]) }
  },

  data () {
    return {
      games: []
    }
  },

  computed: {
    provider () {
      return this.$route.params.provider
    },
    categories () {
      return unique(this.games.map(game => game.category))
    }
  },

  async created () {
    const { data } = await axios.get(route('game-providers.games.by-provider').replace('{provider}', this.provider))
    this.games = data
  },

  methods: {
    config,
    ucfirst
  }
}
</script>


<template>
  <v-container class="mt-10">
    <dynamic-games-list
      :title="$t('Original games')"
      :games="games"
      :display-style="config('settings.content.home.games.display_style')"
      :display-count="config('settings.content.home.games.display_count')"
      :load-count="config('settings.content.home.games.load_count')"
    />
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex'
import { config } from '~/plugins/config'
import DynamicGamesList from '~/components/Home/DynamicGamesList'

export default {
  components: { DynamicGamesList },

  computed: {
    ...mapGetters({ games: 'package-manager/getGames' })
  },

  methods: {
    config
  }
}
</script>

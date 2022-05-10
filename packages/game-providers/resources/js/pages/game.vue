<template>
  <iframe v-if="gameUrl" frameborder="0" allowfullscreen :src="gameUrl" />
</template>

<script>
import axios from 'axios'
import { route } from '~/plugins/route'

export default {
  middleware: ['auth', 'verified', '2fa_passed'],

  metaInfo () {
    return { title: this.$t('{0} games', [this.provider]) }
  },

  data () {
    return {
      gameUrl: null
    }
  },

  computed: {
    provider () {
      return this.$route.params.provider
    },
    game () {
      return this.$route.params.game
    }
  },

  async created () {
    const { data } = await axios.get(route('game-providers.games.url').replace('{provider}', this.provider).replace('{id}', this.game))
    this.gameUrl = data
  }
}
</script>
<style lang="scss" scoped>
iframe {
  height: calc(100vh - 100px);
  width: 100%;
  overflow: hidden;
}
</style>

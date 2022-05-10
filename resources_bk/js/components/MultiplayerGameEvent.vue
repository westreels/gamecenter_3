<template></template>

<script>
import axios from 'axios'
import { mapState } from 'vuex'

export default {
  data () {
    return {
      channel: null
    }
  },

  computed: {
    ...mapState('auth', ['user']),
    ...mapState('broadcasting', ['echo']),
    gamePackageId () {
      return this.$route.params.game
    }
  },

  async created () {
    // it's important to wait for next tick,
    // because the game component can be initialized from beforeRouteUpdate() hook,
    // when the route parameters are not yet updated
    this.$nextTick(() => {
      // note that there is no access to this.$route in beforeDestroy() hook,
      // that's why channel is stored as data property and not computed property
      this.channel = `multiplayer-game.${this.gamePackageId}`
      this.fetchGame()
      this.subscribe()
    })
  },

  beforeDestroy () {
    this.unsubscribe()
  },

  methods: {
    async fetchGame () {
      const { data: game } = await axios.get(`/api/multiplayer-games/${this.gamePackageId}`)

      this.$emit('game-init', game)
    },
    subscribe () {
      if (!this.echo) {
        return false
      }

      this.echo.join(this.channel)
        // game event
        .listen('MultiplayerGameAction', event => {
          this.$emit('event', event)
        })
    },
    unsubscribe () {
      if (!this.echo) {
        return false
      }

      this.echo.leave(this.channel)
    }
  }
}
</script>

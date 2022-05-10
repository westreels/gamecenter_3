<template>
  <component :is="layout" v-if="layout" :class="routeClass"/>
</template>
<script>
import { config } from '~/plugins/config'
import { mapState, mapGetters } from 'vuex'

// Load layout components dynamically.
const requireContext = require.context('~/layouts', false, /.*\.vue$/)

const layouts = requireContext.keys()
  .map(file =>
    [file.replace(/(^.\/)|(\.vue$)/g, ''), requireContext(file)]
  )
  .reduce((components, [name, component]) => {
    components[name] = component.default || component
    return components
  }, {})

export default {
  data () {
    return {
      layout: null
    }
  },

  metaInfo: {
    // if no subcomponents specify a metaInfo.title, this title will be used
    title: 'Page',
    // all titles will be injected into this template
    titleTemplate: '%s | ' + config('app.name')
  },

  computed: {
    ...mapState('broadcasting', ['echo']),
    ...mapGetters({ authenticated: 'auth/check' }),
    routeClass () {
      if (!this.$route.name) {
        return ''
      }

      let result = this.$route.name.replaceAll('.', '-')

      if (this.$route.name === 'game') {
        result += `-${this.$route.params.game}`
      } else if (this.$route.name === 'page') {
        result += `-${this.$route.params.id}`
      }

      return `view-${result}`
    },
    displayOnlineStatus () {
      return config('settings.interface.online.enabled')
    },
    gameFeedDelay () {
      return Math.max(0, parseInt(config('settings.interface.game_feed.delay'), 10)) * 1000
    }
  },

  created () {
    if (this.echo) {
      if (this.displayOnlineStatus && this.authenticated) {
        this.joinOnlineChannel()
      }

      if (this.authenticated) {
        this.joinGameFeedChannel()
      }

      this.$watch('authenticated', (isAuthenticated, wasAuthenticated) => {
        if (!wasAuthenticated && isAuthenticated) {
          if (this.displayOnlineStatus) {
            this.joinOnlineChannel()
          }
          this.joinGameFeedChannel()
        } else if (wasAuthenticated && !isAuthenticated) {
          if (this.displayOnlineStatus) {
            this.leaveOnlineChannel()
          }
          this.leaveGameFeedChannel()
        }
      })
    }
  },

  methods: {
    setLayout (layout) {
      this.layout = layouts[layout]
    },

    joinOnlineChannel () {
      this.echo.join('online')
        // currently joined users
        .here(users => this.$store.dispatch('online/init', { users }))
        // new user joined
        .joining(user => this.$store.dispatch('online/add', { user }))
        // user left
        .leaving(user => this.$store.dispatch('online/remove', { user }))
        // bot joined
        .listen('UserIsOnline', user => {
          this.$store.dispatch('online/add', { user, timeout: 300000 }) // stay online for 5 minutes
        })
    },

    leaveOnlineChannel () {
      this.echo.leave('online')
    },

    joinGameFeedChannel () {
      this.echo.private('games')
        .listen('GamePlayed', game => {
          // add delay before displaying live games, so animation can be completed
          setTimeout(() => {
            this.$store.dispatch('game/add', game)
          }, this.gameFeedDelay)
        })
    },

    leaveGameFeedChannel () {
      this.echo.leave('games')
    }
  }
}
</script>

<template>
  <v-row v-if="providersCount">
    <v-col class="text-center">
      <v-btn v-for="(provider, id) in providers" :key="id" class="mx-2" @click="loginWith(id)" fab icon elevation="5">
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-icon v-on="on" large>
              mdi-{{ provider.mdi || id }}
            </v-icon>
          </template>
          <span>{{ $t('Log in with {0}', [ucfirst(id)]) }}</span>
        </v-tooltip>
      </v-btn>
      <v-divider class="my-7" />
    </v-col>
  </v-row>
</template>

<script>
import { config } from '~/plugins/config'
import { ucfirst } from '~/plugins/utils'
import { mapState } from 'vuex'

export default {
  name: 'OAuth',

  computed: {
    ...mapState('auth', [
      'user'
    ]),
    providers () {
      return config('oauth')
    },
    providersCount () {
      return this.providers ? Object.keys(this.providers).length : 0
    }
  },

  mounted () {
    window.addEventListener('message', this.onMessage, false)
  },

  beforeDestroy () {
    window.removeEventListener('message', this.onMessage)
  },

  methods: {
    ucfirst,
    async loginWith (provider) {
      const newWindow = openWindow('', this.$t('Log in with {0}', [provider]))

      newWindow.location.href = await this.$store.dispatch('auth/fetchOAuthRedirectUrl', { provider })
    },

    /**
     * @param {MessageEvent} e
     */
    async onMessage (e) {
      if (e.origin !== window.origin || !e.data.user) {
        return
      }

      // Update the user
      this.$store.dispatch('auth/updateUser', e.data.user)

      if (this.user.two_factor_auth_enabled && !this.user.two_factor_auth_passed) {
        this.$router.push({ name: '2fa' })
      } else {
        window.location.reload()
      }
    }
  }
}

/**
 * @param  {Object} options
 * @return {Window}
 */
function openWindow (url, title, options = {}) {
  if (typeof url === 'object') {
    options = url
    url = ''
  }

  options = { url, title, width: 600, height: 720, ...options }

  const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screen.left
  const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screen.top
  const width = window.innerWidth || document.documentElement.clientWidth || window.screen.width
  const height = window.innerHeight || document.documentElement.clientHeight || window.screen.height

  options.left = ((width / 2) - (options.width / 2)) + dualScreenLeft
  options.top = ((height / 2) - (options.height / 2)) + dualScreenTop

  const optionsStr = Object.keys(options).reduce((acc, key) => {
    acc.push(`${key}=${options[key]}`)
    return acc
  }, []).join(',')

  const newWindow = window.open(url, title, optionsStr)

  if (window.focus) {
    newWindow.focus()
  }

  return newWindow
}
</script>

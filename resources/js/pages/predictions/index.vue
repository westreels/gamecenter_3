<template>
  <div
    class="fill-height"
    :class="{ 'static-background': backgroundImageUrl }"
    :style="{ backgroundImage: backgroundImageUrl ? `url(${backgroundImageUrl})` : 'none' }"
  >
    <animated-background v-if="!backgroundImageUrl" />
    <component :is="component" :class="['prediction-container', $route.params.packageId]" />
    <prediction-menu />
    <game-feed />
  </div>
</template>

<script>
import { config } from '~/plugins/config'
import AnimatedBackground from '~/components/AnimatedBackground'
import GameFeed from '~/components/GameFeed'
import PredictionMenu from '~/components/Predictions/PredictionMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'prediction'],

  components: { PredictionMenu, GameFeed, AnimatedBackground },

  metaInfo () {
    return { title: this.component ? this.$t(this.component.name) : '' }
  },

  data () {
    return {
      component: null,
      backgroundImageUrl: null
    }
  },

  created () {
    // fetch user info when any game page is open just in case user balance was updated and user didn't refresh the page
    this.$store.dispatch('auth/fetchUser')
    this.initComponent(this.$route.params.packageId)
  },

  methods: {
    getBackgroundImageUrl (packageId) {
      return config(this.$route.params.packageId).background
    },
    async initComponent (packageId) {
      // dynamically load component
      const module = await import(/* webpackChunkName: 'packages/[request]' */`packages/${packageId}/resources/js/pages/game`)

      this.component = module.default
      this.backgroundImageUrl = this.getBackgroundImageUrl(packageId)
    }
  },

  async beforeRouteUpdate (to, from, next) {
    await this.initComponent(to.params.packageId)

    next()
  }
}
</script>

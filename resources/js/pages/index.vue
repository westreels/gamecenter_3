<template>
  <div>
    <dynamic-template
      :template="html"
      :components="{ Slider, GamesList, ProviderGamesList, ProvidersList, Predictions, ProvablyFair, BonusCards, Features, Raffles, BiggestWin, RecentGames, Social }"
    />
  </div>
</template>
<script>
import axios from 'axios'
import DynamicTemplate from '~/components/DynamicTemplate'
import Slider from '~/components/Home/Slider'
import GamesList from '~/components/Home/GamesList'
import ProviderGamesList from '~/components/Home/ProviderGamesList'
import ProvidersList from '~/components/Home/ProvidersList'
import Predictions from '~/components/Home/Predictions'
import ProvablyFair from '~/components/Home/ProvablyFair'
import BonusCards from '~/components/Home/BonusCards'
import Features from '~/components/Home/Features'
import Raffles from '~/components/Home/Raffles'
import BiggestWin from '~/components/Home/BiggestWin'
import RecentGames from '~/components/Home/RecentGames'
import Social from '~/components/Home/Social'
import { mapGetters } from 'vuex'

export default {
  components: { DynamicTemplate },

  metaInfo () {
    return {
      title: this.$t('Play to Earn')
    }
  },

  data () {
    return {
      Slider,
      GamesList,
      ProviderGamesList,
      ProvidersList,
      Predictions,
      ProvablyFair,
      BonusCards,
      Features,
      Raffles,
      BiggestWin,
      RecentGames,
      Social,
      html: ''
    }
  },

  computed: {
    ...mapGetters({ authenticated: 'auth/check' })
  },

  async created () {
    const { data: { html } } = await axios.get('/api/pages/home')
    this.html = `<div>${html}</div>`
    if (this.authenticated) {
      // update user balance
      this.$store.dispatch('auth/fetchUser')
    }
  }
}
</script>

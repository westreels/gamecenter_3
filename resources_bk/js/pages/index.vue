<template>
  <div>
    <dynamic-template
      :template="html"
      :components="{ Slider, GamesList, Predictions, ProvablyFair, BonusCards, Features, Raffles, BiggestWin, RecentGames, Social }"
    />
  </div>
</template>
<script>
import axios from 'axios'
import DynamicTemplate from '~/components/DynamicTemplate'
import Slider from '~/components/Home/Slider'
import GamesList from '~/components/Home/GamesList'
import Predictions from '~/components/Home/Predictions'
import ProvablyFair from '~/components/Home/ProvablyFair'
import BonusCards from '~/components/Home/BonusCards'
import Features from '~/components/Home/Features'
import Raffles from '~/components/Home/Raffles'
import BiggestWin from '~/components/Home/BiggestWin'
import RecentGames from '~/components/Home/RecentGames'
import Social from '~/components/Home/Social'

export default {
  components: { DynamicTemplate },

  metaInfo () {
    return {
      title: this.$t('Fair online casino games')
    }
  },

  data () {
    return {
      Slider,
      GamesList,
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

  async created () {
    const { data: { html } } = await axios.get('/api/pages/home')
    this.html = `<div>${html}</div>`
  }
}
</script>

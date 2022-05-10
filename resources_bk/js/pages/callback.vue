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
    const code = this.$route.query.code
    await this.login(code)
  },
  methods: {
    async login (code) {

      const { data} = await axios.post('/api/auth/login', { code: code})

      if (data) {
        // Store the user
        this.$store.dispatch('auth/updateUser', data)

        // this.$store.dispatch('message/success', { text: this.$t('Login successfully.') })
        if (this.$route.query.uri) {
          window.location = this.$route.query.uri
        } else {
          this.$router.push({ name: 'home' })
        }
      } else {
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>

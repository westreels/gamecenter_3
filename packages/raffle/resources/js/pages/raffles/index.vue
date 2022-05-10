<template>
  <div>
    <v-tabs centered>
      <v-tab v-for="(tab,i) in tabs" :key="i" :to="{ name: tab.route }">
        {{ tab.name }}
      </v-tab>
    </v-tabs>
    <raffle-menu />
    <router-view />
  </div>
</template>

<script>
import RaffleMenu from 'packages/raffle/resources/js/components/RaffleMenu'

export default {
  components: { RaffleMenu },
  middleware: ['auth', 'verified', '2fa_passed'],

  data () {
    return {
      activeTab: null
    }
  },

  computed: {
    tabs () {
      return [
        { route: 'raffles.current', name: this.$t('Current') },
        { route: 'raffles.completed', name: this.$t('Completed') }
      ]
    }
  }
}
</script>

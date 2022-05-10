<template>
  <div>
    <v-tabs centered>
      <v-tab v-for="(tab,i) in tabs" :to="{ name: tab.route }" :key="i">
        {{ tab.name }}
      </v-tab>
    </v-tabs>
    <router-view />
  </div>
</template>

<script>
export default {
  middleware: ['auth', 'verified', '2fa_passed'],

  data () {
    return {
      activeTab: null
    }
  },

  computed: {
    tabs () {
      return [
        { route: 'history.user', name: this.$t('My') },
        { route: 'history.recent', name: this.$t('Recent') },
        { route: 'history.wins', name: this.$t('Wins') },
        { route: 'history.losses', name: this.$t('Losses') }
      ]
    }
  }
}
</script>

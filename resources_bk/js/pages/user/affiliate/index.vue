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
        { route: 'user.affiliate.info', name: this.$t('Info') },
        { route: 'user.affiliate.commissions', name: this.$t('Commissions') },
        { route: 'user.affiliate.stats', name: this.$t('Stats') }
      ]
    }
  }
}
</script>

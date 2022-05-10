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
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  data () {
    return {
      activeTab: null
    }
  },

  computed: {
    tabs () {
      return [
        { route: 'admin.chat.rooms.index', name: this.$t('Rooms') },
        { route: 'admin.chat.messages.index', name: this.$t('Messages') }
      ]
    }
  }
}
</script>

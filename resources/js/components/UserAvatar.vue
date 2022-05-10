<template>
  <v-badge overlap dot :value="displayOnlineStatus" :color="onlineUserIds.indexOf(user.id) > -1 ? 'green' : 'red'">
    <v-avatar :size="size">
      <v-img :src="user.avatar" />
    </v-avatar>
  </v-badge>
</template>

<script>
import { config } from '~/plugins/config'
import { mapGetters } from 'vuex'

export default {
  props: {
    user: {
      type: Object,
      required: true
    },
    size: {
      type: Number,
      required: false,
      default: 35
    }
  },

  computed: {
    ...mapGetters({ onlineUserIds: 'online/ids' }),

    displayOnlineStatus () {
      return config('settings.interface.online.enabled')
    }
  }
}
</script>

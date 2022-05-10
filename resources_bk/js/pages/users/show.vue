<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" sm="9" md="6">
        <user-profile :id="id" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import UserProfile from '~/components/UserProfile'

export default {
  components: { UserProfile },

  middleware: ['auth', 'verified', '2fa_passed'],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('User profile') }
  }
}
</script>

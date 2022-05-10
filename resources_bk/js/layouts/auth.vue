<template>
  <v-app>
    <v-main>
      <animated-background />
      <message />
      <router-view />
    </v-main>

    <secondary-footer />
  </v-app>
</template>

<script>
import { config } from '~/plugins/config'
import Message from '~/components/Message'
import AnimatedBackground from '~/components/AnimatedBackground'
import SecondaryFooter from '~/components/SecondaryFooter'

export default {
  name: 'AuthLayout',

  components: { Message, AnimatedBackground, SecondaryFooter },

  computed: {
    appName () {
      return config('app.name')
    }
  }
}
</script>

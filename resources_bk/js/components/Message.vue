<template>
  <v-row v-if="!!type && !!text" justify="center" class="my-0">
    <v-col sm="12" md="8" lg="6" class="alert-container">
      <v-alert :value="!!type && !!text" :type="type" dismissible transition="scroll-y-transition" @input="close">
        {{ text }}
      </v-alert>
    </v-col>
  </v-row>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: 'Message',

  computed: {
    ...mapState('message', [
      'type',
      'text'
    ])
  },

  methods: {
    close () {
      this.$store.dispatch('message/clear')
    }
  },

  watch: {
    text (after, before) {
      if (before == null && after != null) {
        setTimeout(() => {
          // if not yet closed manually
          if (this.text != null) {
            this.close()
          }
        }, 5000)
      }
    }
  }
}
</script>

<style scoped>
  .alert-container {
    position: fixed;
    z-index: 10;
  }
</style>

<template>
  <transition name="scale">
    <v-alert
      v-if="message"
      dense
      outlined
      text
      color="primary"
    >
      {{ message }}
    </v-alert>
  </transition>
</template>

<script>
export default {
  props: {
    message: {
      required: true,
      validator: value => typeof value === 'string' || value === null
    }
  }
}
</script>

<style lang="scss" scoped>
.scale-enter, .scale-leave-to {
  transform: scale(0);
  opacity: 1;
}

.scale-enter-active, .scale-leave-active {
  transition: all 0.3s;
}
</style>

<template>
  <v-progress-linear
    indeterminate
    absolute
    bottom
    :active="active"
  />
</template>

<script>
export default {
  props: {
    active: {
      type: Boolean,
      required: true
    }
  }
}
</script>

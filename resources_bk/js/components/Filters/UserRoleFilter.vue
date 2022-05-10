<template>
  <v-select
    v-model="value"
    :items="options"
    :label="$t('Role')"
    :disabled="disabled"
    outlined
    multiple
    @change="change"
  />
</template>

<script>
export default {
  props: {
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      value: ['user', 'bot', 'admin'],
      options: [
        { text: this.$t('User'), value: 'user' },
        { text: this.$t('Bot'), value: 'bot' },
        { text: this.$t('Admin'), value: 'admin' }
      ]
    }
  },

  methods: {
    change () {
      this.$emit('change', { roles: this.value })
    }
  }
}
</script>

<template>
  <v-select
    v-model="value"
    :items="options"
    :label="$t('Status')"
    :disabled="disabled"
    outlined
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
      value: null,
      options: [
        { text: this.$t('All'), value: null },
        { text: this.$t('Created'), value: 'created' },
        { text: this.$t('Pending'), value: 'pending' },
        { text: this.$t('Completed'), value: 'completed' },
        { text: this.$t('Cancelled'), value: 'cancelled' }
      ]
    }
  },

  methods: {
    change () {
      this.$emit('change', { status: this.value })
    }
  }
}
</script>

<template>
  <v-select
    v-model="value"
    :items="options"
    :label="$t('Period')"
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
      value: 'month',
      options: [
        { text: this.$t('Week'), value: 'week' },
        { text: this.$t('Month'), value: 'month' },
        { text: this.$t('Year'), value: 'year' }
      ]
    }
  },

  methods: {
    change () {
      this.$emit('change', { period: this.value })
    }
  }
}
</script>

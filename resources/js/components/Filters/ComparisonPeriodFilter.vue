<template>
  <v-select
    v-model="value"
    :items="options"
    :label="$t('Period')"
    :disabled="disabled"
    :solo="solo"
    :hide-details="hideDetails"
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
    },
    hideDetails: {
      type: Boolean,
      required: false,
      default: false
    },
    solo: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      value: 'month',
      options: [
        { text: this.$t('By day'), value: 'day' },
        { text: this.$t('By week'), value: 'week' },
        { text: this.$t('By month'), value: 'month' },
        { text: this.$t('By year'), value: 'year' }
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

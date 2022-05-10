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
      value: null,
      options: [
        { text: this.$t('All time'), value: null },
        { text: this.$t('This week'), value: 'week' },
        { text: this.$t('Previous week'), value: 'prev_week' },
        { text: this.$t('This month'), value: 'month' },
        { text: this.$t('Previous month'), value: 'prev_month' },
        { text: this.$t('This year'), value: 'year' },
        { text: this.$t('Previous year'), value: 'prev_year' }
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

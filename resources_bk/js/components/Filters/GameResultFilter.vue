<template>
  <v-select
    v-model="value"
    :items="options"
    :label="$t('Result')"
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
        { text: this.$t('Profitable'), value: 'profitable' },
        { text: this.$t('Unprofitable'), value: 'unprofitable' }
      ]
    }
  },

  methods: {
    change () {
      this.$emit('change', { result: this.value })
    }
  }
}
</script>

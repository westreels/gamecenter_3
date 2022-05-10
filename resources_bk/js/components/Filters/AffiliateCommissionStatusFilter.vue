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
        { text: this.$t('Pending'), value: 'pending' },
        { text: this.$t('Approved'), value: 'approved' },
        { text: this.$t('Rejected'), value: 'rejected' }
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

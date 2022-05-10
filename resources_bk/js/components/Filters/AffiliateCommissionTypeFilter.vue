<template>
  <v-select
    v-model="value"
    :items="options"
    :label="$t('Type')"
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
        { text: this.$t('Sign up'), value: 'sign_up' },
        { text: this.$t('Game loss'), value: 'game_loss' },
        { text: this.$t('Game win'), value: 'game_win' },
        { text: this.$t('Deposit'), value: 'deposit' }
      ]
    }
  },

  methods: {
    change () {
      this.$emit('change', { type: this.value })
    }
  }
}
</script>

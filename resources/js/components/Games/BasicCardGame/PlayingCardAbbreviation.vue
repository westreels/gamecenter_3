<template>
  <span :class="`${color}--text`">
    {{ rankValue }}<span class="suit">{{ suitSymbol }}</span>
  </span>
</template>

<script>
export default {
  props: {
    card: {
      required: true,
      validator: value => (typeof value === 'string' && value.length === 2) || value === null
    }
  },

  computed: {
    suit () {
      return this.card ? this.card[0] : null
    },
    suitSymbol () {
      return this.suit === 'D' ? '♦'
        : (this.suit === 'C' ? '♣'
          : (this.suit === 'H' ? '♥' : '♠'))
    },
    rank () {
      return this.card ? this.card[1] : null
    },
    rankValue () {
      return this.rank === 'T' ? 10 : this.rank
    },
    color () {
      return ['D', 'H'].indexOf(this.suit) > -1 ? 'red' : 'text--primary' + (this.$vuetify.theme.isDark ? ' text--darken-1' : '')
    }
  }
}
</script>

<style lang="scss" scoped>
.suit {
  font-size: 1.5em;
}
</style>

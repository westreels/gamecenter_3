<template>
  <span>{{ animatedValue }}</span>
</template>
<script>
import { TweenLite } from 'gsap/all'
import { decimal } from '~/plugins/format'

export default {
  props: {
    number: {
      type: Number,
      required: true
    }
  },

  data () {
    return {
      tweenedValue: 0
    }
  },

  computed: {
    animatedValue () {
      // tweenedValue is a decimal, so need to convert it to integer for display
      return decimal(this.tweenedValue)
    }
  },

  watch: {
    number (value) {
      this.tween(value)
    }
  },

  created () {
    this.tween(this.number)
  },

  methods: {
    tween (number) {
      TweenLite.to(this.$data, 0.5, { tweenedValue: number })
    }
  }
}
</script>

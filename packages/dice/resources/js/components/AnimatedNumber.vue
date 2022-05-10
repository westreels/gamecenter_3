<template>
  <span>{{ animatedValue }}</span>
</template>
<script>
  import { TweenLite } from 'gsap/all'

  export default {
    props: {
      number: {
        type: Number,
        required: true
      },
      animating: {
        type: Boolean,
        required: true
      }
    },

    data () {
      return {
        tweenedValue: 0,
        animationCompleted: false
      }
    },

    computed: {
      animatedValue () {
        // tweenedValue is a decimal, so need to convert it to integer for display
        return this.tweenedValue.toFixed(0)
      }
    },

    watch: {
      animating (animating) {
        if (animating) {
          this.animationCompleted = false
          this.tweenedValue = 0
          this.tween(9)
        }
      }
    },

    methods: {
      tween (value) {
        TweenLite.to(this.$data, 0.3, {
          tweenedValue: value,
          delay: 0,
          onComplete: () => {
            // animate from 0 ot 9 and backwards
            if (this.animating) {
              this.tweenedValue = 0
              this.tween(9)
              // when game is completed animate to the result number
            } else if (!this.animationCompleted) {
              this.tween(this.number)
              this.animationCompleted = true
            }
          }
        })
      }
    }
  }
</script>

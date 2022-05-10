<template>
  <div ref="chip" class="chip" :class="{disabled, active}" @click="click">
    <img :src="`/images/games/card-game-ui/bet-chips/${value}.png`">
  </div>
</template>

<script>

import clickSound from '~/../audio/common/click.wav'
import SoundMixin from '~/mixins/Sound'

export default {
  mixins: [SoundMixin],
  props: {
    value: {
      type: Number,
      required: true
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    active: {
      type: Boolean,
      required: false,
      default: false
    },
    slide: {
      type: Number,
      required: true
    }
  },
  methods: {
    click () {
      if (!this.disabled) {
        this.sound(clickSound)
        const chip = this.$refs.chip
        let el = chip
        let x = el.offsetLeft - el.offsetWidth * this.slide
        let y = el.offsetTop
        do {
          el = el.parentElement
          // x += el.offsetLeft
          // y += el.offsetTop
        } while (!el.classList.contains('chips-container'))
        x += el.offsetLeft + 138 / 2
        y += el.offsetTop + 138 / 2
        this.$emit('click', { x, y })
        // const parent = chip.parentElement
        // this.$emit('click', { x: chip.offsetLeft + parent.offsetLeft + chip.offsetWidth / 2, y: chip.offsetTop + parent.offsetTop + chip.offsetHeight / 2 })
      }
    }
  }
}
</script>

<style scoped lang="scss">
div.chip {
  // margin-right: 8px;
  cursor: pointer;
  img {
    transition: filter .15s;
  }
  &.disabled {
    cursor: default;
    img {
      filter: grayscale(1) brightness(0.5);
    }
  }
  &:hover:not(.disabled) {
    img {
      filter: brightness(1.1);
    }
  }
  &:active:not(.disabled) {
    img {
      filter: brightness(1.4);
    }
  }
}
</style>

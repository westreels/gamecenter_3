<template>
  <div class="button" :class="{disabled}" @click="click">
    <div class="sprite">
      <img class="normal" :src="`/images/games/card-game-ui/${type}.png`">
      <img class="disabled" :src="`/images/games/card-game-ui/${type}-disabled.png`">
      <img class="hover" :src="`/images/games/card-game-ui/${type}-hover.png`">
      <img class="active" :src="`/images/games/card-game-ui/${type}-active.png`">
    </div>
    <div class="btn-title">
      {{ title }}
    </div>
  </div>
</template>

<script>

import clickSound from '~/../audio/common/click.wav'
import SoundMixin from '~/mixins/Sound'

export default {
  mixins: [SoundMixin],
  props: {
    title: {
      type: String,
      required: true
    },
    type: {
      type: String,
      required: true
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  methods: {
    click () {
      if (!this.disabled) {
        this.sound(clickSound)
        this.$emit('click')
      }
    }
  }
}
</script>

<style scoped lang="scss">
div.button {
  width: 170px;
  height: 195px;
  margin: 0 0px;
  position: relative;
  cursor: pointer;
  padding: 0 5px;
  &.disabled {
    cursor: default;
  }
  .sprite {
    position: absolute;
    width: 160px;
    height: 160px;
    overflow: hidden;
    border-radius: 50%;
    box-shadow: 0px 5px 24px 5px rgb(0 0 0 / 75%);
  }
  img {
    position: absolute;
    top: 50%;
    left: 50%;
    opacity: 0;
    transition: opacity 0.15s;
    transform: translate(-50%,-50%);
    &.normal {
      opacity: 1;
    }
  }
  &.disabled img.disabled {
    opacity: 1;
  }
  &:not(.disabled) {
    &:hover img.hover {
      opacity: 1;
    }
    &:active img.active {
      opacity: 1;
    }
  }
  .btn-title {
    position: absolute;
    top: calc(100% + 2px);
    left: 50%;
    transform: translate(-50%, -50%) translateZ(0px);
    font-family: consolas;
    font-size: 27px;
    font-weight: bold;
    color: #f5ad63;
    width: 100%;
    text-align: center;
    text-transform: uppercase;
  }
  &.disabled .btn-title {
    color: #adadad;
  }
}
</style>

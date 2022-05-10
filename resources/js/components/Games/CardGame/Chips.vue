<template>
  <div class="chips-container" :class="{ disabled }">
    <vue-tiny-slider ref="slider" class="chips-slider" items="5" :nav="false" :controls="false" :mouse-drag="true" :loop="false">
      <div v-for="chip in chipsAvailable" :key="`chip-${chip}`">
        <chip :value="chip" :disabled="chip > betMaxAvailable || disabled" class="chip" :class="{active: chip === active}" :slide="slideIndex" @click="pos => click(chip, pos)" />
      </div>
    </vue-tiny-slider>
    <div v-if="showArrows && chipsAvailable.length > 5 && slideIndex > 0" class="btn-control prev" @click="prev" />
    <div v-if="showArrows && chipsAvailable.length > 5 && slideIndex < (chipsAvailable.length - 5)" class="btn-control next" @click="next" />
  </div>
</template>

<script>

import VueTinySlider from 'vue-tiny-slider'
import Chip from './Chip'
import ChipValues from './ChipValues'
import { mapState } from 'vuex'
export default {
  components: { Chip, VueTinySlider },
  props: {
    betMin: {
      type: Number,
      required: true
    },
    betMax: {
      type: Number,
      required: true
    },
    bet: {
      type: Number,
      default: 0,
      required: false
    },
    disabled: {
      type: Boolean,
      default: false,
      required: false
    }
  },
  data () {
    return {
      showArrows: false,
      active: 0,
      index: 0,
      slider: null
    }
  },
  computed: {
    slideIndex () {
      return this.index
    },
    chipsAvailable () {
      return ChipValues.filter(v => this.betMin <= v && v <= this.betMax)
    },
    betMaxAvailable () {
      let mb = this.betMax - this.bet
      if (mb + this.bet > this.account.balance) {
        mb -= mb + this.bet - this.account.balance
      }
      return mb
    },
    ...mapState('auth', ['account'])
  },
  mounted () {
    this.$nextTick(() => {
      this.showArrows = true
      this.$refs.slider.slider.events.on('indexChanged', this.changed)
    })
  },
  methods: {
    click (bet, pos) {
      if (bet === this.active) {
        this.$emit('bet', { bet, pos })
      } else {
        this.active = bet
      }
    },
    changed (info) {
      // console.log(info)
      this.index = info.index
    },
    next () {
      let i = this.$refs.slider.slider.getInfo().index
      if (i < (this.chipsAvailable.length - 5)) {
        i++
        this.$refs.slider.slider.goTo(i)
      }
    },
    prev () {
      let i = this.$refs.slider.slider.getInfo().index
      if (i > 0) {
        i--
        this.$refs.slider.slider.goTo(i)
      }
    }
  }
}
</script>

<style scoped lang="scss">
// @import '~/../../node_modules/tiny-slider/dist/tiny-slider.css';
@import 'tiny-slider/src/tiny-slider';
.chips-container {
  width: calc(138px * 5);
  position: relative;
  &::v-deep {
    .tns-outer {
      width: 100%;
    }
    .tns-inner {
      overflow: hidden;
    }
    .tns-visually-hidden {
      display: none;
    }
  }
}
.chip {
  position: relative;
  border: 5px solid transparent;
  border-radius: 50%;
  width: 138px;
  height: 138px;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border .15s;
  &.active:not(.disabled) {
    border-color: #20f722;
    z-index: 2;
  }
}
.btn-control {
  position: absolute;
  cursor: pointer;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 55px;
  height: 55px;
  &:after {
    content: " ";
    display: block;
    border-color: rgba(255,255,255,0.5);
    border-width: 15px;
    width: 55px;
    height: 55px;
    transform: rotate(45deg);
  }
  &.prev {
    left: -32px;
  }
  &.next {
    right: -87px;
  }
  &.prev:after {
    border-style: none none solid solid;
  }
  &.next:after {
    border-style: solid solid none none;
  }
}
</style>

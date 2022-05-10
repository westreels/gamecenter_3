<template>
  <div>
    <div class="bet-container" :style="`left: calc(3184px / 2 + (178px + 8px) * ${position})`" @click="e => $emit('click', e)">
      <div class="circle">
        <span v-if="!noCircle" :class="{ blink }" :style="size !== 1 ? `width: ${178 * size}px; height: ${178 * size}px;` : ''" />
      </div>
      <div class="label">
        {{ label }}
      </div>
      <div v-if="betValue > 0" class="value" :class="{'value-right': valueRight}">
        <div class="text">
          {{ parseFloat(betValue.toFixed(2)) }}
        </div>
      </div>
    </div>
    <div v-for="(chip, i) in betChips" :key="`chip-${i}-${chip.bet}`" class="chip" :class="{shadow: i === 0 && chip.animationDone, action: betChips.length === (i - 1)}" :style="`left: ${chip.pos.x}px; top: ${chip.pos.y}px;`" @click="e => chipClick(i)">
      <img :src="`/images/games/card-game-ui/playing-chips/${chip.bet}.png`">
    </div>
  </div>
</template>

<script>
//  :style="fontSize != 1 ? `font-size: ${fontSize * 37}px; line-height: ${fontSize * 29}px;` : ''"
import ChipValues from './ChipValues'
export default {
  props: {
    label: {
      type: String,
      default: ''
    },
    noValue: {
      type: Boolean,
      default: false
    },
    dealer: {
      type: Boolean,
      default: false
    },
    valueRight: {
      type: Boolean,
      default: false
    },
    size: {
      type: Number,
      required: false,
      default: 1
    },
    fontSize: {
      type: Number,
      required: false,
      default: 1
    },
    position: {
      type: Number,
      required: false,
      default: 0
    },
    chips: {
      type: Array,
      required: false,
      default: () => []
    },
    noCircle: {
      type: Boolean,
      default: false
    },
    blink: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data () {
    return {
      animation: true,
      lastChipsLen: 0,
      betChips: [],
      chipSpeed: 300,
      chipSpeedDealer: 600,
      chipOptimized: true,
      requestAnimationFrame: e => setTimeout(e, 1),
      requestAnimationFrame_get: () => {
        const raf = window.requestAnimationFrame ||
          window.mozRequestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.msRequestAnimationFrame ||
          window.oRequestAnimationFrame
        this.requestAnimationFrame = raf ? raf.bind(window) : null
      }
    }
  },
  computed: {
    betValue () {
      return this.betChips.reduce((acc, chip) => acc + chip.bet, 0)
    }
  },
  watch: {
    position () {
      this.betChips.forEach((chip, i) => {
        if (chip.animationDone) {
          chip.posTo.x = 3184 / 2 + (178 + 8) * this.position + (0.5 - Math.random()) * 10
          chip.posTo.y = 1100.5 - (i + this.betChips.length) * 10 + (0.5 - Math.random()) * 5
          chip.pos.x = chip.posTo.x
          chip.pos.y = chip.posTo.y
        }
      })
    },
    chips (newVal) {
      if (Array.isArray(newVal)) {
        if (this.lastChipsLen < newVal.length) {
          const newChips = newVal.slice(this.lastChipsLen)
          this.lastChipsLen = newVal.length
          // for (let i = newChips.length - 1; i >= 0; i--) {
          newChips.forEach((chip, i) => {
            // chip = newChips[i]
            chip.posFrom = { x: chip.pos.x, y: chip.pos.y }
            chip.posTo = { x: 3184 / 2 + (178 + 8) * this.position + (0.5 - Math.random()) * 10, y: 1100.5 - (i + this.betChips.length) * 10 + (0.5 - Math.random()) * 5 }
            chip.t = Date.now() + 100 * i // (newChips.length - i)
            chip.animationDone = false
            chip.chipSpeed = this.dealer ? this.chipSpeedDealer : this.chipSpeed
          })
          this.betChips.push(...newChips)
          this.chipOptimized = false
        } else if (newVal.length === 0 && this.lastChipsLen > 0) {
          this.lastChipsLen = 0
          this.betChips.forEach((chip, i) => {
            chip.t = Date.now() + 40 * (this.betChips.length - i)
            chip.animationDone = false
            chip.animationRemove = true
            chip.posFrom = { x: chip.pos.x, y: chip.pos.y }
            chip.posTo = {
              x: 3184 / 2 + (178 + 8) * this.position + (0.5 - Math.random()) * 14,
              y: this.dealer ? 160 : 1520
            }
            chip.chipSpeed = this.dealer ? this.chipSpeedDealer : this.chipSpeed
          })
        } else {
          this.lastChipsLen = newVal.length
          const unbet = this.betValue - newVal.reduce((acc, item) => acc + item.bet, 0)
          let bet = this.betValue - unbet
          const newChips = []
          while (bet > 0) {
            const chip = ChipValues.reduce((acc, val) => bet >= val ? val : acc, 1)
            bet -= chip
            newChips.push({
              bet: chip,
              pos:
                {
                  x: newChips.length < this.betChips.length ? this.betChips[newChips.length].pos.x : 3184 / 2 + (178 + 8) * this.position + (0.5 - Math.random()) * 10,
                  y: newChips.length < this.betChips.length ? this.betChips[newChips.length].pos.y : 1100.5 - newChips.length * 10 + (0.5 - Math.random()) * 5
                },
              animationDone: true
            })
          }
          if (unbet > 0) {
            newChips.push({
              bet: unbet,
              posFrom:
                {
                  x: newChips.length < this.betChips.length ? this.betChips[newChips.length].pos.x : 3184 / 2 + (178 + 8) * this.position + (0.5 - Math.random()) * 10,
                  y: newChips.length < this.betChips.length ? this.betChips[newChips.length].pos.y : 1100.5 - newChips.length * 10 + (0.5 - Math.random()) * 5
                },
              pos:
                {
                  x: newChips.length < this.betChips.length ? this.betChips[newChips.length].pos.x : 3184 / 2 + (178 + 8) * this.position + (0.5 - Math.random()) * 10,
                  y: newChips.length < this.betChips.length ? this.betChips[newChips.length].pos.y : 1100.5 - newChips.length * 10 + (0.5 - Math.random()) * 5
                },
              posTo:
                {
                  x: 3184 / 2 + (178 + 8) * this.position + (0.5 - Math.random()) * 14,
                  y: 1520
                },
              animationDone: false,
              animationRemove: true,
              t: Date.now(),
              chipSpeed: this.dealer ? this.chipSpeedDealer : this.chipSpeed
            })
          }
          this.betChips = newChips
        }
      }
    }
  },
  mounted () {
    this.animate()
  },
  beforeDestroy () {
    this.animation = false
  },
  methods: {
    easeInOutCubic (x) {
      return x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2
    },
    animate () {
      if (!this.animation) return
      this.requestAnimationFrame(this.animate)
      const t = Date.now()
      this.betChips.forEach(chip => {
        if (!chip.animationDone && chip.t <= t) {
          const mt = t - chip.t
          if (mt >= chip.chipSpeed) {
            chip.animationDone = true
            chip.pos.x = chip.posTo.x
            chip.pos.y = chip.posTo.y
          } else {
            const k = this.easeInOutCubic(mt / chip.chipSpeed)
            chip.pos.x = chip.posFrom.x + (chip.posTo.x - chip.posFrom.x) * k
            chip.pos.y = chip.posFrom.y + (chip.posTo.y - chip.posFrom.y) * k
          }
        }
      })
      if (!this.chipOptimized && this.betChips.reduce((acc, item) => item.animationDone && acc, true)) {
        let bet = this.betValue
        const newChips = []
        while (bet > 0) {
          const chip = ChipValues.reduce((acc, val) => bet >= val ? val : acc, 1)
          bet -= chip
          newChips.push({
            bet: chip,
            pos:
              {
                x: newChips.length < this.betChips.length ? this.betChips[newChips.length].pos.x : 3184 / 2 + (178 + 8) * this.position + (0.5 - Math.random()) * 10,
                y: newChips.length < this.betChips.length ? this.betChips[newChips.length].pos.y : 1100.5 - newChips.length * 10 + (0.5 - Math.random()) * 5
              },
            animationDone: true
          })
        }
        if (newChips.length !== this.betChips.length) {
          this.betChips = newChips
        }
        this.chipOptimized = true
        this.$emit('animated')
      }
      if (this.betChips.length > 0 && this.betChips.reduce((acc, item) => item.animationDone && acc, true) && this.betChips[this.betChips.length - 1].animationRemove) {
        while (this.betChips.length > 0 && this.betChips[this.betChips.length - 1].animationRemove) this.betChips.pop()
        this.$emit('animated')
      }
    },
    chipClick (i) {
      if (this.betChips.length === i + 1) {
        this.$emit('unbet')
      }
    }
  }
}
</script>

<style scoped lang="scss">
@keyframes circle-blink {
  0% {
    border: 12px solid rgba(100, 100, 100, 0.25);
  }
  50% {
    border: 12px solid rgba(100, 100, 100, 0.95);
  }
  100% {
    border: 12px solid rgba(100, 100, 100, 0.25);
  }
}
.bet-container {
  position: absolute;
  bottom: 140px;
  transform: translate(-50%, -50%);
  mix-blend-mode: color-dodge;
  cursor: pointer;
  .circle {
    width: 178px;
    height: 145px;
    overflow: hidden;
    position: relative;
    span {
      width: 178px;
      height: 178px;
      display: block;
      border: 12px solid rgba(100, 100, 100, 0.5);
      border-radius: 90px;
      opacity: 0.6;
      mix-blend-mode: color-dodge;
      position: absolute;
      bottom: calc(145px - 178px);
      left: 50%;
      transform: translate(-50%,0);
      &.blink {
        animation: circle-blink 1s infinite linear;
      }
    }
  }
  .label {
    text-align: center;
    font-family: 'impact', sans-serif;
    font-size: 37px;
    color: rgba(100,100,100,0.5);
    margin-top: 8px;
    line-height: 29px;
    height: 37px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    text-transform: uppercase;
  }
  .value {
    mix-blend-mode: color-dodge;
    position: absolute;
    top: calc(50% - 47px);
    right: calc(100% + 16px);
    background-color: rgba(100,100,100,0.6);
    font-family: 'impact', sans-serif;
    font-size: 30pt;
    color: black;
    padding: 8px 32px;
    border-radius: 40px;
    min-width: 55px;
    opacity: 0.6;
    &.value-right {
      right: auto;
      left: calc(100% + 16px);
    }
  }
}
.chip {
  position: absolute;
  transform: translate(-50%, -50%);
  border-radius: 50%;
  width: 131px;
  height: 131px;
  transition: 'box-shadow' .15s;
  img {
    width: 131px;
    height: 131px;
  }
  &.shadow {
    box-shadow: 0 6px 15px 0 rgb(0 0 0 / 75%);
  }
  &.action {
    cursor: pointer;
    &:hover {
      img {
        filter: brightness(1.1);
      }
    }
    &:active {
      img {
        filter: brightness(1.4);
      }
    }
  }
}
</style>

<template>
  <div class="hand-container" :style="opacity < 1 ? `opacity: ${opacity};` : ''">
    <transition-group
      tag="div"
      class="hand"
      name="hand-card-animation"
      @before-enter="cardAnimationBeforeEnter"
      @enter="cardAnimationEnter"
      @leave="cardAnimationLeave"
    >
      <template v-for="(card, i) in handCards">
        <div
          :key="i"
          class="playing-card-container"
          :class="`deck-${deck || 'symbols'}${card.faceUp ? ' face-up' : ''}${card.demo ? ' demo' : ''}${card.hintView ? ' hint-view' : ''}`"
          :data-index="i"
          :data-face="card.faceUp ? 1 : 0"
          :data-position-x="card.positionX"
          :data-position-y="card.positionY"
          :style="`left: ${card.positionX}px; top: ${card.positionY}px;`"
        >
          <div class="playing-card">
            <div class="front rounded" :style="{ backgroundImage: card.frontImageUrl }" />
            <div class="back rounded" :style="{ backgroundImage: card.backImageUrl }" />
          </div>
        </div>
      </template>
    </transition-group>
    <template v-if="result.length">
      <div :class="`result${resultBottom ? ' bottom' : ''}`" :style="`left: ${calcResultPosX}px; top: ${calcResultPosY}px;`">
        <div class="bg" :style="`background-image: url(/images/games/card-game-ui/table-${deck}.jpg);right: calc(-1 * (3184px - ${calcResultPosX}px));`" />
        <div class="value">
          {{ result }}
        </div>
      </div>
    </template>
  </div>
</template>

<script>
// right: calc(-1 * (3184px - ${calcResultPosX}px));top: calc(-1 * (1440px / 2 + ${calcResultPosY}px));
// background-position: -${calcResultPosX - 300 - resultWidth / 4 + 100 * positionY}px -${637 + calcResultPosY * 0.9}px;
import gsap from 'gsap'
import { config } from '~/plugins/config'
import SoundMixin from '~/mixins/Sound'

import dealSound from '~/../audio/common/card-deal.wav'
import flipSound from '~/../audio/common/card-flip.wav'

export default {
  mixins: [SoundMixin],
  props: {
    opacity: {
      type: Number,
      default: 1
    },
    cards: {
      type: Array,
      required: true
    },
    result: {
      type: String,
      required: false,
      default: ''
    },
    resultBottom: {
      type: Boolean,
      default: false
    },
    fold: {
      type: Boolean,
      default: false
    },
    demo: {
      type: Array,
      required: false,
      default: () => []
    },
    hintView: {
      type: Array,
      required: false,
      default: () => []
    },
    cardsCount: {
      type: Number,
      required: false,
      default: 1
    },
    positionX: {
      type: Number,
      required: false,
      default: 0
    },
    positionY: {
      type: Number,
      required: false,
      default: 0
    }
  },
  data () {
    return {
      cardsCountLast: 0,
      cardsCountAdded: 0,
      animationsDisabled: {},
      animationsSource: {},
      animations: []
    }
  },
  computed: {
    frontImageUrl () {
      return this.deck
        ? (this.value !== null ? `url('/images/games/playing-cards/${this.deck}/${this.suit.toLowerCase()}${this.value.toLowerCase()}.png')` : '')
        : `url("${config('settings.games.playing_cards.front_image')}")`
    },
    backImageUrl () {
      return this.deck
        ? `url("/images/games/playing-cards/${this.deck}/back.png")`
        : `url("${config('settings.games.playing_cards.back_image')}")`
    },
    deck () {
      return config('settings.games.playing_cards.deck')
    },
    handCards () {
      return this.cards.map((card, i) => {
        const suit = typeof card === 'string' ? card[0] : null
        const value = typeof card === 'string' ? card[1] : null
        return {
          card,
          hintView: this.hintView.indexOf(i) !== -1,
          demo: this.demo.indexOf(card) !== -1,
          suit: suit ? suit.toLowerCase() : null,
          value: value ? value.toLowerCase() : null,
          faceUp: !!card,
          positionX: this.calcCardPosX(i),
          positionY: this.calcCardPosY(i),
          frontImageUrl:
            this.deck
              ? (value && suit ? `url('/images/games/playing-cards/${this.deck}/${suit.toLowerCase()}${value.toLowerCase()}.png')` : '')
              : `url("${config('settings.games.playing_cards.front_image')}")`,
          backImageUrl:
            this.deck
              ? `url("/images/games/playing-cards/${this.deck}/back.png")`
              : `url("${config('settings.games.playing_cards.back_image')}")`
        }
      })
    },
    calcResultPosX () {
      if (this.resultBottom) {
        return 3184 / 2 + this.positionX * ((this.cardsCount - 1) * 16 + 162.8 * this.cardsCount)
      } else {
        return 3184 / 2 - ((this.cardsCount - 1) * 16 + 162.8 * this.cardsCount) / 2 - 32 + this.positionX * ((this.cardsCount - 1) * 16 + 162.8 * this.cardsCount)
      }
    },
    calcResultPosY () {
      if (this.resultBottom) {
        return 231 * this.positionY + 16 * this.positionY + 231 + 64
      } else {
        return 231 * this.positionY + 16 * this.positionY + 231 / 2
      }
    }
  },
  watch: {
    cards (newVal) {
      if (this.cardsCountLast === newVal.length) {
        this.sound(flipSound)
      }
      if (newVal.length > this.cardsCountLast) this.cardsCountAdded += newVal.length - this.cardsCountLast
      this.cardsCountLast = newVal.length
    }
  },
  beforeDestroy () {
    while (this.animations.length) this.animations.pop().kill()
  },
  methods: {
    calcCardPosX (i) {
      if (this.fold) {
        return 3184 / 2 - ((this.cardsCount - 1) * 16 + 162.8 * this.cardsCount) / 2 + 60 * i + this.positionX * ((this.cardsCount - 1) * 16 + 162.8 * this.cardsCount)
      } else {
        return 3184 / 2 - ((this.cardsCount - 1) * 16 + 162.8 * this.cardsCount) / 2 + (162.8 + 16) * i + this.positionX * ((this.cardsCount - 1) * 16 + 162.8 * this.cardsCount)
      }
    },
    calcCardPosY (i) {
      return 231 * this.positionY + 16 * this.positionY
    },
    cardAnimationBeforeEnter (el) {
      const index = parseInt(el.dataset.index)
      if (this.animationsSource[index]) {
        el.style.animationDuration = '0s'
        gsap.set(el, {
          left: this.animationsSource[index].positionX,
          top: this.animationsSource[index].positionY,
          // rotateZ: 0,
          // scaleY: 1,
          // rotateX: 0,
          // rotateY: 180,
          z: 0
        })
        // eslint-disable-next-line eqeqeq
      } else if (el.dataset.face == 1) {
        el.style.animationDuration = '0s'
        gsap.set(el, {
          left: 2144,
          top: -646,
          rotateZ: 40,
          scaleY: 1.1,
          rotateY: 180,
          rotateX: 180,
          z: 0,
          opacity: 0,
          animation: 'none'
        })
      } else {
        gsap.set(el, {
          left: 2144,
          top: -646,
          rotateZ: 40,
          scaleY: 1.1,
          rotateY: 0,
          opacity: 0,
          z: 0
        })
      }
    },
    cardAnimationEnter (el, done) {
      const positionX = parseFloat(el.dataset.positionX)
      const positionY = parseFloat(el.dataset.positionY)
      const index = parseInt(el.dataset.index)
      // const firstDelay = (index - (this.cards.length - this.cardsCountAdded)) * 0.35 + 0.1
      const firstDelay = (this.cardsCountAdded - (this.cards.length - index)) * 0.35 + 0.1
      // console.log(firstDelay, index, this.cardsCountAdded, this.cards.length)
      const delay = 0.4
      // const delay = 1
      if (this.animationsSource[index]) {
        this.animations.push(
          gsap.to(el, {
            duration: delay / 2,
            left: positionX,
            top: positionY,
            // rotateZ: 0,
            // scaleY: 1,
            // rotateX: 180,
            z: 0,
            ease: 'none',
            clearProps: 'transform',
            onComplete: () => {
              el.style.animationDuration = ''
              this.animations.shift()
              this.cardsCountAdded--
              done()
            }
          })
        )
        this.$delete(this.animationsSource, index)
      } else {
        // onStart
        this.animations.push(
          gsap.to(el, {
            duration: 0.1,
            delay: firstDelay - 0.1,
            left: 1945,
            top: -421,
            rotateZ: 40,
            scaleY: 1,
            rotateY: 180,
            rotateX: 0,
            z: 0 + index * 2,
            ease: 'none',
            onStart: () => {
              setTimeout(() => this.sound(dealSound), 250)
              gsap.set(el, {
                clearProps: 'opacity'
              })
            },
            onComplete: () => this.animations.shift()
          })
        )
        // eslint-disable-next-line eqeqeq
        if (el.dataset.face == 1) {
          this.animations.push(
            gsap.to(el, {
              duration: delay / 4,
              delay: firstDelay,
              left: (1945 - positionX) / 2 + positionX,
              top: (-421 - positionY) / 2 + positionY,
              rotateZ: 0,
              scaleY: 1,
              rotateX: 0,
              rotateY: 180,
              z: 150 + index * 2,
              ease: 'none',
              onComplete: () => this.animations.shift()
            })
          )
          this.animations.push(
            gsap.to(el, {
              duration: delay / 4,
              delay: firstDelay + delay / 4,
              left: positionX - (1945 - positionX) / 4,
              top: positionY - (-421 - positionY) / 4,
              rotateZ: 0,
              scaleY: 1,
              rotateX: 0,
              rotateY: 180,
              z: 150 + index * 2,
              ease: 'none',
              onComplete: () => this.animations.shift()
            })
          )
          this.animations.push(
            gsap.to(el, {
              duration: delay / 4,
              delay: firstDelay + delay / 2,
              left: positionX,
              top: positionY,
              rotateZ: 0,
              scaleY: 1,
              rotateX: 0,
              rotateY: 180,
              z: 100 + index * 2,
              ease: 'none',
              clearProps: 'transform',
              onComplete: () => {
                el.style.animationDuration = ''
                this.animations.shift()
                this.cardsCountAdded--
                done()
              }
            })
          )
        } else {
          this.animations.push(
            gsap.to(el, {
              duration: delay / 2,
              delay: firstDelay,
              left: positionX,
              top: positionY,
              rotateZ: 0,
              scaleX: 1,
              rotateY: 0,
              ease: 'none',
              clearProps: 'transform',
              onComplete: () => {
                this.animations.shift()
                this.cardsCountAdded--
                done()
              }
            })
          )
        }
      }
    },
    cardAnimationLeave (el, done) {
      gsap.set(el, {
        transition: 0
      })
      const positionX = parseFloat(el.dataset.positionX)
      const positionY = parseFloat(el.dataset.positionY)
      const index = parseInt(el.dataset.index)
      if (this.animationsDisabled[index]) {
        this.$delete(this.animationsDisabled, index)
        done()
        return
      }
      let delay = index * 100 / 1000 - 0.15 + 0.1 + 0.1 * this.positionY
      // eslint-disable-next-line eqeqeq
      if (el.dataset.face == 1) {
        this.animations.push(
          gsap.to(el, {
            duration: 0.15,
            delay: delay += 0.15,
            left: positionX,
            top: positionY,
            rotateZ: 0,
            scaleY: 1,
            rotateY: 90,
            x: 130,
            z: 200,
            ease: 'none',
            onStart: () => {
              // this.sound(flipSound)
            },
            onComplete: () => this.animations.shift()
          })
        )
        this.animations.push(
          gsap.to(el, {
            duration: 0.15,
            delay: delay += 0.15,
            left: positionX,
            top: positionY,
            rotateZ: 0,
            scaleY: 1,
            rotateY: 0,
            x: 0,
            z: index * 5 * this.positionY,
            ease: 'none',
            onComplete: () => this.animations.shift()
          })
        )
      }
      this.animations.push(
        gsap.to(el, {
          duration: 0.2,
          delay: delay += 0.15,
          left: 1100,
          top: -345.5,
          rotateZ: 0,
          scaleY: 1,
          rotateY: 0,
          x: 0,
          z: index * 5 * this.positionY,
          ease: 'none',
          onStart: () => {
            this.sound(dealSound)
          },
          onComplete: () => this.animations.shift()
        })
      )
      this.animations.push(
        gsap.to(el, {
          duration: 0.2,
          delay: delay += 0.2,
          left: 873,
          top: -689,
          rotateZ: -34,
          scaleY: 1,
          rotateY: 9,
          x: 0,
          z: index * 5 * this.positionY,
          ease: 'none',
          onComplete: () => {
            this.animations.shift()
            done()
          }
        })
      )
    },
    getCardPosition (index) {
      return {
        positionX: this.calcCardPosX(index),
        positionY: this.calcCardPosY(index)
      }
    },
    disableCardAnimation (index) {
      this.$set(this.animationsDisabled, index, 1)
    },
    setAnimationsSource (index, data) {
      this.$set(this.animationsSource, index, data)
    }
  }
}
</script>

<style scoped lang="scss">
@keyframes card-animation {
  0% {
    transition: 0;
    transform-origin: left center;
    transform: translateX(0) translateZ(0px) rotateY(0deg);
  }
  49.99999% {
    transform-origin: left center;
    transform: translateX(10%) translateZ(210px) rotateY(90deg);
  }
  50% {
    transform-origin: right center;
    transform: translateX(-90%) translateZ(40px) rotateY(90deg);
  }
  99% {
    transition: 0;
    transform-origin: right center;
    transform: translateX(-100%) translateZ(0px) rotateY(180deg);
  }
  100% {
    transform-origin: center;
    transform: translateX(0px) translateZ(0px) rotateY(180deg);
  }
}
.hand, .hand-container {
  transform-style: preserve-3d;
  transition: opacity 0.3s;
}
.playing-card-container {
  position: absolute;
  transform-style: preserve-3d;
  transition: .3s;
  box-shadow: 0px 4px 11px -4px rgb(0 0 0 / 50%);
  &.deck-symbols {
    .playing-card {
      .front {
        background-color: var(--v-secondary-lighten1);
      }
      .back {
        background-color: var(--v-primary-darken4);
      }
    }
  }
  &.hint-view {
    transform: translateX(0px) translateY(-50px) translateZ(100px) rotateX(-30deg);
    box-shadow: 0px 38px 27px -10px rgb(0 0 0 / 50%);
  }
  &.face-up {
    animation: card-animation 0.3s linear;
    // transform: translateX(0px) translateZ(0px) rotateY(180deg);
    transform: translateX(0px) translateY(0px) translateZ(0px) rotateX(0deg) rotateY(180deg);
    &:after {
      content: ' ';
      position: absolute;
      left: -8px;
      right: -8px;
      top: -8px;
      bottom: -8px;
      background: rgba(255,255,127, 0);
      border-radius: 14px;
      transition: .3s;
      z-index: -1;
    }
    &.demo {
      transform: translateX(0px) translateY(-50px) translateZ(100px) rotateX(-15deg) rotateY(180deg);
      box-shadow: 0px 38px 27px -10px rgb(0 0 0 / 50%);
      &:after {
        background: rgba(255, 255, 127, 0.75);
      }
    }
  }
  &.hand-card-animation-enter-active, &.hand-card-animation-leave-active {
    // transition: none !important;
    box-shadow: 20px 20px 27px -10px rgb(0 0 0 / 50%);
    &.face-up {
      box-shadow: 30px 30px 30px -15px rgb(0 0 0 / 50%);
    }
    // transition: left .1s, top .1s !important;
    transition: .05s;
  }
  .playing-card {
    position: relative;
    width: 162.8px;
    height: 231px;
    transform-style: preserve-3d;

    .front, .back {
      position: absolute;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      backface-visibility: hidden;
    }
    .front {
      transform: rotateY(180deg);
    }
    &.clickable {
      cursor: pointer;
    }
  }
}
.result {
  position: absolute;
  transform: translate(-100%, -50%);
  font-size: 39px;
  font-family: arial;
  border-radius: 63px;
  overflow:hidden;
  &.bottom {
    transform: translate(-50%, -50%);
  }
  div.bg {
    position: absolute;
    width: 3184px;
    height: 1440px;
    right: calc(-1794.8px);
    top: calc(-712px);
  }
  div.value {
    mix-blend-mode: color-dodge;
    display: block;
    background-color: rgba(100, 100, 100, 0.5);
    opacity: 0.7;
    border-radius: 63px;
    padding: 10px 50px;
    color: black;
    font-weight: bold;
  }
}
</style>

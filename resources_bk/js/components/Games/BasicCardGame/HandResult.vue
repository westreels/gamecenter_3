<template>
  <transition name="balloon">
    <div
      v-if="result"
      class="hand-result elevation-2 ml-n5 ml-lg-0"
      :style="{ width, left }"
    >
      {{ result.toUpperCase() }}
    </div>
  </transition>
</template>

<script>
export default {
  props: {
    result: {
      type: String,
      required: true
    }
  },

  data () {
    return {
      minWidth: 7
    }
  },

  computed: {
    baseWidth () {
      return Math.max(this.minWidth, this.result.length * 0.7)
    },
    width () {
      return this.baseWidth + 'em'
    },
    left () {
      return 'calc(50% - ' + this.baseWidth / 2 + 'em)'
    }
  }
}
</script>

<style lang="scss" scoped>
.hand-result {
  position: absolute;
  height: 2em;
  bottom: calc(50% - 1em);
  text-align: center;
  line-height: 2em;
  opacity: 0.85;
  border-radius: 0.25em;
}

.balloon-enter-active {
  animation: balloon-in ease-in-out 0.6s;
  transform-origin: 50% 100%;
}
.balloon-leave-active {
  transition: all 0.1s;
  transform-origin: 50% 100%;
}
.balloon-leave-to {
  transform: scale(0);
  opacity: 0;
}

@keyframes balloon-in {
  0% {
    transform: scale(0.00) skewX(0deg) rotate(-20deg);
  }
  40% {
    transform: scale(1.00) skewX(-5deg) rotate(10deg);
  }
  50% {
    transform: skewX(5deg) rotate(-5deg);
  }
  60% {
    transform: skewX(-4deg) rotate(3deg);
  }
  70% {
    transform: skewX(4deg) rotate(-1deg);
  }
  80% {
    transform: skewX(-3deg) rotate(0deg);
  }
  85% {
    transform: skewX(3deg);
  }
  90% {
    transform: skewX(-2deg);
  }
  95% {
    transform: skewX(2deg);
  }
  100% {
    transform: skewX(0deg);
  }
}
</style>

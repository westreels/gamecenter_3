<template>
  <transition name="chip">
    <div v-show="bet > 0" class="bet-win-container">
      <div class="bet-win my-2 ml-n5 ml-lg-0 text-center" :class="{ 'display-win': win > 0 }">
        <div class="front">
          <v-responsive
            class="chip-bet text-center rounded-circle d-inline-flex align-center justify-center elevation-2"
            :style="{ fontSize: getChipFontSize(bet) }"
            height="32"
            width="32"
          >
            {{ bet || '' }}
          </v-responsive>
        </div>
        <div class="back elevation-2">
          <v-responsive
            class="chip-win text-center rounded-circle d-inline-flex align-center justify-center elevation-2"
            :style="{ fontSize: getChipFontSize(win) }"
            height="32"
            width="32"
          >
            {{ win || '' }}
          </v-responsive>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  props: {
    bet: {
      type: Number,
      required: true
    },
    win: {
      type: Number,
      required: true
    }
  },

  methods: {
    getChipFontSize (value) {
      const length = ('' + value).length
      return length < 3 ? '1em' : (length === 3 ? '0.9em' : '0.6em')
    }
  }
}
</script>

<style lang="scss" scoped>
.chip-bet {
  color: var(--v-primary-base);
  background: var(--v-primary-lighten4);
  border: 2px solid var(--v-primary-base);
}

.chip-win {
  background: var(--v-primary-base);
  border: 2px solid var(--v-primary-lighten3);
}

.chip-enter, .chip-leave-to {
  transform: translateY(100vh);
  opacity: 1;
}

.chip-enter-active, .chip-leave-active {
  transition: all 0.4s;
}

.bet-win-container {
  perspective: 300px;

  .bet-win {
    position: relative;
    transform-style: preserve-3d;
    transition: all 0.4s ease-out;

    .front, .back {
      position: absolute;
      width: 100%;
      height: 100%;
      backface-visibility: hidden;
      transition: all 2s;
    }

    .back {
      transform: rotateY(180deg);
    }

    &.display-win {
     transform: rotateY(180deg);
    }
  }
}
</style>

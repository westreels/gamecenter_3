<template>
  <div class="playing-card-container" :class="`deck-${deck || 'symbols'}`">
    <slot name="top"></slot>
    <div class="playing-card ml-n10 mx-lg-1" :class="{ 'face-down': isFaceDown, clickable, inactive }">
      <div class="front rounded elevation-2" :style="{ backgroundImage: frontImageUrl }">
        <div v-if="!deck" class="d-flex flex-column pa-2">
          <card-value :value="value" :suit="suit" class="mb-1" />
          <card-suit :suit="suit" />
        </div>
      </div>
      <div class="back rounded elevation-2" :style="{ backgroundImage: backImageUrl }"></div>
    </div>
  </div>
</template>

<script>
import { config } from '~/plugins/config'
import CardValue from './PlayingCardValue'
import CardSuit from './PlayingCardSuit'

export default {
  components: {
    CardValue,
    CardSuit
  },

  props: {
    card: {
      required: true,
      validator: value => (typeof value === 'string' && value.length === 2) || value === null
    },
    clickable: {
      type: Boolean,
      required: false,
      default: false
    },
    inactive: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      suite: null,
      value: null,
      isFaceDown: false
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
    }
  },

  watch: {
    card (card) {
      if (card === null) {
        this.isFaceDown = true

        // delay setting the card value to null to complete flip animation
        setTimeout(() => {
          this.suit = null
          this.value = null
        }, 500)
      } else {
        this.suit = card[0]
        this.value = card[1]
        this.isFaceDown = false
      }
    }
  },

  created () {
    if (this.card !== null) {
      this.suit = this.card[0]
      this.value = this.card[1]
    } else {
      this.isFaceDown = true
    }
  }
}
</script>

<style lang="scss" scoped>
@import '~/../sass/_variables.scss';

.playing-card-container {
  perspective: 600px;

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

  .playing-card {
    position: relative;
    width: $playing-card-width;
    height: $playing-card-height;
    transform-style: preserve-3d;
    transition: all 0.5s ease-out;

    &.inactive {
      opacity: 0.4;
    }

    .front, .back {
      position: absolute;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      backface-visibility: hidden;
    }

    .back {
      transform: rotateY(180deg);
    }

    &.face-down {
      transform: rotateY(180deg);
    }

    &.clickable {
      cursor: pointer;
    }
  }
}

@media screen and (max-width: $playing-card-sm-breakpoint) {
  .playing-card-container {
    .playing-card {
      width: $playing-card-width-sm;
      height: $playing-card-height-sm;

      &.inactive {
        opacity: 1;
      }
    }
  }
}
</style>

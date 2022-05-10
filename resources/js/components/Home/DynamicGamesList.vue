<template>
  <div v-if="games.length" :class="classes">
    <v-row>
      <v-col>
        <h3 class="display-1 font-weight-thin text-center">
          {{ title }}
        </h3>
      </v-col>
    </v-row>

    <v-row v-if="filter && categories.length > 1">
      <v-col>
        <v-chip-group
          v-model="category"
          active-class="primary"
          mandatory
        >
          <v-chip label active @click="filterByCategory('')">
            {{ $t('All') }}
          </v-chip>
          <v-chip v-for="cat in categories" :key="cat" label @click="filterByCategory(cat)">
            {{ cat }}
          </v-chip>
        </v-chip-group>
      </v-col>
    </v-row>

    <v-row v-if="games.length" ref="games" justify="center">
      <v-col
        v-for="(game, i) in (count > 0 ? games.slice(0, count) : games)"
        :key="i"
        cols="12"
        sm="6"
        lg="3"
        xl="2"
        class="game-card"
        :data-groups="JSON.stringify([game.category])"
      >
        <component :is="`game-${displayStyle}`" :game="game" class="mx-auto" />
      </v-col>
    </v-row>
    <v-row v-else>
      <v-col v-for="(v,i) in Array(count).fill(0)" :key="i">
        <v-skeleton-loader type="card" />
      </v-col>
    </v-row>
    <v-row v-if="count > 0 && count < games.length" class="my-10">
      <v-col class="text-center">
        <v-btn color="primary" large @click="loadMore">
          <v-icon left>
            mdi-autorenew
          </v-icon>
          {{ $t('Load more') }}
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import GameCard from '~/components/Home/GameCard'
import GameBlock from '~/components/Home/GameBlock'
import GameBlock2 from '~/components/Home/GameBlock2'
import { unique } from '~/plugins/utils'
import Shuffle from 'shufflejs'

export default {
  components: { GameBlock, GameCard, GameBlock2 },

  props: {
    title: {
      type: String,
      required: true
    },
    games: {
      type: Array,
      required: true
    },
    displayStyle: {
      type: String,
      required: true
    },
    displayCount: {
      type: Number,
      required: false,
      default: 0
    },
    loadCount: {
      type: Number,
      required: false,
      default: 0
    },
    filter: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      count: this.displayCount,
      category: null,
      shuffle: null
    }
  },

  computed: {
    classes () {
      return ['game-list', 'game-list-' + this.title.replace(/[^a-z]+/ig, '-').toLowerCase()]
    },
    categories () {
      return unique(this.games.map(game => game.category)).sort()
    }
  },

  methods: {
    loadMore () {
      this.count += this.loadCount
    },
    filterByCategory (category) {
      if (!this.shuffle) {
        this.shuffle = new Shuffle(this.$refs.games, { itemSelector: '.game-card' })
      }

      this.shuffle.filter(category)
    }
  }
}
</script>
<style lang="scss" scoped>
.v-chip-group::v-deep {
  .v-slide-group__content {
    justify-content: center;
  }
}
</style>

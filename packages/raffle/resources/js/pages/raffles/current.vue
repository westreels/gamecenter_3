<template>
  <v-container>
    <template v-if="raffles">
      <v-row v-if="raffles.length" justify="center">
        <v-col
          v-for="(raffle, i) in raffles.slice(0, displayCount)"
          :key="raffle.id"
          cols="12"
          md="6"
        >
          <raffle-card :raffle="raffle" @update="update($event.raffle, i)"/>
        </v-col>
      </v-row>
      <h5 v-else class="text-h5 mt-5">
        {{ $t('No raffles are running at the moment.') }}
      </h5>
      <v-row v-if="displayCount > 0 && displayCount < raffles.length" class="my-10">
        <v-col class="text-center">
          <v-btn color="primary" large @click="loadMore">
            <v-icon left>
              mdi-autorenew
            </v-icon>
            {{ $t('Load more') }}
          </v-btn>
        </v-col>
      </v-row>
    </template>
    <v-row v-else>
      <v-col v-for="i in [0,1]" :key="i" cols="12" md="6">
        <v-skeleton-loader type="card"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import RaffleCard from 'packages/raffle/resources/js/components/RaffleCard'
import { config } from '~/plugins/config'

export default {
  components: { RaffleCard },

  metaInfo () {
    return { title: this.$t('Current raffles') }
  },

  data () {
    return {
      raffles: null,
      displayCount: config('raffle.display_count'),
      loadCount: config('raffle.load_count')
    }
  },

  async created () {
    this.$store.dispatch('auth/fetchUser')

    const { data } = await axios.get('/api/raffles')

    this.raffles = data
  },

  methods: {
    loadMore () {
      this.displayCount += this.loadCount
    },
    update (raffle, i) {
      this.raffles.splice(i, 1, raffle)
    }
  }
}
</script>

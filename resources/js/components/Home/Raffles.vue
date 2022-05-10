<template>
  <v-container v-if="rafflePackageEnabled" class="mt-10">
    <v-row>
      <v-col class="text-center">
        <h3 class="display-1 font-weight-thin">
          {{ $t('Buy tickets and win raffles') }}
        </h3>
      </v-col>
    </v-row>
    <v-row class="justify-center" justify="center">
      <template v-if="raffles">
        <template v-if="raffles.length">
          <v-col
            v-for="raffle in raffles"
            :key="raffle.id"
            cols="12"
            md="4"
            lg="3"
            class="text-center"
          >
            <v-card outlined raised color="primary">
              <v-img
                :src="raffle.banner || ''"
                class="white--text align-center"
                gradient="to bottom, rgba(0,0,0,0.8), rgba(0,0,0,0.8)"
                height="200px"
              >
                <v-card-title class="d-block text-center">
                  {{ raffle.title }}
                </v-card-title>
              </v-img>
              <v-card-text>
                <v-btn :to="{ name: 'raffles' }" outlined depressed>
                  {{ $t('Win up to {0}', [integer(raffle.max_pot_size)]) }}
                </v-btn>
              </v-card-text>
            </v-card>
          </v-col>
        </template>
        <h5 v-else class="text-h5 mt-5">
          {{ $t('No raffles are running at the moment.') }}
        </h5>
      </template>
      <template v-else>
        <v-col v-for="i in [0,1,2]" :key="i" cols="12" md="4" lg="3">
          <v-skeleton-loader type="card" />
        </v-col>
      </template>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import { integer } from '~/plugins/format'

export default {
  data () {
    return {
      raffles: null
    }
  },

  computed: {
    rafflePackageEnabled () {
      return this.$store.getters['package-manager/getById']('raffle')
    }
  },

  created () {
    if (this.rafflePackageEnabled) {
      this.pullRaffles()
    }
  },

  methods: {
    integer,
    async pullRaffles () {
      const { data } = await axios.get('/api/pub/raffles')

      this.raffles = data
    }
  }
}
</script>

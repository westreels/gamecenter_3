<template>
  <v-card>
    <v-toolbar>
      <v-toolbar-title>
        {{ $t('Game information') }}
      </v-toolbar-title>
      <v-spacer />
      <v-btn icon @click="$emit('close')">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <template v-slot:extension>
        <v-tabs v-model="infoTab" centered hide-slider>
          <v-tab href="#tab-about">
            {{ $t('How to play') }}
          </v-tab>
          <v-tab href="#tab-paytable">
            {{ $t('Paytable') }}
          </v-tab>
          <v-tab href="#tab-reels">
            {{ $t('Reels') }}
          </v-tab>
        </v-tabs>
      </template>
    </v-toolbar>
    <v-tabs-items v-model="infoTab">
      <v-tab-item value="tab-about">
        <v-card flat>
          <v-card-text class="about-text">
            <p>
              {{ $t('The objective of the game is to get the symbols on the reels to fall in a perfect line behind one of the paylines.') }}
              {{ $t('You can choose how many paylines to play in each round - from 1 to 20.') }}
              {{ $t('Please note that your bet is multiplied by the number of paylines you play.') }}
            </p>
            <p>
              {{ $t('Apart from regular symbols there can be magic symbols - wilds and scatters.') }}
              {{ $t('Please check the Paytable tab to see if such symbols are enabled in this slot machine.') }}
            </p>
            <p>
              {{ $t('The wild symbol has the power to substitute any symbol on the reel that it appears in to create a payline.') }}
              {{ $t('The scatter symbols will pay out no matter where on the reels they appear, they do not need to land on a payline to win.') }}
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-paytable">
        <v-card flat>
          <v-card-text v-if="variation">
            <v-row v-for="(symbol, symbolIndex) in variation.symbols" :key="symbolIndex">
              <v-col class="text-center" align-self="center">
                <v-avatar size="64" tile>
                  <img :src="symbolImagePath(symbolIndex)" :alt="symbolImagePath(symbolIndex)">
                </v-avatar>
                <div v-if="symbol.wild" class="mt-2">
                  <v-chip color="primary" outlined>
                    {{ $t('Wild') }}
                  </v-chip>
                </div>
                <div v-if="symbol.scatter" class="mt-2">
                  <v-chip color="primary" outlined>
                    {{ $t('Scatter') }}
                  </v-chip>
                </div>
              </v-col>
              <v-col align-self="center">
                <template v-for="(payout, payoutIndex) in symbol.payouts">
                  <div v-if="payout > 0" :key="payoutIndex">
                    x{{ payoutIndex + 1 }} {{ $t('pays') }} {{ $t('bet') }} {{ symbol.scatter ? ' x ' + $t('lines') : '' }} x {{ payout }}
                  </div>
                </template>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-reels">
        <v-card flat>
          <v-card-text v-if="variation">
            <v-row>
              <v-col v-for="reelIndex in [0,1,2,3,4]" :key="reelIndex" class="reel overflow-y-auto text-center">
                <v-avatar v-for="(symbolIndex, index) in variation.reels[reelIndex]" :key="index" size="64" tile class="mb-3">
                  <img :src="symbolImagePath(symbolIndex)" :alt="symbolImagePath(symbolIndex)">
                </v-avatar>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'

export default {
  data () {
    return {
      infoTab: 'tab-about'
    }
  },

  computed: {
    variations () {
      return config('slots.variations')
    },
    variation () {
      return this.variations[this.variationIndex]
    },
    variationIndex () {
      return this.variations.findIndex(o => o.slug === this.$route.params.slug)
    }
  },

  methods: {
    symbolImagePath (symbolIndex) {
      return this.variation.symbols[symbolIndex].image
    }
  }
}
</script>
<style lang="scss" scoped>
  .reel {
    max-height: 350px;
  }
</style>

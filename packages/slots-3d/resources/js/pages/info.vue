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
              {{ $t('The objective of the game is to get the symbols on the reels to fall in a perfect line.') }}
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-paytable">
        <v-card flat>
          <v-card-text class="text-center">
            <v-row v-for="(payline, paylineIndex) in paytable" :key="paylineIndex">
              <v-col>
                <v-avatar v-for="reelIndex in [0,1,2]" :key="reelIndex" size="48" tile>
                  <img v-if="payline.positions[reelIndex] !== null" :src="symbols[payline.positions[reelIndex]]">
                  <span v-else class="font-weight-bold">{{ $t('Any') }}</span>
                </v-avatar>
                <span class="ml-5">{{ $t('pays x{0} credits', [payline.payout]) }}</span>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-reels">
        <v-card flat>
          <v-card-text>
            <v-row>
              <v-col v-for="reelIndex in [0,1,2]" :key="reelIndex" class="reel overflow-y-auto text-center">
                <v-avatar v-for="(symbolIndex, index) in reels[reelIndex]" :key="index" size="64" tile class="mb-3 d-block ml-auto mr-auto">
                  <img :src="symbols[symbolIndex]" :alt="symbols[symbolIndex]">
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
    symbols () {
      return config('slots-3d.symbols')
    },
    reels () {
      return config('slots-3d.reels')
    },
    paytable () {
      return config('slots-3d.paytable')
    }
  }
}
</script>

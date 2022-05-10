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
        </v-tabs>
      </template>
    </v-toolbar>
    <v-tabs-items v-model="infoTab">
      <v-tab-item value="tab-about">
        <v-card flat>
          <v-card-text class="about-text">
            <p>
              {{ $t('Heads Or Tails is a simple game that is based on chance.') }}
              {{ $t('This game involves predicting the result of a flip of a coin.') }}
              {{ $t('This means that you have a fifty – fifty chance to win.') }}
            </p>
            <p>
              {{ $t('If you win your payout will be calculated as bet x {0}, otherwise you will lose your bet.', [this.payout]) }}
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'
import { round } from '~/plugins/utils'

export default {
  data () {
    return {
      infoTab: 'tab-about'
    }
  },

  computed: {
    payout () {
      return round(2 - config('heads-or-tails.house_edge') / 100, 2)
    }
  }
}
</script>

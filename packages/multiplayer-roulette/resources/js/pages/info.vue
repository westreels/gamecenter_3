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
              {{ $t('Multiplayer roulette is similar to the regular roulette, except that the betting rounds last for a specific amount of time ({0} seconds) and any player can place a bet during that time.', [duration]) }}
              {{ $t('Bets are accepted for red (pays bet x {0}), black (pays bet x {1}) and zero (pays bet x {2}).', [paytable.red, paytable.black, paytable.zero]) }}
              {{ $t('After completion of the betting round, the roulette spins and the winners are determined.') }}
            </p>
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
      infoTab: 'tab-about',
      duration: parseInt(config('multiplayer-roulette.duration'), 10),
      paytable: config('multiplayer-roulette.paytable')
    }
  }
}
</script>

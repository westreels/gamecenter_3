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
              {{ $t('Wait until the betting round starts, choose how much you want to wager and click "Bet" button.') }}
              {{ $t('Note that the betting round lasts for {0} seconds, so you need to place your bet before the time runs out.', [duration]) }}
              {{ $t('After all bets are accepterd the game begins.') }}
              {{ $t('Watch the current payout and cash out before the rocket explodes!') }}
              {{ $t('You will get your bet back multiplied by the payout at the time of cash out.') }}
              {{ $t('The game can end at any time, so if you are late to cash out you will get nothing.') }}
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
      duration: config('crash.duration')
    }
  }
}
</script>

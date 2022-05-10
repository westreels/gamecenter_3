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
        </v-tabs>
      </template>
    </v-toolbar>
    <v-tabs-items v-model="infoTab">
      <v-tab-item value="tab-about">
        <v-card flat>
          <v-card-text class="about-text">
            <p>
              {{ $t('The objective of the game is to collect one of the possible winning combinations based on the 5-card draw.') }}
            </p>
            <p>
              {{ $t('In the first round you receive 5 random cards.') }}
              {{ $t('You can then exchange any one of the received cards (or all of them) for another in order to collect a winning combination.') }}
              {{ $t('To keep a specific card click on it and it will be held during the second draw.') }}
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-paytable">
        <v-card flat>
          <v-card-text>
            <v-simple-table>
              <thead>
                <tr>
                  <th>{{ $t('Result') }}</th>
                  <th class="text-right">
                    {{ $t('Payout') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(payout, i) in paytable" :key="i">
                  <td>{{ combinations[i] }}</td>
                  <td class="text-right">
                    {{ $t('bet') }} x {{ payout }}
                  </td>
                </tr>
              </tbody>
            </v-simple-table>
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
      paytable: config('video-poker.paytable').slice(1),
      combinations: [
        this.$t('Pair (jacks or better)'),
        this.$t('Two pair'),
        this.$t('Three of a kind'),
        this.$t('Straight'),
        this.$t('Flush'),
        this.$t('Full house'),
        this.$t('Four of a kind'),
        this.$t('Straight flush'),
        this.$t('Royal flush')
      ]
    }
  }
}
</script>

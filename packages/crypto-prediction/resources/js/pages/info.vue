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
          <v-tab href="#tab-assets">
            {{ $t('Assets') }}
          </v-tab>
        </v-tabs>
      </template>
    </v-toolbar>
    <v-tabs-items v-model="infoTab">
      <v-tab-item value="tab-about">
        <v-card flat>
          <v-card-text class="about-text">
            <p>
              {{ $t('The objective of this game is to correctly predict whether the price of a certain coin will go up or down.') }}
              {{ $t('To play the game select a coin, duration (period) and bet amount.') }}
              {{ $t('If you think the price will rise from the current price level, click "Higher".') }}
              {{ $t('Otherwise, if you think the price will drop from the current price level, click "Below".') }}
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-paytable">
        <v-simple-table>
          <thead>
            <tr>
              <th>{{ $t('Bet type') }}</th>
              <th class="text-right">
                {{ $t('Payout') }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ $t('Higher') }}</td>
              <td class="text-right">
                {{ higherPayout }}
              </td>
            </tr>
            <tr>
              <td>{{ $t('Lower') }}</td>
              <td class="text-right">
                {{ lowerPayout }}
              </td>
            </tr>
          </tbody>
        </v-simple-table>
      </v-tab-item>
      <v-tab-item value="tab-assets">
        <v-simple-table>
          <thead>
            <tr>
              <th>#</th>
              <th>{{ $t('Symbol') }}</th>
              <th>{{ $t('Name') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(asset, i) in assets" :key="asset.id">
              <td>{{ ++i }}</td>
              <td>{{ asset.symbol }}</td>
              <td>{{ asset.name }}</td>
            </tr>
          </tbody>
        </v-simple-table>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>

<script>
import axios from 'axios'
import { config } from '~/plugins/config'
import { route } from '~/plugins/route'

export default {
  data () {
    return {
      infoTab: 'tab-about',
      assets: []
    }
  },

  computed: {
    higherPayout () {
      return config('crypto-prediction.paytable')[1]
    },
    lowerPayout () {
      return config('crypto-prediction.paytable')[-1]
    }
  },

  async created () {
    const { data: assets } = await axios.post(route('assets.search'), { type: 'crypto', search: '', exact: false })
    this.assets = assets
  }
}
</script>

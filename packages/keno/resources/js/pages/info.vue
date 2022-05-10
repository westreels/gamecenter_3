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
              {{ $t('Select {0} numbers from 1 to {1} by clicking on a specfic number.', [selectCount, max]) }}
              {{ $t('If it is too boring click "Random" button to generate numbers for you.') }}
              {{ $t('Choose your bet and click "Play".') }}
            </p>
            <p>
              {{ $t('{0} unique random numbers will be generated.', [drawCount]) }}
              {{ $t('The goal is to match as many numbers you chose on the board with the generated numbers as possible.') }}
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
                  <th>{{ $t('Number of matches') }}</th>
                  <th class="text-right">
                    {{ $t('Payout') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <template v-for="(payout, i) in paytable">
                  <tr v-if="payout > 0" :key="i">
                    <td>{{ i + 1 }}</td>
                    <td class="text-right">
                      {{ $t('bet') }} x {{ payout }}
                    </td>
                  </tr>
                </template>
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
      infoTab: 'tab-about'
    }
  },

  computed: {
    paytable () {
      return config('keno.paytable')
    },
    selectCount () {
      return parseInt(config('keno.select_count'), 10)
    },
    drawCount () {
      return parseInt(config('keno.draw_count'), 10)
    },
    max () {
      return parseInt(config('keno.rows_count'), 10) * parseInt(config('keno.cols_count'), 10)
    }
  }
}
</script>

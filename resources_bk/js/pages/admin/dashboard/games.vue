<template>
  <v-container>
    <v-row>
      <v-col cols="12" lg="6" class="text-center">
        <v-card :loading="!bets">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Bets') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['comparison-period']"
              :disabled="!houseEdge"
              @apply="loadData('games-bets-comparison', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="previousBets > 0 || currentBets > 0 ? 100 * previousBets / (previousBets + currentBets) : 0"
                  color="primary"
                >
                  <span class="headline">{{ previousBets }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('previous') }}
                </v-subheader>
              </v-col>
              <v-col cols="12" md="6" class="text-center">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="previousBets > 0 || currentBets > 0 ? 100 * currentBets / (previousBets + currentBets) : 0"
                  color="primary"
                >
                  <span class="headline">{{ currentBets }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('current') }}
                </v-subheader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" lg="6" class="text-center">
        <v-card :loading="!houseEdge">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('House edge') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['comparison-period']"
              :disabled="!houseEdge"
              @apply="loadData('games-house-edge-comparison', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="previousHouseEdge > 0 ? -90 : -90 - (360 * Math.abs(previousHouseEdge / 100))"
                  :size="200"
                  :width="30"
                  :value="Math.abs(previousHouseEdge)"
                  :color="previousHouseEdge > 0 ? 'primary' : 'error'"
                >
                  <span class="headline">{{ percentage(previousHouseEdge) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('previous') }}
                </v-subheader>
              </v-col>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="currentHouseEdge > 0 ? -90 : -90 - (360 * Math.abs(currentHouseEdge / 100))"
                  :size="200"
                  :width="30"
                  :value="Math.abs(currentHouseEdge)"
                  :color="currentHouseEdge > 0 ? 'primary' : 'error'"
                >
                  <span class="headline">{{ percentage(currentHouseEdge) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('current') }}
                </v-subheader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" lg="12">
        <v-card class="text-center" :loading="!data['games-wagered-by-week']">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Wagered last 8 weeks') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-sheet>
              <v-sparkline
                :value="data['games-wagered-by-week'] || Array(8).fill(0)"
                :color="chartLineColor"
                height="100"
                padding="24"
                stroke-linecap="round"
                line-width="2"
                smooth="5"
                auto-draw
                :auto-draw-duration="2000"
              >
                <template v-slot:label="item">
                  {{ short(item.value) }}
                </template>
              </v-sparkline>
            </v-sheet>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" lg="12">
        <v-card class="text-center" :loading="!stats">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Totals by game') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['period', 'user-role']"
              :disabled="!stats"
              @apply="loadData('games-stats', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">{{ $t('Game') }}</th>
                    <th class="text-right">{{ $t('Bets') }}</th>
                    <th class="text-right">{{ $t('Wagered') }}</th>
                    <th class="text-right">{{ $t('Won') }}</th>
                    <th class="text-right">{{ $t('Return to player') }}</th>
                    <th class="text-right">{{ $t('House profit') }}</th>
                    <th class="text-right">{{ $t('House edge') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="stats">
                    <template v-if="stats.length">
                      <tr v-for="item in stats" :key="item.title">
                        <td class="text-left">{{ item.title }}</td>
                        <td class="text-right">{{ integer(item.bet_count) }}</td>
                        <td class="text-right">{{ integer(item.bet_total) }}</td>
                        <td class="text-right">{{ integer(item.win_total) }}</td>
                        <td class="text-right">{{ percentage(100 - item.house_edge) }}</td>
                        <td class="text-right">{{ integer(item.bet_total-item.win_total) }}</td>
                        <td class="text-right" :class="{ 'error--text': item.house_edge < 0 }">{{ percentage(item.house_edge) }}</td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="6">
                        {{ $t('No data found') }}
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(10).fill(0)" :key="i">
                      <td colspan="6">
                        <v-skeleton-loader type="text" />
                      </td>
                    </tr>
                  </template>
                </tbody>
              </template>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { integer, percentage, short } from '~/plugins/format'
import FilterMenu from '~/components/Filters/FilterMenu'
import DashboardMixin from '~/mixins/Admin/Dashboard'

export default {
  components: { FilterMenu },

  mixins: [DashboardMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Dashboard') + ' ' + this.$t('Games') }
  },

  data () {
    return {
      queries: [
        'games-bets-comparison',
        'games-house-edge-comparison',
        'games-wagered-by-week',
        'games-stats'
      ]
    }
  },

  computed: {
    bets () {
      return this.data['games-bets-comparison'] || null
    },
    previousBets () {
      return this.bets !== null ? this.bets[0] : 0
    },
    currentBets () {
      return this.bets !== null ? this.bets[1] : 0
    },
    houseEdge () {
      return this.data['games-house-edge-comparison'] || null
    },
    previousHouseEdge () {
      return this.houseEdge !== null ? this.houseEdge[0] : 0
    },
    currentHouseEdge () {
      return this.houseEdge !== null ? this.houseEdge[1] : 0
    },
    stats () {
      return this.data['games-stats'] || null
    }
  },

  methods: {
    integer,
    percentage,
    short
  }
}
</script>

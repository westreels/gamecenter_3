<template>
  <v-container>
    <v-row>
      <v-col cols="12" lg="6" class="text-center">
        <v-card :loading="!ggr">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('GGR') }}
              <tooltip>{{ $t('Gross Gaming Revenue is the difference between the amount wagered and the amount won') }}</tooltip>
            </v-toolbar-title>
            <v-spacer />
            <comparison-period-filter
              :disabled="!ggr"
              :hide-details="true"
              :solo="true"
              class="flex-grow-0"
              @change="loadData('games-ggr-comparison', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="totalAbsGgr ? 100 * Math.abs(previousGgr) / totalAbsGgr : 0"
                  :color="previousGgr > 0 ? 'primary' : (previousGgr < 0 ? 'error' : '')"
                >
                  <span class="headline">{{ short(previousGgr) }}</span>
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
                  :value="totalAbsGgr ? 100 * Math.abs(currentGgr) / totalAbsGgr : 0"
                  :color="currentGgr > 0 ? 'primary' : (currentGgr < 0 ? 'error' : '')"
                >
                  <span class="headline">{{ short(currentGgr) }}</span>
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
        <v-card :loading="!ggrMargin">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('GGR margin') }}
              <tooltip>{{ $t('Gross Gaming Revenue as a percentage of the amount wagered') }}</tooltip>
            </v-toolbar-title>
            <v-spacer />
            <comparison-period-filter
              :disabled="!ggrMargin"
              :hide-details="true"
              :solo="true"
              class="flex-grow-0"
              @change="loadData('games-ggr-margin-comparison', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="Math.abs(previousGgrMargin)"
                  :color="previousGgrMargin > 0 ? 'primary' : (previousGgrMargin < 0 ? 'error' : '')"
                >
                  <span class="headline">{{ percentage(previousGgrMargin) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('previous') }}
                </v-subheader>
              </v-col>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="Math.abs(currentGgrMargin)"
                  :color="currentGgrMargin > 0 ? 'primary' : (currentGgrMargin < 0 ? 'error' : '')"
                >
                  <span class="headline">{{ percentage(currentGgrMargin) }}</span>
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
                :key="!!data['games-wagered-by-week']"
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
        <v-card class="text-center" :loading="!data['games-ggr-by-week']">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('GGR last 8 weeks') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-sheet>
              <v-sparkline
                :key="!!data['games-ggr-by-week']"
                :value="data['games-ggr-by-week'] || Array(8).fill(0)"
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
              {{ $t('Detailed breakdown') }}
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
                    <th class="text-left">
                      {{ $t('Game') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Bets') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Wagered') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Won') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Return to player') }}
                    </th>
                    <th class="text-right">
                      {{ $t('GGR') }}
                      <tooltip>{{ $t('Gross Gaming Revenue is the difference between the amount wagered and the amount won') }}</tooltip>
                    </th>
                    <th class="text-right">
                      {{ $t('GGR margin') }}
                      <tooltip>{{ $t('Gross Gaming Revenue as a percentage of the amount wagered') }}</tooltip>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="stats">
                    <template v-if="stats.length">
                      <tr v-for="item in stats" :key="item.title">
                        <td class="text-left">
                          {{ item.title }}
                        </td>
                        <td class="text-right">
                          {{ integer(item.bet_count) }}
                        </td>
                        <td class="text-right">
                          {{ integer(item.bet_total) }}
                        </td>
                        <td class="text-right">
                          {{ integer(item.win_total) }}
                        </td>
                        <td class="text-right" :class="{ 'error--text': item.ggr_margin < 0 }">
                          {{ percentage(100 - item.ggr_margin) }}
                        </td>
                        <td class="text-right" :class="{ 'error--text': item.ggr_margin < 0 }">
                          {{ integer(item.bet_total - item.win_total) }}
                        </td>
                        <td class="text-right" :class="{ 'error--text': item.ggr_margin < 0 }">
                          {{ percentage(item.ggr_margin) }}
                        </td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="7">
                        {{ $t('No data found') }}
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(10).fill(0)" :key="i">
                      <td colspan="7">
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
import Tooltip from '~/components/Tooltip'
import ComparisonPeriodFilter from '~/components/Filters/ComparisonPeriodFilter'

export default {
  components: { ComparisonPeriodFilter, Tooltip, FilterMenu },

  mixins: [DashboardMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Dashboard') + ' ' + this.$t('Games') }
  },

  data () {
    return {
      queries: [
        'games-ggr-comparison',
        'games-ggr-margin-comparison',
        'games-wagered-by-week',
        'games-ggr-by-week',
        'games-stats'
      ]
    }
  },

  computed: {
    ggr () {
      return this.data['games-ggr-comparison'] || null
    },
    previousGgr () {
      return this.ggr !== null ? this.ggr[0] : 0
    },
    currentGgr () {
      return this.ggr !== null ? this.ggr[1] : 0
    },
    totalAbsGgr () {
      return Math.abs(this.previousGgr) + Math.abs(this.currentGgr)
    },
    ggrMargin () {
      return this.data['games-ggr-margin-comparison'] || null
    },
    previousGgrMargin () {
      return this.ggrMargin !== null ? this.ggrMargin[0] : 0
    },
    currentGgrMargin () {
      return this.ggrMargin !== null ? this.ggrMargin[1] : 0
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

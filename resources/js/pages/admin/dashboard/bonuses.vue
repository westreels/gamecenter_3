<template>
  <v-container>
    <v-row>
      <v-col cols="12" lg="12">
        <v-card class="text-center" :loading="!data['bonuses-by-week']">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Bonuses paid last 8 weeks') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-sheet>
              <v-sparkline
                :key="!!data['bonuses-by-week']"
                :value="data['bonuses-by-week'] || Array(8).fill(0)"
                :color="chartLineColor"
                height="100"
                padding="24"
                stroke-linecap="round"
                line-width="2"
                smooth="5"
                auto-draw
                :auto-draw-duration="2000"
              >
                <template #label="item">
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
              @apply="loadData('bonuses-stats', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-simple-table>
              <template #default>
                <thead>
                  <tr>
                    <th class="text-left">
                      {{ $t('Type') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Count') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Min') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Max') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Average') }}
                    </th>
                    <th class="text-right">
                      {{ $t('Total') }}
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
                          {{ integer(item.count) }}
                        </td>
                        <td class="text-right">
                          {{ integer(item.amount_min) }}
                        </td>
                        <td class="text-right">
                          {{ integer(item.amount_max) }}
                        </td>
                        <td class="text-right">
                          {{ integer(item.amount_avg) }}
                        </td>
                        <td class="text-right">
                          {{ integer(item.amount) }}
                        </td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="6">
                        {{ $t('No data found') }}
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(5).fill(0)" :key="i">
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
import Tooltip from '~/components/Tooltip'
import ComparisonPeriodFilter from '~/components/Filters/ComparisonPeriodFilter'

export default {
  components: { ComparisonPeriodFilter, Tooltip, FilterMenu },

  mixins: [DashboardMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Dashboard') + ' ' + this.$t('Bonuses') }
  },

  data () {
    return {
      queries: [
        'bonuses-by-week',
        'bonuses-stats'
      ]
    }
  },

  computed: {
    stats () {
      return this.data['bonuses-stats'] || null
    }
  },

  methods: {
    integer,
    percentage,
    short
  }
}
</script>

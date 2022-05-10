<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12">
        <v-card :loading="!stats">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('NGR') }}
              <tooltip>{{ $t('Net Gaming Revenue is equal to GGR minus all bonuses and commissions paid to players.') }}</tooltip>
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['period', 'user-role']"
              :disabled="!stats"
              @apply="loadData('stats', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12">
                <div class="headline text-center">
                  {{ $t('NGR') }}
                  <v-icon class="text--secondary">
                    mdi-equal
                  </v-icon>
                  {{ $t('GGR') }}
                  <v-icon class="text--secondary">
                    mdi-minus
                  </v-icon>
                  {{ $t('Bonuses') }}
                  <v-icon class="text--secondary">
                    mdi-minus
                  </v-icon>
                  {{ $t('Affiliate commissions') }}
                  <v-icon class="text--secondary">
                    mdi-minus
                  </v-icon>
                  {{ $t('Credits') }}
                  <v-icon class="text--secondary">
                    mdi-plus
                  </v-icon>
                  {{ $t('Debits') }}
                  <v-icon class="text--secondary">
                    mdi-plus
                  </v-icon>
                  {{ $t('Raffle fees') }}
                </div>
              </v-col>
            </v-row>
            <v-row class="justify-center">
              <v-col v-if="stats" cols="12">
                <div class="text-center display-2">
                  <span class="" :class="{ 'green--text': ngr > 0, 'red--text': ngr < 0 }">
                    {{ short(ngr) }}
                  </span>
                  <v-icon class="text--secondary">
                    mdi-equal
                  </v-icon>
                  {{ short(ggr) }}
                  <v-icon class="text--secondary">
                    mdi-minus
                  </v-icon>
                  {{ short(bonuses) }}
                  <v-icon class="text--secondary">
                    mdi-minus
                  </v-icon>
                  {{ short(commissions) }}
                  <v-icon class="text--secondary">
                    mdi-minus
                  </v-icon>
                  {{ short(credits) }}
                  <v-icon class="text--secondary">
                    mdi-plus
                  </v-icon>
                  {{ short(debits) }}
                  <v-icon class="text--secondary">
                    mdi-plus
                  </v-icon>
                  {{ short(raffleFees) }}
                </div>
              </v-col>
              <v-col v-else cols="12" lg="6">
                <v-skeleton-loader type="text" class="py-4" />
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" lg="6" class="text-center">
        <v-card :loading="!balance">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Balance') }}
              <tooltip>{{ $t('Total amount in credits is displayed') }}</tooltip>
            </v-toolbar-title>
            <v-spacer />
            <period-filter
              :disabled="!balance"
              :hide-details="true"
              :solo="true"
              class="flex-grow-0"
              @change="loadData('accounting-balance', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="depositsTotal > 0 || withdrawalsTotal > 0 ? 100 * depositsTotal / (depositsTotal + withdrawalsTotal) : 0"
                  :color="depositsTotal > 0 ? 'primary' : ''"
                >
                  <span class="headline">{{ short(depositsTotal) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('deposits') }}
                </v-subheader>
              </v-col>
              <v-col cols="12" md="6" class="text-center">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="depositsTotal > 0 || withdrawalsTotal > 0 ? 100 * withdrawalsTotal / (depositsTotal + withdrawalsTotal) : 0"
                  :color="withdrawalsTotal > 0 ? 'primary' : ''"
                >
                  <span class="headline">{{ short(withdrawalsTotal) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('withdrawals') }}
                </v-subheader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" lg="6" class="text-center">
        <v-card :loading="!deposits">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Deposits') }}
            </v-toolbar-title>
            <v-spacer />
            <comparison-period-filter
              :disabled="!deposits"
              :hide-details="true"
              :solo="true"
              class="flex-grow-0"
              @change="loadData('accounting-deposits-comparison', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="previousDeposits > 0 || currentDeposits > 0 ? 100 * previousDeposits / (previousDeposits + currentDeposits) : 0"
                  :color="previousDeposits > 0 ? 'primary' : ''"
                >
                  <span class="headline">{{ short(previousDeposits) }}</span>
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
                  :value="previousDeposits > 0 || currentDeposits > 0 ? 100 * currentDeposits / (previousDeposits + currentDeposits) : 0"
                  :color="currentDeposits > 0 ? 'primary' : ''"
                >
                  <span class="headline">{{ short(currentDeposits) }}</span>
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
      <v-col cols="12" lg="6">
        <v-card class="text-center" :loading="!data['accounting-deposits-history']">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Deposits last 8 weeks') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-sheet>
              <v-sparkline
                :key="!!data['accounting-deposits-history']"
                :value="data['accounting-deposits-history'] || Array(8).fill(0)"
                :color="chartLineColor"
                height="150"
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
      <v-col cols="12" lg="6">
        <v-card class="text-center" :loading="!data['accounting-withdrawals-history']">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Withdrawals last 8 weeks') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-sheet>
              <v-sparkline
                :key="!!data['accounting-withdrawals-history']"
                :value="data['accounting-withdrawals-history'] || Array(8).fill(0)"
                :color="chartLineColor"
                height="150"
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
        <v-card class="text-center" :loading="!depositsByStatus">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Deposits by status') }}
            </v-toolbar-title>
            <v-spacer />
            <period-filter
              :disabled="!depositsByStatus"
              :hide-details="true"
              :solo="true"
              class="flex-grow-0"
              @change="loadData('accounting-deposits-by-status', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">{{ $t('Status') }}</th>
                    <th class="text-right">{{ $t('Count') }}</th>
                    <th class="text-right">{{ $t('Min') }}</th>
                    <th class="text-right">{{ $t('Max') }}</th>
                    <th class="text-right">{{ $t('Average') }}</th>
                    <th class="text-right">{{ $t('Total') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="depositsByStatus">
                    <template v-if="depositsByStatus.length">
                      <tr v-for="item in depositsByStatus" :key="item.status">
                        <td class="text-left">{{ item.status_title }}</td>
                        <td class="text-right">{{ integer(item.count) }}</td>
                        <td class="text-right">{{ decimal(item.amount_min) }}</td>
                        <td class="text-right">{{ decimal(item.amount_max) }}</td>
                        <td class="text-right">{{ decimal(item.amount_avg) }}</td>
                        <td class="text-right">{{ decimal(item.amount) }}</td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="6">
                        {{ $t('No data found') }}
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(4).fill(0)" :key="i">
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
    <v-row>
      <v-col cols="12" lg="12">
        <v-card class="text-center" :loading="!withdrawalsByStatus">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Withdrawals by status') }}
            </v-toolbar-title>
            <v-spacer />
            <period-filter
              :disabled="!withdrawalsByStatus"
              :hide-details="true"
              :solo="true"
              class="flex-grow-0"
              @change="loadData('accounting-withdrawals-by-status', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">{{ $t('Status') }}</th>
                    <th class="text-right">{{ $t('Count') }}</th>
                    <th class="text-right">{{ $t('Min') }}</th>
                    <th class="text-right">{{ $t('Max') }}</th>
                    <th class="text-right">{{ $t('Average') }}</th>
                    <th class="text-right">{{ $t('Total') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="withdrawalsByStatus">
                    <template v-if="withdrawalsByStatus.length">
                      <tr v-for="item in withdrawalsByStatus" :key="item.status">
                        <td class="text-left">{{ item.status_title }}</td>
                        <td class="text-right">{{ integer(item.count) }}</td>
                        <td class="text-right">{{ decimal(item.amount_min) }}</td>
                        <td class="text-right">{{ decimal(item.amount_max) }}</td>
                        <td class="text-right">{{ decimal(item.amount_avg) }}</td>
                        <td class="text-right">{{ decimal(item.amount) }}</td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="6">
                        {{ $t('No data found') }}
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(4).fill(0)" :key="i">
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
import { integer, decimal, percentage, short } from '~/plugins/format'
import DashboardMixin from '~/mixins/Admin/Dashboard'
import PeriodFilter from '~/components/Filters/PeriodFilter'
import ComparisonPeriodFilter from '~/components/Filters/ComparisonPeriodFilter'
import Tooltip from '~/components/Tooltip'
import FilterMenu from '~/components/Filters/FilterMenu'

export default {
  components: { FilterMenu, Tooltip, ComparisonPeriodFilter, PeriodFilter },

  mixins: [DashboardMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Dashboard') + ' ' + this.$t('Accounting') }
  },

  data () {
    return {
      endpoint: '/api/admin/dashboard/payments/data',
      queries: [
        'stats',
        'accounting-balance',
        'accounting-deposits-comparison',
        'accounting-deposits-history',
        'accounting-withdrawals-history',
        'accounting-deposits-by-status',
        'accounting-withdrawals-by-status'
      ]
    }
  },

  computed: {
    stats () {
      return this.data.stats || null
    },
    ngr () {
      return this.stats ? this.stats.ngr : 0
    },
    ggr () {
      return this.stats ? this.stats.ggr : 0
    },
    bonuses () {
      return this.stats ? this.stats.bonuses : 0
    },
    commissions () {
      return this.stats ? this.stats.commissions : 0
    },
    debits () {
      return this.stats ? this.stats.debits : 0
    },
    credits () {
      return this.stats ? this.stats.credits : 0
    },
    raffleFees () {
      return this.stats ? this.stats.raffle_fees : 0
    },
    // totalProfit () {
    //   return this.profit ? this.profit.deposits_total - this.profit.withdrawals_total - this.profit.accounts_total : 0
    // },
    balance () {
      return this.data['accounting-balance'] || null
    },
    depositsTotal () {
      return this.balance !== null ? this.balance[0] : 0
    },
    withdrawalsTotal () {
      return this.balance !== null ? this.balance[1] : 0
    },
    deposits () {
      return this.data['accounting-deposits-comparison'] || null
    },
    previousDeposits () {
      return this.deposits !== null ? this.deposits[0] : 0
    },
    currentDeposits () {
      return this.deposits !== null ? this.deposits[1] : 0
    },
    depositsByStatus () {
      return this.data['accounting-deposits-by-status'] || null
    },
    withdrawalsByStatus () {
      return this.data['accounting-withdrawals-by-status'] || null
    }
  },

  methods: {
    integer,
    decimal,
    percentage,
    short
  }
}
</script>

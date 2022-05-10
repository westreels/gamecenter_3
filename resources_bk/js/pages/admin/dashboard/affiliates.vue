<template>
  <v-container>
    <v-row>
      <v-col cols="12" lg="6">
        <v-card class="text-center" :loading="!data['affiliates-commissions-history']">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Commissions last 8 weeks') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-sheet>
              <v-sparkline
                :value="data['affiliates-commissions-history'] || Array(8).fill(0)"
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
      <v-col cols="12" lg="6" class="text-center">
        <v-card :loading="!referrals">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Referrals') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['comparison-period']"
              :disabled="!referrals"
              @apply="loadData('affiliates-referrals-comparison', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="previousReferrals > 0 || currentReferrals > 0 ? 100 * previousReferrals / (previousReferrals + currentReferrals) : 0"
                  color="primary"
                >
                  <span class="headline">{{ previousReferrals }}</span>
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
                  :value="previousReferrals > 0 || currentReferrals > 0 ? 100 * currentReferrals / (previousReferrals + currentReferrals) : 0"
                  color="primary"
                >
                  <span class="headline">{{ currentReferrals }}</span>
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
      <v-col cols="12" class="text-center">
        <v-card :loading="!commissionsByStatus">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Commissions amounts by status') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['comparison-period']"
              :disabled="!commissionsByStatus"
              @apply="loadData('affiliates-commissions-by-status', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="4">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="commissionsTotal > 0 ? 100 * pendingCommissionsTotal / commissionsTotal : 0"
                  color="primary"
                >
                  <span class="headline">{{ short(pendingCommissionsTotal) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('Pending') }}
                </v-subheader>
              </v-col>
              <v-col cols="12" md="4" class="text-center">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="commissionsTotal > 0 ? 100 * approvedCommissionsTotal / commissionsTotal : 0"
                  color="primary"
                >
                  <span class="headline">{{ short(approvedCommissionsTotal) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('Approved') }}
                </v-subheader>
              </v-col>
              <v-col cols="12" md="4" class="text-center">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="commissionsTotal > 0 ? 100 * rejectedCommissionsTotal / commissionsTotal : 0"
                  color="primary"
                >
                  <span class="headline">{{ short(rejectedCommissionsTotal) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('Rejected') }}
                </v-subheader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" lg="12">
        <v-card class="text-center" :loading="!commissionsByType">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Commissions by type') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['period']"
              :disabled="!commissionsByType"
              @apply="loadData('affiliates-commissions-by-type', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">{{ $t('Type') }}</th>
                    <th class="text-right">{{ $t('Count') }}</th>
                    <th class="text-right">{{ $t('Total') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="commissionsByType">
                    <template v-if="commissionsByType.length">
                      <tr v-for="item in commissionsByType" :key="item.type">
                        <td class="text-left">{{ item.title }}</td>
                        <td class="text-right">{{ integer(item.commissions_count) }}</td>
                        <td class="text-right">{{ decimal(item.commissions_total) }}</td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="6">
                        {{ $t('No data found') }}
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(3).fill(0)" :key="i">
                      <td colspan="3">
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
import { integer, decimal, short } from '~/plugins/format'
import FilterMenu from '~/components/Filters/FilterMenu'
import DashboardMixin from '~/mixins/Admin/Dashboard'

export default {
  components: { FilterMenu },

  mixins: [DashboardMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Dashboard') + ' ' + this.$t('Affiliates') }
  },

  data () {
    return {
      queries: [
        'affiliates-commissions-history',
        'affiliates-referrals-comparison',
        'affiliates-commissions-by-status',
        'affiliates-commissions-by-type'
      ]
    }
  },

  computed: {
    referrals () {
      return this.data['affiliates-referrals-comparison'] || null
    },
    previousReferrals () {
      return this.referrals !== null ? this.referrals[0] : 0
    },
    currentReferrals () {
      return this.referrals !== null ? this.referrals[1] : 0
    },
    commissionsByStatus () {
      return this.data['affiliates-commissions-by-status'] || null
    },
    pendingCommissionsTotal () {
      return this.commissionsByStatus !== null ? this.commissionsByStatus[0] : 0
    },
    approvedCommissionsTotal () {
      return this.commissionsByStatus !== null ? this.commissionsByStatus[1] : 0
    },
    rejectedCommissionsTotal () {
      return this.commissionsByStatus !== null ? this.commissionsByStatus[2] : 0
    },
    commissionsTotal () {
      return this.pendingCommissionsTotal + this.approvedCommissionsTotal + this.rejectedCommissionsTotal
    },
    commissionsByType () {
      return this.data['affiliates-commissions-by-type'] || null
    }
  },

  methods: {
    integer,
    decimal,
    short
  }
}
</script>

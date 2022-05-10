<template>
  <v-container>
    <v-row>
      <v-col cols="12" lg="6">
        <v-card class="text-center" :loading="!data['users-base']">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('User base growth over the last 7 days') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-sheet>
              <v-sparkline
                :key="!!data['users-base']"
                :value="data['users-base'] || Array(7).fill(0)"
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
        <v-card :loading="!signUps">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Sign-ups') }}
            </v-toolbar-title>
            <v-spacer />
            <comparison-period-filter
              :disabled="!signUps"
              :hide-details="true"
              :solo="true"
              class="flex-grow-0"
              @change="loadData('users-sign-ups-comparison', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="previousSignUps > 0 || currentSignUps > 0 ? 100 * previousSignUps / (previousSignUps + currentSignUps) : 0"
                  :color="previousSignUps > 0 ? 'primary' : ''"
                >
                  <span class="headline">{{ previousSignUps }}</span>
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
                  :value="previousSignUps > 0 || currentSignUps > 0 ? 100 * currentSignUps / (previousSignUps + currentSignUps) : 0"
                  :color="currentSignUps > 0 ? 'primary' : ''"
                >
                  <span class="headline">{{ currentSignUps }}</span>
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
        <v-card class="text-center" :loading="!signUpsByMethod">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Sign-ups by source') }}
            </v-toolbar-title>
            <v-spacer />
            <period-filter
              :disabled="!signUpsByMethod"
              :hide-details="true"
              :solo="true"
              class="flex-grow-0"
              @change="loadData('users-sign-ups-by-method', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">{{ $t('Source') }}</th>
                    <th class="text-right">{{ $t('Count') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="signUpsByMethod">
                    <template v-if="signUpsByMethod.length">
                      <tr v-for="item in signUpsByMethod" :key="item.source">
                        <td class="text-left">{{ ucfirst(item.source) }}</td>
                        <td class="text-right">{{ integer(item.count) }}</td>
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
                      <td colspan="2">
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
import { integer, short } from '~/plugins/format'
import { ucfirst } from '~/plugins/utils'
import DashboardMixin from '~/mixins/Admin/Dashboard'
import ComparisonPeriodFilter from '~/components/Filters/ComparisonPeriodFilter'
import PeriodFilter from '~/components/Filters/PeriodFilter'

export default {
  components: { PeriodFilter, ComparisonPeriodFilter },

  mixins: [DashboardMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Dashboard') + ' ' + this.$t('Users') }
  },

  data () {
    return {
      queries: [
        'users-base',
        'users-sign-ups-comparison',
        'users-sign-ups-by-method'
      ]
    }
  },

  computed: {
    signUps () {
      return this.data['users-sign-ups-comparison'] || null
    },
    previousSignUps () {
      return this.signUps !== null ? this.signUps[0] : 0
    },
    currentSignUps () {
      return this.signUps !== null ? this.signUps[1] : 0
    },
    signUpsByMethod () {
      return this.data['users-sign-ups-by-method'] || null
    }
  },

  methods: {
    integer,
    short,
    ucfirst
  }
}
</script>

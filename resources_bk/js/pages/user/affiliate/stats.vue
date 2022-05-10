<template>
  <v-container fluid>
    <v-row justify="center">
      <v-col cols="12" lg="6">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Stats') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <h2 class="title">{{ $t('Registrations') }}</h2>

            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-center">{{ $t('Tier {0}', [1]) }}</th>
                    <th class="text-center">{{ $t('Tier {0}', [2]) }}</th>
                    <th class="text-center">{{ $t('Tier {0}', [3]) }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <template v-if="stats">
                      <td class="text-center">{{ stats.registrations.tier1_count }}</td>
                      <td class="text-center">{{ stats.registrations.tier2_count }}</td>
                      <td class="text-center">{{ stats.registrations.tier3_count }}</td>
                    </template>
                    <template v-else>
                      <td colspan="3">
                        <v-skeleton-loader type="text" />
                      </td>
                    </template>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>

            <h2 class="title mt-5">{{ $t('Commissions by tier') }}</h2>

            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-center">{{ $t('Tier {0}', [1]) }}</th>
                    <th class="text-center">{{ $t('Tier {0}', [2]) }}</th>
                    <th class="text-center">{{ $t('Tier {0}', [3]) }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <template v-if="stats">
                      <td v-for="tier in [1,2,3]" :key="tier" class="text-center">
                        {{ stats.commissions_by_tier[tier] ? decimal(stats.commissions_by_tier[tier].commissions_total) : '0.00' }}
                      </td>
                    </template>
                    <template v-else>
                      <td colspan="3">
                        <v-skeleton-loader type="text" />
                      </td>
                    </template>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>

            <h2 class="title mt-5">{{ $t('Commissions by type') }}</h2>

            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">{{ $t('Type') }}</th>
                    <th class="text-right">{{ $t('Amount') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="stats">
                    <template v-if="stats.commissions_by_type.length">
                      <tr v-for="(commission, type) in stats.commissions_by_type" :key="commission.title">
                        <td class="text-left">{{ commission.title }}</td>
                        <td class="text-right">{{ decimal(commission.commissions_total) }}</td>
                      </tr>
                    </template>
                    <template v-else>
                      <tr>
                        <td colspan="2">
                          {{ $t('No commissions found.') }}
                        </td>
                      </tr>
                    </template>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(5).fill(0)" :key="i">
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
import axios from 'axios'
import { mapState } from 'vuex'
import { decimal } from '~/plugins/format'

export default {
  metaInfo () {
    return { title: this.$t('Affiliate program') }
  },

  data () {
    return {
      stats: null
    }
  },

  computed: {
    ...mapState('auth', [
      'user'
    ])
  },

  async created () {
    const { data } = await axios.get('/api/user/affiliate/stats')

    this.stats = data
  },

  methods: {
    decimal
  }
}
</script>

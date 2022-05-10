<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="8">
        <v-card>
          <v-toolbar>
            <v-btn icon @click="$router.go(-1)">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Withdrawal {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <withdrawal-menu :id="id" />
            <preloader :active="!data" />
          </v-toolbar>
          <v-card-text>
            <v-simple-table class="wrap-long-text">
              <template v-slot:default>
                <tbody>
                  <template v-if="data">
                    <tr v-for="(value, key) in data" :key="key">
                      <td>{{ key }}</td>
                      <td class="text-right">{{ value }}</td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(10).fill(0)" :key="i">
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
import Preloader from '~/components/Preloader'
import WithdrawalMenu from 'packages/payments/resources/js/components/Admin/WithdrawalMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { WithdrawalMenu, Preloader },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Withdrawal {0}', [this.id]) }
  },

  data () {
    return {
      data: null
    }
  },

  async created () {
    const { data } = await axios.get(`/api/admin/withdrawals/${this.id}/transaction`)

    if (!data.success) {
      this.$store.dispatch('message/error', { text: data.message })
      this.$router.push({ name: 'admin.withdrawals.index' })
    }

    this.data = data.data
  }
}
</script>

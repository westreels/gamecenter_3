<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="6">
        <v-card>
          <v-toolbar>
            <v-btn icon @click="$router.go(-1)">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Info') }}
            </v-toolbar-title>
            <v-spacer />
            <preloader :active="!data" />
          </v-toolbar>
          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <tbody>
                  <template v-if="data">
                    <tr v-for="(value, key) in data" :key="key">
                      <td>{{ key }}</td>
                      <td class="text-right">
                        {{ value }}
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(8).fill(0)" :key="i">
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

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { Preloader },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Info') }
  },

  data () {
    return {
      data: null
    }
  },

  async created () {
    const type = this.$route.meta.type

    const { data } = await axios.get(`/api/admin/${type}-methods/${this.id}/info`)

    if (!data.success) {
      this.$store.dispatch('message/error', { text: data.message })
      this.$router.push({ name: `admin.${type}-methods.index` })
    }

    this.data = data.data
  }
}
</script>

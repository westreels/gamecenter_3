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
              {{ $t('Changelog {0} add-on', [id]) }}
            </v-toolbar-title>
            <preloader :active="!data" />
          </v-toolbar>
          <v-card-text>
            <pre v-if="data" class="pre-wrap">{{ data }}</pre>
            <template v-else>
              <v-skeleton-loader type="article" />
              <v-skeleton-loader type="list-item-two-line" />
            </template>
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
    return { title: this.$t('Changelog {0} add-on', [this.id]) }
  },

  data () {
    return {
      data: null
    }
  },

  async created () {
    const { data } = await axios.get(`/api/admin/add-ons/${this.id}/changelog`)

    this.data = data.changelog
  }
}
</script>
<style scoped lang="scss">
.pre-wrap {
  white-space: pre-wrap;
}
</style>

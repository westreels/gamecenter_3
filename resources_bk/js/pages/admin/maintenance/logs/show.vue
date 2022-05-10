<template>
  <v-container fluid>
    <v-card>
      <v-toolbar>
        <v-btn icon @click="$router.go(-1)">
          <v-icon>mdi-arrow-left</v-icon>
        </v-btn>
        <v-toolbar-title>
          {{ file }}.log
        </v-toolbar-title>
        <v-spacer />
        <v-btn icon :href="downloadUrl" target="_blank">
          <v-icon>
            mdi-download
          </v-icon>
        </v-btn>
        <v-btn icon @click="deleteLogFile">
          <v-icon>
            mdi-delete
          </v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text>
        <pre class="log">{{ content }}</pre>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script>
import axios from 'axios'
import { route } from '~/plugins/route'

export default {
  metaInfo () {
    return { title: this.$t('Logs') }
  },

  props: {
    // this.$route.params.file
    file: {
      type: String,
      required: false,
      default: ''
    }
  },

  data () {
    return {
      content: null
    }
  },

  computed: {
    fetchUrl () {
      return route('logs.show').replace('{file}', this.file)
    },
    deleteUrl () {
      return route('logs.delete').replace('{file}', this.file)
    },
    downloadUrl () {
      return route('logs.download').replace('{file}', this.file)
    }
  },

  async created () {
    if (!this.$route.params.file) {
      this.$router.replace({ name: 'admin.maintenance.index' })
    } else {
      this.fetchLogFile()
    }
  },

  methods: {
    async fetchLogFile () {
      const { data: content } = await axios.get(this.fetchUrl)
      this.content = content
    },
    async deleteLogFile () {
      const { data } = await axios.delete(this.fetchUrl)

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })

      this.$router.push({ name: 'admin.maintenance.index' })
    }
  }
}
</script>
<style lang="scss" scoped>
.log {
  max-height: 65vh;
  overflow-x: hidden;
  overflow-y: scroll;
}
</style>

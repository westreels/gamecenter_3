<template>
  <v-container>
    <v-card>
      <v-card-title>
        {{ $t('Help') }}
      </v-card-title>
      <v-card-text>
        <div class="d-flex">
          <v-list
            dense
            rounded
            width="15%"
            min-width="200"
            class="mr-5"
          >
            <v-list-item
              v-for="section in files"
              :key="section"
              :to="{ name: 'admin.help.index', params: { file: section } }"
            >
              <v-list-item-content>
                <v-list-item-title class="text-uppercase">
                  {{ section }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <div v-html="content" />
        </div>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script>
import axios from 'axios'
import goTo from 'vuetify/lib/services/goto'

export default {
  metaInfo () {
    return { title: this.$t('Help') }
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
      files: [],
      content: null
    }
  },

  watch: {
    file (file) {
      this.fetchData(file)
    }
  },

  async created () {
    if (!this.$route.params.file) {
      this.$router.replace({ name: 'admin.help.index', params: { file: 'app' } })
    } else {
      this.fetchData(this.$route.params.file)
    }
  },

  methods: {
    async fetchData (file) {
      const { data: { files, content } } = await axios.get(`/api/admin/help/${file}`)
      this.files = files
      this.content = content

      // scroll into selected question
      this.$nextTick(() => {
        if (this.$route.hash) {
          goTo(this.$route.hash)
        }
      })
    }
  }
}
</script>

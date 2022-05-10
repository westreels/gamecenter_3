<template>
  <v-container class="px-5">
    <v-row>
      <v-col v-if="html[id]" v-html="html[id]" />
    </v-row>
  </v-container>
</template>
<script>
import axios from 'axios'
import { config } from '~/plugins/config'

export default {
  props: ['id'],

  metaInfo () {
    return { title: this.title }
  },

  data () {
    return {
      html: {}
    }
  },

  computed: {
    title () {
      return config('settings.content.footer.menu').find(({ id }) => id === this.id).title
    }
  },

  created () {
    this.fetchPageContent(this.id)
  },

  methods: {
    async fetchPageContent (id) {
      if (!this.html[id]) {
        const { data: { html } } = await axios.get(`/api/pages/${id}`)

        if (!html) {
          this.$router.replace('/404')
        }

        this.$set(this.html, id, html)
      }
    }
  },

  async beforeRouteUpdate (to, from, next) {
    await this.fetchPageContent(to.params.id)

    next()
  }
}
</script>

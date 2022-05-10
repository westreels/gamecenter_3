<script>
import axios from 'axios'

export default {
  data () {
    return {
      data: {},
      endpoint: '/api/admin/dashboard/data'
    }
  },

  computed: {
    theme () {
      const mode = this.$vuetify.theme.isDark ? 'dark' : 'light'
      return this.$vuetify.theme.themes[mode]
    },
    chartLineColor () {
      return this.theme.primary
    }
  },

  created () {
    this.queries.forEach(key => this.loadData(key))
  },

  methods: {
    async loadData (key, params = null) {
      if (this.data[key]) {
        this.$delete(this.data, key)
      }

      const { data } = await axios.get(`${this.endpoint}/${key}`, { params })

      if (data.success) {
        this.$set(this.data, key, data.data)
      } else {
        this.$store.dispatch('message/error', { text: data.message })
      }
    }
  }
}
</script>

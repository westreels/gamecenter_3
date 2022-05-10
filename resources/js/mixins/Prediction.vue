<script>
import { config } from '~/plugins/config'
import { route } from '~/plugins/route'

export default {
  computed: {
    packageId () {
      return this.$route.params.packageId
    },
    config () {
      return config(this.packageId)
    }
  },

  methods: {
    getRoute (action) {
      return route(`markets.${this.packageId}.${action}`)
    }
  }
}
</script>

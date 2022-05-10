<script>
import axios from 'axios'
import Form from 'vform'

export default {
  data () {
    return {
      method: null,
      form: new Form({
        code: null,
        name: null,
        description: null,
        currency: null,
        rate: null,
        decimal_places: null,
        payment_currencies: [],
        parameters: [],
        payment_method_parameters: {},
        enabled: null
      })
    }
  },

  methods: {
    async pullMethod (type) {
      const { data } = await axios.get(`/api/admin/${type}-methods/${this.id}`)

      this.method = data

      // Fill the form with method data
      this.form.keys().forEach(key => {
        this.form[key] = this.method[key]
      })
    },
    async update (type) {
      const { data } = await this.form.patch(`/api/admin/${type}-methods/${this.id}`)

      this.$store.dispatch('message/success', { text: data.message })

      this.$router.push({ name: `admin.${type}-methods.index` })
    }
  }
}
</script>

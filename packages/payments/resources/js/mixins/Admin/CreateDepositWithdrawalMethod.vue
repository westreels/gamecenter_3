<script>
import axios from 'axios'
import Form from 'vform'

export default {
  data () {
    return {
      gateways: null,
      gatewayId: null,
      form: new Form({
        payment_method_id: null,
        code: null,
        name: null,
        description: null,
        currency: 'USD',
        rate: 100,
        decimal_places: null,
        parameters: [],
        payment_method_parameters: {},
        enabled: 0
      })
    }
  },

  computed: {
    gateway () {
      return this.gatewayId
        ? this.gateways.find(o => o.id === this.gatewayId)
        : {}
    },
    gatewayPaymentMethod () {
      return this.gatewayId && this.form.payment_method_id
        ? this.gateway.payment_methods.find(o => o.id === this.form.payment_method_id)
        : {}
    },
    gatewayItems () {
      const emptyValue = [{ text: this.$t('None'), value: null }]

      return this.gateways
        ? [...emptyValue, ...this.gateways.map(gateway => ({ text: gateway.name, value: gateway.id }))]
        : emptyValue
    }
  },

  watch: {
    gateway (gateway) {
      this.form.payment_method_id = null
      this.form.payment_method_parameters = {}
    },
    gatewayPaymentMethod (gatewayPaymentMethod) {
      if (gatewayPaymentMethod.id && gatewayPaymentMethod.parameters.length) {
        // Assign default values to custom parameters
        gatewayPaymentMethod.parameters.forEach(parameter => {
          this.form.payment_method_parameters[parameter.id] = parameter.default
        })
      }
    }
  },

  methods: {
    async pullGateways (type) {
      const { data } = await axios.get(`/api/admin/payment-gateways?type=${type}`)

      this.gateways = data
    },
    async create (type) {
      const { data } = await this.form.post(`/api/admin/${type}-methods`)

      this.$store.dispatch('message/success', { text: data.message })

      this.$router.push({ name: `admin.${type}-methods.index` })
    }
  }
}
</script>

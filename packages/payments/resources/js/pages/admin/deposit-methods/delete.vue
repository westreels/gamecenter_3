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
              {{ $t('Deposit method {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <deposit-method-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <p>
              {{ $t('Are you sure you want to delete this deposit method and all related deposits?') }}
            </p>
            <v-form @submit.prevent="destroy">
              <v-btn type="submit" color="error" :disabled="form.busy" :loading="form.busy">{{ $t('Delete') }}</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import Form from 'vform'
import DepositMethodMenu from 'packages/payments/resources/js/components/Admin/DepositMethodMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { DepositMethodMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Deposit method {0}', [this.id]) }
  },

  data () {
    return {
      form: new Form()
    }
  },

  methods: {
    async destroy () {
      const { data } = await this.form.delete(`/api/admin/deposit-methods/${this.id}`)

      this.$store.dispatch('message/success', { text: data.message })

      this.$router.push({ name: 'admin.deposit-methods.index' })
    }
  }
}
</script>

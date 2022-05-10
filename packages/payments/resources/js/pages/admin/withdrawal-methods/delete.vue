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
              {{ $t('Withdrawal method {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <withdrawal-method-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <p>
              {{ $t('Are you sure you want to delete this withdrawal method and all related withdrawals?') }}
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
import WithdrawalMethodMenu from 'packages/payments/resources/js/components/Admin/WithdrawalMethodMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { WithdrawalMethodMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Withdrawal method {0}', [this.id]) }
  },

  data () {
    return {
      form: new Form()
    }
  },

  methods: {
    async destroy () {
      const { data } = await this.form.delete(`/api/admin/withdrawal-methods/${this.id}`)

      this.$store.dispatch('message/success', { text: data.message })

      this.$router.push({ name: 'admin.withdrawal-methods.index' })
    }
  }
}
</script>

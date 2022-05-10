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
              {{ $t('Account {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <account-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="process">
              <v-text-field
                v-model="form.amount"
                :label="$t('Amount')"
                :disabled="form.busy"
                :rules="[validationRequired, validationNumeric]"
                :error="form.errors.has('amount')"
                :error-messages="form.errors.get('amount')"
                outlined
                @keydown="clearFormErrors"
              />

              <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
                {{ $t('Debit') }}
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import AccountMenu from '~/components/Admin/AccountMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { AccountMenu },

  mixins: [FormMixin],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Account {0}', [this.id]) }
  },

  data () {
    return {
      form: new Form({
        amount: null
      })
    }
  },

  methods: {
    async process () {
      await this.form.post(`/api/admin/accounts/${this.id}/debit`)

      this.$store.dispatch('message/success', { text: this.$t('Account successfully debited.') })

      this.$router.push({ name: 'admin.accounts.index' })
    }
  }
}
</script>

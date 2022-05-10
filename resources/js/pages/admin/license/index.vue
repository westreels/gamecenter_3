<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="6">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('License registration') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="register">
              <v-skeleton-loader type="heading" :loading="!loaded" class="mt-7">
                <v-text-field
                  v-model="form.code"
                  :label="$t('Purchase code')"
                  type="text"
                  name="code"
                  :rules="[validationRequired]"
                  :error="form.errors.has('code')"
                  :error-messages="form.errors.get('code')"
                  outlined
                  @keydown="clearFormErrors($event,'code')"
                />
              </v-skeleton-loader>

              <v-skeleton-loader type="heading" :loading="!loaded" class="mt-7 mb-7">
                <v-text-field
                  v-model="form.email"
                  :label="$t('Licensee email')"
                  type="email"
                  name="email"
                  :rules="[validationRequired, validationEmail]"
                  :error="form.errors.has('email')"
                  :error-messages="form.errors.get('email')"
                  outlined
                  @keydown="clearFormErrors($event,'email')"
                />
              </v-skeleton-loader>

              <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
                {{ $t('Register') }}
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import Form from 'vform'
import FormMixin from '~/mixins/Form'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('License registration') }
  },

  mixins: [FormMixin],

  data () {
    return {
      form: new Form({
        code: null,
        email: null
      }),
      loaded: false
    }
  },

  async created () {
    const { data: { code, email } } = await axios.get('/api/admin/license')

    this.form.code = code
    this.form.email = email
    this.loaded = true
  },

  methods: {
    async register () {
      const { data } = await this.form.submit('post', '/api/admin/license')

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })
    }
  }
}
</script>

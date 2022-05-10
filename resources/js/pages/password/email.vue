<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="elevation-12">
          <v-toolbar color="primary">
            <router-link :to="{ name: 'home' }">
              <v-avatar size="40" tile>
                <v-img :src="appLogoUrl" />
              </v-avatar>
            </router-link>
            <v-toolbar-title class="ml-2">
              {{ $t('Password reset') }}
            </v-toolbar-title>
            <v-spacer />
          </v-toolbar>
          <v-card-text>
            <v-form ref="form" v-model="formIsValid" @submit.prevent="reset">
              <v-text-field
                v-model="form.email"
                :label="$t('Email')"
                type="email"
                name="email"
                :rules="[validationRequired, validationEmail]"
                :error="form.errors.has('email')"
                :error-messages="form.errors.get('email')"
                outlined
                @keydown="clearFormErrors"
              />

              <vue-recaptcha
                v-if="recaptchaPublicKey"
                ref="recaptcha"
                :sitekey="recaptchaPublicKey"
                :loadRecaptchaScript="true"
                :theme="this.$vuetify.theme.isDark ? 'dark' : 'light'"
                @verify="token => form.recaptcha = token"
                class="mb-3"
              />

              <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy || (!!recaptchaPublicKey && !form.recaptcha)" :loading="form.busy">
                {{ $t('Reset') }}
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { config } from '~/plugins/config'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import VueRecaptcha from 'vue-recaptcha'

export default {
  components: { VueRecaptcha },

  mixins: [FormMixin],

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('Password reset') }
  },

  data: () => ({
    form: new Form({
      email: null,
      recaptcha: null
    })
  }),

  computed: {
    appLogoUrl () {
      return config('app.logo')
    },
    recaptchaPublicKey () {
      return config('services.recaptcha.public_key')
    }
  },

  methods: {
    async reset () {
      const { data } = await this.form.post('/api/auth/password/email')
        .catch(() => {
          if (this.recaptchaPublicKey) {
            this.form.recaptcha = null
            this.$refs.recaptcha.reset()
          }
          return {}
        })

      // in case of any error data will be undefined
      if (data) {
        this.$store.dispatch('message/success', { text: data.message })

        this.form.reset()
        this.$refs.recaptcha.reset()
        this.$refs.form.reset()
      }
    }
  }
}
</script>

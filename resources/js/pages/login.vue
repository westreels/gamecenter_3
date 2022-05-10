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
              {{ $t('Authentication') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <o-auth />
            <v-form v-model="formIsValid" @submit.prevent="login">
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

              <v-text-field
                v-model="form.password"
                :label="$t('Password')"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword ? 'text' : 'password'"
                name="password"
                :rules="[validationRequired]"
                :error="form.errors.has('password')"
                :error-messages="form.errors.get('password')"
                outlined
                :counter="true"
                @click:append="showPassword = !showPassword"
                @keydown="clearFormErrors"
              />

              <v-checkbox
                v-model="form.remember"
                name="remember"
                :label="$t('Remember me')"
                color="primary"
                true-value="1"
                false-value=""
              />

              <vue-recaptcha
                v-if="recaptchaPublicKey"
                ref="recaptcha"
                :sitekey="recaptchaPublicKey"
                :loadRecaptchaScript="true"
                :theme="this.$vuetify.theme.isDark ? 'dark' : 'light'"
                class="mb-3"
                @verify="token => form.recaptcha = token"
              />

              <v-row align="center">
                <v-col class="text-center text-md-left">
                  <v-btn type="submit" color="primary" :disabled="!formIsValid || loading || (!!recaptchaPublicKey && !form.recaptcha)" :loading="loading">
                    {{ $t('Log in') }}
                  </v-btn>
                </v-col>
                <v-col class="d-flex flex-column text-center text-md-right">
                  <router-link :to="{ name: 'register' }">
                    {{ $t('Not signed up?') }}
                  </router-link>
                  <router-link :to="{ name: 'password.email' }">
                    {{ $t('Forgot password?') }}
                  </router-link>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import { config } from '~/plugins/config'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import OAuth from '~/components/OAuth'
import { mapState } from 'vuex'
import VueRecaptcha from 'vue-recaptcha'

export default {
  components: { OAuth, VueRecaptcha },

  mixins: [FormMixin],

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('Authentication') }
  },

  data () {
    return {
      loading: false,
      csrfCookieRetrieved: false,
      showPassword: false,
      form: new Form({
        email: null,
        password: null,
        remember: '', // it's important to pass empty string as false value so $request->filled('remember') returns false,
        recaptcha: null
      })
    }
  },

  computed: {
    ...mapState('auth', [
      'user'
    ]),
    appLogoUrl () {
      return config('app.logo')
    },
    recaptchaPublicKey () {
      return config('services.recaptcha.public_key')
    }
  },

  methods: {
    async login () {
      this.loading = true

      if (!this.csrfCookieRetrieved) {
        await axios.get('/sanctum/csrf-cookie')
        this.csrfCookieRetrieved = true
      }

      // log in
      const { data } = await this.form.post('/api/auth/login')
        .catch(() => {
          if (this.recaptchaPublicKey) {
            this.form.recaptcha = null
            this.$refs.recaptcha.reset()
            this.loading = false
          }
          return {}
        })

      // in case of any error data will be undefined
      if (data) {
        // Store the user
        this.$store.dispatch('auth/updateUser', data)

        if (this.user.two_factor_auth_enabled && !this.user.two_factor_auth_passed) {
          this.$router.push({ name: '2fa' })
        } else {
          this.$router.push({ name: 'home' })
        }
      } else {
        this.loading = false
      }
    }
  }
}
</script>

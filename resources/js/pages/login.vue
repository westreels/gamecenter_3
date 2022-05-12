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
              <a :href="urlLogin">
                <v-btn
                    style="background: linear-gradient(283.85deg, #1B0441 -30.23%, #521698 76.26%)"
                    depressed
                    color="primary"
                >
                  Login with your social account
                </v-btn>
              </a>
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
    },
    urlLogin () {
      const domain = window.config.define.social.domain;
      const appId = window.config.define.social.app_id;
      const appSecret = window.config.define.social.app_secret;

      return `${domain}/oauth?app_id=${appId}&app_secret=${appSecret}`
    },
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
        // Store the users

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

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
              {{ $t('Registration') }}
            </v-toolbar-title>
            <v-spacer />
          </v-toolbar>
          <v-card-text>
            <o-auth />
            <v-form ref="form" v-model="formIsValid" @submit.prevent="register">
              <v-text-field
                v-model="form.name"
                :label="$t('Name')"
                type="text"
                name="name"
                :rules="[validationRequired]"
                :error="form.errors.has('name')"
                :error-messages="form.errors.get('name')"
                outlined
                @keydown="clearFormErrors($event,'name')"
              />

              <v-text-field
                v-model="form.email"
                :label="$t('Email')"
                type="email"
                name="email"
                :rules="[validationRequired, validationEmail]"
                :error="form.errors.has('email')"
                :error-messages="form.errors.get('email')"
                outlined
                @keydown="clearFormErrors($event,'email')"
              />

              <v-text-field
                v-model="form.password"
                :label="$t('Password')"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword ? 'text' : 'password'"
                name="password"
                :rules="[validationRequired, v => validationMinLength(v,6)]"
                :error="form.errors.has('password')"
                :error-messages="form.errors.get('password')"
                outlined
                :counter="true"
                @click:append="showPassword = !showPassword"
                @keydown="clearFormErrors($event,'password')"
              />

              <v-text-field
                v-model="form.password_confirmation"
                :label="$t('Confirm password')"
                :append-icon="showPassword2 ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword2 ? 'text' : 'password'"
                name="password_confirmation"
                :rules="[validationRequired, v => validationMatch(form.password,v)]"
                :error="form.errors.has('password_confirmation')"
                :error-messages="form.errors.get('password_confirmation')"
                outlined
                :counter="true"
                @click:append="showPassword2 = !showPassword2"
                @keydown="clearFormErrors($event,'password_confirmation')"
              />

              <v-checkbox
                v-model="agreementAccepted"
                color="primary"
              >
                <template v-slot:label>
                  <i18n path="I accept {0} and {1}" tag="div">
                    <template v-slot:0>
                      <a href="/pages/terms-of-use" target="_blank" @click.stop>
                        {{ $t('Terms of use') }}
                      </a>
                    </template>
                    <template v-slot:1>
                      <a href="/pages/privacy-policy" target="_blank" @click.stop>
                        {{ $t('Privacy policy') }}
                      </a>
                    </template>
                  </i18n>
                </template>
              </v-checkbox>

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
                  <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy || !agreementAccepted || (!!recaptchaPublicKey && !form.recaptcha)" :loading="form.busy">
                    {{ $t('Register') }}
                  </v-btn>
                </v-col>
                <v-col class="text-center text-md-right">
                  <router-link :to="{ name: 'login' }">
                    {{ $t('Already signed up?') }}
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
import { config } from '~/plugins/config'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import OAuth from '~/components/OAuth'
import VueRecaptcha from 'vue-recaptcha'

export default {
  components: { OAuth, VueRecaptcha },

  mixins: [FormMixin],

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('Registration') }
  },

  data () {
    return {
      agreementAccepted: false,
      showPassword: false,
      showPassword2: false,
      form: new Form({
        name: null,
        email: null,
        password: null,
        password_confirmation: null,
        recaptcha: null
      })
    }
  },

  computed: {
    appLogoUrl () {
      return config('app.logo')
    },
    emailVerification () {
      return config('settings.email_verification')
    },
    recaptchaPublicKey () {
      return config('services.recaptcha.public_key')
    }
  },

  methods: {
    async register () {
      // Register the user
      const { data } = await this.form.post('/api/auth/register')
        .catch(() => {
          if (this.recaptchaPublicKey) {
            this.form.recaptcha = null
            this.$refs.recaptcha.reset()
          }
          return {}
        })

      // in case of any error data will be undefined
      if (data) {
        // Store the user
        this.$store.dispatch('auth/updateUser', data)

        this.$store.dispatch('message/success', {
          text: this.emailVerification
            ? ['We have sent a verification link to your email.', 'Please click on that link to verify your email and continue using our website.']
              .map(m => this.$t(m)).join(' ')
            : this.$t('You have successfully registered!')
        })

        // Redirect home
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>

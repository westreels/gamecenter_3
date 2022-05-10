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
                :error="form.errors.has('email')"
                :error-messages="form.errors.get('email')"
                outlined
                readonly
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
                counter
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
                counter
                @click:append="showPassword2 = !showPassword2"
                @keydown="clearFormErrors($event,'password_confirmation')"
              />

              <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
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

export default {
  mixins: [FormMixin],

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('Password reset') }
  },

  data: () => ({
    showPassword: false,
    showPassword2: false,
    form: new Form({
      token: null,
      email: null,
      password: null,
      password_confirmation: null
    })
  }),

  computed: {
    appLogoUrl () {
      return config('app.logo')
    }
  },

  created () {
    this.form.email = this.$route.query.email
    this.form.token = this.$route.params.token
  },

  methods: {
    async reset () {
      const { data } = await this.form.post('/api/auth/password/reset')

      await this.$store.dispatch('auth/fetchUser')

      this.$store.dispatch('message/success', { text: data.message })

      this.$router.push({ name: 'home' })
    }
  }
}
</script>

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
              {{ $t('Two-factor authentication') }}
            </v-toolbar-title>
            <v-spacer/>
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="verify">
              <v-text-field
                v-model="form.one_time_password"
                :label="$t('One-time password')"
                type="text"
                name="one_time_password"
                :rules="[validationRequired, v => validationMinLength(v,6)]"
                :error="form.errors.has('one_time_password')"
                :error-messages="form.errors.get('one_time_password')"
                outlined
                @keydown="clearFormErrors($event, 'one_time_password', form)"
              />

              <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
                {{ $t('Verify') }}
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
  middleware: ['auth', 'verified', '2fa_not_passed'],

  mixins: [FormMixin],

  metaInfo () {
    return { title: this.$t('Two-factor authentication') }
  },

  data: () => ({
    formIsValid: null,
    form: new Form({
      one_time_password: null
    })
  }),

  computed: {
    appLogoUrl () {
      return config('app.logo')
    }
  },

  methods: {
    async verify () {
      const { data } = await this.form.post('/api/user/security/2fa/verify')

      // Update the user
      this.$store.dispatch('auth/updateUser', data)

      this.$router.push({ name: 'home' })
    }
  }
}
</script>

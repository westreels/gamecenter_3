<template>
  <v-container fluid>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Change password') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form ref="form" v-model="formIsValid" @submit.prevent="update">
              <v-text-field
                v-model="form.current_password"
                :label="$t('Current password')"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword ? 'text' : 'password'"
                name="current_password"
                :rules="[validationRequired]"
                :error="form.errors.has('current_password')"
                :error-messages="form.errors.get('current_password')"
                outlined
                counter
                @click:append="showPassword = !showPassword"
                @keydown="clearFormErrors($event,'current_password')"
              />

              <v-text-field
                v-model="form.password"
                :label="$t('New password')"
                :append-icon="showPassword2 ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword2 ? 'text' : 'password'"
                name="password"
                :rules="[validationRequired, v => validationMinLength(v,6)]"
                :error="form.errors.has('password')"
                :error-messages="form.errors.get('password')"
                outlined
                counter
                @click:append="showPassword2 = !showPassword2"
                @keydown="clearFormErrors($event,'password')"
              />

              <v-text-field
                v-model="form.password_confirmation"
                :label="$t('Confirm new password')"
                :append-icon="showPassword3 ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword3 ? 'text' : 'password'"
                name="password_confirmation"
                :rules="[validationRequired, v => validationMatch(form.password,v)]"
                :error="form.errors.has('password_confirmation')"
                :error-messages="form.errors.get('password_confirmation')"
                outlined
                counter
                @click:append="showPassword3 = !showPassword3"
                @keydown="clearFormErrors($event,'password_confirmation')"
              />

              <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
                {{ $t('Save') }}
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

export default {
  mixins: [FormMixin],

  metaInfo () {
    return { title: this.$t('Change password') }
  },

  data: () => ({
    showPassword: false,
    showPassword2: false,
    showPassword3: false,
    form: new Form({
      current_password: '',
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async update () {
      await this.form.patch('/api/user/security/password/update')

      this.$store.dispatch('message/success', { text: this.$t('Password successfully changed.') })

      this.form.reset()
      this.$refs.form.reset()
    }
  }
}
</script>

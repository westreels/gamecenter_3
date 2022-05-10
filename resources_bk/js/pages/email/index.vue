<template>
  <v-container fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Email verification') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <p>
              {{ $t('We have sent a verification link to your email.') }}
              {{ $t('Please click on that link to verify your email and continue using our website.') }}
            </p>
            <v-form ref="form" @submit.prevent="send">
              <v-btn type="submit" color="primary" :disabled="form.busy" :loading="form.busy">
                {{ $t('Resend verification link') }}
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

export default {
  middleware: ['auth', 'not_verified'],

  metaInfo () {
    return { title: this.$t('Email verification') }
  },

  data () {
    return {
      form: new Form()
    }
  },

  methods: {
    async send () {
      await this.form.post('/api/email/resend')

      this.$store.dispatch('message/success', { text: this.$t('A fresh verification link has been sent to your email address.') })

      this.form.reset()
      this.$refs.form.reset()
    }
  }
}
</script>

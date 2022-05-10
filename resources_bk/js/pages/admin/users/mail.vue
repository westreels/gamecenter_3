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
              {{ $t('User {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <user-menu :id="id" />
            <preloader :active="!user.id" />
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="send">
              <v-text-field
                v-model="form.subject"
                :label="$t('Subject')"
                type="text"
                name="subject"
                :disabled="form.busy || !user.id"
                :rules="[validationRequired]"
                :error="form.errors.has('subject')"
                :error-messages="form.errors.get('subject')"
                outlined
                @keydown="clearFormErrors($event,'subject')"
              />

              <v-text-field
                v-model="form.greeting"
                :label="$t('Greeting')"
                type="text"
                name="greeting"
                :disabled="form.busy || !user.id"
                :rules="[validationRequired]"
                :error="form.errors.has('greeting')"
                :error-messages="form.errors.get('greeting')"
                outlined
                @keydown="clearFormErrors($event,'greeting')"
              />

              <v-text-field
                :label="$t('Email')"
                type="email"
                outlined
                :value="user.email"
                disabled
              />

              <v-textarea
                v-model="form.message"
                :label="$t('Message')"
                name="message"
                :disabled="form.busy || !user.id"
                :rules="[validationRequired]"
                :error="form.errors.has('message')"
                :error-messages="form.errors.get('message')"
                outlined
                @keydown="clearFormErrors($event,'message')"
              />

              <v-skeleton-loader type="button" :loading="!user.id">
                <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
                  {{ $t('Send') }}
                </v-btn>
              </v-skeleton-loader>
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
import UserMenu from '~/components/Admin/UserMenu'
import Preloader from '~/components/Preloader'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { Preloader, UserMenu },

  mixins: [FormMixin],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('User {0}', [this.id]) }
  },

  data () {
    return {
      user: {},
      roles: [],
      showPassword: false,
      email: null,
      form: new Form({
        subject: null,
        greeting: this.$t('Hello'),
        message: null
      })
    }
  },

  async created () {
    const { data } = await axios.get(`/api/admin/users/${this.id}`)

    this.user = data.user
  },

  methods: {
    async send () {
      await this.form.post(`/api/admin/users/${this.id}/mail`)

      this.$store.dispatch('message/success', { text: this.$t('Message is successfully sent.') })

      this.$router.push({ name: 'admin.users.index' })
    }
  }
}
</script>

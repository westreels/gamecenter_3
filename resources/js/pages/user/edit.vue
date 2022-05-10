<template>
  <v-container fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Profile') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="update">
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
                :disabled="!!user.email_verified_at"
                outlined
                @keydown="clearFormErrors($event,'email')"
              >
                <template v-slot:append>
                  <v-icon v-if="user.email_verified_at" color="success" >
                    mdi-check
                  </v-icon>
                  <v-btn
                    v-else
                    text
                    class="mt-n2"
                    :loading="emailVerificationForm.busy"
                    @click="verifyEmail"
                  >
                    {{ $t('Verify') }}
                  </v-btn>
                </template>
              </v-text-field>

              <image-upload
                :label="$t('Avatar')"
                :form="form"
                form-image-property-name="avatar"
                :default-value="user.avatar"
                :default-url="user.avatar_url"
                @change="changeAvatar"
              />

              <v-switch
                v-model="form.hide_profit"
                :label="$t('Hide profit')"
                color="primary"
                :false-value="false"
                :true-value="true"
              />

              <v-btn type="submit" color="primary" :disabled="!formIsValid || !changed || form.busy" :loading="form.busy">
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
import ImageUpload from '~/components/ImageUpload'
import { mapState } from 'vuex'
import objectToFormData from '~/plugins/objectToFormData'

export default {
  components: { ImageUpload },

  mixins: [FormMixin],

  middleware: ['auth', 'verified', '2fa_passed'],

  metaInfo () {
    return { title: this.$t('Profile') }
  },

  data () {
    return {
      form: new Form({
        name: null,
        email: null,
        hide_profit: null
      }),
      emailVerificationForm: new Form()
    }
  },

  computed: {
    ...mapState('auth', [
      'user'
    ]),
    changed () {
      return this.form.keys().some(key => this.form[key] !== this.user[key])
    }
  },

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })
  },

  methods: {
    changeAvatar (avatar) {
      this.$set(this.form, 'avatar', avatar)
    },
    async verifyEmail () {
      await this.emailVerificationForm.submit('post', '/api/email/resend')

      this.$store.dispatch('message/success', { text: this.$t('A fresh verification link has been sent to your email address.') })
    },
    async update () {
      // transform form data to FormData if avatar is uploaded
      const options = this.form.avatar ? { transformRequest: [(data, headers) => objectToFormData(data)] } : {}

      const { data } = await this.form.submit('post', '/api/user/update', options)

      this.$store.dispatch('auth/updateUser', data)

      this.$store.dispatch('message/success', { text: this.$t('Data successfully updated.') })
    }
  }
}
</script>

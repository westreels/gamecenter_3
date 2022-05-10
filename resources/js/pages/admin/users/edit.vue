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
            <preloader :active="!user" />
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="update">
              <v-text-field
                v-model="form.name"
                :label="$t('Name')"
                type="text"
                :disabled="form.busy || !user"
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
                :disabled="form.busy || !user"
                :rules="[validationRequired, validationEmail]"
                :error="form.errors.has('email')"
                :error-messages="form.errors.get('email')"
                outlined
                @keydown="clearFormErrors($event,'email')"
              />

              <v-select
                v-model="form.role"
                :items="roles"
                :label="$t('Role')"
                :disabled="form.busy || !user"
                :error="form.errors.has('role')"
                :error-messages="form.errors.get('role')"
                outlined
              />

              <v-switch
                v-if="form.role === 2"
                v-model="individualPermissions"
                :label="$t('Individual permissions')"
                color="primary"
                :disabled="form.busy || !user"
                class="mb-5"
              />

              <v-card
                v-if="form.role === 2 && individualPermissions"
                outlined
                class="mb-10"
              >
                <v-card-title class="font-weight-thin">
                  {{ $t('Permissions') }}
                </v-card-title>
                <v-card-text>
                  <v-row v-for="(permission, id) in permissions" :key="id" class="align-center">
                    <v-col cols="6">
                      {{ permission }}
                    </v-col>
                    <v-col cols="6">
                      <v-select
                        v-if="form.permissions !== null"
                        v-model="form.permissions[id]"
                        :items="accessModes"
                        :label="$t('Access')"
                        hide-details
                        dense
                        outlined
                      />
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>

              <file-upload
                v-if="user"
                v-model="form.avatar"
                :label="$t('Avatar')"
                :name="user.id.toString()"
                path="/storage/avatars"
                folder="avatars"
              />

              <v-text-field
                v-model="form.password"
                :label="$t('Password')"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword ? 'text' : 'password'"
                :disabled="form.busy || !user"
                :error="form.errors.has('password')"
                :error-messages="form.errors.get('password')"
                :hint="$t('Leave empty to preserve current user password.')"
                persistent-hint
                outlined
                :counter="true"
                @click:append="showPassword = !showPassword"
                @keydown="clearFormErrors($event,'password')"
              />

              <v-switch
                v-model="form.status"
                :label="$t('Blocked')"
                color="primary"
                :disabled="form.busy || !user"
                :false-value="0"
                :true-value="1"
              />

              <v-switch
                v-model="form.hide_profit"
                :label="$t('Hide profit')"
                color="primary"
                :disabled="form.busy || !user"
                :false-value="false"
                :true-value="true"
              />

              <v-switch
                v-model="form.banned_from_chat"
                :label="$t('Banned from chat')"
                color="primary"
                :disabled="form.busy || !user"
                :false-value="false"
                :true-value="true"
              />

              <v-skeleton-loader type="button" :loading="!user">
                <v-btn type="submit" color="primary" :disabled="!formIsValid || !changed || form.busy" :loading="form.busy">
                  {{ $t('Save') }}
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
import FileUpload from '~/components/Admin/FileUpload'

export default {
  components: { FileUpload, Preloader, UserMenu },

  mixins: [FormMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('User {0}', [this.id]) }
  },

  data () {
    return {
      user: null,
      roles: [],
      accessModes: [],
      permissions: {},
      individualPermissions: false,
      showPassword: false,
      form: new Form({
        name: null,
        email: null,
        role: null,
        permissions: null,
        avatar: null,
        status: null,
        hide_profit: null,
        banned_from_chat: null
      })
    }
  },

  computed: {
    changed () {
      return this.form.keys().some(key => this.user && this.form[key] !== this.user[key])
    }
  },

  watch: {
    individualPermissions (enabled) {
      if (enabled && !this.form.permissions) {
        // set default values
        this.form.permissions = { ...Object.keys(this.permissions).reduce((a, key) => ({ ...a, [key]: 0 }), {}) }
      } else if (!enabled) {
        this.form.permissions = null
      }
    },
    'form.role' (role) {
      if (role !== 2) {
        this.individualPermissions = false
      }
    }
  },

  async created () {
    const { data } = await axios.get(`/api/admin/users/${this.id}`)

    this.user = data.user
    this.roles = Object.keys(data.roles).map(i => ({ value: parseInt(i, 10), text: data.roles[i] }))
    this.accessModes = Object.keys(data.access_modes).map(i => ({ value: parseInt(i, 10), text: data.access_modes[i] }))
    this.permissions = data.permissions

    if (this.user.permissions) {
      this.individualPermissions = true
    }

    // Fill the form with user data
    this.form.keys().forEach(key => {
      const value = this.user[key]
      this.form[key] = value !== null && typeof value === 'object' ? { ...value } : value // note that, typeof value = 'object'
    })
  },

  methods: {
    async update () {
      await this.form.patch(`/api/admin/users/${this.id}`)

      this.$store.dispatch('message/success', { text: this.$t('User successfully updated.') })

      this.$router.push({ name: 'admin.users.index' })
    }
  }
}
</script>

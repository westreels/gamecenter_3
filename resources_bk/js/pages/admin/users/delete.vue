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
          </v-toolbar>
          <v-card-text>
            <p>
              {{ $t('Are you sure you want to delete this user?') }}
            </p>
            <v-form @submit.prevent="destroy">
              <v-btn type="submit" color="error" :disabled="form.busy" :loading="form.busy">
                {{ $t('Delete') }}
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
import UserMenu from '~/components/Admin/UserMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { UserMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('User {0}', [this.id]) }
  },

  data () {
    return {
      form: new Form()
    }
  },

  methods: {
    async destroy () {
      await this.form.delete(`/api/admin/users/${this.id}`)

      this.$store.dispatch('message/success', { text: this.$t('User successfully deleted.') })

      this.$router.push({ name: 'admin.users.index' })
    }
  }
}
</script>

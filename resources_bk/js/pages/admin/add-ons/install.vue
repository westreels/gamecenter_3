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
              {{ $t('Install {0} add-on', [id]) }}
            </v-toolbar-title>
            <preloader :active="!data" />
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="install">
              <v-text-field
                v-model="form.code"
                :label="$t('Purchase code')"
                :disabled="form.busy"
                :rules="[validationRequired]"
                :error="form.errors.has('code')"
                :error-messages="form.errors.get('code')"
                outlined
                @keydown="clearFormErrors($event,'code')"
              />

              <v-skeleton-loader type="button" :loading="!data">
                <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
                  {{ $t('Install') }}
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
import Preloader from '~/components/Preloader'

export default {
  components: { Preloader },

  mixins: [FormMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Install {0} add-on', [this.id]) }
  },

  data () {
    return {
      data: null,
      form: new Form({
        code: null
      })
    }
  },

  async created () {
    const { data } = await axios.get(`/api/admin/add-ons/${this.id}`)

    this.data = data

    this.form.code = data.code
  },

  methods: {
    async install () {
      const { data } = await this.form.post(`/api/admin/add-ons/${this.id}/install`)

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })

      if (data.success) {
        this.$router.push({ name: 'admin.add-ons.index' })
      }
    }
  }
}
</script>

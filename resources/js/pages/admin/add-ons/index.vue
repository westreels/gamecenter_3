<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card :loading="!data.packages">
          <v-card-title>
            {{ $t('Add-ons') }}
          </v-card-title>
          <v-card-text>
            <v-row v-for="(pkg, id) in data.packages" :key="id">
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ pkg.name }}
                    <template v-if="pkg.enabled">
                      <v-chip
                        class="ml-2"
                        color="secondary"
                        small
                        label
                      >
                        {{ pkg.version }}
                      </v-chip>
                    </template>
                  </v-card-title>
                  <v-card-text>
                    {{ pkg.description }}
                    <v-alert
                      v-if="pkg.system_requirements"
                      prominent
                      border="left"
                      type="warning"
                      :icon="null"
                      outlined
                      class="mt-5"
                    >
                      <h6 class="text-h6 font-weight-light mb-3">
                        {{ $t('System requirements') }}
                      </h6>
                      <ul>
                        <li v-for="(req, i) in pkg.system_requirements" :key="i">
                          {{ req }}
                        </li>
                      </ul>
                    </v-alert>
                    <div v-if="pkg.enabled && pkg.update_available && !pkg.update_can_be_installed" class="mt-2 error--text">
                      {{ $t('Please upgrade Stake to version {0} first to be able to upgrade the add-on.', [data.releases['add-ons'][id].min_app_version]) }}
                    </div>
                    <div v-else-if="pkg.installed && !pkg.enabled && !pkg.min_app_version_installed" class="mt-2 error--text">
                      {{ $t('Please upgrade Stake to version {0} to use this add-on', [pkg.min_app_version]) }}
                    </div>
                  </v-card-text>
                  <v-card-actions>
                    <template v-if="pkg.installed && pkg.min_app_version_installed">
                      <template v-if="pkg.update_available">
                        <v-btn
                          v-if="pkg.hash"
                          outlined
                          color="warning"
                          :to="{ name: 'admin.add-ons.install', params: { id } }"
                          :disabled="!pkg.update_can_be_installed"
                        >
                          <v-icon left>
                            mdi-upload
                          </v-icon>
                          {{ $t('Upgrade to {0}', [data.releases['add-ons'][id].version]) }}
                        </v-btn>
                      </template>
                      <template v-else-if="pkg.enabled">
                        <v-btn v-if="pkg.hash" outlined color="primary" :to="{ name: 'admin.add-ons.install', params: { id } }">
                          <v-icon left>
                            mdi-autorenew
                          </v-icon>
                          {{ $t('Re-install') }}
                        </v-btn>
                      </template>
                      <v-btn v-if="pkg.enabled" outlined color="error" @click="disable(id)">
                        <v-icon left>
                          mdi-close
                        </v-icon>
                        {{ $t('Disable') }}
                      </v-btn>
                      <v-btn v-else outlined color="success" @click="enable(id)">
                        <v-icon left>
                          mdi-check
                        </v-icon>
                        {{ $t('Enable') }}
                      </v-btn>
                      <v-btn outlined color="grey" :to="{ name: 'admin.add-ons.changelog', params: { id } }">
                        <v-icon left>
                          mdi-text
                        </v-icon>
                        {{ $t('Changelog') }}
                      </v-btn>
                    </template>
                    <template v-else-if="!pkg.installed">
                      <v-btn v-if="pkg.purchase_url" outlined color="primary" :href="pkg.purchase_url" target="_blank">
                        <v-icon left>
                          mdi-cart
                        </v-icon>
                        {{ $t('Purchase') }}
                      </v-btn>
                      <v-btn v-if="pkg.hash" outlined color="primary" :to="{ name: 'admin.add-ons.install', params: { id } }">
                        <v-icon left>
                          mdi-download
                        </v-icon>
                        {{ $t('Install') }}
                      </v-btn>
                    </template>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
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

export default {
  mixins: [FormMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Add-ons') }
  },

  data () {
    return {
      form: new Form(),
      data: {}
    }
  },

  async created () {
    const { data } = await axios.get('/api/admin/add-ons')

    this.data = data
  },

  methods: {
    async disable (packageId) {
      const { data } = await this.form.post(`/api/admin/add-ons/${packageId}/disable`)

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })

      if (data.success) {
        this.data.packages[packageId].enabled = false
        this.data.packages[packageId].update_available = false
        this.data.packages[packageId].update_can_be_installed = false
      }
    },
    async enable (packageId) {
      const { data } = await this.form.post(`/api/admin/add-ons/${packageId}/enable`)

      if (data.success) {
        // refresh the page, so package data is loaded
        this.$router.go()
      } else {
        this.$store.dispatch('message/error', { text: data.message })
      }
    }
  }
}
</script>

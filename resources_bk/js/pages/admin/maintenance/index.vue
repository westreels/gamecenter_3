<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            {{ $t('Maintenance') }}
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('System info') }}
                  </v-card-title>
                  <v-card-text>
                    <v-skeleton-loader type="list-item-three-line" :loading="!dataLoaded">
                      <div>
                        <div v-for="(item, i) in data.system_info" :key="i">
                          {{ item.title }}: {{ item.value }}
                        </div>
                      </div>
                    </v-skeleton-loader>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Application version') }}
                  </v-card-title>
                  <v-card-text>
                    <v-skeleton-loader type="text" :loading="!dataLoaded">
                      <p v-if="data.update_available" class="warning--text mb-0">
                        <v-icon color="warning" left>
                          mdi-alert
                        </v-icon>
                        {{ $t('New version {0} is available ({1} currently installed).', [data.latest_version, data.current_version]) }}
                        <a href="/admin/help/app#q10">{{ $t('How to upgrade?') }}</a>
                      </p>
                      <p v-else class="success--text mb-0">
                        <v-icon color="success" left>
                          mdi-check-all
                        </v-icon>
                        {{ $t('Application is up-to-date (version {0} is installed).', [data.latest_version]) }}
                      </p>
                    </v-skeleton-loader>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Maintenance mode') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                      {{ $t('You can enable maintenance mode during application upgrade or when doing other service tasks, so nobody except administrators can use the website.') }}
                    </p>
                    <v-form v-model="maintenanceFormIsValid" class="mt-5" @submit.prevent="switchMaintenanceMode">
                      <v-text-field
                        v-model="maintenanceForm.message"
                        :label="$t('Message')"
                        :rules="[validationRequired]"
                        :error="maintenanceForm.errors.has('message')"
                        :error-messages="maintenanceForm.errors.get('message')"
                        :disabled="typeof data.maintenance_mode === 'undefined' || data.maintenance_mode"
                        outlined
                      />

                      <v-skeleton-loader type="button" :loading="!dataLoaded">
                        <v-btn type="submit" color="primary" :disabled="!maintenanceFormIsValid || maintenanceForm.busy" :loading="maintenanceForm.busy">
                          <template v-if="data.maintenance_mode">
                            {{ $t('Disable maintenance mode') }}
                          </template>
                          <template v-else>
                            {{ $t('Enable maintenance mode') }}
                          </template>
                        </v-btn>
                      </v-skeleton-loader>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Tasks') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                      {{ $t('The app provides a number of service tasks, which can be executed on demand.') }}
                    </p>
                    <v-form v-model="commandFormIsValid" class="mt-5" @submit.prevent="executeCommand">
                      <v-select
                        v-model="commandForm.command"
                        :items="commands"
                        :label="$t('Task')"
                        :error="commandForm.errors.has('command')"
                        :error-messages="commandForm.errors.get('command')"
                        outlined
                        :disabled="!data.commands"
                      />

                      <template v-if="commandForm.command">
                        <template v-for="command in data.commands">
                          <template v-if="command.class === commandForm.command">
                            <v-text-field
                              v-for="(arg, i) in command.arguments"
                              :key="i"
                              v-model="commandForm.arguments[arg.id]"
                              :label="$t(arg.title)"
                              :rules="[validationRequired]"
                              :placeholder="arg.default"
                              :error="commandForm.errors.has('arguments')"
                              :error-messages="commandForm.errors.get('arguments')"
                              outlined
                            />
                          </template>
                        </template>
                      </template>

                      <v-skeleton-loader type="button" :loading="!dataLoaded">
                        <v-btn type="submit" color="primary" :disabled="!commandForm.command || !commandFormIsValid || commandForm.busy" :loading="commandForm.busy">
                          {{ $t('Execute') }}
                        </v-btn>
                      </v-skeleton-loader>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Database') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                      {{ $t('After upgrading to a new version of the application it is necessary to update the database objects.') }}
                      {{ $t('All current data will be preserved.') }}
                    </p>
                    <v-form @submit.prevent="migrate">
                      <v-skeleton-loader type="button" :loading="!dataLoaded">
                        <v-btn type="submit" color="primary" :disabled="migrationForm.busy" :loading="migrationForm.busy">
                          {{ $t('Update database') }}
                        </v-btn>
                      </v-skeleton-loader>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Cache') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                      {{ $t('The application caches templates, configuration, translation strings, aggregated data etc to improve performance.') }}
                      {{ $t('To clear all caches at once click the button below.') }}
                    </p>
                    <v-form @submit.prevent="clearCache">
                      <v-skeleton-loader type="button" :loading="!dataLoaded">
                        <v-btn type="submit" color="primary" :disabled="cacheForm.busy" :loading="cacheForm.busy">
                          {{ $t('Clear cache') }}
                        </v-btn>
                      </v-skeleton-loader>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Cron') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                      {{ $t('Certain application tasks are supposed to run automatically on a regular basis.') }}
                      {{ $t('To make it work please add the following system cron job.') }}
                      {{ $t('Please note that the command-line (cli) PHP version on your server should also meet the minimum PHP version requirements, otherwise the cron job will fail to execute.') }}
                      {{ $t('If you add the cron job via cPanel you need to omit the leading asterisk symbols.') }}
                      {{ $t('Please refer to the installation guide for more information how to add a cron job.') }}
                    </p>
                    <v-form @submit.prevent="cron">
                      <v-text-field
                        ref="cron"
                        dense
                        outlined
                        readonly
                        append-icon="mdi-content-copy"
                        :value="data.cron_job"
                        :disabled="!dataLoaded"
                        @click:append="copyToClipboard($refs.cron)"
                      />

                      <v-skeleton-loader type="button" :loading="!dataLoaded">
                        <v-btn type="submit" color="primary" :disabled="cronForm.busy" :loading="cronForm.busy">
                          {{ $t('Execute cron job manually') }}
                        </v-btn>
                      </v-skeleton-loader>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Queues') }}
                  </v-card-title>
                  <v-card-text>
                    <v-row>
                      <v-col>
                        <v-btn :to="{ name: 'admin.maintenance.jobs.index' }" color="primary">
                          {{ $t('Jobs') }}
                        </v-btn>
                        <v-btn :to="{ name: 'admin.maintenance.failed-jobs.index' }" color="primary">
                          {{ $t('Failed jobs') }}
                        </v-btn>
                      </v-col>
                    </v-row>
                    <v-form class="mt-5" @submit.prevent="clearQueue">
                      <v-select
                        v-model="clearQueueForm.queue"
                        :items="data.queues"
                        :label="$t('Queue')"
                        outlined
                        :disabled="!data.queues"
                        :hide-details="true"
                      >
                        <template v-slot:append-outer>
                          <v-skeleton-loader type="button" :loading="!dataLoaded">
                            <v-btn type="submit" color="primary" :disabled="!clearQueueForm.queue || clearQueueForm.busy" :loading="clearQueueForm.busy">
                              {{ $t('Delete all jobs from the queue') }}
                            </v-btn>
                          </v-skeleton-loader>
                        </template>
                      </v-select>
                    </v-form>

                    <v-form class="mt-5" @submit.prevent="stopQueueWorker">
                      <v-skeleton-loader type="button" :loading="!dataLoaded">
                        <v-btn type="submit" color="primary" :disabled="stopQueueWorkerForm.busy" :loading="stopQueueWorkerForm.busy">
                          {{ $t('Stop queue worker') }}
                        </v-btn>
                      </v-skeleton-loader>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Log files') }}
                  </v-card-title>
                  <v-card-text>
                    <v-skeleton-loader type="button" :loading="!dataLoaded">
                      <div v-if="data.log_files && data.log_files.length">
                        <v-btn
                          v-for="(file, i) in data.log_files"
                          :key="i"
                          color="primary"
                          tile
                          text
                          :to="{ name: 'admin.maintenance.logs.show', params: { file } }"
                          class="text-lowercase"
                        >
                          <v-icon left>
                            mdi-file
                          </v-icon>
                          {{ file }}.log
                        </v-btn>
                      </div>
                      <div v-else>
                        {{ $t('There are no log files.') }}
                      </div>
                    </v-skeleton-loader>
                  </v-card-text>
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
import { copyToClipboard } from '~/plugins/utils'

export default {
  mixins: [FormMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Maintenance') }
  },

  data () {
    return {
      data: {},
      maintenanceFormIsValid: null,
      maintenanceForm: new Form({
        message: this.$t('Sorry, we are doing some maintenance. Please check back soon.')
      }),
      commandFormIsValid: null,
      commandForm: new Form({
        command: null,
        arguments: {}
      }),
      migrationForm: new Form(),
      cacheForm: new Form(),
      cronForm: new Form(),
      clearQueueForm: new Form({
        queue: null
      }),
      stopQueueWorkerForm: new Form()
    }
  },

  computed: {
    commands () {
      return this.data.commands
        ? this.data.commands.map(command => ({
          value: command.class,
          text: command.description
        }))
        : []
    },
    dataLoaded () {
      return this.data && Object.keys(this.data).length > 0
    }
  },

  async created () {
    const { data } = await axios.get('/api/admin/maintenance')

    this.data = data
  },

  methods: {
    async switchMaintenanceMode () {
      const action = this.data.maintenance_mode ? 'up' : 'down'

      const { data } = await this.maintenanceForm.post(`/api/admin/maintenance/${action}`)

      this.data.maintenance_mode = !this.data.maintenance_mode

      this.$store.dispatch('message/success', { text: data.message })
    },
    async executeCommand () {
      const { data } = await this.commandForm.post('/api/admin/maintenance/command')

      this.$store.dispatch('message/success', { text: data.message })
    },
    async migrate () {
      const { data } = await this.migrationForm.post('/api/admin/maintenance/migrate')

      this.$store.dispatch('message/success', { text: data.message })
    },
    async clearCache () {
      const { data } = await this.cacheForm.post('/api/admin/maintenance/cache')

      this.$store.dispatch('message/success', { text: data.message })
    },
    async cron () {
      const { data } = await this.cronForm.post('/api/admin/maintenance/cron')

      this.$store.dispatch('message/success', { text: data.message })
    },
    async clearQueue () {
      const { data } = await this.clearQueueForm.post('/api/admin/maintenance/queues/clear')

      this.$store.dispatch('message/success', { text: data.message })
    },
    async stopQueueWorker () {
      const { data } = await this.stopQueueWorkerForm.post('/api/admin/maintenance/queues/stop')

      this.$store.dispatch('message/success', { text: data.message })
    },
    copyToClipboard (ref) {
      return copyToClipboard(ref.$el.querySelector('input'))
    }
  }
}
</script>

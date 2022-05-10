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
              {{ $t('Create raffle') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="create">
              <v-text-field
                v-model="form.title"
                :label="$t('Title')"
                :disabled="form.busy"
                :rules="[validationRequired]"
                :error="form.errors.has('title')"
                :error-messages="form.errors.get('title')"
                outlined
                @keydown="clearFormErrors($event,'title')"
              />

              <v-textarea
                v-model="form.description"
                :label="$t('Description')"
                :disabled="form.busy"
                :error="form.errors.has('description')"
                :error-messages="form.errors.get('description')"
                outlined
                @keydown="clearFormErrors($event,'description')"
              />

              <file-upload
                v-model="form.banner"
                :label="$t('Banner')"
                name="banner"
                folder="raffles"
                :clearable="true"
              />

              <v-text-field
                v-model.number="form.ticket_price"
                :label="$t('Ticket price')"
                :disabled="form.busy"
                :rules="[validationPositiveInteger]"
                :error="form.errors.has('ticket_price')"
                :error-messages="form.errors.get('ticket_price')"
                :suffix="$t('credits')"
                outlined
                @keydown="clearFormErrors($event,'ticket_price')"
              />

              <v-text-field
                v-model.number="form.max_tickets_per_user"
                :label="$t('Max tickets per user')"
                :disabled="form.busy"
                :rules="[validationNonNegativeInteger]"
                :error="form.errors.has('max_tickets_per_user')"
                :error-messages="form.errors.get('max_tickets_per_user')"
                outlined
                @keydown="clearFormErrors($event,'max_tickets_per_user')"
              />

              <v-text-field
                v-model.number="form.total_tickets"
                :label="$t('Total number of tickets')"
                :disabled="form.busy"
                :rules="[validationPositiveInteger]"
                :error="form.errors.has('total_tickets')"
                :error-messages="form.errors.get('total_tickets')"
                outlined
                @keydown="clearFormErrors($event,'total_tickets')"
              />

              <v-text-field
                v-model.number="form.fee"
                :label="$t('Fee')"
                :disabled="form.busy"
                :rules="[validationNonNegativeNumber]"
                :error="form.errors.has('fee')"
                :error-messages="form.errors.get('fee')"
                suffix="%"
                :hint="$t('Percentage of all tickets value that will be withheld.')"
                persistent-hint
                outlined
                @keydown="clearFormErrors($event,'fee')"
              />

              <v-radio-group
                v-model="form.completion_trigger"
                :label="$t('Raffle ends')"
                :mandatory="true"
                :disabled="form.busy"
              >
                <v-radio :label="$t('After specific time')" :value="1" />
                <v-radio :label="$t('After all tickets are sold')" :value="2" />
              </v-radio-group>

              <v-select
                v-if="form.completion_trigger === 1"
                v-model="form.duration"
                :label="$t('Duration')"
                :disabled="form.busy"
                :items="durations"
                :rules="[validationRequired]"
                :error="form.errors.has('duration')"
                :error-messages="form.errors.get('duration')"
                outlined
              />

              <v-switch
                v-model="form.recurring"
                :label="$t('Recurring')"
                color="primary"
                :disabled="form.busy"
                :false-value="false"
                :true-value="true"
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
import FileUpload from '~/components/Admin/FileUpload'

export default {
  components: { FileUpload },
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  mixins: [FormMixin],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Create raffle') }
  },

  data () {
    return {
      form: new Form({
        title: null,
        description: null,
        banner: null,
        ticket_price: null,
        max_tickets_per_user: null,
        total_tickets: null,
        fee: null,
        completion_trigger: 1,
        duration: null,
        recurring: false
      }),
      durations: [
        { text: this.$t('5 minutes'), value: 300 },
        { text: this.$t('10 minutes'), value: 600 },
        { text: this.$t('15 minutes'), value: 900 },
        { text: this.$t('30 minutes'), value: 1800 },
        { text: this.$t('1 hour'), value: 3600 },
        { text: this.$t('2 hours'), value: 7200 },
        { text: this.$t('3 hours'), value: 10800 },
        { text: this.$t('4 hours'), value: 14400 },
        { text: this.$t('5 hours'), value: 18000 },
        { text: this.$t('6 hours'), value: 21600 },
        { text: this.$t('10 hours'), value: 36000 },
        { text: this.$t('12 hours'), value: 43200 },
        { text: this.$t('1 day'), value: 86400 },
        { text: this.$t('2 days'), value: 172800 },
        { text: this.$t('5 days'), value: 432000 },
        { text: this.$t('1 week'), value: 604800 },
        { text: this.$t('2 weeks'), value: 1209600 },
        { text: this.$t('1 month'), value: 2592000 },
        { text: this.$t('2 months'), value: 5184000 },
        { text: this.$t('6 months'), value: 15552000 }
      ]
    }
  },

  methods: {
    async create () {
      await this.form.post('/api/admin/raffles')

      this.$store.dispatch('message/success', { text: this.$t('Raffle successfully created.') })

      this.$router.push({ name: 'admin.raffles.index' })
    }
  }
}
</script>

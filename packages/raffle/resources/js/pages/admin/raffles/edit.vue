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
              {{ $t('Raffle {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <raffle-menu :id="id" />
            <preloader :active="!raffle" />
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="update">
              <v-text-field
                v-model="form.title"
                :label="$t('Title')"
                :disabled="form.busy || !raffle || raffle.is_completed"
                :rules="[validationRequired]"
                :error="form.errors.has('title')"
                :error-messages="form.errors.get('title')"
                outlined
                @keydown="clearFormErrors($event,'title')"
              />

              <v-textarea
                v-model="form.description"
                :label="$t('Description')"
                :disabled="form.busy || !raffle || raffle.is_completed"
                :error="form.errors.has('description')"
                :error-messages="form.errors.get('description')"
                outlined
                @keydown="clearFormErrors($event,'description')"
              />

              <file-upload
                v-model="form.banner"
                :label="$t('Banner')"
                :disabled="form.busy || !raffle || raffle.is_completed"
                name="banner"
                folder="raffles"
                :clearable="true"
              />

              <v-text-field
                v-model.number="form.ticket_price"
                :label="$t('Ticket price')"
                :disabled="form.busy || !raffle || raffle.is_completed"
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
                :disabled="form.busy || !raffle || raffle.is_completed"
                :rules="[validationNonNegativeInteger]"
                :error="form.errors.has('max_tickets_per_user')"
                :error-messages="form.errors.get('max_tickets_per_user')"
                outlined
                @keydown="clearFormErrors($event,'max_tickets_per_user')"
              />

              <v-text-field
                v-model.number="form.total_tickets"
                :label="$t('Total number of tickets')"
                :disabled="form.busy || !raffle || raffle.is_completed"
                :rules="[validationPositiveInteger]"
                :error="form.errors.has('total_tickets')"
                :error-messages="form.errors.get('total_tickets')"
                outlined
                @keydown="clearFormErrors($event,'total_tickets')"
              />

              <v-text-field
                v-model.number="form.fee"
                :label="$t('Fee')"
                :disabled="form.busy || !raffle || raffle.is_completed"
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
                v-if="raffle"
                :label="$t('Raffle ends')"
                :mandatory="true"
                :disabled="true"
              >
                <v-radio :label="$t('At {0}', [raffle.end_date])" :value="1" />
                <v-radio :label="$t('After all tickets are sold')" :value="2" />
              </v-radio-group>

              <v-switch
                v-model="form.recurring"
                :label="$t('Recurring')"
                color="primary"
                :disabled="form.busy || !raffle || raffle.is_completed"
                :false-value="false"
                :true-value="true"
              />

              <v-skeleton-loader type="button" :loading="!raffle">
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
import Preloader from '~/components/Preloader'
import RaffleMenu from 'packages/raffle/resources/js/components/Admin/RaffleMenu'
import FileUpload from '~/components/Admin/FileUpload'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { RaffleMenu, Preloader, FileUpload },

  mixins: [FormMixin],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Raffle {0}', [this.id]) }
  },

  data () {
    return {
      raffle: null,
      form: new Form({
        title: null,
        description: null,
        banner: null,
        ticket_price: null,
        max_tickets_per_user: null,
        total_tickets: null,
        fee: null,
        recurring: false
      })
    }
  },

  computed: {
    changed () {
      return this.form.keys().some(key => this.raffle && this.form[key] !== this.raffle[key])
    }
  },

  async created () {
    const { data } = await axios.get(`/api/admin/raffles/${this.id}`)

    this.raffle = data

    // Fill the form with raffle data
    this.form.keys().forEach(key => {
      this.form[key] = this.raffle[key]
    })
  },

  methods: {
    async update () {
      await this.form.patch(`/api/admin/raffles/${this.id}`)

      this.$store.dispatch('message/success', { text: this.$t('Raffle successfully updated.') })

      this.$router.push({ name: 'admin.raffles.index' })
    }
  }
}
</script>

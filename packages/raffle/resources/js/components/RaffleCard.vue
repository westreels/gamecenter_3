<template>
  <v-card elevation="5">
    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline py-2">
          {{ raffle.title }}
        </v-list-item-title>
      </v-list-item-content>
    </v-list-item>
    <v-hover v-slot:default="{ hover }" v-model="displayForm" close-delay="3000">
      <v-img :src="raffle.banner || ''" height="300" :alt="raffle.title" :title="raffle.title" dark>
        <template v-slot:placeholder>
          <v-skeleton-loader type="image" />
        </template>
        <v-expand-transition>
          <div v-if="hover" class="fill-height secondary darken-2 transition-fast-in-fast-out v-card--reveal">
            <v-form
              v-model="formIsValid"
              class="d-flex fill-height justify-center align-center"
              @submit.prevent="purchase"
            >
              <div class="text-center width-90">
                <template v-if="maxTickets > 0">
                  <template v-if="raffle.max_tickets_per_user > 0">
                    <h6 class="text-h6 font-weight-thin">
                      {{ $t('Tickets bought / limit') }}
                    </h6>
                    <v-progress-linear
                      :value="100 * raffle.user_tickets_count / raffle.max_tickets_per_user"
                      color="primary"
                      height="20"
                      striped
                      rounded
                      class="mt-4 mb-6"
                    >
                      <template>
                        <strong>{{ raffle.user_tickets_count }} / {{ raffle.max_tickets_per_user }}</strong>
                      </template>
                    </v-progress-linear>
                  </template>
                  <v-text-field
                    v-model.number="form.quantity"
                    :label="$t('Number of tickets')"
                    :disabled="form.busy"
                    :rules="[validationPositiveInteger, v => validationMax(v, maxTickets)]"
                    :error="form.errors.has('quantity')"
                    :error-messages="form.errors.get('quantity')"
                    :success-messages="formSuccessMessages"
                    outlined
                    :hint="$t('1 ticket costs {0} credits', [raffle.ticket_price])"
                    :persistent-hint="true"
                    :suffix="quantityInputSuffix"
                    @keydown="clearFormErrors($event,'quantity')"
                  />
                  <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">
                    {{ $t('Buy') }}
                  </v-btn>
                </template>
                <h6 v-else class="text-h6">
                  {{ $t('You can not buy tickets.') }}
                </h6>
              </div>
            </v-form>
          </div>
        </v-expand-transition>
      </v-img>
    </v-hover>
    <v-card-text v-if="raffle.description">
      {{ raffle.description }}
    </v-card-text>
    <v-card-text class="text-center">
      <h6 class="text-h6 font-weight-thin">
        {{ $t('Tickets sold / total') }}
      </h6>
      <v-progress-linear
        :value="soldTicketsPct"
        color="primary"
        height="20"
        striped
        rounded
        class="my-4"
      >
        <template>
          <strong>{{ soldTickets }} / {{ totalTickets }}</strong>
        </template>
      </v-progress-linear>
      <h6 class="text-h6 font-weight-thin">
        {{ $t('Current / max pot size') }}
      </h6>
      <v-progress-linear
        :value="potSizePct"
        color="primary"
        height="20"
        striped
        rounded
        class="my-4"
      >
        <template>
          <strong>{{ integer(raffle.pot_size) }} / {{ integer(raffle.max_pot_size) }} {{ $t('credits') }}</strong>
        </template>
      </v-progress-linear>
    </v-card-text>
    <v-divider class="mt-2 mb-2 mx-4" />
    <v-card-text>
      <v-row class="align-center">
        <v-col cols="12" sm="6" lg="8" class="text-center text-sm-left">
          <v-icon>mdi-clock-outline</v-icon>
          <span v-if="raffle.end_date_unix">
            {{ $t('Raffle will end in') }}
            <countdown-timer :end-date="raffle.end_date_unix" class="text-no-wrap" />
          </span>
          <span v-else>
            {{ $t('Raffle will end when all tickets are sold') }}
          </span>
        </v-col>
        <v-col cols="12" sm="6" lg="4" class="text-center text-sm-right">
          <v-btn color="primary" @click="displayForm = !displayForm">
            {{ $t('Buy tickets') }}
          </v-btn>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import CountdownTimer from '~/components/CountdownTimer'
import { integer } from '~/plugins/format'

export default {
  components: { CountdownTimer },
  mixins: [FormMixin],

  props: {
    raffle: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      displayForm: false,
      formSuccessMessages: [], // should be an array
      form: new Form({
        quantity: null
      })
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    maxTickets () {
      const maxTicketsPerUser = this.raffle.max_tickets_per_user || this.raffle.total_tickets

      return Math.min(
        this.raffle.total_tickets - this.raffle.tickets_count, // number of tickets left in the raffle
        maxTicketsPerUser - this.raffle.user_tickets_count, // number of tickets allowed for a user
        Math.floor(this.account.balance / this.raffle.ticket_price) // number of tickets user can buy with their balance
      )
    },
    quantityInputSuffix () {
      return this.form.quantity > 0
        ? `x ${this.raffle.ticket_price} = ${this.raffle.ticket_price * this.form.quantity} ${this.$t('credits')}`
        : ''
    },
    soldTickets () {
      return integer(this.raffle.tickets_count)
    },
    soldTicketsPct () {
      return 100 * this.raffle.tickets_count / this.raffle.total_tickets
    },
    totalTickets () {
      return integer(this.raffle.total_tickets)
    },
    potSizePct () {
      return 100 * this.raffle.tickets_count / this.raffle.total_tickets
    }
  },

  watch: {
    displayForm (currentlyDisplayed, wasDisplayed) {
      if (wasDisplayed && !currentlyDisplayed) {
        this.form.reset()
      }
    }
  },

  methods: {
    integer,
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance'
    }),
    async purchase () {
      const quantity = this.form.quantity

      const { data } = await this.form.post(`/api/raffles/${this.raffle.id}`)

      this.formSuccessMessages.push(this.$t('You purchased {0} tickets', [quantity]))

      this.updateUserAccountBalance(data.account.balance)

      this.$emit('update', { raffle: data.raffle })

      setTimeout(() => {
        this.formSuccessMessages = []
      }, 5000)
    }
  }
}
</script>
<style lang="scss" scoped>
.width-90 {
  width: 90%;
}
</style>

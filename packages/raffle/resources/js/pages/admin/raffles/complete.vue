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
            <v-skeleton-loader type="text" :loading="!raffle" class="my-3">
              <p v-if="get(raffle, 'is_in_progress')">
                {{ $t('Are you sure you want to complete this raffle ahead of schedule?') }}
              </p>
              <p v-else>
                {{ $t('This raffle is already completed.') }}
              </p>
            </v-skeleton-loader>
            <template>
              <v-form @submit.prevent="complete">
                <v-skeleton-loader type="button" :loading="!raffle">
                  <v-btn type="submit" color="primary" :disabled="form.busy || !get(raffle, 'is_in_progress')" :loading="form.busy">
                    {{ $t('Complete') }}
                  </v-btn>
                </v-skeleton-loader>
              </v-form>
            </template>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import { get } from '~/plugins/utils'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import Preloader from '~/components/Preloader'
import RaffleMenu from 'packages/raffle/resources/js/components/Admin/RaffleMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { RaffleMenu, Preloader },

  mixins: [FormMixin],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Raffle {0}', [this.id]) }
  },

  data () {
    return {
      raffle: null,
      form: new Form()
    }
  },

  async created () {
    const { data } = await axios.get(`/api/admin/raffles/${this.id}`)

    this.raffle = data
  },

  methods: {
    get,
    async complete () {
      await this.form.post(`/api/admin/raffles/${this.id}/complete`)

      this.$store.dispatch('message/success', { text: this.$t('Raffle successfully completed.') })

      this.$router.push({ name: 'admin.raffles.index' })
    }
  }
}
</script>

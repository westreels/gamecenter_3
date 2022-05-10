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
              {{ $t('Chat room {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <chat-room-menu :id="id" />
            <preloader :active="!room"></preloader>
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="update">
              <v-text-field
                v-model="form.name"
                :label="$t('Name')"
                type="text"
                :disabled="form.busy || !room"
                :rules="[validationRequired]"
                :error="form.errors.has('name')"
                :error-messages="form.errors.get('name')"
                outlined
                @keydown="clearFormErrors($event,'name')"
              />

              <v-switch
                v-model="form.enabled"
                :label="$t('Enabled')"
                color="primary"
                :disabled="form.busy || !room"
                :false-value="0"
                :true-value="1"
              />

              <v-skeleton-loader type="button" :loading="!room">
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
import ChatRoomMenu from '~/components/Admin/ChatRoomMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { ChatRoomMenu, Preloader },

  mixins: [FormMixin],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Chat room {0}', [this.id]) }
  },

  data () {
    return {
      room: null,
      form: new Form({
        name: null,
        enabled: null
      })
    }
  },

  computed: {
    changed () {
      return this.form.keys().some(key => this.room && this.form[key] !== this.room[key])
    }
  },

  async created () {
    const { data } = await axios.get(`/api/admin/chat/rooms/${this.id}`)

    this.room = data

    // Fill the form with room data
    this.form.keys().forEach(key => {
      this.form[key] = this.room[key]
    })
  },

  methods: {
    async update () {
      await this.form.patch(`/api/admin/chat/rooms/${this.id}`)

      this.$store.dispatch('message/success', { text: this.$t('Chat room successfully updated.') })

      this.$router.push({ name: 'admin.chat.rooms.index' })
    }
  }
}
</script>

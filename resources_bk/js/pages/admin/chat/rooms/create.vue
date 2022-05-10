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
              {{ $t('Create chat room') }}
            </v-toolbar-title>
            <v-spacer />
            <chat-room-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <v-form v-model="formIsValid" @submit.prevent="create">
              <v-text-field
                v-model="form.name"
                :label="$t('Name')"
                :disabled="form.busy"
                :rules="[validationRequired]"
                :error="form.errors.has('name')"
                :error-messages="form.errors.get('name')"
                outlined
                @keydown="clearFormErrors($event,'name')"
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
import ChatRoomMenu from '~/components/Admin/ChatRoomMenu'

export default {
  components: { ChatRoomMenu },
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  mixins: [FormMixin],

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Create chat room', [this.id]) }
  },

  data () {
    return {
      form: new Form({
        name: null
      })
    }
  },

  methods: {
    async create () {
      await this.form.post('/api/admin/chat/rooms')

      this.$store.dispatch('message/success', { text: this.$t('Chat room successfully created.') })

      this.$router.push({ name: 'admin.chat.rooms.index' })
    }
  }
}
</script>

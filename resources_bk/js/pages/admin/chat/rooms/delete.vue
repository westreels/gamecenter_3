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
          </v-toolbar>
          <v-card-text>
            <p>
              {{ $t('Are you sure you want to delete this chat room and all related messages?') }}
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
import ChatRoomMenu from '~/components/Admin/ChatRoomMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { ChatRoomMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Chat room {0}', [this.id]) }
  },

  data () {
    return {
      form: new Form()
    }
  },

  methods: {
    async destroy () {
      await this.form.delete(`/api/admin/chat/rooms/${this.id}`)

      this.$store.dispatch('message/success', { text: this.$t('Chat room successfully deleted.') })

      this.$router.push({ name: 'admin.chat.rooms.index' })
    }
  }
}
</script>

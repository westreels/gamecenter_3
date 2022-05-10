<template>
  <v-navigation-drawer
    id="chat"
    :value="value"
    app
    right
    width="300"
    @input="changeDrawerState"
  >
    <div class="px-2 py-4">
      <h4 class="title">
        {{ $t('Chat') }}
        <span class="float-right">
          <v-menu v-if="rooms.length > 1" v-model="roomMenu" :close-on-content-click="false" offset-y left>
            <template v-slot:activator="{ on }">
              <v-btn icon v-on="on">
                <v-icon>mdi-filter-variant</v-icon>
              </v-btn>
            </template>
            <v-card>
              <v-subheader class="text-uppercase">
                {{ $t('Rooms') }}
              </v-subheader>
              <v-select
                v-model="room"
                :items="rooms"
                item-text="name"
                item-value="id"
                class="px-5"
                @change="roomMenu = false"
              />
            </v-card>
          </v-menu>
          <span class="font-weight-regular">
            <v-icon>mdi-account-multiple</v-icon>
            {{ usersCount }}
          </span>
        </span>
      </h4>
    </div>

    <div ref="messages" class="chat-messages-container overflow-y-auto" :class="`theme--${$vuetify.theme.isDark ? 'dark' : 'light'}`">
      <v-list two-line class="pa-0">
        <transition-group name="slide-x-transition" tag="div">
          <v-hover v-slot:default="{ hover }" v-for="(msg, index) in messages" :key="`message-${index}`">
            <v-list-item :class="{ 'grey darken-2': hover }">
              <v-list-item-avatar size="50">
                <user-avatar :user="msg.user" />
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title class="subtitle-2">
                  <user-profile-modal :user="msg.user">
                    <template v-slot="{ on }">
                      <span class="text--primary link" v-on="on">
                        {{ msg.user.name }}
                      </span>
                    </template>
                  </user-profile-modal>
                  <v-btn v-if="user.id !== msg.user.id" v-show="hover" icon x-small class="float-right" @click="addRecipient(msg.user)">
                    <v-icon small>
                      mdi-reply
                    </v-icon>
                  </v-btn>
                </v-list-item-title>
                <p class="text--secondary mb-0 chat-message">
                  <template v-if="msg.recipients">
                    <user-profile-modal
                      v-for="recipient in msg.recipients"
                      :key="`recipient-${recipient.name}`"
                      :user="recipient"
                    >
                      <template v-slot="{ on }">
                        <v-chip label link small class="mb-1 mr-1" v-on="on">
                          <v-icon left small>
                            mdi-account
                          </v-icon>
                          {{ recipient.name }}
                        </v-chip>
                      </template>
                    </user-profile-modal>
                  </template>
                  {{ msg.message }}
                </p>
              </v-list-item-content>
            </v-list-item>
          </v-hover>
        </transition-group>
      </v-list>
    </div>

    <template v-slot:append>
      <v-form v-model="formIsValid" class="pa-2" @submit.prevent="sendMessage">
        <v-text-field
          v-model="form.message"
          dense
          flat
          full-width
          :placeholder="$t('Message')"
          append-outer-icon="mdi-send"
          :readonly="form.busy || !echo"
          :loading="form.busy"
          :counter="maxLength"
          :rules="[v => validationMaxLength(v, maxLength)]"
          :error="form.errors.has('message')"
          :error-messages="form.errors.get('message')"
        >
          <template v-slot:prepend-inner>
            <v-chip
              v-for="(recipient, index) in recipients"
              :key="`user-${recipient.name}`"
              label
              pill
              small
              :close="true"
              @click:close="removeRecipient(index)"
            >
              {{ recipient.name }}
            </v-chip>
          </template>
          <template v-slot:append-outer>
            <v-btn type="submit" icon :disabled="form.busy || !room || !form.message || !formIsValid">
              <v-icon>mdi-send</v-icon>
            </v-btn>
          </template>
        </v-text-field>
      </v-form>
    </template>
  </v-navigation-drawer>
</template>
<script>
import axios from 'axios'
import Form from 'vform'
import { config } from '~/plugins/config'
import { mapState } from 'vuex'
import FormMixin from '~/mixins/Form'
import SoundMixin from '~/mixins/Sound'
import UserProfileModal from '~/components/UserProfileModal'
import messageSound from '~/../audio/chat/message.wav'
import UserAvatar from './UserAvatar'

export default {
  components: { UserAvatar, UserProfileModal },

  mixins: [FormMixin, SoundMixin],

  props: ['value'],

  data () {
    return {
      roomMenu: false,
      rooms: [],
      room: null,
      usersCount: 0,
      messages: [],
      recipients: [],
      unreadMessagesCount: 0,
      form: new Form({
        message: '',
        recipients: []
      })
    }
  },

  computed: {
    ...mapState('auth', ['user']),
    ...mapState('broadcasting', ['echo']),
    maxLength () {
      return config('settings.interface.chat.message_max_length')
    }
  },

  watch: {
    value (newValue, oldValue) {
      // if chat drawer was closed and now open
      if (!oldValue && newValue) {
        // clear unread messages notification
        setTimeout(() => {
          this.setUnreadMessagesCount(0)
        }, 1000)
      }
    },
    room (newRoom, oldRoom) {
      if (oldRoom && newRoom) {
        this.leaveRoom(oldRoom)
        this.joinRoom(newRoom)
        this.fetchMessages(newRoom)
      }
    }
  },

  async created () {
    await this.fetchRooms()
    this.fetchMessages(this.room)
    this.joinRoom(this.room)
  },

  destroyed () {
    this.leaveRoom()
  },

  methods: {
    async fetchRooms () {
      const { data } = await axios.get('/api/chat/rooms')

      this.rooms = data

      if (this.rooms.length) {
        this.room = this.rooms[0].id
      }
    },

    async fetchMessages (room) {
      if (!room) {
        return false
      }

      const { data } = await axios.get(`/api/chat/${room}`)

      this.messages = data

      this.scrollToBottom()
    },

    addMessage (message) {
      this.messages.push(message)
      this.scrollToBottom()

      // check if current user is amongst recipients of the message
      if (message.recipients.length) {
        message.recipients.forEach(recipient => {
          if (recipient.id === this.user.id) {
            // play a sound to notify user
            this.sound(messageSound)

            // if the chat window is closed increase the number of unread messages
            if (!this.value) {
              this.setUnreadMessagesCount(this.unreadMessagesCount + 1)
            }
          }
        })
      }
    },

    addRecipient (user) {
      if (this.recipients.length === 0) {
        this.recipients.push(user)
      }
    },

    removeRecipient (index) {
      this.recipients.splice(index, 1)
    },

    setUnreadMessagesCount (count) {
      this.unreadMessagesCount = count
      this.$emit('message', this.unreadMessagesCount)
    },

    changeDrawerState (isOpen) {
      // parent needs to be notified via event to update its state,
      // when the drawer is closed by clicking outside of it (on mobiles)
      if (this.value !== isOpen) {
        this.$emit('input', isOpen)
      }
    },

    joinRoom (room) {
      if (!this.echo || !room) {
        return false
      }

      this.echo.join(`chat.${room}`)
      // currently joined users
        .here(users => {
          this.usersCount = users.length
        })
        // new user joined
        .joining(user => {
          this.usersCount++
        })
        // user left
        .leaving(user => {
          this.usersCount--
        })
        // new message
        .listen('ChatMessageSent', message => this.addMessage(message))
    },

    leaveRoom (room) {
      if (!this.echo || !room) {
        return false
      }

      this.echo.leave(`chat.${room}`)
    },

    async sendMessage () {
      if (this.recipients.length) {
        this.form.recipients = this.recipients.map(user => user.id)
      }

      await this.form.post(`/api/chat/${this.room}`)

      this.form.message = ''
      this.recipients = []
      this.form.recipients = []
    },

    // automatically scroll messages area to bottom
    scrollToBottom () {
      this.$nextTick(() => {
        // https://stackoverflow.com/a/270628/2767324
        this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.chat-message {
  line-height: 1.6em;
}
</style>

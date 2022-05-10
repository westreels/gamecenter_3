<template>
  <div :class="{ 'd-flex justify-center fill-height align-center': !room }">
    <v-system-bar
      v-if="room"
      color="primary"
      height="35"
    >
      <v-icon>mdi-map-marker</v-icon>
      <span>{{ room.name }}</span>
      <v-icon class="ml-2">mdi-cash</v-icon>
      <span>{{ room.parameters.bet }}</span>
      <v-icon class="ml-2">mdi-account-multiple</v-icon>
      <span>{{ $t('{0}/{1}', [playersCount, room.parameters.players_count]) }}</span>
      <v-spacer />
      <v-btn icon small :disabled="forms.joinOrLeave.busy || playing" @click="leaveRoom">
        <v-icon>mdi-logout-variant</v-icon>
      </v-btn>
    </v-system-bar>
    <template v-else>
      <v-container v-if="rooms" fluid class="align-self-start">
        <v-row align="center" justify="center">
          <v-col cols="12" md="8">
            <v-card>
              <v-toolbar>
                <v-toolbar-title>
                  {{ $t('Game rooms') }}
                </v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-row>
                  <v-col cols="12" md="6" class="pr-md-5">
                    <h2 class="headline mb-5">
                      {{ $t('Join an existing room') }}
                    </h2>

                    <template v-if="rooms.length">
                      <v-form @submit.prevent="joinRoom">
                        <v-select
                          v-model="forms.joinOrLeave.room_id"
                          :items="rooms"
                          item-value="id"
                          :item-text="room => room.name + ' - ' + $t('{0}/{1} players', [room.players_count, room.parameters.players_count])"
                          :error="forms.joinOrLeave.errors.has('room_id')"
                          :error-messages="forms.joinOrLeave.errors.get('room_id')"
                          :label="$t('Game room')"
                          outlined
                          :disabled="forms.joinOrLeave.busy"
                        />

                        <v-btn type="submit" color="primary" :disabled="!forms.joinOrLeave.room_id || forms.joinOrLeave.busy" :loading="forms.joinOrLeave.busy">
                          {{ $t('Join') }}
                        </v-btn>
                      </v-form>
                    </template>
                    <p v-else>
                      {{ $t('There are no rooms available to join') }}
                    </p>
                  </v-col>
                  <v-col cols="12" md="6" class="pl-md-5 border-left">
                    <h2 class="headline mb-5">
                      {{ $t('Create a new room') }}
                    </h2>

                    <v-form @submit.prevent="createRoom">
                      <v-text-field
                        v-model="forms.create.name"
                        :label="$t('Name')"
                        :rules="[validationRequired, v => validationMinLength(v, 3), v => validationMaxLength(v, 50)]"
                        :error="forms.create.errors.has('name')"
                        :error-messages="forms.create.errors.get('name')"
                        outlined
                        :disabled="forms.create.busy"
                        @keydown="clearFormErrors($event, 'name', forms.create)"
                      />

                      <form-parameter
                        v-for="parameter in parameters"
                        :key="parameter.id"
                        v-model="forms.create.parameters[parameter.id]"
                        :parameter="parameter"
                        :form="forms.create"
                        form-key="parameters"
                        :disabled="forms.create.busy"
                      />

                      <v-btn type="submit" color="primary" :disabled="!forms.create.name || forms.create.busy" :loading="forms.create.busy">
                        {{ $t('Create') }}
                      </v-btn>
                    </v-form>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
      <block-preloader v-else />
    </template>
  </div>
</template>

<script>
import { config } from '~/plugins/config'
import axios from 'axios'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import SoundMixin from '~/mixins/Sound'
import FormParameter from '~/components/FormParameter'
import { mapState } from 'vuex'
import UserJoinedSound from '~/../audio/common/user-joined.wav'
import UserLeftSound from '~/../audio/common/user-left.wav'
import BlockPreloader from '~/components/BlockPreloader'

export default {
  components: { BlockPreloader, FormParameter },

  mixins: [FormMixin, SoundMixin],

  props: {
    playing: {
      type: Boolean,
      required: true
    }
  },

  data () {
    return {
      room: null,
      rooms: null,
      players: null,
      game: null,
      forms: {
        joinOrLeave: new Form({
          room_id: null
        }),
        create: new Form({
          name: null,
          parameters: {}
        })
      }
    }
  },

  computed: {
    ...mapState('broadcasting', ['echo']),
    gamePackageId () {
      return this.$route.params.game
    },
    parameters () {
      return config(`${this.gamePackageId}.parameters`)
    },
    playersCount () {
      return this.players ? this.players.length : 0
    }
  },

  watch: {
    room (room, roomBefore) {
      // join a room
      if (room && !roomBefore) {
        this.subscribe(room)
      // leave a room
      } else if (!room && roomBefore) {
        this.unsubscribe(roomBefore)
        this.fetchRooms()
        this.$emit('exit')
      }

      this.$emit('room', { room })
    },
    game (game, gameBefore) {
      if (game && !gameBefore) {
        this.$emit('game', { game })
      }
    },
    playersCount (playersCount) {
      if (playersCount === parseInt(this.room.parameters.players_count, 10)) {
        this.$emit('ready', { ready: true })
      } else {
        this.$emit('ready', { ready: false })
      }
    }
  },

  created () {
    // it's important to wait for next tick,
    // because the game component can be initialized from beforeRouteUpdate() hook,
    // when the route parameters are not yet updated
    this.$nextTick(() => {
      this.fetchRooms()

      if (this.parameters) {
        this.parameters.forEach(parameter => {
          this.forms.create.parameters[parameter.id] = parameter.default
        })
      }
    })
  },

  beforeDestroy () {
    // note that there is no access to this.$route (and hence gamePackageId computed property) in this hook
    this.unsubscribe(this.room)
    this.room = null
    this.rooms = null
    this.players = null
  },

  methods: {
    async fetchRooms () {
      const { data } = await axios.get(`/api/games/${this.gamePackageId}/rooms`)

      if (data.room) {
        this.room = data.room
        this.forms.joinOrLeave.room_id = data.room.id

        if (data.game) {
          this.game = data.game
        }
      } else {
        this.rooms = data.rooms
      }
    },
    async joinRoom () {
      const { data } = await this.forms.joinOrLeave.post(`/api/games/${this.gamePackageId}/rooms/join`)

      if (data.success) {
        this.room = data.room
        this.rooms = null // clear rooms list, so it needs to be fetched again when the player leaves the room
      }
    },
    async leaveRoom () {
      const { data } = await this.forms.joinOrLeave.post(`/api/games/${this.gamePackageId}/rooms/leave`)

      if (data.success) {
        this.room = null
      }
    },
    async createRoom () {
      const { data } = await this.forms.create.post(`/api/games/${this.gamePackageId}/rooms`)

      this.room = data.room
      this.forms.joinOrLeave.room_id = data.room.id
    },
    subscribe (room) {
      if (!this.echo || !room) {
        return false
      }

      this.echo.join(`game.${room.id}`)
      // currently joined players
        .here(players => {
          this.players = players
          this.$emit('players', { players })
        })
        // new player joined
        .joining(player => {
          this.players.push(player)
          this.$emit('player-joined', { player })
          this.sound(UserJoinedSound)
        })
        // player left
        .leaving(player => {
          this.players.splice(this.players.findIndex(item => item.id === player.id), 1)
          this.$emit('player-left', { player })
          this.sound(UserLeftSound)
        })
        // game event
        .listen('MultiroomMultiplayerGameAction', event => {
          this.$emit('event', { event })
        })
    },
    unsubscribe (room) {
      if (!this.echo || !room) {
        return false
      }

      this.echo.leave(`game.${room.id}`)
    }
  }
}
</script>
<style lang="scss" scoped>
  @import '~vuetify/src/styles/settings/_variables';

  @media #{map-get($display-breakpoints, 'md-and-up')} {
    .border-left {
      border-left: 1px solid grey;
    }
  }
</style>

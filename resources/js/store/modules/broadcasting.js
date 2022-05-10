import { get } from '~/plugins/utils'
import axios from 'axios'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js' // used by Echo, needs to be explicitly imported

// we can't use config store since it's not yet initialized, so using global window variable instead
const key = get(window, 'config.broadcasting.connections.pusher.key')
const cluster = get(window, 'config.broadcasting.connections.pusher.options.cluster')

const echo = key && cluster
  ? new Echo({
    broadcaster: 'pusher',
    key,
    cluster,
    encrypted: true,
    authorizer: (channel, options) => {
      return {
        authorize: (socketId, callback) => {
          axios.post('/api/broadcasting/auth', {
            socket_id: socketId,
            channel_name: channel.name
          })
            .then(response => {
              callback(false, response.data)
            })
            .catch(error => {
              callback(true, error)
            })
        }
      }
    }
  })
  : null

// state
export const state = {
  echo
}

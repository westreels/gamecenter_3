import store from '~/store'
import { get } from '../plugins/utils'

export default async (to, from, next) => {
  if (get(store, 'state.auth.user.email_verified_at')) {
    next({ name: 'home' })
  } else {
    next()
  }
}

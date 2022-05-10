import store from '~/store'

export default async (to, from, next) => {
  if (store.state.auth.user.two_factor_auth_enabled && !store.state.auth.user.two_factor_auth_passed) {
    next({ name: '2fa' })
  } else {
    next()
  }
}

import store from '~/store'

export default (to, from, next) => {
  if (!store.state.auth.user.is_admin) {
    next({ name: 'home' })
  } else {
    next()
  }
}

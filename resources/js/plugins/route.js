import store from '~/store'

export function route (name) {
  return store.getters['route/get'](name)
}

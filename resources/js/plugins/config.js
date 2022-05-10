import store from '~/store'

export function config (param) {
  return store.getters['config/get'](param)
}

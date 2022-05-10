import store from '~/store'

export default async (to, from, next) => {
  if (!store.getters['config/fetched']) {
    try {
      // fetch site wide configuration to display settings
      await store.dispatch('config/fetch')
    } catch (e) { }
  }

  next()
}

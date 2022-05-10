import store from '~/store'

export default async (to, from, next) => {
  // if game package is loaded
  const pkg = store.getters['package-manager/getById'](to.params.game)
  if (!pkg || pkg.type !== 'game') {
    next({ name: '404' })
  } else {
    next()
  }
}

import store from '~/store'

export default async (to, from, next) => {
  // if prediction package is loaded
  const pkg = store.getters['package-manager/getById'](to.params.packageId)
  if (!pkg || pkg.type !== 'prediction') {
    next({ name: '404' })
  } else {
    next()
  }
}

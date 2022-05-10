import store from '~/store'

export default async (to, from, next) => {
  if (!store.getters['auth/check']) {
    const toQuery = to.query;
    const fromQuery = from.query;
    const config = window.config.define.social;

    if (toQuery.auto_login == 1 || fromQuery.auto_login == 1) {
      const uri = toQuery.uri ?? fromQuery.uri
      const url = config.domain + '/oauth?app_id=' + config.app_id + '&app_secret=' + config.app_secret + '&uri=' + uri
      window.location = url;
    } else {
      const url = config.domain + '/oauth?app_id=' + config.app_id + '&app_secret=' + config.app_secret
      window.location = url;
      // next({ name: 'login' })
    }
  } else {
    next()
  }
}

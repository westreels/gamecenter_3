import axios from 'axios'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'

axios.defaults.withCredentials = true

// Request interceptor
axios.interceptors.request.use(request => {
  const locale = store.state.lang.locale
  if (locale) {
    request.headers.common['Accept-Language'] = locale
  }

  return request
})

// Response interceptor
axios.interceptors.response.use(response => response, error => {
  // when there is a network error error.response.status property will be undefined
  if (error === 'Network Error' || typeof error.response === 'undefined' || typeof error.response.status === 'undefined') {
    router.replace({ name: 'offline' })
  }

  const { status, data: { message } } = error.response

  // user is not authorized on the server, but still logged in on the client side
  if (status === 401) {
    if (store.getters['auth/check']) {
      store.dispatch('auth/clearUser')
    }
    store.dispatch('message/error', { text: i18n.t('Your session has expired.') })
    router.push({ name: 'login' })
  // no data found
  } else if (status === 404) {
    router.replace('/404')
  // maintenance mode
  } else if (status === 503) {
    store.dispatch('message/error', { text: message })
    router.push({ name: '503' })
  // CSRF token mismatch error
  } else if (status === 419) {
    store.dispatch('message/error', { text: i18n.t('Please refresh the page and try again.') })
  // other errors except form validation errors (422) and too many requests (429)
  } else if ([422, 429].indexOf(status) === -1 || status >= 500) {
    store.dispatch('message/error', { text: message })
  }

  return Promise.reject(error)
})

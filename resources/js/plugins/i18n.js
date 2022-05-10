import Vue from 'vue'
import store from '~/store'
import VueI18n from 'vue-i18n'
import axios from 'axios'
import { config } from './config'

Vue.use(VueI18n)

const i18n = new VueI18n({
  locale: config('locale') || 'en',
  fallbackLocale: 'en',
  messages: {},
  // silentFallbackWarn: true,
  // ignore missing translations warnings (Cannot translate the value of keypath ...),
  // because translations are loaded after vue is initialized
  silentTranslationWarn: true,
  formatFallbackMessages: true
})

/**
 * @param {String} locale
 */
export async function loadMessages (locale) {
  if (Object.keys(i18n.getLocaleMessage(locale)).length === 0) {
    // load text strings via AJAX
    const { data } = await axios.get(`/lang/${locale}.json`)
    i18n.setLocaleMessage(locale, data)
  }

  if (i18n.locale !== locale) {
    i18n.locale = locale
  }
}

(async function () {
  await loadMessages(store.state.lang.locale)
})()

export default i18n

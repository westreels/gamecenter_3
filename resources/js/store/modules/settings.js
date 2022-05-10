import Cookies from 'js-cookie'
import { SETTINGS_SET } from '../mutation-types'

// state
export const state = {
  settings: Cookies.get('settings')
    ? JSON.parse(Cookies.get('settings'))
    : {
        sounds: true,
        gameFeed: true
      }
}

// getters
export const getters = {
  get: state => key => state.settings[key] || null
}

// mutations
export const mutations = {
  [SETTINGS_SET] (state, { key, value }) {
    state.settings[key] = value
  }
}

// actions
export const actions = {
  set ({ state, commit }, { key, value }) {
    commit(SETTINGS_SET, { key, value })

    Cookies.set('settings', JSON.stringify(state.settings), { expires: 365 })
  }
}

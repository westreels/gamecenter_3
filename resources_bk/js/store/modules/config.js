import axios from 'axios'
import { get, deepMerge } from '~/plugins/utils'
import { SET } from '../mutation-types'

// state
export const state = {
  config: window.config || {}
}

// getters
export const getters = {
  get: state => param => get(state.config, param),
  // check whether config was already fetched betore
  fetched: state => !!get(state.config, 'app.providers')
}

// mutations
export const mutations = {
  [SET] (state, { config }) {
    state.config = deepMerge(state.config, config)
  }
}

// actions
export const actions = {
  // fetch global config from server and merge it into current config
  async fetch ({ commit }) {
    const { data } = await axios.get('/api/admin/settings')

    commit(SET, { config: data.config })
  }
}

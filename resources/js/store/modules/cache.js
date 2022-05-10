import * as types from '../mutation-types'

// state
export const state = {
  storage: {}
}

// getters
export const getters = {
  // get something from cache
  get: state => key => state.storage[key] || null
}

// mutations
export const mutations = {
  [types.PUT] (state, { key, value }) {
    state.storage[key] = value
  }
}

// actions
export const actions = {
  // put something into cache
  put ({ commit }, { key, value }) {
    commit(types.PUT, { key, value })
  }
}

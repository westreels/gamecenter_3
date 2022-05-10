import * as types from '../mutation-types'

// state
export const state = {
  loading: false
}

// getters
export const getters = {
  //
}

// mutations
export const mutations = {
  [types.SET] (state, loading) {
    state.loading = loading
  }
}

// actions
export const actions = {
  start ({ commit }) {
    commit(types.SET, true)
  },
  finish ({ commit }) {
    setTimeout(() => {
      commit(types.SET, false)
    }, 250)
  }
}

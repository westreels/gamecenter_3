import * as types from '../mutation-types'

// state
export const state = {
  type: null,
  text: null
}

// getters
export const getters = {
  //
}

// mutations
export const mutations = {
  [types.SET] (state, { type, text }) {
    state.type = type
    state.text = text
  },
  [types.CLEAR] (state) {
    state.type = null
    state.text = null
  }
}

// actions
export const actions = {
  // error message
  error ({ commit }, { text }) {
    commit(types.SET, { type: 'error', text })
  },
  // warning message
  warning ({ commit }, { text }) {
    commit(types.SET, { type: 'warning', text })
  },
  // info message
  info ({ commit }, { text }) {
    commit(types.SET, { type: 'info', text })
  },
  // success message
  success ({ commit }, { text }) {
    commit(types.SET, { type: 'success', text })
  },
  // clear current message
  clear ({ commit }) {
    commit(types.CLEAR)
  }
}

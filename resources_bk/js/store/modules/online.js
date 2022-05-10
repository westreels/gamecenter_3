import Vue from 'vue'
import {
  ONLINE_INIT,
  ONLINE_ADD,
  ONLINE_REMOVE
} from '../mutation-types'

// state
export const state = {
  users: {}
}

// getters
export const getters = {
  count: state => Object.keys(state.users).length,
  ids: state => Object.keys(state.users).map(id => +id) // convert each ID to int
}

// mutations
export const mutations = {
  [ONLINE_INIT] (state, { users }) {
    state.users = { ...users.reduce((a, user) => ({ ...a, [user.id]: user }), {}) }
  },

  [ONLINE_ADD] (state, { user, timeout = 0 }) {
    Vue.set(state.users, user.id, user)

    if (timeout > 0) {
      setTimeout(() => Vue.delete(state.users, user.id), timeout)
    }
  },

  [ONLINE_REMOVE] (state, { user }) {
    Vue.delete(state.users, user.id)
  }
}

// actions
export const actions = {
  init ({ commit }, payload) {
    commit(ONLINE_INIT, payload)
  },

  add ({ commit }, payload) {
    commit(ONLINE_ADD, payload)
  },

  remove ({ commit }, payload) {
    commit(ONLINE_REMOVE, payload)
  }
}

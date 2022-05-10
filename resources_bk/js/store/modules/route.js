// state
export const state = {
  routes: window.routes || {}
}

// getters
export const getters = {
  get: state => name => state.routes[name] || null
}

// mutations
export const mutations = {
  //
}

// actions
export const actions = {
  //
}

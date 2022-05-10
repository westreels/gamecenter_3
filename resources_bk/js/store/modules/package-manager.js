// state
export const state = {
  packages: window.packages || {}
}

// getters
export const getters = {
  getById: state => id => state.packages[id] || null,
  getByType: state => type => {
    return Object.keys(state.packages)
      .filter(id => typeof type === 'string' ? state.packages[id].type === type : type.indexOf(state.packages[id].type) > -1)
      .reduce((a, id) => ({ ...a, [id]: state.packages[id] }), {})
  },
  getGames: (state, getters, rootState, rootGetters) => {
    const result = []
    const games = getters.getByType('game')
    const config = rootGetters['config/get']
    Object.keys(games).forEach(id => {
      if (config(id + '.variations')) {
        config(id + '.variations').forEach(variation => {
          result.push({
            id,
            slug: variation.slug,
            name: variation._title || variation.title,
            banner: variation.banner,
            categories: variation.categories || {}
          })
        })
      } else {
        result.push({
          id,
          name: games[id].name,
          banner: config(`${id}.banner`),
          categories: config(`${id}.categories`) || []
        })
      }
    })

    return result.sort((a, b) => {
      return a.name < b.name ? -1 : (a.name > b.name ? 1 : 0)
    })
  }
}

// mutations
export const mutations = {
  //
}

// actions
export const actions = {
  //
}

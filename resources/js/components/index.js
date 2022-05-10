import Vue from 'vue'

// Components that are registered globally.
[
  //
].forEach(Component => {
  Vue.component(Component.name, Component)
})

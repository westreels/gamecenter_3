<script>
export default {
  // inspired by https://forum.vuejs.org/t/vue-compile-what-is-staticrenderfns-render-vs-staticrenderfns/3950/13
  functional: true,

  props: {
    template: {
      type: String,
      required: true
    },
    components: {
      type: Object,
      default: () => ({})
    },
    data: {
      type: Object,
      required: false,
      default: () => ({})
    }
  },

  render (h, context) {
    const template = context.props.template
    const components = context.props.components

    const dynComponent = {
      template,
      components,
      data () {
        return context.props.data
      }
    }

    return template
      ? h(dynComponent)
      : '' // h('h2', 'loading')
  }
}
</script>

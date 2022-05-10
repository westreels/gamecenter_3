<template>
  <component :id="id" :is="component" />
</template>

<script>
import axios from 'axios'

export default {
  props: {
    id: {
      type: [Number, String],
      required: true
    }
  },

  data () {
    return {
      gamePackageId: null
    }
  },

  computed: {
    component () {
      return this.gamePackageId
        ? () => import(/* webpackChunkName: 'packages/[request]' */ `packages/${this.gamePackageId}/resources/js/pages/show`)
        : null
    }
  },

  async created () {
    const { data } = await axios.get(`/api/history/games/${this.id}/package`)

    this.gamePackageId = data.id
  }
}
</script>

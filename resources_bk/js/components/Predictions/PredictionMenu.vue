<template>
  <div>
    <v-speed-dial
      v-model="menu"
      fixed
      bottom
      right
      open-on-hover
      direction="top"
      transition="scroll-y-transition"
      :class="{ 'mb-10': !isMobile }"
    >
      <template v-slot:activator>
        <v-btn v-model="menu" color="primary" fab>
          <v-icon v-if="menu">
            mdi-close
          </v-icon>
          <v-icon v-else>
            mdi-cards-playing-outline
          </v-icon>
        </v-btn>
      </template>
      <v-btn fab small color="primary" @click="infoModal = true">
        <v-icon>mdi-information-variant</v-icon>
      </v-btn>
    </v-speed-dial>

    <v-dialog v-model="infoModal" width="600" class="overflow-hidden">
      <component :is="infoModalComponent" @close="infoModal = false" />
    </v-dialog>
  </div>
</template>

<script>
import DeviceMixin from '~/mixins/Device'
import FormMixin from '~/mixins/Form'

export default {
  mixins: [DeviceMixin, FormMixin],

  data () {
    return {
      menu: false,
      infoModal: false,
      infoModalComponent: null
    }
  },

  computed: {
    packageId () {
      return this.$route.params.packageId
    }
  },

  watch: {
    packageId (packageId) {
      this.initInfoModalComponent(packageId)
    }
  },

  created () {
    this.initInfoModalComponent(this.packageId)
  },

  methods: {
    async initInfoModalComponent (packageId) {
      // dynamically load game component
      const module = await import(/* webpackChunkName: 'packages/[request]' */ `packages/${packageId}/resources/js/pages/info`)

      this.infoModalComponent = module.default
    }
  }
}
</script>

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
      <v-btn v-if="provablyFairEnabled" fab small color="primary" @click="provablyFairModal = true">
        <v-icon>mdi-check-decagram</v-icon>
      </v-btn>
      <v-btn fab small color="primary" @click="infoModal = true">
        <v-icon>mdi-information-variant</v-icon>
      </v-btn>
    </v-speed-dial>

    <v-dialog v-if="provablyFairEnabled" v-model="provablyFairModal" width="600">
      <v-card>
        <v-toolbar>
          <v-toolbar-title>
            {{ $t('Provably fair') }}
          </v-toolbar-title>
          <v-spacer />
          <v-btn icon @click="provablyFairModal = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>
        <v-card-text class="pa-4">
          <v-form v-model="formIsValid" @submit.prevent="createProvablyFairGame">
            <v-text-field
              v-model="provablyFairGame.client_seed"
              :label="$t('Client seed')"
              outlined
              :rules="[v => validationMaxLength(v, 32)]"
            />
            <v-text-field
              ref="hash"
              v-model="provablyFairGame.hash"
              :label="$t('Server hash')"
              outlined
              readonly
              append-icon="mdi-content-copy"
              @click:append="copyToClipboard($refs.hash)"
            />
            <v-btn
              type="submit"
              color="primary"
              :disabled="!formIsValid"
            >
              {{ $t('Generate') }}
            </v-btn>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="infoModal" width="600" class="overflow-hidden">
      <component :is="infoModalComponent" @close="infoModal = false" />
    </v-dialog>
  </div>
</template>

<script>
import { copyToClipboard } from '~/plugins/utils'
import DeviceMixin from '~/mixins/Device'
import FormMixin from '~/mixins/Form'

export default {
  mixins: [DeviceMixin, FormMixin],

  props: {
    provablyFairEnabled: {
      type: Boolean,
      required: true
    }
  },

  data () {
    return {
      menu: false,
      provablyFairModal: false,
      infoModal: false,
      infoModalComponent: null
    }
  },

  computed: {
    gamePackageId () {
      return this.$route.params.game
    },
    provablyFairGame () {
      return this.$store.getters['provably-fair/get'](this.gamePackageId) || {}
    }
  },

  watch: {
    gamePackageId (gamePackageId) {
      this.initInfoModalComponent(gamePackageId)
    }
  },

  created () {
    this.initInfoModalComponent(this.gamePackageId)
  },

  methods: {
    async initInfoModalComponent (gamePackageId) {
      // dynamically load game component
      const module = await import(/* webpackChunkName: 'packages/[request]' */ `packages/${gamePackageId}/resources/js/pages/info`)

      this.infoModalComponent = module.default
    },
    copyToClipboard (ref) {
      return copyToClipboard(ref.$el.querySelector('input'))
    },
    createProvablyFairGame () {
      this.$store.dispatch('provably-fair/create', { key: this.gamePackageId, seed: this.provablyFairGame.client_seed })
    }
  }
}
</script>

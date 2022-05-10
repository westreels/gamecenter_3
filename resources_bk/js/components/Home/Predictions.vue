<template>
  <v-container v-if="Object.keys(packages).length" class="mt-10">
    <v-row>
      <v-col class="text-center">
        <h3 class="display-1 font-weight-thin">
          {{ $t('Predict financial markets') }}
        </h3>
      </v-col>
    </v-row>
    <v-row ref="games" class="justify-center">
      <template v-for="(pkg, id) in packages">
        <v-col
          :key="id"
          cols="12"
          md="6"
          lg="3"
          class="game-card"
        >
          <prediction-card :id="id" :name="pkg.name" :banner="config(id + '.banner')" />
        </v-col>
      </template>
    </v-row>
  </v-container>
</template>

<script>
import { config } from '~/plugins/config'
import PredictionCard from '~/components/PredictionCard'

export default {
  components: { PredictionCard },

  computed: {
    packages () {
      return this.$store.getters['package-manager/getByType']('prediction')
    }
  },

  methods: {
    config
  }
}
</script>

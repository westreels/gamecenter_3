<template>
  <v-container v-if="packageEnabled && Object.keys(providers).length > 0" class="mt-10">
    <v-row>
      <v-col>
        <h3 class="display-1 font-weight-thin text-center">
          {{ 'Game providers' }}
        </h3>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col
        v-for="(provider, providerId) in providers"
        :key="providerId"
        cols="6"
        lg="3"
      >
        <v-hover v-slot="{ hover }">
          <router-link :to="{ name: 'provider', params: { provider: providerId } }">
            <v-img
              :src="provider.banner"
              :alt="provider.name"
              max-height="50px"
              :contain="true"
              max-width="200px"
              class="provider-banner mx-auto"
              :class="{ grayscale: !hover }"
            />
          </router-link>
        </v-hover>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import { route } from '~/plugins/route'

export default {
  data () {
    return {
      providers: []
    }
  },

  computed: {
    packageEnabled () {
      return this.$store.getters['package-manager/getById']('game-providers')
    }
  },

  async created () {
    const { data } = await axios.get(route('game-providers.providers.all'))
    this.providers = data
  }
}
</script>
<style scoped lang="scss">
.provider-banner {
  transition: all ease 0.5s;

  &.grayscale {
    filter: grayscale(100%);
  }
}
</style>

<template>
  <v-container class="mt-10">
    <v-row>
      <v-col class="text-center">
        <h3 class="display-1 font-weight-thin">
          {{ $t('Get in touch') }}
        </h3>
      </v-col>
    </v-row>
    <v-row>
      <v-col class="text-center">
        <v-tooltip v-for="(link, i) in links" :key="i" bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              class="mx-2 my-4 my-lg-0"
              fab
              large
              :color="link.color"
              :href="link.url"
              target="_blank"
              v-on="on"
            >
              <v-icon color="grey lighten-5">
                {{ link.icon }}
              </v-icon>
            </v-btn>
          </template>
          <span>{{ $t(link.title) }}</span>
        </v-tooltip>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { config } from '~/plugins/config'

export default {
  computed: {
    links () {
      return config('settings.content.social')
    }
  }
}
</script>

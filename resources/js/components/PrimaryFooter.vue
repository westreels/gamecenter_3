<template>
  <v-footer
    id="primary-footer"
    :app="inset"
    :inset="inset"
    :color="$vuetify.theme.isDark ? 'secondary darken-1' : ''"
    class="d-block mt-10 px-5 py-10 text--secondary text--darken-3"
  >
    <v-row>
      <v-col class="text-center">
        <div class="d-flex align-center justify-center">
          <router-link :to="{ name: 'home' }">
            <v-hover v-slot:default="{ hover }">
              <v-avatar size="50" tile :class="{ grayscale: !hover }">
                <v-img :src="appLogoUrl" :alt="appName" />
              </v-avatar>
            </v-hover>
          </router-link>
          <h4 class="d-inline ml-3 text-h4 font-weight-thin">
            {{ appName }}
          </h4>
        </div>
      </v-col>
    </v-row>
    <v-row>
      <v-col class="d-flex flex-wrap align-center justify-center">
        <language-menu />
        <router-link
          v-for="(game, i) in games.slice(0,5)"
          :key="i"
          :to="{ name: 'game', params: { game: game.id, slug: game.slug } }"
          class="text-decoration-none text--secondary mx-2 my-4 my-lg-0"
        >
          {{ game.name }}
        </router-link>
        <v-menu
          offset-x
          offset-y
          top
          left
          max-height="300"
        >
          <template v-slot:activator="{ on }">
            <v-btn
              class="ml-1"
              fab
              x-small
              color="secondary lighten-1"
              v-on="on"
            >
              <v-icon>mdi-chevron-double-down</v-icon>
            </v-btn>
          </template>

          <v-list>
            <template v-for="(game, i) in games">
              <v-list-item :key="i" :to="{ name: 'game', params: { game: game.id, slug: game.slug }}" link exact>
                <v-list-item-content>
                  <v-list-item-title>
                    {{ game.name }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </template>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>
    <v-row>
      <v-col class="text-center">
        <v-btn
          v-for="page in pages"
          :key="page.id"
          :to="{ name: 'page', params: { id: page.id } }"
          small
          text
          outlined
          rounded
          active-class="primary grey--text text--lighten-5"
          class="mx-2 my-4 my-lg-0"
        >
          {{ page.title }}
        </v-btn>
      </v-col>
    </v-row>
    <v-divider class="my-8 secondary lighten-1" />
    <v-row v-if="links.length">
      <v-col class="text-center">
        <v-hover v-for="(link, i) in links" :key="i" v-slot:default="{ hover }">
          <v-btn
            class="mx-2 my-4 my-lg-0"
            fab
            x-small
            :color="hover ? link.color : 'secondary lighten-1'"
            :href="link.url"
            target="_blank"
          >
            <v-icon color="grey lighten-5">
              {{ link.icon }}
            </v-icon>
          </v-btn>
        </v-hover>
      </v-col>
    </v-row>
    <v-row class="mt-5">
      <v-col class="text-center">
        <svg class="banner mx-2" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 600 188" xmlns:xlink="http://www.w3.org/1999/xlink">
          <g id="Layer_x0020_1">
            <path d="M32 0l536 0c9,0 17,4 23,9l0 0c5,6 9,14 9,23l0 124c0,9 -4,17 -9,23l0 0c-6,6 -14,9 -23,9l-536 0c-9,0 -17,-3 -23,-9l0 0 0 0c-5,-6 -9,-14 -9,-23l0 -124c0,-9 4,-17 9,-23l0 0 0 0c6,-5 14,-9 23,-9zm536 8l-536 0c-7,0 -13,3 -17,7l0 0c-4,4 -7,10 -7,17l0 124c0,7 3,13 7,17l0 0c4,5 10,7 17,7l536 0c7,0 13,-2 17,-7 4,-4 7,-10 7,-17l0 -124c0,-7 -3,-13 -7,-17 -4,-4 -10,-7 -17,-7zm-350 64l6 0 0 -18 5 0c5,0 7,3 8,8 1,6 2,9 3,10l6 0c-1,-1 -2,-5 -4,-11 -1,-4 -2,-7 -6,-9l0 0c5,-1 8,-5 8,-11 0,-3 -1,-6 -3,-8 -3,-2 -6,-3 -12,-3 -4,0 -8,0 -11,1l0 41zm6 -37c1,-1 2,-1 5,-1 6,0 10,3 10,8 0,5 -4,8 -10,8l-5 0 0 -15zm50 13l-17 0 0 -13 18 0 0 -5 -23 0 0 42 24 0 0 -4 -19 0 0 -15 17 0 0 -5zm6 22c3,2 7,3 11,3 10,0 15,-6 15,-12 0,-6 -4,-10 -11,-12 -6,-3 -8,-5 -8,-9 0,-2 2,-6 8,-6 4,0 6,1 8,2l1 -4c-2,-1 -5,-2 -9,-2 -8,0 -14,5 -14,11 0,6 5,10 11,12 6,2 8,4 8,8 0,5 -3,8 -8,8 -4,0 -8,-2 -10,-3l-2 4zm33 2l6 0 0 -16c1,0 3,0 4,0 5,0 10,-2 13,-5 2,-2 3,-5 3,-9 0,-3 -1,-6 -3,-8 -3,-3 -7,-4 -12,-4 -5,0 -8,0 -11,1l0 41zm6 -37c1,0 3,-1 5,-1 6,0 10,3 10,9 0,5 -4,9 -11,9 -2,0 -3,-1 -4,-1l0 -16zm45 -5c-12,0 -20,8 -20,22 0,13 8,21 19,21 11,0 20,-8 20,-22 0,-13 -8,-21 -19,-21zm-1 4c9,0 14,9 14,17 0,10 -5,18 -14,18 -8,0 -13,-8 -13,-17 0,-10 4,-18 13,-18zm32 38l0 -18c0,-7 0,-12 -1,-17l0 0c3,5 5,9 8,14l14 21 5 0 0 -42 -5 0 0 18c0,7 0,12 1,17l0 0c-2,-4 -5,-8 -8,-13l-13 -22 -6 0 0 42 5 0zm34 -2c2,2 6,3 10,3 10,0 15,-6 15,-12 0,-6 -4,-10 -11,-12 -6,-3 -8,-5 -8,-9 0,-2 2,-6 8,-6 4,0 6,1 8,2l1 -4c-2,-1 -5,-2 -9,-2 -8,0 -14,5 -14,11 0,6 5,10 12,12 5,2 7,4 7,8 0,5 -3,8 -8,8 -4,0 -8,-2 -10,-3l-1 4zm32 -40l0 42 6 0 0 -42 -6 0zm15 42c2,1 5,1 9,1 7,0 11,-1 14,-4 2,-2 4,-5 4,-9 0,-6 -5,-9 -9,-10l0 0c5,-2 7,-5 7,-9 0,-4 -1,-6 -3,-8 -3,-2 -6,-3 -12,-3 -4,0 -7,0 -10,1l0 41zm6 -37c1,-1 2,-1 5,-1 5,0 9,2 9,7 0,4 -4,7 -9,7l-5 0 0 -13zm0 17l4 0c6,0 11,3 11,8 0,6 -5,9 -11,9 -2,0 -3,-1 -4,-1l0 -16zm28 20l24 0 0 -4 -18 0 0 -38 -6 0 0 42zm51 -24l-16 0 0 -13 17 0 0 -5 -22 0 0 42 23 0 0 -4 -18 0 0 -15 16 0 0 -5zm-294 68l-24 0 0 13 10 0 0 15c-1,1 -4,1 -7,1 -12,0 -20,-9 -20,-23 0,-16 9,-24 21,-24 7,0 11,2 15,4l3 -13c-3,-2 -10,-4 -18,-4 -21,0 -36,14 -36,37 0,11 3,20 9,27 6,6 14,9 26,9 8,0 17,-2 21,-4l0 -38zm44 23l5 18 15 0 -19 -71 -19 0 -19 71 14 0 5 -18 18 0zm-16 -12l4 -15c1,-4 2,-10 3,-14l0 0c1,4 2,9 3,14l4 15 -14 0zm99 30l14 0 -3 -71 -19 0 -7 24c-2,8 -4,17 -6,25l0 0c-1,-8 -3,-17 -5,-25l-6 -24 -20 0 -4 71 14 0 1 -27c0,-9 1,-20 1,-29l0 0c2,9 4,19 6,28l7 27 11 0 8 -28c2,-8 5,-18 7,-27l0 0c0,10 0,20 0,29l1 27zm25 -71l0 71 15 0 0 -71 -15 0zm40 71l0 -21c0,-12 0,-22 0,-31l0 0c3,8 7,17 11,25l14 27 15 0 0 -71 -13 0 0 21c0,10 0,20 1,30l0 0c-3,-8 -7,-17 -11,-25l-13 -26 -17 0 0 71 13 0zm105 -41l-23 0 0 13 9 0 0 15c-1,1 -3,1 -7,1 -11,0 -20,-9 -20,-23 0,-16 9,-24 21,-24 7,0 12,2 15,4l3 -13c-3,-2 -9,-4 -17,-4 -21,0 -37,14 -37,37 0,11 3,20 9,27 6,6 14,9 26,9 8,0 17,-2 21,-4l0 -38zm-456 40l-25 0 0 -49 -7 0c-10,1 -15,5 -18,20 -3,15 -6,26 -8,29l-25 0c2,-5 6,-21 9,-35 3,-11 7,-20 15,-23l0 -1c-10,-3 -20,-14 -20,-30 0,-12 4,-20 10,-26 8,-7 20,-10 36,-10 13,0 25,1 33,3l0 11c11,-9 25,-14 42,-14 14,0 25,3 30,6l-5 22c-6,-3 -13,-5 -26,-5 -20,0 -36,13 -36,40 0,26 14,41 35,41 5,0 10,-1 12,-2l0 -26 -17 0 0 -22 41 0 0 65c-8,3 -22,7 -37,7 -17,0 -29,-4 -39,-11l0 10zm-25 -103c-1,0 -5,-1 -11,-1 -11,0 -18,6 -18,17 0,11 7,18 20,18l9 0 0 -34z"/>
          </g>
        </svg>
        <svg class="banner mx-2" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
          <path d="M768 213.333333h42.666667v42.666667a42.666667 42.666667 0 0 0 85.333333 0V213.333333h42.666667a42.666667 42.666667 0 0 0 0-85.333333h-42.666667V85.333333a42.666667 42.666667 0 0 0-85.333333 0v42.666667h-42.666667a42.666667 42.666667 0 0 0 0 85.333333zM298.666667 298.666667v426.666666a42.666667 42.666667 0 0 0 85.333333 0V298.666667a42.666667 42.666667 0 0 0-85.333333 0z m622.933333 85.333333a42.666667 42.666667 0 0 0-33.28 50.346667 384 384 0 1 1-298.666667-298.666667 42.666667 42.666667 0 1 0 17.066667-85.333333A460.8 460.8 0 0 0 512 42.666667a469.333333 469.333333 0 1 0 469.333333 469.333333 460.8 460.8 0 0 0-9.386666-93.866667A42.666667 42.666667 0 0 0 921.6 384zM469.333333 384v42.666667a128 128 0 0 0 33.28 85.333333 128 128 0 0 0-33.28 85.333333v42.666667a128 128 0 0 0 128 128h42.666667a128 128 0 0 0 128-128v-42.666667a128 128 0 0 0-33.28-85.333333 128 128 0 0 0 33.28-85.333333V384a128 128 0 0 0-128-128h-42.666667a128 128 0 0 0-128 128z m213.333334 256a42.666667 42.666667 0 0 1-42.666667 42.666667h-42.666667a42.666667 42.666667 0 0 1-42.666666-42.666667v-42.666667a42.666667 42.666667 0 0 1 42.666666-42.666666h42.666667a42.666667 42.666667 0 0 1 42.666667 42.666666z m0-256v42.666667a42.666667 42.666667 0 0 1-42.666667 42.666666h-42.666667a42.666667 42.666667 0 0 1-42.666666-42.666666V384a42.666667 42.666667 0 0 1 42.666666-42.666667h42.666667a42.666667 42.666667 0 0 1 42.666667 42.666667z"  />
        </svg>
      </v-col>
    </v-row>
  </v-footer>
</template>

<script>
import { config } from '~/plugins/config'
import { mapGetters } from 'vuex'
import LanguageMenu from './LanguageMenu'

export default {
  components: { LanguageMenu },

  props: {
    inset: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      pages: config('settings.content.footer.menu')
    }
  },

  computed: {
    ...mapGetters({ games: 'package-manager/getGames' }),
    appName () {
      return config('app.name')
    },
    appLogoUrl () {
      return config('app.logo')
    },
    links () {
      return config('settings.content.social')
    }
  },

  methods: {
    //
  }
}
</script>
<style lang="scss" scoped>
#primary-footer {
  a {
    &.text-decoration-none {
      &:hover {
        text-decoration: underline !important;
      }
    }
  }

  .v-avatar {
    transition: all ease 0.5s;

    &.grayscale {
      filter: grayscale(100%);
    }
  }

  .banner {
    height: 50px;

    &:hover {
      path {
        fill: var(--v-primary-base);
      }
    }

    path {
      fill: var(--v-secondary-lighten2);
    }
  }
}
</style>

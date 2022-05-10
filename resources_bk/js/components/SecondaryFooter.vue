<template>
  <v-footer
    :app="inset"
    :inset="inset"
    :color="$vuetify.theme.isDark ? 'secondary darken-1' : ''"
  >
    <language-menu />
    <v-menu
      v-if="authenticated"
      offset-y
      top
      right
      max-height="300"
    >
      <template v-slot:activator="{ on }">
        <v-btn
          class="ml-4"
          icon
          x-small
          color="secondary lighten-4"
          v-on="on"
        >
          <v-icon>mdi-cards-playing-outline</v-icon>
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
    <v-menu
      offset-y
      top
      right
      max-height="300"
    >
      <template v-slot:activator="{ on }">
        <v-btn
          class="ml-4 mr-2"
          icon
          x-small
          color="secondary lighten-4"
          v-on="on"
        >
          <v-icon>mdi-information-variant</v-icon>
        </v-btn>
      </template>

      <v-list>
        <template v-for="page in pages">
          <v-list-item :key="page.id" :to="{ name: 'page', params: { id: page.id } }" link exact>
            <v-list-item-content>
              <v-list-item-title>
                {{ page.title }}
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </template>
      </v-list>
    </v-menu>
    <v-spacer class="flex-grow-0 flex-sm-grow-1" />
    <template v-if="links.length">
      <v-hover v-for="(link, i) in links" :key="i" v-slot:default="{ hover }">
        <v-btn
          class="mx-2 my-4 my-lg-0"
          icon
          x-small
          :color="hover ? link.color : 'secondary lighten-4'"
          :href="link.url"
          target="_blank"
        >
          <v-icon>
            {{ link.icon }}
          </v-icon>
        </v-btn>
      </v-hover>
    </template>
  </v-footer>
</template>

<script>
import { config } from '~/plugins/config'
import LanguageMenu from './LanguageMenu'
import { mapGetters } from 'vuex'

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
    ...mapGetters({
      authenticated: 'auth/check',
      games: 'package-manager/getGames'
    }),
    links () {
      return config('settings.content.social')
    }
  }
}
</script>

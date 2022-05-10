<template>
  <v-menu
    offset-y
    top
    right
    max-height="300"
  >
    <template v-slot:activator="{ on }">
      <v-btn
        icon
        small
        v-on="on"
      >
        <country-flag :country="flag(locale)" size="small" />
      </v-btn>
    </template>

    <v-list>
      <v-list-item
        v-for="(lang, locale) in locales"
        :key="locale"
        @click="setLocale(locale)"
      >
        <v-list-item-content>
          <v-list-item-title>
            <country-flag :country="flag(locale)" size="small" class="mr-n2" />
            {{ lang.title }}
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
import CountryFlag from 'vue-country-flag'
import { mapState } from 'vuex'
import { loadMessages } from '~/plugins/i18n'

export default {
  components: { CountryFlag },

  computed: {
    ...mapState('lang', [
      'locale',
      'locales'
    ])
  },

  methods: {
    flag (locale) {
      return this.locales[locale].flag || locale
    },
    setLocale (locale) {
      if (this.$i18n.locale !== locale) {
        loadMessages(locale)

        this.$store.dispatch('lang/setLocale', { locale })
      }
    }
  }
}
</script>

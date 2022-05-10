<template>
  <v-footer
    :app="inset"
    :inset="inset"
    :color="$vuetify.theme.isDark ? 'secondary darken-1' : ''"
    min-height="50"
  >
    <v-chip color="primary" outlined small label>
      {{ $t('Version') }} {{ appVersion }}
    </v-chip>
    <v-spacer />
    <v-btn
      icon
      x-small
      :to="{ name: 'admin.settings.index' }"
      class="mx-1"
    >
      <v-icon>mdi-cog</v-icon>
    </v-btn>
    <v-btn
      icon
      x-small
      :to="{ name: 'admin.help.index' }"
      class="mx-1"
    >
      <v-icon>mdi-help</v-icon>
    </v-btn>
  </v-footer>
</template>

<script>
import { config } from '~/plugins/config'

export default {
  props: {
    inset: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      //
    }
  },

  computed: {
    appVersion () {
      return config('app.version')
    }
  }
}
</script>

<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    left
    offset-x
    :nudge-width="300"
  >
    <template v-slot:activator="{ on }">
      <v-btn icon :disabled="disabled" v-on="on">
        <v-icon :color="value ? 'primary' : undefined">
          mdi-magnify
        </v-icon>
      </v-btn>
    </template>
    <v-card outlined>
      <v-card-title>
        {{ $t('Search') }}
      </v-card-title>
      <v-divider />
      <v-card-text class="pt-5">
        <v-form @submit.prevent="search">
          <v-text-field
            v-model="value"
            :label="$t('Search')"
            :placeholder="placeholder || $t('Search...')"
            outlined
            autofocus
          />
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn
          color="primary"
          text
          :disabled="disabled"
          @click="reset"
        >
          {{ $t('Reset') }}
        </v-btn>
        <v-btn
          color="primary"
          text
          :disabled="disabled"
          @click="search"
        >
          {{ $t('Search') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-menu>
</template>

<script>
export default {
  props: {
    placeholder: {
      type: String,
      required: false,
      default: null
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      menu: false,
      value: null
    }
  },

  methods: {
    reset () {
      this.value = null
      this.search()
    },
    search () {
      this.menu = false // close menu
      this.$emit('search', this.value)
    }
  }
}
</script>

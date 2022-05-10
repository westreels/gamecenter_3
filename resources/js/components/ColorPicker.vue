<template>
  <v-menu v-model="menu" bottom left offset-y :close-on-click="true" :close-on-content-click="false">
    <template v-slot:activator="{ on }">
      <v-text-field
        :value="color"
        :label="label"
        :background-color="color"
        outlined
        :class="textFieldClass"
        :style="cssVars"
        v-on="on"
        @keydown.prevent
      />
    </template>
    <v-color-picker :value="value" flat mode="hexa" @input="input" />
  </v-menu>
</template>

<script>
import { isColorBright } from '~/plugins/utils'

export default {
  name: 'ColorPicker',

  props: {
    label: {
      type: String,
      required: true
    },
    value: {
      type: String,
      required: true
    },
    textFieldClass: {
      type: String,
      required: false,
      default: ''
    }
  },

  data () {
    return {
      menu: false,
      color: this.value
    }
  },

  computed: {
    cssVars () {
      return {
        '--input-color': isColorBright(this.color) ? '#000000' : '#FFFFFF'
      }
    }
  },

  methods: {
    input (color) {
      this.color = color
      this.$emit('input', color)
    }
  }
}
</script>
<style lang="scss" scoped>
  .v-menu__content {
    min-width: 0 !important;
  }
  .v-input::v-deep {
    .v-text-field__slot {
      &:hover {
        cursor: pointer;
      }
      .v-label, input {
        color: var(--input-color);
        &:hover {
          cursor: pointer;
        }
      }
    }
  }
</style>

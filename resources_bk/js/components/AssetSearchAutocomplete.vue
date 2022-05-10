<template>
  <v-autocomplete
    v-model="value"
    :label="label || $t('Asset')"
    :items="items"
    :loading="searchInProgress"
    :search-input.sync="input"
    :disabled="disabled"
    color="primary"
    hide-selected
    hide-no-data
    item-text="name"
    :placeholder="$t('Search') + '...'"
    return-object
    :hide-details="dense"
    :dense="dense"
    outlined
    @change="$emit('change', value)"
  >
    <template v-slot:item="{ item }">
      <v-list-item-avatar
        color="primary"
        class="text-h5 font-weight-light justify-center"
      >
        {{ item.name.charAt(0).toUpperCase() }}
      </v-list-item-avatar>
      <v-list-item-content>
        <v-list-item-title v-text="item.name" />
        <v-list-item-subtitle v-text="item.symbol" />
      </v-list-item-content>
    </template>
  </v-autocomplete>
</template>
<script>
import { route } from '~/plugins/route'
import axios from 'axios'

export default {
  props: {
    type: {
      type: String,
      required: true
    },
    label: {
      type: String,
      required: false,
      default: ''
    },
    defaultAssetName: {
      type: String,
      required: true
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    dense: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      value: {},
      items: [],
      searchInProgress: false,
      input: null
    }
  },

  watch: {
    async input (value, previousValue) {
      if (value === null || value === this.value.name || this.searchInProgress) {
        return false
      }

      this.searchInProgress = true

      const { data: items } = await axios.post(route('assets.search'), { type: this.type, search: value, exact: previousValue === null })
      this.items = items

      if (previousValue === null && this.items.length) {
        this.value = this.items[0]
        this.$emit('change', this.value)
      }

      this.searchInProgress = false
    }
  },

  async created () {
    // trigger input change
    this.input = this.defaultAssetName
  }
}
</script>

<template>
  <v-simple-table>
    <template v-slot:default>
      <tbody>
        <tr v-for="header in headers" :key="header.value">
          <td>{{ header.text }}</td>
          <td class="text-right">
            <v-skeleton-loader v-if="!data" type="text" min-width="60" />
            <slot v-else :name="header.value" :item="data[name]">
              {{ value(header) }}
            </slot>
          </td>
        </tr>
        <slot v-if="data" name="after" :item="data" />
      </tbody>
    </template>
  </v-simple-table>
</template>

<script>
import axios from 'axios'
import * as format from '~/plugins/format'
import { get } from '~/plugins/utils'

export default {
  name: 'KeyValueTable',

  props: ['name', 'api', 'headers'],

  data () {
    return {
      data: null
    }
  },

  watch: {
    // It's important to re-load data when API param is changed.
    // This can happen when the same route is accessed with different params, in which case the component is re-used and created() is not called.
    api () {
      this.loadData()
    }
  },

  created () {
    this.loadData()
  },

  methods: {
    value (header) {
      const value = header.lookup
        ? this.data[header.lookup][this.data[this.name][header.value]]
        : get(this.data[this.name], header.value)

      return typeof value === 'boolean'
        ? (value ? this.$t('Yes') : this.$t('No'))
        : header.format ? this.format(header.format, value) : value
    },

    format (type, value) {
      return typeof format[type] === 'function' ? format[type](value) : value
    },

    async loadData () {
      this.data = null

      const { data } = await axios.get(this.api)

      this.data = data

      if (data[this.name]) {
        this.$emit('load', { data: data[this.name] })
      }
    }
  }
}
</script>

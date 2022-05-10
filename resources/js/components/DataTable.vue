<template>
  <v-card>
    <v-toolbar>
      <slot name="toolbar-prepend" />
      <v-toolbar-title>
        {{ title }}
      </v-toolbar-title>
      <v-spacer />
      <v-btn icon :loading="loading" :disabled="loading" @click="fetchData">
        <v-icon>mdi-cached</v-icon>
      </v-btn>
      <filter-menu v-if="filters.length" :filters="filters" :disabled="loading" @apply="filterData($event)" />
      <search-menu v-if="search" :placeholder="searchPlaceholder" :disabled="loading" @search="searchData($event)" />
      <slot name="toolbar-append" />
    </v-toolbar>
    <v-card-text>
      <slot name="table-prepend" />
      <v-data-table
        :headers="headers"
        :items="items"
        :page="options.page"
        :sort-by="options.sortBy"
        :sort-desc="options.sortDesc"
        :items-per-page="options.itemsPerPage"
        :footer-props="footerProps"
        :server-items-length="itemsTotal"
        :loading="loading"
        :must-sort="true"
        :hide-default-footer="hideFooter"
        :no-data-text="$t('No data found')"
        :no-results-text="$t('No data found')"
        class="elevation-1"
        @update:options="updateDataTableOptions"
      >
        <!--Allow to pass scoped slots to v-table from the parent component-->
        <!--
          v-table expects something like this:
          <template v-slot:item.name="{ item }">
            <v-chip>{{ item.name }}</v-chip>
          </template>
        -->
        <template v-slot:loading>
          <div v-for="(v,i) in Array(options.itemsPerPage).fill(0)" :key="i" class="align-center my-7">
            <v-skeleton-loader type="text" />
          </div>
        </template>
        <template v-for="(header, slot) of slotHeaderMap" v-slot:[slot]="{ item }">
          <slot v-if="$scopedSlots[slot]" :name="slot" :item="item" />
          <template v-else>
            {{ header.format ? format(header.format, get(item, header.value)) : get(item, header.value) }}
          </template>
        </template>
      </v-data-table>
      <slot name="table-append" />
    </v-card-text>
  </v-card>
</template>

<script>
import axios from 'axios'
import { mapGetters, mapActions } from 'vuex'
import { get } from '~/plugins/utils'
import * as format from '~/plugins/format'
import FilterMenu from './Filters/FilterMenu'
import SearchMenu from './SearchMenu'

export default {
  name: 'DataTable',
  components: { SearchMenu, FilterMenu },
  props: {
    title: {
      type: String,
      required: true
    },
    api: {
      type: String,
      required: true
    },
    filters: {
      type: Array,
      required: false,
      default: () => []
    },
    headers: {
      type: Array,
      required: true
    },
    sortBy: {
      type: String,
      required: false,
      default: 'id'
    },
    sortDesc: {
      type: Boolean,
      required: false,
      default: true
    },
    search: {
      type: Boolean,
      required: false,
      default: false
    },
    searchPlaceholder: {
      type: String,
      required: false,
      default: null
    },
    hideFooter: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      loading: true,
      options: {},
      items: [],
      itemsTotal: 0
    }
  },

  computed: {
    ...mapGetters({ cacheGet: 'cache/get' }),
    cacheKey () {
      return 'datatable.' + this.$route.name
    },
    footerProps () {
      return {
        'items-per-page-options': [5, 10, 25, 50],
        'items-per-page-text': this.$t('Rows per page')
      }
    },
    sortDirection () {
      return this.options.sortDesc ? 'desc' : 'asc'
    },
    // Get slot => header map
    slotHeaderMap () {
      return this.headers.reduce((obj, header) => {
        obj['item.' + header.value] = header
        return obj
      }, {})
    }
  },

  created () {
    // get current pagination & sorting settings from cache or initialize with defaults
    this.options = this.cacheGet(this.cacheKey) || {
      page: 1,
      itemsPerPage: 10,
      sortBy: this.sortBy,
      sortDesc: this.sortDesc,
      search: null,
      filters: {}
    }
  },

  methods: {
    ...mapActions({ cachePut: 'cache/put' }),
    get (...args) {
      return get(...args)
    },
    format (type, value) {
      return typeof format[type] === 'function' ? format[type](value) : value
    },
    updateDataTableOptions ({ page, itemsPerPage, sortBy, sortDesc }) {
      this.options = { ...this.options, page, itemsPerPage, sortBy: sortBy[0], sortDesc: sortDesc[0] }
      this.cacheOptions()
      this.fetchData()
    },
    // save current filters, search & sort settings into cache
    cacheOptions () {
      // don't save filters & search in cache, because it's difficult to initialize their default values after user left the page
      const options = { ...this.options, filters: {}, search: null }
      this.cachePut({ key: this.cacheKey, value: options })
    },
    filterData (filters) {
      this.options.filters = filters
      this.fetchData()
    },
    searchData (text) {
      this.options.search = text
      this.fetchData()
    },
    async fetchData () {
      this.loading = true

      const { data } = await axios.get(this.api, {
        params: {
          page: this.options.page,
          items_per_page: this.options.itemsPerPage,
          sort_by: this.options.sortBy,
          sort_direction: this.sortDirection,
          search: this.options.search,
          ...this.options.filters
        }
      })

      this.items = data.items
      this.itemsTotal = data.count

      this.loading = false
    }
  }
}
</script>

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
        <v-icon :color="Object.keys(values).length ? 'primary' : undefined">
          mdi-filter-outline
        </v-icon>
      </v-btn>
    </template>
    <v-card outlined>
      <v-card-title>
        {{ $t('Filter') }}
      </v-card-title>
      <v-divider />
      <v-card-text class="pt-5">
        <component
          :is="`${filter}-filter`"
          v-for="filter in filters"
          :key="`${filter}-${key}`"
          @change="change($event)"
        />
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
          @click="apply"
        >
          {{ $t('Apply') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-menu>
</template>

<script>
import PeriodFilter from './PeriodFilter'
import ComparisonPeriodFilter from './ComparisonPeriodFilter'
import UserRoleFilter from './UserRoleFilter'
import UserStatusFilter from './UserStatusFilter'
import GameFilter from './GameFilter'
import GameResultFilter from './GameResultFilter'
import DepositWithdrawalStatusFilter from './DepositWithdrawalStatusFilter'
import AffiliateCommissionTypeFilter from './AffiliateCommissionTypeFilter'
import AffiliateCommissionStatusFilter from './AffiliateCommissionStatusFilter'

export default {
  components: {
    PeriodFilter,
    ComparisonPeriodFilter,
    UserRoleFilter,
    UserStatusFilter,
    GameFilter,
    GameResultFilter,
    DepositWithdrawalStatusFilter,
    AffiliateCommissionTypeFilter,
    AffiliateCommissionStatusFilter
  },

  props: {
    filters: {
      type: Array,
      required: true
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
      values: {},
      key: 1
    }
  },

  methods: {
    change (object) {
      this.values = { ...this.values, ...object }
    },
    reset () {
      // force filter filters re-load and reset all values to defaults by incrementing the key
      this.key++
      this.values = {}
      this.apply()
    },
    apply () {
      this.menu = false // close menu
      this.$emit('apply', this.values)
    }
  }
}
</script>

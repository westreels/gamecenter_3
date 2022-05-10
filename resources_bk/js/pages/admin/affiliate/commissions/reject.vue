<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="6">
        <v-card>
          <v-toolbar>
            <v-btn icon @click="$router.go(-1)">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Affiliate commission {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <affiliate-commission-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <p>
              {{ $t('Are you sure you want to reject this commission?') }}
            </p>
            <v-form @submit.prevent="reject">
              <v-btn type="submit" color="error" :disabled="form.busy" :loading="form.busy">{{ $t('Reject') }}</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import Form from 'vform'
import AffiliateCommissionMenu from '~/components/Admin/AffiliateCommissionMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { AffiliateCommissionMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Affiliate commission {0}', [this.id]) }
  },

  data () {
    return {
      form: new Form()
    }
  },

  methods: {
    async reject () {
      const { data } = await this.form.post(`/api/admin/affiliate/commissions/${this.id}/reject`)

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })

      this.$router.push({ name: 'admin.affiliate.commissions.index' })
    }
  }
}
</script>

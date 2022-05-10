<template/>
<script>
import axios from 'axios'

const qs = (params) => Object.keys(params).map(key => `${key}=${params[key]}`).join('&')

export default {
  middleware: ['auth', 'not_verified'],

  metaInfo () {
    return { title: this.$t('Email verification') }
  },

  async beforeRouteEnter (to, from, next) {
    try {
      const { data } = await axios.post(`/api/email/verify/${to.params.id}/${to.params.hash}?${qs(to.query)}`)

      // there is no access to this in beforeRouteEnter()
      next(vm => {
        // Update the user
        vm.$store.dispatch('auth/updateUser', data)

        vm.$store.dispatch('message/success', { text: vm.$t('Your email address is successfully verified.') })

        vm.$router.push({ name: 'home' })
      })
    } catch (e) {
      next(vm => {
        vm.$store.dispatch('message/error', { text: e.response.data.message })

        vm.$router.push({ name: 'verification.index' })
      })
    }
  }
}
</script>

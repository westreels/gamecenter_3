<script>
export default {
  computed: {
    isMobile () {
      return this.$vuetify.breakpoint.width < 960
    }
  }
}
</script>

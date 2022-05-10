<template>
  <span>{{ timer }}</span>
</template>
<script>
export default {
  props: {
    endDate: {
      type: Number,
      required: true
    }
  },

  data () {
    return {
      timer: null,
      interval: null
    }
  },

  watch: {
    endDate (endDate) {
      if (endDate) {
        this.destroy()
        this.create()
      }
    }
  },

  created () {
    this.create()
  },

  beforeDestroy () {
    this.destroy()
  },

  methods: {
    create () {
      this.tick()
      this.interval = setInterval(this.tick, 1000)
    },
    destroy () {
      clearInterval(this.interval)
    },
    tick () {
      let diff = Math.max(0, Math.round(this.endDate - Date.now() / 1000))
      let seconds = Math.floor(diff % 60)
      let minutes = Math.floor((diff / 60) % 60)
      let hours = Math.floor((diff / (60 * 60)) % 24)
      let days = Math.floor(diff / (60 * 60 * 24))

      seconds = seconds < 10 ? '0' + seconds : seconds
      minutes = minutes < 10 ? '0' + minutes : minutes
      hours = hours < 10 ? '0' + hours : hours

      this.timer = (days > 0 ? `${days}${this.$t('d')} : ` : '') +
        (hours > 0 || days > 0 ? `${hours}${this.$t('h')} : ` : '') +
        `${minutes}${this.$t('m')} : ${seconds}${this.$t('s')}`

      // clear interval if time elapsed
      if (diff === 0 && this.interval) {
        clearInterval(this.interval)
      }
    }
  }
}
</script>

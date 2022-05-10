<script>
import { mapState } from 'vuex'
import { Howl } from 'howler'

export default {
  data () {
    return {
      howl: {}
    }
  },

  computed: {
    ...mapState('settings', ['settings'])
  },

  methods: {
    sound (path) {
      if (this.settings.sounds) {
        if (!this.howl[path]) {
          this.howl[path] = new Howl({ src: [path] })
        }
        this.howl[path].play()
      }
    },
    soundLoop (path) {
      if (this.settings.sounds) {
        if (!this.howl[path]) {
          this.howl[path] = new Howl({ src: [path] })
        }
        this.howl[path].loop(true)
        this.howl[path].play()
      }
    },
    soundStop (path) {
      if (this.howl[path]) {
        this.howl[path].stop()
        this.howl[path].loop(false)
      }
    }
  }
}
</script>

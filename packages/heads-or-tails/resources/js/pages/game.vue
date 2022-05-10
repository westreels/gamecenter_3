<template>
  <div class="d-flex flex-column fill-height py-3">
    <div class="message-container d-flex justify-center align-center">
      <div class="message">
        <game-message :message="message" />
      </div>
    </div>
    <div class="d-flex justify-center fill-height align-center">
      <div ref="animation" class="animation" />
    </div>
    <div class="d-flex justify-center flex-column">
      <v-btn-toggle
        v-model="tossBet"
        active-class="primary--text"
        mandatory
        rounded
        group
        class="align-self-center mb-3"
      >
        <v-btn
          :disabled="playing || !is_ready"
          class="mx-1"
          small
          :value="0"
          @click="sound(clickSound)"
        >
          {{ $t('Heads') }}
        </v-btn>
        <v-btn
          :disabled="playing || !is_ready"
          class="mx-1"
          small
          :value="1"
          @click="sound(clickSound)"
        >
          {{ $t('Tails') }}
        </v-btn>
      </v-btn-toggle>
    </div>
    <play-controls :loading="loading" :playing="playing" :disabled="!formIsValid || !is_ready" @play="play" />
  </div>
</template>

<script>
import * as CANNON from 'cannon'
import * as THREE from 'three'
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import { config } from '~/plugins/config'
import { decimal } from '~/plugins/format'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import { sleep } from '~/plugins/utils'
import clickSound from '~/../audio/common/click.wav'
import winSound from 'packages/heads-or-tails/resources/audio/win.wav'
import loseSound from 'packages/heads-or-tails/resources/audio/lose.wav'
import hit1Sound from 'packages/heads-or-tails/resources/audio/hit1.wav'
import hit2Sound from 'packages/heads-or-tails/resources/audio/hit2.wav'
import PlayControls from '~/components/Games/PlayControls'
import GameMessage from '~/components/Games/GameMessage'

export default {
  name: 'HeadsOrTails',

  components: { GameMessage, PlayControls },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      clickSound,
      winSound,
      loseSound,
      hit1Sound,
      hit2Sound,
      formIsValid: true,
      loading: false,
      playing: false,
      is_ready: false,
      tossBet: 0,
      tossResult: null,
      result: null,
      message: null,
      animation: {}
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    houseEdge () {
      return config('heads-or-tails.house_edge')
    },
    headsImageUrl () {
      return config('heads-or-tails.images.heads')
    },
    tailsImageUrl () {
      return config('heads-or-tails.images.tails')
    },
    edgeImageUrl () {
      return config('heads-or-tails.images.edge')
    },
    cameraAngle () {
      return config('heads-or-tails.animation.camera_angle')
    },
    cameraPosition () {
      return config('heads-or-tails.animation.camera_position')
    },
    throwHeight () {
      return config('heads-or-tails.animation.throw_height')
    }
  },
  async mounted () {
    window.hot = this
    this.animation = {
      pos: null,
      velocity: null,
      container: null,
      scene: null,
      camera: null,
      renderer: null,
      world: null,
      floorWidth: 0,
      floorHeight: 0,
      width: this.$refs.animation.offsetWidth,
      height: this.$refs.animation.offsetHeight,
      aspect: this.$refs.animation.offsetWidth / this.$refs.animation.offsetHeight,
      camera_angle: this.cameraAngle,
      camera_position: 100,
      near: 0.01,
      far: 10000,
      animationStep: 0,
      lastSound: 0,
      isAnimation: false,
      coin: null,
      coin_body: null,
      coin_radius: 5,
      coin_height: 0.3,
      coin_top: null,
      coin_bottom: null,
      coin_top_texture: null,
      coin_bottom_texture: null,
      coin_side_texture: null,
      result: null,
      frames: [],
      animate: () => {
        if (this.animation.isThrought) {
          if (this.animation.frames.length > this.animation.animationStep) {
            this.animation.coin.position.copy(this.animation.frames[this.animation.animationStep].position)
            this.animation.coin.quaternion.copy(this.animation.frames[this.animation.animationStep].quaternion)
            if (this.animation.frames[this.animation.animationStep].is_contact && this.animation.lastSound < this.animation.animationStep) {
              if (this.animation.lastSound === 0) this.sound(this.hit1Sound)
              else this.sound(this.hit2Sound)
              this.animation.lastSound = this.animation.animationStep + 5
            }
            this.animation.animationStep++
          } else {
            this.updateUserAccountBalance(this.result.balance)
            this.sound(this.result.sound)
            this.message = this.result.message
            this.animation.animationStep = 0
            this.animation.frames = []
            this.animation.lastSound = 0
            this.animation.isThrought = false
            this.result = null
            this.playing = false
          }
        }
        this.animation.renderer.render(this.animation.scene, this.animation.camera)
        this.animation.coin.position.copy(this.animation.coin_body.position)
        this.animation.coin.quaternion.copy(this.animation.coin_body.quaternion)
        requestAnimationFrame(this.animation.animate)
      }
    }
    this.animation.scene = new THREE.Scene()
    this.animation.camera = new THREE.PerspectiveCamera(this.animation.camera_angle, this.animation.aspect, this.animation.near, this.animation.far)
    this.animation.scene.add(this.animation.camera)
    this.animation.camera.position.set(0, this.animation.camera_position, 100)
    this.animation.camera.lookAt(0, 0, this.cameraPosition)
    this.animation.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true })
    this.animation.renderer.setClearColor(0x000000, 0)
    this.animation.renderer.setSize(this.animation.width * 2, this.animation.height * 2)
    this.animation.renderer.shadowMap.enabled = true
    this.animation.renderer.shadowMap.type = THREE.PCFSoftShadowMap
    this.animation.container = this.$refs.animation
    this.animation.container.appendChild(this.animation.renderer.domElement)

    const ambient = new THREE.AmbientLight('#ffffff', 0.5)
    this.animation.scene.add(ambient)

    const directionalLight = new THREE.DirectionalLight('#ffffff', 0.5)
    directionalLight.position.x = -1000
    directionalLight.position.y = 1000
    directionalLight.position.z = 1000
    this.animation.scene.add(directionalLight)

    const light = new THREE.SpotLight(0xefdfd5, 0.75)
    light.position.y = 60
    light.target.position.set(0, 0, 0)
    light.castShadow = true
    light.shadow.camera.near = 50
    light.shadow.camera.far = 210
    light.shadow.mapSize.width = 1024
    light.shadow.mapSize.height = 1024
    this.animation.scene.add(light)

    this.animation.floorHeight = 2 * Math.tan(this.animation.camera.fov * Math.PI / 180 / 2) * Math.abs(this.animation.camera.position.y)
    this.animation.floorWidth = this.animation.floorHeight * this.animation.camera.aspect
    this.animation.world = new CANNON.World()
    this.animation.world.gravity.set(0, -9.82 * 10, 0)
    this.animation.world.broadphase = new CANNON.NaiveBroadphase()
    this.animation.world.solver.iterations = 16

    this.animation.coinBodyMaterial = new CANNON.Material()
    this.animation.floorBodyMaterial = new CANNON.Material()
    this.animation.barrierBodyMaterial = new CANNON.Material()
    this.animation.world.addContactMaterial(
        new CANNON.ContactMaterial(this.animation.floorBodyMaterial, this.animation.coinBodyMaterial, { friction: 0.001, restitution: 0.5 })
    )
    this.animation.world.addContactMaterial(
        new CANNON.ContactMaterial(this.animation.barrierBodyMaterial, this.animation.coinBodyMaterial, { friction: 0.001, restitution: 0.5 })
    )
    // Floor
    this.animation.floorBody = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: this.animation.floorBodyMaterial })
    this.animation.floorBody.quaternion.setFromAxisAngle(new CANNON.Vec3(1, 0, 0), -Math.PI / 2)
    this.animation.world.add(this.animation.floorBody)
    // Barrier
    var barrier
    const barrierCorner = 0.85
    barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: this.animation.barrierBodyMaterial })
    barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), -Math.PI)
    barrier.position.set(0, 0, this.animation.floorHeight * barrierCorner / 2 - 5)
    this.animation.world.add(barrier)
    barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: this.animation.barrierBodyMaterial })
    barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), 0)
    barrier.position.set(0, 0, -(this.animation.floorHeight * barrierCorner / 2) - 5)
    this.animation.world.add(barrier)
    window.barrier = barrier
    window.CANNON = CANNON
    barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: this.animation.barrierBodyMaterial })
    barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), -Math.PI / 2)
    barrier.position.set(this.animation.floorWidth * barrierCorner / 2, 0, -5)
    this.animation.world.add(barrier)
    barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: this.animation.barrierBodyMaterial })
    barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), Math.PI / 2)
    barrier.position.set(-(this.animation.floorWidth * barrierCorner / 2), 0, -5)
    this.animation.world.add(barrier)
    // Coin
    const resizeImage = (image) => {
      const width = THREE.Math.floorPowerOfTwo(image.width * 2)
      const height = THREE.Math.floorPowerOfTwo(image.height * 2)
      const canvas = document.createElementNS('http://www.w3.org/1999/xhtml', 'canvas')
      canvas.width = width
      canvas.height = height
      const context = canvas.getContext('2d')
      context.drawImage(image, 0, 0, width, height)
      return canvas
    }
    this.animation.coin_top_texture = await (new Promise((resolve, reject) => {
          var img = new Image()
          img.onload = () => resolve(resizeImage(img))
          img.onerror = reject
          img.src = this.headsImageUrl
        }))
    this.animation.coin_bottom_texture = await (new Promise((resolve, reject) => {
          var img = new Image()
          img.onload = () => resolve(resizeImage(img))
          img.onerror = reject
          img.src = this.tailsImageUrl
        }))
    this.animation.coin_side_texture = await (new Promise((resolve, reject) => {
          var img = new Image()
          img.onload = () => resolve(resizeImage(img))
          img.onerror = reject
          img.src = this.edgeImageUrl
        }))
    this.animation.coin = new THREE.Object3D()
    const coinSides = new THREE.CylinderGeometry(this.animation.coin_radius, this.animation.coin_radius, this.animation.coin_height, 100, 1, true)
    const sideTexture = new THREE.Texture(this.animation.coin_side_texture)
    sideTexture.needsUpdate = true
    const sidesMat = new THREE.MeshLambertMaterial({ map: sideTexture })
    const sidesMesh = new THREE.Mesh(coinSides, sidesMat)
    this.animation.coin.add(sidesMesh)
    this.animation.coin_swap = (swap) => {
      if (swap === undefined) swap = false
      if (this.animation.coin_top !== null) this.animation.coin.add(this.animation.coin_top)
      const capTopGeo = new THREE.CircleGeometry(this.animation.coin_radius, 100)
      const capTopTexture = new THREE.Texture(swap ? this.animation.coin_bottom_texture : this.animation.coin_top_texture)
      capTopTexture.needsUpdate = true
      const capTopMat = new THREE.MeshLambertMaterial({ map: capTopTexture })
      this.animation.coin_top = new THREE.Mesh(capTopGeo, capTopMat)
      this.animation.coin_top.position.y = this.animation.coin_height / 2
      this.animation.coin_top.rotation.x = -Math.PI / 2
      this.animation.coin.add(this.animation.coin_top)
      if (this.animation.coin_bottom !== null) this.animation.coin.add(this.animation.coin_bottom)
      const capBottomGeo = new THREE.CircleGeometry(this.animation.coin_radius, 100)
      const capBottomTexture = new THREE.Texture(swap ? this.animation.coin_top_texture : this.animation.coin_bottom_texture)
      capBottomTexture.needsUpdate = true
      const capBottomMat = new THREE.MeshLambertMaterial({ map: capBottomTexture })
      this.animation.coin_bottom = new THREE.Mesh(capBottomGeo, capBottomMat)
      this.animation.coin_bottom.position.y = -this.animation.coin_height / 2
      this.animation.coin_bottom.rotation.x = Math.PI / 2
      this.animation.coin.add(this.animation.coin_bottom)
    }
    this.animation.coin_swap()
    this.animation.scene.add(this.animation.coin)
    this.animation.coin_body = new CANNON.Body({ mass: 600, shape: new CANNON.Cylinder(this.animation.coin_radius, this.animation.coin_radius, this.animation.coin_height, 100), material: this.animation.coinBodyMaterial })
    this.animation.coin_body.linearDamping = 0.1
    this.animation.coin_body.angularDamping = 0.1
    var quat = new CANNON.Quaternion()
    quat.setFromAxisAngle(new CANNON.Vec3(1, 0, 0), -Math.PI / 2)
    var translation = new CANNON.Vec3(0, 0, 0)
    this.animation.coin_body.shapes[0].transformAllPoints(translation, quat)
    this.animation.throw = (result) => { // 0 - heads, 1 - tails
      this.animation.coin_body.velocity.set(0, this.throwHeight, 0)
      this.animation.coin_body.angularVelocity.set(25 * Math.random() - 25, Math.random(), 25 * Math.random() - 25)
      this.animation.coin_body.position.set(0, 10, 0)
      this.animation.frames = []
      var isContacts = false
      const collide = () => { isContacts = true }
      this.animation.floorBody.addEventListener('collide', collide)
      var stableCount = 0
      while (stableCount < 10) {
        this.animation.frames.push({
          position: this.animation.coin_body.position.clone(),
          quaternion: this.animation.coin_body.quaternion.clone(),
          is_contact: isContacts
        })
        isContacts = false
        this.animation.world.step(1.00 / 30.00)
        if (this.animation.isFinished()) stableCount++
        else stableCount = 0
      }
      this.animation.floorBody.removeEventListener('collide', collide)
      const side = this.animation.getSide()
      if (side > Math.PI / 2 * 0.7 && side < Math.PI / 2 * 1.3) return this.animation.throw(result)
      this.animation.coin_swap(((side < Math.PI / 2) ? 1 : 0) === result)
      this.animation.isThrought = true
    }
    this.animation.getSide = () => {
      const vectorTop = new THREE.Vector3(0, 1, 0)
      var vector = new THREE.Vector3(0, 1, 0)
      vector.applyQuaternion(this.animation.coin_body.quaternion)
      return vectorTop.angleTo(vector)
    }
    this.animation.isFinished = () => {
        const threshold = 0.5
        const angularVelocity = this.animation.coin_body.angularVelocity
        const velocity = this.animation.coin_body.velocity
        return (
                    Math.abs(angularVelocity.x) < threshold &&
                    Math.abs(angularVelocity.y) < threshold &&
                    Math.abs(angularVelocity.z) < threshold &&
                    Math.abs(velocity.x) < threshold &&
                    Math.abs(velocity.y) < threshold &&
                    Math.abs(velocity.z) < threshold)
    }
    this.animation.world.add(this.animation.coin_body)
    this.animation.animate()
    this.is_ready = true
  },
  beforeDestroy () {
    this.animation.animate = () => {}
  },
  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    async play (bet) {
      this.loading = true
      this.playing = true
      this.message = null

      // update user balance
      this.updateUserAccountBalance(this.account.balance - bet)

      await sleep(10)

      // API request params
      const endpoint = this.getRoute('play')
      const requestParams = { hash: this.provablyFairGame.hash, bet, toss_bet: this.tossBet }

      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)
      this.loading = false
      this.result = {
        balance: game.account.balance,
        win: game.win,
        message: null,
        sound: null
      }
      if (game.win > 0) {
        this.result.sound = winSound
        this.result.message = game.gameable.result + ', ' + this.$t('you won') + ' ' + game.win
      } else {
        this.result.sound = loseSound
        this.result.message = game.gameable.result + ', ' + this.$t('you lost')
      }
      // 0 - heads, 1 - tails
      this.tossResult = game.gameable.toss_result
      this.animation.throw(this.tossResult)
      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })
    }
  }
}
</script>

<style lang="scss" scoped>
  .animation {
    width:100%;
    max-width:1200px;
    height:350px;
    &::v-deep canvas {
      width: 100% !important;
      height: auto !important;
    }
  }
  .message-container {
    position:relative;
    height:0;
    z-index:2;
    .message {
      position:absolute;
      top:5px;
      left:50%;
      transform: translateX(-50%);
    }
  }
</style>

<template>
  <div class="d-flex flex-column fill-height py-3">
    <div class="message-container">
      <game-message class="message" :message="message" />
    </div>
    <div class="d-flex justify-center fill-height align-center">
      <div ref="ThreeJS" class="animation" :click="play" />
    </div>
    <div class="d-flex justify-center flex-column">
      <v-slider
        v-model="refNumber"
        thumb-label="always"
        :min="minRefNumber"
        :max="maxRefNumber"
        :step="1"
        :track-color="sliderTrackColor"
        :track-fill-color="sliderFillColor"
        class="px-5"
      >
        <template v-slot:prepend>
          <v-icon @click="decrementRefNumber">
            mdi-minus
          </v-icon>
        </template>
        <template v-slot:append>
          <v-icon @click="incrementRefNumber">
            mdi-plus
          </v-icon>
        </template>
      </v-slider>
      <div class="align-self-center">
        <v-form v-model="formIsValid" class="d-md-flex">
          <v-col cols="12" md="6" class="pa-0">
            <v-text-field
              :value="winChance"
              dense
              outlined
              :full-width="false"
              class="text-center flex-grow-0 mr-0 mr-lg-2"
              :label="$t('Win chance')"
              :disabled="true"
            />
          </v-col>
          <v-col cols="12" md="6" class="pa-0">
            <v-text-field
              :value="payout"
              dense
              outlined
              :full-width="false"
              class="text-center flex-grow-0 ml-0 ml-lg-2"
              :label="$t('Payout')"
              :disabled="true"
            />
          </v-col>
        </v-form>
      </div>
      <v-btn-toggle
        v-model="direction"
        active-class="primary--text"
        mandatory
        rounded
        group
        class="align-self-center mb-3"
      >
        <v-btn
          :disabled="playing"
          class="mx-1"
          small
          :value="-1"
          @click="sound(clickSound)"
        >
          {{ rollLessText }}
        </v-btn>
        <v-btn
          :disabled="playing"
          class="mx-1"
          small
          :value="1"
          @click="sound(clickSound)"
        >
          {{ rollGreaterText }}
        </v-btn>
      </v-btn-toggle>
    </div>
    <play-controls :loading="loading" :playing="playing" :disabled="!formIsValid" @bet-change="bet = $event" @play="play" />
  </div>
</template>

<script>
import * as CANNON from 'cannon'
import * as THREE from 'three'
import { DiceD4, DiceD6, DiceD8, DiceD10, DiceD12, DiceD20, DiceManager } from 'packages/dice-3d/resources/js/components/dice.js'
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import { config } from '~/plugins/config'
import { decimal } from '~/plugins/format'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import { round } from '~/plugins/utils'
import clickSound from '~/../audio/common/click.wav'
import winSound from 'packages/dice-3d/resources/audio/win.wav'
import roll1Sound from 'packages/dice-3d/resources/audio/roll1.wav'
import roll2Sound from 'packages/dice-3d/resources/audio/roll2.wav'
import loseSound from 'packages/dice-3d/resources/audio/lose.wav'
import PlayControls from '~/components/Games/PlayControls'
import GameMessage from '~/components/Games/GameMessage'

export default {
  name: 'Dice3D',

  components: { GameMessage, PlayControls },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      clickSound,
      roll1Sound,
      roll2Sound,
      formIsValid: true,
      loading: false,
      playing: false,
      refNumber: null,
      direction: -1,
      diceFillColor: config('dice-3d.fill_color'),
      diceFontColor: config('dice-3d.font_color'),
      bet: parseInt(config('dice-3d.default_bet_amount'), 10),
      roll: 0,
      message: null,
      animation: {}
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    dices () {
      return config('dice-3d.dice')
    },
    minRoll () {
      return config('dice-3d.dice').length
    },
    maxRoll () {
      return config('dice-3d.dice').reduce((a, id) => { a += config('dice-3d.dice_types')[id].max; return a }, 0)
    },
    rollsRangeLength () {
      return this.maxRoll - this.minRoll + 1
    },
    minRefNumber () {
      return this.minRoll + 1
    },
    maxRefNumber () {
      return this.maxRoll
    },
    houseEdge () {
      return config('dice-3d.house_edge')
    },
    winChance () {
      const winChance = this.direction === -1
        ? (this.refNumber - this.minRoll) / this.rollsRangeLength * 100
        : (this.maxRoll - this.refNumber + 1) / this.rollsRangeLength * 100

      return round(winChance, 2)
    },
    payout () {
      const payout = (100 - this.houseEdge) / this.winChance
      return round(payout, 4)
    },
    rollLessText () {
      return this.minRoll < this.refNumber - 1
        ? this.$t('Roll {0} - {1}', [this.minRoll, this.refNumber - 1])
        : this.$t('Roll {0}', [this.refNumber - 1])
    },
    rollGreaterText () {
      return this.maxRoll > this.refNumber
        ? this.$t('Roll {0} - {1}', [this.refNumber, this.maxRoll])
        : this.$t('Roll {0}', [this.refNumber])
    },
    sliderTrackColor () {
      return this.direction === -1 ? 'grey' : 'primary'
    },
    sliderFillColor () {
      return this.direction === -1 ? 'primary' : 'grey'
    }
  },

  created () {
    // trigger change
    this.refNumber = this.minRoll + Math.floor((this.maxRoll - this.minRoll) / 2)
  },
  mounted () {
    window.dice = this
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
      width: this.$refs.ThreeJS.offsetWidth,
      height: this.$refs.ThreeJS.offsetHeight,
      aspect: this.$refs.ThreeJS.offsetWidth / this.$refs.ThreeJS.offsetHeight,
      camera_angle: 10,
      camera_position: 100,
      near: 0.01,
      far: 10000,
      diceValues: [],
      diceAnimationStep: 0,
      isThrought: false,
      dice: [],
      animate: () => {
        var i
        if (this.animation.isThrought) {
          for (i in this.animation.dice) {
            if (this.animation.diceValues[i].steps.length > this.animation.diceAnimationStep) this.animation.dice[i].setMeshFrom(this.animation.diceValues[i].steps[this.animation.diceAnimationStep])
            if (this.animation.diceValues[i].sound.length > this.animation.diceAnimationStep && this.animation.diceValues[i].sound[this.animation.diceAnimationStep] && this.animation.diceValues[i].lastSound < this.animation.diceAnimationStep) {
              if (this.animation.diceValues[i].lastSound === 0) this.sound(this.roll1Sound)
              else this.sound(this.roll2Sound)
              this.animation.diceValues[i].lastSound = this.animation.diceAnimationStep + 5
            }
          }
          this.animation.diceAnimationStep++
        }
        this.animation.renderer.render(this.animation.scene, this.animation.camera)
        requestAnimationFrame(this.animation.animate)
        if (this.animation.isThrought) {
          var isFinished = true
          for (i = 0; i < this.animation.dice.length; i++) isFinished = isFinished && !(this.animation.diceValues[i].steps.length > this.animation.diceAnimationStep)
          if (isFinished) {
            this.playing = false
            this.updateUserAccountBalance(this.animation.result.balance)
            if (this.animation.result.win > 0) {
              this.sound(winSound)
              this.message = this.$t('You rolled') + ' ' + this.roll + ' ' + this.$t('and won') + ' ' + decimal(this.animation.result.win)
            } else {
              this.sound(loseSound)
              this.message = this.$t('You rolled') + ' ' + this.roll
            }
            this.animation.isThrought = false
          }
        }
      }
    }
    this.animation.scene = new THREE.Scene()
    this.animation.camera = new THREE.PerspectiveCamera(this.animation.camera_angle, this.animation.aspect, this.animation.near, this.animation.far)
    this.animation.scene.add(this.animation.camera)
    this.animation.camera.position.set(0, this.animation.camera_position, 0)
    this.animation.camera.lookAt(0, 0, 0)
    this.animation.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true })
    this.animation.renderer.setClearColor(0x000000, 0)
    this.animation.renderer.setSize(this.animation.width * 2, this.animation.height * 2)
    this.animation.renderer.shadowMap.enabled = true
    this.animation.renderer.shadowMap.type = THREE.PCFSoftShadowMap
    this.animation.container = this.$refs.ThreeJS
    this.animation.container.appendChild(this.animation.renderer.domElement)

    const ambient = new THREE.AmbientLight('#ffffff', 0.5)
    this.animation.scene.add(ambient)

    const directionalLight = new THREE.DirectionalLight('#ffffff', 0.2)
    directionalLight.position.x = -1000
    directionalLight.position.y = 1000
    directionalLight.position.z = 1000
    this.animation.scene.add(directionalLight)

    const light = new THREE.SpotLight(0xefdfd5, 0.5)
    light.position.y = 200
    light.target.position.set(0, 0, 0)
    light.castShadow = true
    light.shadow.camera.near = 50
    light.shadow.camera.far = 110
    light.shadow.mapSize.width = 1024
    light.shadow.mapSize.height = 1024
    this.animation.scene.add(light)

    this.animation.floorHeight = 2 * Math.tan(this.animation.camera.fov * Math.PI / 180 / 2) * Math.abs(this.animation.camera.position.y)
    this.animation.floorWidth = this.animation.floorHeight * this.animation.camera.aspect
    // var floorMaterial = new THREE.MeshPhongMaterial({ color: '#00aa00', side: THREE.DoubleSide })
    // var floorGeometry = new THREE.PlaneGeometry(floorWidth * 0.95, floorHeight * 0.95, 10, 10)
    // var floor = new THREE.Mesh(floorGeometry, floorMaterial)
    // floor.receiveShadow = true
    // floor.rotation.x = Math.PI / 2
    // scene.add(floor)
    this.animation.world = new CANNON.World()
    this.animation.world.gravity.set(0, -9.82 * 10, 0)
    this.animation.world.broadphase = new CANNON.NaiveBroadphase()
    this.animation.world.solver.iterations = 16
    DiceManager.setWorld(this.animation.world)
    // Floor
    const floorBody = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: DiceManager.floorBodyMaterial })
    floorBody.quaternion.setFromAxisAngle(new CANNON.Vec3(1, 0, 0), -Math.PI / 2)
    this.animation.world.add(floorBody)
    // floorBody.addEventListener("collide",() => {
    //   this.sound(this.clickSound)
    // })
    // Walls
    var barrier
    const barrierCorner = 0.85
    barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: DiceManager.barrierBodyMaterial })
    barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), -Math.PI)
    barrier.position.set(0, 0, this.animation.floorHeight * barrierCorner / 2)
    this.animation.world.add(barrier)
    barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: DiceManager.barrierBodyMaterial })
    barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), 0)
    barrier.position.set(0, 0, -(this.animation.floorHeight * barrierCorner / 2))
    this.animation.world.add(barrier)
    window.barrier = barrier
    window.CANNON = CANNON
    barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: DiceManager.barrierBodyMaterial })
    barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), -Math.PI / 2)
    barrier.position.set(this.animation.floorWidth * barrierCorner / 2, 0, 0)
    this.animation.world.add(barrier)
    barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: DiceManager.barrierBodyMaterial })
    barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), Math.PI / 2)
    barrier.position.set(-(this.animation.floorWidth * barrierCorner / 2), 0, 0)
    this.animation.world.add(barrier)
    // DICE COLORS
    // const fontColor = this.$vuetify.theme.isDark ? '#ffffff' : '#000000'
    const fontColor = this.diceFontColor
    // const backColor = getComputedStyle(document.body).getPropertyValue('--v-primary-base').trim()
    const backColor = this.diceFillColor
    // const backColors = ['#ff0000','#00ff00','#0000ff','#777700','#770077','#007777']
    this.dices.forEach((dice, i) => {
      var d
      switch (dice) {
        case 'tetrahedron':
          d = new DiceD4({ size: 1.5, backColor: backColor, fontColor: fontColor })
          // d = new DiceD4({ size: 1.5, backColor: backColors[i], fontColor: fontColor })
          break
        case 'cube':
          d = new DiceD6({ size: 1.5, backColor: backColor, fontColor: fontColor })
          // d = new DiceD6({ size: 1.5, backColor: backColors[i], fontColor: fontColor })
          break
        case 'octahedron':
          d = new DiceD8({ size: 1.5, backColor: backColor, fontColor: fontColor })
          // d = new DiceD8({ size: 1.5, backColor: backColors[i], fontColor: fontColor })
          break
        case 'dipyramid':
          d = new DiceD10({ size: 1.5, backColor: backColor, fontColor: fontColor })
          // d = new DiceD10({ size: 1.5, backColor: backColors[i], fontColor: fontColor })
          break
        case 'dodecahedron':
          d = new DiceD12({ size: 1.5, backColor: backColor, fontColor: fontColor })
          // d = new DiceD12({ size: 1.5, backColor: backColors[i], fontColor: fontColor })
          break
        case 'icosahedron':
          d = new DiceD20({ size: 1.5, backColor: backColor, fontColor: fontColor })
          // d = new DiceD20({ size: 1.5, backColor: backColors[i], fontColor: fontColor })
          break
      }
      this.animation.scene.add(d.getObject())
      this.animation.dice.push(d)
    })
    this.animation.diceThrow = () => {
      if (this.animation.pos == null) this.animation.randomDiceThrow()
      const pos = this.animation.pos
      const velocity = this.animation.velocity
      this.animation.isThrought = true
      this.animation.diceAnimationStep = 0
      this.animation.diceValues = []
      if (Math.abs(velocity.x) > 100) velocity.x /= (Math.abs(velocity.x) / 100)
      if (Math.abs(velocity.y) > 100) velocity.y /= (Math.abs(velocity.y) / 100)
      if (Math.abs(velocity.z) > 100) velocity.z /= (Math.abs(velocity.z) / 100)
      var i
      for (i = 0; i < this.animation.dice.length; i++) {
          this.animation.dice[i].getObject().position.x = pos.x - (i % 3) * 1.5
          this.animation.dice[i].getObject().position.y = pos.y + Math.floor(i / 3) * 1.5
          this.animation.dice[i].getObject().position.z = pos.z + (i % 3) * 1.5
          // this.animation.dice[i].getObject().quaternion.x = (Math.random()*90-45) * Math.PI / 180;
          // this.animation.dice[i].getObject().quaternion.z = (Math.random()*90-45) * Math.PI / 180;
          this.animation.dice[i].updateBodyFromMesh()
          // const yRand = Math.random() * 10
          // const rand = Math.random() * 1 + 1
          this.animation.dice[i].getObject().body.velocity.set(
                          velocity.x,
                          velocity.y,
                          velocity.z)
          this.animation.dice[i].getObject().body.angularVelocity.set(
                          10 * Math.random() - 10,
                          10 * Math.random() - 10,
                          10 * Math.random() - 10)

          this.animation.diceValues.push({ dice: this.animation.dice[i], value: i + 1 })
      }
      var roll = this.roll
      for (i = 0; i < this.animation.diceValues.length; i++) {
        this.animation.diceValues[i].value = Math.ceil(Math.random() * Math.min(this.animation.diceValues[i].dice.values, (roll - (this.animation.diceValues.length - i - 1))))
        roll -= this.animation.diceValues[i].value
      }
      while (roll > 0) {
        for (i = 0; i < this.animation.diceValues.length; i++) {
          if (this.animation.diceValues[i].value < this.animation.diceValues[i].dice.values) {
            var add = Math.ceil(Math.random() * Math.min(this.animation.diceValues[i].dice.values - this.animation.diceValues[i].value, roll))
            this.animation.diceValues[i].value += add
            roll -= add
            if (roll <= 0) break
          }
        }
      }

      DiceManager.prepareValues(this.animation.diceValues, floorBody)
      this.animation.pos = null
      this.animation.velocity = null
    }
    const makeRandomVector = (vector) => {
      var rnd = Math.random()
      var randomAngle = rnd * Math.PI / 5 - Math.PI / 5 / 2
      var vec = {
          x: vector.x * Math.cos(randomAngle) - vector.y * Math.sin(randomAngle),
          y: vector.x * Math.sin(randomAngle) + vector.y * Math.cos(randomAngle)
      }
      if (vec.x === 0) vec.x = 0.01
      if (vec.y === 0) vec.y = 0.01
      return vec
    }
    this.animation.randomDiceThrow = () => {
      var rnd = Math.random()
      var vector = { x: (rnd * 2 - 1) * this.animation.floorWidth, y: -(rnd * 2 - 1) * this.animation.floorHeight }
      var dist = Math.sqrt(vector.x * vector.x + vector.y * vector.y)
      var boost = dist * 0.1 // (rnd ) * dist*0.05;

      var vec = makeRandomVector(vector)
      var pos = {
          x: this.animation.floorWidth / 2 * (vec.x > 0 ? -1 : 1) * 0.9,
          y: 10,
          z: this.animation.floorHeight / 2 * (vec.y > 0 ? -1 : 1) * 0.9
      }
      var projector = Math.abs(vec.x / vec.y)
      if (projector > 1.0) pos.z /= projector; else pos.x *= projector
      var velvec = makeRandomVector(vector)
      var velocity = { x: velvec.x * boost, y: -0.01, z: velvec.y * boost }
      this.animation.pos = pos
      this.animation.velocity = velocity
    }
    const getMouseCoords = ev => {
      var touches = ev.changedTouches
      if (touches) return { x: touches[0].clientX, y: touches[0].clientY }
      return { x: ev.clientX, y: ev.clientY }
    }
    const mouseStart = ev => {
      ev.preventDefault()
      if (this.playing) return
      this.animation.mouse_time = (new Date()).getTime()
      this.animation.mouse_start = getMouseCoords(ev)
    }
    const mouseEnd = ev => {
      if (this.animation.mouse_start === undefined) return
      ev.stopPropagation()
      var m = getMouseCoords(ev)
      var vector = { x: m.x - this.animation.mouse_start.x, y: (m.y - this.animation.mouse_start.y) }
      this.animation.mouse_start = undefined
      var dist = Math.sqrt(vector.x * vector.x + vector.y * vector.y)
      if (dist < Math.sqrt(this.animation.floorWidth * this.animation.floorHeight * 0.01)) return
      var timeInt = (new Date()).getTime() - this.animation.mouse_time
      if (timeInt > 2000) timeInt = 2000
      var boost = Math.sqrt((2500 - timeInt) / 2500) * dist * 0.001

      // var rnd = Math.random()
      var vec = makeRandomVector(vector)
      var pos = {
          x: this.animation.floorWidth / 2 * (vec.x > 0 ? -1 : 1) * 0.9,
          y: 10,
          z: this.animation.floorHeight / 2 * (vec.y > 0 ? -1 : 1) * 0.9
      }
      var projector = Math.abs(vec.x / vec.y)
      if (projector > 1.0) pos.z /= projector; else pos.x *= projector
      var velvec = makeRandomVector(vector)
      var velocity = { x: velvec.x * boost, y: -0.01, z: velvec.y * boost }
      this.animation.pos = pos
      this.animation.velocity = velocity
      this.play(this.bet)
      // this.animation.diceThrow(pos,velocity)
    }
    this.animation.renderer.domElement.addEventListener('mousedown', mouseStart)
    this.animation.renderer.domElement.addEventListener('mouseup', mouseEnd)
    this.animation.renderer.domElement.addEventListener('touchstart', mouseStart)
    this.animation.renderer.domElement.addEventListener('touchend', mouseEnd)
    this.animation.world.step(1.0 / 60.0)
    requestAnimationFrame(this.animation.animate)
    this.animation.start = () => {
      this.animation.diceValues = []
      for (var i = 0; i < this.animation.dice.length; i++) {
        this.animation.dice[i].getObject().position.x = (i % 3) * 1.7
        this.animation.dice[i].getObject().position.y = 15 + Math.floor(i / 3) * 1.7
        this.animation.dice[i].getObject().position.z = (i % 3) * 1.7
        this.animation.dice[i].getObject().quaternion.x = 0
        this.animation.dice[i].getObject().quaternion.y = 0
        this.animation.dice[i].getObject().quaternion.z = 0
        this.animation.dice[i].updateBodyFromMesh()
        this.animation.dice[i].getObject().body.velocity.set(0, 10, 0)
        this.animation.dice[i].getObject().body.angularVelocity.set(0, 5, 0)
        this.animation.diceValues.push({ dice: this.animation.dice[i], value: i + 1 })
      }
      DiceManager.prepareValues(this.animation.diceValues, floorBody)
      this.animation.dice.forEach(dice => dice.updateMeshFromBody())
      // if(is_prep)
    }
    this.animation.start()
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
      // API request params
      const endpoint = this.getRoute('play')
      const requestParams = { hash: this.provablyFairGame.hash, bet, direction: this.direction, ref_number: this.refNumber }
      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)
      this.loading = false
      this.roll = game.gameable.roll
      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })

      this.animation.result = {
        balance: game.account.balance,
        win: game.win
      }
      this.animation.diceThrow()
    },
    decrementRefNumber () {
      this.refNumber = Math.max(this.minRefNumber, --this.refNumber)
    },
    incrementRefNumber () {
      this.refNumber = Math.min(this.maxRefNumber, ++this.refNumber)
    }
  }
}
</script>

<style lang="scss" scoped>
  .animation {
    width:100%;
    max-width:1200px;
    height:350px;
    z-index: 3;
    &::v-deep canvas {
      z-index: 3;
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

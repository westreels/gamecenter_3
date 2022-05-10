<template>
  <div v-if="landscape" class="d-flex flex-column fill-height py-3 align-center">
    <div class="align-stretch d-flex flex-column flex-grow-1 game-board justify-center">
      <div class="board-row-1 d-flex flex-grow-1 align-stretch justify-stretch">
        <div class="d-flex flex-column align-stretch justify-stretch">
          <button :data-betsize="getBetSize('small')" class="align-center btn btn-big d-flex flex-column flex-grow-1 justify-center pa-2" :class="{betted: getBetSize('small'), win: showWin && 4 <= rollTotal && rollTotal <= 10}" :disabled="playing" @click="placeBet('small')">
            <span class="big text-h4">{{ $t('SMALL') }}</span>
            <span class="accent--text caption pt-2 text-uppercase">4-10</span>
            <span class="accent--text caption pt-2 text-uppercase">1 {{ $t('wins') }} {{ (paytable ? paytable.small : 1) - 1 }}</span>
          </button>
          <div class="d-flex align-stretch justify-stretch">
            <div class="align-stretch d-flex flex-column justify-stretch pa-2">
              <div class="accent--text caption pb-2 text-center text-uppercase">
                {{ $t('Each double 1 wins') }} {{ (paytable ? paytable.double : 1) - 1 }}
              </div>
              <div class="d-flex justify-stretch flex-grow-1 align-stretch">
                <button :data-betsize="getBetSize('double', [1])" class="btn-double align-center justify-center btn d-flex flex-column flex-grow-1" :class="{betted: getBetSize('double', [1]), win: showWin && rollDuplicates[1] == 2}" :disabled="playing" @click="placeBet('double', [1])">
                  <dice-svg num="1" class="mb-2" />
                  <dice-svg num="1" />
                </button>
                <button :data-betsize="getBetSize('double', [2])" class="btn-double align-center justify-center btn d-flex flex-column flex-grow-1" :class="{betted: getBetSize('double', [2]), win: showWin && rollDuplicates[2] == 2}" :disabled="playing" @click="placeBet('double', [2])">
                  <dice-svg num="2" class="mb-2" />
                  <dice-svg num="2" />
                </button>
                <button :data-betsize="getBetSize('double', [3])" class="btn-double align-center justify-center btn d-flex flex-column flex-grow-1" :class="{betted: getBetSize('double', [3]), win: showWin && rollDuplicates[3] == 2}" :disabled="playing" @click="placeBet('double', [3])">
                  <dice-svg num="3" class="mb-2" />
                  <dice-svg num="3" />
                </button>
              </div>
            </div>
            <div class="pa-2">
              <div class="accent--text caption pb-2 text-center text-uppercase">
                {{ $t('Each triple 1 wins') }} {{ (paytable ? paytable.triple : 1) - 1 }}
              </div>
              <div class="align-center d-flex justify-stretch triple">
                <button :data-betsize="getBetSize('triple', [1])" class="btn-triple btn d-flex flex-column flex-grow-1 align-center py-1" :class="{betted: getBetSize('triple', [1]), win: showWin && rollDuplicates[1] > 2}" :disabled="playing" @click="placeBet('triple', [1])">
                  <dice-svg num="1" class="mb-1" />
                  <dice-svg num="1" class="mb-1" />
                  <dice-svg num="1" />
                </button>
                <button :data-betsize="getBetSize('triple', [2])" class="btn-triple btn d-flex flex-column flex-grow-1 align-center py-1" :class="{betted: getBetSize('triple', [2]), win: showWin && rollDuplicates[2] > 2}" :disabled="playing" @click="placeBet('triple', [2])">
                  <dice-svg num="2" class="mb-1" />
                  <dice-svg num="2" class="mb-1" />
                  <dice-svg num="2" />
                </button>
                <button :data-betsize="getBetSize('triple', [3])" class="btn-triple btn d-flex flex-column flex-grow-1 align-center py-1" :class="{betted: getBetSize('triple', [3]), win: showWin && rollDuplicates[3] > 2}" :disabled="playing" @click="placeBet('triple', [3])">
                  <dice-svg num="3" class="mb-1" />
                  <dice-svg num="3" class="mb-1" />
                  <dice-svg num="3" />
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="align-stretch d-flex flex-column flex-grow-1 justify-stretch middle-cell">
          <div class="align-start animation d-flex flex-grow-1 justify-center">
            <div ref="ThreeJS" class="align-center d-flex dice-animation flex-column justify-start">
              <v-alert v-if="message" dense outlined text color="primary" class="text-center message">
                {{ message }}
              </v-alert>
            </div>
          </div>
          <div>
            <button :data-betsize="getBetSize('any_triple')" class="btn pa-2 btn-mid" :class="{betted: getBetSize('any_triple'), win: showWin && Object.values(rollDuplicates).reduce((acc, n) => n > acc ? n : acc, 0) === 3}" :disabled="playing" @click="placeBet('any_triple')">
              <div class="text-uppercase accent--text caption pb-2">
                {{ $t('1 wins') }} {{ (paytable ? paytable.double : 1) - 1 }} {{ $t('any triple') }}
              </div>
              <div class="d-flex justify-center text-center">
                <div v-for="i in 6" :key="i" class="px-2 d-flex">
                  <dice-svg :num="i" class="mr-1" /> <dice-svg :num="i" class="mr-1" /> <dice-svg :num="i" />
                </div>
              </div>
            </button>
          </div>
        </div>
        <div class="d-flex flex-column align-stretch justify-stretch">
          <button :data-betsize="getBetSize('big')" class="align-center btn btn-big d-flex flex-column flex-grow-1 justify-center pa-2" :class="{betted: getBetSize('big'), win: showWin && 11 <= rollTotal && rollTotal <= 17}" :disabled="playing" @click="placeBet('big')">
            <span class="big text-h4">{{ $t('BIG') }}</span>
            <span class="accent--text caption pt-2 text-uppercase">11-17</span>
            <span class="accent--text caption pt-2 text-uppercase">1 {{ $t('wins') }} {{ (paytable ? paytable.big : 1) - 1 }}</span>
          </button>
          <div class="d-flex align-stretch justify-stretch">
            <div class="pa-2">
              <div class="accent--text caption pb-2 text-center text-uppercase">
                {{ $t('Each triple 1 wins') }} {{ (paytable ? paytable.triple : 1) - 1 }}
              </div>
              <div class="align-center d-flex justify-stretch triple">
                <button :data-betsize="getBetSize('triple', [4])" class="btn-triple btn d-flex flex-column flex-grow-1 align-center py-1" :class="{betted: getBetSize('triple', [4]), win: showWin && rollDuplicates[4] > 2}" :disabled="playing" @click="placeBet('triple', [4])">
                  <dice-svg num="4" class="mb-1" />
                  <dice-svg num="4" class="mb-1" />
                  <dice-svg num="4" />
                </button>
                <button :data-betsize="getBetSize('triple', [5])" class="btn-triple btn d-flex flex-column flex-grow-1 align-center py-1" :class="{betted: getBetSize('triple', [5]), win: showWin && rollDuplicates[5] > 2}" :disabled="playing" @click="placeBet('triple', [5])">
                  <dice-svg num="5" class="mb-1" />
                  <dice-svg num="5" class="mb-1" />
                  <dice-svg num="5" />
                </button>
                <button :data-betsize="getBetSize('triple', [6])" class="btn-triple btn d-flex flex-column flex-grow-1 align-center py-1" :class="{betted: getBetSize('triple', [6]), win: showWin && rollDuplicates[6] > 2}" :disabled="playing" @click="placeBet('triple', [6])">
                  <dice-svg num="6" class="mb-1" />
                  <dice-svg num="6" class="mb-1" />
                  <dice-svg num="6" />
                </button>
              </div>
            </div>
            <div class="align-stretch d-flex flex-column justify-stretch pa-2">
              <div class="accent--text caption pb-2 text-center text-uppercase">
                {{ $t('Each double 1 wins') }} {{ (paytable ? paytable.double : 1) - 1 }}
              </div>
              <div class="d-flex justify-stretch flex-grow-1 align-stretch">
                <button :data-betsize="getBetSize('double', [4])" class="btn-double align-center justify-center btn d-flex flex-column flex-grow-1" :class="{betted: getBetSize('double', [4]), win: showWin && rollDuplicates[4] == 2}" :disabled="playing" @click="placeBet('double', [4])">
                  <dice-svg num="4" class="mb-2" />
                  <dice-svg num="4" />
                </button>
                <button :data-betsize="getBetSize('double', [5])" class="btn-double align-center justify-center btn d-flex flex-column flex-grow-1" :class="{betted: getBetSize('double', [5]), win: showWin && rollDuplicates[5] == 2}" :disabled="playing" @click="placeBet('double', [5])">
                  <dice-svg num="5" class="mb-2" />
                  <dice-svg num="5" />
                </button>
                <button :data-betsize="getBetSize('double', [6])" class="btn-double align-center justify-center btn d-flex flex-column flex-grow-1" :class="{betted: getBetSize('double', [6]), win: showWin && rollDuplicates[6] == 2}" :disabled="playing" @click="placeBet('double', [6])">
                  <dice-svg num="6" class="mb-2" />
                  <dice-svg num="6" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="board-row-2 d-flex justify-stretch">
        <button v-for="(n, i) in (() => {let r = []; for (var i = 4; i <= 17; i++) r.push(i); return r;})()" :key="i" :data-betsize="getBetSize('total', [n])" class="align-center btn btn-total d-flex flex-column pa-2 flex-grow-1" :class="{betted: getBetSize('total', [n]), win: showWin && rollTotal == n}" :disabled="playing" @click="placeBet('total', [n])">
          <span class="text-h4 total-number">{{ n }}</span>
          <span class="accent--text caption pt-2 text-uppercase">
            1 {{ $t('wins') }} {{ (paytable && paytable.total ? paytable.total[Math.abs(i - (i > 6 ? 7 : 6))] - 1 : 1) }}
          </span>
        </button>
      </div>
      <div class="board-row-3 d-flex justify-stretch">
        <div class="btn-combination-caption accent--text caption pa-1 d-flex flex-column align-center justify-center text-uppercase text-center">
          1<br>
          {{ $t('wins') }}<br>
          {{ (paytable ? paytable.combination : 1) - 1 }}
        </div>
        <button v-for="(c, i) in combo" :key="i" :data-betsize="getBetSize('combination', c)" class="btn-combination btn d-flex flex-column align-center flex-grow-1 pa-2" :class="{betted: getBetSize('combination', c), win: showWin && rollCombo(c)}" :disabled="playing" @click="placeBet('combination', c)">
          <div class="d-flex justify-center pb-1 pl-1">
            <dice-svg v-for="(n, k) in c" :key="k" :num="n" class="mr-1" />
          </div>
          <span class="accent--text caption pt-1 text-uppercase">1 {{ $t('wins') }} {{ (paytable ? paytable.combination : 1) - 1 }}</span>
        </button>
      </div>
      <div class="board-row-4 d-flex justify-stretch flex-column align-stretch">
        <div class="d-flex justify-stretch">
          <button v-for="(n, i) in 6" :key="i" :data-betsize="getBetSize('single', [n])" class="btn btn-single d-flex flex-column align-center justify-center flex-grow-1 pa-2" :class="{betted: getBetSize('single', [n]), win: showWin && roll.indexOf(n) != -1}" :disabled="playing" @click="placeBet('single', [n])">
            <dice-svg :num="n" />
          </button>
        </div>
        <div class="d-flex justify-stretch accent--text caption text-uppercase text-center">
          <div class="flex-grow-1 pa-2">
            {{ (paytable.single ? paytable.single[0] : 1) - 1 }} {{ $t(':1 on one dice') }}
          </div>
          <div class="flex-grow-1 pa-2">
            {{ (paytable.single ? paytable.single[1] : 1) - 1 }} {{ $t(':1 on two dice') }}
          </div>
          <div class="flex-grow-1 pa-2">
            {{ (paytable.single ? paytable.single[2] : 1) - 1 }} {{ $t(':1 on three dice') }}
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex mt-5">
      <play-controls :loading="loading" :disabled="!bets.length" :playing="playing" @play="play" @bet-change="b => bet = b">
        <template v-slot:before-bet-input>
          <v-btn
            outlined
            color="primary"
            class="mr-2"
            :disabled="playing || !bets.length"
            @click="bets = []"
          >
            {{ $t('Reset') }}
          </v-btn>
          <v-btn
            outlined
            color="primary"
            class="mr-2"
            :disabled="playing || !bets_old.length"
            @click="rebet"
          >
            {{ $t('Rebet') }}
          </v-btn>
        </template>
      </play-controls>
    </div>
  </div>
  <div v-else class="d-flex fill-height align-center justify-center">
    <v-alert
      dense
      outlined
      text
      color="primary"
      class="text-center"
    >
      {{ $t('Please use landscape (horizontal) orientation.') }}
    </v-alert>
  </div>
</template>

<script>
import * as CANNON from 'cannon'
import * as THREE from 'three'
import { DiceD6, DiceManager } from 'packages/sic-bo/resources/js/components/dice.js'

import Color from 'color'
import cloneDeep from 'clone-deep'
import { same } from 'fast-array-diff'
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'

import PlayControls from '~/components/Games/PlayControls'

import clickSound from '~/../audio/common/click.wav'
import winSound from 'packages/sic-bo/resources/audio/win.wav'
import loseSound from 'packages/sic-bo/resources/audio/lose.wav'
import roll1Sound from 'packages/sic-bo/resources/audio/roll1.wav'
import roll2Sound from 'packages/sic-bo/resources/audio/roll2.wav'

import diceSvg from '../components/dice-svg.vue'

export default {
  name: 'SicBo',

  components: { PlayControls, diceSvg },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      clickSound,
      showWin: false,
      message: null,
      roll: [0, 0, 0],
      formIsValid: true,
      ready: false,
      loading: false,
      playing: false,
      refNumber: null,
      payout: null,
      winChance: null,
      direction: -1,
      mutating: false,
      combo: [[1, 2], [1, 3], [1, 4], [1, 5], [1, 6], [2, 3], [2, 4], [2, 5], [2, 6], [3, 4], [3, 5], [3, 6], [4, 5], [4, 6], [5, 6]],
      bet: 0,
      bets: [],
      bets_old: [],
      bets_win: [],
      animation: {}
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    paytable () {
      return this.config.paytable || {}
    },
    rollTotal () {
      return this.roll.reduce((acc, val) => acc + val, 0)
    },
    rollDuplicates () {
      const d = {}
      this.roll.forEach(i => {
        if (!d[i]) {
          d[i] = 1
        } else {
          d[i]++
        }
      })
      return d
    },
    landscape () {
      return this.$vuetify.breakpoint.width > this.$vuetify.breakpoint.height
    }
  },
  watch: {
    landscape () {
      this.$nextTick(() => {
        this.init()
      })
    }
  },

  created () {
    //
  },

  mounted () {
    this.init()
  },
  beforeDestroy () {
    this.animation.destroy = true
    if (typeof this.animation.resolve === 'function') {
      this.animation.resolve()
      this.animation.resolve = null
    }
  },
  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    init () {
      if (this.ready) {
        if (!this.$refs.ThreeJS) return
        // this.animation.renderer.domElement.remove()
        this.animation.container = this.$refs.ThreeJS
        this.animation.container.appendChild(this.animation.renderer.domElement)
        return
      }
      if (!this.$refs.ThreeJS) return
      this.animation = {
        resolve: null,
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
          if (this.animation.destroy) return
          var i
          if (this.animation.isThrought) {
            for (i in this.animation.dice) {
              if (this.animation.diceValues[i].steps.length > this.animation.diceAnimationStep) this.animation.dice[i].setMeshFrom(this.animation.diceValues[i].steps[this.animation.diceAnimationStep])
              if (this.animation.diceValues[i].sound.length > this.animation.diceAnimationStep && this.animation.diceValues[i].sound[this.animation.diceAnimationStep] && this.animation.diceValues[i].lastSound < this.animation.diceAnimationStep) {
                if (this.animation.diceValues[i].lastSound === 0) this.sound(roll1Sound)
                else this.sound(roll2Sound)
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
              this.animation.isThrought = false
              if (typeof this.animation.resolve === 'function') this.animation.resolve()
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
      barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: DiceManager.barrierBodyMaterial })
      barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), -Math.PI / 2)
      barrier.position.set(this.animation.floorWidth * barrierCorner / 2, 0, 0)
      this.animation.world.add(barrier)
      barrier = new CANNON.Body({ mass: 0, shape: new CANNON.Plane(), material: DiceManager.barrierBodyMaterial })
      barrier.quaternion.setFromAxisAngle(new CANNON.Vec3(0, 1, 0), Math.PI / 2)
      barrier.position.set(-(this.animation.floorWidth * barrierCorner / 2), 0, 0)
      this.animation.world.add(barrier)
      // DICE COLORS
      const backColor = this.$vuetify.theme.currentTheme.primary
      const fontColor = (Color(backColor).rgb().array().reduce((acc, val) => acc + val, 0) / 3) > 127 ? '#333333' : '#eeeeee'
      let d
      d = new DiceD6({ size: this.$vuetify.breakpoint.width < 900 ? 3.5 : 1.5, backColor: backColor, fontColor: fontColor })
      this.animation.scene.add(d.getObject())
      this.animation.dice.push(d)
      d = new DiceD6({ size: this.$vuetify.breakpoint.width < 900 ? 3.5 : 1.5, backColor: backColor, fontColor: fontColor })
      this.animation.scene.add(d.getObject())
      this.animation.dice.push(d)
      d = new DiceD6({ size: this.$vuetify.breakpoint.width < 900 ? 3.5 : 1.5, backColor: backColor, fontColor: fontColor })
      this.animation.scene.add(d.getObject())
      this.animation.dice.push(d)

      this.animation.diceThrow = (roll) => {
        return new Promise(resolve => {
          this.animation.resolve = resolve
          const pos = {
            x: 0,
            y: 10,
            z: this.animation.floorHeight / 2 * -0.9
          }
          const velocity = {
            x: 20 - 40 * Math.random(),
            y: -0.01,
            z: 40 * Math.random() + 40
          }
          this.animation.isThrought = true
          this.animation.diceAnimationStep = 0
          this.animation.diceValues = []
          var i
          for (i = 0; i < this.animation.dice.length; i++) {
            this.animation.dice[i].getObject().position.x = pos.x - (i % 3) * 1.5
            this.animation.dice[i].getObject().position.y = pos.y + Math.floor(i / 3) * 1.5
            this.animation.dice[i].getObject().position.z = pos.z + (i % 3) * 1.5
            this.animation.dice[i].updateBodyFromMesh()
            this.animation.dice[i].getObject().body.velocity.set(
              velocity.x,
              velocity.y,
              velocity.z)
            this.animation.dice[i].getObject().body.angularVelocity.set(
              10 * Math.random() - 10,
              10 * Math.random() - 10,
              10 * Math.random() - 10)
            this.animation.diceValues.push({ dice: this.animation.dice[i], value: roll[i] })
          }
          DiceManager.prepareValues(this.animation.diceValues, floorBody)
        })
      }
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
      }
      this.animation.start()
      this.ready = true
    },
    rollCombo (c) {
      return same(c, this.roll).length >= 2
    },
    rebet () {
      this.showWin = false
      this.bets = cloneDeep(this.bets_old)
    },
    placeBet (type, numbers) {
      this.showWin = false
      if (numbers === undefined) numbers = []
      let i
      if (!(i = this.bets.find(bet => bet.type === type && (numbers === undefined || (same(bet.numbers, numbers).length === numbers.length && numbers.length === bet.numbers.length))))) {
        i = {
          type,
          numbers,
          amount: this.bet
        }
        this.bets.push(i)
      } else {
        i.amount += this.bet
        if (i.amount > this.config.max_bet) i.amount = this.config.max_bet
      }
    },
    getBetSize (type, numbers) {
      let i
      if (this.showWin) {
        if ((i = this.bets_win.find(bet => bet.type === type && (numbers === undefined || (same(bet.numbers, numbers).length === numbers.length && numbers.length === bet.numbers.length))))) {
          return i.win
        } else {
          return null
        }
      } else {
        if ((i = this.bets.find(bet => bet.type === type && (numbers === undefined || (same(bet.numbers, numbers).length === numbers.length && numbers.length === bet.numbers.length))))) {
          return i.amount
        } else {
          return null
        }
      }
    },
    async play (bet) {
      this.loading = true
      this.playing = true
      this.win = 0
      this.message = null

      // update user balance
      this.updateUserAccountBalance(this.account.balance - bet)

      // API request params
      const endpoint = this.getRoute('play')
      const requestParams = { hash: this.provablyFairGame.hash, bets: this.bets }

      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)

      this.loading = false

      await this.animation.diceThrow(game.gameable.roll)
      if (this.animation.destroy) return

      this.roll = game.gameable.roll.sort()
      this.bets_win = game.gameable.bets.filter(bet => bet.win > 0)
      this.showWin = true
      this.bets_old = this.bets
      this.bets = []
      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })

      this.playing = false
      this.updateUserAccountBalance(game.account.balance)

      if (game.win > 0) {
        this.sound(winSound)
        this.message = this.$t('You won') + ' ' + game.win
      } else {
        this.sound(loseSound)
        this.message = this.$t('You lose')
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.dice-animation {
  width:100%;
  height:100%;
  z-index: 3;
  position: relative;
  .message {
    position: relative;
    z-index: 4;
    @media (max-width: 1024px) {
        position: absolute !important;
        left: 4px;
        top: 50%;
        transform: translateY(-50%);
    }
    &:after {
        content: ' ';
        position: absolute;
        top:0;left:0;right:0;bottom:0;
        background: var(--v-primary-base);
        opacity: 0.2;
        border-radius: inherit;
    }
  }
  &::v-deep canvas {
    position: absolute;
    top:0;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3;
    width: 100% !important;
    height: 100% !important;
  }
}

.game-board {
    button {
        outline: none;
        position: relative;
        &:after {
            content: ' ';
            position: absolute;
            top:0;left:0;right:0;bottom:0;
            background: var(--v-primary-base);
            opacity: 0;
            border-radius: inherit;
            transition: .1s;
        }
        &:hover:after {
            opacity: .2;
        }
        &:active:after {
            opacity: .3;
        }
        &:before{
          content:attr(data-betsize);
          position: absolute;
          // width: calc( #{$width}/2.2 );
          // height: calc( #{$width}/3 );
          height: 20px;
          min-width: 20px;
          border-radius: 10px;
          font-size: 12px;
          background-color: #FFFFFF;
          padding: 4px 6px;
          @media screen and (max-width: 992px) { font-size: 8px;height: 16px;min-width: 16px; }
          color: black;
          text-align: center;
          bottom:1px;
          transform:scale(0);
          transform-origin:center;
          transition:.3s;
          letter-spacing: 0;
          // line-height: calc( #{$width}/3 );
          line-height: 1;
          opacity:0;
          top:2px;
          right:2px;
          z-index:10;
        }
        &[data-betsize]:before {
          transform:scale(1);
          opacity:1;
        }
        &.btn-big:before,
        &.btn-mid:before{
          top: 50%;
          right: 20px;
          transform: scale(0) translate(0, -50%);
        }
        &.btn-big[data-betsize]:before,
        &.btn-mid[data-betsize]:before{
            transform: scale(1) translate(0, -50%);
        }
        &.btn-single:before{
          top: 5px;
          left: calc(50% + 30px);
          right: auto;
        }
        &.win {
          color: var(--v-success-base);
        }
    }
    .btn-big {
        background: var(--v-primary-darken3);
    }
    .btn-big + div {
        background: var(--v-primary-darken2);
    }
    .btn-total {
        background: var(--v-primary-darken3);
        border-right: 1px solid var(--v-primary-darken2);
        &:last-child {
            border-right: none;
        }
    }
    .btn-single {
        background: var(--v-primary-darken3);
        border-right: 1px solid var(--v-primary-darken2);
        &:last-child {
            border-right: none;
        }
    }
    .btn-combination {
        border-right: 1px solid var(--v-primary-darken1);
        &:last-child {
            border-right: none;
        }
    }
    .btn-double, .btn-triple {
        border-right: 1px solid var(--v-primary-darken1);
        &:last-child {
            border-right: none;
        }
    }
    .board-row-3 {
        background: var(--v-primary-darken2);
    }
    .board-row-4 {
        background: var(--v-primary-darken2);
    }

    .board-row-1 {
        max-height: 300px;
        & > div:first-child, & > div:last-child {
            & > button:first-child {
                border: 3px solid var(--v-primary-darken1);
                border-radius: 10px 10px 0 0;
                border-bottom: none;
            }
            & > div:nth-child(2) {
                border: 3px solid var(--v-primary-darken1);
                border-top-width: 2px;
                border-bottom: none;
                & > div:first-child {
                    border-right: 2px solid var(--v-primary-darken1);
                }
            }
        }
        & > div:nth-child(2) {
            > div:first-child {
                border-bottom: 3px solid var(--v-primary-darken1);
            }
            > div:last-child {
                background: var(--v-primary-darken2);
                margin: 0 -3px;
                position: relative;
                &:after {
                    content: ' ';
                    display: block;
                    position: absolute;
                    left:0.5px;right:0.5px;top:0;bottom:0;
                    border-left: 2px solid var(--v-primary-darken1);
                    border-right: 2px solid var(--v-primary-darken1);
                }
                .btn-mid {
                    position: relative;
                    z-index: 4;
                }
            }
        }
    }
    .board-row-2 {
        max-height: 300px;
        border: 3px solid var(--v-primary-darken1);
        border-top-width: 2px;
        border-bottom: none;
    }
    .board-row-3 {
        border: 3px solid var(--v-primary-darken1);
        border-top-width: 2px;
        border-bottom: none;
    }
    .board-row-4 {
        border: 3px solid var(--v-primary-darken1);
        border-top-width: 2px;
        border-radius: 0 0 10px 10px;
    }
    .btn-combination-caption {
        display: none !important;
        border-right: 1px solid var(--v-primary-darken1);
    }
}

.theme-light {
  .game-board {
      button {
          &:after {
              background: var(--v-primary-base);
          }
          &:before{
            background-color: #FFFFFF;
            color: black;
          }
          &.win {
            color: var(--v-success-base);
          }
      }
      .btn-big {
          background: var(--v-primary-lighten4);
      }
      .btn-big + div {
          background: var(--v-primary-lighten5);
      }
      .btn-total {
          background: var(--v-primary-lighten4);
          border-right: 1px solid var(--v-primary-lighten3);
          &:last-child {
              border-right: none;
          }
      }
      .btn-single {
          background: var(--v-primary-lighten4);
          border-right: 1px solid var(--v-primary-lighten3);
          @media (min-width: 1024px) {
              border-bottom: 1px solid var(--v-primary-lighten3);
          }
          &:last-child {
              border-right: none;
          }
      }
      .btn-combination {
          border-right: 1px solid var(--v-primary-lighten4);
          &:last-child {
              border-right: none;
          }
      }
      .btn-double, .btn-triple {
          border-right: 1px solid var(--v-primary-lighten3);
          &:last-child {
              border-right: none;
          }
      }
      .board-row-3 {
          background: var(--v-primary-lighten5);
      }
      .board-row-4 {
          background: var(--v-primary-lighten4);
          > div:last-child {
              background: var(--v-primary-lighten5);
          }
      }

      .board-row-1 {
          & > div:first-child, & > div:last-child {
              & > button:first-child {
                  border: 3px solid var(--v-primary-lighten2);
              }
              & > div:nth-child(2) {
                  border: 3px solid var(--v-primary-lighten2);
                  & > div:first-child {
                      border-right: 2px solid var(--v-primary-lighten4);
                  }
              }
          }
          & > div:nth-child(2) {
              > div:first-child {
                  border-bottom: 3px solid var(--v-primary-lighten2);
              }
              > div:last-child {
                  background: var(--v-primary-lighten5);
                  &:after {
                      border-left: 2px solid var(--v-primary-lighten4);
                      border-right: 2px solid var(--v-primary-lighten4);
                  }
              }
          }
      }
      .board-row-2 {
          border: 3px solid var(--v-primary-lighten2);
          background: var(--v-primary-lighten4);
      }
      .board-row-3 {
          border: 3px solid var(--v-primary-lighten2);
      }
      .board-row-4 {
          border: 3px solid var(--v-primary-lighten2);
      }
      .btn-combination-caption {
          border-right: 1px solid var(--v-primary-darken1);
      }

  }
}

@media (max-width: 1200px) {
  .game-board {
    padding-left: 4px;
    padding-right: 4px;
    width: 100%;
    .text-h4 {
        font-size: 18px !important;
        line-height: 24px !important;
    }
    .caption {
        font-size: .5rem !important;
        line-height: 0.65rem !important;
    }
    .btn-triple > div {
        width: 12px !important;
        height: 12px !important;
    }

    .btn-double > div {
        width: 18px !important;
        height: 18px !important;
    }
    .btn-mid {
        & > div > div > div {
            width: 12px !important;
            height: 12px !important;
        }
    }
    .btn-big {
        .pt-2 {
            padding-top: 4px !important;
        }
    }
    .btn-total {
        padding: 4px !important;
        .text-h4 {
            line-height: 26px;
        }
        .pt-2 {
            padding-top: 4px !important;
        }
    }
    .btn-triple,
    .btn-double
    {
        margin-right: 8px;
    }
    .btn-mid > div > div {
        flex-wrap: wrap;
        justify-content: center;
    }
    .btn-mid > div > div > div:first-child {
        margin-left: 8px !important;
        margin-right: 8px !important;
        margin-bottom: 4px;
    }
    .btn-combination-caption {
        display: flex !important;
    }
    .btn-combination {
        span {display: none}
        > div {
            flex-direction: column;
        }
        > div > div {
            &:first-child {
                margin-bottom: 4px;
            }
            width: 16px !important;
            height: 16px !important;
        }
    }
    .btn-single {
        > div {
            width: 16px !important;
            height: 16px !important;
        }
    }
    .board-row-4 {
        flex-direction: row-reverse !important;
        > div:first-child {
            flex-grow:1;
        }
        > div:last-child {
            padding: 4px;
            flex-direction: column;
            align-content: flex-start !important;
            text-align: left !important;
            .pa-2 {
                padding: 1px 0 !important;
            }
        }
    }
  }
}
</style>

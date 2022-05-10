<template>
  <div v-if="ready && !notSupported" class="d-flex flex-column fill-height">
    <div ref="container" class="game-slots-container flex-grow-1">
      <div class="message-container d-flex justify-center align-center">
        <game-message :message="message" />
      </div>
    </div>
    <div class="controls mt-4">
      <play-controls :loading="loading" :playing="playing" @play="play" />
    </div>
  </div>
  <div v-else-if="ready && notSupported" class="d-flex fill-height justify-center align-center">
    <div class="">
      {{ $t('WebGL is not supported by your browser') }}
    </div>
  </div>
  <div v-else class="d-flex fill-height justify-center align-center">
    <block-preloader />
  </div>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
// import { config } from '~/plugins/config'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
// import clickSound from '~/../audio/common/click.wav'
import winSound from 'packages/slots-3d/resources/audio/win.wav'
import loseSound from 'packages/slots-3d/resources/audio/lose.wav'
import spinSound from 'packages/slots-3d/resources/audio/spin.wav'
import startSound from 'packages/slots-3d/resources/audio/start.wav'
import stopSound from 'packages/slots-3d/resources/audio/stop.wav'

import PlayControls from '~/components/Games/PlayControls'
import GameMessage from '~/components/Games/GameMessage'
import BlockPreloader from '~/components/BlockPreloader'

import * as TWEEN from '@tweenjs/tween.js'
import * as THREE from 'three'
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls'
import { WEBGL } from 'three/examples/jsm/WebGL'
import { Reflector } from 'three/examples/jsm/objects/Reflector'
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader'
import model from 'packages/slots-3d/resources/models/model.glb'

export default {
  name: 'Slots',

  components: {
    BlockPreloader,
    GameMessage,
    PlayControls
  },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      notSupported: false,
      destroyed: false,
      loading: false,
      playing: false,
      ready: false,
      message: null,
      reelPositions: [],

      camera: null,
      scene: null,
      renderer: null,

      dirLight: null,
      hemiLight: null,
      obj: null,
      modelTexture: null,
      skyBoxTexture: null,
      reflectionTexture: null,
      sceneBackgroundTexture: null,
      reelsTexture: [],
      reelAnimationConfig: {
        speedBrkMp: 2,
        waitTime: 500, // ms
        speedAcc: [
          2.5,
          2.5,
          2.5
        ],
        speedMax: [
          0.5,
          0.5,
          0.5
        ]
      },
      reelAnimation: {
        t: Date.now(),
        sound: [false, false, false],
        speed: [0, 0, 0],
        speedBrk: [0, 0, 0],
        speedAcc: [0, 0, 0],
        speedMax: [0, 0, 0],
        animate: false,
        endPromise: null
      },
      buttonAnimation: {
        t: Date.now(),
        dr: false,
        db: false,
        dy: false,
        cr: 0xE90000,
        cb: 0x009FFF,
        cy: 0xFFD700
      }
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    reelPosStep () {
      return [
        1 / (this.config.reels[0].length * 3),
        1 / (this.config.reels[1].length * 3),
        1 / (this.config.reels[2].length * 3)
      ]
    },
    reelPosMin () {
      return [
        this.reelPosStep[0] * (this.config.reels[0].length - 1),
        this.reelPosStep[1] * (this.config.reels[1].length - 1),
        this.reelPosStep[2] * (this.config.reels[2].length - 1)
      ]
    },
    reelPosMax () {
      return [
        this.reelPosStep[0] * (this.config.reels[0].length * 2 - 1),
        this.reelPosStep[1] * (this.config.reels[1].length * 2 - 1),
        this.reelPosStep[2] * (this.config.reels[2].length * 2 - 1)
      ]
    },
    reelPosSpin () {
      return [
        this.reelPosStep[0] * this.config.reels[0].length,
        this.reelPosStep[1] * this.config.reels[1].length,
        this.reelPosStep[2] * this.config.reels[2].length
      ]
    },
    isBgSkyBox () {
      return this.config.background_skybox && typeof (this.config.background_skybox) === 'string' && this.config.background_skybox.length
    },
    isTable () {
      return typeof this.config.table_show === 'number' && this.config.table_show === 1
    },
    isBg () {
      return this.config.background_env && typeof (this.config.background_env) === 'string' && this.config.background_env.length
    },
    isReflection () {
      return this.config.reflection && typeof (this.config.reflection) === 'string' && this.config.reflection.length
    },
    tableColor () {
      return this.config.table_color
    },
    tableSidesColor () {
      return this.config.table_border_color
    },
    tableTextureImg () {
      return this.config.table_texture
    },
    skyboxRotate () {
      return parseInt(this.config.background_skybox_rotate, 10)
    },
    skyboxDeep () {
      return parseInt(this.config.background_skybox_deep, 10)
    },
    cameraFov () {
      return parseInt(this.config.camera_fov, 10)
    }
  },

  beforeDestroy () {
    this.destroyed = true
    window.removeEventListener('resize', this.onWindowResize)
    this.soundStop(spinSound)
  },
  created () {},
  mounted () {
    this.notSupported = !WEBGL.isWebGLAvailable()
    if (this.notSupported) {
      this.ready = true
      return
    }
    this.$nextTick(async () => {
      this.reelAnimationConfig.speedAcc[0] = this.reelAnimationConfig.speedAcc[0] * 9 / this.config.reels[0].length
      this.reelAnimationConfig.speedAcc[1] = this.reelAnimationConfig.speedAcc[1] * 9 / this.config.reels[1].length
      this.reelAnimationConfig.speedAcc[2] = this.reelAnimationConfig.speedAcc[2] * 9 / this.config.reels[2].length
      this.reelAnimationConfig.speedMax[0] = this.reelAnimationConfig.speedMax[0] * 9 / this.config.reels[0].length
      this.reelAnimationConfig.speedMax[1] = this.reelAnimationConfig.speedMax[1] * 9 / this.config.reels[1].length
      this.reelAnimationConfig.speedMax[2] = this.reelAnimationConfig.speedMax[2] * 9 / this.config.reels[2].length
      this.initScene()
      if (this.isBgSkyBox) {
        this.skyBoxTexture = await this.skyBoxLoad(this.config.background_skybox)
        const rt = new THREE.WebGLCubeRenderTarget(this.skyBoxTexture.image.height)
        rt.fromEquirectangularTexture(this.renderer, this.skyBoxTexture)
        this.reflectionTexture = rt.texture
        const geometry = new THREE.SphereGeometry(10000, 60, 40)
        geometry.scale(-1, 1, 1)
        const mesh = new THREE.Mesh(
          geometry,
          new THREE.MeshBasicMaterial({ map: this.skyBoxTexture })
        )
        this.scene.add(mesh)
        mesh.rotateY(this.skyboxRotate * Math.PI / 180)
        mesh.position.z = -this.skyboxDeep
      } else if (this.isBg) {
        const img = await this.loadImage(this.config.background_env)
        this.sceneBackgroundTexture = await this.sceneBackgroundLoad(img)
        this.scene.background = this.sceneBackgroundTexture
        if (this.isReflection) {
          const skyBoxTexture = await this.skyBoxLoad(this.config.reflection)
          const rt = new THREE.WebGLCubeRenderTarget(skyBoxTexture.image.height)
          rt.fromEquirectangularTexture(this.renderer, skyBoxTexture)
          this.reflectionTexture = rt.texture
        } else {
          this.reflectionTexture = await this.reflectionLoad(img)
        }
      } else if (this.isReflection) {
        const skyBoxTexture = await this.skyBoxLoad(this.config.reflection)
        const rt = new THREE.WebGLCubeRenderTarget(skyBoxTexture.image.height)
        rt.fromEquirectangularTexture(this.renderer, skyBoxTexture)
        this.reflectionTexture = rt.texture
      }
      this.obj = await this.modelLoad()
      this.modelTexture = await this.modelTextureLoad(this.config.texture)
      if (typeof this.tableTextureImg === 'string' && this.tableTextureImg.length) {
        this.tableTexture = await this.modelTextureLoad(this.tableTextureImg)
      } else {
        this.tableTexture = null
      }
      const symbolsImg = []
      for (const src of this.config.symbols) {
        symbolsImg.push(await this.loadImage(src))
      }
      for (const reel of this.config.reels) {
        this.reelsTexture.push(await this.reelTextureInit(symbolsImg, reel))
      }
      this.applyLights()
      this.applyModel()
      this.mountScene()
      this.animate()
      this.cubeCamera.update(this.renderer, this.scene)
    })
  },
  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    loadImage (src) {
      return new Promise((resolve, reject) => {
        const i = new Image()
        i.onload = () => {
          resolve(i)
        }
        i.src = src
      })
    },
    skyBoxLoad (img) {
      return new Promise(resolve => {
        const loader = new THREE.TextureLoader()
        const texture = loader.load(
          img,
          () => {
            // (new THREE.TextureLoader()).load(img, texture => {
            // const rt = new THREE.WebGLCubeRenderTarget(texture.image.height)
            // rt.fromEquirectangularTexture(this.renderer, texture)
            // resolve(rt.texture)
            resolve(texture)
          }
        )
      })
    },
    applyTable () {
      const container = this.$refs.container
      this.meshGround = new Reflector(
        // new THREE.CylinderGeometry(1000, 1000, 20, 2000),
        new THREE.CircleBufferGeometry(1000, 1000),
        {
          clipBias: 0.003,
          color: this.tableColor,
          textrueWidth: container.offsetWidth * window.devicePixelRatio,
          textureHeight: container.offsetHeight * window.devicePixelRatio
        }
      )
      // this.meshGround.position.set(0, -200, 0)
      this.meshGround.receiveShadow = true
      this.meshGround.rotateX(-Math.PI / 2)
      this.meshGround.position.x = -60
      this.meshGround.position.y = -1
      this.scene.add(this.meshGround)
      this.meshGround2 = new THREE.Mesh(
        new THREE.CylinderGeometry(1000, 1000, 50, 2000),
        // new THREE.CircleBufferGeometry(1000, 1000),
        [
          new THREE.MeshPhongMaterial({
            color: this.tableSidesColor,
            // map: this.tableTexture,
            // color: 0xAB3200,
            shininess: 400,
            // metalness: 1,
            transparent: true,
            opacity: 0.98,
            reflectivity: 0.15,
            refractionRatio: 0.98
            // envMap: this.reflectionTexture
          }),
          new THREE.MeshPhongMaterial({
            color: this.tableColor,
            map: this.tableTexture,
            // color: 0xAB3200,
            shininess: 400,
            // metalness: 1,
            transparent: true,
            opacity: this.tableTexture ? 0.9 : 0.98,
            reflectivity: 0.15,
            refractionRatio: 0.98
            // envMap: this.reflectionTexture
          }),
          new THREE.MeshPhongMaterial({
            color: this.tableSidesColor,
            // map: this.tableTexture,
            // color: 0xAB3200,
            shininess: 400,
            // metalness: 1,
            transparent: true,
            opacity: 0.98,
            reflectivity: 0.15,
            refractionRatio: 0.98
            // envMap: this.reflectionTexture
          })
        ]
      )
      this.meshGround2.receiveShadow = true
      this.meshGround2.position.x = -60
      this.meshGround2.position.y = -25
      this.scene.add(this.meshGround2)
      this.meshGround3 = new THREE.Mesh(
        new THREE.CylinderGeometry(800, 800, 300, 2000),
        // new THREE.CircleBufferGeometry(1000, 1000),
        new THREE.MeshPhongMaterial({
          color: this.tableSidesColor,
          // color: 0xAB3200,
          shininess: 400,
          // metalness: 1,
          transparent: true,
          opacity: 0.98,
          reflectivity: 0.15,
          refractionRatio: 0.98
          // envMap: this.reflectionTexture
        })
      )
      this.meshGround3.receiveShadow = true
      this.meshGround3.position.x = -60
      this.meshGround3.position.y = -175
      this.scene.add(this.meshGround3)
    },
    sceneBackgroundPrepare (img) {
      const imagePieces = [null, null, null, null, null, null]
      const imagePiecesNums = [
        0, 5, 1,
        2, 3, 4
      ]
      // px // 0 left
      // nx // 1 right
      // py // 2
      // ny // 3
      // pz // 4 front
      // nz // 5 back
      const tileWidth = img.width / 3
      const tileHeight = tileWidth
      const yyy = img.height / 2 - tileHeight / 2
      let pos = 0
      for (let xxx = 0; xxx < 3; xxx++) {
        const tileCanvas = document.createElement('canvas')
        tileCanvas.width = tileWidth
        tileCanvas.height = tileHeight
        const tileContext = tileCanvas.getContext('2d')
        tileContext.drawImage(img, xxx * tileWidth, yyy, tileWidth, tileHeight, 0, 0, tileCanvas.width, tileCanvas.height)
        imagePieces[imagePiecesNums[pos]] = tileCanvas.toDataURL()
        pos++
      }
      while (pos < 6) {
        const tileCanvas = document.createElement('canvas')
        tileCanvas.width = tileWidth
        tileCanvas.height = tileHeight
        const tileContext = tileCanvas.getContext('2d')
        tileContext.fillStyle = 'white'
        tileContext.fillRect(0, 0, tileWidth, tileHeight)
        imagePieces[imagePiecesNums[pos]] = tileCanvas.toDataURL()
        pos++
      }
      imagePiecesNums[5] = imagePiecesNums[4]
      return imagePieces
    },
    sceneBackgroundLoad (img) {
      return new Promise(resolve => {
        new THREE.CubeTextureLoader().load(this.sceneBackgroundPrepare(img), texture => {
          resolve(texture)
        })
      })
    },
    reflectionPrepare (img) {
      // const skyBoxMaterialArray = []
      const imagePieces = [null, null, null, null, null, null]
      // const imagePiecesNums = [0, 1]
      const imagePiecesNums = [
        1, 4, 0,
        2, 3, 5
      ]
      // px // 0 left
      // nx // 1 right
      // py // 2
      // ny // 3
      // pz // 4 front
      // nz // 5 back
      const tileWidth = img.width / 3
      const tileHeight = tileWidth
      const yyy = img.height / 2 - tileHeight / 2
      let pos = 0
      for (let xxx = 0; xxx < 3; xxx++) {
        const tileCanvas = document.createElement('canvas')
        tileCanvas.width = tileWidth
        tileCanvas.height = tileHeight
        const tileContext = tileCanvas.getContext('2d')
        tileContext.drawImage(img, xxx * tileWidth, yyy, tileWidth, tileHeight, 0, 0, tileCanvas.width, tileCanvas.height)
        imagePieces[imagePiecesNums[pos]] = tileCanvas.toDataURL()
        pos++
      }
      while (pos < 6) {
        const tileCanvas = document.createElement('canvas')
        tileCanvas.width = tileWidth
        tileCanvas.height = tileHeight
        const tileContext = tileCanvas.getContext('2d')
        tileContext.fillStyle = 'white'
        tileContext.fillRect(0, 0, tileWidth, tileHeight)
        imagePieces[imagePiecesNums[pos]] = tileCanvas.toDataURL()
        pos++
      }
      imagePieces[5] = imagePieces[4]
      imagePieces[2] = imagePieces[4]
      imagePieces[3] = imagePieces[4]
      return imagePieces
    },
    reflectionLoad (img) {
      return new Promise(resolve => {
        new THREE.CubeTextureLoader().load(this.reflectionPrepare(img), texture => {
          resolve(texture)
        })
      })
    },
    modelTextureLoad (img) {
      return new Promise(resolve => {
        new THREE.TextureLoader().load(img, texture => {
          // texture.rotation = Math.PI / 2
          texture.flipY = false
          resolve(texture)
        })
      })
    },
    modelLoad () {
      return new Promise(resolve => {
        const loader = new GLTFLoader()
        loader.load(model, (object) => {
          object.scene.traverse(function (child) {
            if (child.isMesh) {
              child.castShadow = true
              child.receiveShadow = true
              child.material = new THREE.MeshPhongMaterial({
                color: 0x353535
              })
            }
          })
          resolve(object.scene.children[0])
        })
      })
    },
    reelTextureInit (imgs, reel) {
      return new Promise(resolve => {
        const canvas = document.createElement('canvas')
        const w = 480
        const h = 480
        const gc = 3
        canvas.width = w
        canvas.height = h * reel.length * gc
        const ctx = canvas.getContext('2d')
        ctx.fillStyle = '#F9EAB6'
        ctx.fillRect(0, 0, canvas.width, canvas.height)
        for (let g = 0; g < gc; g++) {
          reel.forEach((i, k) => {
            ctx.drawImage(imgs[i], 0, 0, imgs[i].width, imgs[i].height, 0, k * h + h * reel.length * g, w, h)
          })
        }
        new THREE.TextureLoader().load(canvas.toDataURL(), texture => {
          texture.flipY = false
          texture.repeat.y = 4000 / canvas.height
          resolve(texture)
        })
      })
    },
    animationLever () {
      return new Promise(resolve => {
        (new TWEEN.Tween({ r: 0 }))
          .easing(TWEEN.Easing.Quadratic.InOut)
          .onUpdate(o => {
            this.obj.children[0].rotation.x = o.r
          })
          .to({ r: 1 }, 200)
          .start()
          .onComplete(o => {
            (new TWEEN.Tween({ r: 1 }))
              .easing(TWEEN.Easing.Quadratic.InOut)
              .onUpdate(o => {
                this.obj.children[0].rotation.x = o.r
              })
              .to({ r: 0 }, 300)
              .start()
              .onComplete(o => {
                resolve()
              })
          })
      })
    },
    async play (bet) {
      this.message = null
      this.loading = true
      this.playing = true
      this.animationLever()
      this.reelAnimationStart()
      this.sound(startSound)
      this.soundLoop(spinSound)
      // update user balance
      this.updateUserAccountBalance(this.account.balance - bet)
      // execute the action
      const { data: game } = await axios.post(this.getRoute('play'), { hash: this.provablyFairGame.hash, bet })
      if (this.destroyed) return
      this.loading = false
      await new Promise(resolve => setTimeout(resolve, this.reelAnimationConfig.waitTime))
      if (this.destroyed) return
      this.reelPositions = game.gameable.positions.map((e, k) => Number.isInteger(e) ? e : Math.round((this.config.reels[k].length - 1) * Math.random()))
      await this.reelAnimationStop()
      if (this.destroyed) return
      this.soundStop(spinSound)
      this.playing = false
      // update balance
      this.updateUserAccountBalance(game.account.balance)
      // play sound
      if (game.win > 0) {
        this.sound(winSound)
        this.message = this.$t('You won') + ' ' + game.win
      } else {
        this.sound(loseSound)
        this.message = this.$t('You lost')
      }
      this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })
    },
    initScene () {
      this.camera = new THREE.PerspectiveCamera(this.cameraFov, window.innerWidth / window.innerHeight, 1, 400000)
      if (typeof this.config.camera_position === 'object' && this.config.camera_position.length && this.config.camera_position.length === 3) {
        this.camera.position.set(this.config.camera_position[0], this.config.camera_position[1], this.config.camera_position[2])
      } else {
        this.camera.position.set(500, 1000, 1500)
      }
      this.scene = new THREE.Scene()
      // if (this.sceneBackgroundTexture != null) this.scene.background = this.sceneBackgroundTexture
      // this.scene.background = this.skyBoxTexture
      this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true })
      this.renderer.setClearColor(0x000000, 0)
      this.renderer.setPixelRatio(window.devicePixelRatio)
      this.renderer.setSize(window.innerWidth, window.innerHeight)
      this.renderer.shadowMap.enabled = true
      this.controls = new OrbitControls(this.camera, this.renderer.domElement)
      this.controls.target.set(-60, 318, 0)
      // if (this.config.camera_limit === 1 || this.config.camera_limit === '1' || this.config.camera_limit === 'true' || this.config.camera_limit === true) {
      this.controls.enablePan = false
      this.controls.maxDistance = 1850
      this.controls.minPolarAngle = Math.PI / 2.5
      this.controls.maxPolarAngle = Math.PI / 2
      this.controls.minAzimuthAngle = -Math.PI / 3
      this.controls.maxAzimuthAngle = Math.PI / 3
      // }
    },
    mountScene () {
      this.ready = true
      this.$nextTick(() => {
        const container = this.$refs.container
        this.onWindowResize()
        container.appendChild(this.renderer.domElement)
        this.controls.update()
        if (this.isTable) {
          this.applyTable()
        }
        window.addEventListener('resize', this.onWindowResize)
      })
    },
    applyLights () {
      this.hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444)
      this.hemiLight.position.set(0, 200, 200)
      this.hemiLight.intensity = 0.5
      this.scene.add(this.hemiLight)

      this.dirLight = new THREE.DirectionalLight(0xffffff)
      this.dirLight.position.set(200, 800, 500)
      // this.dirLight.intensity = 1
      this.dirLight.castShadow = true
      this.dirLight.shadow.camera.near = 10
      this.dirLight.shadow.camera.far = 3000
      this.dirLight.shadow.camera.top = 2000
      this.dirLight.shadow.camera.bottom = -2000
      this.dirLight.shadow.camera.left = -2000
      this.dirLight.shadow.camera.right = 2000
      this.dirLight.shadow.mapSize.width = 4096
      this.dirLight.shadow.mapSize.height = 4096
      this.scene.add(this.dirLight)
    },
    applyModel () {
      this.obj.children[3].material = new THREE.MeshPhysicalMaterial({
        color: 0x353535,
        metalness: 0,
        roughness: 0.85,
        reflectivity: 0.1,
        refractionRatio: 0.98
      })
      this.obj.children[5].material = new THREE.MeshPhysicalMaterial({
        color: 0xAB3200,
        metalness: 0,
        roughness: 0.2,
        reflectivity: 1,
        refractionRatio: 0.98
      })
      this.obj.children[1].material = new THREE.MeshLambertMaterial({
        map: this.modelTexture,
        reflectivity: 0.15,
        refractionRatio: 0.98,
        envMap: this.reflectionTexture
      })
      this.obj.children[4].material = new THREE.MeshLambertMaterial({
        map: this.modelTexture,
        reflectivity: 0.15,
        refractionRatio: 0.58,
        envMap: this.reflectionTexture
      })
      this.obj.children[10].material = new THREE.MeshPhysicalMaterial({
        color: 0xE90000,
        metalness: 0,
        roughness: 0.2,
        reflectivity: 1,
        refractionRatio: 0.98
      })
      this.obj.children[9].material = new THREE.MeshPhysicalMaterial({
        color: 0x009FFF,
        metalness: 0,
        roughness: 0.2,
        reflectivity: 1,
        refractionRatio: 0.98
      })
      this.obj.children[8].material = new THREE.MeshPhysicalMaterial({
        color: 0xFFD700,
        metalness: 0,
        roughness: 0.2,
        reflectivity: 1,
        refractionRatio: 0.98
      })
      this.obj.children[0].children[0].material = new THREE.MeshStandardMaterial({
        color: 0xffffff,
        roughness: 0.25,
        metalness: 1,
        envMap: this.reflectionTexture,
        envMapIntensity: 0.5
      })
      this.obj.children[0].children[1].material = new THREE.MeshPhongMaterial({
        color: 0xAB3200,
        shininess: 400,
        // metalness: 1,
        reflectivity: 0.15,
        refractionRatio: 0.98,
        envMap: this.reflectionTexture
      })
      this.obj.children[7].castShadow = false
      this.obj.children[7].material = new THREE.MeshPhysicalMaterial({
        color: 0xffffff,
        metalness: 0.9,
        roughness: 0.05,
        envMapIntensity: 0.85,
        clearcoat: 1,
        transparent: true,
        opacity: 0.15,
        reflectivity: 0.8,
        refractionRatio: 0.985,
        envMap: this.reflectionTexture
      })
      const cubeRenderTarget = new THREE.WebGLCubeRenderTarget(64, { format: THREE.RGBFormat, generateMipmaps: true, minFilter: THREE.LinearMipmapLinearFilter })
      this.cubeCamera = new THREE.CubeCamera(1, 100000, cubeRenderTarget)
      this.cubeCamera.position.set(-68, 386, 199)
      this.scene.add(this.cubeCamera)
      const chromeMaterial = new THREE.MeshPhysicalMaterial({
        color: 0xffffff,
        roughness: 0.25,
        metalness: 0.6,
        envMapIntensity: 0.5,
        envMap: cubeRenderTarget.texture
      })
      this.obj.children[6].material = chromeMaterial
      this.cubeCamera.update(this.renderer, this.scene)
      this.obj.children[2].material = new THREE.MeshLambertMaterial({
        map: this.reelsTexture[2]
      })
      this.obj.children[12].material = new THREE.MeshLambertMaterial({
        map: this.reelsTexture[1]
      })
      this.obj.children[13].material = new THREE.MeshLambertMaterial({
        map: this.reelsTexture[0]
      })
      this.obj.children[2].rotateX(-0.8 - 0.75)
      this.obj.children[12].rotateX(-0.8 - 0.75)
      this.obj.children[13].rotateX(-0.8 - 0.75)
      // this.reelsTexture[0].offset.y = this.reelPosStep[0] * 1
      // this.reelsTexture[1].offset.y = this.reelPosStep[1] * 1
      // this.reelsTexture[2].offset.y = this.reelPosStep[2] * 1
      this.reelsTexture[0].offset.y = this.reelPosMin[0] + this.reelPosStep[0] * Math.round(Math.random() * (this.config.reels[0].length - 1))
      this.reelsTexture[1].offset.y = this.reelPosMin[1] + this.reelPosStep[1] * Math.round(Math.random() * (this.config.reels[1].length - 1))
      this.reelsTexture[2].offset.y = this.reelPosMin[2] + this.reelPosStep[2] * Math.round(Math.random() * (this.config.reels[2].length - 1))
      this.scene.add(this.obj)
    },
    onWindowResize () {
      const container = this.$refs.container
      this.camera.aspect = container.offsetWidth / container.offsetHeight
      this.camera.updateProjectionMatrix()
      this.renderer.setSize(container.offsetWidth, container.offsetHeight)
    },
    animateReel () {
      const t = Date.now()
      const td = (t - this.reelAnimation.t) / 1000
      if (this.reelAnimation.animate || this.reelAnimation.speed.reduce((a, b) => a + b, 0) > 0) {
        this.reelAnimation.speed.forEach((s, k) => {
          if (this.reelAnimation.animate) {
            if (s < this.reelAnimation.speedMax[k]) {
              s += this.reelAnimation.speedAcc[k] * td
              this.reelAnimation.speed[k] = s
            }
          } else {
            if (s > 0 && this.reelAnimation.speedBrk[k] !== undefined) {
              if (s < this.reelPosStep[k] * 0.5 && !(this.reelAnimation.sound[k])) {
                this.reelAnimation.sound[k] = true
                this.sound(stopSound)
              }
              if (s < this.reelAnimation.speedBrk[k] * td || this.reelAnimation.distance[k] < 0) {
                s = 0
                this.reelsTexture[k].offset.y = this.reelAnimation.target[k]
                if (k < 2) {
                  this.reelAnimationStopCalc(k + 1)
                }
              } else {
                s -= this.reelAnimation.speedBrk[k] * td
              }
              if (s < 0) s = 0
              this.reelAnimation.speed[k] = s
              this.reelAnimation.distance[k] -= s * td
              if (this.reelAnimation.distance[k] < this.reelPosStep[k] * this.reelAnimationConfig.speedBrkMp) {
                this.reelAnimation.speedBrk[k] = Math.max(0.0001, (this.reelAnimation.speed[k] * this.reelAnimation.speed[k]) / (2 * this.reelAnimation.distance[k]))
              } else {
                this.reelAnimation.speedBrk[k] = 0
              }
            }
          }
          this.reelsTexture[k].offset.y -= s * td
          while (this.reelsTexture[k].offset.y <= this.reelPosMin[k]) {
            this.reelsTexture[k].offset.y += this.reelPosSpin[k]
          }
        })
        if (!this.reelAnimation.animate && this.reelAnimation.endPromise != null && this.reelAnimation.speed.reduce((a, b) => a + b, 0) === 0) {
          this.reelAnimation.endPromise()
          this.reelAnimation.endPromise = null
        }
      }
      this.reelAnimation.t = t
    },
    reelAnimationStart () {
      this.reelAnimation.animate = true
      this.reelAnimation.speedAcc[0] = this.reelAnimationConfig.speedAcc[0] * (1 + 0.1 - 0.2 * Math.random())
      this.reelAnimation.speedAcc[1] = this.reelAnimationConfig.speedAcc[1] * (1 + 0.1 - 0.2 * Math.random())
      this.reelAnimation.speedAcc[2] = this.reelAnimationConfig.speedAcc[2] * (1 + 0.1 - 0.2 * Math.random())
      this.reelAnimation.speedMax[0] = this.reelAnimationConfig.speedMax[0] * (1 + 0.1 - 0.2 * Math.random())
      this.reelAnimation.speedMax[1] = this.reelAnimationConfig.speedMax[1] * (1 + 0.1 - 0.2 * Math.random())
      this.reelAnimation.speedMax[2] = this.reelAnimationConfig.speedMax[2] * (1 + 0.1 - 0.2 * Math.random())
      this.reelAnimation.endPromise = this.onReelAnimationEnd
    },
    reelAnimationStop () {
      this.reelAnimation.sound = [false, false, false]
      this.reelAnimation.target = [
        undefined,
        undefined,
        undefined
      ]
      this.reelAnimation.distance = [
        undefined,
        undefined,
        undefined
      ]
      this.reelAnimation.speedBrk = [
        undefined,
        undefined,
        undefined
      ]
      this.reelAnimationStopCalc(0)
      this.reelAnimation.animate = false
      return new Promise(resolve => {
        this.reelAnimation.resolve = resolve
      })
    },
    reelAnimationStopCalc (k) {
      let target = this.reelPosMin[k] + this.reelPosStep[k] * this.reelPositions[k]
      this.reelAnimation.target[k] = this.reelPosMin[k] + this.reelPosStep[k] * this.reelPositions[k]
      while (target > this.reelsTexture[k].offset.y) target -= this.reelPosSpin[k]
      this.reelAnimation.distance[k] = this.reelsTexture[k].offset.y + this.reelPosSpin[k] * 1 - target
      this.reelAnimation.speedBrk[k] = 0
    },
    onReelAnimationEnd () {
      if (typeof this.reelAnimation.resolve === 'function') {
        this.reelAnimation.resolve()
        delete this.reelAnimation.resolve
      }
    },
    animateButton () {
      const t = Date.now()
      if (this.reelAnimation.animate || this.buttonAnimation.cr !== 0xE90000 || this.reelAnimation.speed[0] > 0) {
        const td = (t - this.buttonAnimation.t) / ((1 - this.reelAnimation.speed[0] + 0.25) * 3)
        const c = Math.round(0x03 * td) * 0x10000
        this.buttonAnimation.cr += (this.buttonAnimation.dr ? 1 : -1) * c
        if (this.buttonAnimation.cr >= 0xE90000) {
          this.buttonAnimation.dr = false
          this.buttonAnimation.cr = 0xE90000
        }
        if (this.buttonAnimation.cr <= 0x690000) {
          this.buttonAnimation.dr = true
          this.buttonAnimation.cr = 0x690000
        }
        this.obj.children[10].material.color.set(this.buttonAnimation.cr)
      }
      if (this.reelAnimation.animate || this.buttonAnimation.cb !== 0x009FFF || this.reelAnimation.speed[1] > 0) {
        const td = (t - this.buttonAnimation.t) / ((1 - this.reelAnimation.speed[1] + 0.25) * 3)
        const c = Math.round(0x03 * td) * 0x100 + Math.round(0x03 * td)
        // 0x000303
        this.buttonAnimation.cb += (this.buttonAnimation.db ? 1 : -1) * c
        if (this.buttonAnimation.cb >= 0x009FFF) {
          this.buttonAnimation.db = false
          this.buttonAnimation.cb = 0x009FFF
        }
        if (this.buttonAnimation.cb <= 0x001F7F) {
          this.buttonAnimation.db = true
          this.buttonAnimation.cb = 0x001F7F
        }
        this.obj.children[9].material.color.set(this.buttonAnimation.cb)
      }
      if (this.reelAnimation.animate || this.buttonAnimation.cy !== 0xFFD700 || this.reelAnimation.speed[2] > 0) {
        const td = (t - this.buttonAnimation.t) / ((1 - this.reelAnimation.speed[2] + 0.25) * 3)
        const c = Math.round(0x03 * td) * 0x100 + Math.round(0x03 * td) * 0x10000
        // 0x030300
        this.buttonAnimation.cy += (this.buttonAnimation.dy ? 1 : -1) * c
        if (this.buttonAnimation.cy >= 0xFFD700) {
          this.buttonAnimation.dy = false
          this.buttonAnimation.cy = 0xFFD700
        }
        if (this.buttonAnimation.cy <= 0x7F5700) {
          this.buttonAnimation.dy = true
          this.buttonAnimation.cy = 0x7F5700
        }
        this.obj.children[8].material.color.set(this.buttonAnimation.cy)
      }
      this.buttonAnimation.t = t
    },
    animate () {
      if (this.destroyed) return
      requestAnimationFrame(this.animate)
      TWEEN.update()
      this.animateReel()
      this.animateButton()
      // const delta = this.clock.getDelta()
      // if (this.mixer) this.mixer.update(delta)
      // this.cubeCamera.update(this.renderer, this.scene)
      // this.mirrorCamera.update(this.renderer, this.scene)
      // mirrorSphereCamera.updateCubeMap( renderer, scene );
      this.renderer.render(this.scene, this.camera)
      // stats.update();
    }
  }
}
</script>

<style lang="scss" scoped>
  .game-slots-container {
    position: relative;
    .message-container {
      position: relative;
      height: 0;
      z-index: 2;
      top: 50px;

      > div {
        background-color: var(--v-primary-base) !important;
        border-color: var(--v-primary-base) !important;
        color: #ffffff !important;
      }
    }
    canvas {
      position: absolute;
      top: 0;
      left: 0;
      outline: 0;
    }
  }
</style>

import * as CANNON from 'cannon'
import * as THREE from 'three'

class DiceManagerClass {
  constructor () {
    this.world = null
  }

  setWorld (world) {
    this.world = world

    this.diceBodyMaterial = new CANNON.Material()
    this.floorBodyMaterial = new CANNON.Material()
    this.barrierBodyMaterial = new CANNON.Material()

    world.addContactMaterial(
      new CANNON.ContactMaterial(this.floorBodyMaterial, this.diceBodyMaterial, { friction: 0.01, restitution: 0.5 })
    )
    world.addContactMaterial(
      new CANNON.ContactMaterial(this.barrierBodyMaterial, this.diceBodyMaterial, { friction: 0, restitution: 1.0 })
    )
    world.addContactMaterial(
      new CANNON.ContactMaterial(this.diceBodyMaterial, this.diceBodyMaterial, { friction: 0, restitution: 0.5 })
    )
  }

  /**
     *
     * @param {array} diceValues
     * @param {DiceObject} [diceValues.dice]
     * @param {number} [diceValues.value]
     *
     */
  prepareValues (diceValues, floorBody) {
    if (this.throwRunning) throw new Error('Cannot start another throw. Please wait, till the current throw is finished.')

    for (let i = 0; i < diceValues.length; i++) {
      if (diceValues[i].value < 1 || diceValues[i].dice.values < diceValues[i].value) {
        throw new Error('Cannot throw die to value ' + diceValues[i].value + ', because it has only ' + diceValues[i].dice.values + ' sides.')
      }
    }
    this.throwRunning = true
    for (let i = 0; i < diceValues.length; i++) {
      diceValues[i].dice.simulationRunning = true
      // diceValues[i].vectors = diceValues[i].dice.getCurrentVectors();
      diceValues[i].stableCount = 0
      diceValues[i].steps = []
      diceValues[i].sound = []
      diceValues[i].lastSound = 0
      diceValues[i].steps.push(diceValues[i].dice.getVisibleVectors())
    }
    var isContacts = false
    const collide = () => { isContacts = true }
    floorBody.addEventListener('collide', collide)
    const check = () => {
      let allStable = true
      for (let i = 0; i < diceValues.length; i++) {
        if (diceValues[i].dice.isFinished()) {
          diceValues[i].stableCount++
        } else {
          diceValues[i].stableCount = 0
        }

        if (diceValues[i].stableCount < 50) {
          allStable = false
        }
        diceValues[i].steps.push(diceValues[i].dice.getVisibleVectors())
        diceValues[i].sound.push(isContacts)
      }
      isContacts = false
      this.simulateStep++
      if (allStable) {
        // console.log("all stable",this.simulateStep);
        DiceManager.world.removeEventListener('postStep', check)
        floorBody.removeEventListener('collide', collide)
        for (let i = 0; i < diceValues.length; i++) {
          // console.log('Dice ',i,diceValues[i].value,diceValues[i].dice.getUpsideValue())
          diceValues[i].dice.shiftUpperValue(diceValues[i].value)
          // diceValues[i].dice.setVectors(diceValues[i].vectors);
          diceValues[i].dice.simulationRunning = false
        }
        this.throwRunning = false
      } else {
        DiceManager.world.step(1.00 / 60.00)
      }
    }

    this.world.addEventListener('postStep', check)
    DiceManager.world.step(1.00 / 60.00)
  }
}

class DiceObject {
  /**
     * @constructor
     * @param {object} options
     * @param {Number} [options.size = 100]
     * @param {Number} [options.fontColor = '#000000']
     * @param {Number} [options.backColor = '#ffffff']
     */
  constructor (options) {
    options = this.setDefaults(options, {
      size: 100,
      fontColor: '#000000',
      backColor: '#ffffff'
    })

    this.object = null
    this.size = options.size
    this.invertUpside = false

    this.materialOptions = {
      specular: 0x172022,
      color: 0xf0f0f0,
      shininess: 40,
      flatShading: true
    }
    this.labelColor = options.fontColor
    this.diceColor = options.backColor
  }

  setDefaults (options, defaults) {
    options = options || {}

    for (const key in defaults) {
      if (!defaults.hasOwnProperty(key)) continue

      if (!(key in options)) {
        options[key] = defaults[key]
      }
    }

    return options
  }

  emulateThrow (callback) {
    let stableCount = 0

    const check = () => {
      if (this.isFinished()) {
        stableCount++

        if (stableCount === 50) {
          DiceManager.world.removeEventListener('postStep', check)
          callback(this.getUpsideValue())
        }
      } else {
        stableCount = 0
      }

      DiceManager.world.step(DiceManager.world.dt)
    }

    DiceManager.world.addEventListener('postStep', check)
  }

  isFinished () {
    const threshold = 1

    const angularVelocity = this.object.body.angularVelocity
    const velocity = this.object.body.velocity

    return (
      Math.abs(angularVelocity.x) < threshold &&
                    Math.abs(angularVelocity.y) < threshold &&
                    Math.abs(angularVelocity.z) < threshold &&
                    Math.abs(velocity.x) < threshold &&
                    Math.abs(velocity.y) < threshold &&
                    Math.abs(velocity.z) < threshold)
  }

  getUpsideValue () {
    const vector = new THREE.Vector3(0, this.invertUpside ? -1 : 1)
    let closestFace
    let closestAngle = Math.PI * 2
    for (let i = 0; i < this.object.geometry.faces.length; ++i) {
      const face = this.object.geometry.faces[i]
      if (face.materialIndex === 0) continue

      const angle = face.normal.clone().applyQuaternion(this.object.body.quaternion).angleTo(vector)
      if (angle < closestAngle) {
        closestAngle = angle
        closestFace = face
      }
    }

    return closestFace.materialIndex - 1
  }

  getVisibleVectors () {
    return {
      position: this.object.body.position.clone(),
      quaternion: this.object.body.quaternion.clone()
    }
  }

  getCurrentVectors () {
    return {
      position: this.object.body.position.clone(),
      quaternion: this.object.body.quaternion.clone(),
      velocity: this.object.body.velocity.clone(),
      angularVelocity: this.object.body.angularVelocity.clone()
    }
  }

  setVectors (vectors) {
    this.object.body.position = vectors.position
    this.object.body.quaternion = vectors.quaternion
    this.object.body.velocity = vectors.velocity
    this.object.body.angularVelocity = vectors.angularVelocity
  }

  shiftUpperValue (toValue) {
    var geometry = this.shiftUpperValueCustom(toValue)
    if (!geometry) {
      geometry = this.object.geometry.clone()
      const fromValue = this.getUpsideValue()

      for (let i = 0, l = geometry.faces.length; i < l; ++i) {
        let materialIndex = geometry.faces[i].materialIndex
        if (materialIndex === 0) continue

        materialIndex += toValue - fromValue - 1
        while (materialIndex > this.values) materialIndex -= this.values
        while (materialIndex < 1) materialIndex += this.values

        geometry.faces[i].materialIndex = materialIndex + 1
      }
    }
    this.object.geometry = geometry
  }

  getChamferGeometry (vectors, faces, chamfer) {
    const chamferVectors = []; const chamferFaces = []; const cornerFaces = new Array(vectors.length)
    for (let i = 0; i < vectors.length; ++i) cornerFaces[i] = []
    for (let i = 0; i < faces.length; ++i) {
      const ii = faces[i]; const fl = ii.length - 1
      const centerPoint = new THREE.Vector3()
      const face = new Array(fl)
      for (let j = 0; j < fl; ++j) {
        const vv = vectors[ii[j]].clone()
        centerPoint.add(vv)
        cornerFaces[ii[j]].push(face[j] = chamferVectors.push(vv) - 1)
      }
      centerPoint.divideScalar(fl)
      for (let j = 0; j < fl; ++j) {
        const vv = chamferVectors[face[j]]
        vv.subVectors(vv, centerPoint).multiplyScalar(chamfer).addVectors(vv, centerPoint)
      }
      face.push(ii[fl])
      chamferFaces.push(face)
    }
    for (let i = 0; i < faces.length - 1; ++i) {
      for (let j = i + 1; j < faces.length; ++j) {
        const pairs = []; let lastm = -1
        for (let m = 0; m < faces[i].length - 1; ++m) {
          const n = faces[j].indexOf(faces[i][m])
          if (n >= 0 && n < faces[j].length - 1) {
            if (lastm >= 0 && m !== lastm + 1) pairs.unshift([i, m], [j, n])
            else pairs.push([i, m], [j, n])
            lastm = m
          }
        }
        if (pairs.length !== 4) continue
        chamferFaces.push([chamferFaces[pairs[0][0]][pairs[0][1]],
          chamferFaces[pairs[1][0]][pairs[1][1]],
          chamferFaces[pairs[3][0]][pairs[3][1]],
          chamferFaces[pairs[2][0]][pairs[2][1]], -1])
      }
    }
    for (let i = 0; i < cornerFaces.length; ++i) {
      const cf = cornerFaces[i]; const face = [cf[0]]; let count = cf.length - 1
      while (count) {
        for (let m = faces.length; m < chamferFaces.length; ++m) {
          let index = chamferFaces[m].indexOf(face[face.length - 1])
          if (index >= 0 && index < 4) {
            if (--index === -1) index = 3
            const nextVertex = chamferFaces[m][index]
            if (cf.indexOf(nextVertex) >= 0) {
              face.push(nextVertex)
              break
            }
          }
        }
        --count
      }
      face.push(-1)
      chamferFaces.push(face)
    }
    return { vectors: chamferVectors, faces: chamferFaces }
  }

  makeGeometry (vertices, faces, radius, tab, af) {
    const geom = new THREE.Geometry()
    for (let i = 0; i < vertices.length; ++i) {
      const vertex = vertices[i].multiplyScalar(radius)
      vertex.index = geom.vertices.push(vertex) - 1
    }
    for (let i = 0; i < faces.length; ++i) {
      const ii = faces[i]; const fl = ii.length - 1
      const aa = Math.PI * 2 / fl
      for (let j = 0; j < fl - 2; ++j) {
        geom.faces.push(new THREE.Face3(ii[0], ii[j + 1], ii[j + 2], [geom.vertices[ii[0]],
          geom.vertices[ii[j + 1]], geom.vertices[ii[j + 2]]], 0, ii[fl] + 1))
        geom.faceVertexUvs[0].push([
          new THREE.Vector2((Math.cos(af) + 1 + tab) / 2 / (1 + tab),
            (Math.sin(af) + 1 + tab) / 2 / (1 + tab)),
          new THREE.Vector2((Math.cos(aa * (j + 1) + af) + 1 + tab) / 2 / (1 + tab),
            (Math.sin(aa * (j + 1) + af) + 1 + tab) / 2 / (1 + tab)),
          new THREE.Vector2((Math.cos(aa * (j + 2) + af) + 1 + tab) / 2 / (1 + tab),
            (Math.sin(aa * (j + 2) + af) + 1 + tab) / 2 / (1 + tab))])
      }
    }
    geom.computeFaceNormals()
    geom.boundingSphere = new THREE.Sphere(new THREE.Vector3(), radius)
    return geom
  }

  createShape (vertices, faces, radius) {
    const cv = new Array(vertices.length); const cf = new Array(faces.length)
    for (let i = 0; i < vertices.length; ++i) {
      const v = vertices[i]
      cv[i] = new CANNON.Vec3(v.x * radius, v.y * radius, v.z * radius)
    }
    for (let i = 0; i < faces.length; ++i) {
      cf[i] = faces[i].slice(0, faces[i].length - 1)
    }
    return new CANNON.ConvexPolyhedron(cv, cf)
  }

  getGeometry () {
    const radius = this.size * this.scaleFactor

    const vectors = new Array(this.vertices.length)
    for (let i = 0; i < this.vertices.length; ++i) {
      vectors[i] = (new THREE.Vector3()).fromArray(this.vertices[i]).normalize()
    }

    const chamferGeometry = this.getChamferGeometry(vectors, this.faces, this.chamfer)
    const geometry = this.makeGeometry(chamferGeometry.vectors, chamferGeometry.faces, radius, this.tab, this.af)
    geometry.cannon_shape = this.createShape(vectors, this.faces, radius)

    return geometry
  }

  calculateTextureSize (approx) {
    return Math.max(128, Math.pow(2, Math.floor(Math.log(approx) / Math.log(2))))
  }

  createTextTexture (text, color, backColor) {
    const canvas = document.createElement('canvas')
    const context = canvas.getContext('2d')
    const ts = this.calculateTextureSize(this.size / 2 + this.size * this.textMargin) * 2
    canvas.width = canvas.height = ts
    context.font = ts / (1 + 2 * this.textMargin) + 'pt Arial'
    context.fillStyle = backColor
    context.fillRect(0, 0, canvas.width, canvas.height)
    context.textAlign = 'center'
    context.textBaseline = 'middle'
    context.fillStyle = color
    context.fillText(text, canvas.width / 2, canvas.height / 2)
    const texture = new THREE.Texture(canvas)
    texture.needsUpdate = true
    return texture
  }

  getMaterials () {
    const materials = []
    for (let i = 0; i < this.faceTexts.length; ++i) {
      let texture = null
      if (this.customTextTextureFunction) {
        texture = this.customTextTextureFunction(this.faceTexts[i], this.labelColor, this.diceColor)
      } else {
        texture = this.createTextTexture(this.faceTexts[i], this.labelColor, this.diceColor)
      }

      materials.push(new THREE.MeshPhongMaterial(Object.assign({}, this.materialOptions, { map: texture })))
    }
    return materials
  }

  getObject () {
    return this.object
  }

  create () {
    if (!DiceManager.world) throw new Error('You must call DiceManager.setWorld(world) first.')
    this.object = new THREE.Mesh(this.getGeometry(), this.getMaterials())

    this.object.reveiceShadow = true
    this.object.castShadow = true
    this.object.diceObject = this
    this.object.body = new CANNON.Body({
      mass: this.mass,
      shape: this.object.geometry.cannon_shape,
      material: DiceManager.diceBodyMaterial
    })
    this.object.body.linearDamping = 0.1
    this.object.body.angularDamping = 0.1
    DiceManager.world.add(this.object.body)

    return this.object
  }

  updateMeshFromBody () {
    if (!this.simulationRunning) {
      this.object.position.copy(this.object.body.position)
      this.object.quaternion.copy(this.object.body.quaternion)
    }
  }

  updateBodyFromMesh () {
    this.object.body.position.copy(this.object.position)
    this.object.body.quaternion.copy(this.object.quaternion)
  }

  setMeshFrom (object) {
    this.object.position.copy(object.position)
    this.object.quaternion.copy(object.quaternion)
  }

  shiftUpperValueCustom (diceValue) { return null }
}

export class DiceD4 extends DiceObject {
  constructor (options) {
    super(options)

    this.tab = -0.1
    this.af = Math.PI * 7 / 6
    this.chamfer = 0.96
    this.vertices = [[1, 1, 1], [-1, -1, 1], [-1, 1, -1], [1, -1, -1]]
    this.faces = [[1, 0, 2, 1], [0, 1, 3, 2], [0, 3, 2, 3], [1, 2, 3, 4]]
    this.faceTexts = [[], [0, 0, 0], [2, 4, 3], [1, 3, 4], [2, 1, 4], [1, 2, 3]]
    this.scaleFactor = 1.2
    this.values = 4
    // [[], [0, 0, 0], [2, 4, 3], [1, 3, 4], [2, 1, 4], [1, 2, 3]]  original
    this.d4FaceTexts = [
      [
        [[], [0, 0, 0], [2, 4, 3], [1, 3, 4], [2, 1, 4], [1, 2, 3]], // 1 - 1
        [[], [0, 0, 0], [1, 4, 3], [2, 3, 4], [1, 2, 4], [2, 1, 3]], // 1 - 2
        [[], [0, 0, 0], [2, 4, 1], [3, 1, 4], [2, 3, 4], [3, 2, 1]], // 1 - 3
        [[], [0, 0, 0], [2, 1, 3], [4, 3, 1], [2, 4, 1], [4, 2, 3]]// 1 - 4
      ],
      [
        [[], [0, 0, 0], [1, 4, 3], [2, 3, 4], [1, 2, 4], [2, 1, 3]], // 2 - 1
        [[], [0, 0, 0], [2, 4, 3], [1, 3, 4], [2, 1, 4], [1, 2, 3]], // 2 - 2
        [[], [0, 0, 0], [3, 4, 2], [1, 2, 4], [3, 1, 4], [1, 3, 2]], // 2 - 3
        [[], [0, 0, 0], [4, 2, 3], [1, 3, 2], [4, 1, 2], [1, 4, 3]]// 2 - 4
      ],
      [
        [[], [0, 0, 0], [2, 4, 1], [3, 1, 4], [2, 3, 4], [3, 2, 1]], // 3 - 1
        [[], [0, 0, 0], [3, 4, 2], [1, 2, 4], [3, 1, 4], [1, 3, 2]], // 3 - 2
        [[], [0, 0, 0], [2, 4, 3], [1, 3, 4], [2, 1, 4], [1, 2, 3]], // 3 - 3
        [[], [0, 0, 0], [2, 3, 4], [1, 4, 3], [2, 1, 3], [1, 2, 4]]// 3 - 4
      ],
      [
        [[], [0, 0, 0], [2, 1, 3], [4, 3, 1], [2, 4, 1], [4, 2, 3]], // 4 - 1
        [[], [0, 0, 0], [4, 2, 3], [1, 3, 2], [4, 1, 2], [1, 4, 3]], // 4 - 2
        [[], [0, 0, 0], [2, 3, 4], [1, 4, 3], [2, 1, 3], [1, 2, 4]], // 4 - 3
        [[], [0, 0, 0], [2, 4, 3], [1, 3, 4], [2, 1, 4], [1, 2, 3]]// 4 - 4
      ]
    ]
    // this.faceTexts = this.d4FaceTexts[0];
    // this.updateMaterialsForValue = (diceValue) => {
    //     if (diceValue < 0) diceValue += 4;
    //     this.faceTexts = this.d4FaceTexts[diceValue];
    //     this.object.material = this.getMaterials();
    // };

    this.shiftUpperValueCustom = (diceValueNew) => {
      // let geometry = this.object.geometry.clone()
      const geometry = this.object.geometry
      const fromValue = this.getUpsideValue()
      this.faceTexts = this.d4FaceTexts[fromValue - 1][diceValueNew - 1]
      this.object.material = this.getMaterials()
      // this.updateMaterialsForValue(diceValueNew - fromValue);
      return geometry
    }

    this.customTextTextureFunction = function (text, color, backColor) {
      const canvas = document.createElement('canvas')
      const context = canvas.getContext('2d')
      const ts = this.calculateTextureSize(this.size / 2 + this.size * 2) * 2
      canvas.width = canvas.height = ts
      context.font = ts / 5 + 'pt Arial'
      context.fillStyle = backColor
      context.fillRect(0, 0, canvas.width, canvas.height)
      context.textAlign = 'center'
      context.textBaseline = 'middle'
      context.fillStyle = color
      for (const i in text) {
        context.fillText(text[i], canvas.width / 2,
          canvas.height / 2 - ts * 0.3)
        context.translate(canvas.width / 2, canvas.height / 2)
        context.rotate(Math.PI * 2 / 3)
        context.translate(-canvas.width / 2, -canvas.height / 2)
      }
      const texture = new THREE.Texture(canvas)
      texture.needsUpdate = true
      return texture
    }
    this.mass = 300
    this.inertia = 5
    this.invertUpside = true

    this.create()
  }
}

export class DiceD6 extends DiceObject {
  constructor (options) {
    super(options)

    this.tab = 0.1
    this.af = Math.PI / 4
    this.chamfer = 0.96
    this.vertices = [[-1, -1, -1], [1, -1, -1], [1, 1, -1], [-1, 1, -1],
      [-1, -1, 1], [1, -1, 1], [1, 1, 1], [-1, 1, 1]]
    this.faces = [[0, 3, 2, 1, 1], [1, 2, 6, 5, 2], [0, 1, 5, 4, 3],
      [3, 7, 6, 2, 4], [0, 4, 7, 3, 5], [4, 5, 6, 7, 6]]
    this.scaleFactor = 0.9
    this.values = 6
    this.faceTexts = [' ', '0', '1', '2', '3', '4', '5', '6', '7', '8',
      '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20']
    this.textMargin = 1.0
    this.mass = 300
    this.inertia = 13

    this.create()
  }
}

export class DiceD8 extends DiceObject {
  constructor (options) {
    super(options)

    this.tab = 0
    this.af = -Math.PI / 4 / 2
    this.chamfer = 0.965
    this.vertices = [[1, 0, 0], [-1, 0, 0], [0, 1, 0], [0, -1, 0], [0, 0, 1], [0, 0, -1]]
    this.faces = [[0, 2, 4, 1], [0, 4, 3, 2], [0, 3, 5, 3], [0, 5, 2, 4], [1, 3, 4, 5],
      [1, 4, 2, 6], [1, 2, 5, 7], [1, 5, 3, 8]]
    this.scaleFactor = 1
    this.values = 8
    this.faceTexts = [' ', '0', '1', '2', '3', '4', '5', '6', '7', '8',
      '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20']
    this.textMargin = 1.2
    this.mass = 340
    this.inertia = 10

    this.create()
  }
}

export class DiceD10 extends DiceObject {
  constructor (options) {
    super(options)

    this.tab = 0
    this.af = Math.PI * 6 / 5
    this.chamfer = 0.945
    this.vertices = []
    this.faces = [[5, 7, 11, 0], [4, 2, 10, 1], [1, 3, 11, 2], [0, 8, 10, 3], [7, 9, 11, 4],
      [8, 6, 10, 5], [9, 1, 11, 6], [2, 0, 10, 7], [3, 5, 11, 8], [6, 4, 10, 9],
      [1, 0, 2, -1], [1, 2, 3, -1], [3, 2, 4, -1], [3, 4, 5, -1], [5, 4, 6, -1],
      [5, 6, 7, -1], [7, 6, 8, -1], [7, 8, 9, -1], [9, 8, 0, -1], [9, 0, 1, -1]]

    for (let i = 0, b = 0; i < 10; ++i, b += Math.PI * 2 / 10) {
      this.vertices.push([Math.cos(b), Math.sin(b), 0.105 * (i % 2 ? 1 : -1)])
    }
    this.vertices.push([0, 0, -1])
    this.vertices.push([0, 0, 1])

    this.scaleFactor = 0.9
    this.values = 10
    this.faceTexts = [' ', '0', '1', '2', '3', '4', '5', ' 6.', '7', '8',
      ' 9.', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20']
    this.textMargin = 1.0
    this.mass = 350
    this.inertia = 9

    this.create()
  }
}

export class DiceD12 extends DiceObject {
  constructor (options) {
    super(options)

    const p = (1 + Math.sqrt(5)) / 2
    const q = 1 / p

    this.tab = 0.2
    this.af = -Math.PI / 4 / 2
    this.chamfer = 0.968
    this.vertices = [[0, q, p], [0, q, -p], [0, -q, p], [0, -q, -p], [p, 0, q],
      [p, 0, -q], [-p, 0, q], [-p, 0, -q], [q, p, 0], [q, -p, 0], [-q, p, 0],
      [-q, -p, 0], [1, 1, 1], [1, 1, -1], [1, -1, 1], [1, -1, -1], [-1, 1, 1],
      [-1, 1, -1], [-1, -1, 1], [-1, -1, -1]]
    this.faces = [[2, 14, 4, 12, 0, 1], [15, 9, 11, 19, 3, 2], [16, 10, 17, 7, 6, 3], [6, 7, 19, 11, 18, 4],
      [6, 18, 2, 0, 16, 5], [18, 11, 9, 14, 2, 6], [1, 17, 10, 8, 13, 7], [1, 13, 5, 15, 3, 8],
      [13, 8, 12, 4, 5, 9], [5, 4, 14, 9, 15, 10], [0, 12, 8, 10, 16, 11], [3, 19, 7, 17, 1, 12]]
    this.scaleFactor = 0.9
    this.values = 12
    this.faceTexts = [' ', '0', '1', '2', '3', '4', '5', ' 6.', '7', '8',
      ' 9.', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20']
    this.textMargin = 1.0
    this.mass = 350
    this.inertia = 8

    this.create()
  }
}

export class DiceD20 extends DiceObject {
  constructor (options) {
    super(options)

    const t = (1 + Math.sqrt(5)) / 2

    this.tab = -0.2
    this.af = -Math.PI / 4 / 2
    this.chamfer = 0.955
    this.vertices = [[-1, t, 0], [1, t, 0], [-1, -t, 0], [1, -t, 0],
      [0, -1, t], [0, 1, t], [0, -1, -t], [0, 1, -t],
      [t, 0, -1], [t, 0, 1], [-t, 0, -1], [-t, 0, 1]]
    this.faces = [[0, 11, 5, 1], [0, 5, 1, 2], [0, 1, 7, 3], [0, 7, 10, 4], [0, 10, 11, 5],
      [1, 5, 9, 6], [5, 11, 4, 7], [11, 10, 2, 8], [10, 7, 6, 9], [7, 1, 8, 10],
      [3, 9, 4, 11], [3, 4, 2, 12], [3, 2, 6, 13], [3, 6, 8, 14], [3, 8, 9, 15],
      [4, 9, 5, 16], [2, 4, 11, 17], [6, 2, 10, 18], [8, 6, 7, 19], [9, 8, 1, 20]]
    this.scaleFactor = 1
    this.values = 20
    this.faceTexts = [' ', '0', '1', '2', '3', '4', '5', ' 6.', '7', '8',
      ' 9.', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20']
    this.textMargin = 1.0
    this.mass = 400
    this.inertia = 6

    this.create()
  }
}

// ---------------------------------------------//

export const DiceManager = new DiceManagerClass()

if (typeof define === 'function' && define.amd) {
  define(function () {
    return {
      DiceManager: DiceManager,
      DiceD4: DiceD4,
      DiceD6: DiceD6,
      DiceD8: DiceD8,
      DiceD10: DiceD10,
      DiceD12: DiceD12,
      DiceD20: DiceD20
    }
  })
} else if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
  module.exports = {
    DiceManager: DiceManager,
    DiceD4: DiceD4,
    DiceD6: DiceD6,
    DiceD8: DiceD8,
    DiceD10: DiceD10,
    DiceD12: DiceD12,
    DiceD20: DiceD20
  }
} else {
  window.Dice = {
    DiceManager: DiceManager,
    DiceD4: DiceD4,
    DiceD6: DiceD6,
    DiceD8: DiceD8,
    DiceD10: DiceD10,
    DiceD12: DiceD12,
    DiceD20: DiceD20
  }
}

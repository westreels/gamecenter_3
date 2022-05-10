import Matter from 'matter-js'
import Color from 'color'
// import decomp from 'poly-decomp'
// window.decomp = decomp
const {
  Bodies,
  Body,
  Composite,
  Engine,
  Events,
  World,
  Render
} = Matter

export default class {
  pegSize = 4
  ballRadius = 20
  offsetX = 47
  offsetY = 60
  countX = 14
  countY = 8
  canvas = null
  engine = null
  w = 0
  h = 0
  ballColor = '#efefef'
  pegColor = '#efefef'
  dom = null
  render = null
  frame = 0
  ballTexture = null
  pegs = []
  punch = null
  bang = null
  light = false
  bucket = -1
  bucketTarget = -1
  buckets = []

  svgToPng (svg, width, height) {
    return new Promise(resolve => {
      if (typeof width === 'undefined') width = 300
      if (typeof height === 'undefined') height = 300
      const canvas = document.createElement('canvas')
      const ctx = canvas.getContext('2d')
      canvas.width = width
      canvas.height = height
      const img = new Image()
      img.onload = () => {
        ctx.drawImage(img, 0, 0, width, height)
        resolve(canvas.toDataURL())
      }
      img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg)
    })
  }

  async setup (dom, paytable) {
    this.countX = paytable.length + 1
    this.countY = paytable.length - 1
    this.w = this.offsetX * (this.countX + 1)
    this.h = this.offsetY * this.countY + this.offsetY
    this.dom = dom
    this.engine = Engine.create()
    Engine.run(this.engine)
    this.engine.timing.timeScale = 1.25
    this.engine.world.gravity.y = 2
    this.render = Render.create({
      element: this.dom,
      engine: this.engine,
      options: {
        width: this.w,
        height: this.h,
        showAngleIndicator: false,
        wireframes: false
        // pixelRatio: 'auto'
      }
    })
    Render.run(this.render)
    Events.on(this.engine, 'collisionStart', (...args) => this.collisionStart(...args))
    Events.on(this.engine, 'collisionActive', (...args) => this.collisionActive(...args))
    Events.on(this.engine, 'collisionEnd', (...args) => this.collisionEnd(...args))
    Events.on(this.render, 'beforeRender', () => this.draw())
    let xStart = this.offsetX
    let cnt = this.countX

    const ballSvg = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="291.428" height="291.428" viewBox="0 0 77.107 77.107"><defs><radialGradient xlink:href="#a" id="b" cx="47.448" cy="32.282" fx="47.448" fy="32.282" r="38.554" gradientUnits="userSpaceOnUse" gradientTransform="matrix(.29267 1.48514 -1.34936 .26591 55.437 -52.406)"/><linearGradient id="a"><stop offset="0" stop-color="${Color(this.ballColor).lighten(this.light ? 0.5 : 0)}"/><stop offset="1" stop-color="${Color(this.ballColor).darken(this.light ? 0 : 0.75)}"/></linearGradient></defs><circle style="mix-blend-mode:normal" cx="38.554" cy="38.554" r="38.554" fill="url(#b)" fill-rule="evenodd"/><ellipse ry="9.347" rx="6.221" cy="2.039" cx="-36.407" transform="matrix(-.66009 -.75119 .759 -.6511 0 0)" fill="#ffffff"/></svg>`
    this.ballTexture = await this.svgToPng(ballSvg, this.ballRadius * 2, this.ballRadius * 2)
    const pegSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="291.428" height="291.428" viewBox="0 0 77.107 77.107"><g fill-rule="evenodd"><circle cx="38.554" cy="38.554" r="38.554" fill="${this.light ? '#aaaaaa' : '#777777'}"/><path d="M38.422 0A38.554 38.554 0 000 38.554a38.554 38.554 0 003.193 15.361 38.554 38.554 0 0015.36 3.192 38.554 38.554 0 0038.554-38.554 38.554 38.554 0 00-3.192-15.36A38.554 38.554 0 0038.554 0a38.554 38.554 0 00-.132 0z" fill="${this.light ? '#efefef' : '#dddddd'}"/></g></svg>`
    const pegTexture = await this.svgToPng(pegSvg, this.pegSize * 2, this.pegSize * 2)
    const pegLightSvg = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1165.712" height="1165.712" viewBox="0 0 308.428 308.428"><defs><radialGradient xlink:href="#a" id="b" cx="154.214" cy="154.214" fx="154.214" fy="154.214" r="154.214" gradientUnits="userSpaceOnUse"/><linearGradient id="a"><stop offset="0" stop-color="${this.light ? this.pegColor : '#fff'}" stop-opacity="${this.light ? '0.5' : '0.35'}"/><stop offset="1" stop-color="${this.light ? this.pegColor : '#fff'}" stop-opacity="0"/></linearGradient></defs><g fill-rule="evenodd"><circle cx="154.214" cy="154.214" r="154.214" fill="url(#b)"/><g transform="translate(115.66 115.66)"><circle cx="38.554" cy="38.554" r="38.554" fill="${Color(this.pegColor).lighten(0)}"/><path d="M38.422 0A38.554 38.554 0 000 38.554a38.554 38.554 0 003.193 15.361 38.554 38.554 0 0015.36 3.192 38.554 38.554 0 0038.554-38.554 38.554 38.554 0 00-3.192-15.36A38.554 38.554 0 0038.554 0a38.554 38.554 0 00-.132 0z" fill="${Color(this.pegColor).lighten(0.5)}"/></g></g></svg>`
    const pegLightTexture = await this.svgToPng(pegLightSvg, this.pegSize * 8, this.pegSize * 8)

    for (let y = this.countY - 1; y >= 0; y--) {
      for (let x = 0; x < cnt; x++) {
        const peg = this.addCircle({
          x: x * this.offsetX + xStart,
          y: y * this.offsetY + this.offsetY,
          r: this.pegSize,
          options: {
            isStatic: true,
            density: 5,
            restitution: 1,
            collisionFilter: { category: 1 },
            label: `peg-${y}-${x}`,
            activePeg: null,
            render: { zIndex: 0, sprite: { texture: pegTexture } }
          }
        })
        peg.activePeg = this.addCircle({
          x: x * this.offsetX + xStart,
          y: y * this.offsetY + this.offsetY,
          r: this.pegSize,
          options: {
            isStatic: true,
            collisionFilter: { category: 4, mask: 0, group: -1 },
            render: { visible: false, zIndex: 1, opacity: 0, sprite: { texture: pegLightTexture } }
          }
        })
        this.pegs.push(peg)
      }
      cnt--
      xStart += this.offsetX / 2
    }
    paytable.forEach(async (pay, i) => {
      const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="47" height="32" viewBox="0 0 47 32">
          <g>
            <rect x="1" y="1" width="45" height="26" fill="transparent" rx="3" stroke="${this.ballColor}" stroke-width="1" />
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Verdana" font-size="16" fill="${this.ballColor}">${pay}</text>
          </g>
        </svg>
      `
      const svgA = `
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="47" height="32" viewBox="0 0 47 32">
          <g>
            <rect x="1" y="1" width="45" height="26" fill="${this.ballColor}"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Verdana" font-size="16" fill="white">${pay}</text>
          </g>
        </svg>
      `
      const payTexture = await this.svgToPng(svg, 45, 32)
      const payATexture = await this.svgToPng(svgA, 45, 32)
      const body = Bodies.rectangle(this.offsetX + this.offsetX * i + this.offsetX * 0.5, this.offsetY * this.countY + this.offsetY * 0.5 + 10, this.offsetX, this.offsetY * 0.5, {
        isStatic: true,
        label: 'bucket-' + i,
        render: { zIndex: 4, sprite: { texture: payTexture } }
      })
      this.addBody(body)
      body.bodyActive = Bodies.rectangle(this.offsetX + this.offsetX * i + this.offsetX * 0.5, this.offsetY * this.countY + this.offsetY * 0.5 + 10, this.offsetX, this.offsetY * 0.5, {
        isStatic: true,
        label: 'bucket-active' + i,
        render: { visible: false, opacity: 0, zIndex: 45, sprite: { texture: payATexture } }
      })
      this.addBody(body.bodyActive)
      this.buckets.push(body)
    })
  }

  addBody (...bodies) {
    World.add(this.engine.world, ...bodies)
  }

  removeBody (body) {
    Matter.Composite.remove(this.engine.world, body)
  }

  addCircle ({
    x = 0,
    y = 0,
    r = 20,
    options = {}
  } = {}) {
    const body = Bodies.circle(x, y, r, options)
    this.addBody(body)
    return body
  }

  pull (path, bucket, callback) {
    this.bucketTarget = bucket
    // path = [1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0]
    const pegs = [1]
    path.forEach(d => pegs.push(pegs[pegs.length - 1] + d))
    this.addCircle({
      x: this.w * 0.5,
      y: 0,
      r: this.ballRadius,
      options: {
        callback,
        density: 0.1,
        restitution: 0.9,
        torque: 0,
        label: 'ball',
        path,
        pegs,
        collisionFilter: { category: 4, mask: 1 },
        render: {
          zIndex: 3,
          sprite: {
            texture: this.ballTexture
          }
        }
      }
    })
    const allBodies = Composite.allBodies(this.engine.world)
    allBodies.sort((a, b) => {
      const zIndexA = a.render && typeof a.render.zIndex !== 'undefined' ? a.render.zIndex : 0
      const zIndexB = b.render && typeof b.render.zIndex !== 'undefined' ? b.render.zIndex : 0
      return zIndexA - zIndexB
    })
  }

  draw () {
    this.frame++
    this.pegs.forEach(peg => {
      if (peg.activePeg && peg.activePeg.render.visible) {
        peg.activePeg.render.opacity -= 0.04
        if (peg.activePeg.render.opacity <= 0) {
          peg.activePeg.render.opacity = 0
          peg.activePeg.render.visible = false
        }
      }
    })
    this.buckets.forEach(bucket => {
      const x = parseInt(bucket.label.split('-')[1])
      if (x === this.bucket && bucket.bodyActive.render.opacity < 1) {
        bucket.bodyActive.render.opacity += 0.1
        if (bucket.bodyActive.render.opacity > 1) bucket.bodyActive.render.opacity = 1
        if (!bucket.bodyActive.render.visible) bucket.bodyActive.render.visible = true
      } else if (x !== this.bucket && bucket.bodyActive.render.visible) {
        bucket.bodyActive.render.opacity -= 0.1
        if (bucket.bodyActive.render.opacity <= 0) {
          bucket.bodyActive.render.opacity = 0
          bucket.bodyActive.render.visible = false
        }
      }
    })
    const bodies = Composite.allBodies(this.engine.world)
    bodies.forEach(n => {
      const render = n.render
      if (!render || !render.visible) {
        return
      }
      if (n.label === 'ball') {
        n.angle = 0
        if (n.pegs.length && n.position.y > (n.pegs.length - 1) * this.offsetY + this.offsetY) {
          if (n.path[n.pegs.length]) {
            if (n.path[n.pegs.length] === 0 && n.velocity.x > 0) {
              Body.setVelocity(n, { x: -0.05, y: n.velocity.y })
            } else if (n.path[n.pegs.length] === 1 && n.velocity.x < 0) {
              Body.setVelocity(n, { x: 0.05, y: n.velocity.y })
            }
          }
        }
        if (!(this.frame % 5)) {
          this.addCircle({
            x: n.position.x - this.ballRadius + this.ballRadius * 2 * Math.random(),
            y: n.position.y,
            r: this.ballRadius * 0.19 * Math.random() + this.ballRadius * 0.01,
            options: {
              // isStatic: 1,
              label: 'bubble',
              collisionFilter: { category: 8, mask: 0, group: -1 },
              size: 1,
              render: {
                fillStyle: Color(this.ballColor).lighten(0.25),
                zIndex: 4
              }
            }
          })
        }
        if (n.position.y > this.offsetY * this.countY) {
          //  this.h + this.ballRadius
          const a = (Math.PI * 0.7) / 25
          for (let i = Math.PI * 0.15; i < Math.PI * 0.85; i += a) {
            const s = 3 * Math.random() + 3
            const x = Math.cos(i) * s
            const y = -Math.sin(i) * s
            const body = this.addCircle({
              x: n.position.x,
              y: n.position.y,
              r: this.ballRadius * 0.19 * Math.random() + this.ballRadius * 0.01,
              options: {
                label: 'bang',
                collisionFilter: { category: 4, mask: 0, group: -1 },
                size: 1,
                density: 0.0000001,
                timeScale: 0.3,
                render: {
                  fillStyle: Color(this.ballColor).lighten(0.35),
                  zIndex: 4
                }
              }
            })
            Body.setVelocity(body, { x, y })
          }
          this.removeBody(n)
          if (typeof this.bang === 'function') this.bang()
          if (typeof n.callback === 'function') n.callback()
          this.bucket = this.bucketTarget
        }
      } else if (n.label === 'bubble') {
        n.size -= 0.001
        if (n.size < 0.95) {
          this.removeBody(n)
        } else {
          Body.scale(n, n.size, n.size)
          Body.setVelocity(n, { x: 0, y: -0.6 - (0.5 - (1 - n.size) * 10) })
        }
      } else if (n.label === 'bang') {
        n.size -= 0.001
        if (n.size < 0.95) {
          this.removeBody(n)
        } else {
          Body.scale(n, n.size, n.size)
        }
      }
    })
  }

  collisionStart ({ pairs }) {
    pairs.forEach(({
      bodyA,
      bodyB
    }) => {
      if (bodyA.label === 'ball' && bodyB.label.substr(0, 3) === 'peg') {
        if (typeof this.punch === 'function') this.punch()
        bodyB.activePeg.render.opacity = 1
        bodyB.activePeg.render.visible = true
      } else if (bodyB.label === 'ball' && bodyA.label.substr(0, 3) === 'peg') {
        if (typeof this.punch === 'function') this.punch()
        bodyA.activePeg.render.opacity = 1
        bodyA.activePeg.render.visible = true
      }
    })
  }

  collisionActive ({ pairs }) {
    pairs.forEach(({
      bodyA,
      bodyB
    }) => {
      let body, bodyPeg
      if (bodyA.label === 'ball' && bodyB.label.substr(0, 3) === 'peg') {
        // bodyB.activePeg.render.opacity = 1
        body = bodyA
        bodyPeg = bodyB
      } else if (bodyB.label === 'ball' && bodyA.label.substr(0, 3) === 'peg') {
        // bodyA.activePeg.render.opacity = 1
        body = bodyB
        bodyPeg = bodyA
      } else {
        return
      }
      const y = parseInt(bodyPeg.label.split('-')[1])
      const x = parseInt(bodyPeg.label.split('-')[2])
      if (y === body.last_y) {
        if (x === body.pegs[y]) {
          if (body.path[y] === 0 && body.position.x > bodyPeg.position.x + this.ballRadius * 0.25) {
            Body.applyForce(body, body.position, { x: -0.15, y: 1.5 })
          } else if (body.path[y] === 1 && body.position.x < bodyPeg.position.x - this.ballRadius * 0.25) {
            Body.applyForce(body, body.position, { x: 0.15, y: 1.5 })
          } else if (body.path[y] === 0 && (body.velocity.x > 0) && body.position.x > bodyPeg.position.x - this.ballRadius * 0.25) {
            // Body.setVelocity(body, { x: -(body.position.y > bodyPeg.position.y ? ((body.position.y - bodyPeg.position.y) / 10) : 0), y: body.velocity.y })
            Body.applyForce(body, body.position, { x: -0.15 - (body.position.y > bodyPeg.position.y ? ((body.position.y - bodyPeg.position.y) / 2) : 0), y: 0 })
          } else if (body.path[y] === 1 && (body.velocity.x < 0) && body.position.x < bodyPeg.position.x + this.ballRadius * 0.25) {
            // Body.setVelocity(body, { x: +(body.position.y < bodyPeg.position.y ? ((bodyPeg.position.y - body.position.y) / 10) : 0), y: body.velocity.y })
            Body.applyForce(body, body.position, { x: +0.15 + (body.position.y < bodyPeg.position.y ? ((bodyPeg.position.y - body.position.y) / 2) : 0), y: 0 })
          }
        }
      }
    })
  }

  collisionEnd ({ pairs }) {
    pairs.forEach(({
      bodyA,
      bodyB
    }) => {
      let body, bodyPeg
      if (bodyA.label === 'ball' && bodyB.label.substr(0, 3) === 'peg') {
        body = bodyA
        bodyPeg = bodyB
      } else if (bodyB.label === 'ball' && bodyA.label.substr(0, 3) === 'peg') {
        body = bodyB
        bodyPeg = bodyA
      } else {
        return
      }
      const y = parseInt(bodyPeg.label.split('-')[1])
      const x = parseInt(bodyPeg.label.split('-')[2])
      if (typeof body.last_y === 'undefined') body.last_y = -1
      if (y > body.last_y) {
        body.last_y = y
        let vx = 0
        if (body.path[y] === 0) {
          vx = -1 * (0.15 + 0.5 * Math.random() + (body.position.x > bodyPeg.position.x ? ((body.position.x - bodyPeg.position.x) / 2) : 0))
        } else {
          vx = +1 * (0.15 + 0.5 * Math.random() + (body.position.x < bodyPeg.position.x ? ((bodyPeg.position.x - body.position.x) / 2) : 0))
        }
        Body.setVelocity(body, { x: vx, y: body.velocity.y })
      } else if (y < body.last_y) {
        Body.setVelocity(body, { x: 0, y: body.velocity.y })
      } else if (y === body.last_y) {
        if (x === body.pegs[y]) {
          if (body.path[y] === 0 && (body.velocity.x > 0)) {
            Body.setVelocity(body, { x: (body.path[y] ? 1 : -1) * (0.15 + (body.position.x > bodyPeg.position.x ? ((body.position.x - bodyPeg.position.x) / 2) : 0) + 0.5 * Math.random()), y: (body.velocity.y < 1.5 ? 1.5 : body.velocity.y) })
          } else if (body.path[y] === 1 && (body.velocity.x < 0)) {
            Body.setVelocity(body, { x: (body.path[y] ? 1 : -1) * (0.15 + (body.position.x < bodyPeg.position.x ? ((bodyPeg.position.x - body.position.x) / 2) : 0) + 0.5 * Math.random()), y: (body.velocity.y < 1.5 ? 1.5 : body.velocity.y) })
          } else if (body.path[y] === 0 && body.velocity.x < -2) {
            Body.setVelocity(body, { x: -2, y: body.velocity.y })
          } else if (body.path[y] === 0 && body.velocity.x < -2) {
            Body.setVelocity(body, { x: 2, y: body.velocity.y })
          }
        }
        if (x > body.pegs[y] && body.velocity.x > 0) {
          Body.setVelocity(body, { x: -0.1, y: body.velocity.y < 0.1 ? 0.15 : body.velocity.y })
        } else if (x < body.pegs[y] && body.velocity.x < 0) {
          Body.setVelocity(body, { x: 0.1, y: body.velocity.y < 0.1 ? 0.15 : body.velocity.y })
        } else if (x > body.pegs[y] && body.velocity.x < -2) {
          Body.setVelocity(body, { x: -2, y: body.velocity.y })
        } else if (x < body.pegs[y] && body.velocity.x > 2) {
          Body.setVelocity(body, { x: 2, y: body.velocity.y })
        }
      }
    })
  }
}

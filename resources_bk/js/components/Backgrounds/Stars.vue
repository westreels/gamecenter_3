<template>
  <div class="stars">
    <svg
      v-for="(starStyle, i) in starStyles"
      :key="i"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      :style="starStyle"
    >
      <path opacity="0.1" fill="currentColor" class="white--text" d="M14.1,14.1L12,24l-2.1-9.9L0,12l9.9-2.1L12,0l2.1,9.9L24,12L14.1,14.1z"/>
    </svg>
  </div>
</template>

<script>
export default {
  data: () => ({ starStyles: [] }),
  created () {
    const numStars = 10
    const minSize = 10
    const maxSize = 80
    const minDuration = 10
    const maxDuration = 30
    const height = document.documentElement.clientHeight || document.body.clientHeight
    for (let i = 0; i < numStars; i++) {
      let size = minSize + Math.random() * (maxSize - minSize)
      let top = Math.min(Math.random() * height, height - maxSize - 100)
      top += 'px'
      size += 'px'
      let animationDuration = minDuration + (Math.random() * (maxDuration - minDuration))
      animationDuration += 's'
      let animationDelay = Math.random() * maxDuration
      animationDelay += 's'
      this.starStyles.push({
        position: 'absolute',
        animationDuration,
        animationDelay,
        width: size,
        height: size,
        top
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.stars {
  svg {
    left: -80px;
    animation: slide-right 1s linear infinite;
  }
}

@keyframes slide-right {
  0% {
    transform: translateX(0);
    opacity: 1;
  }

  75% {
    opacity: 0.8;
  }

  100% {
    transform: translateX(100vw);
    opacity: 0;
  }
}
</style>

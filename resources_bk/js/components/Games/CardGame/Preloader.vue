<template>
  <div class="wrap">
    <div class="chip">
      {{ progress }}%
    </div>
  </div>
</template>

<script>
export default {
  props: {
    progress: {
      type: Number,
      required: true
    }
  }
}
</script>

<style scoped lang="scss">
  .wrap {
    background-color: black;
    width: 100%;
    height: 100%;
    background-image: url(/images/games/card-game-ui/preloader-bg.png);
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .chip {
    width: 200px;
    height: 200px;
    position:relative;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 31px;
    color: #809f71;
    font-family: 'impact';
    &:after {
      content: ' ';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 200px;
      height: 200px;
      background-size: cover;
      background-image: url(/images/games/card-game-ui/preloader-chip.png);
      transform-origin: center;
      animation: chip 2s infinite linear;
    }
  }
  @keyframes chip {
    from {
      transform: translate(-50%,-50%) rotate(0deg);
    }
    to {
      transform: translate(-50%,-50%) rotate(360deg);
    }
  }
</style>

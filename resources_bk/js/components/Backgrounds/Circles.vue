<template>
  <div class="container">
    <div v-for="(v, i) in Array(100).fill(0)" :key="i" class="circle-container">
      <div class="circle"></div>
    </div>
  </div>
</template>
<style lang="scss" scoped>
@mixin rg($color) {
  background-image: radial-gradient(
      $color,
      $color 10%,
      rgba($color, 0) 56%
  );
}

.theme-light {
  .container {
    .circle-container {
      .circle {
        @include rg(hsl(213, 100%, 87%))
      }
    }
  }
}

.theme-dark {
  .container {
    .circle-container {
      .circle {
        @include rg(hsl(217, 100%, 70%))
      }
    }
  }
}

.container {
  width: 100%;
  height: 100%;
  overflow: hidden;
  position: absolute;

  .circle-container {
    $particleNum: 200;

    position: absolute;
    transform: translateY(-10vh);
    animation-iteration-count: infinite;
    animation-timing-function: linear;

    .circle {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      mix-blend-mode: screen;

      animation: fadein-frames 200ms infinite, scale-frames 2s infinite;

      @keyframes fade-frames {
        0% {
          opacity: 1;
        }

        50% {
          opacity: 0.7;
        }

        100% {
          opacity: 1;
        }
      }

      @keyframes scale-frames {
        0% {
          transform: scale3d(0.4, 0.4, 1);
        }

        50% {
          transform: scale3d(2.2, 2.2, 1);
        }

        100% {
          transform: scale3d(0.4, 0.4, 1);
        }
      }
    }

    $particleBaseSize: 8;


    @for $i from 1 through $particleNum {
      &:nth-child(#{$i}) {
        $circleSize: random($particleBaseSize);
        width: $circleSize + px;
        height: $circleSize + px;

        $startPositionY: random(10) + 100;
        $framesName: "move-frames-" + $i;
        $moveDuration: 28000 + random(9000) + ms;

        animation-name: #{$framesName};
        animation-duration: $moveDuration;
        animation-delay: random(37000) + ms;

        @keyframes #{$framesName} {
          from {
            transform: translate3d(
                #{random(100) + vw},
                #{$startPositionY + vh},
                0
            );
          }

          to {
            transform: translate3d(
                #{random(100) + vw},
                #{- $startPositionY - random(30) + vh},
                0
            );
          }
        }

        .circle {
          animation-delay: random(4000) + ms;
        }
      }
    }
  }
}
</style>

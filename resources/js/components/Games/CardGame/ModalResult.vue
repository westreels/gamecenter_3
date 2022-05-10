<template>
  <transition name="dialog">
    <div v-if="value" class="modal-wrap" @click="() => stay ? null : $emit('input', false)">
      <div class="modal" :class="`glow-${glow}`" @click.stop>
        <slot />
      </div>
    </div>
  </transition>
</template>

<script>

export default {
  props: {
    value: {
      type: Boolean,
      required: true
    },
    glow: {
      type: String,
      default: 'none'
    },
    stay: {
      type: Boolean,
      default: false
    }
  },
  watch: {
    value (newValue) {
      if (newValue && !this.stay) {
        setTimeout(() => {
          this.$emit('input', false)
        }, 3000)
      }
    }
  }
}
</script>

<style scoped lang="scss">

.modal-wrap {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  &::v-deep {
    .modal::-webkit-scrollbar{ width: 5px;}
    .modal::-webkit-scrollbar-button{ display: block; height: 10px; border-radius: 0px; background-color: transparent; }
    .modal::-webkit-scrollbar-thumb{ left:-5px;background-color: #9a9a9a; }
    .modal::-webkit-scrollbar-thumb:hover{ background-color: #9a9a9a; }
    .modal::-webkit-scrollbar-track{ background-color: transparent; }
  }
}
.modal {
    position: absolute;
    max-width: calc(100vw - 64px);
    max-height: calc(100vh - 64px);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transform-origin: center;
    overflow: auto;
    border-radius: 32px;
    font-family: "impact";
    font-size: 16px;
    padding: 38px 37px;
    color: #d3c9b7;
    width: 672px;
    height: 298px;
    box-sizing: border-box;
    background: url(/images/games/card-game-ui/popup-bg.png) no-repeat center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 35px;
    &.glow-lose {
      box-shadow: 0 0 81px 0px #f22;
    }
    &.glow-win {
      box-shadow: 0 0 81px 0px #2f2;
    }
    &.glow-push {
      box-shadow: 0 0 81px 0px #ffcf3b;
    }
    &::v-deep {
      h5 {
        font-size: 82px;
        font-weight: normal;
        text-align: center;
        color: #f7a85b;
        margin: 0;
        text-shadow: -1px -1px 0 #ffcc9b, 1px -1px 0 #c38549, -1px 1px 0 #543d25, 1px 1px 0 #7c5630;
      }
      h6 {
        font-size: 70px;
        font-weight: normal;
        text-align: center;
        color: #f7a85b;
        margin: 0;
        text-shadow: -1px -1px 0 #ffcc9b, 1px -1px 0 #c38549, -1px 1px 0 #543d25, 1px 1px 0 #7c5630;
      }
      p {
        font-size: 71px;
        line-height: 80px;
        text-align: center;
        color: #f7a85b;
        margin: 0;
        text-shadow: -1px -1px 0 #ffcc9b, 1px -1px 0 #c38549, -1px 1px 0 #543d25, 1px 1px 0 #7c5630;
      }
      button {
        margin: 0 4px;
        background: rgba(191, 140, 42, 0.25);
        box-sizing: border-box;
        border-radius: 8px;
        margin-top: 8px;
        padding: 8px 35px;
        transition: background 0.2s;
        font-size: 45px;
      }
    }
}

.dialog-enter-active {
  transition: 0.6s;
  opacity: 0;
  .modal {
    transition: 0.6s;
    opacity: 0;
    transform-origin: center;
    transform: translate(-50%, -50%) scale(0.9);
  }
}

.dialog-leave-active {
  transition: 0.6s;
  opacity: 1;
  .modal {
    transition: 0.6s;
    opacity: 1;
    transform-origin: center;
    transform: translate(-50%, -50%) scale(1);
  }
}

.dialog-enter-to, .dialog-leave {
  opacity: 1;
  .modal {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
}

.dialog-enter, .dialog-leave-to {
  opacity: 0;
  .modal {
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.9);
  }
}
</style>

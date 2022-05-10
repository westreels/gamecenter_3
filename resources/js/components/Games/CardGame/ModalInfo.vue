<template>
  <transition name="dialog">
    <div v-if="value" class="modal-wrap" @click="$emit('input', false)">
      <div class="modal" @click.stop>
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
    }
  }
}
</script>

<style scoped lang="scss">

.modal-wrap {
  position: fixed;
  top: 0;
  left:0;
  bottom:0;
  right:0;
  background: rgba(0,0,0,.7);
  z-index: 10000;
  &::v-deep {
    .modal::-webkit-scrollbar{ width: 5px;}
    .modal::-webkit-scrollbar-button{ display: block; height: 10px; border-radius: 0; background-color: transparent; }
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
    background: rgba(0,0,0,0.25);
    transform-origin: center;
    /* transform: translate(-50%, -50%) scale(0.9); */
    overflow: auto;
    border-radius: 32px;
    border: 3px solid #ffb438;
    font-size: 16px;
    padding: 32px 48px;
    box-shadow: 0 0 32px -8px #ffb438;
    color: #d3c9b7;
    &::v-deep {
      h5 {
        font-family: 'impact', sans-serif;
        font-size: 1.8em;
        font-weight: normal;
        text-align: center;
        color: #be9243;
        margin: 16px 0;
        &:first-child {
          margin-top: 0;
        }
      }
      table {
        width: 100%;
        border-collapse: collapse;

        thead {
          th {
            font-family: 'impact', sans-serif;
            font-weight: normal;
            font-size: 1.2em;
            color: #be9243;
            text-align: center;
            padding: 4px 16px;
          }
        }
        tbody {
          tr {
            &:hover {
              background: rgba(255, 255, 255, 0.1);
            }

            td {
              color: #d3c9b7;
              text-align: center;
              padding: 0 16px;
            }
          }
        }
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

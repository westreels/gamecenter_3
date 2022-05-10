<template>
  <transition name="pop">
    <div
      v-if="score >= 0"
      class="hand-score align-self-start"
      :class="{ 'primary text--primary': score === 21, 'error text--primary': score > 21 }"
    >
      {{ score }}
    </div>
  </transition>
</template>

<script>
export default {
  props: {
    score: {
      type: Number,
      required: true
    }
  }
}
</script>

<style lang="scss" scoped>
@import '~/../sass/_variables.scss';

.hand-score {
  position: absolute;
  top: -0.75em;
  right: -0.25em;
  width: 1.5em;
  height: 1.5em;
  font-size: 1.2em;
  line-height: 1.5em;
  background: #fff;
  border-radius: 50%;
  text-align: center;
  color: var(--v-secondary-darken2);
}
.pop-enter-active {
  transition: all 0.3s ease-out;
}
.pop-leave-active {
  transition: all 0.3s ease-in;
}
.pop-enter, .pop-leave-to {
  transform: scale(0) rotate(360deg);
}

@media screen and (max-width: $playing-card-sm-breakpoint) {
  .hand-score {
    right: -0.5em;
    font-size: 1em;
  }
}
</style>

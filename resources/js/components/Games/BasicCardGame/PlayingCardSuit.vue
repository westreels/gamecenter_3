<template>
  <div class="suit">
    <svg style="display: none;">
      <symbol id="suit-C" viewBox="0 0 45.33 50.5" fill="currentColor" class="white--text">
        <circle cx="32.49" cy="29.52" r="11.69" opacity="0.5"/>
        <circle cx="22.5" cy="12.06" r="11.69" opacity="0.5"/>
        <circle cx="12.51" cy="29.52" r="11.69" opacity="0.5"/>
        <polygon points="22.5 37.09 14.73 50.36 30.26 50.36 22.5 37.09" opacity="0.5"/>
      </symbol>
      <symbol id="suit-D" viewBox="0 0 45.33 50.5" fill="currentColor" class="red--text">
        <polygon points="22 50.5 38.51 22.22 5.49 22.22 22 50.5" opacity="0.7"/>
        <polygon points="22 0.5 5.49 28.78 38.51 28.78 22 0.5" opacity="0.7"/>
      </symbol>
      <symbol id="suit-H" viewBox="0 0 45.33 50.5" fill="currentColor" class="red--text">
        <circle cx="11.33" cy="11.33" r="11.33" opacity="0.7"/>
        <circle cx="34" cy="11.33" r="11.33" opacity="0.7"/>
        <polygon points="22.66 50 45.17 11.46 0.16 11.46 22.66 50" opacity="0.7"/>
      </symbol>
      <symbol id="suit-S" viewBox="0 0 45.33 50.5" fill="currentColor" class="white--text">
        <circle cx="32.33" cy="34.2" r="10" opacity="0.5"/>
        <circle cx="12.33" cy="34.2" r="10" opacity="0.5"/>
        <polygon points="22.33 0 2.38 34.17 42.29 34.17 22.33 0" opacity="0.5"/>
        <polygon points="22.33 38.65 15.69 50 28.97 50 22.33 38.65" opacity="0.5"/>
      </symbol>
    </svg>
    <svg v-if="suit">
      <use :xlink:href="'#suit-' + suit"></use>
    </svg>
  </div>
</template>

<script>
export default {
  props: {
    suit: {
      required: true,
      validator: value => (typeof value === 'string' && value.length === 1) || value === null
    }
  }
}
</script>

<style lang="scss" scoped>
@import '~/../sass/_variables.scss';

.suit svg {
  width: 2em;
  height: 2em;
}

@media screen and (max-width: $playing-card-sm-breakpoint) {
  .suit svg {
    width: 1.5em;
    height: 1.5em;
  }
}
</style>

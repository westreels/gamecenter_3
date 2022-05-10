<template>
  <div :style="{width: `${$attrs.size ? $attrs.size : 24}px`, height: `${$attrs.size ? $attrs.size : 24}px`}">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 23 23">
      <path fill="currentColor" d="M19.6 0H3.4A3.4 3.4 0 000 3.4v16.2c0 2 1.5 3.4 3.4 3.4h16.2c1.9 0 3.4-1.5 3.4-3.4V3.4C23 1.6 21.5 0 19.6 0zM21 19.6c0 .8-.6 1.4-1.4 1.4H3.4c-.8 0-1.4-.6-1.4-1.4V3.4C2 2.7 2.6 2 3.4 2h16.2c.8 0 1.4.7 1.4 1.4z"/>
      <g v-if="$attrs.num == 1" id="p1">
        <circle fill="currentColor" cx="11.5" cy="11.5" r="2"/>
      </g>
      <g v-if="$attrs.num == 2" id="p2">
        <circle fill="currentColor" cx="17" cy="6" r="2"/>
        <circle fill="currentColor" cx="6" cy="17" r="2"/>
      </g>
      <g v-if="$attrs.num == 3" id="p3">
        <circle fill="currentColor" cx="17" cy="6" r="2"/>
        <circle fill="currentColor" cx="11.5" cy="11.5" r="2"/>
        <circle fill="currentColor" cx="6" cy="17" r="2"/>
      </g>
      <g v-if="$attrs.num == 4" id="p4">
        <circle fill="currentColor" cx="6" cy="6" r="2"/>
        <circle fill="currentColor" cx="17" cy="6" r="2"/>
        <circle fill="currentColor" cx="6" cy="17" r="2"/>
        <circle fill="currentColor" cx="17" cy="17" r="2"/>
      </g>
      <g v-if="$attrs.num == 5" id="p5">
        <circle fill="currentColor" cx="6" cy="6" r="2"/>
        <circle fill="currentColor" cx="17" cy="6" r="2"/>
        <circle fill="currentColor" cx="6" cy="17" r="2"/>
        <circle fill="currentColor" cx="17" cy="17" r="2"/>
        <circle fill="currentColor" cx="11.5" cy="11.5" r="2"/>
      </g>
      <g v-if="$attrs.num == 6" id="p6">
        <circle fill="currentColor" cx="6" cy="6" r="2"/>
        <circle fill="currentColor" cx="17" cy="6" r="2"/>
        <circle fill="currentColor" cx="6" cy="17" r="2"/>
        <circle fill="currentColor" cx="17" cy="17" r="2"/>
        <circle fill="currentColor" cx="6" cy="11.5" r="2"/>
        <circle fill="currentColor" cx="17" cy="11.5" r="2"/>
      </g>
    </svg>
  </div>
</template>

<style scoped lang="scss">
    div {
        display: inline-block;
        position: relative;
        svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    }
</style>

<script>
export default {
  params: {
    num: Number,
    size: Number
  }
}
</script>

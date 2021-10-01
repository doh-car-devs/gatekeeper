<template>
  <router-link :to="to" :class="`text-gray-500 hover:text-gray-400`">
    <slot></slot>
  </router-link>
</template>

<script>
export default {
  name: "LinkSecondary",
  props: {
    to: String,
  },
}
</script>

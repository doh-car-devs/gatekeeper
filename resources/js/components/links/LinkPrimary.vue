<template>
  <router-link :to="to" :class="`text-green-500 hover:text-green-400`">
    <slot></slot>
  </router-link>
</template>

<script>
export default {
  name: "LinkPrimary",
  props: {
    to: String,
  },
}
</script>

<template>
  <button type="button" class="text-white bg-red-500 px-4 py-1 rounded hover:bg-red-400 focus:bg-red-400">
    <slot></slot>
  </button>
</template>

<script>
export default {
  name: "ButtonDelete",
}
</script>

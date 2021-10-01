<template>
  <div class="absolute inset-0 w-full h-full">
    <div class="absolute inset-0 bg-gray-700 opacity-80"></div>
    <div class="absolute inset-0 h-full flex flex-col justify-center items-center p-4">
      <div class="w-full max-h-full lg:w-1/3 bg-white rounded p-4 lg:p-8 overflow-y-auto">
        <div class="flex justify-between pb-2 border-b">
          <p class="text-lg text-gray-600 font-medium">{{ title }}</p>
          <button type="button" @click="$emit('close')" class="text-gray-600 hover:text-gray-500"><i class="fas fa-times text-sm"></i></button>
        </div>

        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Modal",
  props: {
    title: String,
  },
}
</script>

<template>
  <auth-layout>
    <div class="flex flex-col lg:flex-row lg:flex-wrap">
      <a :href="`http://192.168.224.68:50003`" class="lg:w-80 bg-green-500 p-4 border rounded shadow-lg hover:bg-green-400">
        <p class="text-lg text-white text-center">Financial Management Tracking Information System</p>
      </a>
    </div>
  </auth-layout>
</template>

<script>
import AuthLayout from "../components/layouts/AuthLayout";

export default {
  name: "Home",
  components: {
    AuthLayout,
  },
}
</script>

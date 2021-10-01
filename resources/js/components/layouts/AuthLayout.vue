<template>
  <div class="flex flex-col h-full relative">
    <div id="navbar" class="flex-shrink-0 h-16 lg:h-24 flex flex-col lg:flex-row items-center justify-between lg:px-20">
      <router-link to="/" class="h-full flex items-center space-x-2">
        <img src="images/DOH-Logo.png" class="w-12 lg:w-16">
        <span class="text-xl lg:text-3xl text-gray-600 font-cinzel font-bold uppercase">Gatekeeper</span>
      </router-link>
      <div class="hidden lg:flex lg:items-center lg:space-x-4">
        <router-link :to="{ path: '/home' }" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Home</router-link>
        <router-link v-if="$store.getters['getTokenScope'].includes('user:list')" :to="{ path: '/users', query: { page: 1, per_page: 25, sort: 'name_up' }}" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Users</router-link>
        <router-link v-if="$store.getters['getTokenScope'].includes('role:list')" :to="{ path: '/roles', query: { page: 1, per_page: 25, sort: 'name_up' }}" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Roles</router-link>
        <router-link v-if="$store.getters['getTokenScope'].includes('permission:list')" :to="{ path: '/permissions', query: { page: 1, per_page: 25, sort: 'name_up' }}" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Permissions</router-link>
        <router-link :to="{ path: '/logout' }" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Logout</router-link>
      </div>
      <button type="button" @click="show_sidebar = true" class="lg:hidden absolute top-5 right-5"><i class="fas fa-bars"></i></button>
    </div>
    <div :class="{ 'translate-x-0 opacity-100': show_sidebar, 'translate-x-full opacity-0': !show_sidebar }" class="lg:hidden absolute w-full h-full bg-gray-100 flex flex-col items-center space-y-4 p-8 transition transform">
      <button type="button" @click="show_sidebar = false" class="lg:hidden absolute top-5 left-5"><i class="fas fa-times"></i></button>
      <router-link to="/" class="flex items-center space-x-2">
        <img src="images/DOH-Logo.png" class="w-12 lg:w-16">
        <span class="text-xl lg:text-3xl text-gray-600 font-cinzel font-bold uppercase">Gatekeeper</span>
      </router-link>
      <router-link :to="{ path: '/home' }" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Home</router-link>
      <router-link v-if="$store.getters['getTokenScope'].includes('user:list')" :to="{ path: '/users', query: { page: 1, per_page: 25, sort: 'name_up' }}" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Users</router-link>
      <router-link v-if="$store.getters['getTokenScope'].includes('role:list')" :to="{ path: '/roles', query: { page: 1, per_page: 25, sort: 'name_up' }}" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Roles</router-link>
      <router-link v-if="$store.getters['getTokenScope'].includes('permission:list')" :to="{ path: '/permissions', query: { page: 1, per_page: 25, sort: 'name_up' }}" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Permissions</router-link>
      <router-link :to="{ path: '/logout' }" :class="`text-gray-600 hover:text-green-600 font-medium uppercase`">Logout</router-link>
    </div>
    <div class="lg:px-20 px-4 lg:px-0 py-4 lg:py-8 flex-grow overflow-y-auto">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "AuthLayout",
  data() {
    return {
      show_sidebar: false,
    }
  },
  methods: {
    logout() {

    },
  },
}
</script>

<style scoped>
  .router-link-active {
    @apply text-green-500
  }
</style>

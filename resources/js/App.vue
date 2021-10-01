<template>
  <router-view :key="$route.fullPath" />
</template>

<script>
export default {
  name: "App",
  data() {
    return {
      count: 0,
    }
  },
  created() {
    setInterval(function () {
      if (this.$store.getters['getToken']) {
        axios.get(`/api/auth`, {
          headers: {
            Authorization: `Bearer ${this.$store.getters['getToken']}`,
          },
        })
          .then(response => {
            this.$store.dispatch('setUser', response.data.user);
            this.$store.dispatch('setTokenScope', response.data.token_scope);
          })
          .catch(error => {
            this.$store.dispatch('setToken', null);
            this.$store.dispatch('setTokenScope', null);
            this.$store.dispatch('setUser', null);
          });
      }
    }.bind(this), 5000);
  },
}
</script>

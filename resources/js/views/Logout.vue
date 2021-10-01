<template>

</template>

<script>
  export default {
    name: "Logout",
    created() {
      axios.delete('/api/auth', {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
        .then(response => {
          this.$store.dispatch('setToken', null);
          this.$router.push('/login');
        })
        .catch(error => {
          alert('Failed to logout. Please try again later.');
        });
    },
  }
</script>

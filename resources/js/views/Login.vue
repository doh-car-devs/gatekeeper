<template>
  <div class="h-full flex justify-center items-center bg-gray-100">
    <div class="w-11/12 lg:w-96 bg-white rounded shadow-lg">
      <img src="images/DOH-Logo.png" class="w-24 mx-auto py-8">
      <p class="text-3xl text-gray-700 text-center font-bold font-cinzel uppercase mb-8">Gatekeeper</p>

      <div v-if="login_failed" class="text-red-500 bg-red-50 mx-8 my-4 p-2">Incorrect username or password.</div>

      <form @submit.prevent>
        <div class="flex flex-col px-8 pb-8">
          <div class="flex flex-col mb-4">
            <label>Username</label>
            <input type="text" v-model="form_data.username" class="px-2 py-1 border rounded" />
          </div>

          <div class="flex flex-col mb-4">
            <label>Password</label>
            <input type="password" v-model="form_data.password" class="px-2 py-1 border rounded" />
          </div>

          <div class="flex flex-col">
            <button type="submit" @click="login" class="text-white bg-green-600 px-2 py-1 border rounded hover:bg-green-500 focus:bg-green-500">Login</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "Login",
  data() {
    return {
      form_data: {
        username: '',
        password: '',
      },
      login_failed: false,
    }
  },
  beforeCreate() {
    if (this.$store.getters['getToken'] !== null) {
      this.$router.push('/');
    }
  },
  methods: {
    login() {
      this.login_failed = false;

      axios.post('/api/auth', this.form_data)
      .then(response => {
        this.$store.commit('setToken', response.data.token);
        this.$store.commit('setTokenScope', response.data.token_scope);
        this.$router.push('/');
      })
      .catch(error => {
        this.login_failed = true;
      });
    },
  },
}
</script>

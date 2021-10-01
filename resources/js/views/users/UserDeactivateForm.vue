<template>
  <form @submit.prevent>
    <div class="flex flex-col pt-4">
      <p class="font-medium">You are about to deactivate this user:</p>
      <p>{{ `${user.last_name}, ${user.given_name} ${user.middle_name} ${user.name_suffix ?? ''}` }}</p>
      <p>{{ user.username }}</p>
      <div class="flex flex-wrap">
        <span v-for="role in user.roles" :key="role.id" class="text-sm bg-gray-100 px-4 py-1 m-1 rounded-full">
          {{ role.name }}
        </span>
      </div>

      <div class="flex justify-between pt-4">
        <button-secondary type="submit" @click="$emit('cancel')">Cancel</button-secondary>
        <button-delete type="submit" @click="deactivateUser">Confirm</button-delete>
      </div>
    </div>
  </form>
</template>

<script>
import ButtonSecondary from "../../components/buttons/ButtonSecondary";
import ButtonDelete from "../../components/buttons/ButtonDelete";

export default {
  name: "UserDeactivateForm",
  components: {
    ButtonDelete,
    ButtonSecondary,
  },
  props: {
    user: Array|Object,
  },
  methods: {
    deactivateUser() {
      axios.delete(`/api/user_access/${this.user.id}`, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => this.$emit('deactivatedUser', this.user.id))
      .catch(error => {});
    },
  },
}
</script>

<template>
  <form @submit.prevent>
    <div class="flex flex-col pt-4">
      <p class="font-medium">You are about to delete this role:</p>
      <p>{{ role.name }}</p>
      <p class="italic">{{ role.description }}</p>
      <p class="text-sm uppercase">{{ `${role.users_count} Members` }}</p>

      <p class="italic mt-4">*** Deleting this role will remove the role assigned to the users.</p>

      <div class="flex justify-between pt-4">
        <button-secondary type="submit" @click="$emit('cancel')">Cancel</button-secondary>
        <button-delete type="submit" @click="deleteRole">Confirm</button-delete>
      </div>
    </div>
  </form>
</template>

<script>
import ButtonSecondary from "../../components/buttons/ButtonSecondary";
import ButtonDelete from "../../components/buttons/ButtonDelete";

export default {
  name: "RoleDeleteForm",
  components: {
    ButtonDelete,
    ButtonSecondary,
  },
  props: {
    role: Object,
  },
  methods: {
    deleteRole() {
      axios.delete(`/api/roles/${this.role.id}`, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => this.$emit('deletedRole', this.role.id))
      .catch(error => {});
    },
  },
}
</script>

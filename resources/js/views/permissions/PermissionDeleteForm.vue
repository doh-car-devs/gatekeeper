<template>
  <form @submit.prevent>
    <div class="flex flex-col pt-4">
      <p class="font-medium">You are about to delete this permission:</p>
      <p>{{ permission.name }}</p>
      <p class="italic">{{ permission.description }}</p>
      <p class="text-sm uppercase">{{ `${permission.roles_count} Members` }}</p>

      <p class="italic mt-4">*** Deleting this permission will remove the permission assigned to the roles.</p>

      <div class="flex justify-between pt-4">
        <button-secondary type="submit" @click="$emit('cancel')">Cancel</button-secondary>
        <button-delete type="submit" @click="deletePermission">Confirm</button-delete>
      </div>
    </div>
  </form>
</template>

<script>
import ButtonSecondary from "../../components/buttons/ButtonSecondary";
import ButtonDelete from "../../components/buttons/ButtonDelete";

export default {
  name: "PermissionDeleteForm",
  components: {
    ButtonDelete,
    ButtonSecondary,
  },
  props: {
    permission: Object,
  },
  methods: {
    deletePermission() {
      axios.delete(`/api/permissions/${this.permission.id}`, {
        headers: {
          Authorization: `Bearer ${this.$store.getters['getToken']}`,
        },
      })
      .then(response => this.$emit('deletedPermission', this.permission.id))
      .catch(error => {});
    },
  },
}
</script>

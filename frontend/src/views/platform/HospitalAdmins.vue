<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Hospital Admins</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Create and manage admin accounts for hospitals.
        </p>
      </div>
      <button @click="openCreate"
        class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm">
        <Plus class="w-3.5 h-3.5" />
        Add Admin
      </button>
    </div>

    <!-- Success -->
    <div v-if="successMsg"
      class="mb-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium rounded-lg px-4 py-3">
      <CheckCircle class="w-4 h-4 flex-shrink-0" />{{ successMsg }}
    </div>

    <!-- Error -->
    <div v-if="adminStore.error && !showForm"
      class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
      <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ adminStore.error }}
    </div>

    <!-- Loading -->
    <div v-if="adminStore.loading && adminStore.admins.length === 0" class="space-y-3">
      <div v-for="n in 3" :key="n" class="h-14 bg-white rounded-xl border border-gray-100 animate-pulse" />
    </div>

    <!-- No hospitals -->
    <div v-else-if="hospitalStore.hospitals.length === 0 && !hospitalStore.loading"
      class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-800 font-medium">
      No hospitals registered yet. Register a hospital first before adding admins.
    </div>

    <!-- Table -->
    <HospitalAdminTable
      v-else
      :admins="adminStore.admins"
      @edit="openEdit"
      @delete="confirmDelete"
    />

    <!-- Create / Edit modal -->
    <HospitalAdminForm
      v-if="showForm"
      :admin="selectedAdmin"
      :hospitals="hospitalStore.hospitals"
      :loading="adminStore.loading"
      :error="formError"
      @close="closeForm"
      @submit="handleSubmit"
    />

    <!-- Delete confirmation -->
    <div v-if="showDeleteConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Delete Admin</h3>
            <p class="text-xs text-gray-400 mt-0.5">This cannot be undone.</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Delete <span class="font-semibold text-gray-800">{{ adminToDelete?.first_name }} {{ adminToDelete?.last_name }}</span>?
        </p>
        <div class="flex items-center justify-end gap-3">
          <button @click="showDeleteConfirm = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button @click="handleDelete" :disabled="adminStore.loading"
            class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="adminStore.loading" class="w-3.5 h-3.5 animate-spin" />
            Delete
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Plus, AlertCircle, CheckCircle, Trash2, Loader2 } from "lucide-vue-next";
import { useHospitalStore } from "../../stores/hospitalStore";
import { useHospitalAdminStore } from "../../stores/hospitalAdminStore";
import HospitalAdminTable from "../../components/hospital/HospitalAdminTable.vue";
import HospitalAdminForm from "../../components/hospital/HospitalAdminForm.vue";

const hospitalStore = useHospitalStore();
const adminStore = useHospitalAdminStore();

const showForm = ref(false);
const selectedAdmin = ref(null);
const formError = ref(null);
const successMsg = ref(null);
const showDeleteConfirm = ref(false);
const adminToDelete = ref(null);

onMounted(() => Promise.all([hospitalStore.fetchAll(), adminStore.fetchAll()]));

function openCreate() {
  selectedAdmin.value = null;
  formError.value = null;
  successMsg.value = null;
  showForm.value = true;
}

function openEdit(admin) {
  selectedAdmin.value = admin;
  formError.value = null;
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  selectedAdmin.value = null;
  formError.value = null;
}

function flash(msg) {
  successMsg.value = msg;
  setTimeout(() => { successMsg.value = null; }, 5000);
}

async function handleSubmit(payload) {
  formError.value = null;
  try {
    if (selectedAdmin.value) {
      await adminStore.update(selectedAdmin.value.id, payload);
      flash("Admin updated successfully.");
    } else {
      const result = await adminStore.create(payload);
      const user = result?.user ?? result;
      flash(`Admin "${user.first_name} ${user.last_name}" created successfully.`);
    }
    closeForm();
  } catch (err) {
    const errors = err.response?.data?.errors;
    formError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
  }
}

function confirmDelete(admin) {
  adminToDelete.value = admin;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  try {
    await adminStore.destroy(adminToDelete.value.id);
    showDeleteConfirm.value = false;
    adminToDelete.value = null;
    flash("Admin deleted successfully.");
  } catch { /* store.error handles it */ }
}
</script>

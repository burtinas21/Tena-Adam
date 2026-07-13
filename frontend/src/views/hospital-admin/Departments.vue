<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <!-- Page header -->
    <div
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6"
    >
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
          Departments
        </h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Manage hospital departments and their details.
        </p>
      </div>
      <button
        @click="openCreate"
        class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <Plus class="w-3.5 h-3.5" />
        New Department
      </button>
    </div>

    <!-- Error banner -->
    <div
      v-if="(store.error && !showForm) || hospitalError"
      class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
    >
      <AlertCircle class="w-4 h-4 flex-shrink-0" />
      {{ hospitalError || store.error }}
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="store.loading && store.departments.length === 0"
      class="space-y-3"
    >
      <div
        v-for="n in 4"
        :key="n"
        class="h-14 bg-white rounded-xl border border-gray-100 animate-pulse"
      />
    </div>

    <!-- Table -->
    <DepartmentTable
      v-else
      :departments="store.departments"
      @edit="openEdit"
      @delete="confirmDelete"
    />

    <!-- Create / Edit modal -->
    <DepartmentForm
      v-if="showForm"
      :department="selectedDepartment"
      :hospital-id="hospitalId"
      :loading="store.loading"
      :error="formError"
      @close="closeForm"
      @submit="handleFormSubmit"
    />

    <!-- Delete confirmation modal -->
    <div
      v-if="showDeleteConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div
            class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0"
          >
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Delete Department</h3>
            <p class="text-xs text-gray-400 mt-0.5">
              This action cannot be undone.
            </p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Are you sure you want to delete
          <span class="font-semibold text-gray-800">{{
            departmentToDelete?.name
          }}</span
          >?
        </p>
        <div class="flex items-center justify-end gap-3">
          <button
            @click="showDeleteConfirm = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
          >
            Cancel
          </button>
          <button
            @click="handleDelete"
            :disabled="store.loading"
            class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <Loader2 v-if="store.loading" class="w-3.5 h-3.5 animate-spin" />
            Delete
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Plus, AlertCircle, Trash2, Loader2 } from "lucide-vue-next";
import { useDepartmentStore } from "../../stores/departmentStore";
import hospitalApi from "../../api/hospitalApi";
import DepartmentTable from "../../components/hospital/DepartmentTable.vue";
import DepartmentForm from "../../components/hospital/DepartmentForm.vue";

const store = useDepartmentStore();

// Hospital ID resolved from the API on mount
const hospitalId = ref("");
const hospitalError = ref(null);

// Form state
const showForm = ref(false);
const selectedDepartment = ref(null);
const formError = ref(null);

// Delete state
const showDeleteConfirm = ref(false);
const departmentToDelete = ref(null);

onMounted(async () => {
  // Fetch departments and the user's hospital in parallel
  try {
    const [, hospitalRes] = await Promise.all([
      store.fetchAll(),
      hospitalApi.getAll(),
    ]);
    const hospitals = hospitalRes.data?.data ?? hospitalRes.data ?? [];
    if (hospitals.length > 0) {
      hospitalId.value = hospitals[0].id;
    } else {
      hospitalError.value = "No hospital found for your account.";
    }
  } catch {
    hospitalError.value = "Failed to load hospital information.";
  }
});

function openCreate() {
  selectedDepartment.value = null;
  formError.value = null;
  showForm.value = true;
}

function openEdit(dept) {
  selectedDepartment.value = dept;
  formError.value = null;
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  selectedDepartment.value = null;
  formError.value = null;
}

async function handleFormSubmit(payload) {
  formError.value = null;
  try {
    if (selectedDepartment.value) {
      await store.update(selectedDepartment.value.id, payload);
    } else {
      await store.create(payload);
    }
    closeForm();
  } catch (err) {
    const errors = err.response?.data?.errors;
    if (errors) {
      formError.value = Object.values(errors).flat().join(" ");
    } else {
      formError.value =
        err.response?.data?.message ||
        "Something went wrong. Please try again.";
    }
  }
}

function confirmDelete(dept) {
  departmentToDelete.value = dept;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  if (!departmentToDelete.value) return;
  try {
    await store.destroy(departmentToDelete.value.id);
    showDeleteConfirm.value = false;
    departmentToDelete.value = null;
  } catch {
    // error is surfaced via store.error
  }
}
</script>

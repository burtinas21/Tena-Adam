<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 class="text-base font-bold text-gray-800">
            {{ isEditing ? 'Edit Hospital Admin' : 'Add Hospital Admin' }}
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ isEditing ? 'Update admin account details.' : 'Create a new admin account for a hospital.' }}
          </p>
        </div>
        <button @click="$emit('close')" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
          <X class="w-4 h-4" />
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="px-6 py-5 space-y-4">
        <div v-if="error" class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
          <span>{{ error }}</span>
        </div>

        <!-- Hospital selector (create only) -->
        <div v-if="!isEditing">
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Hospital <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.hospital_id" required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          >
            <option value="" disabled>Select hospital</option>
            <option v-for="h in hospitals" :key="h.id" :value="h.id">{{ h.name }}</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              First Name <span class="text-red-500">*</span>
            </label>
            <input v-model="form.first_name" type="text" placeholder="Abebe" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Last Name <span class="text-red-500">*</span>
            </label>
            <input v-model="form.last_name" type="text" placeholder="Kebede" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Email <span class="text-red-500">*</span>
          </label>
          <input v-model="form.email" type="email" placeholder="admin@hospital.et" required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
          <input v-model="form.phone" type="text" placeholder="+251 911 000 000"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
        </div>

        <!-- Password (create only) -->
        <div v-if="!isEditing">
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Password <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <input v-model="form.password" :type="showPassword ? 'text' : 'password'"
              placeholder="Min. 8 characters" required minlength="8"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 pr-10 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            <button type="button" @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
              <Eye v-if="!showPassword" class="w-4 h-4" />
              <EyeOff v-else class="w-4 h-4" />
            </button>
          </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="button" @click="$emit('close')"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button type="submit" :disabled="loading"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="loading" class="w-3.5 h-3.5 animate-spin" />
            {{ isEditing ? 'Save Changes' : 'Create Admin' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { X, AlertCircle, Loader2, Eye, EyeOff } from "lucide-vue-next";

const props = defineProps({
  admin:     { type: Object, default: null },
  hospitals: { type: Array,  default: () => [] },
  loading:   { type: Boolean, default: false },
  error:     { type: String,  default: null },
});
const emit = defineEmits(["close", "submit"]);

const isEditing = computed(() => !!props.admin);
const showPassword = ref(false);

const form = ref({
  hospital_id: "", first_name: "", last_name: "",
  email: "", phone: "", password: "",
});

watch(() => props.admin, (a) => {
  if (a) {
    form.value = {
      hospital_id: "", // not editable
      first_name: a.first_name ?? "",
      last_name:  a.last_name  ?? "",
      email:      a.email      ?? "",
      phone:      a.phone      ?? "",
      password:   "",
    };
  } else {
    form.value = { hospital_id: "", first_name: "", last_name: "", email: "", phone: "", password: "" };
  }
}, { immediate: true });

function handleSubmit() {
  if (isEditing.value) {
    emit("submit", {
      first_name: form.value.first_name,
      last_name:  form.value.last_name,
      email:      form.value.email,
      phone:      form.value.phone || null,
    });
  } else {
    emit("submit", {
      hospital_id: form.value.hospital_id,
      first_name:  form.value.first_name,
      last_name:   form.value.last_name,
      email:       form.value.email,
      phone:       form.value.phone || null,
      password:    form.value.password,
    });
  }
}
</script>

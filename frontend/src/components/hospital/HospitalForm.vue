<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] flex flex-col">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 flex-shrink-0">
        <div>
          <h3 class="text-base font-bold text-gray-800">
            {{ isEditing ? 'Edit Hospital' : 'Register Hospital' }}
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ isEditing ? 'Update hospital information.' : 'Fill in the details to register a hospital.' }}
          </p>
        </div>
        <button @click="$emit('close')" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
          <X class="w-4 h-4" />
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
        <div v-if="error" class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
          <span>{{ error }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <!-- Name -->
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Hospital Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name" type="text" placeholder="e.g. General Hospital" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Code (create only) -->
          <div v-if="!isEditing">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Code</label>
            <input
              v-model="form.code" type="text" maxlength="20" placeholder="e.g. GH-001"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Registration Number (create only) -->
          <div v-if="!isEditing">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Reg. Number</label>
            <input
              v-model="form.registration_number" type="text" placeholder="MOH-123456"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Address -->
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Address <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.address" type="text" placeholder="123 Main Street" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- City -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              City <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.city" type="text" placeholder="Addis Ababa" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Region -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Region</label>
            <input
              v-model="form.region" type="text" placeholder="Oromia"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
            <input
              v-model="form.phone" type="text" placeholder="+251 911 000 000"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
            <input
              v-model="form.email" type="email" placeholder="info@hospital.et"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>

          <!-- Website (create only) -->
          <div class="col-span-2" v-if="!isEditing">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Website</label>
            <input
              v-model="form.website" type="text" placeholder="https://hospital.et"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>
        </div>
      </form>

      <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
        <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
          Cancel
        </button>
        <button
          @click="handleSubmit"
          :disabled="loading"
          class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2"
        >
          <Loader2 v-if="loading" class="w-3.5 h-3.5 animate-spin" />
          {{ isEditing ? 'Save Changes' : 'Register Hospital' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { X, AlertCircle, Loader2 } from "lucide-vue-next";

const props = defineProps({
  hospital: { type: Object, default: null },
  loading: { type: Boolean, default: false },
  error: { type: String, default: null },
});
const emit = defineEmits(["close", "submit"]);

const isEditing = computed(() => !!props.hospital);

const form = ref({
  name: "", code: "", address: "", city: "", region: "",
  phone: "", email: "", website: "", registration_number: "",
});

watch(
  () => props.hospital,
  (h) => {
    if (h) {
      form.value = {
        name: h.name ?? "", code: h.code ?? "",
        address: h.address ?? "", city: h.city ?? "",
        region: h.region ?? "", phone: h.phone ?? "",
        email: h.email ?? "", website: h.website ?? "",
        registration_number: h.registration_number ?? "",
      };
    } else {
      form.value = {
        name: "", code: "", address: "", city: "", region: "",
        phone: "", email: "", website: "", registration_number: "",
      };
    }
  },
  { immediate: true }
);

function handleSubmit() {
  // UpdateHospitalRequest only accepts: name, address, city, phone, email
  const payload = isEditing.value
    ? {
        name: form.value.name,
        address: form.value.address,
        city: form.value.city,
        phone: form.value.phone || null,
        email: form.value.email || null,
      }
    : {
        name: form.value.name,
        code: form.value.code || null,
        address: form.value.address,
        city: form.value.city,
        region: form.value.region || null,
        phone: form.value.phone || null,
        email: form.value.email || null,
        website: form.value.website || null,
        registration_number: form.value.registration_number || null,
      };
  emit("submit", payload);
}
</script>

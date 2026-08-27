<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
          My Profile
        </h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Complete your profile to book appointments
        </p>
      </div>

      <!-- Profile completion status banner -->
      <div
        :class="
          isActive
            ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
            : 'bg-amber-50 border-amber-200 text-amber-700'
        "
        class="flex items-center gap-3 border rounded-xl px-4 py-3 mb-6 text-sm font-medium"
      >
        <CheckCircle v-if="isActive" class="w-4 h-4 flex-shrink-0" />
        <AlertCircle v-else class="w-4 h-4 flex-shrink-0" />
        <span v-if="isActive"
          >Profile complete — you can book appointments.</span
        >
        <span v-else>
          Complete your address, occupation and add a primary emergency contact
          to activate your account.
        </span>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loadingProfile" class="space-y-4">
        <div
          v-for="n in 3"
          :key="n"
          class="h-16 bg-white rounded-xl border border-gray-100 animate-pulse"
        />
      </div>

      <template v-else>
        <!-- ── Personal Info card ─────────────────────────────────────── -->
        <div
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-5"
        >
          <h2
            class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2"
          >
            <User class="w-4 h-4 text-[#004795]" />
            Personal Information
          </h2>

          <!-- Read-only info -->
          <div class="grid grid-cols-2 gap-4 mb-5">
            <InfoItem label="Full Name" :value="fullName" />
            <InfoItem label="Email" :value="patient?.user?.email" />
            <InfoItem label="Phone" :value="patient?.user?.phone || '—'" />
            <InfoItem label="Blood Type" :value="patient?.blood_type || '—'" />
            <InfoItem
              label="Date of Birth"
              :value="patient?.date_of_birth || '—'"
            />
            <InfoItem label="Gender" :value="patient?.gender || '—'" />
          </div>

          <!-- Editable fields -->
          <form @submit.prevent="saveProfile" class="space-y-4">
            <div
              v-if="profileError"
              class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
            >
              <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{
                profileError
              }}
            </div>
            <div
              v-if="profileSuccess"
              class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium rounded-lg px-3 py-2.5"
            >
              <CheckCircle class="w-3.5 h-3.5 flex-shrink-0" />{{
                profileSuccess
              }}
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Address <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="profileForm.address"
                rows="2"
                required
                placeholder="Sub-city, Kebele, House No. — Addis Ababa"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none"
              />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                  Occupation <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="profileForm.occupation"
                  type="text"
                  required
                  placeholder="e.g. Teacher"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5"
                  >National ID</label
                >
                <input
                  v-model="profileForm.national_id"
                  type="text"
                  placeholder="e.g. 123456789"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
                />
              </div>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="savingProfile"
                class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2"
              >
                <Loader2
                  v-if="savingProfile"
                  class="w-3.5 h-3.5 animate-spin"
                />
                Save Profile
              </button>
            </div>
          </form>
        </div>

        <!-- ── Emergency Contacts card ───────────────────────────────── -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
              <Phone class="w-4 h-4 text-[#004795]" />
              Emergency Contacts
              <span class="text-[10px] font-normal text-gray-400 ml-1">
                (at least one primary required)
              </span>
            </h2>
            <button
              @click="openContactForm(null)"
              class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-1.5 px-3 rounded-lg flex items-center gap-1.5 transition"
            >
              <Plus class="w-3.5 h-3.5" /> Add
            </button>
          </div>

          <!-- Contact error -->
          <div
            v-if="contactError"
            class="mb-3 flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
          >
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{
              contactError
            }}
          </div>

          <!-- Empty state -->
          <div v-if="!contacts.length" class="py-8 text-center text-gray-400">
            <PhoneOff class="w-7 h-7 mx-auto mb-2 text-gray-300" />
            <p class="text-sm font-medium">No emergency contacts yet</p>
            <p class="text-xs mt-1">
              Add at least one primary contact to activate your account.
            </p>
          </div>

          <!-- Contacts list -->
          <div v-else class="divide-y divide-gray-50">
            <div
              v-for="contact in contacts"
              :key="contact.id"
              class="py-3 flex items-center justify-between gap-4"
            >
              <div class="flex items-center gap-3">
                <div
                  class="w-9 h-9 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0"
                >
                  <span class="text-xs font-bold text-[#004795]">
                    {{ (contact.name?.[0] ?? "?").toUpperCase() }}
                  </span>
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-gray-800">
                      {{ contact.name }}
                    </p>
                    <span
                      v-if="contact.is_primary"
                      class="text-[9px] font-bold bg-[#004795] text-white px-1.5 py-0.5 rounded uppercase"
                    >
                      Primary
                    </span>
                  </div>
                  <p class="text-xs text-gray-400 mt-0.5">
                    {{ contact.relationship }} · {{ contact.phone }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-1 flex-shrink-0">
                <button
                  @click="openContactForm(contact)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-[#004795] hover:bg-[#004795]/10 transition"
                >
                  <Pencil class="w-3.5 h-3.5" />
                </button>
                <button
                  @click="deleteContact(contact.id)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- ── Contact Form Modal ───────────────────────────────────────── -->
    <div
      v-if="showContactForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeContactForm"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div
          class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100"
        >
          <h3 class="text-sm font-bold text-gray-800">
            {{ editingContact ? "Edit Contact" : "Add Emergency Contact" }}
          </h3>
          <button
            @click="closeContactForm"
            class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
        <form @submit.prevent="saveContact" class="px-6 py-4 space-y-4">
          <div
            v-if="contactFormError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
          >
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{
              contactFormError
            }}
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Full Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="contactForm.name"
              type="text"
              required
              placeholder="e.g. Abebe Girma"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Relationship <span class="text-red-500">*</span>
              </label>
              <input
                v-model="contactForm.relationship"
                type="text"
                required
                placeholder="e.g. Spouse"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Phone <span class="text-red-500">*</span>
              </label>
              <input
                v-model="contactForm.phone"
                type="text"
                required
                placeholder="+251 911 000 000"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
              />
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5"
              >Email</label
            >
            <input
              v-model="contactForm.email"
              type="email"
              placeholder="contact@example.com"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>
          <div class="flex items-center gap-3">
            <button
              type="button"
              @click="contactForm.is_primary = !contactForm.is_primary"
              :class="contactForm.is_primary ? 'bg-[#004795]' : 'bg-gray-300'"
              class="relative w-10 h-5 rounded-full transition-colors duration-300 focus:outline-none"
            >
              <span
                :class="
                  contactForm.is_primary ? 'translate-x-5' : 'translate-x-0'
                "
                class="absolute left-0.5 top-0.5 w-4 h-4 rounded-full bg-white shadow-md transform transition-transform duration-300"
              ></span>
            </button>
            <span class="text-sm text-gray-700 font-medium"
              >Set as primary contact</span
            >
          </div>

          <div class="flex items-center justify-end gap-3 pt-1">
            <button
              type="button"
              @click="closeContactForm"
              class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="savingContact"
              class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2"
            >
              <Loader2 v-if="savingContact" class="w-3.5 h-3.5 animate-spin" />
              {{ editingContact ? "Save Changes" : "Add Contact" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, defineComponent, h } from "vue";
import {
  User,
  Phone,
  Plus,
  Pencil,
  Trash2,
  X,
  CheckCircle,
  AlertCircle,
  Loader2,
  PhoneOff,
} from "lucide-vue-next";
import api from "../../../api/axios";
import { useAuthStore } from "../../../stores/authStore";
const InfoItem = defineComponent({
  props: { label: String, value: String },
  setup: (p) => () =>
    h("div", {}, [
      h(
        "p",
        {
          class:
            "text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5",
        },
        p.label,
      ),
      h("p", { class: "text-sm font-semibold text-gray-800" }, p.value || "—"),
    ]),
});

const authStore = useAuthStore();

// ── State ─────────────────────────────────────────────────────────────────
const patient = ref(null);
const contacts = ref([]);
const loadingProfile = ref(false);

// Profile form
const profileForm = ref({ address: "", occupation: "", national_id: "" });
const savingProfile = ref(false);
const profileError = ref(null);
const profileSuccess = ref(null);
const contactError = ref(null);

// Contact form
const showContactForm = ref(false);
const editingContact = ref(null);
const savingContact = ref(false);
const contactFormError = ref(null);
const contactForm = ref({
  name: "",
  relationship: "",
  phone: "",
  email: "",
  is_primary: false,
});

const isActive = computed(() => patient.value?.patient_status === "active");
const fullName = computed(() => {
  const u = authStore.user;
  return u ? `${u.first_name ?? ""} ${u.last_name ?? ""}`.trim() : "—";
});

// ── Load ──────────────────────────────────────────────────────────────────
async function load() {
  try {
    loadingProfile.value = true;
    const res = await api.get("/patient/profile");
    const data = res.data?.data ?? res.data;
    patient.value = data;
    contacts.value = data.emergency_contacts ?? data.emergencyContacts ?? [];
    // Pre-fill editable fields
    profileForm.value.address = data.address ?? "";
    profileForm.value.occupation = data.occupation ?? "";
    profileForm.value.national_id = data.national_id ?? "";
  } catch (err) {
    profileError.value =
      err.response?.data?.message || "Failed to load profile.";
  } finally {
    loadingProfile.value = false;
  }
}

onMounted(load);

async function saveProfile() {
  profileError.value = null;
  profileSuccess.value = null;
  try {
    savingProfile.value = true;
    const res = await api.put("/patient/profile", {
      address: profileForm.value.address,
      occupation: profileForm.value.occupation,
      national_id: profileForm.value.national_id || null,
    });
    const data = res.data?.data ?? res.data;
    patient.value = data;
    contacts.value =
      data.emergency_contacts ?? data.emergencyContacts ?? contacts.value;
    profileSuccess.value = "Profile saved successfully!";
    setTimeout(() => {
      profileSuccess.value = null;
    }, 4000);
  } catch (err) {
    const errors = err.response?.data?.errors;
    profileError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
  } finally {
    savingProfile.value = false;
  }
}

// ── Contact CRUD ──────────────────────────────────────────────────────────
function openContactForm(contact) {
  editingContact.value = contact;
  contactFormError.value = null;
  if (contact) {
    contactForm.value = {
      name: contact.name ?? "",
      relationship: contact.relationship ?? "",
      phone: contact.phone ?? "",
      email: contact.email ?? "",
      is_primary: contact.is_primary ?? false,
    };
  } else {
    contactForm.value = {
      name: "",
      relationship: "",
      phone: "",
      email: "",
      is_primary: false,
    };
  }
  showContactForm.value = true;
}

function closeContactForm() {
  showContactForm.value = false;
  editingContact.value = null;
  contactFormError.value = null;
}

async function saveContact() {
  contactFormError.value = null;
  try {
    savingContact.value = true;
    if (editingContact.value) {
      const res = await api.put(
        `/patient/emergency-contacts/${editingContact.value.id}`,
        contactForm.value,
      );
      const updated = res.data?.data ?? res.data;
      const idx = contacts.value.findIndex(
        (c) => c.id === editingContact.value.id,
      );
      if (idx !== -1) contacts.value[idx] = updated;
    } else {
      const res = await api.post(
        "/patient/emergency-contacts",
        contactForm.value,
      );
      const created = res.data?.data ?? res.data;
      contacts.value.push(created);
    }
    closeContactForm();
    // Reload to get updated patient_status
    await load();
  } catch (err) {
    const errors = err.response?.data?.errors;
    contactFormError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
  } finally {
    savingContact.value = false;
  }
}

async function deleteContact(id) {
  contactError.value = null;
  try {
    await api.delete(`/patient/emergency-contacts/${id}`);
    contacts.value = contacts.value.filter((c) => c.id !== id);
    await load();
  } catch (err) {
    contactError.value =
      err.response?.data?.message || "Failed to delete contact.";
  }
}
</script>

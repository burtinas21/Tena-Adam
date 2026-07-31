<template>
  <main
    class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200"
  >
    <div class="max-w-5xl mx-auto">
      <!-- Header -->
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            My Appointments
          </h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            Book and manage your medical appointments
          </p>
        </div>
        <div class="relative group">
          <button
            @click="openBook"
            :disabled="!profileActive"
            :class="
              profileActive
                ? 'bg-[#004795] hover:bg-[#003670] text-white cursor-pointer shadow-sm'
                : 'bg-gray-200 text-gray-400 cursor-not-allowed'
            "
            class="font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition"
          >
            <Plus class="w-3.5 h-3.5" /> Book Appointment
          </button>
          <div
            v-if="!profileActive"
            class="absolute right-0 top-full mt-2 z-10 w-64 bg-gray-900 text-white text-xs rounded-lg px-3 py-2 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
          >
            Complete your profile first — add your address, occupation and a
            primary emergency contact.
            <div
              class="absolute -top-1 right-4 w-2 h-2 bg-gray-900 rotate-45"
            ></div>
          </div>
        </div>
      </div>
      <div
        v-if="!profileActive"
        class="mb-4 flex items-center justify-between gap-4 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium rounded-xl px-4 py-3"
      >
        <div class="flex items-center gap-2">
          <AlertCircle class="w-4 h-4 flex-shrink-0 text-amber-500" />
          <span
            >Your profile is incomplete. Add your address, occupation and a
            primary emergency contact to book appointments.</span
          >
        </div>
        <router-link
          to="/patient/profile"
          class="flex-shrink-0 font-bold text-[#004795] underline hover:no-underline"
          >Complete Profile</router-link
        >
      </div>
      <!-- Stat cards -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        <!-- Awaiting Payment -->
        <div
          class="bg-white rounded-xl border border-orange-100 shadow-sm p-4 flex flex-col gap-1"
        >
          <span class="text-2xl font-extrabold text-orange-500">{{
            store.pendingPayment.length
          }}</span>
          <span class="text-xs font-semibold text-gray-500 leading-tight"
            >Awaiting Payment</span
          >
          <div class="mt-1 h-1 rounded-full bg-orange-100">
            <div
              class="h-1 rounded-full bg-orange-400 transition-all"
              :style="{
                width: store.appointments.length
                  ? (store.pendingPayment.length / store.appointments.length) *
                      100 +
                    '%'
                  : '0%',
              }"
            ></div>
          </div>
        </div>
        <!-- Pending -->
        <div
          class="bg-white rounded-xl border border-amber-100 shadow-sm p-4 flex flex-col gap-1"
        >
          <span class="text-2xl font-extrabold text-amber-500">{{
            store.pending.length
          }}</span>
          <span class="text-xs font-semibold text-gray-500 leading-tight"
            >Pending</span
          >
          <div class="mt-1 h-1 rounded-full bg-amber-100">
            <div
              class="h-1 rounded-full bg-amber-400 transition-all"
              :style="{
                width: store.appointments.length
                  ? (store.pending.length / store.appointments.length) * 100 +
                    '%'
                  : '0%',
              }"
            ></div>
          </div>
        </div>
        <!-- Confirmed -->
        <div
          class="bg-white rounded-xl border border-blue-100 shadow-sm p-4 flex flex-col gap-1"
        >
          <span class="text-2xl font-extrabold text-blue-600">{{
            store.confirmed.length
          }}</span>
          <span class="text-xs font-semibold text-gray-500 leading-tight"
            >Confirmed</span
          >
          <div class="mt-1 h-1 rounded-full bg-blue-100">
            <div
              class="h-1 rounded-full bg-blue-500 transition-all"
              :style="{
                width: store.appointments.length
                  ? (store.confirmed.length / store.appointments.length) * 100 +
                    '%'
                  : '0%',
              }"
            ></div>
          </div>
        </div>
        <!-- Completed -->
        <div
          class="bg-white rounded-xl border border-emerald-100 shadow-sm p-4 flex flex-col gap-1"
        >
          <span class="text-2xl font-extrabold text-emerald-600">{{
            store.completed.length
          }}</span>
          <span class="text-xs font-semibold text-gray-500 leading-tight"
            >Completed</span
          >
          <div class="mt-1 h-1 rounded-full bg-emerald-100">
            <div
              class="h-1 rounded-full bg-emerald-500 transition-all"
              :style="{
                width: store.appointments.length
                  ? (store.completed.length / store.appointments.length) * 100 +
                    '%'
                  : '0%',
              }"
            ></div>
          </div>
        </div>
        <!-- Cancelled -->
        <div
          class="bg-white rounded-xl border border-red-100 shadow-sm p-4 flex flex-col gap-1 col-span-2 sm:col-span-1"
        >
          <span class="text-2xl font-extrabold text-red-500">{{
            store.cancelled.length
          }}</span>
          <span class="text-xs font-semibold text-gray-500 leading-tight"
            >Cancelled</span
          >
          <div class="mt-1 h-1 rounded-full bg-red-100">
            <div
              class="h-1 rounded-full bg-red-400 transition-all"
              :style="{
                width: store.appointments.length
                  ? (store.cancelled.length / store.appointments.length) * 100 +
                    '%'
                  : '0%',
              }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Error -->
      <div
        v-if="store.error && !showBook"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ store.error }}
      </div>

      <!-- Profile incomplete banner -->

      <!-- Loading -->
      <div v-if="store.loading && !store.appointments.length" class="space-y-3">
        <div
          v-for="n in 3"
          :key="n"
          class="h-20 bg-white rounded-xl border border-gray-100 animate-pulse"
        />
      </div>

      <!-- Empty -->
      <div
        v-else-if="!store.appointments.length"
        class="bg-white rounded-xl border border-gray-100 py-16 flex flex-col items-center text-gray-400"
      >
        <CalendarDays class="w-10 h-10 mb-3 text-gray-300" />
        <p class="text-sm font-medium">No appointments yet</p>
        <button
          v-if="profileActive"
          @click="openBook"
          class="mt-3 text-xs text-[#004795] font-semibold hover:underline"
        >
          Book your first appointment
        </button>
      </div>

      <!-- Appointment list -->
      <div v-else class="space-y-3">
        <div
          v-for="appt in store.appointments"
          :key="appt.id"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow"
        >
          <div
            class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4"
          >
            <div class="flex items-start gap-4">
              <div
                class="w-10 h-10 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0 overflow-hidden"
              >
                <img
                  v-if="appt.doctor?.profile_picture_url"
                  :src="appt.doctor.profile_picture_url"
                  class="w-10 h-10 rounded-full object-cover"
                />
                <span v-else class="text-xs font-bold text-[#004795]">{{
                  initials(appt.doctor)
                }}</span>
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-sm">
                  Dr. {{ appt.doctor?.user?.first_name }}
                  {{ appt.doctor?.user?.last_name }}
                </p>
                <p class="text-xs text-gray-500 mt-0.5">
                  {{
                    appt.department?.name ??
                    appt.doctor?.department?.name ??
                    "—"
                  }}
                </p>
                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                  <span class="flex items-center gap-1"
                    ><CalendarDays class="w-3.5 h-3.5 text-gray-400" />{{
                      formatDate(appt.scheduled_time)
                    }}</span
                  >
                  <span class="flex items-center gap-1"
                    ><Clock class="w-3.5 h-3.5 text-gray-400" />{{
                      formatTime(appt.scheduled_time)
                    }}</span
                  >
                  <span
                    v-if="appt.is_telehealth"
                    class="flex items-center gap-1 text-blue-600"
                    ><Monitor class="w-3.5 h-3.5" /> Telemedicine</span
                  >
                </div>
                <!-- Uploaded documents chips -->
                <div
                  v-if="appt.documents?.length"
                  class="flex flex-wrap gap-1.5 mt-2"
                >
                  <button
                    v-for="doc in appt.documents"
                    :key="doc.id"
                    @click="downloadDoc(doc)"
                    :disabled="downloadingDoc[doc.id]"
                    class="inline-flex items-center gap-1 text-[10px] font-semibold text-[#004795] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full hover:bg-blue-100 transition disabled:opacity-50"
                  >
                    <Loader2
                      v-if="downloadingDoc[doc.id]"
                      class="w-2.5 h-2.5 animate-spin"
                    />
                    <Paperclip v-else class="w-2.5 h-2.5" />{{ doc.file_name }}
                  </button>
                </div>
                <!-- Referral badge -->
                <div v-if="appt.referrals?.length" class="mt-2">
                  <span
                    v-for="ref in appt.referrals"
                    :key="ref.id"
                    :class="referralBadgeClass(ref.status)"
                    class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border capitalize"
                  >
                    <ArrowRightLeft class="w-2.5 h-2.5" />
                    Referred{{
                      ref.referredToDoctor?.user
                        ? ` → Dr. ${ref.referredToDoctor.user.first_name} ${ref.referredToDoctor.user.last_name}`
                        : ""
                    }}
                    ({{ ref.status }})
                  </span>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
              <span
                :class="statusClass(appt.status)"
                class="text-xs font-semibold px-2.5 py-0.5 rounded-full border capitalize"
                >{{
                  appt.status === "pending_payment"
                    ? "Awaiting Payment"
                    : appt.status
                }}</span
              >
              <!-- Pay Now button for pending_payment -->
              <button
                v-if="appt.status === 'pending_payment'"
                @click="retryPayment(appt)"
                class="text-xs font-semibold text-white bg-orange-500 hover:bg-orange-600 px-3 py-1 rounded-lg transition"
              >
                Pay Now
              </button>
              <!-- 3-dot action menu for pending and pending_payment -->
              <div
                v-if="['pending', 'pending_payment'].includes(appt.status)"
                class="relative"
              >
                <button
                  @click.stop="toggleActionMenu(appt.id)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                >
                  <MoreVertical class="w-4 h-4" />
                </button>
                <div
                  v-if="openActionMenuId === appt.id"
                  class="absolute right-0 top-full mt-1 w-36 bg-white border border-gray-100 rounded-xl shadow-lg z-30 py-1"
                  @click.stop
                  @mousedown.stop
                >
                  <button
                    @click="doReschedule(appt)"
                    class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-[#004795] hover:bg-blue-50 transition"
                  >
                    <CalendarDays class="w-3.5 h-3.5" /> Reschedule
                  </button>
                  <button
                    @click="doCancel(appt.id)"
                    :disabled="store.loading"
                    class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition disabled:opacity-50"
                  >
                    <XCircle class="w-3.5 h-3.5" /> Cancel
                  </button>
                </div>
              </div>
              <!-- Hide from history for completed/cancelled -->
              <button
                v-if="appt.status === 'completed' || appt.status === 'cancelled'"
                @click="promptHideHistory(appt)"
                :disabled="hidingId === appt.id"
                class="inline-flex items-center gap-1 text-xs font-semibold text-gray-400 hover:text-red-500 transition disabled:opacity-50"
                title="Remove from my history"
              >
                <Loader2 v-if="hidingId === appt.id" class="w-3 h-3 animate-spin" />
                <Trash2 v-else class="w-3 h-3" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Book Appointment Modal ──────────────────────────────────────── -->
    <div
      v-if="showBook"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeBook"
    >
      <div
        class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[92vh] flex flex-col"
      >
        <div
          class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0"
        >
          <h3 class="text-sm font-bold text-gray-800">Book Appointment</h3>
          <button
            @click="closeBook"
            class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
        <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
          <div
            v-if="bookError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
          >
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{
              bookError
            }}
          </div>

          <!-- Hospital -->
          <div class="relative" ref="hospitalDropdownRef">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5"
              >Hospital
              <span
                v-if="!prefilledHospitalId"
                class="text-gray-400 font-normal"
                >(optional)</span
              ></label
            >
            <div
              v-if="prefilledHospitalId"
              class="w-full border border-emerald-200 bg-emerald-50 rounded-lg px-3 py-2.5 flex items-center gap-2"
            >
              <Building2 class="w-4 h-4 text-emerald-600 flex-shrink-0" />
              <span class="text-sm font-semibold text-emerald-800 flex-1">{{
                selectedHospitalObj?.name ?? "Hospital"
              }}</span>
              <span
                class="text-[10px] text-emerald-600 font-bold uppercase tracking-wide bg-emerald-100 px-1.5 py-0.5 rounded"
                >Auto-filled</span
              >
            </div>
            <template v-else>
              <div
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white cursor-pointer flex items-center justify-between gap-2"
                @click="hospitalDropdownOpen = !hospitalDropdownOpen"
              >
                <span
                  :class="
                    selectedHospitalObj ? 'text-gray-800' : 'text-gray-400'
                  "
                  >{{
                    selectedHospitalObj
                      ? selectedHospitalObj.name
                      : "All hospitals"
                  }}</span
                >
                <ChevronDown
                  class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform"
                  :class="hospitalDropdownOpen ? 'rotate-180' : ''"
                />
              </div>
              <div
                v-if="hospitalDropdownOpen"
                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
              >
                <div class="p-2 border-b border-gray-100">
                  <div
                    class="flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1.5"
                  >
                    <Search
                      class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"
                    /><input
                      v-model="hospitalSearch"
                      type="text"
                      placeholder="Search hospital..."
                      class="flex-1 text-xs bg-transparent outline-none"
                      @click.stop
                    />
                  </div>
                </div>
                <div class="max-h-48 overflow-y-auto">
                  <div
                    class="px-3 py-2 text-xs text-gray-600 hover:bg-gray-50 cursor-pointer"
                    :class="
                      !bookForm.hospital_id
                        ? 'bg-[#004795]/5 font-semibold text-[#004795]'
                        : ''
                    "
                    @click="selectHospital(null)"
                  >
                    All hospitals
                  </div>
                  <div
                    v-for="hosp in filteredHospitals"
                    :key="hosp.id"
                    class="px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 cursor-pointer"
                    :class="
                      bookForm.hospital_id === hosp.id
                        ? 'bg-[#004795]/5 font-semibold text-[#004795]'
                        : ''
                    "
                    @click="selectHospital(hosp)"
                  >
                    {{ hosp.name }}
                  </div>
                  <div
                    v-if="!filteredHospitals.length"
                    class="px-3 py-2 text-xs text-gray-400 italic"
                  >
                    No hospitals found
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- Doctor -->
          <div class="relative" ref="doctorDropdownRef">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5"
              >Doctor <span class="text-red-500">*</span></label
            >
            <div
              v-if="prefilledDoctorId"
              class="w-full border border-emerald-200 bg-emerald-50 rounded-lg px-3 py-2.5 flex items-center gap-2"
            >
              <span class="text-sm font-semibold text-emerald-800 flex-1"
                >Dr. {{ selectedDoctorObj?.user?.first_name }}
                {{ selectedDoctorObj?.user?.last_name }}</span
              >
              <span
                class="text-[10px] text-emerald-600 font-bold uppercase bg-emerald-100 px-1.5 py-0.5 rounded"
                >Auto-filled</span
              >
            </div>
            <template v-else>
              <div
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white cursor-pointer flex items-center justify-between gap-2"
                :class="doctorsLoading ? 'opacity-60 pointer-events-none' : ''"
                @click="doctorDropdownOpen = !doctorDropdownOpen"
              >
                <span
                  :class="selectedDoctorObj ? 'text-gray-800' : 'text-gray-400'"
                >
                  <template v-if="selectedDoctorObj"
                    >Dr. {{ selectedDoctorObj.user?.first_name }}
                    {{ selectedDoctorObj.user?.last_name }}
                    <span class="text-gray-400"
                      >— {{ selectedDoctorObj.department?.name }}</span
                    ></template
                  >
                  <template v-else>{{
                    doctorsLoading ? "Loading doctors…" : "Choose a doctor"
                  }}</template>
                </span>
                <Loader2
                  v-if="doctorsLoading"
                  class="w-3.5 h-3.5 text-gray-400 animate-spin flex-shrink-0"
                />
                <ChevronDown
                  v-else
                  class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform"
                  :class="doctorDropdownOpen ? 'rotate-180' : ''"
                />
              </div>
              <div
                v-if="doctorDropdownOpen"
                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
              >
                <div class="p-2 border-b border-gray-100">
                  <div
                    class="flex items-center gap-2 bg-gray-50 rounded-md px-2 py-1.5"
                  >
                    <Search
                      class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"
                    /><input
                      v-model="doctorSearch"
                      type="text"
                      placeholder="Search doctor or department..."
                      class="flex-1 text-xs bg-transparent outline-none"
                      @click.stop
                    />
                  </div>
                </div>
                <div class="max-h-52 overflow-y-auto">
                  <div
                    v-for="doc in filteredDoctors"
                    :key="doc.id"
                    class="px-3 py-2.5 text-xs text-gray-700 hover:bg-gray-50 cursor-pointer"
                    :class="
                      bookForm.doctor_id === doc.id
                        ? 'bg-[#004795]/5 font-semibold text-[#004795]'
                        : ''
                    "
                    @click="selectDoctor(doc)"
                  >
                    <span class="font-medium"
                      >Dr. {{ doc.user?.first_name }}
                      {{ doc.user?.last_name }}</span
                    ><span class="text-gray-400 ml-1"
                      >— {{ doc.department?.name }}</span
                    >
                  </div>
                  <div
                    v-if="!filteredDoctors.length && !doctorsLoading"
                    class="px-3 py-2 text-xs text-gray-400 italic"
                  >
                    No doctors found
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- Date -->
          <div v-if="bookForm.doctor_id">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5"
              >Appointment Date <span class="text-red-500">*</span></label
            >
            <input
              v-model="bookForm.appointment_date"
              type="date"
              required
              :min="today"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
              @change="loadAvailableSlots"
            />
          </div>

          <!-- Slots -->
          <div v-if="bookForm.appointment_date && bookForm.doctor_id">
            <label class="block text-xs font-semibold text-gray-700 mb-2"
              >Available Time Slots <span class="text-red-500">*</span></label
            >
            <div
              v-if="slotsLoading"
              class="flex items-center gap-2 text-xs text-gray-500"
            >
              <Loader2 class="w-3.5 h-3.5 animate-spin" /> Loading slots...
            </div>
            <div
              v-else-if="!availableSlots.length"
              class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2.5 flex items-center gap-2"
            >
              <AlertCircle class="w-3.5 h-3.5 text-amber-500" /> No available
              slots on this date. Please pick another day.
            </div>
            <div v-else class="grid grid-cols-3 sm:grid-cols-4 gap-2">
              <button
                v-for="slot in availableSlots"
                :key="slot.id || slot.start_time"
                type="button"
                @click="selectSlot(slot)"
                :class="
                  selectedSlotTime === formatSlotTime(slot.start_time)
                    ? 'bg-[#004795] text-white border-[#004795]'
                    : 'bg-white text-gray-700 border-gray-200 hover:border-[#004795] hover:text-[#004795]'
                "
                class="py-2 text-xs font-semibold rounded-lg border transition"
              >
                {{ formatSlotTime(slot.start_time) }}
              </button>
            </div>
          </div>

          <!-- Visit Type -->
          <div v-if="bookForm.appointment_time">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5"
              >Visit Type <span class="text-red-500">*</span></label
            >
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
              <button
                v-for="vt in visitTypes"
                :key="vt.value"
                type="button"
                @click="bookForm.visit_type = vt.value"
                :class="
                  bookForm.visit_type === vt.value
                    ? 'bg-[#004795] text-white border-[#004795]'
                    : 'bg-white text-gray-700 border-gray-200 hover:border-[#004795] hover:text-[#004795]'
                "
                class="py-2 text-xs font-semibold rounded-lg border transition"
              >
                {{ vt.label }}
              </button>
            </div>
          </div>

          <!-- Reason -->
          <div v-if="bookForm.appointment_time">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5"
              >Reason <span class="text-red-500">*</span></label
            >
            <textarea
              v-model="bookForm.reason"
              rows="3"
              required
              placeholder="Describe your symptoms or reason for visit..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none"
            />
          </div>

          <!-- Telemedicine toggle -->
          <div
            v-if="
              bookForm.appointment_time &&
              selectedDoctorObj?.is_telehealth_available
            "
            class="flex items-center gap-3"
          >
            <button
              type="button"
              @click="bookForm.is_telehealth = !bookForm.is_telehealth"
              :class="bookForm.is_telehealth ? 'bg-[#004795]' : 'bg-gray-200'"
              class="flex items-center w-11 h-6 p-1 rounded-full transition-colors duration-200"
              :style="{
                justifyContent: bookForm.is_telehealth
                  ? 'flex-end'
                  : 'flex-start',
              }"
            >
              <span class="w-4 h-4 bg-white rounded-full shadow" />
            </button>
            <span class="text-sm text-gray-700 font-medium"
              >Telemedicine appointment</span
            >
          </div>

          <!-- ── Optional file upload ──────────────────────────────────── -->
          <div
            v-if="bookForm.appointment_time"
            class="border border-dashed border-gray-200 rounded-xl p-4 bg-gray-50"
          >
            <div class="flex items-center justify-between mb-2">
              <div>
                <p
                  class="text-xs font-semibold text-gray-700 flex items-center gap-1.5"
                >
                  <Paperclip class="w-3.5 h-3.5 text-gray-400" /> Upload Medical
                  Files
                  <span class="text-gray-400 font-normal">(optional)</span>
                </p>
                <p class="text-[10px] text-gray-400 mt-0.5">
                  PDF, JPG, PNG · max 10 MB each · up to 5 files
                </p>
              </div>
              <label
                class="cursor-pointer px-3 py-1.5 text-xs font-semibold text-[#004795] border border-[#004795]/30 rounded-lg hover:bg-[#004795]/5 transition"
              >
                Choose Files
                <input
                  ref="fileInput"
                  type="file"
                  multiple
                  accept=".pdf,.jpg,.jpeg,.png"
                  class="hidden"
                  @change="onFilesSelected"
                />
              </label>
            </div>
            <div v-if="bookFiles.length" class="space-y-1.5 mt-2">
              <div
                v-for="(f, i) in bookFiles"
                :key="i"
                class="flex items-center justify-between bg-white border border-gray-100 rounded-lg px-3 py-1.5"
              >
                <div class="flex items-center gap-2 min-w-0">
                  <component
                    :is="fileIcon(f.name)"
                    class="w-3.5 h-3.5 flex-shrink-0 text-gray-400"
                  />
                  <span class="text-xs text-gray-700 truncate">{{
                    f.name
                  }}</span>
                  <span class="text-[10px] text-gray-400 flex-shrink-0">{{
                    formatFileSize(f.size)
                  }}</span>
                </div>
                <button
                  type="button"
                  @click="removeFile(i)"
                  class="p-1 text-gray-400 hover:text-red-500 transition"
                >
                  <X class="w-3 h-3" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <div
          class="flex items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0"
        >
          <!-- Consultation fee notice -->
          <p
            v-if="selectedDoctorObj?.consultation_fee"
            class="text-xs text-gray-500 flex-1"
          >
            Consultation fee:
            <span class="font-bold text-gray-800"
              >{{ selectedDoctorObj.consultation_fee }} ETB</span
            >
            <span class="text-gray-400">
              — you will be redirected to Chapa to pay</span
            >
          </p>
          <div class="flex items-center gap-3 flex-shrink-0">
            <button
              type="button"
              @click="closeBook"
              class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
            >
              Cancel
            </button>
            <button
              @click="handleBook"
              :disabled="!canBook || bookSaving"
              class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2"
            >
              <Loader2 v-if="bookSaving" class="w-3.5 h-3.5 animate-spin" />
              {{
                selectedDoctorObj?.consultation_fee
                  ? "Confirm & Pay"
                  : "Confirm Booking"
              }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Reschedule Modal ────────────────────────────────────────────── -->
    <div
      v-if="showReschedule"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeReschedule"
    >
      <div
        class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[85vh] flex flex-col"
      >
        <div
          class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0"
        >
          <div>
            <h3 class="text-sm font-bold text-gray-800">
              Reschedule Appointment
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">
              Pick a new date and time slot
            </p>
          </div>
          <button
            @click="closeReschedule"
            class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
        <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
          <div
            v-if="rescheduleError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
          >
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{
              rescheduleError
            }}
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5"
              >New Date <span class="text-red-500">*</span></label
            >
            <input
              v-model="rescheduleDate"
              type="date"
              :min="today"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
              @change="loadRescheduleSlots"
            />
          </div>
          <div v-if="rescheduleDate">
            <label class="block text-xs font-semibold text-gray-700 mb-2"
              >Available Slots <span class="text-red-500">*</span></label
            >
            <div
              v-if="rescheduleSlotsLoading"
              class="flex items-center gap-2 text-xs text-gray-500"
            >
              <Loader2 class="w-3.5 h-3.5 animate-spin" /> Loading slots...
            </div>
            <div
              v-else-if="!rescheduleSlots.length"
              class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2.5 flex items-center gap-2"
            >
              <AlertCircle class="w-3.5 h-3.5 text-amber-500" /> No available
              slots on this date.
            </div>
            <div v-else class="grid grid-cols-3 sm:grid-cols-4 gap-2">
              <button
                v-for="slot in rescheduleSlots"
                :key="slot.id || slot.start_time"
                type="button"
                @click="
                  rescheduleTime = slot.start_time.substring(11, 16);
                  selectedRescheduleSlotTime = formatSlotTime(slot.start_time);
                  selectedRescheduleSlotId = slot.id;
                "
                :class="
                  selectedRescheduleSlotId === slot.id
                    ? 'bg-[#004795] text-white border-[#004795]'
                    : 'bg-white text-gray-700 border-gray-200 hover:border-[#004795] hover:text-[#004795]'
                "
                class="py-2 text-xs font-semibold rounded-lg border transition"
              >
                {{ formatSlotTime(slot.start_time) }}
              </button>
            </div>
          </div>
        </div>
        <div
          class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0"
        >
          <button
            @click="closeReschedule"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
          >
            Cancel
          </button>
          <button
            @click="handleReschedule"
            :disabled="!selectedRescheduleSlotId || rescheduleSaving"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2"
          >
            <Loader2 v-if="rescheduleSaving" class="w-3.5 h-3.5 animate-spin" />
            Confirm Reschedule
          </button>
        </div>
      </div>
    </div>
    <!-- ── Hide-from-history Confirmation Modal ──────────────────────── -->
    <div
      v-if="showHideConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div
            class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0"
          >
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">
              Remove from history?
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
              This appointment will be removed from your list. The doctor and
              hospital will still have access to the record.
            </p>
          </div>
        </div>
        <div class="flex gap-3 mt-5">
          <button
            @click="cancelHide"
            class="flex-1 px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition"
          >
            Cancel
          </button>
          <button
            @click="confirmHide"
            :disabled="hidingId !== null"
            class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-xl transition disabled:opacity-50 flex items-center justify-center gap-2"
          >
            <Loader2
              v-if="hidingId !== null"
              class="w-3.5 h-3.5 animate-spin"
            />
            Remove
          </button>
        </div>
      </div>
    </div>

    <!-- ── Cancel Appointment Confirmation Modal ─────────────────────── -->
    <div
      v-if="showCancelConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div
            class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0"
          >
            <XCircle class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Cancel appointment?</h3>
            <p class="text-xs text-gray-500 mt-0.5">
              This will cancel your appointment. This action cannot be undone.
            </p>
          </div>
        </div>
        <div class="flex gap-3 mt-5">
          <button
            @click="dismissCancelConfirm"
            :disabled="cancelInProgress"
            class="flex-1 px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition"
          >
            Keep
          </button>
          <button
            @click="confirmCancel"
            :disabled="cancelInProgress"
            class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-xl transition disabled:opacity-50 flex items-center justify-center gap-2"
          >
            <Loader2 v-if="cancelInProgress" class="w-3.5 h-3.5 animate-spin" />
            Yes, Cancel
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useRoute } from "vue-router";
import {
  Plus,
  CalendarDays,
  Clock,
  Monitor,
  AlertCircle,
  X,
  Loader2,
  ChevronDown,
  Search,
  Building2,
  Paperclip,
  FileText,
  Image,
  ArrowRightLeft,
  Download,
  Trash2,
  MoreVertical,
  XCircle,
} from "lucide-vue-next";
import { useAppointmentStore } from "../../stores/appointmentStore";
import doctorApi from "../../api/doctorApi";
import slotApi from "../../api/slotApi";
import api from "../../api/axios";
import hospitalApi from "../../api/hospitalApi";
import medicalDocumentApi from "../../api/medicalDocumentApi";
import paymentApi from "../../api/paymentApi";

const baseUrl = api.defaults.baseURL;

const store = useAppointmentStore();
const route = useRoute();
const doctors = ref([]);
const hospitals = ref([]);
const today = new Date().toISOString().split("T")[0];

// Visit type options — Normal (first time), Follow Up, and Urgent
const visitTypes = [
  { value: "normal",    label: "Normal" },
  { value: "follow_up", label: "Follow Up" },
  { value: "urgent",    label: "Urgent" },
];

// Dropdown state
const hospitalSearch = ref("");
const doctorSearch = ref("");
const hospitalDropdownOpen = ref(false);
const doctorDropdownOpen = ref(false);
const hospitalDropdownRef = ref(null);
const doctorDropdownRef = ref(null);
const doctorsLoading = ref(false);

const filteredHospitals = computed(() => {
  if (!hospitalSearch.value.trim()) return hospitals.value;
  const q = hospitalSearch.value.toLowerCase();
  return hospitals.value.filter((h) => h.name?.toLowerCase().includes(q));
});
const filteredDoctors = computed(() => {
  if (!doctorSearch.value.trim()) return doctors.value;
  const q = doctorSearch.value.toLowerCase();
  return doctors.value.filter(
    (d) =>
      `${d.user?.first_name} ${d.user?.last_name}`.toLowerCase().includes(q) ||
      d.department?.name?.toLowerCase().includes(q),
  );
});

function selectHospital(hosp) {
  bookForm.value.hospital_id = hosp?.id ?? "";
  hospitalDropdownOpen.value = false;
  hospitalSearch.value = "";
  bookForm.value.doctor_id = "";
  bookForm.value.appointment_date = "";
  bookForm.value.appointment_time = "";
  selectedSlotTime.value = "";
  availableSlots.value = [];
  loadDoctors(hosp?.id ?? null);
}
function selectDoctor(doc) {
  bookForm.value.doctor_id = doc.id;
  doctorDropdownOpen.value = false;
  doctorSearch.value = "";
  onDoctorChange();
}

function handleOutsideClick(e) {
  if (
    hospitalDropdownRef.value &&
    !hospitalDropdownRef.value.contains(e.target)
  )
    hospitalDropdownOpen.value = false;
  if (doctorDropdownRef.value && !doctorDropdownRef.value.contains(e.target))
    doctorDropdownOpen.value = false;
  // Action menu panels use @click.stop + @mousedown.stop so neither event
  // bubbles here when clicking inside the panel.
  // Any event reaching here is outside — close the open menu.
  if (openActionMenuId.value !== null) {
    openActionMenuId.value = null;
  }
}
onUnmounted(() => {
  document.removeEventListener("click", handleOutsideClick);
});

// Profile check
const profileActive = ref(null);
const profileLoading = ref(false);
async function checkPatientProfile() {
  profileLoading.value = true;
  try {
    const res = await api.get("/patient/profile");
    const p = res.data?.data ?? res.data;
    profileActive.value = p?.patient_status === "active";
  } catch {
    profileActive.value = false;
  } finally {
    profileLoading.value = false;
  }
}
watch(
  () => route.fullPath,
  () => checkPatientProfile(),
);

// Book modal
const showBook = ref(false);
const bookError = ref(null);
const bookSaving = ref(false);
const slotsLoading = ref(false);
const availableSlots = ref([]);
const selectedSlotTime = ref("");
const prefilledHospitalId = ref("");
const prefilledDoctorId = ref("");
const fileInput = ref(null);
const bookFiles = ref([]);

const bookForm = ref({
  hospital_id: "",
  doctor_id: "",
  appointment_date: "",
  appointment_time: "",
  reason: "",
  is_telehealth: false,
  visit_type: "normal",
});
const selectedHospitalObj = computed(
  () =>
    hospitals.value.find((h) => h.id === bookForm.value.hospital_id) ?? null,
);
const selectedDoctorObj = computed(
  () => doctors.value.find((d) => d.id === bookForm.value.doctor_id) ?? null,
);
const canBook = computed(
  () =>
    bookForm.value.doctor_id &&
    bookForm.value.appointment_date &&
    bookForm.value.appointment_time &&
    bookForm.value.reason.trim(),
);

// File helpers
function onFilesSelected(e) {
  const selected = Array.from(e.target.files ?? []);
  const combined = [...bookFiles.value, ...selected].slice(0, 5);
  bookFiles.value = combined;
  if (fileInput.value) fileInput.value.value = "";
}
function removeFile(i) {
  bookFiles.value.splice(i, 1);
}
function formatFileSize(bytes) {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`;
  return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
}
function fileIcon(name) {
  if (/\.pdf$/i.test(name)) return FileText;
  return Image;
}

// ── Authenticated document download ──────────────────────────────────────
const downloadingDoc = ref({});
async function downloadDoc(doc) {
  if (downloadingDoc.value[doc.id]) return;
  downloadingDoc.value[doc.id] = true;
  try {
    await medicalDocumentApi.download(doc.id, doc.file_name);
  } catch (e) {
    alert("Download failed: " + (e.message || "Unknown error"));
  } finally {
    downloadingDoc.value[doc.id] = false;
  }
}

// Reschedule modal
const showReschedule = ref(false);
const rescheduleAppointment = ref(null);
const rescheduleDate = ref("");
const rescheduleTime = ref("");
const rescheduleSlots = ref([]);
const rescheduleSlotsLoading = ref(false);
const rescheduleSaving = ref(false);
const rescheduleError = ref(null);
const selectedRescheduleSlotTime = ref("");
const selectedRescheduleSlotId = ref(null);

function openReschedule(appt) {
  rescheduleAppointment.value = appt;
  rescheduleDate.value = "";
  rescheduleTime.value = "";
  rescheduleSlots.value = [];
  rescheduleError.value = null;
  selectedRescheduleSlotTime.value = "";
  selectedRescheduleSlotId.value = null;
  showReschedule.value = true;
}
function closeReschedule() {
  showReschedule.value = false;
  rescheduleError.value = null;
}

async function loadRescheduleSlots() {
  if (!rescheduleDate.value || !rescheduleAppointment.value) return;
  rescheduleTime.value = "";
  rescheduleSlots.value = [];
  selectedRescheduleSlotTime.value = "";
  selectedRescheduleSlotId.value = null;
  rescheduleSlotsLoading.value = true;
  try {
    rescheduleSlots.value = await slotApi.getAvailable(
      rescheduleAppointment.value.doctor_id,
      rescheduleDate.value,
    );
  } catch {
    rescheduleSlots.value = [];
  } finally {
    rescheduleSlotsLoading.value = false;
  }
}

async function handleReschedule() {
  if (!selectedRescheduleSlotId.value || !rescheduleAppointment.value) return;
  rescheduleError.value = null;
  rescheduleSaving.value = true;
  try {
    await store.reschedule(
      rescheduleAppointment.value.id,
      selectedRescheduleSlotId.value,
    );
    closeReschedule();
  } catch (err) {
    const errors = err.response?.data?.errors;
    rescheduleError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Failed to reschedule.";
  } finally {
    rescheduleSaving.value = false;
  }
}

async function loadDoctors(hospitalId = null) {
  doctorsLoading.value = true;
  try {
    const params = hospitalId ? { hospital_id: hospitalId } : {};
    const res = await doctorApi.getAll(params);
    doctors.value = res.data?.data ?? res.data ?? [];
  } catch {
  } finally {
    doctorsLoading.value = false;
  }
}
async function loadHospitals() {
  try {
    const res = await hospitalApi.getAll();
    hospitals.value = res.data?.data ?? res.data ?? [];
  } catch {}
}

onMounted(async () => {
  await Promise.all([
    store.fetchAll(),
    loadDoctors(),
    loadHospitals(),
    checkPatientProfile(),
  ]);
  document.addEventListener("click", handleOutsideClick);
  const qDoctorId = route.query.doctor_id;
  const qHospitalId = route.query.hospital_id;
  if (qDoctorId && profileActive.value !== false) {
    if (profileActive.value === null) await checkPatientProfile();
    if (profileActive.value) openBookWithDoctor(qDoctorId, qHospitalId ?? "");
  }
});

async function loadAvailableSlots() {
  if (!bookForm.value.doctor_id || !bookForm.value.appointment_date) return;
  selectedSlotTime.value = "";
  bookForm.value.appointment_time = "";
  availableSlots.value = [];
  slotsLoading.value = true;
  try {
    availableSlots.value = await slotApi.getAvailable(
      bookForm.value.doctor_id,
      bookForm.value.appointment_date,
    );
  } catch {
  } finally {
    slotsLoading.value = false;
  }
}
function onDoctorChange() {
  bookForm.value.appointment_date = "";
  bookForm.value.appointment_time = "";
  selectedSlotTime.value = "";
  availableSlots.value = [];
}
function selectSlot(slot) {
  selectedSlotTime.value = formatSlotTime(slot.start_time);
  bookForm.value.appointment_time = slot.start_time.substring(11, 16);
}

async function openBookWithDoctor(doctorId, hospitalId) {
  bookForm.value = {
    hospital_id: hospitalId ?? "",
    doctor_id: doctorId,
    appointment_date: "",
    appointment_time: "",
    reason: "",
    is_telehealth: false,
    visit_type: "follow_up",
  };
  prefilledHospitalId.value = hospitalId ?? "";
  prefilledDoctorId.value = doctorId;
  selectedSlotTime.value = "";
  availableSlots.value = [];
  bookError.value = null;
  bookFiles.value = [];
  hospitalSearch.value = "";
  doctorSearch.value = "";
  hospitalDropdownOpen.value = false;
  doctorDropdownOpen.value = false;
  showBook.value = true;
}
function openBook() {
  if (!profileActive.value) return;
  bookForm.value = {
    hospital_id: "",
    doctor_id: "",
    appointment_date: "",
    appointment_time: "",
    reason: "",
    is_telehealth: false,
    visit_type: "follow_up",
  };
  selectedSlotTime.value = "";
  availableSlots.value = [];
  bookError.value = null;
  bookFiles.value = [];
  hospitalSearch.value = "";
  doctorSearch.value = "";
  hospitalDropdownOpen.value = false;
  doctorDropdownOpen.value = false;
  prefilledHospitalId.value = "";
  prefilledDoctorId.value = "";
  showBook.value = true;
}
function closeBook() {
  showBook.value = false;
  bookError.value = null;
  prefilledHospitalId.value = "";
  prefilledDoctorId.value = "";
  bookFiles.value = [];
}

async function handleBook() {
  bookError.value = null;
  try {
    bookSaving.value = true;
    const result = await store.create({
      doctor_id: bookForm.value.doctor_id,
      appointment_date: bookForm.value.appointment_date,
      appointment_time: bookForm.value.appointment_time,
      reason: bookForm.value.reason,
      is_telehealth: bookForm.value.is_telehealth,
      visit_type: bookForm.value.visit_type || "normal",
      files: bookFiles.value,
    });
    closeBook();
    // Redirect to Chapa payment page
    if (result?.checkoutUrl) {
      window.location.href = result.checkoutUrl;
    }
  } catch (err) {
    const errors = err.response?.data?.errors;
    bookError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";

    // If the slot was taken, refresh available slots so the UI reflects
    // the current availability and the patient can pick another time
    if (
      err.response?.status === 422 &&
      (errors?.appointment_time || bookError.value?.toLowerCase().includes("slot"))
    ) {
      bookForm.value.appointment_time = "";
      selectedSlotTime.value = "";
      await loadAvailableSlots();
    }
  } finally {
    bookSaving.value = false;
  }
}

async function handleCancel(id) {
  try {
    await store.updateStatus(id, "cancelled");
  } catch {}
}

// ── Hide from history (in-page confirmation modal) ────────────────────────
const showHideConfirm = ref(false);
const hidingId = ref(null);
const pendingHideId = ref(null);

function promptHideHistory(appt) {
  pendingHideId.value = appt.id;
  showHideConfirm.value = true;
}
function cancelHide() {
  showHideConfirm.value = false;
  pendingHideId.value = null;
}
async function confirmHide() {
  if (!pendingHideId.value) return;
  hidingId.value = pendingHideId.value;
  try {
    await store.hideFromHistory(pendingHideId.value);
    showHideConfirm.value = false;
    pendingHideId.value = null;
  } catch (err) {
    // Show error inline — no alert
    store.error =
      err.response?.data?.message || "Failed to remove appointment.";
    showHideConfirm.value = false;
  } finally {
    hidingId.value = null;
  }
}

// ── 3-dot action menu ────────────────────────────────────────────────────
const openActionMenuId = ref(null);

function toggleActionMenu(id) {
  openActionMenuId.value = openActionMenuId.value === id ? null : id;
}
function closeActionMenu() {
  openActionMenuId.value = null;
}

// Wrapper called from the dropdown — closes menu then acts
function doReschedule(appt) {
  openActionMenuId.value = null;
  openReschedule(appt);
}

// Cancel confirmation modal state
const showCancelConfirm = ref(false);
const cancelTargetId = ref(null);
const cancelInProgress = ref(false);

function doCancel(id) {
  openActionMenuId.value = null;
  cancelTargetId.value = id;
  showCancelConfirm.value = true;
}
function dismissCancelConfirm() {
  showCancelConfirm.value = false;
  cancelTargetId.value = null;
}
async function confirmCancel() {
  if (!cancelTargetId.value) return;
  cancelInProgress.value = true;
  try {
    await store.updateStatus(cancelTargetId.value, "cancelled");
    showCancelConfirm.value = false;
    cancelTargetId.value = null;
  } catch (err) {
    store.error = err.response?.data?.message || "Failed to cancel appointment.";
    showCancelConfirm.value = false;
  } finally {
    cancelInProgress.value = false;
  }
}

// Retry payment for pending_payment appointments
async function retryPayment(appt) {
  try {
    // Step 1: find the pending payment record for this appointment
    const res = await paymentApi.getByAppointment(appt.id);
    const payment = res.data?.data ?? res.data;

    if (!payment?.id) {
      alert("No pending payment found. Please contact support.");
      return;
    }

    // Step 2: re-initialize with Chapa to get a fresh checkout URL
    const reinitRes = await paymentApi.reinitialize(payment.id);
    const checkoutUrl = reinitRes.data?.checkout_url;

    if (checkoutUrl) {
      window.location.href = checkoutUrl;
    } else {
      alert("Payment link not available. Please contact support.");
    }
  } catch (err) {
    const msg =
      err.response?.data?.message ||
      "Unable to retrieve payment link. Please try again.";
    alert(msg);
  }
}

// Download invoice for a completed appointment
const downloadingInvoice = ref({});
async function downloadInvoice(appt) {
  if (downloadingInvoice.value[appt.id]) return;
  downloadingInvoice.value[appt.id] = true;
  try {
    // Fetch invoices filtered by appointment_id
    const res = await api.get(`/invoices`, {
      params: { appointment_id: appt.id },
    });
    const list = res.data?.data ?? res.data ?? [];
    const invoices = Array.isArray(list) ? list : (list.data ?? []);
    const invoice = invoices.find(
      (inv) =>
        inv.appointment_id === appt.id ||
        inv.payment?.appointment_id === appt.id,
    );

    if (!invoice?.id) {
      alert("Invoice not available yet. Payment may still be processing.");
      return;
    }

    const blob = await paymentApi.downloadInvoice(invoice.id);
    const url = window.URL.createObjectURL(
      new Blob([blob.data], { type: "application/pdf" }),
    );
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute(
      "download",
      `invoice-${invoice.invoice_number ?? invoice.id}.pdf`,
    );
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch {
    alert("Invoice download failed. Please try again.");
  } finally {
    downloadingInvoice.value[appt.id] = false;
  }
}

// Helpers
function initials(doctor) {
  const u = doctor?.user;
  return u
    ? ((u.first_name?.[0] ?? "") + (u.last_name?.[0] ?? "")).toUpperCase()
    : "?";
}
function formatDate(dt) {
  return dt
    ? new Date(dt).toLocaleDateString("en-ET", {
        day: "numeric",
        month: "short",
        year: "numeric",
      })
    : "—";
}
function formatTime(dt) {
  return dt
    ? new Date(dt).toLocaleTimeString("en-ET", {
        hour: "2-digit",
        minute: "2-digit",
      })
    : "—";
}
function formatSlotTime(t) {
  if (!t) return "";
  const d = new Date(t);
  return isNaN(d)
    ? t.substring(11, 16)
    : d.toLocaleTimeString("en-ET", { hour: "2-digit", minute: "2-digit" });
}
function statusClass(status) {
  return (
    {
      pending: "bg-amber-50 text-amber-700 border-amber-200",
      pending_payment: "bg-orange-50 text-orange-700 border-orange-200",
      confirmed: "bg-blue-50 text-blue-700 border-blue-200",
      completed: "bg-emerald-50 text-emerald-700 border-emerald-200",
      cancelled: "bg-red-50 text-red-600 border-red-200",
      no_show: "bg-gray-50 text-gray-500 border-gray-200",
    }[status] ?? "bg-gray-50 text-gray-500 border-gray-200"
  );
}
function referralBadgeClass(status) {
  return (
    {
      pending: "bg-amber-50 text-amber-700 border-amber-200",
      accepted: "bg-emerald-50 text-emerald-700 border-emerald-200",
      rejected: "bg-red-50 text-red-600 border-red-200",
    }[status] ?? "bg-gray-50 text-gray-500 border-gray-200"
  );
}
</script>

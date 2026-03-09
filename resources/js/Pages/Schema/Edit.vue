<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'

// ─── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  name: { type: String, required: true },
})

// ─── State ────────────────────────────────────────────────────────────────────
const loading   = ref(true)
const saving    = ref(false)
const saved     = ref(false)
const saveError = ref(null)
const sections  = ref([])

// ─── Constants ────────────────────────────────────────────────────────────────
const TYPES = ['text', 'textarea', 'number', 'checkbox', 'select', 'file', 'repeater']

const TYPE_LABELS = {
  text: 'Text',
  textarea: 'Textarea',
  number: 'Number',
  checkbox: 'Checkbox',
  select: 'Select',
  file: 'File',
  repeater: 'Repeater',
}

// ─── Base-field detection ─────────────────────────────────────────────────────
// Fields loaded from the DB whose key is NOT under attributes.* are stamped with
// _locked=true at load time. New fields added by the user never get this flag,
// so typing in their key is always allowed regardless of what they type.
const isBaseField = (field) => !!field._locked

// ─── Load ─────────────────────────────────────────────────────────────────────
const loadSchema = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/api/schemas/${props.name}`)
    // Deep-clone, then stamp base fields (non attributes.*) as locked
    const raw = JSON.parse(JSON.stringify(data.data ?? []))
    for (const section of raw) {
      for (const field of section.fields ?? []) {
        if (field.key && !field.key.startsWith('attributes.')) {
          field._locked = true
        }
      }
    }
    sections.value = raw
  } finally {
    loading.value = false
  }
}

onMounted(loadSchema)

// ─── Save ─────────────────────────────────────────────────────────────────────
const save = async () => {
  saving.value    = true
  saved.value     = false
  saveError.value = null
  try {
    // Strip the client-only _locked flag before sending to the API
    const cleanSections = sections.value.map(section => ({
      ...section,
      fields: (section.fields ?? []).map(({ _locked, ...field }) => field),
    }))

    await axios.put(`/api/schemas/${props.name}`, {
      name: props.name,
      sections: cleanSections,
    })
    saved.value = true
    setTimeout(() => (saved.value = false), 3000)
  } catch (err) {
    saveError.value = err.response?.data?.message ?? 'Could not save. Please try again.'
  } finally {
    saving.value = false
  }
}

// ─── Section helpers ──────────────────────────────────────────────────────────
const addSection = () => {
  sections.value.push({ title: 'New section', fields: [] })
}

const removeSection = (si) => {
  if (!window.confirm('Delete this section and all its fields?')) return
  sections.value.splice(si, 1)
}

// ─── Field helpers ────────────────────────────────────────────────────────────
const addField = (si) => {
  sections.value[si].fields.push({ key: 'attributes.', label: '', type: 'text', required: false })
}

const removeField = (si, fi) => {
  sections.value[si].fields.splice(fi, 1)
}

const moveField = (si, fi, dir) => {
  const fields = sections.value[si].fields
  const target = fi + dir
  if (target < 0 || target >= fields.length) return
  const [item] = fields.splice(fi, 1)
  fields.splice(target, 0, item)
}

// ─── Options helpers (select) ─────────────────────────────────────────────────
const addOption = (field) => {
  if (!Array.isArray(field.options)) field.options = []
  field.options.push({ value: '', label: '' })
}

const removeOption = (field, oi) => field.options.splice(oi, 1)

// ─── Subfield helpers (repeater) ──────────────────────────────────────────────
const addSubfield = (field) => {
  if (!Array.isArray(field.subfields)) field.subfields = []
  field.subfields.push({ key: '', label: '', type: 'text' })
}

const removeSubfield = (field, si) => field.subfields.splice(si, 1)

// ─── Stats ────────────────────────────────────────────────────────────────────
const totalFields = computed(() =>
  sections.value.reduce((acc, s) => acc + (s.fields?.length ?? 0), 0)
)
</script>

<template>
  <Head :title="`Edit Schema · ${name}`" />

  <AuthenticatedLayout>
    <!-- ── Header ── -->
    <template #header>
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-2">
          <Link :href="route('web.schema.index')" class="shrink-0 text-sm text-indigo-600 hover:text-indigo-500">
            ← Schemas
          </Link>
          <span class="text-gray-300">/</span>
          <h2 class="truncate text-xl font-semibold capitalize text-gray-800">{{ name }}</h2>
          <span class="shrink-0 rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
            {{ totalFields }} field{{ totalFields !== 1 ? 's' : '' }}
          </span>
        </div>

        <div class="flex shrink-0 items-center gap-3">
          <Transition
            enter-active-class="transition duration-200"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <span v-if="saved" class="text-sm font-medium text-green-600">✓ Saved!</span>
          </Transition>

          <button
            type="button"
            :disabled="saving"
            class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:bg-indigo-300"
            @click="save"
          >
            {{ saving ? 'Saving…' : 'Save changes' }}
          </button>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

        <!-- Error banner -->
        <div v-if="saveError" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          {{ saveError }}
        </div>

        <!-- Loading skeleton -->
        <div v-if="loading" class="space-y-4">
          <div v-for="i in 3" :key="i" class="h-32 animate-pulse rounded-xl bg-gray-100" />
        </div>

        <template v-else>

          <!-- ── Sections ── -->
          <div
            v-for="(section, si) in sections"
            :key="si"
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
          >
            <!-- Section header -->
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-violet-50 px-4 py-3 sm:px-6">
              <div class="flex min-w-0 flex-1 items-center gap-2">
                <span class="shrink-0 text-lg select-none">🗂️</span>
                <input
                  v-model="section.title"
                  class="min-w-0 flex-1 cursor-text rounded-lg border border-transparent bg-transparent px-2 py-1 text-sm font-semibold text-indigo-800 placeholder-indigo-300 transition hover:border-indigo-300 hover:bg-white/70 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300"
                  placeholder="Section title (click to edit)"
                />
              </div>
              <button
                type="button"
                class="shrink-0 rounded-md px-2 py-1 text-xs text-red-400 transition hover:bg-red-50 hover:text-red-700"
                @click="removeSection(si)"
              >
                Delete section
              </button>
            </div>

            <!-- Field cards -->
            <div class="divide-y divide-gray-100">
              <div
                v-for="(field, fi) in section.fields"
                :key="fi"
                class="px-4 py-4 sm:px-6"
              >
                <!-- ── Field card: stacked rows ── -->
                <div class="space-y-3">

                  <!-- Row 1: Label (full width) -->
                  <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-400">
                      Label <span v-if="field.required" class="ml-0.5 text-red-400">*</span>
                    </label>
                    <input
                      v-model="field.label"
                      type="text"
                      placeholder="e.g. Glass type"
                      class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>

                  <!-- Row 2: Key + Type side by side -->
                  <div class="flex flex-wrap gap-3 sm:flex-nowrap">
                    <!-- Key -->
                    <div class="min-w-0 flex-1">
                      <label class="mb-1 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Field key
                        <span
                          v-if="isBaseField(field)"
                          class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-1.5 py-0.5 text-xs font-medium normal-case tracking-normal text-amber-700"
                          title="Base model field — key is fixed by the backend."
                        >🔒 model field</span>
                      </label>
                      <!-- Locked key for base fields -->
                      <div
                        v-if="isBaseField(field)"
                        class="flex items-center rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 font-mono text-xs text-gray-500"
                      >
                        {{ field.key }}
                      </div>
                      <!-- Editable key for attributes.* fields -->
                      <input
                        v-else
                        v-model="field.key"
                        type="text"
                        placeholder="e.g. attributes.abv"
                        class="w-full rounded-lg border-gray-300 font-mono text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      />
                    </div>

                    <!-- Type -->
                    <div class="w-full sm:w-36 shrink-0">
                      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-400">Type</label>
                      <select
                        v-model="field.type"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      >
                        <option v-for="t in TYPES" :key="t" :value="t">{{ TYPE_LABELS[t] }}</option>
                      </select>
                    </div>
                  </div>

                  <!-- Row 3: Placeholder + Required + Action buttons -->
                  <div class="flex flex-wrap items-center gap-x-3 gap-y-2">

                    <!-- Placeholder (for text-like types) -->
                    <input
                      v-if="['text', 'textarea', 'number'].includes(field.type)"
                      v-model="field.placeholder"
                      type="text"
                      placeholder="Placeholder hint (optional)"
                      class="min-w-0 flex-1 rounded-lg border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <div v-else class="flex-1" />

                    <!-- Required checkbox -->
                    <label class="inline-flex shrink-0 cursor-pointer items-center gap-1.5 text-xs text-gray-600 select-none">
                      <input
                        v-model="field.required"
                        type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-400"
                      />
                      Required
                    </label>

                    <!-- Separator -->
                    <span class="h-4 w-px shrink-0 bg-gray-200" />

                    <!-- Move up -->
                    <button
                      type="button"
                      :disabled="fi === 0"
                      title="Move up"
                      class="rounded p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 disabled:opacity-30"
                      @click="moveField(si, fi, -1)"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                      </svg>
                    </button>

                    <!-- Move down -->
                    <button
                      type="button"
                      :disabled="fi === section.fields.length - 1"
                      title="Move down"
                      class="rounded p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 disabled:opacity-30"
                      @click="moveField(si, fi, 1)"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>

                    <!-- Delete -->
                    <button
                      type="button"
                      :disabled="isBaseField(field)"
                      :title="isBaseField(field) ? 'Base model fields cannot be deleted' : 'Delete field'"
                      class="rounded p-1 transition"
                      :class="isBaseField(field) ? 'cursor-not-allowed text-gray-200' : 'text-red-400 hover:bg-red-50 hover:text-red-600'"
                      @click="!isBaseField(field) && removeField(si, fi)"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>

                  <!-- ── Select options panel — always visible when type = select ── -->
                  <div
                    v-if="field.type === 'select'"
                    class="rounded-xl border border-indigo-200 bg-indigo-50 p-4"
                  >
                    <div class="mb-3 flex items-center justify-between gap-2">
                      <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">
                        Select options
                      </p>
                      <!-- Empty state hint -->
                      <span
                        v-if="!field.options?.length"
                        class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700"
                      >
                        ⚠️ Add at least one option
                      </span>
                    </div>

                    <!-- Header row for columns -->
                    <div v-if="field.options?.length" class="mb-1.5 flex gap-2 px-1 text-xs font-semibold uppercase tracking-wide text-indigo-400">
                      <span class="w-36 shrink-0">Value (code)</span>
                      <span class="flex-1">Label (displayed)</span>
                      <span class="w-6" />
                    </div>

                    <div class="space-y-2">
                      <div
                        v-for="(opt, oi) in (field.options ?? [])"
                        :key="oi"
                        class="flex items-center gap-2"
                      >
                        <input
                          v-model="opt.value"
                          type="text"
                          placeholder="e.g. gin"
                          class="w-36 shrink-0 rounded-lg border-gray-300 font-mono text-xs shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                        />
                        <input
                          v-model="opt.label"
                          type="text"
                          placeholder="e.g. Gin"
                          class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                        />
                        <button
                          type="button"
                          class="shrink-0 rounded p-1 text-red-400 transition hover:bg-red-50 hover:text-red-600"
                          title="Remove option"
                          @click="removeOption(field, oi)"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                    </div>

                    <button
                      type="button"
                      class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-dashed border-indigo-300 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100"
                      @click="addOption(field)"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                      </svg>
                      Add option
                    </button>
                  </div>

                  <!-- ── Repeater subfields panel — always visible when type = repeater ── -->
                  <div
                    v-if="field.type === 'repeater'"
                    class="rounded-xl border border-violet-200 bg-violet-50 p-4"
                  >
                    <div class="mb-3 flex items-center justify-between gap-2">
                      <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">
                        Repeater sub-fields
                        <span class="ml-1 font-normal normal-case tracking-normal text-violet-400">(columns per row)</span>
                      </p>
                      <span
                        v-if="!field.subfields?.length"
                        class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700"
                      >
                        ⚠️ Add at least one sub-field
                      </span>
                    </div>

                    <!-- Header row -->
                    <div v-if="field.subfields?.length" class="mb-1.5 flex gap-2 px-1 text-xs font-semibold uppercase tracking-wide text-violet-400">
                      <span class="w-28 shrink-0">Key</span>
                      <span class="flex-1">Label</span>
                      <span class="w-20 shrink-0">Type</span>
                      <span class="w-6" />
                    </div>

                    <div class="space-y-2">
                      <div
                        v-for="(sub, subI) in (field.subfields ?? [])"
                        :key="subI"
                        class="flex items-center gap-2"
                      >
                        <input
                          v-model="sub.key"
                          type="text"
                          placeholder="key"
                          class="w-28 shrink-0 rounded-lg border-gray-300 font-mono text-xs shadow-sm focus:border-violet-400 focus:ring-violet-400"
                        />
                        <input
                          v-model="sub.label"
                          type="text"
                          placeholder="Label"
                          class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-violet-400 focus:ring-violet-400"
                        />
                        <select
                          v-model="sub.type"
                          class="w-20 shrink-0 rounded-lg border-gray-300 text-xs shadow-sm focus:border-violet-400 focus:ring-violet-400"
                        >
                          <option value="text">text</option>
                          <option value="number">number</option>
                        </select>
                        <button
                          type="button"
                          class="shrink-0 rounded p-1 text-red-400 transition hover:bg-red-50 hover:text-red-600"
                          title="Remove sub-field"
                          @click="removeSubfield(field, subI)"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                    </div>

                    <button
                      type="button"
                      class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-dashed border-violet-300 px-3 py-1.5 text-xs font-semibold text-violet-600 transition hover:bg-violet-100"
                      @click="addSubfield(field)"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                      </svg>
                      Add sub-field
                    </button>
                  </div>

                </div>
              </div>

              <!-- Add field row -->
              <div class="px-4 py-3 sm:px-6">
                <button
                  type="button"
                  class="inline-flex items-center gap-2 rounded-lg border border-dashed border-gray-300 px-4 py-2 text-sm text-gray-500 transition hover:border-indigo-400 hover:bg-indigo-50 hover:text-indigo-700"
                  @click="addField(si)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                  Add field
                </button>
              </div>
            </div>
          </div>

          <!-- Add section button -->
          <button
            type="button"
            class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-200 py-5 text-sm font-semibold text-gray-400 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600"
            @click="addSection"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Add section
          </button>

          <!-- Bottom save -->
          <div class="flex items-center justify-end gap-3">
            <Transition
              enter-active-class="transition duration-200"
              enter-from-class="opacity-0 translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-150"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <span v-if="saved" class="text-sm font-medium text-green-600">✓ Saved!</span>
            </Transition>

            <button
              type="button"
              :disabled="saving"
              class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:bg-indigo-300"
              @click="save"
            >
              {{ saving ? 'Saving…' : 'Save changes' }}
            </button>
          </div>

        </template>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

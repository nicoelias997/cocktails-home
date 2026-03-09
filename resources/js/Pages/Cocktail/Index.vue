<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DynamicTable from '@/Components/Dynamic/DynamicTable.vue'
import Modal from '@/Components/Modal.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import { computed, onMounted, reactive, ref } from 'vue'

const typeColors = {
  gin:     { bg: 'bg-sky-100',    text: 'text-sky-700'    },
  vodka:   { bg: 'bg-gray-100',   text: 'text-gray-600'   },
  rum:     { bg: 'bg-amber-100',  text: 'text-amber-700'  },
  whiskey: { bg: 'bg-orange-100', text: 'text-orange-700' },
  tequila: { bg: 'bg-green-100',  text: 'text-green-700'  },
  other:   { bg: 'bg-violet-100', text: 'text-violet-700' },
}

const loading = ref(false)
const items   = ref([])
const schemaSections = ref([])
const viewingCocktail = ref(null)

const typeColor = computed(() => typeColors[viewingCocktail.value?.alcohol_type] ?? { bg: 'bg-gray-100', text: 'text-gray-600' })

// Flatten all attributes.* fields from the schema, preserving order
const schemaFields = computed(() =>
  schemaSections.value.flatMap(s => s.fields ?? []).filter(f => f.key?.startsWith('attributes.'))
)

const meta = ref({ current_page: 1, last_page: 1, total: 0 })

const filters = reactive({
  alcohol_type: '',
  sort: 'created_desc',
  per_page: 12,
  page: 1,
})

const columns = [
  { key: 'name', label: 'Name' },
  { key: 'alcohol_type', label: 'Alcohol type' },
]

const fetchCocktails = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/cocktails', { params: filters })
    items.value = data.data ?? []
    meta.value = data.meta ?? meta.value
  } finally {
    loading.value = false
  }
}

const fetchSchema = async () => {
  try {
    const { data } = await axios.get('/api/schemas/cocktails')
    schemaSections.value = data.data ?? []
  } catch { /* schema optional — graceful degradation */ }
}

const applyFilter = () => {
  filters.page = 1
  fetchCocktails()
}

const removeCocktail = async (cocktail) => {
  if (!window.confirm(`Delete "${cocktail.name}"?`)) return
  await axios.delete(`/api/cocktails/${cocktail.id}`)
  await fetchCocktails()
}

const goToPage = async (page) => {
  filters.page = page
  await fetchCocktails()
}

// Helper: get attribute value by bare key (strip 'attributes.' prefix)
const attrValue = (field) => {
  const key = field.key.slice('attributes.'.length)
  return viewingCocktail.value?.attributes?.[key]
}

onMounted(() => Promise.all([fetchCocktails(), fetchSchema()]))
</script>

<template>
  <Head title="Cocktails" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Cocktails</h2>
        <Link :href="route('web.cocktail.create')" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
          New cocktail
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
        <div class="grid gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-3">
          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Alcohol type</label>
            <select v-model="filters.alcohol_type" class="w-full rounded-md border-gray-300 text-sm" @change="applyFilter">
              <option value="">All</option>
              <option value="gin">Gin</option>
              <option value="vodka">Vodka</option>
              <option value="rum">Rum</option>
              <option value="whiskey">Whiskey</option>
              <option value="tequila">Tequila</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Order</label>
            <select v-model="filters.sort" class="w-full rounded-md border-gray-300 text-sm" @change="applyFilter">
              <option value="created_desc">Newest first</option>
              <option value="created_asc">Oldest first</option>
            </select>
          </div>
          <div class="flex items-end">
            <button class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" @click="fetchCocktails">
              Refresh
            </button>
          </div>
        </div>

        <DynamicTable
          :columns="columns"
          :items="items"
          :loading="loading"
          @show="(item) => viewingCocktail = item"
          @edit="(item) => router.visit(route('web.cocktail.edit', item.id))"
          @delete="removeCocktail"
        />

        <div class="flex items-center justify-between text-sm text-gray-600">
          <p>Total: {{ meta.total }}</p>
          <div class="flex items-center gap-2">
            <button
              class="rounded border border-gray-300 px-2 py-1 disabled:cursor-not-allowed disabled:opacity-40"
              :disabled="meta.current_page <= 1"
              @click="goToPage(meta.current_page - 1)"
            >
              Prev
            </button>
            <span>Page {{ meta.current_page }} / {{ meta.last_page }}</span>
            <button
              class="rounded border border-gray-300 px-2 py-1 disabled:cursor-not-allowed disabled:opacity-40"
              :disabled="meta.current_page >= meta.last_page"
              @click="goToPage(meta.current_page + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
    <Modal :show="!!viewingCocktail" max-width="lg" @close="viewingCocktail = null">
      <div v-if="viewingCocktail">

        <!-- Photo hero / placeholder -->
        <div class="relative h-44 w-full overflow-hidden rounded-t-lg bg-gradient-to-br from-indigo-100 to-violet-100">
          <img
            v-if="viewingCocktail.photo_path"
            :src="viewingCocktail.photo_path"
            :alt="viewingCocktail.name"
            class="h-full w-full object-cover"
          >
          <div v-else class="flex h-full items-center justify-center text-7xl select-none opacity-40">
            🍹
          </div>
          <!-- Close button overlay -->
          <button
            class="absolute right-3 top-3 flex size-8 items-center justify-center rounded-full bg-white/80 text-gray-600 shadow backdrop-blur-sm transition hover:bg-white hover:text-gray-900"
            @click="viewingCocktail = null"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="space-y-5 p-6">

          <!-- Name + spirit badge -->
          <div class="flex items-start justify-between gap-3">
            <h3 class="text-xl font-bold leading-tight text-gray-900">{{ viewingCocktail.name }}</h3>
            <span
              class="mt-0.5 shrink-0 rounded-full px-3 py-1 text-xs font-semibold capitalize"
              :class="[typeColor.bg, typeColor.text]"
            >
              {{ viewingCocktail.alcohol_type }}
            </span>
          </div>

          <!-- Dynamic attributes — driven by schema -->
          <template v-for="field in schemaFields" :key="field.key">
            <template v-if="attrValue(field) !== undefined && attrValue(field) !== '' && attrValue(field) !== null">

              <!-- Repeater → table -->
              <div v-if="field.type === 'repeater' && Array.isArray(attrValue(field)) && attrValue(field).length" class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ field.label }}</p>
                <ul class="overflow-hidden rounded-xl border border-gray-200">
                  <li
                    v-for="(row, i) in attrValue(field)"
                    :key="i"
                    class="flex items-center justify-between bg-white px-4 py-2.5 text-sm"
                    :class="i > 0 ? 'border-t border-gray-100' : ''"
                  >
                    <template v-for="(val, colKey, colIdx) in row" :key="colKey">
                      <span v-if="colIdx === 0" class="font-medium text-gray-800">{{ val }}</span>
                      <span v-else class="rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ val }}</span>
                    </template>
                  </li>
                </ul>
              </div>

              <!-- Textarea / long text → text block -->
              <div v-else-if="field.type === 'textarea'" class="space-y-1.5">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ field.label }}</p>
                <p class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm leading-relaxed text-gray-700">
                  {{ attrValue(field) }}
                </p>
              </div>

              <!-- Checkbox → Yes/No badge -->
              <div v-else-if="field.type === 'checkbox'" class="flex items-center gap-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ field.label }}</p>
                <span
                  class="rounded-full px-2.5 py-0.5 text-xs font-semibold"
                  :class="attrValue(field) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                >
                  {{ attrValue(field) ? 'Yes' : 'No' }}
                </span>
              </div>

              <!-- Scalar (text, number, select) → pill -->
              <div v-else class="flex items-center gap-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ field.label }}</p>
                <span class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-1 text-sm text-gray-700">
                  {{ attrValue(field) }}
                </span>
              </div>

            </template>
          </template>

          <!-- Fallback while schema loads -->
          <p v-if="!schemaFields.length" class="italic text-sm text-gray-400">Loading details…</p>

        </div>
      </div>

    </Modal>
  </AuthenticatedLayout>
</template>

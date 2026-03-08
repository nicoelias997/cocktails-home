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

const difficultyConfig = {
  easy:   { label: 'Easy',   cls: 'bg-green-100 text-green-700'   },
  medium: { label: 'Medium', cls: 'bg-amber-100 text-amber-700'   },
  hard:   { label: 'Hard',   cls: 'bg-red-100   text-red-700'     },
}

const loading = ref(false)
const items = ref([])
const viewingCocktail = ref(null)

const typeColor = computed(() => typeColors[viewingCocktail.value?.alcohol_type] ?? { bg: 'bg-gray-100', text: 'text-gray-600' })
const difficultyBadge = computed(() => difficultyConfig[viewingCocktail.value?.attributes?.difficulty] ?? null)
const meta = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
})

const filters = reactive({
  alcohol_type: '',
  sort: 'created_at',
  per_page: 12,
  page: 1,
})

const columns = [
  { key: 'name', label: 'Name' },
  { key: 'alcohol_type', label: 'Alcohol type' }
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

const applyFilter = () => {
  filters.page = 1
  fetchCocktails()
}

const removeCocktail = async (cocktail) => {
  if (!window.confirm(`Delete "${cocktail.name}"?`)) {
    return
  }

  await axios.delete(`/api/cocktails/${cocktail.id}`)
  await fetchCocktails()
}

const goToPage = async (page) => {
  filters.page = page
  await fetchCocktails()
}

onMounted(fetchCocktails)
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
            <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ viewingCocktail.name }}</h3>
            <span
              class="mt-0.5 shrink-0 rounded-full px-3 py-1 text-xs font-semibold capitalize"
              :class="[typeColor.bg, typeColor.text]"
            >
              {{ viewingCocktail.alcohol_type }}
            </span>
          </div>

          <!-- Quick-info pills -->
          <div v-if="viewingCocktail.attributes" class="flex flex-wrap gap-2 text-sm">
            <span
              v-if="viewingCocktail.attributes.glass"
              class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-gray-700"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15M14.25 3.104c.251.023.501.05.75.082M19.8 15a2.25 2.25 0 01-.659 1.591l-1.591 1.591a2.25 2.25 0 01-3.182 0l-1.591-1.591A2.25 2.25 0 0112 15.75h0a2.25 2.25 0 00-1.591.659l-1.591 1.591a2.25 2.25 0 01-3.182 0L4.045 16.409A2.25 2.25 0 013.386 14.818" />
              </svg>
              {{ viewingCocktail.attributes.glass }}
            </span>

            <span
              v-if="viewingCocktail.attributes.garnish"
              class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-gray-700"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707" />
              </svg>
              {{ viewingCocktail.attributes.garnish }}
            </span>

            <span
              v-if="difficultyBadge"
              class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold capitalize"
              :class="difficultyBadge.cls"
            >
              {{ difficultyBadge.label }}
            </span>
          </div>

          <!-- Ingredients -->
          <div v-if="viewingCocktail.attributes?.ingredients?.length" class="space-y-2">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Ingredients</p>
            <ul class="divide-y divide-gray-100 rounded-xl border border-gray-200 overflow-hidden">
              <li
                v-for="(ing, i) in viewingCocktail.attributes.ingredients"
                :key="i"
                class="flex items-center justify-between px-4 py-2.5 text-sm bg-white"
              >
                <span class="font-medium text-gray-800">{{ ing.name }}</span>
                <span class="rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ ing.amount }}</span>
              </li>
            </ul>
          </div>

          <!-- Instructions -->
          <div v-if="viewingCocktail.attributes?.instructions" class="space-y-1.5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Instructions</p>
            <p class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm leading-relaxed text-gray-700">
              {{ viewingCocktail.attributes.instructions }}
            </p>
          </div>

          <!-- Tags -->
          <div v-if="viewingCocktail.attributes?.tags?.length" class="flex flex-wrap items-center gap-2">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Tags</p>
            <span
              v-for="tag in viewingCocktail.attributes.tags"
              :key="tag"
              class="rounded-full bg-indigo-50 px-3 py-0.5 text-xs font-medium text-indigo-700"
            >
              {{ tag }}
            </span>
          </div>

        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

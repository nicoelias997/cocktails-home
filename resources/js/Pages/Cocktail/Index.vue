<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DynamicTable from '@/Components/Dynamic/DynamicTable.vue'
import Modal from '@/Components/Modal.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, reactive, ref } from 'vue'

const loading = ref(false)
const items = ref([])
const viewingCocktail = ref(null)
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
      <div class="p-6 space-y-5">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">{{ viewingCocktail?.name }}</h3>
          <button class="text-gray-400 hover:text-gray-600" @click="viewingCocktail = null">✕</button>
        </div>

        <img
          v-if="viewingCocktail?.photo_path"
          :src="viewingCocktail.photo_path"
          :alt="viewingCocktail.name"
          class="w-full h-48 rounded-md object-cover"
        >

        <dl class="grid grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="font-medium text-gray-500">Name</dt>
            <dd class="mt-0.5 text-gray-800">{{ viewingCocktail?.name }}</dd>
          </div>
          <div>
            <dt class="font-medium text-gray-500">Alcohol type</dt>
            <dd class="mt-0.5 capitalize text-gray-800">{{ viewingCocktail?.alcohol_type }}</dd>
          </div>
        </dl>

        <div v-if="viewingCocktail?.attributes" class="space-y-3 border-t border-gray-100 pt-4 text-sm text-gray-700">
          <div v-if="viewingCocktail.attributes.glass">
            <span class="font-medium text-gray-500">Glass:</span> {{ viewingCocktail.attributes.glass }}
          </div>
          <div v-if="viewingCocktail.attributes.garnish">
            <span class="font-medium text-gray-500">Garnish:</span> {{ viewingCocktail.attributes.garnish }}
          </div>
          <div v-if="viewingCocktail.attributes.difficulty">
            <span class="font-medium text-gray-500">Difficulty:</span> {{ viewingCocktail.attributes.difficulty }}
          </div>
          <div v-if="viewingCocktail.attributes.instructions">
            <span class="font-medium text-gray-500">Instructions:</span>
            <p class="mt-1 text-gray-600">{{ viewingCocktail.attributes.instructions }}</p>
          </div>
          <div v-if="viewingCocktail.attributes.ingredients?.length">
            <span class="font-medium text-gray-500">Ingredients:</span>
            <ul class="mt-1 list-disc list-inside space-y-0.5 text-gray-600">
              <li v-for="(ing, i) in viewingCocktail.attributes.ingredients" :key="i">
                {{ ing.name }} — {{ ing.amount }}
              </li>
            </ul>
          </div>
          <div v-if="viewingCocktail.attributes.tags?.length">
            <span class="font-medium text-gray-500">Tags:</span>
            <span v-for="tag in viewingCocktail.attributes.tags" :key="tag" class="ml-1 inline-block rounded-full bg-indigo-100 px-2 py-0.5 text-xs text-indigo-700">
              {{ tag }}
            </span>
          </div>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

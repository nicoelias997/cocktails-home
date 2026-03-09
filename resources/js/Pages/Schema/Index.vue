<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const loading = ref(true)
const schemas = ref([])

const fetchSchemas = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/schemas')
    schemas.value = data.data ?? []
  } finally {
    loading.value = false
  }
}

onMounted(fetchSchemas)
</script>

<template>
  <Head title="Form Schemas" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Form Schemas</h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-4xl space-y-5 px-4 sm:px-6 lg:px-8">

        <!-- Loading skeleton -->
        <div v-if="loading" class="space-y-3">
          <div v-for="i in 2" :key="i" class="h-20 animate-pulse rounded-xl bg-gray-100" />
        </div>

        <!-- Empty state -->
        <div
          v-else-if="schemas.length === 0"
          class="rounded-xl border border-dashed border-gray-300 bg-white py-16 text-center text-sm text-gray-400"
        >
          No schemas found. Run <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs">php artisan db:seed --class=FormSchemaSeeder</code> to create the default schema.
        </div>

        <!-- Schema cards -->
        <div v-else class="space-y-3">
          <div
            v-for="schema in schemas"
            :key="schema.name"
            class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white px-6 py-4 shadow-sm transition hover:shadow-md"
          >
            <!-- Left info -->
            <div class="flex items-center gap-4">
              <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl">🗂️</span>
              <div>
                <p class="font-semibold text-gray-900 capitalize">{{ schema.name }}</p>
                <p class="mt-0.5 text-xs text-gray-500">
                  {{ schema.field_count }} field{{ schema.field_count !== 1 ? 's' : '' }}
                  <span class="mx-1.5 text-gray-300">·</span>
                  <span
                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                    :class="schema.active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500'"
                  >
                    {{ schema.active ? 'Active' : 'Inactive' }}
                  </span>
                </p>
              </div>
            </div>

            <!-- Action -->
            <Link
              :href="route('web.schema.edit', schema.name)"
              class="shrink-0 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100"
            >
              Edit fields
            </Link>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

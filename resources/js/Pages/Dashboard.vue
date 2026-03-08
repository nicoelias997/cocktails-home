<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const total   = ref(null)   // null = loading
const loading = ref(true)

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/cocktails', { params: { per_page: 1 } })
    total.value = data.meta?.total ?? 0
  } finally {
    loading.value = false
  }
})

const features = [
  {
    title: 'Collection management',
    description: 'Add, edit and remove cocktails from your personal bar. Keep every recipe organized and always at hand.',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />`,
    color: 'text-indigo-600 bg-indigo-50',
  },
  {
    title: 'Dynamic forms',
    description: 'Forms are driven by schemas stored in the database. Add new fields or sections without touching front-end code.',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />`,
    color: 'text-violet-600 bg-violet-50',
  },
  {
    title: 'Filter & search',
    description: 'Filter your collection by spirit type, sort by date and paginate results. The list view adapts as your bar grows.',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />`,
    color: 'text-sky-600 bg-sky-50',
  },
  {
    title: 'Ingredient repeater',
    description: 'Add as many ingredients as a recipe needs using the repeater field — name and amount, row by row.',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5M8.25 12h7.5m-7.5 5.25h7.5M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />`,
    color: 'text-amber-600 bg-amber-50',
  },
  {
    title: 'MongoDB backend',
    description: 'All data lives in MongoDB. The schema-less model lets you store flexible attributes without migrations for every change.',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 6c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />`,
    color: 'text-green-600 bg-green-50',
  },
  {
    title: 'Auth & profiles',
    description: 'Full authentication with registration, login, email verification and profile management out of the box.',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />`,
    color: 'text-rose-600 bg-rose-50',
  },
]

// Quick actions — extend this array when you add new models
const quickActions = [
  {
    label: 'Add cocktail',
    description: 'Create a new recipe',
    route: 'web.cocktail.create',
    style: 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm',
  },
  {
    label: 'Browse collection',
    description: 'View all cocktails',
    route: 'web.cocktail.index',
    style: 'bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 shadow-sm',
  },
]
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
    </template>

    <div class="py-10">
      <div class="mx-auto max-w-7xl space-y-10 px-4 sm:px-6 lg:px-8">

        <!-- ── Hero ── -->
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-500">
              Welcome, {{ $page.props.auth.user.name }}
            </p>
            <h1 class="mt-1 text-3xl font-bold text-gray-900">Your personal bar</h1>
            <p class="mt-2 max-w-xl text-sm text-gray-500 leading-relaxed">
              A platform to manage cocktail recipes with dynamic forms, flexible attributes
              and a clean interface. Here's everything you can do.
            </p>
          </div>

          <!-- Stat pill -->
          <div class="shrink-0 rounded-2xl border border-gray-200 bg-white px-6 py-5 text-center shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Recipes stored</p>
            <p class="mt-1 text-4xl font-bold text-indigo-600">
              <span v-if="loading" class="inline-block h-10 w-14 animate-pulse rounded-lg bg-gray-200" />
              <span v-else>{{ total }}</span>
            </p>
          </div>
        </div>

        <!-- ── Features ── -->
        <div>
          <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-400">What's inside</h2>
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="feat in features"
              :key="feat.title"
              class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md"
            >
              <div class="mb-3 inline-flex rounded-lg p-2.5" :class="feat.color">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" v-html="feat.icon" />
              </div>
              <h3 class="font-semibold text-gray-900">{{ feat.title }}</h3>
              <p class="mt-1 text-sm text-gray-500 leading-relaxed">{{ feat.description }}</p>
            </div>
          </div>
        </div>

        <!-- ── Quick actions ── -->
        <div>
          <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-400">Quick actions</h2>
          <div class="flex flex-wrap gap-3">
            <Link
              v-for="action in quickActions"
              :key="action.route"
              :href="route(action.route)"
              class="flex flex-col rounded-xl px-5 py-4 text-sm font-semibold transition-colors"
              :class="action.style"
            >
              {{ action.label }}
              <span class="mt-0.5 text-xs font-normal opacity-70">{{ action.description }}</span>
            </Link>
          </div>
        </div>

        <!-- ── How it works ── -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="mb-5 text-sm font-semibold uppercase tracking-wide text-gray-400">How it works</h2>
          <ol class="space-y-5">
            <li v-for="(step, i) in [
              { title: 'Create a recipe', body: 'Use the New cocktail button to fill in a schema-driven form with all the details: spirit type, glass, difficulty, ingredients and more.' },
              { title: 'Browse your collection', body: 'The Cocktails page lists all your recipes with filtering by spirit type, sorting and pagination.' },
              { title: 'Extend the schema', body: 'Need a new field? Edit the cocktails schema in the database and it appears in the form automatically — no code changes needed.' },
              { title: 'Add new models', body: 'Create a new FormSchema document, a new model and a new CRUD — the Dynamic components (DynamicForm, DynamicTable, FieldRepeater) are reusable across any model.' },
            ]" :key="step.title" class="flex items-start gap-4">
              <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                {{ i + 1 }}
              </span>
              <div>
                <p class="font-semibold text-gray-800">{{ step.title }}</p>
                <p class="mt-0.5 text-sm text-gray-500 leading-relaxed">{{ step.body }}</p>
              </div>
            </li>
          </ol>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

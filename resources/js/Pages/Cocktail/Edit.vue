<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, reactive, ref } from 'vue'

const props = defineProps({
  id: { type: String, required: true },
})

const loading = ref(false)
const submitting = ref(false)
const errors = ref({})
const photo = ref(null)
const currentPhotoUrl = ref(null)

const form = reactive({
  name: '',
  alcohol_type: 'other',
  remove_photo: false,
  attributes: {
    glass: '',
    garnish: '',
    difficulty: 'easy',
    instructions: '',
    ingredients: [],
  },
})

const addIngredient = () => {
  form.attributes.ingredients.push({ name: '', amount: '' })
}

const removeIngredient = (index) => {
  form.attributes.ingredients.splice(index, 1)
}

const loadCocktail = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/api/cocktails/${props.id}`)
    const cocktail = data.data
    form.name = cocktail.name
    form.alcohol_type = cocktail.alcohol_type ?? 'other'
    currentPhotoUrl.value = cocktail.photo_path ?? null

    const attr = cocktail.attributes ?? {}
    form.attributes.glass = attr.glass ?? ''
    form.attributes.garnish = attr.garnish ?? ''
    form.attributes.difficulty = attr.difficulty ?? 'easy'
    form.attributes.instructions = attr.instructions ?? ''
    form.attributes.ingredients = attr.ingredients ?? []
  } finally {
    loading.value = false
  }
}

const submit = async () => {
  submitting.value = true
  errors.value = {}

  const payload = new FormData()
  payload.append('name', form.name)
  payload.append('alcohol_type', form.alcohol_type)
  payload.append('remove_photo', form.remove_photo ? '1' : '0')
  payload.append('attributes', JSON.stringify(form.attributes))

  if (photo.value) {
    payload.append('photo', photo.value)
  }

  try {
    await axios.post(`/api/cocktails/${props.id}?_method=PUT`, payload)
    router.visit(route('web.cocktail.index'))
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors ?? {}
    }
  } finally {
    submitting.value = false
  }
}

onMounted(loadCocktail)
</script>

<template>
  <Head title="Edit Cocktail" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit cocktail</h2>
        <Link :href="route('web.cocktail.index')" class="text-sm text-indigo-600 hover:text-indigo-500">
          Back to list
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-3xl space-y-5 px-4 sm:px-6 lg:px-8">
        <div v-if="loading" class="rounded-lg border border-gray-200 bg-white p-6 text-sm text-gray-500 shadow-sm">
          Loading...
        </div>

        <template v-else>

          <!-- Basic info -->
          <section class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900">Basic info</h3>

            <div>
              <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Name</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
              <p v-if="errors.name?.[0]" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
            </div>

            <div>
              <label for="alcohol_type" class="mb-1 block text-sm font-medium text-gray-700">Alcohol type</label>
              <select
                id="alcohol_type"
                v-model="form.alcohol_type"
                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="gin">Gin</option>
                <option value="vodka">Vodka</option>
                <option value="rum">Rum</option>
                <option value="whiskey">Whiskey</option>
                <option value="tequila">Tequila</option>
                <option value="other">Other</option>
              </select>
              <p v-if="errors.alcohol_type?.[0]" class="mt-1 text-sm text-red-600">{{ errors.alcohol_type[0] }}</p>
            </div>

            <div class="space-y-3">
              <label class="block text-sm font-medium text-gray-700">Photo</label>

              <img
                v-if="currentPhotoUrl && !form.remove_photo"
                :src="currentPhotoUrl"
                alt="Current photo"
                class="h-40 w-full rounded-md object-cover"
              >

              <div v-if="currentPhotoUrl" class="flex items-center gap-2 rounded-md border border-red-200 bg-red-50 px-3 py-2">
                <input
                  id="remove_photo"
                  v-model="form.remove_photo"
                  type="checkbox"
                  class="rounded border-red-300 text-red-600 shadow-sm focus:ring-red-500"
                >
                <label for="remove_photo" class="cursor-pointer select-none text-sm text-red-700">
                  Remove current photo
                </label>
              </div>

              <input
                type="file"
                accept="image/*"
                :disabled="form.remove_photo"
                class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-40"
                @change="photo = $event.target.files?.[0] ?? null"
              >

              <p v-if="errors.photo?.[0]" class="text-sm text-red-600">{{ errors.photo[0] }}</p>
            </div>
          </section>

          <!-- Attributes -->
          <section class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900">Attributes</h3>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="glass" class="mb-1 block text-sm font-medium text-gray-700">Glass</label>
                <input
                  id="glass"
                  v-model="form.attributes.glass"
                  type="text"
                  placeholder="e.g. rocks, coupe"
                  class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
              </div>

              <div>
                <label for="garnish" class="mb-1 block text-sm font-medium text-gray-700">Garnish</label>
                <input
                  id="garnish"
                  v-model="form.attributes.garnish"
                  type="text"
                  placeholder="e.g. orange peel"
                  class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
              </div>
            </div>

            <div>
              <label for="difficulty" class="mb-1 block text-sm font-medium text-gray-700">Difficulty</label>
              <select
                id="difficulty"
                v-model="form.attributes.difficulty"
                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
              </select>
            </div>

            <div>
              <label for="instructions" class="mb-1 block text-sm font-medium text-gray-700">Instructions</label>
              <textarea
                id="instructions"
                v-model="form.attributes.instructions"
                rows="3"
                placeholder="Describe how to prepare this cocktail..."
                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
          </section>

          <!-- Ingredients -->
          <section class="space-y-3 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
              <h3 class="text-base font-semibold text-gray-900">Ingredients</h3>
              <button
                type="button"
                class="rounded-md bg-indigo-50 px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-100"
                @click="addIngredient"
              >
                + Add
              </button>
            </div>

            <p v-if="form.attributes.ingredients.length === 0" class="text-sm text-gray-400">
              No ingredients yet.
            </p>

            <div v-for="(ingredient, i) in form.attributes.ingredients" :key="i" class="flex items-center gap-2">
              <input
                v-model="ingredient.name"
                type="text"
                placeholder="Ingredient"
                class="flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
              <input
                v-model="ingredient.amount"
                type="text"
                placeholder="Amount"
                class="w-28 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
              <button type="button" class="text-red-400 hover:text-red-600" @click="removeIngredient(i)">✕</button>
            </div>
          </section>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3">
            <Link :href="route('web.cocktail.index')" class="text-sm text-gray-600 hover:text-gray-800">
              Cancel
            </Link>
            <button
              type="button"
              :disabled="submitting"
              class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:bg-indigo-300"
              @click="submit"
            >
              {{ submitting ? 'Saving...' : 'Save changes' }}
            </button>
          </div>

        </template>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

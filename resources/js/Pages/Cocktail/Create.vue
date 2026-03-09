<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DynamicForm from '@/Components/Dynamic/DynamicForm.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const submitting  = ref(false)
const errors      = ref({})
const sections    = ref([])
const schemaError = ref(null)

// Form state — initialised dynamically from schema after load
const form = ref({
  name: '',
  alcohol_type: 'other',
  photo: null,
})

/**
 * Walk the schema sections and ensure every field that maps to
 * "attributes.*" has a corresponding key initialized inside form.attributes.
 * Field types that carry sub-items (repeater) start as [].
 * Everything else starts as ''.
 */
const initAttributesFromSchema = (schemaSections) => {
  const attrs = {}
  for (const section of schemaSections) {
    for (const field of section.fields ?? []) {
      if (!field.key?.startsWith('attributes.')) continue
      const attrKey = field.key.slice('attributes.'.length)
      attrs[attrKey] = field.type === 'repeater' ? [] : ''
    }
  }
  form.value = { ...form.value, attributes: attrs }
}

const loadSchema = async () => {
  try {
    const { data } = await axios.get('/api/schemas/cocktails')
    sections.value = data.data ?? []
    initAttributesFromSchema(sections.value)
  } catch {
    schemaError.value = 'Could not load the form. Please refresh the page.'
  }
}

const submit = async () => {
  submitting.value = true
  errors.value = {}

  const payload = new FormData()
  payload.append('name', form.value.name)
  payload.append('alcohol_type', form.value.alcohol_type)
  payload.append('attributes', JSON.stringify(form.value.attributes ?? {}))
  if (form.value.photo instanceof File) {
    payload.append('photo', form.value.photo)
  }

  try {
    await axios.post('/api/cocktails', payload)
    router.visit(route('web.cocktail.index'))
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors ?? {}
    }
  } finally {
    submitting.value = false
  }
}

onMounted(loadSchema)
</script>

<template>
  <Head title="Create Cocktail" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Create cocktail</h2>
        <Link :href="route('web.cocktail.index')" class="text-sm text-indigo-600 hover:text-indigo-500">
          Back to list
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-3xl space-y-5 px-4 sm:px-6 lg:px-8">
        <div v-if="schemaError" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
          {{ schemaError }}
        </div>

        <DynamicForm
          v-else
          :sections="sections"
          :model-value="form"
          :errors="errors"
          :disabled="submitting"
          @update:model-value="form = $event"
        />

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
            {{ submitting ? 'Creating...' : 'Create cocktail' }}
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

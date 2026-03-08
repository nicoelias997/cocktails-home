<script setup>
import FieldRepeater from '@/Components/Dynamic/FieldRepeater.vue'

const props = defineProps({
  sections: {
    type: Array,
    default: () => [],
  },
  modelValue: {
    type: Object,
    default: () => ({}),
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  // { photo: 'https://...' } — current URLs for file fields (edit mode)
  currentFiles: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['update:modelValue'])

// Recursively sets a value by dot-notation path without mutating
const deepSet = (obj, keys, value) => {
  if (keys.length === 1) return { ...obj, [keys[0]]: value }
  const [head, ...tail] = keys
  return { ...obj, [head]: deepSet(obj[head] ?? {}, tail, value) }
}

const getValue = (fieldKey) =>
  fieldKey.split('.').reduce((obj, part) => obj?.[part], props.modelValue) ?? ''

const setValue = (fieldKey, value) =>
  emit('update:modelValue', deepSet(props.modelValue, fieldKey.split('.'), value))

const fieldError = (fieldKey) => {
  for (const k of [fieldKey, fieldKey.replace(/^attributes\./, '')]) {
    const err = props.errors?.[k]
    if (Array.isArray(err) && err.length > 0) return err[0]
  }
  return null
}

const normalizedOptions = (field) =>
  (field.options ?? [])
    .map((opt) => {
      if (typeof opt === 'string') return { value: opt, label: opt }
      if (opt && typeof opt === 'object') return { value: opt.value ?? '', label: opt.label ?? opt.value ?? '' }
      return null
    })
    .filter(Boolean)

// Full-width types that shouldn't sit in the 2-col grid
const fullWidthTypes = ['textarea', 'file', 'repeater']
</script>

<template>
  <div class="space-y-6">
    <section
      v-for="section in sections"
      :key="section.title"
      class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
    >
      <!-- Section header -->
      <div
        v-if="section.title"
        class="flex items-center gap-3 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-violet-50 px-6 py-4"
      >
        <span class="text-xl">🍹</span>
        <h3 class="text-sm font-semibold uppercase tracking-wide text-indigo-700">
          {{ section.title }}
        </h3>
      </div>

      <div class="p-6">
        <!-- 2-col grid; full-width fields span both columns -->
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
          <div
            v-for="field in section.fields"
            :key="field.key"
            :class="fullWidthTypes.includes(field.type) ? 'sm:col-span-2' : ''"
            class="space-y-1.5"
          >
            <!-- Label — repeater renders its own -->
            <label
              v-if="field.type !== 'repeater'"
              :for="field.key"
              class="block text-sm font-medium text-gray-700"
            >
              {{ field.label }}<span v-if="field.required" class="ml-0.5 text-red-500">*</span>
            </label>

            <!-- text | number -->
            <input
              v-if="field.type === 'text' || field.type === 'number'"
              :id="field.key"
              :type="field.type"
              :placeholder="field.placeholder"
              :value="getValue(field.key)"
              :required="field.required"
              :min="field.min"
              :max="field.max"
              :disabled="disabled"
              class="w-full rounded-lg border-gray-300 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400"
              @input="setValue(field.key, $event.target.value)"
            >

            <!-- textarea -->
            <textarea
              v-else-if="field.type === 'textarea'"
              :id="field.key"
              :placeholder="field.placeholder"
              :value="getValue(field.key)"
              :required="field.required"
              :rows="field.rows ?? 4"
              :disabled="disabled"
              class="w-full rounded-lg border-gray-300 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400"
              @input="setValue(field.key, $event.target.value)"
            />

            <!-- select -->
            <select
              v-else-if="field.type === 'select'"
              :id="field.key"
              :value="getValue(field.key)"
              :required="field.required"
              :disabled="disabled"
              class="w-full rounded-lg border-gray-300 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400"
              @change="setValue(field.key, $event.target.value)"
            >
              <option value="">Select an option</option>
              <option
                v-for="opt in normalizedOptions(field)"
                :key="`${field.key}-${opt.value}`"
                :value="opt.value"
              >
                {{ opt.label }}
              </option>
            </select>

            <!-- checkbox -->
            <label
              v-else-if="field.type === 'checkbox'"
              class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-gray-700"
            >
              <input
                :id="field.key"
                type="checkbox"
                :checked="Boolean(getValue(field.key))"
                :disabled="disabled"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                @change="setValue(field.key, $event.target.checked)"
              >
              {{ field.placeholder || 'Enabled' }}
            </label>

            <!-- file -->
            <div v-else-if="field.type === 'file'" class="space-y-3">
              <img
                v-if="currentFiles[field.key] && !getValue('remove_' + field.key)"
                :src="currentFiles[field.key]"
                :alt="field.label"
                class="h-44 w-full rounded-xl object-cover ring-1 ring-gray-200"
              >

              <div
                v-if="currentFiles[field.key] && field.removable"
                class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2"
              >
                <input
                  :id="`remove_${field.key}`"
                  type="checkbox"
                  :checked="Boolean(getValue('remove_' + field.key))"
                  class="rounded border-red-300 text-red-600 shadow-sm focus:ring-red-500"
                  @change="setValue('remove_' + field.key, $event.target.checked)"
                >
                <label
                  :for="`remove_${field.key}`"
                  class="cursor-pointer select-none text-sm text-red-700"
                >
                  Remove current {{ field.label.toLowerCase() }}
                </label>
              </div>

              <input
                type="file"
                :accept="field.accept"
                :disabled="disabled || Boolean(getValue('remove_' + field.key))"
                class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-40"
                @change="setValue(field.key, $event.target.files?.[0] ?? null)"
              >
            </div>

            <!-- repeater -->
            <FieldRepeater
              v-else-if="field.type === 'repeater'"
              :field="field"
              :model-value="getValue(field.key) || []"
              :disabled="disabled"
              @update:model-value="setValue(field.key, $event)"
            />

            <!-- Error -->
            <p v-if="fieldError(field.key)" class="flex items-center gap-1 text-xs text-red-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              {{ fieldError(field.key) }}
            </p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

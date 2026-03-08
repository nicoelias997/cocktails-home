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
  // Check exact key and bare key (without "attributes." prefix)
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
</script>

<template>
  <div class="space-y-5">
    <section
      v-for="section in sections"
      :key="section.title"
      class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
    >
      <h3 v-if="section.title" class="text-base font-semibold text-gray-900">
        {{ section.title }}
      </h3>

      <div v-for="field in section.fields" :key="field.key" class="space-y-1">
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
          class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-100"
          @input="setValue(field.key, $event.target.value)"
        >

        <!-- textarea -->
        <textarea
          v-else-if="field.type === 'textarea'"
          :id="field.key"
          :placeholder="field.placeholder"
          :value="getValue(field.key)"
          :required="field.required"
          :rows="field.rows ?? 3"
          :disabled="disabled"
          class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-100"
          @input="setValue(field.key, $event.target.value)"
        />

        <!-- select -->
        <select
          v-else-if="field.type === 'select'"
          :id="field.key"
          :value="getValue(field.key)"
          :required="field.required"
          :disabled="disabled"
          class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-100"
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
          class="inline-flex cursor-pointer items-center gap-2 text-sm text-gray-700"
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
            class="h-40 w-full rounded-md object-cover"
          >

          <div
            v-if="currentFiles[field.key] && field.removable"
            class="flex items-center gap-2 rounded-md border border-red-200 bg-red-50 px-3 py-2"
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
            class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-40"
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

        <p v-if="fieldError(field.key)" class="text-sm text-red-600">
          {{ fieldError(field.key) }}
        </p>
      </div>
    </section>
  </div>
</template>

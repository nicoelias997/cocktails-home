<script setup>
const props = defineProps({
  field: {
    type: Object,
    required: true,
  },
  modelValue: {
    type: Array,
    default: () => [],
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])

const addRow = () => {
  const empty = Object.fromEntries(
    (props.field.subfields ?? []).map((sub) => [sub.key, '']),
  )
  emit('update:modelValue', [...props.modelValue, empty])
}

const removeRow = (index) => {
  emit('update:modelValue', props.modelValue.filter((_, i) => i !== index))
}

const updateRow = (index, subKey, value) => {
  emit(
    'update:modelValue',
    props.modelValue.map((row, i) => (i === index ? { ...row, [subKey]: value } : row)),
  )
}
</script>

<template>
  <div class="space-y-3">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <span class="text-sm font-medium text-gray-700">
        {{ field.label }}<span v-if="field.required" class="ml-0.5 text-red-500">*</span>
      </span>
      <button
        type="button"
        :disabled="disabled"
        class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-40"
        @click="addRow"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Add
      </button>
    </div>

    <!-- Empty state -->
    <div v-if="modelValue.length === 0" class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center">
      <p class="text-sm text-gray-400">No {{ field.label.toLowerCase() }} yet. Click Add to start.</p>
    </div>

    <!-- Rows -->
    <div
      v-for="(row, i) in modelValue"
      :key="i"
      class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 shadow-sm"
    >
      <span class="shrink-0 text-xs font-bold text-indigo-400 w-5 text-center">{{ i + 1 }}</span>

      <input
        v-for="sub in field.subfields ?? []"
        :key="sub.key"
        :value="row[sub.key] ?? ''"
        type="text"
        :placeholder="sub.label"
        :disabled="disabled"
        :class="sub.flex ? 'flex-1' : 'w-28'"
        class="rounded-lg border-gray-300 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-100"
        @input="updateRow(i, sub.key, $event.target.value)"
      >

      <button
        type="button"
        :disabled="disabled"
        title="Remove"
        class="flex size-7 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-100 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-40"
        @click="removeRow(i)"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

  </div>
</template>

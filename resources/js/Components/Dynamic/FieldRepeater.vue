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
    <div class="flex items-center justify-between">
      <span class="text-sm font-medium text-gray-700">
        {{ field.label }}<span v-if="field.required" class="ml-0.5 text-red-500">*</span>
      </span>
      <button
        type="button"
        :disabled="disabled"
        class="rounded-md bg-indigo-50 px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-40"
        @click="addRow"
      >
        + Add
      </button>
    </div>

    <p v-if="modelValue.length === 0" class="text-sm text-gray-400">
      No {{ field.label.toLowerCase() }} yet.
    </p>

    <div
      v-for="(row, i) in modelValue"
      :key="i"
      class="flex items-center gap-2"
    >
      <input
        v-for="sub in field.subfields ?? []"
        :key="sub.key"
        :value="row[sub.key] ?? ''"
        type="text"
        :placeholder="sub.label"
        :disabled="disabled"
        :class="sub.flex ? 'flex-1' : 'w-28'"
        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-100"
        @input="updateRow(i, sub.key, $event.target.value)"
      >
      <button
        type="button"
        :disabled="disabled"
        class="shrink-0 text-red-400 hover:text-red-600 disabled:opacity-40"
        @click="removeRow(i)"
      >
        ✕
      </button>
    </div>
  </div>
</template>

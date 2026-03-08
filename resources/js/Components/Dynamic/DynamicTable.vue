<script setup>
const props = defineProps({
  columns: {
    type: Array,
    default: () => [],
  },
  items: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['show', 'edit', 'delete'])
</script>

<template>
  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th v-for="col in columns" :key="col.key" class="px-4 py-3 text-left font-semibold text-gray-700">
            {{ col.label }}
          </th>
          <th class="px-4 py-3 text-left font-semibold text-gray-700">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <tr v-if="loading">
          <td :colspan="columns.length + 1" class="px-4 py-6 text-gray-500">Loading...</td>
        </tr>
        <tr v-else-if="items.length === 0">
          <td :colspan="columns.length + 1" class="px-4 py-6 text-gray-500">No data yet.</td>
        </tr>
        <tr v-for="item in items" :key="item.id">
          <td v-for="col in columns" :key="`${item.id}-${col.key}`" class="px-4 py-3 text-gray-800">
            {{ typeof col.format === 'function' ? col.format(item[col.key], item) : item[col.key] }}
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2">
              <button class="rounded border border-gray-300 px-2 py-1 text-xs hover:bg-gray-50" @click="$emit('show', item)">
                View
              </button>
              <button class="rounded border border-indigo-300 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-50" @click="$emit('edit', item)">
                Edit
              </button>
              <button class="rounded border border-red-300 px-2 py-1 text-xs text-red-700 hover:bg-red-50" @click="$emit('delete', item)">
                Delete
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

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
  <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead>
        <tr class="bg-gradient-to-r from-indigo-50 to-violet-50">
          <th
            v-for="col in columns"
            :key="col.key"
            class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-indigo-700"
          >
            {{ col.label }}
          </th>
          <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-indigo-700">
            Actions
          </th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100">

        <!-- Skeleton -->
        <tr v-if="loading" v-for="n in 5" :key="`skel-${n}`" class="animate-pulse">
          <td v-for="col in columns" :key="`skel-col-${col.key}`" class="px-5 py-4">
            <div class="h-3 rounded bg-gray-200" :class="n % 2 === 0 ? 'w-2/3' : 'w-1/2'" />
          </td>
          <td class="px-5 py-4">
            <div class="flex justify-end gap-2">
              <div class="size-7 rounded-lg bg-gray-200" />
              <div class="size-7 rounded-lg bg-gray-200" />
              <div class="size-7 rounded-lg bg-gray-200" />
            </div>
          </td>
        </tr>

        <!-- Empty -->
        <tr v-else-if="items.length === 0">
          <td :colspan="columns.length + 1" class="px-5 py-16 text-center">
            <p class="text-5xl">🍸</p>
            <p class="mt-3 text-sm font-medium text-gray-500">No cocktails yet.</p>
            <p class="mt-0.5 text-xs text-gray-400">Create your first one to get started.</p>
          </td>
        </tr>

        <!-- Rows -->
        <tr
          v-else
          v-for="item in items"
          :key="item.id"
          class="group cursor-pointer transition-colors hover:bg-indigo-50/40"
          @click="$emit('show', item)"
        >
          <td
            v-for="col in columns"
            :key="`${item.id}-${col.key}`"
            class="px-5 py-3.5 font-medium text-gray-800"
          >
            {{ typeof col.format === 'function' ? col.format(item[col.key], item) : item[col.key] }}
          </td>

          <td class="px-5 py-3.5" @click.stop>
            <div class="flex items-center justify-end gap-1.5">

              <!-- View -->
              <button
                title="View"
                class="flex size-7 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-indigo-100 hover:text-indigo-600"
                @click="$emit('show', item)"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>

              <!-- Edit -->
              <button
                title="Edit"
                class="flex size-7 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-amber-100 hover:text-amber-600"
                @click="$emit('edit', item)"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
              </button>

              <!-- Delete -->
              <button
                title="Delete"
                class="flex size-7 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-red-100 hover:text-red-600"
                @click="$emit('delete', item)"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>

            </div>
          </td>
        </tr>

      </tbody>
    </table>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="flex min-h-screen">

    <!-- Left panel — brand -->
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-600 p-12 text-white">
      <Link href="/" class="flex items-center gap-2.5 text-white/90 hover:text-white transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-7 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
        <span class="text-lg font-bold tracking-tight">Cocktails Home</span>
      </Link>

      <div class="space-y-4">
        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-200">Your personal bar</p>
        <h1 class="text-4xl font-bold leading-snug">
          Manage your cocktail collection with ease.
        </h1>
        <p class="text-indigo-200 text-sm leading-relaxed max-w-sm">
          Store recipes, track ingredients, filter by spirit and keep your bar organized — all in one place.
        </p>
      </div>

      <p class="text-xs text-indigo-300">
        &copy; {{ new Date().getFullYear() }} Cocktails Home
      </p>
    </div>

    <!-- Right panel — form slot -->
    <div class="flex flex-1 flex-col items-center justify-center bg-gray-50 px-6 py-12">

      <!-- Mobile logo -->
      <Link href="/" class="mb-8 flex items-center gap-2 lg:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
        <span class="text-base font-bold text-gray-900 tracking-tight">Cocktails Home</span>
      </Link>

      <div class="w-full max-w-md">
        <div class="rounded-2xl border border-gray-200 bg-white px-8 py-8 shadow-sm">
          <slot />
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import { Link } from '@inertiajs/vue3'

const showingNavigationDropdown = ref(false)
const isAdmin = computed(() => usePage().props.auth.user?.is_admin ?? false)

// Nav items — add new entries here when adding models
const allNavItems = [
  {
    label: 'Dashboard',
    route: 'dashboard',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />`,
  },
  {
    label: 'Schemas',
    route: 'web.schema.index',
    adminOnly: true,
    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />`,
  },
  {
    label: 'Cocktails',
    route: 'web.cocktail.index',
    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />`,
  }
]

const navItems = computed(() => allNavItems.filter(item => !item.adminOnly || isAdmin.value))
</script>

<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Top nav -->
    <nav class="border-b border-gray-200 bg-white shadow-sm">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

          <!-- Left: logo + links -->
          <div class="flex items-center gap-8">

            <!-- Wordmark -->
            <Link :href="route('dashboard')" class="flex items-center gap-2 shrink-0">
              <span class="flex size-8 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 text-white shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
              </span>
              <span class="hidden text-sm font-bold tracking-tight text-gray-900 sm:block">Cocktails Home</span>
            </Link>

            <!-- Desktop nav links -->
            <div class="hidden items-center gap-1 sm:flex">
              <NavLink
                v-for="item in navItems"
                :key="item.route"
                :href="route(item.route)"
                :active="route().current(item.route)"
                class="inline-flex items-center gap-1.5"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" v-html="item.icon" />
                {{ item.label }}
              </NavLink>
            </div>
          </div>

          <!-- Right: user dropdown -->
          <div class="hidden sm:flex sm:items-center">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button
                  type="button"
                  class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none"
                >
                  <span class="flex size-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                  </span>
                  {{ $page.props.auth.user.name }}
                  <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </template>

              <template #content>
                <DropdownLink :href="route('web.profile.edit')">
                  Profile
                </DropdownLink>
                <DropdownLink :href="route('logout')" method="post" as="button">
                  Log Out
                </DropdownLink>
              </template>
            </Dropdown>
          </div>

          <!-- Mobile hamburger -->
          <button
            class="-me-2 flex items-center justify-center rounded-md p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 sm:hidden"
            @click="showingNavigationDropdown = !showingNavigationDropdown"
          >
            <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path
                :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile menu -->
      <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
        <div class="space-y-1 border-t border-gray-100 px-3 pb-3 pt-2">
          <ResponsiveNavLink
            v-for="item in navItems"
            :key="item.route"
            :href="route(item.route)"
            :active="route().current(item.route)"
          >
            {{ item.label }}
          </ResponsiveNavLink>
        </div>

        <div class="border-t border-gray-200 px-4 pb-3 pt-4">
          <div class="flex items-center gap-3">
            <span class="flex size-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">
              {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
            </span>
            <div>
              <p class="text-sm font-semibold text-gray-800">{{ $page.props.auth.user.name }}</p>
              <p class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</p>
            </div>
          </div>
          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('web.profile.edit')">Profile</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page heading slot -->
    <header v-if="$slots.header" class="border-b border-gray-200 bg-white">
      <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Page content -->
    <main>
      <slot />
    </main>
  </div>
</template>

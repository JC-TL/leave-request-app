<script setup>
import { ref, computed } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const page = usePage();
const user = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash);
const departmentColor = computed(() => page.props.auth.departmentColor);

const dashboardRoute = computed(() => {
    switch (user.value?.role) {
        case 'employee': return 'employee.dashboard';
        case 'dept_manager': return 'manager.dashboard';
        case 'hr_admin':
        case 'ceo': return 'hr.dashboard';
        default: return 'dashboard';
    }
});

const roleLabel = computed(() => {
    switch (user.value?.role) {
        case 'employee': return 'Employee';
        case 'dept_manager': return 'Manager';
        case 'hr_admin': return 'HR Admin';
        case 'ceo': return 'CEO';
        default: return user.value?.role;
    }
});

const isHR = computed(() => ['hr_admin', 'ceo'].includes(user.value?.role));
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100/85">
            <nav class="border-b border-gray-100 bg-white" :style="{ borderTopColor: departmentColor, borderTopWidth: '3px' }">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:flex">
                                <NavLink :href="route(dashboardRoute)" :active="route().current(dashboardRoute)">
                                    Dashboard
                                </NavLink>
                                
                                <!-- HR-specific links -->
                                <template v-if="isHR">
                                    <NavLink :href="route('hr.policies')" :active="route().current('hr.policies')">
                                        Policies
                                    </NavLink>
                                    <NavLink :href="route('hr.employees')" :active="route().current('hr.employees')">
                                        Employees
                                    </NavLink>
                                    <NavLink :href="route('hr.departments')" :active="route().current('hr.departments')">
                                        Departments
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Role Badge with Dynamic Department Color -->
                            <span 
                                :style="{ 
                                    backgroundColor: departmentColor + '15',
                                    color: departmentColor,
                                    borderColor: departmentColor + '40'
                                }"
                                class="mr-4 rounded-full border px-3 py-1 text-xs font-medium"
                            >
                                {{ roleLabel }}
                            </span>
                            
                            <!-- Settings Dropdown (hamburger trigger) -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                :style="{ '--focus-ring-color': departmentColor }"
                                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-1"
                                                style="--tw-ring-color: var(--focus-ring-color)"
                                                aria-label="Open menu"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="border-b border-gray-100 px-4 py-3">
                                            <p class="text-sm font-medium text-gray-800">{{ user.name }}</p>
                                            <p class="text-xs text-gray-500">{{ roleLabel }}</p>
                                        </div>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            <span class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                                </svg>
                                                Log Out
                                            </span>
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route(dashboardRoute)" :active="route().current(dashboardRoute)">
                            Dashboard
                        </ResponsiveNavLink>
                        
                        <template v-if="isHR">
                            <ResponsiveNavLink :href="route('hr.policies')" :active="route().current('hr.policies')">
                                Policies
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('hr.employees')" :active="route().current('hr.employees')">
                                Employees
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('hr.departments')" :active="route().current('hr.departments')">
                                Departments
                            </ResponsiveNavLink>
                        </template>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">{{ user.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Flash Messages (after header) -->
            <div v-if="flash?.success" class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ flash.success }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="flash?.error" class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ flash.error }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

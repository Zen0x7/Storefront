<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps(['orders', 'error']);
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Orders
            </h2>
        </template>

        <div v-if="error" class="mb-4 rounded bg-red-100 p-4 text-red-700">
            {{ error }}
        </div>

        <div v-if="orders.length === 0 && !error">
            <p>There are no order available.</p>
        </div>

        <div class="p-4">
            <div
                v-for="order in orders"
                :key="order.id"
                class="mb-6 rounded border p-4 shadow-md"
            >
                <div class="mb-2">
                    <h2 class="text-lg font-semibold">{{ order.name }}</h2>
                    <p class="text-sm text-gray-600">
                        Date: {{ new Date(order.createdAt).toLocaleString() }}
                    </p>
                    <p class="text-sm">
                        Client: {{ order.customer.name }} ({{
                            order.customer.email
                        }})
                    </p>
                    <p class="text-sm">Total: ${{ order.totalPrice }}</p>
                </div>

                <div class="mt-2">
                    <p class="font-semibold">Products:</p>
                    <ul class="ml-4 list-disc">
                        <li
                            v-for="item in order.lineItems"
                            :key="item.title + item.unitPrice"
                        >
                            {{ item.quantity }}x {{ item.title }} — ${{
                                item.unitPrice
                            }}
                        </li>
                    </ul>
                </div>
            </div>

            <a
                href="/export/orders"
                class="mt-6 inline-block rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700"
            >
                Get as XLSX
            </a>
        </div>
    </AuthenticatedLayout>
</template>

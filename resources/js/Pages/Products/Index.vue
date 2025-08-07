<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps(['products']);
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Products
            </h2>
        </template>
        <div class="p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="rounded border p-4 shadow"
                >
                    <img
                        :src="product.image"
                        class="mb-2 h-32 object-contain"
                        v-if="product.image"
                    />
                    <h2 class="font-semibold">{{ product.title }}</h2>
                    <p>SKU: {{ product.sku }}</p>
                    <p>Price: ${{ product.price }}</p>
                </div>
            </div>

            <a
                href="/export/products"
                class="mt-6 inline-block rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700"
            >
                Get as XLSX
            </a>
        </div>
    </AuthenticatedLayout>
</template>

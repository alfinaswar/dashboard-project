<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
defineProps({ clients: Object });
</script>
<template>
    <Head title="Klien" /><AuthenticatedLayout>
        <template #header><div class="flex w-full items-center justify-between"><h1 class="text-2xl font-semibold text-slate-900">Klien</h1><Link href="/klien/create" class="rounded-lg bg-slate-900 px-4 py-2 text-sm text-white">Tambah klien</Link></div></template>
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8"><div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div v-if="!clients.data.length" class="px-6 py-16 text-center text-slate-500">Belum ada klien.</div>
            <table v-else class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Perusahaan</th><th class="px-5 py-3">Kontak</th><th class="px-5 py-3">Proyek</th></tr></thead><tbody><tr v-for="client in clients.data" :key="client.id" class="border-t border-slate-100"><td class="px-5 py-4"><Link :href="`/klien/${client.id}`" class="font-medium text-slate-900">{{ client.nama_klien }}</Link></td><td class="px-5 py-4">{{ client.nama_perusahaan || '—' }}</td><td class="px-5 py-4">{{ client.email || client.nomor_whatsapp || '—' }}</td><td class="px-5 py-4">{{ client.proyek_count }}</td></tr></tbody></table>
        </div><div class="mt-5 flex items-center justify-between text-sm text-slate-500"><span>Halaman {{ clients.current_page }} dari {{ clients.last_page }}</span><div class="flex gap-2"><Link v-if="clients.prev_page_url" :href="clients.prev_page_url" class="rounded-lg border bg-white px-3 py-2">Sebelumnya</Link><Link v-if="clients.next_page_url" :href="clients.next_page_url" class="rounded-lg border bg-white px-3 py-2">Berikutnya</Link></div></div></main>
    </AuthenticatedLayout>
</template>

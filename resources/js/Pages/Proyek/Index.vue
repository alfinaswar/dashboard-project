<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
defineProps({ projects: Object });
const money = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
</script>

<template>
    <Head title="Proyek" />
    <AuthenticatedLayout>
        <template #header><div class="flex w-full items-center justify-between"><h1 class="text-2xl font-semibold text-slate-900">Proyek</h1><Link href="/proyek/create" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Tambah proyek</Link></div></template>
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
                <div v-if="!projects.data.length" class="px-6 py-16 text-center text-slate-500">Belum ada proyek. Buat proyek pertama Anda.</div>
                <div v-else class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-5 py-3">Proyek</th><th class="px-5 py-3">Klien</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Pembayaran</th><th class="px-5 py-3">Margin</th><th class="px-5 py-3">Deadline</th><th class="px-5 py-3"></th></tr></thead><tbody><tr v-for="project in projects.data" :key="project.id" class="border-t border-slate-100"><td class="px-5 py-4"><Link :href="`/proyek/${project.id}`" class="font-medium text-slate-900">{{ project.judul_proyek }}</Link><div class="mt-1 font-mono text-xs text-slate-500">{{ project.kode_proyek }}</div></td><td class="px-5 py-4">{{ project.klien.nama_klien }}</td><td class="px-5 py-4">{{ project.status.nama_status }}</td><td class="px-5 py-4 font-mono">{{ money(project.total_dibayar) }} / {{ money(project.nilai_bersih_kontrak) }}</td><td class="px-5 py-4">{{ project.margin_keuntungan }}%</td><td class="px-5 py-4" :class="project.tanggal_deadline && new Date(project.tanggal_deadline) < new Date() ? 'text-rose-600' : ''">{{ project.tanggal_deadline || '—' }}</td><td class="px-5 py-4"><Link :href="`/proyek/${project.id}`" class="text-slate-600 hover:text-slate-900">Buka</Link></td></tr></tbody></table></div>
            </div>
            <div class="mt-5 flex items-center justify-between text-sm text-slate-500"><span>Halaman {{ projects.current_page }} dari {{ projects.last_page }}</span><div class="flex gap-2"><Link v-if="projects.prev_page_url" :href="projects.prev_page_url" class="rounded-lg border bg-white px-3 py-2">Sebelumnya</Link><Link v-if="projects.next_page_url" :href="projects.next_page_url" class="rounded-lg border bg-white px-3 py-2">Berikutnya</Link></div></div>
        </main>
    </AuthenticatedLayout>
</template>

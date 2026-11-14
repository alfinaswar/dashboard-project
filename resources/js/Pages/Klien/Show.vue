<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
defineProps({ client: Object });
</script>
<template>
    <Head :title="client.nama_klien" /><AuthenticatedLayout><template #header><div class="flex w-full items-center justify-between"><h1 class="text-2xl font-semibold text-slate-900">{{ client.nama_klien }}</h1><Link :href="`/klien/${client.id}/edit`" class="rounded-lg border px-4 py-2 text-sm">Edit klien</Link></div></template>
        <main class="mx-auto max-w-5xl space-y-6 px-4 py-8"><section class="rounded-2xl border bg-white p-6"><p>{{ client.nama_perusahaan || 'Individu' }}</p><p class="mt-2 text-sm text-slate-500">{{ client.email || '—' }} · {{ client.nomor_whatsapp || '—' }}</p><p class="mt-4 whitespace-pre-line text-sm text-slate-600">{{ client.alamat || 'Alamat belum diisi.' }}</p></section><section class="rounded-2xl border bg-white p-6"><h2 class="font-semibold">Proyek klien</h2><div v-if="!client.proyek.length" class="mt-4 text-sm text-slate-500">Belum ada proyek.</div><Link v-for="project in client.proyek" :key="project.id" :href="`/proyek/${project.id}`" class="mt-4 flex justify-between border-t pt-4 text-sm"><span>{{ project.judul_proyek }} · {{ project.kode_proyek }}</span><span>{{ project.status.nama_status }}</span></Link></section></main>
    </AuthenticatedLayout></template>

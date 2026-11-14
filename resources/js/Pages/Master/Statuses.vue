<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
defineProps({ items: Array });
const form = useForm({ id: null, nama_status: '', warna_label: 'blue', urutan: 1 });
function submit() { if (form.id) form.put(`/master/status-proyek/${form.id}`); else form.post('/master/status-proyek', { onSuccess: reset }); }
function edit(item) { form.id = item.id; form.nama_status = item.nama_status; form.warna_label = item.warna_label; form.urutan = item.urutan; }
function reset() { form.reset(); }
</script>
<template><Head title="Status proyek"/><AuthenticatedLayout><template #header><h1 class="text-2xl font-semibold">Status proyek</h1></template><main class="mx-auto max-w-5xl space-y-6 px-4 py-8"><form @submit.prevent="submit" class="grid gap-3 rounded-2xl border bg-white p-5 sm:grid-cols-4"><input v-model="form.nama_status" required placeholder="Nama status" class="rounded-lg border-slate-300"/><input v-model="form.warna_label" required placeholder="Warna" class="rounded-lg border-slate-300"/><input v-model="form.urutan" type="number" min="1" required class="rounded-lg border-slate-300"/><div class="flex gap-2"><button class="rounded-lg bg-slate-900 px-4 py-2 text-sm text-white">{{ form.id ? 'Simpan' : 'Tambah status' }}</button><button v-if="form.id" type="button" @click="reset">Batal</button></div></form><section class="overflow-hidden rounded-2xl border bg-white"><div v-for="item in items" :key="item.id" class="flex justify-between border-b p-4 text-sm last:border-0">{{ item.urutan }}. {{ item.nama_status }} <div class="flex gap-3"><button @click="edit(item)" class="text-blue-700">Edit</button><button @click="router.delete(`/master/status-proyek/${item.id}`)" class="text-rose-600">Arsip</button></div></div></section></main></AuthenticatedLayout></template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
const props = defineProps({ client: Object });
const form = useForm({ nama_klien: props.client?.nama_klien ?? '', nama_perusahaan: props.client?.nama_perusahaan ?? '', email: props.client?.email ?? '', nomor_whatsapp: props.client?.nomor_whatsapp ?? '', alamat: props.client?.alamat ?? '', catatan: props.client?.catatan ?? '' });
function submit() { props.client ? form.put(`/klien/${props.client.id}`) : form.post('/klien'); }
</script>
<template>
    <Head title="Data klien" /><AuthenticatedLayout><template #header><h1 class="text-2xl font-semibold text-slate-900">{{ client ? 'Edit klien' : 'Tambah klien' }}</h1></template>
        <main class="mx-auto max-w-3xl px-4 py-8"><form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div><InputLabel for="nama" value="Nama klien" /><TextInput id="nama" v-model="form.nama_klien" class="mt-1 block w-full" required /><InputError :message="form.errors.nama_klien" /></div>
            <div><InputLabel for="company" value="Perusahaan" /><TextInput id="company" v-model="form.nama_perusahaan" class="mt-1 block w-full" /></div>
            <div class="grid gap-5 sm:grid-cols-2"><div><InputLabel for="email" value="Email" /><TextInput id="email" type="email" v-model="form.email" class="mt-1 block w-full" /><InputError :message="form.errors.email" /></div><div><InputLabel for="wa" value="WhatsApp" /><TextInput id="wa" v-model="form.nomor_whatsapp" class="mt-1 block w-full" /></div></div>
            <div><InputLabel for="alamat" value="Alamat" /><textarea id="alamat" v-model="form.alamat" rows="3" class="mt-1 block w-full rounded-md border-slate-300"></textarea></div>
            <div><InputLabel for="catatan" value="Catatan" /><textarea id="catatan" v-model="form.catatan" rows="3" class="mt-1 block w-full rounded-md border-slate-300"></textarea></div>
            <div class="flex justify-end gap-3"><Link href="/klien" class="px-4 py-2 text-sm text-slate-600">Batal</Link><PrimaryButton :disabled="form.processing">Simpan</PrimaryButton></div>
        </form></main>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ project: Object, clients: Array, sources: Array, statuses: Array });
const form = useForm({
    klien_id: props.project?.klien_id ?? '', sumber_order_id: props.project?.sumber_order_id ?? '', status_id: props.project?.status_id ?? '',
    judul_proyek: props.project?.judul_proyek ?? '', deskripsi: props.project?.deskripsi ?? '',
    tanggal_mulai: props.project?.tanggal_mulai?.slice(0, 10) ?? new Date().toISOString().slice(0, 10),
    tanggal_deadline: props.project?.tanggal_deadline?.slice(0, 10) ?? '', mata_uang: props.project?.mata_uang ?? 'IDR', nilai_kotor: props.project?.nilai_kotor ?? '',
    persentase_potongan_platform: props.project?.persentase_potongan_platform ?? 0,
    tech_stack: props.project?.tech_stack ?? '', url_produksi: props.project?.url_produksi ?? '', url_repository: props.project?.url_repository ?? '',
});
const selectedSource = computed(() => props.sources.find(source => source.id == form.sumber_order_id));
const fee = computed(() => Number(form.nilai_kotor || 0) * Number(form.persentase_potongan_platform || 0) / 100);
const net = computed(() => Number(form.nilai_kotor || 0) - fee.value);
function setSource() { if (selectedSource.value) form.persentase_potongan_platform = selectedSource.value.persentase_potongan_default; }
function submit() { props.project ? form.put(`/proyek/${props.project.id}`) : form.post('/proyek'); }
</script>

<template>
    <Head :title="project ? 'Edit proyek' : 'Proyek baru'" />
    <AuthenticatedLayout>
        <template #header><h1 class="text-2xl font-semibold text-slate-900">{{ project ? 'Edit proyek' : 'Proyek baru' }}</h1></template>
        <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8"><form @submit.prevent="submit" class="space-y-6 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2"><InputLabel for="judul" value="Judul proyek" /><TextInput id="judul" v-model="form.judul_proyek" class="mt-1 block w-full" required /><InputError :message="form.errors.judul_proyek" class="mt-2" /></div>
                <div><InputLabel for="klien" value="Klien" /><select id="klien" v-model="form.klien_id" class="mt-1 block w-full rounded-md border-slate-300"><option value="">Pilih klien</option><option v-for="client in clients" :key="client.id" :value="client.id">{{ client.nama_klien }}</option></select><InputError :message="form.errors.klien_id" class="mt-2" /></div>
                <div><InputLabel for="sumber" value="Sumber order" /><select id="sumber" v-model="form.sumber_order_id" @change="setSource" class="mt-1 block w-full rounded-md border-slate-300"><option value="">Pilih sumber</option><option v-for="source in sources" :key="source.id" :value="source.id">{{ source.nama_sumber }} ({{ source.persentase_potongan_default }}%)</option></select><InputError :message="form.errors.sumber_order_id" class="mt-2" /></div>
                <div><InputLabel for="status" value="Status" /><select id="status" v-model="form.status_id" class="mt-1 block w-full rounded-md border-slate-300"><option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.nama_status }}</option></select><InputError :message="form.errors.status_id" class="mt-2" /></div>
                <div><InputLabel for="mulai" value="Tanggal mulai" /><TextInput id="mulai" type="date" v-model="form.tanggal_mulai" class="mt-1 block w-full" required /><InputError :message="form.errors.tanggal_mulai" class="mt-2" /></div>
                <div><InputLabel for="deadline" value="Deadline" /><TextInput id="deadline" type="date" v-model="form.tanggal_deadline" class="mt-1 block w-full" /><InputError :message="form.errors.tanggal_deadline" class="mt-2" /></div>
                <div><InputLabel for="gross" value="Nilai kontrak kotor" /><TextInput id="gross" type="number" min="0" step="0.01" v-model="form.nilai_kotor" class="mt-1 block w-full" required /><InputError :message="form.errors.nilai_kotor" class="mt-2" /></div>
                <div><InputLabel for="fee" value="Potongan platform (%)" /><TextInput id="fee" type="number" min="0" max="100" step="0.01" v-model="form.persentase_potongan_platform" class="mt-1 block w-full" required /><InputError :message="form.errors.persentase_potongan_platform" class="mt-2" /></div>
                <div class="rounded-xl bg-blue-50 p-4 text-sm text-blue-800">Potongan: {{ fee.toLocaleString('id-ID') }} · Bersih: {{ net.toLocaleString('id-ID') }}</div>
                <div><InputLabel for="stack" value="Tech stack" /><TextInput id="stack" v-model="form.tech_stack" class="mt-1 block w-full" /></div>
                <div><InputLabel for="url" value="URL produksi" /><TextInput id="url" type="url" v-model="form.url_produksi" class="mt-1 block w-full" /><InputError :message="form.errors.url_produksi" class="mt-2" /></div>
                <div class="sm:col-span-2"><InputLabel for="repo" value="URL repository" /><TextInput id="repo" type="url" v-model="form.url_repository" class="mt-1 block w-full" /><InputError :message="form.errors.url_repository" class="mt-2" /></div>
                <div class="sm:col-span-2"><InputLabel for="desc" value="Deskripsi / lingkup kerja" /><textarea id="desc" v-model="form.deskripsi" rows="4" class="mt-1 block w-full rounded-md border-slate-300"></textarea><InputError :message="form.errors.deskripsi" class="mt-2" /></div>
            </div>
            <div class="flex justify-end gap-3"><Link href="/proyek" class="rounded-md px-4 py-2 text-sm text-slate-600">Batal</Link><PrimaryButton :disabled="form.processing">Simpan proyek</PrimaryButton></div>
        </form></main>
    </AuthenticatedLayout>
</template>

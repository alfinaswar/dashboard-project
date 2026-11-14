<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({ metrics: Array, cashFlow: Array, sources: Array, activeProjects: Array, searchItems: Array });
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
const searchOpen = ref(false);
const search = ref('');
const filteredSearchItems = computed(() => props.searchItems.filter(item => `${item.label} ${item.meta}`.toLowerCase().includes(search.value.toLowerCase())));
function onKeydown(event) {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); searchOpen.value = !searchOpen.value; }
    if (event.key === 'Escape') searchOpen.value = false;
}
onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));
const currency = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
const cashOptions = {
    chart: { toolbar: { show: false }, foreColor: '#64748b', fontFamily: 'Inter, sans-serif' },
    colors: ['#3b82f6', '#f59e0b', '#10b981'],
    dataLabels: { enabled: false }, stroke: { curve: 'smooth', width: 3 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.28, opacityTo: 0.03 } },
    grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
    xaxis: { categories: props.cashFlow.map(row => row.label) },
    yaxis: { labels: { formatter: value => new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value) } },
    legend: { position: 'top', horizontalAlign: 'right' },
};
const cashSeries = [
    { name: 'Pendapatan bersih', data: props.cashFlow.map(row => row.pendapatan) },
    { name: 'Modal', data: props.cashFlow.map(row => row.modal) },
    { name: 'Laba bersih', data: props.cashFlow.map(row => row.laba) },
];
const sourceOptions = {
    labels: props.sources.map(source => source.name),
    colors: ['#2563eb', '#10b981', '#8b5cf6', '#f59e0b', '#f43f5e'],
    legend: { position: 'bottom' },
    dataLabels: { enabled: true },
    stroke: { colors: ['#fff'] },
};
const sourceSeries = props.sources.map(source => source.count);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <p class="text-sm font-medium text-slate-500">Ringkasan bisnis</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">Dashboard</h1>
            </div>
        </template>

        <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <button @click="searchOpen = true" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-500 shadow-sm">Cari proyek atau klien <kbd class="ml-3 rounded border px-1.5 py-0.5 text-xs">Ctrl K</kbd></button>
            <p class="text-sm text-slate-500">Pantau proyek dan keuangan freelance Anda.</p>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="item in metrics" :key="item.label" class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ item.label }}</p>
                    <p class="mt-3 font-mono text-2xl font-semibold text-slate-900">{{ currency(item.value) }}</p>
                    <p v-if="item.detail" class="mt-2 text-xs text-rose-600">{{ item.detail }}</p>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm lg:col-span-2">
                    <h2 class="font-semibold text-slate-900">Arus kas bulanan</h2>
                    <p class="mt-1 text-sm text-slate-500">Pendapatan, modal, dan laba enam bulan terakhir.</p>
                    <apexchart class="mt-4" type="area" height="250" :options="cashOptions" :series="cashSeries" />
                </section>
                <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                    <h2 class="font-semibold text-slate-900">Sumber order</h2>
                    <p class="mt-1 text-sm text-slate-500">Komposisi kanal proyek Anda.</p>
                    <div v-if="sources.length" class="mt-4"><apexchart type="donut" height="260" :options="sourceOptions" :series="sourceSeries" /></div>
                    <div v-else class="mt-8 grid h-48 place-items-center text-sm text-slate-400">Belum ada proyek</div>
                </section>
            </div>

            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-slate-900">Proyek aktif</h2>
                    <p class="mt-1 text-sm text-slate-500">Progress pembayaran, status, dan deadline.</p>
                    </div>
                    <a href="/proyek" class="text-sm font-medium text-slate-700 hover:text-slate-900">Lihat proyek</a>
                </div>
                <div v-if="!activeProjects.length" class="mt-6 rounded-xl border border-dashed border-slate-200 px-6 py-10 text-center text-sm text-slate-500">Belum ada proyek. Tambahkan proyek pertama untuk mulai melacak keuangan.</div>
                <div v-else class="mt-6 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase tracking-wide text-slate-400"><tr><th class="pb-3">Proyek</th><th class="pb-3">Status</th><th class="pb-3">Pembayaran</th><th class="pb-3">Deadline</th></tr></thead>
                        <tbody><tr v-for="project in activeProjects" :key="project.code" class="border-t border-slate-100"><td class="py-3"><span class="font-medium text-slate-900">{{ project.title }}</span><span class="block text-xs text-slate-500">{{ project.client }} · {{ project.code }}</span></td><td>{{ project.status }}</td><td>{{ currency(project.paid) }} / {{ currency(project.net) }}</td><td>{{ project.deadline || '—' }}</td></tr></tbody>
                    </table>
                </div>
            </section>
        </div>
        <div v-if="searchOpen" class="fixed inset-0 z-50 bg-slate-950/30 p-4 pt-[15vh]" @click.self="searchOpen = false">
            <div class="mx-auto max-w-xl rounded-2xl bg-white p-4 shadow-2xl">
                <input v-model="search" autofocus placeholder="Cari proyek atau klien..." class="w-full rounded-lg border-slate-200" />
                <div class="mt-3 max-h-80 overflow-y-auto">
                    <a v-for="item in filteredSearchItems" :key="item.url" :href="item.url" class="block rounded-lg px-3 py-2 hover:bg-slate-50"><span class="block text-sm font-medium text-slate-900">{{ item.label }}</span><span class="text-xs text-slate-500">{{ item.meta }}</span></a>
                    <p v-if="!filteredSearchItems.length" class="p-4 text-center text-sm text-slate-500">Tidak ada hasil.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

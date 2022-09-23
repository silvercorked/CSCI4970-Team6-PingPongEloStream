<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

defineProps({
    user: Object,
    season_number: Number,
    current_game: Object
});
</script>
<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Season #{{ season_number }} Livestream
            </h2>
        </template>

        <div class="py-12 px-12 flex justify-center">
            
        </div>
    </AppLayout>
</template>
        
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

defineProps({
    user: Object,
    teams: {
        type: Array,
        required: true
    },
    season_number: Number
});
</script>
<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Season #{{ season_number }} Leaderboards
            </h2>
        </template>

        <div class="py-12 px-12 flex justify-center">
            <ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
                <li
                    v-for="(team, index) in teams"
                    :class="[index == 0
                        ? ' rounded-t-lg'
                        : (index == teams.length
                            ? ' rounded-b-lg'
                            : ''),
                        'px-6 py-2 border-b border-gray-200 w-full'
                    ]" :key="index"
                >
                    <div class="grid grid-cols-4 gap-4">
                        <span>Team Members</span>
                        <div v-for="(member, index) in team.members" :key="index">
                            <span >
                                {{ member.name }}
                            </span>
                            <div id="img-handle">
                                <div v-show="member.profile_photo_url" class="mt-2">
                                    <img :src="member.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                                </div>

                                <!-- New Profile Photo Preview 
                                <div v-show="photoPreview" class="mt-2">
                                    <span
                                        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                                        :style="'background-image: url(\'' + photoPreview + '\');'"
                                    />
                                </div>-->
                            </div>
                        </div>
                        <div>{{ team.elo }}</div>
                    </div>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
    
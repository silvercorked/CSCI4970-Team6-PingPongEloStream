<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import {InertiaLink} from "@inertiajs/inertia-vue3";
    import Pagination from '@/Components/Custom/Pagination.vue';
    
    defineProps({
        user: Object,
        teams: {
            type: Object,
            required: true
        },
        season_number: Number
    });
    </script>
    <template>
        <AppLayout title="Dashboard">
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Season #{{ season_number }} Leaderboards Doubles
                </h2>
            </template>

            <inertia-link
                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                :class="{ 'bg-white': route().current('leaderboards.singles') }"
                :href="route('leaderboards.singles')"
            >
                Singles Leaderboards
            </inertia-link>

            <div class="py-12 px-12 justify-center">
                <div class="container">
                    <div v-for="team in $page.props.teams.data" :key="team.id">
                        {{ team.elo }}
                        <div v-for="member in team.members" :key="member.id">
                            {{ member.name }}
                        </div>
                    </div>
                    <br/>
                </div>
                <pagination :links="$page.props.teams.links"></pagination>
                <!--<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
                    <li
                        v-for="(team, index) in teams"
                        :class="[index == 0
                            ? ' rounded-t-lg'
                            : (index == teams.length
                                ? ' rounded-b-lg'
                                : ''),
                            'px-6 py-2 border-b border-gray-200 w-full'
                        ]"
                    >
                        <div class="grid grid-cols-4 gap-4">
                            <span>Team Members</span>
                            <div v-for="member in team.members">
                                <span >
                                    {{ member.name }}
                                </span>
                                <div id="img-handle">
                                    <div v-show="member.profile_photo_url" class="mt-2">
                                        <img :src="member.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                                    </div>
    
                                    New Profile Photo Preview 
                                    <div v-show="photoPreview" class="mt-2">
                                        <span
                                            class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                                            :style="'background-image: url(\'' + photoPreview + '\');'"
                                        />
                                    </div>--><!--
                                </div>
                            </div>
                            <div>{{ team.elo }}</div>
                        </div>
                    </li>
                </ul>-->
            </div>
        </AppLayout>
    </template>
        
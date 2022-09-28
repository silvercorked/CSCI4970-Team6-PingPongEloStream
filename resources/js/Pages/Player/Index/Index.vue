<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import Welcome from '@/Components/Welcome.vue';
    
    defineProps({
        user: Object,
        season_number: Number
    });
    </script>
    <template>
        <AppLayout title="Dashboard">
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Players
                </h2>
            </template>
    
            <div class="py-12 px-12 flex justify-center">
                
            </div>
        </AppLayout>
    </template>
            
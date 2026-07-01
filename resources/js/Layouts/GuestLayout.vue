<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <v-app>
        <v-main class="bg-grey-lighten-4">
            <v-container class="fill-height">
                <v-row justify="center">
                    <v-col cols="12" sm="8" md="6" lg="4">
                        <v-card class="pa-6" elevation="4">
                            <div class="text-center mb-6">
                                <v-avatar size="64" class="mb-2" :color="$page.props.app_settings?.school_logo_path ? 'transparent' : 'primary-lighten-1'">
                                    <v-img v-if="$page.props.app_settings?.school_logo_path" :src="$page.props.app_settings.school_logo_path" cover></v-img>
                                    <v-icon v-else size="40" color="white">mdi-school</v-icon>
                                </v-avatar>
                                <h2 class="text-h4 font-weight-bold color-primary mt-2">Admin {{ $page.props.app_settings?.app_name || 'SPMB' }}</h2>
                                <p class="text-subtitle-1 text-grey">{{ $page.props.app_settings?.school_name || 'SMP Bustanul Ulum NU Jatirokeh' }}</p>
                            </div>
                            <slot />
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<style scoped>
.color-primary {
    color: #1B5E20;
}
</style>

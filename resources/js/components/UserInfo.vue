<script setup lang="ts">
import type { User } from '@/types';

type Props = {
    user: User | null;
    showEmail?: boolean;
};

withDefaults(defineProps<Props>(), {
    showEmail: false,
});
</script>

<template>
    <template v-if="user">
        <div class="grid flex-1 text-left text-sm leading-tight">
            <span class="truncate font-medium">{{ user.name }}</span>
            <span v-if="showEmail" class="truncate text-xs text-muted-foreground">{{
                user.email
            }}</span>
        </div>
    </template>
    <template v-else>
        <div class="grid flex-1 text-left text-sm leading-tight">
            <span class="truncate font-medium">Convidado</span>
        </div>
    </template>
</template>

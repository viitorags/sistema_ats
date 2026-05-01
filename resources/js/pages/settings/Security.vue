<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ShieldCheck } from 'lucide-vue-next';
import { onUnmounted, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { edit } from '@/routes/security';
import { update as updatePassword } from '@/routes/user-password';
import { disable, enable } from '@/routes/two-factor';

type Props = {
    canManageTwoFactor?: boolean;
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    canManageTwoFactor: false,
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Security settings',
                href: edit().url,
            },
        ],
    },
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(updatePassword().url, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const enable2FAForm = useForm({});
const disable2FAForm = useForm({});

const enable2FA = () => {
    enable2FAForm.post(enable().url, {
        onSuccess: () => (showSetupModal.value = true),
    });
};

const disable2FA = () => {
    disable2FAForm.delete(disable().url);
};

onUnmounted(() => clearTwoFactorAuthData());
</script>

<template>
    <Head title="Security settings" />

    <h1 class="sr-only">Security settings</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Update password"
            description="Ensure your account is using a long, random password to stay secure"
        />

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-2">
                <Label for="current_password">Current password</Label>
                <PasswordInput
                    id="current_password"
                    v-model="form.current_password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                    placeholder="Current password"
                />
                <InputError :message="form.errors.current_password" />
            </div>

            <div class="grid gap-2">
                <Label for="password">New password</Label>
                <PasswordInput
                    id="password"
                    v-model="form.password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    placeholder="New password"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <PasswordInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    placeholder="Confirm password"
                />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center gap-4">
                <Button
                    :disabled="form.processing"
                    data-test="update-password-button"
                >
                    Save password
                </Button>
            </div>
        </form>
    </div>

    <div v-if="canManageTwoFactor" class="space-y-6">
        <Heading
            variant="small"
            title="Two-factor authentication"
            description="Manage your two-factor authentication settings"
        />

        <div
            v-if="!twoFactorEnabled"
            class="flex flex-col items-start justify-start space-y-4"
        >
            <p class="text-sm text-muted-foreground">
                When you enable two-factor authentication, you will be prompted
                for a secure pin during login. This pin can be retrieved from a
                TOTP-supported application on your phone.
            </p>

            <div>
                <Button v-if="hasSetupData" @click="showSetupModal = true">
                    <ShieldCheck />Continue setup
                </Button>
                <form v-else @submit.prevent="enable2FA">
                    <Button type="submit" :disabled="enable2FAForm.processing">
                        Enable 2FA
                    </Button>
                </form>
            </div>
        </div>

        <div v-else class="flex flex-col items-start justify-start space-y-4">
            <p class="text-sm text-muted-foreground">
                You will be prompted for a secure, random pin during login,
                which you can retrieve from the TOTP-supported application on
                your phone.
            </p>

            <div class="relative inline">
                <form @submit.prevent="disable2FA">
                    <Button
                        variant="destructive"
                        type="submit"
                        :disabled="disable2FAForm.processing"
                    >
                        Disable 2FA
                    </Button>
                </form>
            </div>

            <TwoFactorRecoveryCodes />
        </div>

        <TwoFactorSetupModal
            v-model:isOpen="showSetupModal"
            :requiresConfirmation="requiresConfirmation"
            :twoFactorEnabled="twoFactorEnabled"
        />
    </div>
</template>

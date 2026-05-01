<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, update as updateProfile } from '@/routes/profile';
import { send } from '@/routes/verification';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const page = usePage();
const user = computed(() => page.props.auth.user);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: '/settings/profile',
            },
        ],
    },
});

const form = useForm({
    name: user.value?.name || '',
    email: user.value?.email || '',
    gemini_key: user.value?.gemini_key || '',
});

const submit = () => {
    form.patch(updateProfile().url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile settings</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Profile information"
            description="Update your name, email address and API keys"
        />

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autocomplete="name"
                    placeholder="Full name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="Email address"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="gemini_key">Gemini API Key</Label>
                <Input
                    id="gemini_key"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.gemini_key"
                    placeholder="API Key for AI Analysis"
                />
                <InputError class="mt-2" :message="form.errors.gemini_key" />
                <p class="text-xs text-muted-foreground">Necessária para realizar a análise automática de currículos.</p>
            </div>

            <div v-if="mustVerifyEmail && user && !user.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                    Your email address is unverified.
                    <Link
                        :href="send().url"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        Click here to resend the verification email.
                    </Link>
                </p>

                <div
                    v-if="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing" data-test="update-profile-button"
                    >Save</Button
                >
            </div>
        </form>
    </div>

    <DeleteUser />
</template>

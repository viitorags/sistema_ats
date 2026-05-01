<script setup lang="ts">
import { Head, useForm, usePage, router, Link } from '@inertiajs/vue3';
import {
    Users,
    Briefcase,
    Calendar,
    Search,
    Bell,
    CheckCircle2,
    Clock,
    Plus,
} from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { computed, ref } from 'vue';
import { vacancies_store, talents, analyse } from '@/routes';

const props = defineProps<{
    stats: {
        activeVacancies: number;
        totalCandidates: number;
        interviewsThisWeek: number;
    };
    recentApplications: Array<{
        id: string;
        candidate_name: string;
        category: string;
        created_at: string;
        score: number;
    }>;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const dashboardStats = computed(() => [
    {
        label: 'Vagas Ativas',
        value: props.stats.activeVacancies,
        icon: Briefcase,
        color: 'text-blue-600 dark:text-blue-400',
        bg: 'bg-blue-100 dark:bg-blue-900/30',
    },
    {
        label: 'Total de Candidatos',
        value: props.stats.totalCandidates,
        icon: Users,
        color: 'text-green-600 dark:text-green-400',
        bg: 'bg-green-100 dark:bg-green-900/30',
    },
    {
        label: 'Entrevistas p/ Semana',
        value: props.stats.interviewsThisWeek,
        icon: Calendar,
        color: 'text-purple-600 dark:text-purple-400',
        bg: 'bg-purple-100 dark:bg-purple-900/30',
    },
]);

const getStatusVariant = (score: number) => {
    if (score >= 80) return 'success';
    if (score >= 50) return 'warning';
    return 'secondary';
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
};

// Form for new vacancy
const isDialogOpen = ref(false);
const form = useForm({
    title: '',
    description: '',
    location: '',
    is_remote: false,
    active: true,
    user_id: user.value?.id || '',
});

const submitVacancy = () => {
    form.post(vacancies_store().url, {
        onSuccess: () => {
            isDialogOpen.value = false;
            form.reset();
            router.reload();
        },
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Bem-vindo de volta!
                </h1>
                <p class="text-muted-foreground">
                    Aqui está o resumo do seu recrutamento hoje.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Dialog v-model:open="isDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Nova Vaga
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <form @submit.prevent="submitVacancy">
                            <DialogHeader>
                                <DialogTitle>Criar Nova Vaga</DialogTitle>
                                <DialogDescription>
                                    Preencha os detalhes da nova oportunidade de
                                    emprego.
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid gap-2">
                                    <Label for="title">Título da Vaga</Label>
                                    <Input
                                        id="title"
                                        v-model="form.title"
                                        placeholder="Ex: Senior Frontend Engineer"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.title"
                                        class="text-xs text-destructive"
                                    >
                                        {{ form.errors.title }}
                                    </div>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="location">Localização</Label>
                                    <Input
                                        id="location"
                                        v-model="form.location"
                                        placeholder="Ex: São Paulo, SP"
                                    />
                                    <div
                                        v-if="form.errors.location"
                                        class="text-xs text-destructive"
                                    >
                                        {{ form.errors.location }}
                                    </div>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="description"
                                        >Descrição (Opcional)</Label
                                    >
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                        placeholder="Descreva as responsabilidades e requisitos..."
                                    ></textarea>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox
                                        id="is_remote"
                                        :checked="form.is_remote"
                                        @update:checked="
                                            (val) => (form.is_remote = val)
                                        "
                                    />
                                    <Label for="is_remote">Vaga Remota</Label>
                                </div>
                            </div>
                            <DialogFooter>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    {{
                                        form.processing
                                            ? 'Salvando...'
                                            : 'Salvar Vaga'
                                    }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="stat in dashboardStats" :key="stat.label">
                <CardHeader
                    class="flex flex-row items-center justify-between space-y-0 pb-2"
                >
                    <CardTitle class="text-sm font-medium">
                        {{ stat.label }}
                    </CardTitle>
                    <div :class="['rounded-md p-2', stat.bg]">
                        <component
                            :is="stat.icon"
                            :class="['h-4 w-4', stat.color]"
                        />
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stat.value }}</div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-6 md:grid-cols-7">
            <Card class="md:col-span-4">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Candidaturas Recentes</CardTitle>
                            <CardDescription>
                                Últimos candidatos processados no sistema.
                            </CardDescription>
                        </div>
                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="talents().url">Ver todos</Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="space-y-6">
                        <div
                            v-for="app in recentApplications"
                            :key="app.id"
                            class="flex items-center justify-between"
                        >
                            <div class="flex items-center gap-4">
                                <div>
                                    <p class="text-sm leading-none font-medium">
                                        {{ app.candidate_name }}
                                    </p>
                                    <p
                                        class="mt-1 text-xs text-muted-foreground"
                                    >
                                        {{ app.category || 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <Badge :variant="getStatusVariant(app.score)">
                                    {{ app.score }}% Match
                                </Badge>
                                <span
                                    class="text-[10px] text-muted-foreground"
                                    >{{ formatDate(app.created_at) }}</span
                                >
                            </div>
                        </div>
                        <div
                            v-if="recentApplications.length === 0"
                            class="py-4 text-center text-muted-foreground"
                        >
                            Nenhuma candidatura recente encontrada.
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="flex flex-col gap-6 md:col-span-3">
                <Card class="border-primary/20 bg-primary/5">
                    <CardHeader class="pb-2">
                        <div class="flex items-center gap-2">
                            <div
                                class="rounded-full bg-primary p-1 text-primary-foreground"
                            >
                                <CheckCircle2 class="h-3 w-3" />
                            </div>
                            <CardTitle class="text-sm"
                                >Sugestão da IA</CardTitle
                            >
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm">
                            O sistema está pronto para processar novos
                            currículos e identificar os melhores talentos para
                            suas vagas ativas.
                        </p>
                        <Button
                            variant="link"
                            class="mt-2 h-auto px-0 text-xs font-semibold text-primary"
                            as-child
                        >
                            <Link :href="analyse().url">
                                Analisar currículos →
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import {
    Sparkles,
    Zap,
    BrainCircuit,
    Upload,
    AlertCircle,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { computed, ref } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { talents_store, talents } from '@/routes';

const props = defineProps<{
    recentResumes: Array<{
        id: string;
        candidate_name: string;
        candidate_email: string;
        score: number;
        technical_score: number;
        match_score: number;
        summary: string;
        category: string;
    }>;
}>();

const selectedResume = computed(() => props.recentResumes?.[0] || null);

const isAnalyzeDialogOpen = ref(false);

const form = useForm({
    file: null as File | null,
    category: '',
});

function submit() {
    form.post(talents_store().url, {
        onSuccess: () => {
            isAnalyzeDialogOpen.value = false;
            form.reset();
            router.reload();
        },
    });
}
</script>

<template>
    <Head title="Análise AI" />

    <div class="flex flex-col gap-6 p-6">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div>
                <div class="mb-1 flex items-center gap-2">
                    <Badge
                        variant="outline"
                        class="border-primary/20 bg-primary/5 text-primary"
                    >
                        <Sparkles class="mr-1 h-3 w-3" />
                        AI Powered
                    </Badge>
                </div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Análise de Candidatos
                </h1>
                <p class="text-muted-foreground">
                    Utilize nossa IA para identificar os melhores matches para
                    suas vagas.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Dialog v-model:open="isAnalyzeDialogOpen">
                    <DialogTrigger as-child>
                        <Button
                            class="bg-gradient-to-r from-indigo-600 to-primary hover:from-indigo-700 hover:to-primary/90"
                        >
                            <Zap class="mr-2 h-4 w-4 fill-current" />
                            Nova Análise
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <form @submit.prevent="submit">
                            <DialogHeader>
                                <DialogTitle
                                    >Analisar Novo Currículo</DialogTitle
                                >
                                <DialogDescription>
                                    Faça o upload de um currículo para que a IA
                                    possa processá-lo e extrair as informações.
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid gap-2">
                                    <Label for="category">Vaga Alvo</Label>
                                    <Input
                                        id="category"
                                        v-model="form.category"
                                        placeholder="Ex: Engenheiro de Software Sênior"
                                    />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="file"
                                        >Arquivo do Currículo</Label
                                    >
                                    <Input
                                        id="file"
                                        type="file"
                                        @input="
                                            form.file =
                                                (
                                                    $event.target as HTMLInputElement
                                                ).files?.[0] || null
                                        "
                                    />
                                    <progress
                                        v-if="form.progress"
                                        :value="form.progress.percentage"
                                        max="100"
                                    >
                                        {{ form.progress.percentage }}%
                                    </progress>
                                </div>
                            </div>
                            <DialogFooter>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    <Upload class="mr-2 h-4 w-4" />
                                    {{
                                        form.processing
                                            ? 'Analisando...'
                                            : 'Analisar'
                                    }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>

        <div v-if="selectedResume" class="grid gap-6 lg:grid-cols-12">
            <Card class="overflow-hidden lg:col-span-8">
                <div
                    class="h-2 bg-gradient-to-r from-indigo-500 via-primary to-purple-500"
                ></div>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div>
                                <CardTitle>{{
                                    selectedResume.candidate_name
                                }}</CardTitle>
                                <CardDescription
                                    >Categoria:
                                    <strong>{{
                                        selectedResume.category || 'N/A'
                                    }}</strong></CardDescription
                                >
                            </div>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="text-4xl font-black text-primary"
                                >{{ selectedResume.score }}%</span
                            >
                            <span
                                class="text-[10px] font-bold tracking-wider text-muted-foreground uppercase"
                                >Overall Score</span
                            >
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-8">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span class="font-medium">Technical Score</span>
                                <span class="text-muted-foreground"
                                    >{{ selectedResume.technical_score }}%</span
                                >
                            </div>
                            <div
                                class="h-2 w-full overflow-hidden rounded-full bg-secondary"
                            >
                                <div
                                    class="h-full bg-blue-500"
                                    :style="{
                                        width: `${selectedResume.technical_score}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span class="font-medium">Job Match</span>
                                <span class="text-muted-foreground"
                                    >{{ selectedResume.match_score }}%</span
                                >
                            </div>
                            <div
                                class="h-2 w-full overflow-hidden rounded-full bg-secondary"
                            >
                                <div
                                    class="h-full bg-green-500"
                                    :style="{
                                        width: `${selectedResume.match_score}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <h3
                            class="flex items-center gap-2 text-sm font-semibold"
                        >
                            <BrainCircuit class="h-4 w-4 text-primary" />
                            Sumário da IA
                        </h3>
                        <div
                            class="rounded-xl border bg-muted p-6 text-sm leading-relaxed text-foreground/80 italic"
                        >
                            "{{
                                selectedResume.summary ||
                                'Nenhum sumário gerado para este candidato.'
                            }}"
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-2 rounded-lg border border-primary/10 bg-primary/5 p-3 text-xs text-muted-foreground"
                    >
                        <AlertCircle class="h-4 w-4 text-primary" />
                        Esta análise é gerada automaticamente e deve ser usada
                        como apoio à decisão humana.
                    </div>
                </CardContent>
            </Card>

            <div class="flex flex-col gap-6 lg:col-span-4">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg"
                            >Processados Recentemente</CardTitle
                        >
                        <CardDescription
                            >Clique para ver o relatório
                            detalhado</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="resume in recentResumes"
                                :key="resume.id"
                                class="flex cursor-pointer items-center justify-between rounded-lg border border-transparent p-2 transition-colors hover:border-border hover:bg-muted"
                            >
                                <div
                                    class="flex items-center gap-3 overflow-hidden"
                                >
                                    <div class="overflow-hidden">
                                        <p class="truncate text-xs font-medium">
                                            {{ resume.candidate_name }}
                                        </p>
                                        <p
                                            class="truncate text-[10px] text-muted-foreground"
                                        >
                                            {{ resume.category || 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <Badge variant="secondary" class="text-[10px]"
                                    >{{ resume.score }}%</Badge
                                >
                            </div>
                            <Button
                                variant="outline"
                                class="mt-2 w-full"
                                size="sm"
                                as-child
                            >
                                <Link :href="talents().url">
                                    Ver todos os currículos
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card
                    class="border-none bg-indigo-600 text-white shadow-lg shadow-indigo-500/20"
                >
                    <CardHeader>
                        <CardTitle class="text-white">Dica da IA</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <p class="text-xs leading-relaxed text-indigo-100">
                            Candidatos com Technical Score acima de 80%
                            geralmente performam melhor em testes práticos.
                            Considere pular o screening inicial para estes
                            perfis.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
        <div
            v-else
            class="rounded-xl border-2 border-dashed bg-muted/20 py-20 text-center"
        >
            <Sparkles
                class="mx-auto mb-4 h-10 w-10 text-muted-foreground opacity-20"
            />
            <p class="text-muted-foreground">
                Nenhuma análise disponível. Processe alguns currículos para
                começar.
            </p>
        </div>
    </div>
</template>

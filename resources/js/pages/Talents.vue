<script setup lang="ts">
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { 
    Search, 
    MoreVertical, 
    Mail, 
    Phone, 
    Star,
    ArrowUpRight,
    Plus,
    FileUp,
    UserPlus,
    Trash2,
    Eye,
    MessageSquare
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { computed, ref } from 'vue';
import { talents_store, talents_destroy } from '@/routes';

const props = defineProps<{
    talents: Array<{
        id: string;
        candidate_name: string;
        candidate_email: string;
        candidate_phone: string;
        score: number;
        category: string;
        skills: any;
        summary: string;
    }>;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const getStatusVariant = (score: number) => {
    if (score >= 80) return 'success';
    if (score >= 50) return 'info';
    return 'secondary';
};

const parseSkills = (skills: any) => {
    if (!skills) return [];
    if (Array.isArray(skills)) return skills;
    if (typeof skills === 'string') {
        try {
            const parsed = JSON.parse(skills);
            return Array.isArray(parsed) ? parsed : skills.split(',').map(s => s.trim());
        } catch (e) {
            return skills.split(',').map(s => s.trim());
        }
    }
    return [];
};

// Modals
const isManualDialogOpen = ref(false);
const isUploadDialogOpen = ref(false);
const isViewDialogOpen = ref(false);
const selectedTalent = ref<any>(null);

// Forms
const manualForm = useForm({
    candidate_name: '',
    candidate_email: '',
    candidate_phone: '',
    category: 'Tecnologia',
    filename: 'manual_entry',
    user_id: user.value?.id || '',
});

const uploadForm = useForm({
    file: null as File | null,
    category: 'Tecnologia',
    user_id: user.value?.id || '',
});

const openViewModal = (talent: any) => {
    selectedTalent.value = talent;
    isViewDialogOpen.value = true;
};

const submitManual = () => {
    manualForm.post(talents_store().url, {
        onSuccess: () => {
            isManualDialogOpen.value = false;
            manualForm.reset();
            router.reload();
        },
    });
};

const submitUpload = () => {
    uploadForm.post(talents_store().url, {
        onSuccess: () => {
            isUploadDialogOpen.value = false;
            uploadForm.reset();
            router.reload();
        },
    });
};

const deleteTalent = (id: string) => {
    if (confirm('Deseja excluir este candidato permanentemente?')) {
        router.delete(talents_destroy(id).url);
    }
};

const onFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files?.length) {
        uploadForm.file = target.files[0];
    }
};
</script>

<template>
    <Head title="Talentos" />

    <div class="flex flex-col gap-6 p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Banco de Talentos</h1>
                <p class="text-muted-foreground">Visualize e gerencie todos os candidatos processados.</p>
            </div>
            <div class="flex items-center gap-2">
                <Dialog v-model:open="isUploadDialogOpen">
                    <DialogTrigger as-child>
                        <Button variant="outline">
                            <FileUp class="mr-2 h-4 w-4" />
                            Analisar com IA
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <form @submit.prevent="submitUpload">
                            <DialogHeader>
                                <DialogTitle>Upload de Currículo</DialogTitle>
                                <DialogDescription>
                                    Nossa IA extrairá as informações automaticamente.
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid gap-2">
                                    <Label for="file">Arquivo (PDF ou DOCX)</Label>
                                    <Input id="file" type="file" accept=".pdf,.docx" @change="onFileChange" required />
                                    <div v-if="uploadForm.errors.file" class="text-xs text-destructive">{{ uploadForm.errors.file }}</div>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="up_category">Área Sugerida</Label>
                                    <Input id="up_category" v-model="uploadForm.category" placeholder="Ex: Tecnologia" />
                                </div>
                            </div>
                            <DialogFooter>
                                <Button type="submit" :disabled="uploadForm.processing">
                                    {{ uploadForm.processing ? 'Analisando...' : 'Iniciar Análise' }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <Dialog v-model:open="isManualDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <UserPlus class="mr-2 h-4 w-4" />
                            Novo Candidato
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <form @submit.prevent="submitManual">
                            <DialogHeader>
                                <DialogTitle>Cadastro Manual</DialogTitle>
                                <DialogDescription>
                                    Insira as informações básicas do candidato.
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid gap-2">
                                    <Label for="name">Nome Completo</Label>
                                    <Input id="name" v-model="manualForm.candidate_name" placeholder="Ex: João Silva" required />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="email">E-mail</Label>
                                    <Input id="email" type="email" v-model="manualForm.candidate_email" placeholder="joao@exemplo.com" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="phone">Telefone</Label>
                                    <Input id="phone" v-model="manualForm.candidate_phone" placeholder="+55 (11) 99999-9999" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="category">Categoria/Área</Label>
                                    <Input id="category" v-model="manualForm.category" placeholder="Ex: Tecnologia" />
                                </div>
                            </div>
                            <DialogFooter>
                                <Button type="submit" :disabled="manualForm.processing">
                                    {{ manualForm.processing ? 'Salvando...' : 'Salvar Candidato' }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>

        <!-- View Dialog -->
        <Dialog v-model:open="isViewDialogOpen">
            <DialogContent class="sm:max-w-[600px] max-h-[80vh] overflow-y-auto">
                <DialogHeader v-if="selectedTalent">
                    <div class="flex items-center justify-between">
                        <Badge :variant="getStatusVariant(selectedTalent.score)">
                            {{ selectedTalent.score }}% Match
                        </Badge>
                        <span class="text-xs text-muted-foreground">{{ selectedTalent.category }}</span>
                    </div>
                    <DialogTitle class="text-2xl mt-2">{{ selectedTalent.candidate_name }}</DialogTitle>
                    <DialogDescription class="flex flex-col gap-1 mt-1">
                        <span class="flex items-center gap-1.5"><Mail class="h-3 w-3" /> {{ selectedTalent.candidate_email }}</span>
                        <span class="flex items-center gap-1.5"><Phone class="h-3 w-3" /> {{ selectedTalent.candidate_phone }}</span>
                    </DialogDescription>
                </DialogHeader>
                
                <div v-if="selectedTalent" class="space-y-6 py-6 border-t mt-4">
                    <div>
                        <h4 class="text-sm font-semibold mb-3 flex items-center gap-2">
                            <Star class="h-4 w-4 text-amber-500" /> Habilidades
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            <Badge v-for="skill in parseSkills(selectedTalent.skills)" :key="skill" variant="secondary">
                                {{ skill }}
                            </Badge>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold mb-2 flex items-center gap-2">
                            <Eye class="h-4 w-4 text-primary" /> Sumário da IA
                        </h4>
                        <p class="text-sm text-muted-foreground leading-relaxed italic bg-muted/30 p-4 rounded-lg border">
                            {{ selectedTalent.summary || 'Nenhuma análise detalhada disponível.' }}
                        </p>
                    </div>
                </div>

                <DialogFooter class="flex gap-2">
                    <Button variant="outline" class="flex-1" @click="deleteTalent(selectedTalent.id)">
                        <Trash2 class="h-4 w-4 mr-2" /> Excluir
                    </Button>
                    <Button class="flex-1" as-child>
                        <a :href="'mailto:' + selectedTalent.candidate_email">
                            <MessageSquare class="h-4 w-4 mr-2" /> Contatar
                        </a>
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between">
            <div class="relative w-full md:w-96">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input placeholder="Buscar por nome ou categoria..." class="pl-10" />
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <Card v-for="talent in talents" :key="talent.id" class="group overflow-hidden hover:border-primary/50 transition-colors flex flex-col">
                <CardHeader class="relative pb-2">
                    <div class="absolute right-4 top-4">
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground">
                                    <MoreVertical class="h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem @click="openViewModal(talent)">
                                    <Eye class="h-4 w-4 mr-2" /> Ver Perfil
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                    <MessageSquare class="h-4 w-4 mr-2" /> Enviar Mensagem
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem class="text-destructive" @click="deleteTalent(talent.id)">
                                    <Trash2 class="h-4 w-4 mr-2" /> Excluir
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                    <div class="flex flex-col items-center gap-2 text-center pt-4">
                        <CardTitle class="text-lg leading-tight">{{ talent.candidate_name }}</CardTitle>
                        <CardDescription class="text-xs font-medium uppercase tracking-wider">{{ talent.category || 'Geral' }}</CardDescription>
                    </div>
                </CardHeader>
                <CardContent class="pt-2 flex-1 flex flex-col">
                    <div class="flex flex-col gap-4 flex-1">
                        <div class="flex items-center justify-center gap-1 text-amber-500">
                            <Star class="h-4 w-4 fill-current" />
                            <span class="text-sm font-bold text-foreground">{{ talent.score }}% Match</span>
                        </div>
                        
                        <div class="flex flex-wrap justify-center gap-1 min-h-[40px]">
                            <Badge v-for="tag in parseSkills(talent.skills).slice(0, 3)" :key="tag" variant="outline" class="text-[10px] px-1.5 py-0">
                                {{ tag }}
                            </Badge>
                            <span v-if="parseSkills(talent.skills).length > 3" class="text-[10px] text-muted-foreground self-center">
                                +{{ parseSkills(talent.skills).length - 3 }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-2 rounded-lg bg-muted/50 p-3">
                            <div class="flex items-center gap-2 text-[11px] text-muted-foreground overflow-hidden">
                                <Mail class="h-3 w-3 shrink-0" />
                                <span class="truncate">{{ talent.candidate_email }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-auto pt-4 border-t">
                            <Badge :variant="getStatusVariant(talent.score)" class="text-[10px]">
                                {{ talent.score >= 50 ? 'Qualificado' : 'Análise' }}
                            </Badge>
                            <Button variant="ghost" size="sm" class="h-8 px-2 text-xs" @click="openViewModal(talent)">
                                Perfil
                                <ArrowUpRight class="ml-1 h-3 w-3" />
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
        <div v-if="talents.length === 0" class="text-center py-20 bg-muted/20 rounded-xl border-2 border-dashed">
            <p class="text-muted-foreground">Nenhum talento encontrado no banco de dados.</p>
        </div>
    </div>
</template>

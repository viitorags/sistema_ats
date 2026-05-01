<script setup lang="ts">
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { 
    Calendar as CalendarIcon, 
    Clock, 
    Video, 
    MapPin, 
    MoreHorizontal,
    Plus,
    Link as LinkIcon,
    Trash2,
    Eye,
    Pencil
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { computed, ref } from 'vue';
import { interviews_store, interviews_update, interviews_destroy } from '@/routes';

const props = defineProps<{
    interviews: Array<{
        id: string;
        summary: string;
        description: string;
        location: string;
        start_time: string;
        end_time: string;
        event_link: string;
        status: string;
    }>;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const formatDate = (dateString: string) => {
    if (!dateString) return '---';
    return new Date(dateString).toLocaleDateString('pt-BR');
};

const formatTimeRange = (start: string, end: string) => {
    if (!start || !end) return '---';
    const s = new Date(start).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    const e = new Date(end).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    return `${s} - ${e}`;
};

const getStatusVariant = (status: string) => {
    if (!status) return 'secondary';
    const s = status.toLowerCase();
    if (s.includes('confirm') || s.includes('conclu')) return 'success';
    if (s.includes('pend') || s.includes('aguar')) return 'warning';
    if (s.includes('cancel')) return 'destructive';
    return 'secondary';
};

// Modals State
const isCreateDialogOpen = ref(false);
const isEditDialogOpen = ref(false);
const selectedInterview = ref<any>(null);

// Forms
const form = useForm({
    summary: '',
    description: '',
    location: '',
    start_time: '',
    end_time: '',
    event_link: '',
    status: 'Pendente',
    user_id: user.value?.id || '',
});

const editForm = useForm({
    summary: '',
    description: '',
    location: '',
    start_time: '',
    end_time: '',
    event_link: '',
    status: 'Pendente',
});

const openEditModal = (interview: any) => {
    selectedInterview.value = interview;
    editForm.summary = interview.summary;
    editForm.description = interview.description || '';
    editForm.location = interview.location || '';
    
    if (interview.start_time) {
        editForm.start_time = new Date(interview.start_time).toISOString().slice(0, 16);
    }
    if (interview.end_time) {
        editForm.end_time = new Date(interview.end_time).toISOString().slice(0, 16);
    }
    
    editForm.event_link = interview.event_link || '';
    editForm.status = interview.status || 'Pendente';
    isEditDialogOpen.value = true;
};

const submitInterview = () => {
    form.post(interviews_store().url, {
        onSuccess: () => {
            isCreateDialogOpen.value = false;
            form.reset();
            router.reload();
        },
    });
};

const updateInterview = () => {
    if (!selectedInterview.value) return;
    editForm.put(interviews_update(selectedInterview.value.id).url, {
        onSuccess: () => {
            isEditDialogOpen.value = false;
            router.reload();
        },
    });
};

const deleteInterview = (id: string) => {
    if (confirm('Deseja cancelar e excluir este agendamento?')) {
        router.delete(interviews_destroy(id).url);
    }
};
</script>

<template>
    <Head title="Entrevistas" />

    <div class="flex flex-col gap-6 p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Entrevistas</h1>
                <p class="text-muted-foreground">Gerencie sua agenda de conversas com os candidatos.</p>
            </div>
            <div class="flex items-center gap-2">
                <Dialog v-model:open="isCreateDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Agendar
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <form @submit.prevent="submitInterview">
                            <DialogHeader>
                                <DialogTitle>Agendar Entrevista</DialogTitle>
                                <DialogDescription>Defina os detalhes da conversa.</DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid gap-2">
                                    <Label for="summary">Título/Candidato</Label>
                                    <Input id="summary" v-model="form.summary" required />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="grid gap-2">
                                        <Label for="start_time">Início</Label>
                                        <Input id="start_time" type="datetime-local" v-model="form.start_time" required />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="end_time">Fim</Label>
                                        <Input id="end_time" type="datetime-local" v-model="form.end_time" required />
                                    </div>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="location">Local</Label>
                                    <Input id="location" v-model="form.location" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="event_link">Link</Label>
                                    <Input id="event_link" type="url" v-model="form.event_link" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="status">Status</Label>
                                    <Select v-model="form.status">
                                        <SelectTrigger><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Pendente">Pendente</SelectItem>
                                            <SelectItem value="Confirmada">Confirmada</SelectItem>
                                            <SelectItem value="Cancelada">Cancelada</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <DialogFooter>
                                <Button type="submit" :disabled="form.processing">Agendar</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>

        <Dialog v-model:open="isEditDialogOpen">
            <DialogContent class="sm:max-w-[425px]">
                <form @submit.prevent="updateInterview">
                    <DialogHeader>
                        <DialogTitle>Editar Agendamento</DialogTitle>
                        <DialogDescription>Altere as informações da entrevista.</DialogDescription>
                    </DialogHeader>
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="edit_summary">Título</Label>
                            <Input id="edit_summary" v-model="editForm.summary" required />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="edit_start">Início</Label>
                                <Input id="edit_start" type="datetime-local" v-model="editForm.start_time" required />
                            </div>
                            <div class="grid gap-2">
                                <Label for="edit_end">Fim</Label>
                                <Input id="edit_end" type="datetime-local" v-model="editForm.end_time" required />
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Label for="edit_location">Local</Label>
                            <Input id="edit_location" v-model="editForm.location" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="edit_link">Link</Label>
                            <Input id="edit_link" type="url" v-model="editForm.event_link" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="edit_status">Status</Label>
                            <Select v-model="editForm.status">
                                <SelectTrigger><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Pendente">Pendente</SelectItem>
                                    <SelectItem value="Confirmada">Confirmada</SelectItem>
                                    <SelectItem value="Cancelada">Cancelada</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="submit" :disabled="editForm.processing">Salvar Alterações</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <div class="grid gap-6">
            <Card>
                <CardHeader>
                    <CardTitle>Agenda de Entrevistas</CardTitle>
                    <CardDescription>Você tem {{ interviews.length }} entrevistas programadas.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="interview in interviews" :key="interview.id" class="flex flex-col md:flex-row md:items-center gap-4 p-4 rounded-xl border hover:bg-muted/30 transition-colors">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="rounded-full bg-primary/10 p-3 text-primary">
                                    <CalendarIcon class="h-6 w-6" />
                                </div>
                                <div>
                                    <p class="font-semibold">{{ interview.summary }}</p>
                                    <p class="text-xs text-muted-foreground line-clamp-1">{{ interview.description }}</p>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-4 flex-[2]">
                                <div class="flex flex-col gap-1 min-w-[120px]">
                                    <div class="flex items-center gap-1.5 text-xs text-muted-foreground font-medium">
                                        <CalendarIcon class="h-3.5 w-3.5" />
                                        {{ formatDate(interview.start_time) }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <Clock class="h-3.5 w-3.5" />
                                        {{ formatTimeRange(interview.start_time, interview.end_time) }}
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5 text-xs text-muted-foreground font-medium">
                                        <MapPin class="h-3.5 w-3.5" />
                                        {{ interview.location }}
                                    </div>
                                    <div class="text-xs font-bold truncate max-w-[200px]">
                                        <a v-if="interview.event_link" :href="interview.event_link" target="_blank" class="text-primary hover:underline flex items-center gap-1">
                                            <LinkIcon class="h-3 w-3" />
                                            Link da Reunião
                                        </a>
                                        <span v-else>Local não definido</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between md:justify-end gap-2 min-w-[140px]">
                                <Badge :variant="getStatusVariant(interview.status)">{{ interview.status }}</Badge>
                                <div class="flex gap-1">
                                    <Button variant="ghost" size="icon" class="h-8 w-8" @click="openEditModal(interview)">
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button variant="ghost" size="icon" class="h-8 w-8 text-destructive hover:text-destructive" @click="deleteInterview(interview.id)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div v-if="interviews.length === 0" class="text-center py-10 text-muted-foreground italic">
                            Nenhuma entrevista agendada.
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

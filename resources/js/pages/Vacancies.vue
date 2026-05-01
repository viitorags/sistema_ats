<script setup lang="ts">
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import {
    Plus,
    MoreVertical,
    MapPin,
    Eye,
    Pencil,
    Trash2,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
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
import { Checkbox } from '@/components/ui/checkbox';
import { computed, ref } from 'vue';
import { vacancies_store, vacancies_update, vacancies_destroy } from '@/routes';

const props = defineProps<{
    vacancies: Array<{
        id: string;
        title: string;
        description: string;
        location: string;
        is_remote: boolean;
        active: boolean;
        created_at: string;
    }>;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
};

// Modals State
const isCreateDialogOpen = ref(false);
const isEditDialogOpen = ref(false);
const isViewDialogOpen = ref(false);
const selectedVacancy = ref<any>(null);

// Forms
const form = useForm({
    title: '',
    description: '',
    location: '',
    is_remote: false,
    active: true,
    user_id: user.value?.id || '',
});

const editForm = useForm({
    title: '',
    description: '',
    location: '',
    is_remote: false,
    active: true,
});

const openEditModal = (vacancy: any) => {
    selectedVacancy.value = vacancy;
    editForm.title = vacancy.title;
    editForm.description = vacancy.description || '';
    editForm.location = vacancy.location || '';
    editForm.is_remote = !!vacancy.is_remote;
    editForm.active = !!vacancy.active;
    isEditDialogOpen.value = true;
};

const openViewModal = (vacancy: any) => {
    selectedVacancy.value = vacancy;
    isViewDialogOpen.value = true;
};

const submitVacancy = () => {
    form.post(vacancies_store().url, {
        onSuccess: () => {
            isCreateDialogOpen.value = false;
            form.reset();
            router.reload();
        },
    });
};

const updateVacancy = () => {
    if (!selectedVacancy.value) return;
    editForm.put(vacancies_update(selectedVacancy.value.id).url, {
        onSuccess: () => {
            isEditDialogOpen.value = false;
            router.reload();
        },
    });
};

const deleteVacancy = (id: string) => {
    if (confirm('Tem certeza que deseja excluir esta vaga?')) {
        router.delete(vacancies_destroy(id).url);
    }
};
</script>

<template>
    <Head title="Vagas" />

    <div class="flex flex-col gap-6 p-6">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Vagas</h1>
                <p class="text-muted-foreground">
                    Gerencie suas oportunidades e acompanhe o fluxo de
                    candidatos.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Dialog v-model:open="isCreateDialogOpen">
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
                                </div>
                                <div class="grid gap-2">
                                    <Label for="location">Localização</Label>
                                    <Input
                                        id="location"
                                        v-model="form.location"
                                        placeholder="Ex: São Paulo, SP"
                                    />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="description">Descrição</Label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                        placeholder="Descreva as responsabilidades..."
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

        <!-- Edit Dialog -->
        <Dialog v-model:open="isEditDialogOpen">
            <DialogContent class="sm:max-w-[425px]">
                <form @submit.prevent="updateVacancy">
                    <DialogHeader>
                        <DialogTitle>Editar Vaga</DialogTitle>
                        <DialogDescription
                            >Atualize as informações da vaga
                            selecionada.</DialogDescription
                        >
                    </DialogHeader>
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="edit_title">Título da Vaga</Label>
                            <Input
                                id="edit_title"
                                v-model="editForm.title"
                                required
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="edit_location">Localização</Label>
                            <Input
                                id="edit_location"
                                v-model="editForm.location"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="edit_description">Descrição</Label>
                            <textarea
                                id="edit_description"
                                v-model="editForm.description"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2 focus-visible:outline-none"
                            ></textarea>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="edit_is_remote"
                                :checked="editForm.is_remote"
                                @update:checked="
                                    (val) => (editForm.is_remote = val)
                                "
                            />
                            <Label for="edit_is_remote">Vaga Remota</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="edit_active"
                                :checked="editForm.active"
                                @update:checked="
                                    (val) => (editForm.active = val)
                                "
                            />
                            <Label for="edit_active">Vaga Ativa</Label>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="submit" :disabled="editForm.processing"
                            >Salvar Alterações</Button
                        >
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- View Dialog -->
        <Dialog v-model:open="isViewDialogOpen">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader v-if="selectedVacancy">
                    <div class="mb-2 flex items-center gap-2">
                        <Badge
                            :variant="
                                selectedVacancy.active ? 'success' : 'secondary'
                            "
                        >
                            {{ selectedVacancy.active ? 'Ativa' : 'Inativa' }}
                        </Badge>
                        <Badge
                            v-if="selectedVacancy.is_remote"
                            variant="outline"
                            >Remoto</Badge
                        >
                    </div>
                    <DialogTitle class="text-2xl">{{
                        selectedVacancy.title
                    }}</DialogTitle>
                    <DialogDescription class="flex items-center gap-1">
                        <MapPin class="h-3 w-3" />
                        {{ selectedVacancy.location }} • Criada em
                        {{ formatDate(selectedVacancy.created_at) }}
                    </DialogDescription>
                </DialogHeader>
                <div v-if="selectedVacancy" class="mt-4 border-t py-4">
                    <h4 class="mb-2 text-sm font-semibold">Descrição</h4>
                    <p
                        class="text-sm leading-relaxed whitespace-pre-wrap text-muted-foreground"
                    >
                        {{
                            selectedVacancy.description ||
                            'Sem descrição informada.'
                        }}
                    </p>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="isViewDialogOpen = false"
                        >Fechar</Button
                    >
                    <Button
                        @click="
                            () => {
                                isViewDialogOpen = false;
                                openEditModal(selectedVacancy);
                            }
                        "
                        >Editar</Button
                    >
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Card>
            <CardHeader class="pb-3">
                <div
                    class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
                ></div>
            </CardHeader>
            <CardContent>
                <div class="overflow-hidden rounded-md border">
                    <table class="w-full text-sm">
                        <thead
                            class="border-b bg-muted/50 font-medium text-muted-foreground"
                        >
                            <tr>
                                <th
                                    class="h-12 px-4 text-left align-middle text-[10px] font-bold tracking-wider uppercase"
                                >
                                    Título
                                </th>
                                <th
                                    class="h-12 px-4 text-left align-middle text-[10px] font-bold tracking-wider uppercase"
                                >
                                    Localização
                                </th>
                                <th
                                    class="h-12 px-4 text-left align-middle text-[10px] font-bold tracking-wider uppercase"
                                >
                                    Status
                                </th>
                                <th
                                    class="h-12 px-4 text-left align-middle text-[10px] font-bold tracking-wider uppercase"
                                >
                                    Data
                                </th>
                                <th
                                    class="h-12 px-4 text-right align-middle text-[10px] font-bold tracking-wider uppercase"
                                >
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="vacancy in vacancies"
                                :key="vacancy.id"
                                class="group transition-colors hover:bg-muted/30"
                            >
                                <td class="p-4 align-middle">
                                    <div class="font-semibold text-primary/90">
                                        {{ vacancy.title }}
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    <div
                                        class="flex items-center gap-1 text-xs"
                                    >
                                        <MapPin
                                            class="h-3 w-3 text-muted-foreground"
                                        />
                                        {{ vacancy.location }}
                                        <Badge
                                            v-if="vacancy.is_remote"
                                            variant="outline"
                                            class="ml-2 h-4 px-1 py-0 text-[10px]"
                                            >Remoto</Badge
                                        >
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    <Badge
                                        :variant="
                                            vacancy.active
                                                ? 'success'
                                                : 'secondary'
                                        "
                                        class="px-1.5 py-0 text-[10px]"
                                    >
                                        {{
                                            vacancy.active ? 'Ativa' : 'Inativa'
                                        }}
                                    </Badge>
                                </td>
                                <td
                                    class="p-4 align-middle text-xs whitespace-nowrap text-muted-foreground"
                                >
                                    {{ formatDate(vacancy.created_at) }}
                                </td>
                                <td class="p-4 text-right align-middle">
                                    <div class="flex justify-end gap-1">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-muted-foreground hover:text-primary"
                                            @click="openViewModal(vacancy)"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-muted-foreground hover:text-primary"
                                            @click="openEditModal(vacancy)"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-muted-foreground hover:text-destructive"
                                            @click="deleteVacancy(vacancy.id)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="vacancies.length === 0">
                                <td
                                    colspan="5"
                                    class="p-12 text-center text-muted-foreground italic"
                                >
                                    Nenhuma vaga cadastrada. Clique em "Nova
                                    Vaga" para começar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

import { useMutation, useQueryClient } from "@tanstack/react-query";
import useUserUIStore from "../stores/useUserUIStore";
import userApi from "../../api/users/user.api";
import { toaster } from "@/components/ui/toaster";

const useDeleteUser = () => {
    const { 
        isDeleteDialogOpen, 
        selectedUserForDelete, 
        openDeleteDialog, 
        closeDeleteDialog 
    } = useUserUIStore();
    const queryClient = useQueryClient();

    const deleteMutation = useMutation({
        mutationFn: async (id) => {
            const res = await userApi.deleteUser(id);
            if (!res.success) {
                throw new Error(res.message || "Failed to delete user");
            }
            return res.data;
        },
        onSuccess: () => {
            toaster.create({
                title: "Success",
                description: "User deleted successfully.",
                type: "success",
            });
            queryClient.invalidateQueries({ queryKey: ["users"] });
            closeDeleteDialog();
        },
        onError: (error) => {
            toaster.create({
                title: "Error",
                description: error.message || "Failed to delete user.",
                type: "error",
            });
        }
    });

    const handleDelete = async () => {
        if (!selectedUserForDelete?.id) return;
        deleteMutation.mutate(selectedUserForDelete.id);
    };

    return {
        isOpen: isDeleteDialogOpen,
        user: selectedUserForDelete,
        onOpen: openDeleteDialog,
        onClose: closeDeleteDialog,
        handleDelete,
        isDeleting: deleteMutation.isPending,
    };
};

export default useDeleteUser;

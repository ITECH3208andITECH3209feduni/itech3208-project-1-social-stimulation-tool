import { useMutation, useQueryClient } from "@tanstack/react-query";
import useUserUIStore from "../stores/useUserUIStore";
import userApi from "../../api/users/user.api";
import { toaster } from "@/components/ui/toaster";

const useUpdateUser = () => {
    const { 
        isUpdateDrawerOpen, 
        selectedUserForUpdate, 
        openUpdateDrawer, 
        closeUpdateDrawer 
    } = useUserUIStore();
    const queryClient = useQueryClient();

    const updateMutation = useMutation({
        mutationFn: async ({ id, data }) => {
            const res = await userApi.updateAccountStatus(id, data);
            if (!res.success) {
                throw new Error(res.message || "Failed to update user");
            }
            return res.data;
        },
        onSuccess: () => {
            toaster.create({
                title: "Success",
                description: "User account status updated successfully.",
                type: "success",
            });
            queryClient.invalidateQueries({ queryKey: ["users"] });
            closeUpdateDrawer();
        },
        onError: (error) => {
            toaster.create({
                title: "Error",
                description: error.message || "Failed to update user status.",
                type: "error",
            });
        }
    });

    const handleUpdate = async (data) => {
        if (!selectedUserForUpdate?.id) return;
        updateMutation.mutate({
            id: selectedUserForUpdate.id,
            data: data
        });
    };

    return {
        isOpen: isUpdateDrawerOpen,
        user: selectedUserForUpdate,
        onOpen: openUpdateDrawer,
        onClose: closeUpdateDrawer,
        handleUpdate,
        isUpdating: updateMutation.isPending,
    };
};

export default useUpdateUser;

import useUserUIStore from "../stores/useUserUIStore";

const useDeleteUser = () => {
    const { 
        isDeleteDialogOpen, 
        selectedUserForDelete, 
        openDeleteDialog, 
        closeDeleteDialog 
    } = useUserUIStore();

    const handleDelete = async () => {
        // Business logic for deleting a user goes here
        console.log("Delete user business logic triggered for:", selectedUserForDelete?.id);

        // After successful deletion:
        closeDeleteDialog();
    };

    return {
        isOpen: isDeleteDialogOpen,
        user: selectedUserForDelete,
        onOpen: openDeleteDialog,
        onClose: closeDeleteDialog,
        handleDelete,
    };
};

export default useDeleteUser;

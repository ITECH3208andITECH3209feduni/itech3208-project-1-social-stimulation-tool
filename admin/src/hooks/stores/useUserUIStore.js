import { create } from "zustand";

const useUserUIStore = create((set) => ({
    // Update Drawer State
    isUpdateDrawerOpen: false,
    selectedUserForUpdate: null,
    openUpdateDrawer: (user) => set({ isUpdateDrawerOpen: true, selectedUserForUpdate: user }),
    closeUpdateDrawer: () => set({ isUpdateDrawerOpen: false, selectedUserForUpdate: null }),

    // Delete Dialog State
    isDeleteDialogOpen: false,
    selectedUserForDelete: null,
    openDeleteDialog: (user) => set({ isDeleteDialogOpen: true, selectedUserForDelete: user }),
    closeDeleteDialog: () => set({ isDeleteDialogOpen: false, selectedUserForDelete: null }),
}));

export default useUserUIStore;

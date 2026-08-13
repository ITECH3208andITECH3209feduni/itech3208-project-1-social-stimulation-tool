import { create } from "zustand";

const useVideoUIStore = create((set) => ({
    // Update Video Modal/Drawer State
    isUpdateDrawerOpen: false,
    selectedVideoForUpdate: null,
    openUpdateDrawer: (video) => set({ isUpdateDrawerOpen: true, selectedVideoForUpdate: video }),
    closeUpdateDrawer: () => set({ isUpdateDrawerOpen: false, selectedVideoForUpdate: null }),

    // Delete Video Dialog State
    isDeleteDialogOpen: false,
    selectedVideoForDelete: null,
    openDeleteDialog: (video) => set({ isDeleteDialogOpen: true, selectedVideoForDelete: video }),
    closeDeleteDialog: () => set({ isDeleteDialogOpen: false, selectedVideoForDelete: null }),
}));

export default useVideoUIStore;

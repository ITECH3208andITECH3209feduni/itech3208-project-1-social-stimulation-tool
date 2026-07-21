import { useMutation, useQueryClient } from "@tanstack/react-query";
import useVideoUIStore from "../stores/useVideoUIStore";
import videoApi from "../../api/videos/video.api";
import { toaster } from "@/components/ui/toaster";

const useDeleteVideo = () => {
    const { 
        isDeleteDialogOpen, 
        selectedVideoForDelete, 
        openDeleteDialog, 
        closeDeleteDialog 
    } = useVideoUIStore();
    const queryClient = useQueryClient();

    const deleteMutation = useMutation({
        mutationFn: async (id) => {
            const res = await videoApi.deleteVideo(id);
            if (!res.success) {
                throw new Error(res.message || "Failed to delete video");
            }
            return res.data;
        },
        onSuccess: () => {
            toaster.create({
                title: "Success",
                description: "Video deleted successfully.",
                type: "success",
            });
            queryClient.invalidateQueries({ queryKey: ["videos"] });
            closeDeleteDialog();
        },
        onError: (error) => {
            toaster.create({
                title: "Error",
                description: error.message || "Failed to delete video.",
                type: "error",
            });
        }
    });

    const handleDelete = async () => {
        if (!selectedVideoForDelete?.id) return;
        deleteMutation.mutate(selectedVideoForDelete.id);
    };

    return {
        isOpen: isDeleteDialogOpen,
        video: selectedVideoForDelete,
        onOpen: openDeleteDialog,
        onClose: closeDeleteDialog,
        handleDelete,
        isDeleting: deleteMutation.isPending,
    };
};

export default useDeleteVideo;

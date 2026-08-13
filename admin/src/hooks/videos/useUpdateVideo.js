import { useMutation, useQueryClient } from "@tanstack/react-query";
import useVideoUIStore from "../stores/useVideoUIStore";
import videoApi from "../../api/videos/video.api";
import { toaster } from "@/components/ui/toaster";

const useUpdateVideo = () => {
    const { 
        isUpdateDrawerOpen, 
        selectedVideoForUpdate, 
        openUpdateDrawer, 
        closeUpdateDrawer 
    } = useVideoUIStore();
    const queryClient = useQueryClient();

    const updateMutation = useMutation({
        mutationFn: async ({ id, formData }) => {
            const res = await videoApi.updateVideo(id, formData);
            if (!res.success) {
                throw new Error(res.message || "Failed to update video");
            }
            return res.data;
        },
        onSuccess: () => {
            toaster.create({
                title: "Success",
                description: "Video updated successfully.",
                type: "success",
            });
            queryClient.invalidateQueries({ queryKey: ["videos"] });
            closeUpdateDrawer();
        },
        onError: (error) => {
            toaster.create({
                title: "Error",
                description: error.message || "Failed to update video.",
                type: "error",
            });
        }
    });

    const handleUpdate = async (id, formData) => {
        if (!id) return;
        updateMutation.mutate({ id, formData });
    };

    return {
        isOpen: isUpdateDrawerOpen,
        video: selectedVideoForUpdate,
        onOpen: openUpdateDrawer,
        onClose: closeUpdateDrawer,
        handleUpdate,
        isUpdating: updateMutation.isPending,
    };
};

export default useUpdateVideo;
